<?php

namespace App\Models\Concerns;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Support\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Shop relationships for users. Kept in a trait so the starter's User model stays easy to merge.
 */
trait ShopCustomer
{
    /**
     * Shoppers have the standard role; staff and admins use the back office.
     */
    public function isCustomer(): bool
    {
        return $this->isA(UserRole::User->value);
    }

    public function storefrontUrl(string $path = ''): string
    {
        return rtrim(config('shop.storefront_url'), '/').'/'.ltrim($path, '/');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->orderByDesc('is_default')->orderBy('id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlist(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlist_items')->withTimestamps();
    }
}
