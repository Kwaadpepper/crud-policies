<td>
    <label class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
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
</td>
@include('crud-policies::crud.modules.ckeditor', [
    'fieldName' => $fieldName
])
