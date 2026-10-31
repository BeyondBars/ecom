<?php

namespace App\Support\Traits;

use Illuminate\Support\Str;

/**
 * Fills `slug` from `name` (or `title`) when it's left empty, adding -2, -3... to keep it unique.
 */
trait HasUniqueSlug
{
    protected static function bootHasUniqueSlug(): void
    {
        static::saving(function ($model) {
            if (blank($model->slug)) {
                $model->slug = static::uniqueSlug($model->name ?? $model->title, $model->getKey());
            }
        });
    }

    public static function uniqueSlug(string $source, $ignoreId = null): string
    {
        $base = Str::slug($source) ?: Str::lower(Str::random(8));
        $slug = $base;
        $suffix = 2;

        while (static::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
