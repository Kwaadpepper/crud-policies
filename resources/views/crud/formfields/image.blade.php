<td>
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    @if(strlen($prop['placeholder']))
    <div class="form-floating">
    @endif
        <input
            id="{{ $fieldName }}"
            name="{{ $fieldName }}"
            type="file"
            class="form-control"
            value="{{ old($fieldName) ?? $model->{$fieldName} ?? $prop['default'] ?? '' }}"
            @if(isset($prop['rules']['max']))maxlength="{{ $prop['rules']['max'] }}"@endif
            @if(isset($prop['rules']['min']))minlength="{{ $prop['rules']['min'] }}"@endif
            @if($prop['disabled'])disabled @endif
            @if($readonly)readonly @endif
            @if(($prop['required'] or (!$prop['required'] and !$prop['nullable'])) and !(isset($model) and $model->{$fieldName}))required @endif>
    @if(strlen($prop['placeholder']))
        <label for="{{ $fieldName }}" class="form-label">{{ __('ex: :placeholder', ['placeholder' => $prop['placeholder']]) }}</label>
    </div>
    @endif
    @if(isset($model) and $model->{$fieldName})
    <img class="img-responsive" src="{{ sprintf('/storage/%s', $model->{$fieldName}) }}" alt="{{ sprintf('/storage/%s', $model->{$fieldName}) }}">
    @endif
</td>
