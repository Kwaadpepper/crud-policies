@php $params = request()->route()->parameters; @endphp
<nav aria-label="breadcrumb" class="d-inline">
    <ol class="breadcrumb d-inline-flex">
        @foreach (request()->route()->parameters as $p)
        @if(is_object($p) and in_array(\Illuminate\Database\Eloquent\Model::class, class_parents($p)))
        <li class="breadcrumb-item">
        @can('viewAny', get_class($p))
            <a href="{{ CrudController::getRoutePrefixed("$modelTable.index", [], true, count($params) - $loop->index) }}">{{
                transFb(
                    sprintf('models.classes.%s', strtolower(Str::plural(class_basename(get_class($p))))),
                    Str::plural(class_basename(get_class($p)))
                )
            }}</a>
        @else
        <span>{{
            transFb(
                sprintf('models.classes.%s', strtolower(Str::plural(class_basename(get_class($p))))),
                Str::plural(class_basename(get_class($p)))
            )
        }}</span>
        @endcan
        </li>
        @endif
        @endforeach
        @if(in_array($action, [CrudAction::viewAny(), CrudAction::create()]))
        @can('viewAny', $modelClass)
        <li class="breadcrumb-item">
            <a href="{{ CrudController::getRoutePrefixed("$modelTable.index") }}">{{
                transFb(
                    sprintf('models.classes.%s', strtolower(Str::plural(class_basename($modelClass)))),
                    Str::plural(class_basename($modelClass))
                )
            }}</a>
        </li>
        @else
        <li class="breadcrumb-item">
        <span>{{
            transFb(
                sprintf('models.classes.%s', strtolower(Str::plural(class_basename($modelClass)))),
                Str::plural(class_basename($modelClass))
            )
        }}</span>
        </li>
        @endcan
        @endif
        <li class="breadcrumb-item active" aria-current="page">{{ __('crud-policies::crud.'.$action->getDefinition()) }}</li>
    </ol>
</nav>
