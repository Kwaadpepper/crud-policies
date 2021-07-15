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
        name="{{ $fieldName }}"
        db-value='@json($related)'>
    </belongs-to-many>
</td>

@push('scriptsConstants')
<script>
    window._locale = '{{ app()->getLocale() }}';
    window._translations = @json(cache(sprintf('translations.%s', app()->getLocale())));
    window._asset = '{{ asset('') }}';
    window._routes = {
        'model.index': '{{ CrudController::getRoutePrefixed((new $props[$fieldName]['belongsToMany'])->getTable().'.index') }}'
    };
</script>
@endpush
