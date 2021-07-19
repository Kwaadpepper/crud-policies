@extends($viewLayout)

@section('title', sprintf('%s - %s', trans(Str::plural(class_basename($modelClass))), __('crud-policies::crud.view')))
@section('description', sprintf('%s %s', __('crud-policies::crud.view'), trans(class_basename($modelClass))))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('crud-policies::crud.modules.flashMessage')
    <div class="card-header">
        @can('viewAny', $modelClass)
        <a href="{{ CrudController::getRoutePrefixed("$modelTable.index")}}">{{ trans(Str::plural(class_basename($modelClass))) }}</a>&nbsp;-&nbsp;{{ __('crud-policies::crud.view') }}
        @else
        {{ trans(Str::plural(class_basename($modelClass))) }}&nbsp;-&nbsp;{{ __('crud-policies::crud.show') }}
        @endcan
        <div class="btn-group float-end" role="group" aria-label="{{ Str::plural(__('crud-policies::crud.action')) }}">
            @can('update', $model)
            <a href="{{ CrudController::getRoutePrefixed("$modelTable.edit", $model)}}" class="btn btn-info ">{{ __('crud-policies::crud.update') }}</a>
            @endcan
            @can('delete', $model)
            <form action="{{ CrudController::getRoutePrefixed("$modelTable.destroy", $model)}}" method="POST" onsubmit="__CRUD.confirmDelete(event)">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('crud-policies::crud.delete') }}</button>
            </form>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <table class="table table-sm table-bordered border-primary align-top table-striped text-center">
            <thead class="thead-dark">
            <tr>
                <th scope="col" class="w-25">{{ __('crud-policies::crud.property') }}</th>
                <th scope="col" class="w-75">{{ __('crud-policies::crud.value') }}</th>
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
                        <td>@include('crud-policies::crud.printfield', [
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
