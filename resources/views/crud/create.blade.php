@extends($viewLayout)

@section('title', sprintf('%s - %s', transFb(sprintf('models.classes.%s', strtolower(Str::plural(class_basename($modelClass)))), Str::plural(class_basename($modelClass))), __('crud-policies::crud.create')))
@section('description', sprintf('%s %s', __('crud-policies::crud.create'), class_basename($modelClass)))
@section('metaIndex', 'noindex,nofollow')

@section('content')
<div class="card">
    @include('crud-policies::crud.modules.flashMessage')
    <div class="card-header">
        @include('crud-policies::crud.modules.breadcrumb', ['action' => 'create'])
    </div>
    <div class="card-body">
        <form action="{{ CrudController::getRoutePrefixed("$modelTable.store") }}" method="POST" @if($hasImage) enctype="multipart/form-data" @endif>
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
                    @php
                        // create form cannot accept readonly field
                        $prop['readonly'] = false;
                    @endphp
                    @if(in_array(CrudAction::create(), $prop['actions']))
                    <tr>
                    @include('crud-policies::crud.formfield')
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            <div class="col text-center">
                <button class="btn btn-info" type="submit">{{ __('crud-policies::crud.create') }}</button>
            </div>
        </form>
    </div>
</div>
@include('crud-policies::crud.modules.scriptConstants')
@endsection
