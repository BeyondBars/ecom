<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Admin-editable shop settings. Missing keys fall back to config('shop.defaults').
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    protected const CACHE_KEY = 'shop.settings';

    public static function allWithDefaults(): array
    {
        $stored = Cache::rememberForever(static::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());

        return array_merge(config('shop.defaults'), array_intersect_key($stored, config('shop.defaults')));
    }

    public static function value(string $key): mixed
    {
        return static::allWithDefaults()[$key] ?? null;
    }

    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(static::CACHE_KEY);
    }
}
