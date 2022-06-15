<div class="col-3 border border-secondary py-1 px-1">
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</div>
<div class="col-9 border border-secondary py-1 px-1">
    @if(strlen($prop['placeholder']))
    <div class="form-floating">
        <input
            id="{{ $fieldName }}"
            name="{{ $fieldName }}"
            type="password"
            class="form-control"
            placeholder="{{ $prop['placeholder'] }}"
            @if(isset($prop['rules']['max']))maxlength="{{ $prop['rules']['max'] }}"@endif
            @if(isset($prop['rules']['min']))minlength="{{ $prop['rules']['min'] }}"@endif
            @if($prop['disabled'])disabled @endif
            @if($readonly)readonly @endif
            @if($prop['required'] or !$prop['nullable'])required @endif>
        <label for="{{ $fieldName }}" class="form-label">{{ __('ex: :placeholder', ['placeholder' => $prop['placeholder']]) }}</label>
    </div>
    @else
    <input
        id="{{ $fieldName }}"
        name="{{ $fieldName }}"
        type="password"
        class="form-control"
        placeholder="{{ $prop['placeholder'] }}"
        @if(isset($prop['rules']['max']))maxlength="{{ $prop['rules']['max'] }}"@endif
        @if(isset($prop['rules']['min']))minlength="{{ $prop['rules']['min'] }}"@endif
        @if($prop['disabled'])disabled @endif
        @if($readonly)readonly @endif
        @if($prop['required'] or !$prop['nullable'])required @endif>
    @endif
</div>
</tr><tr>
<td>
    <label for="{{ $fieldName }}-confirm" class="form-label">{{ __('Confirmation')}}&nbsp;{{ $prop['label'] }}</label>
</td>
<td>
    @if(strlen($prop['placeholder']))
    <div class="form-floating">
        <input
            id="{{ $fieldName }}-confirm"
            name="{{ $fieldName }}-confirm"
            type="password"
            class="form-control"
            placeholder="{{ $prop['placeholder'] }}"
            @if(isset($prop['rules']['max']))maxlength="{{ $prop['rules']['max'] }}"@endif
            @if(isset($prop['rules']['min']))minlength="{{ $prop['rules']['min'] }}"@endif
            @if($prop['disabled'])disabled @endif
            @if($readonly)readonly @endif
            @if($prop['required'] or !$prop['nullable'])required @endif>
        <label for="{{ $fieldName }}-confirm" class="form-label">{{ __('ex: :placeholder', ['placeholder' => $prop['placeholder']]) }}</label>
    </div>
    @else
    <input
        id="{{ $fieldName }}-confirm"
        name="{{ $fieldName }}-confirm"
        type="password"
        class="form-control"
        placeholder="{{ $prop['placeholder'] }}"
        @if(isset($prop['rules']['max']))maxlength="{{ $prop['rules']['max'] }}"@endif
        @if(isset($prop['rules']['min']))minlength="{{ $prop['rules']['min'] }}"@endif
        @if($prop['disabled'])disabled @endif
        @if($readonly)readonly @endif
        @if($prop['required'] or !$prop['nullable'])required @endif>
    @endif
</td>
