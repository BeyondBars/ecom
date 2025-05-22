<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'minimum_order_amount',
        'start_date',
        'expiry_date',
        'usage_limit',
        'usage_count',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_value' => 'float',
        'minimum_order_amount' => 'float',
        'start_date' => 'datetime',
        'expiry_date' => 'datetime',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
    ];

    /**
     * Check if the coupon is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $now = now();
        
        return $this->status === 'active' &&
               $now->greaterThanOrEqualTo($this->start_date) &&
               $now->lessThanOrEqualTo($this->expiry_date) &&
               ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    /**
     * Check if the coupon is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return now()->greaterThan($this->expiry_date);
    }

    /**
     * Check if the coupon has reached its usage limit.
     *
     * @return bool
     */
    public function hasReachedUsageLimit(): bool
    {
        return $this->usage_limit !== null && $this->usage_count >= $this->usage_limit;
    }

    /**
     * Increment the usage count.
     *
     * @return bool
     */
    public function incrementUsage(): bool
    {
        $this->usage_count++;
        return $this->save();
    }
}
