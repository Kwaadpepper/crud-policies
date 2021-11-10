<td>
    <label class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    <div class="crud-vuesjs">
        <custom-ckeditor
            upload="{{ route('crud-policies.upload') }}"
            id="{{ $fieldName }}"
            readonly="{{ $readonly ? 'true' : 'false' }}"
            csrf="{{ csrf_token() }}"
            name="{{ $fieldName }}"
            dbtext="{{ old($fieldName) ?? $model->{$fieldName} ?? $prop['default'] ?? '' }}"></custom-ckeditor>
    </div>
</td>
@include('crud-policies::crud.modules.ckeditor', [
    'fieldName' => $fieldName
])
