@extends('crud-policies::crud.layout')

@section('title', sprintf('%s - Liste', trans(Str::plural(class_basename($modelClass)))))
@section('description', sprintf('Liste des %s', trans(Str::plural(class_basename($modelClass)))))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('crud-policies::crud.modules.flashMessage')
    <div class="card-header">
        <a href="{{ CrudController::getRoutePrefixed("$modelTable.index") }}">{{ trans(Str::plural(class_basename($modelClass))) }}</a>&nbsp;-&nbsp;{{ __('Liste') }}
        @can('create', $modelClass)
        <a href="{{ CrudController::getRoutePrefixed("$modelTable.create") }}" class="btn btn-primary float-end">{{ __('Ajouter') }}</a>
        @endcan
    </div>
    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col">
                {{ $models->links() }}
            </div>
            <div class="col">
                <form action="{{ url()->current() }}">
                    <div class="input-group mb-3">
                        <input type="search" name="search" class="form-control" value="{{ request()->search }}"/>
                        <input type="text" name="sort_col" class="d-none" value="{{ request()->sort_col }}"/>
                        <input type="text" name="sort_way" class="d-none" value="{{ request()->sort_way }}"/>
                        <a class="btn btn-danger" href="{{ url()->current() }}">Réinitialiser</a>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Recherche') }}
                        </button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table text-center">
            <thead class="thead-dark">
            <tr>
                @foreach ($modelClass::getEditableProperties() as $fieldName => $prop)
                @if(in_array(CrudAction::viewAny(), $prop['actions']))
                @switch($prop['type'])
                    @case(CrudType::password())
                        {{-- Ignore --}}
                        @break
                    @default
                        <th scope="col">
                            {{ $prop['label'] }}
                            @include('crud-policies::crud.modules.sorter', ['colname' => $fieldName])
                        </th>
                @endswitch
                @endif
                @endforeach
            </tr>
            </thead>
            <tbody>
            @if(count($models))
            @foreach($models as $model)
            <tr>
                @foreach ($modelClass::getEditableProperties() as $fieldName => $prop)
                @if(in_array(CrudAction::viewAny(), $prop['actions']))
                @switch($prop['type'])
                    @case(CrudType::password())
                        {{-- Ignore --}}
                        @break
                    @default
                        <td>@include('crud-policies::crud.printfield', [
                            'action' => CrudAction::viewAny(),
                            'field' => $model->{$fieldName},
                            'props' => $prop
                        ])</td>
                @endswitch
                @endif
                @endforeach
                <td>
                    <div class="btn-group" role="group" aria-label="{{ __('Actions') }}">
                        @can('view', $model)
                        <a href="{{ CrudController::getRoutePrefixed("$modelTable.show", $model) }}" class="btn btn-sm btn-primary">{{ __('Voir') }}</a>
                        @endcan
                        @can('update', $model)
                        <a href="{{ CrudController::getRoutePrefixed("$modelTable.edit", $model) }}" class="btn btn-sm btn-info">{{ __('Éditer') }}</a>
                        @endcan
                        @can('delete', $model)
                        <form action="{{ CrudController::getRoutePrefixed("$modelTable.destroy", $model) }}" method="POST" onsubmit="__CRUD.confirmDelete(event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">{{ __('Supprimer') }}</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="{{ count($modelClass::getEditableProperties()) }}">
                Rien à afficher
                </td>
            </tr>
            @endif
            </tbody>
        </table>
        {{ $models->links() }}
    </div>
</div>
@endsection
