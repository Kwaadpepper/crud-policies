@extends($viewLayout)

@section('title', sprintf('%s - %s', transFb(sprintf('models.classes.%s', strtolower(Str::plural(class_basename($modelClass)))), Str::plural(class_basename($modelClass))), __('crud-policies::crud.create')))
@section('description', sprintf('%s %s', __('crud-policies::crud.create'), class_basename($modelClass)))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('crud-policies::crud.modules.flashMessage')
    <div class="card-header">
        @include('crud-policies::crud.modules.breadcrumb', ['action' => CrudAction::create()])
        @include('crud-policies::crud.modules.actionbar.bar', ['action' => CrudAction::create()])
    </div>
    <div class="card-body">
        <form action="{{ CrudController::getRoutePrefixed("$modelTable.store") }}" method="POST" @if($hasFile) enctype="multipart/form-data" @endif>
            @csrf
            <div class="container text-center">
                <div class="row">
                    <div class="col-3 border border-secondary py-1 px-1">{{ __('crud-policies::crud.property') }}</div>
                    <div class="col-9 border border-secondary py-1 px-1">{{ __('crud-policies::crud.value') }}</div>
                </div>
                @foreach ($modelClass::getEditableProperties() as $fieldName => $prop)
                @php
                    // create form cannot accept readonly field
                    $prop['readonly'] = false;
                @endphp
                @if(in_array(CrudAction::create(), $prop['actions']))
                <div class="row">
                @include('crud-policies::crud.formfield')
                </div>
                @endif
                @endforeach
                <div class="row">
                    <div class="col text-center">
                        <button class="btn btn-info" type="submit">{{ __('crud-policies::crud.create') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@include('crud-policies::crud.modules.scriptConstants')
@endsection
