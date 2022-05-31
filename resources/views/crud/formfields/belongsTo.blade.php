@php
    $related = isset($model) ? $props[$fieldName]['belongsTo']::find($model->{$fieldName}) : null;
    if($related) {
        $related = [
            'label' => $related->crudLabel,
            'value' => $related->crudValue
        ];
    }
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
        'value' => $related
    ];
    @endphp
    <div
        class="belongs-to"
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
    window.__CRUD._routes['{{$modelTable}}.{{$fieldName}}.model.index'] = '{{ CrudController::getRoutePrefixed((new $props[$fieldName]['belongsTo'])->getTable().'.index') }}';
</script>
@endpush
