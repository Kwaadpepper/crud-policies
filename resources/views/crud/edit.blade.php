@extends('layouts.backend')

@section('title', sprintf('%s - Editer', trans(Str::plural(class_basename($modelClass)))))
@section('description', sprintf('Editer un %s', trans(class_basename($modelClass))))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('modules.flashMessage')
    <div class="card-header">
        @can('viewAny', $modelClass)
        <a href="{{route("bo.$modelTable.index")}}">{{ trans(Str::plural(class_basename($modelClass))) }}</a>&nbsp;-&nbsp;{{ __('Editer') }}
        @else
        {{ trans(Str::plural(class_basename($modelClass))) }}&nbsp;-&nbsp;{{ __('Editer') }}
        @endcan
        <div class="btn-group float-end" role="group" aria-label="{{ __('Actions') }}">
            @can('view', $model)
            <a href="{{route("bo.$modelTable.show", $model)}}" class="btn btn-primary ">{{ __('Voir') }}</a>
            @endcan
            @can('delete', $model)
            <form action="{{route("bo.$modelTable.destroy", $model)}}" method="POST" onsubmit="__CRUD.confirmDelete(event)">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('Supprimer') }}</button>
            </form>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route("bo.$modelTable.update", $model) }}" method="POST" @if($hasImage) enctype="multipart/form-data" @endif>
            @method('put')
            @csrf
            <table class="table table-sm table-bordered border-primary align-middle table-striped text-center">
                <thead class="thead-dark">
                <tr>
                    <th scope="col" class="w-25">{{ __('Propriété') }}</th>
                    <th scope="col" class="w-75">{{ __('Valeur') }}</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($modelClass::getEditableProperties() as $fieldName => $prop)
                    @if(in_array(CrudAction::update(), $prop['actions']))
                    <tr>
                    @include('crud.formfield')
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            <div class="col text-center">
                <button class="btn btn-info" type="submit">{{ __('Modifier') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
