@php
    $related = isset($model) ? $props[$fieldName]['belongsTo']::find($model->{$fieldName}) : null;
    if($related) {
        $related = [
            'label' => $related->crudLabel,
            'value' => $related->crudValue
        ];
    }
@endphp
<div class="col-3 border border-secondary py-1 px-1">
    <label class="form-label">{{ $prop['label'] }}</label>
</div>
<div class="col-9 border border-secondary py-1 px-1">
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
</div>

@push('scriptsConstants')
<script @if(isset($nonce) and is_string($nonce)) nonce="{{ $nonce }}" @endif>
    if (!window.__CRUD) {
        window.__CRUD = {};
    }
    if(!window.__CRUD._routes) {
        window.__CRUD._routes = {};
    }
    window.__CRUD._routes['{{$modelTable}}.{{$fieldName}}.model.index'] = '{{ CrudController::getRoutePrefixed((new $props[$fieldName]['belongsTo'])->getTable().'.index') }}';
</script>
@endpush
