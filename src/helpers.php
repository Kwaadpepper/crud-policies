<?php

if (!function_exists('transFb')) {
    /**
     * Makes translation fall back to specified value if definition does not exist
     *
     * @param string      $key
     * @param null|string $fallback
     * @param null|string $locale
     * @param array|null  $replace
     *
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function transFb(string $key, ?string $fallback = null, ?string $locale = null, ?array $replace = [])
    {
        if (\Illuminate\Support\Facades\Lang::has($key, $locale)) {
            return trans($key, $replace, $locale);
        }
        return $fallback;
    }
}
