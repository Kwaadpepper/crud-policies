@php $params = request()->route()->parameters; @endphp
@foreach (request()->route()->parameters as $p)
@if(is_object($p) and in_array(\Illuminate\Database\Eloquent\Model::class, class_parents($p)))
@can('viewAny', get_class($p))
<a href="{{ CrudController::getRoutePrefixed("$modelTable.index", [], true, count($params) - $loop->index) }}">{{
    transFb(
        sprintf('models.classes.%s', strtolower(Str::plural(class_basename(get_class($p))))),
        Str::plural(class_basename(get_class($p)))
    )
}}</a>&nbsp;-
@else
{{
    transFb(
        sprintf('models.classes.%s', strtolower(Str::plural(class_basename(get_class($p))))),
        Str::plural(class_basename(get_class($p)))
    )
}}&nbsp;-
@endcan
@endif
@endforeach
@if(in_array($action, ['viewAny', 'create']))
@can('viewAny', $modelClass)
&nbsp;<a href="{{ CrudController::getRoutePrefixed("$modelTable.index") }}">{{
    transFb(
        sprintf('models.classes.%s', strtolower(Str::plural(class_basename($modelClass)))),
        Str::plural(class_basename($modelClass))
    )
}}</a>&nbsp;-
@else
&nbsp;{{
    transFb(
        sprintf('models.classes.%s', strtolower(Str::plural(class_basename($modelClass)))),
        Str::plural(class_basename($modelClass))
    )
}}&nbsp;-
@endcan
@endif
&nbsp;{{ __("crud-policies::crud.$action") }}
