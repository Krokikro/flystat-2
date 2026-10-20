<?php

namespace App\Support;

class Localize
{
    /** Recursively collapse {en:..., ru:...} maps to the requested locale (fallback: en, then first). */
    public static function resolve(mixed $data, string $locale): mixed
    {
        if (! is_array($data)) {
            return $data;
        }
        if (self::isTranslationMap($data)) {
            if (array_key_exists($locale, $data) && $data[$locale] !== null && $data[$locale] !== '') {
                return $data[$locale];
            }
            if (isset($data['en']) && $data['en'] !== '') {
                return $data['en'];
            }
            foreach ($data as $v) {
                if ($v !== null && $v !== '') {
                    return $v;
                }
            }

            return '';
        }
        $out = [];
        foreach ($data as $k => $v) {
            $out[$k] = self::resolve($v, $locale);
        }

        return $out;
    }

    public static function isTranslationMap(array $data): bool
    {
        if ($data === [] || array_is_list($data)) {
            return false;
        }
        $locales = config('flystat.locales');
        foreach (array_keys($data) as $k) {
            if (! in_array($k, $locales, true)) {
                return false;
            }
        }

        return true;
    }

    public static function locale(?string $locale): string
    {
        return in_array($locale, config('flystat.locales'), true) ? $locale : config('flystat.default_locale');
    }
}
