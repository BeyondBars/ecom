<?php

namespace App\Models;

use App\Support\Enums\ProductStatus;
use App\Support\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    use HasUniqueSlug;

    public const LOW_STOCK_THRESHOLD = 5;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'sale_price',
        'cost',
        'stock',
        'status',
        'is_featured',
        'options',
        'specifications',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    protected static function booted(): void
    {
        // Back in stock: email everyone who asked to be told
        static::updated(function (Product $product) {
            if ($product->wasChanged('stock') && (int) $product->getOriginal('stock') <= 0 && $product->stock > 0) {
                app(\App\Services\Shop\StockAlertNotifier::class)->notify($product);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cost' => 'decimal:2',
            'rating' => 'decimal:2',
            'stock' => 'integer',
            'review_count' => 'integer',
            'status' => ProductStatus::class,
            'is_featured' => 'boolean',
            'options' => 'array',
            'specifications' => 'array',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes & helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Products customers can see: active and in an active category.
     */
    public function scopeVisible($query): void
    {
        $query->where('products.status', ProductStatus::Active->value)
            ->whereHas('category', fn ($q) => $q->active());
    }

    public function isVisible(): bool
    {
        return $this->status === ProductStatus::Active && (bool) $this->category?->is_active;
    }

    /**
     * The price customers pay: the sale price when one is set and lower than the regular price.
     */
    public function currentPrice(): float
    {
        $price = (float) $this->price;
        $sale = $this->sale_price !== null ? (float) $this->sale_price : null;

        return $sale !== null && $sale < $price ? $sale : $price;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Checks a customer's chosen options against the product's options: every option needs exactly
     * one listed value, and nothing else is allowed. Returns the options in the product's order.
     *
     * @throws \InvalidArgumentException
     */
    public function normaliseSelectedOptions(array $selected): array
    {
        $normalised = [];

        foreach ($this->options ?? [] as $option) {
            $value = $selected[$option['name']] ?? null;

            if (!is_string($value) || !in_array($value, $option['values'], true)) {
                throw new \InvalidArgumentException("Choose a valid {$option['name']} for {$this->name}.");
            }

            $normalised[$option['name']] = $value;
        }

        if (count($selected) !== count($normalised)) {
            throw new \InvalidArgumentException("{$this->name} has no such option.");
        }

        return $normalised;
    }

    public function refreshRating(): void
    {
        $approved = $this->reviews()->approved();

        $this->forceFill([
            'rating' => round((float) $approved->avg('rating'), 2),
            'review_count' => $approved->count(),
        ])->saveQuietly();
    }
}
