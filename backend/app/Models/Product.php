<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'stock',
        'category_id',
        'brand_id',
        'image',
        'status',
        'featured',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'boolean',
        'featured' => 'boolean',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the invoice items for the product.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the wishlists that contain this product.
     */
    public function wishlists(): BelongsToMany
    {
        return $this->belongsToMany(Wishlist::class, 'wishlist_items')
            ->withPivot('notes', 'priority')
            ->withTimestamps();
    }

    /**
     * Get the likes for the product.
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Get the users who liked this product.
     */
    public function likedBy(): BelongsToMany
    {
        return $this->morphToMany(User::class, 'likeable', 'likes');
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the average rating for the product.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->approved()->avg('rating') ?: 0;
    }

    /**
     * Get the total number of reviews for the product.
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->approved()->count();
    }
}
