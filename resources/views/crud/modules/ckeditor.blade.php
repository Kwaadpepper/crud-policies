@push('scripts')
@if(!isset($ckeditorInput))
@php View::share('ckeditorInput', true) @endphp
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
@endif
<script>
ClassicEditor
.create(
    document.querySelector( '#{{ $fieldName }}' )
)
.catch( error => {
    console.error( error );
});
</script>
@endpush
