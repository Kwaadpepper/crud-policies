@php
    $related = isset($model) ? $model->{$fieldName}->mapWithKeys(function ($model) use($prop) {
        return [$model->{'id'} => [
            'label' => $model->{$prop['hasManyLabel']},
            'value' => $model->{'id'}
        ]];
    }) : [];
@endphp
<td>
    <label class="form-label">{{ $prop['label'] }}</label>
</td>
<td class="crud-vuesjs">
    <has-many
        readonly="{{ $readonly }}"
        tablename="{{ $modelTable }}"
        name="{{ $fieldName }}"
        db-value='@json($related)'>
    </has-many>
</td>

@push('scriptsConstants')
<script>
    if (!window.__CRUD) {
        window.__CRUD = {};
    }
    if(!window.__CRUD._routes) {
        window.__CRUD._routes = {};
    }
    window.__CRUD._routes['{{$modelTable}}.{{$fieldName}}.model.index'] = '{{ CrudController::getRoutePrefixed("$modelTable.$fieldName.index", $model) }}';
</script>
@endpush
