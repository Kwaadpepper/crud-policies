<?php


use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Lang;

if (! function_exists('transFb')) {
    /**
     * Makes translation fall back to specified value if definition does not exist
     *
     *
     * @return array|Translator|string
     */
    function transFb(string $key, ?string $fallback = null, ?string $locale = null, ?array $replace = [])
    {
        if (Lang::has($key, $locale)) {
            return trans($key, $replace, $locale);
        }

        return $fallback ?? $key;
    }
}
