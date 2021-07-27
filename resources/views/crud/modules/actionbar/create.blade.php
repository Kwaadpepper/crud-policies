@can('create', $modelClass)
<a href="{{ CrudController::getRoutePrefixed("$modelTable.create") }}" class="btn btn-primary float-end">{{ __('crud-policies::crud.create') }}</a>
@endcan
