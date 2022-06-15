<div class="col-3 border border-secondary py-1 px-1">
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</div>
<div class="col-9 border border-secondary py-1 px-1">
    @if(strlen($prop['placeholder']))
    <div class="form-floating">
        <input
            id="{{ $fieldName }}"
            name="{{ $fieldName }}"
            type="date"
            class="form-control"
            value="{{ old($fieldName, (!empty($model->{$fieldName}) ? $model->{$fieldName}->format('Y-m-d') : null) ?? $prop['default'] ?? '') }}"
            @if(isset($prop['rules']['max']))max="{{ $prop['rules']['max'] }}"@endif
            @if(isset($prop['rules']['min']))min="{{ $prop['rules']['min'] }}"@endif
            @if($prop['disabled'])disabled @endif
            @if($readonly)readonly @endif
            @if($prop['required'] or (!$prop['required'] and !$prop['nullable']))required @endif>
        <label for="{{ $fieldName }}" class="form-label">{{ __('ex: :placeholder', ['placeholder' => $prop['placeholder']]) }}</label>
    </div>
    @else
    <input
        id="{{ $fieldName }}"
        name="{{ $fieldName }}"
        type="date"
        class="form-control"
        value="{{ old($fieldName, (!empty($model->{$fieldName}) ? $model->{$fieldName}->format('Y-m-d') : null) ?? $prop['default'] ?? '') }}"
        @if(isset($prop['rules']['max']))max="{{ $prop['rules']['max'] }}"@endif
        @if(isset($prop['rules']['min']))min="{{ $prop['rules']['min'] }}"@endif
        @if($prop['disabled'])disabled @endif
        @if($readonly)readonly @endif
        @if($prop['required'] or (!$prop['required'] and !$prop['nullable']))required @endif>
    @endif
</div>
