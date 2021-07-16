@push('scriptsConstants')
<script>
    if (!window.__CRUD) {
        window.__CRUD = {};
    }
    window.__CRUD._locale = '{{ app()->getLocale() }}';
    window.__CRUD._translations = @json(cache(sprintf('crud-policies.translations.%s', app()->getLocale())) ?? '{}');
    window.__CRUD._asset = '{{ asset('') }}';
</script>
@endpush
