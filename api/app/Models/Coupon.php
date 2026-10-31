<?php

namespace App\Models;

use App\Support\Enums\CouponType;
use App\Support\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'min_subtotal',
        'max_discount',
        'starts_at',
        'expires_at',
        'usage_limit',
        'usage_limit_per_user',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => CouponType::class,
            'value' => 'decimal:2',
            'min_subtotal' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'usage_limit' => 'integer',
            'usage_limit_per_user' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected function code(): Attribute
    {
        return Attribute::make(set: fn ($value) => strtoupper(trim($value)));
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public static function findByCode(?string $code): ?self
    {
        return $code ? static::where('code', strtoupper(trim($code)))->first() : null;
    }

    /**
     * Why this coupon can't be used for the given subtotal/customer, or null when it can.
     */
    public function unusableReason(float $subtotal, ?User $user): ?string
    {
        if (!$this->is_active || ($this->starts_at && $this->starts_at->isFuture())) {
            return 'This coupon code is not valid.';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'This coupon has expired.';
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return 'This coupon has reached its usage limit.';
        }

        if ($subtotal < (float) $this->min_subtotal) {
            return sprintf('Add items worth ₹%s more to use %s.', number_format((float) $this->min_subtotal - $subtotal), $this->code);
        }

        if ($user && $this->usage_limit_per_user !== null) {
            $used = $this->orders()
                ->where('user_id', $user->id)
                ->where('status', '!=', OrderStatus::Cancelled->value)
                ->count();

            if ($used >= $this->usage_limit_per_user) {
                return 'You have already used this coupon.';
            }
        }

        return null;
    }

    public function discountFor(float $subtotal): float
    {
        $discount = match ($this->type) {
            CouponType::Percent => round($subtotal * (float) $this->value / 100),
            CouponType::Fixed => (float) $this->value,
            CouponType::FreeShipping => 0.0,
        };

        if ($this->max_discount !== null) {
            $discount = min($discount, (float) $this->max_discount);
        }

        return min($discount, $subtotal);
    }
}
