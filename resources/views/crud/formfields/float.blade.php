<td>
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    <input
        id="{{ $fieldName }}"
        class="form-control"
        name="{{ $fieldName }}"
        type="number"
        placeholder="{{ $prop['placeholder'] }}"
        step="0.000001"
        @if(isset($unsigned))
        min="0"
        @endif
        value="{{ old($fieldName) ?? $model->{$fieldName} ?? $prop['rules']['min'] ?? 0 }}"
        @if(isset($prop['rules']['max']))max="{{ $prop['rules']['max'] }}"@endif
        @if(isset($prop['rules']['min']))min="{{ $prop['rules']['min'] }}"@endif
        @if(isset($prop['rules']['step']))step="{{ $prop['rules']['step'] }}"@endif
        @if($prop['disabled'])disabled @endif
        @if($readonly)readonly @endif
        @if($prop['required'] or (!$prop['required'] and !$prop['nullable']))required @endif>
</td>
