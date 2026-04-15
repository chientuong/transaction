<?php

namespace App\Domain\System\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'label',
        'type',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget("system_setting:{$setting->key}");
        });

        static::deleted(function ($setting) {
            Cache::forget("system_setting:{$setting->key}");
        });
    }

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("system_setting:{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value, ?string $label = null, string $type = 'text'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'label' => $label ?? $key,
                'type' => $type,
            ]
        );
    }
}
