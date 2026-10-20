<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'is_secret'];

    protected $casts = ['value' => 'array', 'is_secret' => 'bool'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::all_cached();
        if (! array_key_exists($key, $all)) {
            return $default;
        }

        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', bool $secret = false): void
    {
        $stored = $value;
        if ($secret && is_string($value) && $value !== '') {
            $stored = ['__enc' => Crypt::encryptString($value)];
        }
        static::updateOrCreate(['key' => $key], ['value' => ['v' => $stored], 'group' => $group, 'is_secret' => $secret]);
        Cache::forget('settings.all');
    }

    public static function all_cached(): array
    {
        return Cache::remember('settings.all', 300, function () {
            $out = [];
            foreach (static::query()->get() as $s) {
                $v = $s->value['v'] ?? null;
                if (is_array($v) && isset($v['__enc'])) {
                    try {
                        $v = Crypt::decryptString($v['__enc']);
                    } catch (\Throwable) {
                        $v = null;
                    }
                }
                $out[$s->key] = $v;
            }

            return $out;
        });
    }

    /** Settings safe to expose to the public site. */
    public static function public(): array
    {
        $all = static::all_cached();
        $keys = ['site_mode', 'account_url', 'app_store_url', 'play_store_url', 'app_status', 'contact_emails',
            'company', 'social', 'analytics', 'default_units', 'telegram_channels', 'certification_status', 'legal_version'];

        return array_intersect_key($all, array_flip($keys));
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.all'));
        static::deleted(fn () => Cache::forget('settings.all'));
    }
}
