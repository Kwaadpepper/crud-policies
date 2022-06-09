<div class="col-3 border border-primary">
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</div>
<div class="col-9 border border-primary">
    @if(strlen($prop['placeholder']))
    <div class="form-floating">
    @endif
        <input
            id="{{ $fieldName }}"
            name="{{ $fieldName }}"
            type="file"
            class="form-control"
            value="{{ old($fieldName) ?? $model->{$fieldName} ?? $prop['default'] ?? '' }}"
            @if($prop['disabled'])disabled @endif
            @if($readonly)readonly @endif
            @if(($prop['required'] or (!$prop['required'] and !$prop['nullable'])) and !(isset($model) and $model->{$fieldName}))required @endif>
    @if(strlen($prop['placeholder']))
        <label for="{{ $fieldName }}" class="form-label">{{ __('ex: :placeholder', ['placeholder' => $prop['placeholder']]) }}</label>
    </div>
    @endif
    @if(isset($model) and $model->{$fieldName})
    <img class="img-fluid" src="{{ sprintf('/storage/%s', $model->{$fieldName}) }}" alt="{{ sprintf('/storage/%s', $model->{$fieldName}) }}">
    @endif
</div>
