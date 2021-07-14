<?php

namespace Kwaadpepper\CrudPolicies\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Kwaadpepper\CrudPolicies\Enums\CrudType;
use Kwaadpepper\CrudPolicies\Exceptions\CrudException;
use Kwaadpepper\CrudPolicies\Http\Requests\CrudIndexRequest;
use Kwaadpepper\CrudPolicies\Http\Requests\StoreCrudModel;
use Kwaadpepper\CrudPolicies\Http\Requests\UpdateCrudModel;
use Kwaadpepper\CrudPolicies\Http\Resources\CrudResource;

/**
 * You need to define $modelClass with a Model::class
 * @var static $modelClass
 */
trait CrudController
{
    // Eloquent callbacks
    public function indexQuery(Builder $query): Builder
    {
        return $query;
    }

    public function storeModel(Model &$model)
    {
    }

    public function storedModel(Model &$model)
    {
    }

    public function updateModel(Model &$model)
    {
    }

    public function updatedModel(Model &$model)
    {
    }

    public function __construct()
    {
        // AUTH CONTROL TO ROOT POLICY
        $this->authorizeResource(static::${'modelClass'});

        $modelClass = static::${'modelClass'};
        if (is_null($modelClass) or !class_exists($modelClass)) {
            throw new CrudException(sprintf(
                '%s needs the modelClass prop to be a class, %s %s given',
                __CLASS__,
                gettype($modelClass),
                $modelClass
            ));
        }
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new CrudException(sprintf(
                '%s needs the model prop to be a class the extends %s',
                __CLASS__,
                Model::class
            ));
        }
        if (!request()->ajax() and !app()->runningInConsole()) {
            self::shareCrudModelsClassesToView();
            $this->shareModelClassToView();
            $this->shareModelHasImageToView();
            $this->shareModelTableToView();
            $this->shareModelPropsToView();
        }
    }

    public function index(CrudIndexRequest $request)
    {
        /** @var \Illuminate\Database\Eloquent\Builder */
        $models = static::${'modelClass'}::query();

        // Search on crud Label column
        if ($request->search) {
            $model = static::${'modelClass'};
            $col = (new $model())->crudLabelColumn;
            $models = $models->where($col, 'LIKE', "%$request->search%");
        }

        // Sort columns
        $tableName = (new static::${'modelClass'}())->getTable();
        if ($request->rst) {
            Session::remove("crud.$tableName.sort_col");
            Session::remove("crud.$tableName.sort_way");
        }
        if ($request->sort_col) {
            Session::put("crud.$tableName.sort_col", $request->sort_col);
            Session::save();
        }
        if ($request->sort_way) {
            Session::put("crud.$tableName.sort_way", $request->sort_way);
            Session::save();
        }

        if (
            Session::has("crud.$tableName.sort_col") and
            Session::has("crud.$tableName.sort_way") and
            Schema::hasColumn($tableName, Session::get("crud.$tableName.sort_col"))
        ) {
            $models = $models->orderBy(
                Session::get("crud.$tableName.sort_col"),
                Session::get("crud.$tableName.sort_way")
            );
        }

        $models = $this->indexQuery($models);
        /** @var \Illuminate\Pagination\AbstractPaginator */
        $models = $models->paginate(config('crud.paginate', 15));
        if ($request->ajax()) {
            return response()->json(CrudResource::collection($models));
        } else {
            $models = $models->withQueryString();
            return view('crud.index', compact('models'));
        }
    }

    public function show(Model $model)
    {
        return view('crud.show', ['model' => $model]);
    }

    public function create()
    {
        return view('crud.create');
    }

    public function store(StoreCrudModel $request)
    {
        $modelClass = static::${'modelClass'};
        $model = new $modelClass();
        $model->fill($request->validated());
        $this->storeModel($model);
        $model->saveOrFail();
        $model->saveRelations($request);
        $this->storedModel($model);
        return \redirect()->route(sprintf('bo.%s.edit', $model->getTable()), $model)
            ->with('success', trans(':model a bien été enregistré.', ['model' => $model->getModelName()]));
    }

    public function edit(Model $model)
    {
        return view('crud.edit', ['model' => $model]);
    }

    public function update(UpdateCrudModel $request, Model $model)
    {
        $model->fill($request->validated());
        $this->updateModel($model);
        $model->saveOrFail();
        $model->saveRelations($request);
        $this->updatedModel($model);
        return \redirect()->route(sprintf('bo.%s.edit', $model->getTable()), $model)
            ->with('success', trans(':model a bien été enregistré.', ['model' => $model->getModelName()]));
    }

    public function destroy(Model $model)
    {
        if (
            !in_array(SoftDeletes::class, class_uses_recursive($model), true) and
            $restrict = $model->gotHasManyRelationWithRestrictOnDelete()
        ) {
            return redirect()->back()
                ->with('error', trans(
                    ':model n\'as pas pu être supprimé car il est encore associé avec des :associated',
                    [
                        'model' => $model->getModelName(),
                        'associated' => Str::plural($restrict)
                    ]
                ));
        }
        if ($model->delete()) {
            return \redirect()->route(sprintf('bo.%s.index', $model->getTable()))
                ->with('warning', trans(':model a bien été supprimé.', ['model' => $model->getModelName()]));
        } else {
            return redirect()->back()
                ->with('error', trans(':model n\'as pas pu être supprimé', ['model' => $model->getModelName()]));
        }
    }

    public function getModelClass()
    {
        return static::${'modelClass'};
    }

    /**
     * Get the current model class
     *
     * @return void
     */
    public static function shareCrudModelsClassesToView(): void
    {
        $models = [];
        /** @var \Symfony\Component\Finder\SplFileInfo */
        foreach (File::allFiles(\app_path('Http/Controllers')) as $file) {
            $class = sprintf(
                'App\Http\Controllers\%s',
                \substr(str_replace('/', '\\', $file->getRelativePathname()), 0, -4)
            );
            if (
                in_array(CrudController::class, class_uses_recursive($class)) and
                $class::${'modelClass'}
            ) {
                $models[] = $class::${'modelClass'};
            }
        }
        View::share('modelsClasses', $models);
    }

    /**
     * Get the current model class
     *
     * @return void
     */
    private function shareModelClassToView(): void
    {
        View::share('modelClass', static::${'modelClass'});
    }

    /**
     * Get the current model class
     *
     * @return void
     */
    private function shareModelHasImageToView(): void
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = static::${'modelClass'};

        View::share('hasImage', (bool)collect((new $model())->getEditableProperties())->filter(function ($prop) {
            return $prop['type']->equals(CrudType::image());
        })->count());
    }

    /**
     * Get the current model table
     *
     * @return void
     */
    private function shareModelTableToView(): void
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = static::${'modelClass'};

        View::share('modelTable', (new $model())->getTable());
    }

    /**
     * Get the current model properties
     *
     * @return void
     */
    private function shareModelPropsToView(): void
    {
        $model = static::${'modelClass'};
        View::share('props', (new $model())->getEditableProperties());
    }
}
