<?php

namespace Kwaadpepper\CrudPolicies\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
 * @var public static $modelClass
 * @var public static $layout
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
            $this->shareViewLayout();
            $this->shareCrudModelsClassesToView();
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
        if ($request->sort_col) {
            Session::put("crud.$tableName.sort_col", $request->sort_col);
        } else {
            Session::remove("crud.$tableName.sort_col", $request->sort_col);
        }
        if ($request->sort_way) {
            Session::put("crud.$tableName.sort_way", $request->sort_way);
        } else {
            Session::remove("crud.$tableName.sort_way", $request->sort_way);
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

        // Auto orderBy first column of type order
        if (!$models->getQuery()->orders) {
            $modelClass = static::${'modelClass'};
            $orderProp = collect((new $modelClass())->getEditableProperties())
                ->filter(function ($prop) {
                    return $prop['type']->equals(CrudType::order());
                })->take(1);
            $propName = $orderProp->keys()->first();
            $orderProp = $orderProp->first();
            if ($orderProp) {
                Session::put("crud.$tableName.sort_col", $propName);
                Session::put("crud.$tableName.sort_way", 'asc');
                $models = $models->orderBy($propName);
            }
        }

        Session::save();

        /** @var \Illuminate\Pagination\AbstractPaginator */
        $models = $models->paginate(config('crud.paginate', 15));
        if ($request->ajax()) {
            return response()->json(CrudResource::collection($models));
        } else {
            $models = $models->withQueryString();
            return view('crud-policies::crud.index', compact('models'));
        }
    }

    public function show(Model $model)
    {
        return view('crud-policies::crud.show', ['model' => $model]);
    }

    public function create()
    {
        return view('crud-policies::crud.create');
    }

    public function store(StoreCrudModel $request)
    {
        $modelClass = static::${'modelClass'};
        $model = new $modelClass();
        $model->fill($request->validated());
        $this->storeModel($model);
        $model->saveOrFail();
        static::afterSave($model);
        $model->saveRelations($request);
        $this->storedModel($model);
        return \redirect()->route(sprintf(
            '%s%s.edit',
            self::getRoutePrefix() ? self::getRoutePrefix() . '.' : '',
            $model->getTable()
        ), $model)
            ->with('success', trans(':model a bien été enregistré.', ['model' => $model->getModelName()]));
    }

    public function edit(Model $model)
    {
        return view('crud-policies::crud.edit', ['model' => $model]);
    }

    public function update(UpdateCrudModel $request, Model $model)
    {
        $model->fill($request->validated());
        $this->updateModel($model);
        $model->saveOrFail();
        static::afterSave($model);
        $model->saveRelations($request);
        $this->updatedModel($model);
        return \redirect()->route(sprintf(
            '%s%s.edit',
            self::getRoutePrefix() ? self::getRoutePrefix() . '.' : '',
            $model->getTable()
        ), $model)
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
            return \redirect()->route(sprintf(
                '%s%s.index',
                self::getRoutePrefix() ? self::getRoutePrefix() . '.' : '',
                $model->getTable()
            ))
                ->with('warning', trans(':model a bien été supprimé.', ['model' => $model->getModelName()]));
        } else {
            return redirect()->back()
                ->with('error', trans(':model n\'as pas pu être supprimé', ['model' => $model->getModelName()]));
        }
    }

    /**
     * After save hook
     * For now used to handle order columns
     *
     * @param Model $model
     * @return void
     */
    public static function afterSave(Model $model): void
    {
        $modelClass = \get_class($model);
        $props = collect((new $modelClass())->getEditableProperties());
        DB::beginTransaction();
        foreach ($props as $propName => $prop) {
            if (
                !$model->wasRecentlyCreated and
                !in_array($propName, array_keys($model->getChanges()))
            ) {
                continue;
            }
            if ($prop['type']->equals(CrudType::order())) {
                $ids = $modelClass::orderBy($propName)->pluck('id');
                $i = 0;
                $values = $ids->mapWithKeys(function ($id) use (&$i) {
                    return [$id => $i++];
                })->all();
                static::massUpdate($modelClass, $propName, $values);
            }
        }
        DB::commit();
    }

    private static function massUpdate(string $modelClass, string $propName, array $values)
    {
        $counter = 0;
        $tableName = $modelClass::getModel()->getTable();
        $chunks = collect($values)->chunk(100);

        foreach ($chunks as $chunkValues) {
            $cases = [];
            $ids = [];
            $params = [];

            foreach ($chunkValues as $id => $value) {
                $id = (int)$id;
                $cases[] = "WHEN {$id} then ?";
                $params[] = $value;
                $ids[] = $id;
            }

            $ids = implode(',', $ids);
            $cases = implode(' ', $cases);
            $params[] = Carbon::now();

            $q = "UPDATE `{$tableName}` SET `{$propName}` = CASE `id` {$cases} END";

            if (Schema::hasColumn($tableName, 'updated_at')) {
                $q .= ',`updated_at` = ?';
            }
            $q .= " WHERE `id` in ({$ids})";

            $counter += DB::update(
                $q,
                $params
            );
        }
        return $counter;
    }

    /**
     * Get the current controller crud model class name
     *
     * @return string
     */
    public function getModelClass(): string
    {
        return static::${'modelClass'};
    }

    /**
     * Get the current route prefix
     *
     * @return string|null
     */
    public static function getRoutePrefix()
    {
        /** @var \Illuminate\Routing\Route */
        $route = request()->route();
        return $route->getPrefix();
    }

    /**
     * Get the route url with prefix if needed
     *
     * @param string $route
     * @param array $parameters
     * @param boolean $absolute
     * @return string
     */
    public static function getRoutePrefixed(string $route, $parameters = [], bool $absolute = true): string
    {
        $prefix = self::getRoutePrefix();
        return route($prefix ? "$prefix.$route" : $route, $parameters, $absolute);
    }

    /**
     * Get All Crud Models
     *
     * @return array
     */
    public static function getCrudModels(): array
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
        return $models;
    }

    private function shareViewLayout(): void
    {
        View::share(
            'viewLayout',
            static::${'layout'} ?? \config('crud-policies.viewLayout', 'crud-policies::crud.layout')
        );
    }

    /**
     * Get the current model class
     *
     * @return void
     */
    private function shareCrudModelsClassesToView(): void
    {
        $models = static::getCrudModels();
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
