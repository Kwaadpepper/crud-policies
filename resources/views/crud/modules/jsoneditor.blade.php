@if(!isset($JsonEditor))
@push('scripts')
@php View::share('JsonEditor', true) @endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.5.1/jsoneditor.min.js" integrity="sha512-NA6kPrAqyMsKDzpFPDLTE9TJn1iwCXSQOSIpdWBgVJMrnNIeYZECh8ePT5QI1tBVN6ZiTklWW3RKOLlxQDfMkA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
let container = document.getElementById('{{ $fieldName.'jsoneditor' }}');
let input = document.getElementById('{{ $fieldName }}');
let editor = new JSONEditor(container, {
    "id": "{{ $fieldName }}",
    "mode": "code",
    "indentation": 2,
    "onChangeText": function(jsonString) {
        input.value = jsonString;
    }
})
editor.set({!! $value !!});
</script>
@endpush
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.5.1/jsoneditor.min.css" integrity="sha512-ZS7UFcBWqviCnOtlrIz47Z10BQYs/qYJLIh/uUIVHRBjJ2zDVZ7ALvPssEqRLDObR66r5fEaY8NaLvIOOxQ1Vw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@endif
