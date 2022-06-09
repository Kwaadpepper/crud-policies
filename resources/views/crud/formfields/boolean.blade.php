<div class="col-3 border border-primary">
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</div>
<div class="col-9 border border-primary">
    <label for="{{ $fieldName }}" class="row">
        <div class="col">
            <div class="form-check form-switch ms-2">
                <input
                    id="{{ $fieldName }}"
                    name="{{ $fieldName }}"
                    type="checkbox"
                    class="ml-2 form-check-input"
                    value="1"
                    @if(old($fieldName) ?? ((isset($model) and $model->{$fieldName}) ? $model->{$fieldName} : $prop['default'] ?? false))checked @endif
                    @if($prop['disabled'] or $readonly)disabled @endif>
            </div>
        </div>
    </label>
</div>
