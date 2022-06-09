<div class="col-3 border border-primary">
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</div>
<div class="col-9 border border-primary">
    <input
        id="{{ $fieldName }}"
        class="form-control"
        name="{{ $fieldName }}"
        type="number"
        placeholder="{{ $prop['placeholder'] }}"
        @if(isset($unsigned))
        min="0"
        @endif
        value="{{ old($fieldName) ?? $model->{$fieldName} ?? $prop['rules']['min'] ?? $prop['default'] ?? 0 }}"
        @if(isset($prop['rules']['max']))max="{{ $prop['rules']['max'] }}"@endif
        @if(isset($prop['rules']['min']))min="{{ $prop['rules']['min'] }}"@endif
        @if(isset($prop['rules']['step']))step="{{ $prop['rules']['step'] }}"@endif
        @if($prop['disabled'])disabled @endif
        @if($readonly)readonly @endif
        @if($prop['required'] or (!$prop['required'] and !$prop['nullable']))required @endif>
</div>
