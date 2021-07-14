@extends('layouts.backend')

@section('title', sprintf('%s - Voir', trans(Str::plural(class_basename($modelClass)))))
@section('description', sprintf('Voir un %s', trans(class_basename($modelClass))))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('modules.flashMessage')
    <div class="card-header">
        @can('viewAny', $modelClass)
        <a href="{{route("bo.$modelTable.index")}}">{{ trans(Str::plural(class_basename($modelClass))) }}</a>&nbsp;-&nbsp;{{ __('Voir') }}
        @else
        {{ trans(Str::plural(class_basename($modelClass))) }}&nbsp;-&nbsp;{{ __('Voir') }}
        @endcan
        <div class="btn-group float-end" role="group" aria-label="{{ __('Actions') }}">
            @can('update', $model)
            <a href="{{route("bo.$modelTable.edit", $model)}}" class="btn btn-info ">{{ __('Modifier') }}</a>
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
        <table class="table table-sm table-bordered border-primary align-top table-striped text-center">
            <thead class="thead-dark">
            <tr>
                <th scope="col" class="w-25">{{ __('Propriété') }}</th>
                <th scope="col" class="w-75">{{ __('Valeur') }}</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($modelClass::getEditableProperties() as $fieldName => $prop)
                @if(in_array(CrudAction::view(), $prop['actions']))
                @switch($prop['type'])
                    @case(CrudType::password())
                        {{-- Ignore --}}
                        @break
                    @default
                    <tr>
                        <td>{{ $prop['label'] }}</td>
                        <td>@include('crud.printfield', [
                            'action' => CrudAction::view(),
                            'field' => $model->{$fieldName},
                            'props' => $prop
                        ])</td>
                    </tr>
                @endswitch
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
