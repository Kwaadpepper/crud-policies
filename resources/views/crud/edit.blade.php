@extends('crud-policies::crud.layout')

@section('title', sprintf('%s - %s', trans(Str::plural(class_basename($modelClass))), __('crud-policies::crud.edit')))
@section('description', sprintf('%s %s', __('crud-policies::crud.edit'), trans(class_basename($modelClass))))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('crud-policies::crud.modules.flashMessage')
    <div class="card-header">
        @can('viewAny', $modelClass)
        <a href="{{ CrudController::getRoutePrefixed("$modelTable.index") }}">{{ trans(Str::plural(class_basename($modelClass))) }}</a>&nbsp;-&nbsp;{{ __('crud-policies::crud.edit') }}
        @else
        {{ trans(Str::plural(class_basename($modelClass))) }}&nbsp;-&nbsp;{{ __('crud-policies::crud.edit') }}
        @endcan
        <div class="btn-group float-end" role="group" aria-label="{{ __('crud-policies::crud.action') }}">
            @can('view', $model)
            <a href="{{ CrudController::getRoutePrefixed("$modelTable.show", $model) }}" class="btn btn-primary ">{{ __('crud-policies::crud.view') }}</a>
            @endcan
            @can('delete', $model)
            <form action="{{ CrudController::getRoutePrefixed("$modelTable.destroy", $model) }}" method="POST" onsubmit="__CRUD.confirmDelete(event)">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('crud-policies::crud.delete') }}</button>
            </form>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <form action="{{ CrudController::getRoutePrefixed("$modelTable.update", $model) }}" method="POST" @if($hasImage) enctype="multipart/form-data" @endif>
            @method('put')
            @csrf
            <table class="table table-sm table-bordered border-primary align-middle table-striped text-center">
                <thead class="thead-dark">
                <tr>
                    <th scope="col" class="w-25">{{ __('crud-policies::crud.property') }}</th>
                    <th scope="col" class="w-75">{{ __('crud-policies::crud.value') }}</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($modelClass::getEditableProperties() as $fieldName => $prop)
                    @if(in_array(CrudAction::update(), $prop['actions']))
                    <tr>
                    @include('crud-policies::crud.formfield')
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            <div class="col text-center">
                <button class="btn btn-info" type="submit">{{ __('crud-policies::crud.update') }}</button>
            </div>
        </form>
    </div>
</div>
@include('crud-policies::crud.modules.scriptConstants')
@endsection
