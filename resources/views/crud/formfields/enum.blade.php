<div class="col-3 border border-primary">
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</div>
<div class="col-9 border border-primary">
    <select id="{{ $fieldName }}" class="form-select" name="{{ $fieldName }}" @if($readonly) readonly @endif @if($prop['disabled']) disabled @endif>
        {{-- TODO handle enum nullable ? --}}
        {{-- @if($prop['nullable'])
        @endif --}}
        @foreach ($prop['enum']::toArray() as $enum)
            <option value="{{$enum->value}}" @if(isset($model) and $enum->equals($model->{$fieldName}))selected @endif>{{__($enum->label)}}</option>
        @endforeach
    </select>
</div>
