<td>
    <label class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    <div>
        <textarea id="{{ $fieldName }}" name="{{ $fieldName }}" class="ckeditor form-control" @if($readonly) readonly @endif>{{
            old($fieldName) ?? $model->{$fieldName} ?? $prop['default'] ?? ''
        }}</textarea>
    </div>
</td>
@include('crud-policies::crud.modules.ckeditor', [
    'fieldName' => $fieldName
])
