<?php

namespace App\Models;

use App\Support\Enums\ModerationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['rating', 'title', 'comment', 'status', 'verified_purchase', 'is_featured'];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => ModerationStatus::class,
            'verified_purchase' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Keep the product's average rating and review count in step with approved reviews
        static::saved(fn (Review $review) => $review->product?->refreshRating());
        static::deleted(fn (Review $review) => $review->product?->refreshRating());
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query): void
    {
        $query->where('reviews.status', ModerationStatus::Approved->value);
    }
}
