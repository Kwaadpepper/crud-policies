<div class="col-3 border border-primary">
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</div>
<div class="col-9 border border-primary">
    @if(strlen($prop['placeholder']))
    <div class="form-floating">
        <input
            id="{{ $fieldName }}"
            name="{{ $fieldName }}"
            type="text"
            class="form-control"
            value="{{ old($fieldName) ?? $model->{$fieldName} ?? $prop['default'] ?? '' }}"
            @if(isset($prop['rules']['max']))maxlength="{{ $prop['rules']['max'] }}"@endif
            @if(isset($prop['rules']['min']))minlength="{{ $prop['rules']['min'] }}"@endif
            @if($prop['disabled'])disabled @endif
            @if($readonly)readonly @endif
            @if($prop['required'] or (!$prop['required'] and !$prop['nullable']))required @endif>
        <label for="{{ $fieldName }}" class="form-label">{{ __('ex: :placeholder', ['placeholder' => $prop['placeholder']]) }}</label>
    </div>
    @else
    <input
        id="{{ $fieldName }}"
        name="{{ $fieldName }}"
        type="text"
        class="form-control"
        value="{{ old($fieldName) ?? $model->{$fieldName} ?? $prop['default'] ?? '' }}"
        @if(isset($prop['rules']['max']))maxlength="{{ $prop['rules']['max'] }}"@endif
        @if(isset($prop['rules']['min']))minlength="{{ $prop['rules']['min'] }}"@endif
        @if($prop['disabled'])disabled @endif
        @if($readonly)readonly @endif
        @if($prop['required'] or (!$prop['required'] and !$prop['nullable']))required @endif>
    @endif
</div>
