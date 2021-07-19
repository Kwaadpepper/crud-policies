<td>
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    <div class="mb-3">
        <textarea id="{{ $fieldName }}" name="{{ $fieldName }}" rows="3" class="form-control" @if($readonly) readonly @endif>{{
            old($fieldName) ?? ((isset($model) and $model->{$fieldName}) ? (is_array($model->{$fieldName}) ? json_encode($model->{$fieldName}) : $model->{$fieldName}) : '')
        }}</textarea>
    </div>
</td>
@include('crud-policies::crud.modules.ckeditor', [
    'fieldName' => $fieldName
])
