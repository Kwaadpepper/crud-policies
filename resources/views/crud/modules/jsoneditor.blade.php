@if(!isset($JsonEditor))
@push('scripts')
@php View::share('JsonEditor', true) @endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.5.1/jsoneditor.min.js" integrity="sha512-NA6kPrAqyMsKDzpFPDLTE9TJn1iwCXSQOSIpdWBgVJMrnNIeYZECh8ePT5QI1tBVN6ZiTklWW3RKOLlxQDfMkA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.5.1/jsoneditor.min.css" integrity="sha512-ZS7UFcBWqviCnOtlrIz47Z10BQYs/qYJLIh/uUIVHRBjJ2zDVZ7ALvPssEqRLDObR66r5fEaY8NaLvIOOxQ1Vw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@endif

@push('scripts')
<script @if(isset($nonce) and is_string($nonce)) nonce="{{ $nonce }}" @endif>
document.addEventListener("DOMContentLoaded", function(event) {
    let container = document.getElementById('{{ $fieldName.'jsoneditor' }}');
    let input = document.getElementById('{{ $fieldName }}');
    window.editor = new JSONEditor(container, {
        mode: '{{ $prop['mode'] }}',
        modes: {!! json_encode($prop['modes']) !!},
        indentation: 2,
        onError: function(error) {
            console.log(error);
        },
        onChange: function() {
            input.value = editor.getText();
        }
    }, {!! $jsonValue !!});
});
</script>
@endpush
