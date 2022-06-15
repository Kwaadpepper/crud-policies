@extends($viewLayout)

@section('title', sprintf('%s - %s', transFb(sprintf('models.classes.%s', strtolower(Str::plural(class_basename($modelClass)))), Str::plural(class_basename($modelClass))), __('crud-policies::crud.edit')))
@section('description', sprintf('%s %s', __('crud-policies::crud.edit'), trans(class_basename($modelClass))))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('crud-policies::crud.modules.flashMessage')
    <div class="card-header">
        @include('crud-policies::crud.modules.breadcrumb', ['action' => CrudAction::update()])
        @include('crud-policies::crud.modules.actionbar.bar', ['action' => CrudAction::update()])
    </div>
    <div class="card-body">
        <form action="{{ CrudController::getRoutePrefixed("$modelTable.update", $model) }}" method="POST" @if($hasFile) enctype="multipart/form-data" @endif>
            @method('put')
            @csrf
            <div class="container text-center">
                <div class="row">
                    <div class="col-3 border border-secondary py-1 px-1">{{ __('crud-policies::crud.property') }}</div>
                    <div class="col-9 border border-secondary py-1 px-1">{{ __('crud-policies::crud.value') }}</div>
                </div>
                @foreach ($modelClass::getEditableProperties() as $fieldName => $prop)
                @if(in_array(CrudAction::update(), $prop['actions']))
                <div class="row">
                @include('crud-policies::crud.formfield')
                </div>
                @endif
                @endforeach
            </div>
            <div class="col text-center">
                <button class="btn btn-info" type="submit">{{ __('crud-policies::crud.update') }}</button>
            </div>
        </form>
    </div>
</div>
@include('crud-policies::crud.modules.scriptConstants')
@endsection
