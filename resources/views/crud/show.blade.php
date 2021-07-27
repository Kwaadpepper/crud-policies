@extends($viewLayout)

@section('title', sprintf('%s - %s', transFb(sprintf('models.classes.%s', strtolower(Str::plural(class_basename($modelClass)))), Str::plural(class_basename($modelClass))), __('crud-policies::crud.view')))
@section('description', sprintf('%s %s', __('crud-policies::crud.view'), trans(class_basename($modelClass))))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('crud-policies::crud.modules.flashMessage')
    <div class="card-header">
        @include('crud-policies::crud.modules.breadcrumb', ['action' => CrudAction::view()])
        @include('crud-policies::crud.modules.actionbar.bar', ['action' => CrudAction::view()])
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
@include('crud-policies::crud.modules.scriptConstants')
@endsection
