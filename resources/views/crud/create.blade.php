@extends('layouts.backend')

@section('title', sprintf('%s - Ajouter', Str::plural(class_basename($modelClass))))
@section('description', sprintf('Ajouter un %s', class_basename($modelClass)))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('modules.flashMessage')
    <div class="card-header">
        @can('viewAny', $modelClass)
        <a href="{{route("bo.$modelTable.index")}}">{{ trans(Str::plural(class_basename($modelClass))) }}</a>&nbsp;-&nbsp;{{ __('Ajouter') }}
        @else
        {{ Str::plural(class_basename($modelClass)) }}&nbsp;-&nbsp;{{ __('Ajouter') }}
        @endcan
    </div>
    <div class="card-body">
        <form action="{{ route("bo.$modelTable.store") }}" method="POST" @if($hasImage) enctype="multipart/form-data" @endif>
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
                    @php
                        // create form cannot accept readonly field
                        $prop['readonly'] = false;
                    @endphp
                    @if(in_array(CrudAction::create(), $prop['actions']))
                    <tr>
                    @include('crud.formfield')
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            <div class="col text-center">
                <button class="btn btn-info" type="submit">{{ __('Créer') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
