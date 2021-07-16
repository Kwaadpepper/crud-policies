@php
    $related = isset($model) ? $model->{$fieldName}->mapWithKeys(function ($model) use($prop) {
        return [$model->{'id'} => [
            'label' => $model->{$prop['belongsToManyLabel']},
            'value' => $model->{'id'}
        ]];
    }) : [];
@endphp
<td>
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    <belongs-to-many
        readonly="{{ $readonly }}"
        tablename="{{ $modelTable }}"
        name="{{ $fieldName }}"
        db-value='@json($related)'>
    </belongs-to-many>
</td>

@push('scriptsConstants')
<script>
    if (!window.__CRUD) {
        window.__CRUD = {};
    }
    if(!window.__CRUD._routes) {
        window.__CRUD._routes = {};
    }
    window.__CRUD._routes['{{$modelTable}}.{{$fieldName}}.model.index'] = '{{ CrudController::getRoutePrefixed((new $props[$fieldName]['belongsToMany'])->getTable().'.index') }}';
</script>
@endpush
