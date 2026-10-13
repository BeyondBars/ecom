<?php

namespace App\Models;

use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentMethod;
use App\Support\Enums\PaymentStatus;
use App\Support\Enums\ReturnStatus;
use App\Support\Enums\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'user_id',
        'email',
        'status',
        'payment_method',
        'payment_status',
        'shipping_method',
        'shipping_address',
        'coupon_id',
        'coupon_code',
        'subtotal',
        'discount',
        'shipping',
        'total',
        'currency',
        'razorpay_order_id',
        'payment_reference',
        'paid_at',
        'courier',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'cancel_reason',
        'return_status',
        'return_reason',
        'return_requested_at',
        'customer_note',
        'admin_note',
    ];

    protected $attributes = [
        'status' => 'pending',
        'payment_status' => 'pending',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethod::class,
            'payment_status' => PaymentStatus::class,
            'shipping_method' => ShippingMethod::class,
            'return_status' => ReturnStatus::class,
            'shipping_address' => 'array',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'return_requested_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->orderBy('created_at')->orderBy('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function scopeForCustomer($query, User $user): void
    {
        $query->where('orders.user_id', $user->id);
    }

    /**
     * Orders that count as sales: COD orders and paid online orders that weren't cancelled.
     */
    public function scopePlaced($query): void
    {
        $query->where('status', '!=', OrderStatus::Cancelled->value)
            ->where(fn ($q) => $q
                ->where('payment_method', PaymentMethod::CashOnDelivery->value)
                ->orWhereIn('payment_status', [PaymentStatus::Paid->value, PaymentStatus::RefundPending->value]));
    }

    public function isAwaitingOnlinePayment(): bool
    {
        return $this->payment_method === PaymentMethod::Razorpay
            && $this->payment_status === PaymentStatus::Pending
            && $this->status === OrderStatus::Pending;
    }

    public function canBeCancelledByCustomer(): bool
    {
        return $this->status->isCancellableByCustomer();
    }

    public function canRequestReturn(): bool
    {
        if ($this->status !== OrderStatus::Delivered || $this->return_status !== null) {
            return false;
        }

        $deliveredAt = $this->delivered_at ?? $this->updated_at;

        return $deliveredAt->greaterThanOrEqualTo(now()->subDays((int) Setting::value('return_window_days')));
    }

    public function record(string $type, ?string $note = null, ?User $by = null, bool $internal = false): OrderEvent
    {
        return $this->events()->create([
            'type' => $type,
            'note' => $note,
            'user_id' => $by?->id,
            'is_internal' => $internal,
            'created_at' => now(),
        ]);
    }

    /**
     * Put the ordered quantities back on the shelf (cancellations and completed returns).
     */
    public function restock(): void
    {
        foreach ($this->items as $item) {
            // Through the model so "back in stock" alerts fire
            Product::find($item->product_id)?->increment('stock', $item->quantity);
        }
    }

    public function storefrontUrl(): string
    {
        return rtrim(config('shop.storefront_url'), '/').'/account/orders/'.$this->number;
    }
}
