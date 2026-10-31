<?php

namespace App\Services\Shop;

use App\Models\Coupon;
use App\Support\Enums\ShippingMethod;
use Illuminate\Contracts\Support\Arrayable;

class CartQuote implements Arrayable
{
    public function __construct(
        public readonly array $lines,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $shipping,
        public readonly float $total,
        public readonly ShippingMethod $shippingMethod,
        public readonly ?Coupon $coupon,
        public readonly ?string $couponError,
    ) {
    }

    public function toArray(): array
    {
        return [
            'items' => $this->lines,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'shipping' => $this->shipping,
            'total' => $this->total,
            'shipping_method' => $this->shippingMethod->value,
            'coupon' => $this->coupon ? [
                'code' => $this->coupon->code,
                'description' => $this->coupon->description,
                'type' => $this->coupon->type->value,
            ] : null,
            'coupon_error' => $this->couponError,
        ];
    }
}
