<?php

namespace Kwaadpepper\CrudPolicies\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
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
    /**
     * Index query middleware
     *
     * @param Builder $query
     * @return Builder
     */
    public function indexQuery(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Show action middleware
     *
     * @param Model $model
     * @return void
     */
    public function showModel(Model &$model)
    {
    }

    /**
     * Show create page middleware
     *
     * @return void
     */
    public function createModel()
    {
    }

    /**
     * Before store model middleware
     *
     * @param Model $model
     * @return void
     */
    public function storeModel(Model &$model)
    {
    }

    /**
     * After store model middleware
     *
     * @param Model $model
     * @return void
     */
    public function storedModel(Model &$model)
    {
    }

    /**
     * Show edit model middleware
     *
     * @param Model $model
     * @return void
     */
    public function editModel(Model &$model)
    {
    }

    /**
     * Before update model middleware
     *
     * @param Model $model
     * @return void
     */
    public function updateModel(Model &$model)
    {
    }

    /**
     * After update model middleware
     *
     * @param Model $model
     * @return void
     */
    public function updatedModel(Model &$model)
    {
    }

    /**
     * Before delete model middleware
     *
     * @param Model $model
     * @return void
     */
    public function deleteModel(Model &$model)
    {
    }

    /**
     * After delete model middleware
     *
     * @param Model $model
     * @return void
     */
    public function deletedModel(Model &$model)
    {
    }

    /**
     * CrudController
     * @throws CrudException If a model parameter is incorrect.
     */
    public function __construct()
    {
        // * AUTH CONTROL TO ROOT POLICY
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
            $this->shareModelHasFileToView();
            $this->shareModelTableToView();
            $this->shareModelPropsToView();
        }
        // * Check if parent constructor exists before call
        if (method_exists(get_parent_class($this), '__construct')) {
            parent::__construct();
        }
    }

    /**
     * Crud index action
     *
     * @param CrudIndexRequest $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse
     */
    public function index(CrudIndexRequest $request)
    {
        /** @var \Illuminate\Database\Eloquent\Builder */
        $models = static::${'modelClass'}::query();

        // * Search on crud Label column
        if ($request->search) {
            $model  = static::${'modelClass'};
            $col    = (new $model())->crudLabelColumn;
            $models = $models->where($col, 'LIKE', "%$request->search%");
        }

        // * Sort columns
        $tableName = (new static::${'modelClass'}())->getTable();
        if ($request->sort_col) {
            Session::put("crud.$tableName.sort_col", $request->sort_col);
        }
        if ($request->sort_way) {
            Session::put("crud.$tableName.sort_way", $request->sort_way);
        }

        if ($request->rst) {
            Session::remove("crud.$tableName.sort_way");
            Session::remove("crud.$tableName.sort_col");
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

        // * Auto orderBy first column of type order
        if (!$models->getQuery()->orders) {
            $modelClass = static::${'modelClass'};
            $orderProp  = collect((new $modelClass())->getEditableProperties())
                ->filter(function ($prop) {
                    return $prop['type']->equals(CrudType::order());
                })->take(1);
            $propName   = $orderProp->keys()->first();
            $orderProp  = $orderProp->first();
            if ($orderProp) {
                Session::put("crud.$tableName.sort_col", $propName);
                Session::put("crud.$tableName.sort_way", 'asc');
                $models = $models->orderBy($propName);
            }
        }

        /** @var \Illuminate\Pagination\AbstractPaginator */
        $models = $models->paginate(config('crud.paginate', 15));
        if ($request->ajax()) {
            return response()->json(CrudResource::collection($models));
        } else {
            $models = $models->withQueryString();
            return view('crud-policies::crud.index', compact('models'));
        }
    }

    /**
     * Show model action
     *
     * @param Request $request
     * @param mixed   ...$params
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, ...$params)
    {
        $model = $this->getLastModelParam($params);
        $this->showModel($model);
        if ($request->ajax()) {
            return response()->json(new CrudResource($model));
        }
        return view('crud-policies::crud.show', ['model' => $model]);
    }

    /**
     * Create model action
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $this->createModel();
        return view('crud-policies::crud.create');
    }

    /**
     * Store model action
     *
     * @param StoreCrudModel $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCrudModel $request)
    {
        $modelClass = static::${'modelClass'};
        $model      = new $modelClass();
        $model->fill($request->validated());
        $this->storeModel($model);
        $model->saveOrFail();
        static::afterSave($model);
        $model->saveRelations($request);
        $this->storedModel($model);

        // * Try to redirect
        if ($this->can('update', $model)) {
            $return = \redirect()->to(self::getRoutePrefixed(sprintf('%s.edit', $model->getTable()), $model));
        } elseif (($this->can('view', $model))) {
            $return = \redirect()->to(self::getRoutePrefixed(sprintf('%s.show', $model->getTable()), $model));
        } else {
            $return = \redirect()->to(self::getRoutePrefixed(sprintf('%s.index', $model->getTable())));
        }
        return $return->with('success', trans(':model a bien été enregistré.', [
            'model' => Str::singular(
                transFb(sprintf('models.classes.%s', strtolower(Str::plural($model->getModelName()))))
            )
        ]));
    }

    /**
     * Edit model action
     *
     * @param mixed ...$params
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit(...$params)
    {
        $model = $this->getLastModelParam($params);
        $this->editModel($model);
        return view('crud-policies::crud.edit', ['model' => $model]);
    }

    /**
     * Update model action
     *
     * @param UpdateCrudModel $request
     * @param mixed           ...$params
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCrudModel $request, ...$params)
    {
        $model = $this->getLastModelParam($params);
        $model->fill($request->validated());
        $this->updateModel($model);
        $model->saveOrFail();
        static::afterSave($model);
        $model->saveRelations($request);
        $this->updatedModel($model);

        return \redirect()->to(self::getRoutePrefixed(sprintf('%s.edit', $model->getTable()), $model))
            ->with('success', trans(':model a bien été enregistré.', [
                'model' => Str::singular(
                    transFb(sprintf('models.classes.%s', strtolower(Str::plural($model->getModelName()))))
                )
            ]));
    }

    /**
     * Destroy model action
     *
     * @param mixed ...$params
     * @return \Illuminate\Http\RedirectResponse
     * @throws CrudException If delete action is denied.
     */
    public function destroy(...$params)
    {
        $model           = $this->getLastModelParam($params);
        $restrict        = $model->gotHasManyRelationWithRestrictOnDelete();
        $restrictedModel = '';
        try {
            if (is_array($restrict)) {
                foreach ($restrict as $res) {
                    $restrictedModel = $res['className'];
                    $model->load($res['relation']);
                    if (
                        (\is_countable($model->{$res['relation']}) and
                            count($model->{$res['relation']})) or
                        (!is_null($model->{$res['relation']}) and
                            !\is_countable($model->{$res['relation']}))
                    ) {
                        throw new CrudException('retrict delete');
                    }
                }
            }
        } catch (\Exception $e) {
            $restrict = true;
        } finally {
            $restrict = is_array($restrict) ? false : $restrict;
        }

        // * Try to redirect
        $return = \redirect()->to(self::getRoutePrefixed(sprintf('%s.index', $model->getTable())));

        if (
            !in_array(SoftDeletes::class, class_uses_recursive($model), true) and
            $restrict
        ) {
            return $return->with(
                'error',
                trans(
                    ':model n\'as pas pu être supprimé car il est encore associé avec des :associated',
                    [
                        'model' => Str::singular(
                            transFb(sprintf('models.classes.%s', strtolower(Str::plural($model->getModelName()))))
                        ),
                        'associated' => transFb(sprintf('models.classes.%s', strtolower(Str::plural($restrictedModel))))
                    ]
                )
            );
        }
        $this->deleteModel($model);
        if ($model->delete()) {
            $this->deletedModel($model);
            return $return->with('warning', trans(
                ':model a bien été supprimé.',
                ['model' => Str::singular(
                    transFb(sprintf('models.classes.%s', strtolower(Str::plural($model->getModelName()))))
                )]
            ));
        } else {
            return $return->with('error', trans(
                ':model n\'as pas pu être supprimé',
                ['model' => Str::singular(
                    transFb(sprintf('models.classes.%s', strtolower(Str::plural($model->getModelName()))))
                )]
            ));
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
        $props      = collect((new $modelClass())->getEditableProperties());
        DB::beginTransaction();
        foreach ($props as $propName => $prop) {
            if (
                !$model->wasRecentlyCreated and
                !in_array($propName, array_keys($model->getChanges()))
            ) {
                continue;
            }
            if ($prop['type']->equals(CrudType::order())) {
                $ids    = $modelClass::orderBy($propName)->pluck('id');
                $i      = 0;
                $values = $ids->mapWithKeys(function ($id) use (&$i) {
                    return [$id => $i++];
                })->all();
                static::massUpdate($modelClass, $propName, $values);
            }
        }
        DB::commit();
    }

    /**
     * Mass update logic
     *
     * @param string $modelClass
     * @param string $propName
     * @param array  $values
     * @return integer Number or row affected.
     */
    private static function massUpdate(string $modelClass, string $propName, array $values)
    {
        $counter   = 0;
        $tableName = $modelClass::getModel()->getTable();
        $chunks    = collect($values)->chunk(100);

        foreach ($chunks as $chunkValues) {
            $cases  = [];
            $ids    = [];
            $params = [];

            foreach ($chunkValues as $id => $value) {
                $id       = (int)$id;
                $cases[]  = "WHEN {$id} then ?";
                $params[] = $value;
                $ids[]    = $id;
            }

            $ids      = implode(',', $ids);
            $cases    = implode(' ', $cases);
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
        } //end foreach
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
        return trim($route->getPrefix() ?? '', '/');
    }

    /**
     * Get the route url with prefix if needed
     *
     * Cette fonction est à retravailler
     *
     * @param string  $route
     * @param mixed   $parameters
     * @param boolean $absolute
     * @param integer $minus      To remove a parameter from the end.
     * @return string
     */
    public static function getRoutePrefixed(
        string $route,
        $parameters = [],
        bool $absolute = true,
        int $minus = 0
    ): string {
        $currentRouteName = request()->route()->getName();
        $currentAction    = collect(explode('.', $currentRouteName))->last();
        $key              = is_array($parameters) ? null : $parameters;
        $parameters       = array_merge(request()->route()->parameters, is_array($parameters) ? $parameters : []);
        $prefix           = self::getRoutePrefix();
        $prefix           = $prefix ? "$prefix." : '';
        $routePrepend     = '';

        // Remove last parameter if we are not on a .index route .
        if (!in_array($currentAction, ['index', 'create', 'store'])) {
            \array_pop($parameters);
        }

        foreach ($parameters as $parameter) {
            if (
                is_object($parameter) and
                in_array(Model::class, class_parents($parameter))
            ) {
                /** @var \Illuminate\Database\Eloquent\Model $parameter */
                $routePrepend .= sprintf('%s.', $parameter->getTable());
            }
        }
        if ($key) {
            $parameters[] = $key;
        }
        $route  = explode('.', "$prefix$routePrepend$route");
        $action = array_pop($route);

        // Handle special cases .
        if ($currentAction !== 'index') {
            $minus--;
        }

        if ($currentAction === 'create') {
            $minus++;
        }

        for ($i = 0; $i < $minus; $i++) {
            array_pop($route);
            array_pop($parameters);
        }

        $route = implode('.', $route);

        return self::routeExists("$route.$action") ?
            route("$route.$action", $parameters, $absolute) : '#';
    }

    /**
     * Check if route name exists.
     *
     * @param string $routeName
     * @return boolean
     */
    private static function routeExists(string $routeName): bool
    {
        return Route::has($routeName);
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

    /**
     * @phpcs:disable Generic.Files.LineLength.TooLong
     * Check if an action is autorized
     *
     * @param string                                     $action Something in 'viewAny', 'view', 'create', 'update', 'destroy' .
     * @param string|\Illuminate\Database\Eloquent\Model $model  Class name or a Model object .
     * @return boolean
     * @throws CrudException If requested action is not handled.
     */
    private function can(string $action, $model): bool
    {
        $actions = [
            'index' => 'viewAny', 'show' => 'view', 'store' => 'create', 'edit' => 'update', 'destroy' => 'delete'
        ];
        if (
            !in_array(
                $action,
                $actions
            )
        ) {
            throw new CrudException("$action is not in list : " . implode(', ', $actions));
        }
        try {
            if (request()->user()) {
                return $this->authorizeForUser(request()->user(), $action, $model)->allowed();
            }
            return $this->authorize($action, $model)->allowed();
        } catch (AuthorizationException $e) {
            return false;
        }
        return true;
    }

    /**
     * Get the last model from the url params
     *
     * @param array $urlParams
     * @return Model
     * @throws CrudException If model is not found is url.
     */
    private function getLastModelParam(array $urlParams): Model
    {
        $urlParams = \array_reverse($urlParams);
        foreach ($urlParams as $param) {
            if (is_object($param) and in_array(Model::class, class_parents($param))) {
                return $param;
            }
        }
        throw new CrudException('no model found in url params');
    }

    /**
     * Share $viewLayout var to views.
     *
     * @return void
     */
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
    private function shareModelHasFileToView(): void
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = static::${'modelClass'};

        View::share('hasFile', (bool)collect((new $model())->getEditableProperties())->filter(function ($prop) {
            return $prop['type']->equals(CrudType::image()) or $prop['type']->equals(CrudType::file());
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
