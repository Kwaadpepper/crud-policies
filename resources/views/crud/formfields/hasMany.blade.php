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
<td>
    @php
    $dataVue = [
        'readonly' => $readonly,
        'tablename' => $modelTable,
        'name' => $fieldName,
        'values' => $related
    ];
    @endphp
    <div
        class="has-many"
        data-json='@json($dataVue)'></div>
</td>

@push('scriptsConstants')
<script>
    if (!window.__CRUD) {
        window.__CRUD = {};
    }
    if(!window.__CRUD._routes) {
        window.__CRUD._routes = {};
    }
    window.__CRUD._routes['{{$modelTable}}.{{$fieldName}}.model.index'] = '{{  CrudController::getRoutePrefixed((new $props[$fieldName]['hasMany'])->getTable().'.index') }}';
</script>
@endpush
