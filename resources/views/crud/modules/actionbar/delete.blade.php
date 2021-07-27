@can('delete', $model)
<form action="{{ CrudController::getRoutePrefixed("$modelTable.destroy", $model)}}" method="POST" onsubmit="__CRUD.confirmDelete(event)">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">{{ __('crud-policies::crud.delete') }}</button>
</form>
@endcan
