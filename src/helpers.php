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

if (! function_exists('array_merge_recursive_distinct')) {
    function array_merge_recursive_distinct(array $array1, array $array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
