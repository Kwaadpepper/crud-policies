@php
    $related = isset($model) ? $model->{$fieldName}->mapWithKeys(function ($model) use($prop) {
        return [$model->{'id'} => [
            'label' => $model->{$prop['belongsToManyLabel']},
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
        class="belongs-to-many"
        data-json='@json($dataVue)'></div>
</td>

@push('scriptsConstants')
<script @if(isset($nonce) and is_string($nonce)) nonce="{{ $nonce }}" @endif>
    if (!window.__CRUD) {
        window.__CRUD = {};
    }
    if(!window.__CRUD._routes) {
        window.__CRUD._routes = {};
    }
    window.__CRUD._routes['{{$modelTable}}.{{$fieldName}}.model.index'] = '{{ CrudController::getRoutePrefixed((new $props[$fieldName]['belongsToMany'])->getTable().'.index') }}';
</script>
@endpush
