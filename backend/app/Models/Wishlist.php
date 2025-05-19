<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'is_public',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get the user that owns the wishlist.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the wishlist.
     */
    public function items()
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Get the products in the wishlist.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'wishlist_items')
            ->withPivot('notes')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include public wishlists.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
