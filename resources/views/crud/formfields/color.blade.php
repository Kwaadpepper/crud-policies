<td>
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    @if(strlen($prop['placeholder']))
    <div class="form-floating">
        <input
            id="{{ $fieldName }}"
            name="{{ $fieldName }}"
            type="text"
            data-jscolor=""
            class="form-control"
            value="{{ old($fieldName) ?? $model->{$fieldName} ?? $prop['default'] ?? '' }}"
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
        data-jscolor=""
        class="form-control"
        value="{{ old($fieldName) ?? $model->{$fieldName} ?? $prop['default'] ?? '' }}"
        @if($prop['disabled'])disabled @endif
        @if($readonly)readonly @endif
        @if($prop['required'] or (!$prop['required'] and !$prop['nullable']))required @endif>
    @endif
</td>
@include('crud-policies::crud.modules.color')
