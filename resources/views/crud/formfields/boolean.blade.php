<td>
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    <label for="{{ $fieldName }}" class="row">
        <div class="col">
            <div class="form-check form-switch ms-2">
                <input
                    id="{{ $fieldName }}"
                    name="{{ $fieldName }}"
                    type="checkbox"
                    class="ml-2 form-check-input"
                    value="1"
                    @if(old($fieldName) ?? ((isset($model) and $model->{$fieldName}) ? $model->{$fieldName} : false))checked @endif
                    @if($prop['disabled'])disabled @endif
                    @if($readonly)readonly @endif>
            </div>
        </div>
    </label>
</td>
