<td>
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    <select
        id="{{ $fieldName }}"
        name="{{ $fieldName }}"
        class="form-select"
        @if($readonly)readonly @endif
        @if($prop['required'] or (!$prop['required'] and !$prop['nullable']))required @endif
        @if($prop['disabled'])disabled @endif>
    @foreach($modelClass::orderBy($fieldName)->pluck($fieldName) as $order)
        <option value="{{ $order }}" @if(isset($model) and $model->order === $order)selected @endif>{{ $order }}</option>
    @endforeach
    @if(!isset($model))
    <option value="{{ $modelClass::pluck($fieldName)->max() +1 }}" selected>{{ $modelClass::pluck($fieldName)->max() +1 }}</option>
    @endif
    </select>
</td>
