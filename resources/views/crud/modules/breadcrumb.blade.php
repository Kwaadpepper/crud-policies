@php $params = request()->route()->parameters; @endphp
@can('viewAny', $modelClass)
@foreach (request()->route()->parameters as $p)
@if(is_object($p) and in_array(\Illuminate\Database\Eloquent\Model::class, class_parents($p)))
<a href="{{ CrudController::getRoutePrefixed("$modelTable.index", [], true, count($params) - $loop->index) }}">{{
    transFb(
        sprintf('models.classes.%s', strtolower(Str::plural(class_basename(get_class($p))))),
        Str::plural(class_basename(get_class($p)))
    )
}}</a>&nbsp;-
@endif
@endforeach
@if($action === 'viewAny')
<a href="{{ CrudController::getRoutePrefixed("$modelTable.index") }}">{{
    transFb(
        sprintf('models.classes.%s', strtolower(Str::plural(class_basename($modelClass)))),
        Str::plural(class_basename($modelClass))
    )
}}</a>&nbsp;
@endif
&nbsp;{{ __("crud-policies::crud.$action") }}
@else
@foreach (request()->route()->parameters as $p)
@if(is_object($p) and in_array(\Illuminate\Database\Eloquent\Model::class, class_parents($p)))
{{
    transFb(
        sprintf('models.classes.%s', strtolower(Str::plural(class_basename(get_class($p))))),
        Str::plural(class_basename(get_class($p)))
    )
}}&nbsp;-
@endif
@endforeach
&nbsp;{{ __("crud-policies::crud.$action") }}
@endcan



