@can('view', $model)
<a href="{{ CrudController::getRoutePrefixed("$modelTable.show", $model) }}" class="btn btn-primary ">{{ __('crud-policies::crud.view') }}</a>
@endcan
