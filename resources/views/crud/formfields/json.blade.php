@php
$jsonValue = old($fieldName) ?? (
    (isset($model) and $model->{$fieldName}) ?
    (
        is_array($model->{$fieldName}) ?
        json_encode($model->{$fieldName}) : $model->{$fieldName}
    ) : $prop['default']
);
@endphp
<div class="col-3 border border-secondary py-1 px-1">
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</div>
<div class="col-9 border border-secondary py-1 px-1">
    <div class="mb-3">
        <div id="{{ $fieldName }}jsoneditor"></div>
        <textarea id="{{ $fieldName }}" name="{{ $fieldName }}" rows="3" class="form-control d-none" @if($readonly) readonly @endif>{!! $jsonValue !!}</textarea>
    </div>
</div>
@include('crud-policies::crud.modules.jsoneditor', [
    'fieldName' => $fieldName,
    'value' => $jsonValue,
    'prop' => $prop
])
