@can('delete', $model)
<form class="CrudConfirmDelete" action="{{ CrudController::getRoutePrefixed("$modelTable.destroy", $model)}}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">{{ __('crud-policies::crud.delete') }}</button>
</form>
@endcan
