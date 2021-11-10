@if(!isset($ckeditorInput))
@php
    View::share('ckeditorInput', true);
    Session::push('key.subArray', 'value');
@endphp
@push('scriptsConstants')
<script>
    if (!window.__CRUD) {
        window.__CRUD = {};
    }
    if(!window.__CRUD._routes) {
        window.__CRUD._routes = {};
    }
    window.__CRUD._routes['crud-policies.upload'] = '{{ route('crud-policies.upload') }}';
</script>
@endpush
@endif
