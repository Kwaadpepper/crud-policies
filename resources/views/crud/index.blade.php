@extends($viewLayout)

@section('title', sprintf('%s - %s', trans(Str::plural(class_basename($modelClass))), __('crud-policies::crud.viewAny')))
@section('description', sprintf('%s %s', __('crud-policies::crud.viewAny'), trans(Str::plural(class_basename($modelClass)))))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('crud-policies::crud.modules.flashMessage')
    <div class="card-header">
        <a href="{{ CrudController::getRoutePrefixed("$modelTable.index") }}">{{ trans(Str::plural(class_basename($modelClass))) }}</a>&nbsp;-&nbsp;{{ __('crud-policies::crud.viewAny') }}
        @can('create', $modelClass)
        <a href="{{ CrudController::getRoutePrefixed("$modelTable.create") }}" class="btn btn-primary float-end">{{ __('crud-policies::crud.create') }}</a>
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
                        <a class="btn btn-danger" href="{{ url()->current() }}">{{ __('crud-policies::crud.reset') }}</a>
                        <button type="submit" class="btn btn-primary">
                            {{ __('crud-policies::crud.search') }}
                        </button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table text-center">
            <thead class="thead-dark">
            @include('crud-policies::crud.modules.sorter')
            </thead>
            <tbody>
            @if(count($models))

            {{-- INIT QUERIES for OPTIMIZING --}}
            @foreach ($modelClass::getEditableProperties() as $fieldName => $prop)
            @if($prop['type']->equals(CrudType::order()))
            @php ${"$modelTable$fieldName"} = $modelClass::max($fieldName); @endphp
            @endif
            @if($prop['type']->equals(CrudType::belongsTo()))
            @php try { $models->load(Str::singular((new $prop['belongsTo']())->getTable())); } catch(\Exception $e) {} @endphp
            @endif
            @if($prop['type']->equals(CrudType::belongsToMany()))
            @php $models->load("$fieldName"); @endphp
            @endif
            @endforeach

            {{-- DISPLAY MODELS --}}
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
                            'props' => $prop,
                            "$modelTable$fieldName" => ${"$modelTable$fieldName"} ?? null
                        ])</td>
                @endswitch
                @endif
                @endforeach

                {{-- MODEL ACTIONS --}}
                <td>
                    <div class="btn-group" role="group" aria-label="{{ Str::plural(__('crud-policies::crud.action')) }}">
                        @can('view', $model)
                        <a href="{{ CrudController::getRoutePrefixed("$modelTable.show", $model) }}" class="btn btn-sm btn-primary">{{ __('crud-policies::crud.view') }}</a>
                        @endcan
                        @can('update', $model)
                        <a href="{{ CrudController::getRoutePrefixed("$modelTable.edit", $model) }}" class="btn btn-sm btn-info">{{ __('crud-policies::crud.edit') }}</a>
                        @endcan
                        @can('delete', $model)
                        <form action="{{ CrudController::getRoutePrefixed("$modelTable.destroy", $model) }}" method="POST" onsubmit="__CRUD.confirmDelete(event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">{{ __('crud-policies::crud.delete') }}</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="{{ count($modelClass::getEditableProperties()) }}">
                {{ __('crud-policies::crud.nothingtoshow') }}
                </td>
            </tr>
            @endif
            </tbody>
        </table>
        {{ $models->links() }}
    </div>
</div>
@include('crud-policies::crud.modules.scriptConstants')
@endsection
