@if(!isset($CrudColorLib))
@push('scripts')
@php View::share('CrudColorLib', true) @endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.4.5/jscolor.min.js"></script>
@endpush
@endif
