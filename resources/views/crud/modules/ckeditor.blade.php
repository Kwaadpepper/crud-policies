@push('scripts')
@if(!isset($ckeditorInput))
@php View::share('ckeditorInput', true) @endphp
<script src="https://cdn.ckeditor.com/ckeditor5/29.0.0/classic/ckeditor.js"></script>
@endif
<script>
document.addEventListener("DOMContentLoaded", function(event) {
    ClassicEditor
    .create(document.querySelector('#{{ $fieldName }}'))
    .catch(function(error) {
        console.error( error );
    });
});
</script>
@endpush
