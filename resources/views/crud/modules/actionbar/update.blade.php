@can('update', $model)
<a href="{{ CrudController::getRoutePrefixed("$modelTable.edit", $model)}}" class="btn btn-info ">{{ __('crud-policies::crud.update') }}</a>
@endcan
