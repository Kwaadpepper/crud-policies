@if(!isset($CrudColorLib))
@push('scripts')
@php View::share('CrudColorLib', true) @endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.4.5/jscolor.min.js"></script>
@endpush
@endif
@if(isset($crudColorShow) and !isset($colorizedScript))
@php View::share('colorizedScript', true) @endphp
@push('scripts')
<script>
window.addEventListener("DOMContentLoaded", (event) => {
    // https://stackoverflow.com/a/35970186/4355295
    let __invertColor = function(hex, bw) {
        if (hex.indexOf('#') === 0) {
            hex = hex.slice(1);
        }
        // convert 3-digit hex to 6-digits.
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        if (hex.length !== 6) {
            throw new Error('Invalid HEX color.');
        }
        var r = parseInt(hex.slice(0, 2), 16),
            g = parseInt(hex.slice(2, 4), 16),
            b = parseInt(hex.slice(4, 6), 16);
        if (bw) {
            // https://stackoverflow.com/a/3943023/112731
            return (r * 0.299 + g * 0.587 + b * 0.114) > 186
                ? '#000000'
                : '#FFFFFF';
        }
        // invert color components
        r = (255 - r).toString(16);
        g = (255 - g).toString(16);
        b = (255 - b).toString(16);
        // pad each with zeros and return
        return "#" + r.padStart(1, '0') + g.padStart(1, '0') + b.padStart(1, '0');
    }
    let colorSpans = document.getElementsByClassName('colorize');
    if(colorSpans.length) {
        for(let colorSpan of colorSpans) {
            colorSpan.style.color = __invertColor(colorSpan.textContent, true);
            colorSpan.style.backgroundColor = colorSpan.textContent;
        }
    }
});
</script>
@endpush
@endif
