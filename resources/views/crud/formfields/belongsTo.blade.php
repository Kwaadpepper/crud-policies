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
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    <belongs-to
        readonly="{{ $readonly }}"
        name="{{ $fieldName }}"
        db-value='@json($related)'>
    </belongs-to>
</td>

@push('scriptsConstants')
<script>
    window._locale = '{{ app()->getLocale() }}';
    window._translations = @json(cache(sprintf('translations.%s', app()->getLocale())));
    window._asset = '{{ asset('') }}';
    window._routes = {
        'model.index': '{{ route('bo.'.(new $props[$fieldName]['belongsTo'])->getTable().'.index') }}'
    };
</script>
@endpush
