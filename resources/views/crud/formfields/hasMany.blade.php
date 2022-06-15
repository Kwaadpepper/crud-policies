@php
    $related = isset($model) ? $model->{$fieldName}->mapWithKeys(function ($model) use($prop) {
        return [$model->{'id'} => [
            'label' => $model->{$prop['hasManyLabel']},
            'value' => $model->{'id'}
        ]];
    }) : [];
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
        'values' => $related
    ];
    @endphp
    <div
        class="has-many"
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
    window.__CRUD._routes['{{$modelTable}}.{{$fieldName}}.model.index'] = '{{  CrudController::getRoutePrefixed((new $props[$fieldName]['hasMany'])->getTable().'.index') }}';
</script>
@endpush
