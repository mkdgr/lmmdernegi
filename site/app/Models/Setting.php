<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Basit anahtar/değer ayarları (iletişim bilgileri, IBAN'lar, sosyal medya, ödeme ayarları).
 * Değerler JSON saklanır; çevrilebilir metinler {"tr": "...", "en": "..."} biçimindedir.
 */
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = ['value' => 'array'];

    public const CACHE_KEY = 'site-settings';

    public static function allValues(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, $default = null)
    {
        $value = static::allValues()[$key] ?? null;
        if (is_array($value) && array_key_exists('v', $value)) {
            $value = $value['v'];
        }
        if (is_array($value) && (array_key_exists('tr', $value) || array_key_exists('en', $value))) {
            $value = $value[app()->getLocale()] ?? $value['tr'] ?? null;
        }

        return blank($value) ? $default : $value;
    }

    public static function put(string $key, $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => is_array($value) ? $value : ['v' => $value]]);
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }
}
