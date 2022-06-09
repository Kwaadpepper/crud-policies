<div class="col-3 border border-primary">
    <label class="form-label">{{ $prop['label'] }}</label>
</div>
<div class="col-9 border border-primary">
    @php
    $dataVue = [
        'id' => $fieldName,
        'name' => $fieldName,
        'readonly' => $readonly,
        'tablename' => $modelTable,
        'csrf' => csrf_token(),
        'value' => old($fieldName, $model->{$fieldName} ?? $prop['default'] ?? '')
    ];
    @endphp
    <div
        class="custom-ckeditor"
        data-json='@json($dataVue)'></div>
</div>
@include('crud-policies::crud.modules.ckeditor', [
    'fieldName' => $fieldName
])
