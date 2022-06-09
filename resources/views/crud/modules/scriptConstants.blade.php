@push('scriptsConstants')
<script @if(isset($nonce) and is_string($nonce)) nonce="{{ $nonce }}" @endif>
    if (!window.__CRUD) {
        window.__CRUD = {};
    }
    window.__CRUD._locale = '{{ app()->getLocale() }}';
    window.__CRUD._translations = @json(cache(sprintf('crud-policies.translations.%s', app()->getLocale())) ?? '{}');
    window.__CRUD._asset = '{{ asset('') }}';
</script>
@endpush
