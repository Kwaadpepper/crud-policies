<td>
    <label for="{{ $fieldName }}" class="form-label">{{ $prop['label'] }}</label>
</td>
<td>
    <div class="mb-3">
        <textarea id="{{ $fieldName }}" name="{{ $fieldName }}" rows="3" class="form-control" @if($readonly) readonly @endif>{{
            old($fieldName) ?? $model->{$fieldName} ?? ''
        }}</textarea>
    </div>
</td>
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(
            document.querySelector( '#{{ $fieldName }}' )
        )
        .catch( error => {
            console.error( error );
        } );
</script>
@endpush
