<?php

namespace App\Services\Shop;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Support\Enums\CouponType;
use App\Support\Enums\ShippingMethod;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Works out the real price of a cart from the database: product prices, stock, options, coupon
 * and shipping. Prices sent by the browser are never trusted.
 */
class CartPricer
{
    /**
     * @param  array  $items  [['product_id' => 1, 'quantity' => 2, 'options' => ['Size' => 'M']], ...]
     * @param  bool  $strictCoupon  throw when the coupon can't be used (checkout) instead of reporting it (quote)
     * @param  Collection|null  $lockedProducts  products already loaded with a row lock (checkout)
     *
     * @throws ValidationException
     */
    public function price(
        array $items,
        ShippingMethod $shippingMethod,
        ?string $couponCode = null,
        ?User $user = null,
        bool $strictCoupon = false,
        ?Collection $lockedProducts = null,
    ): CartQuote {
        $products = $lockedProducts ?? Product::query()
            ->with('images', 'category')
            ->whereIn('id', collect($items)->pluck('product_id'))
            ->get()
            ->keyBy('id');

        $lines = [];
        $errors = [];
        $quantities = [];

        foreach (array_values($items) as $index => $item) {
            $product = $products->get($item['product_id']);

            if (!$product || !$product->isVisible()) {
                $errors["items.{$index}.product_id"] = 'This product is no longer available.';
                continue;
            }

            try {
                $options = $product->normaliseSelectedOptions($item['options'] ?? []);
            } catch (\InvalidArgumentException $e) {
                $errors["items.{$index}.options"] = $e->getMessage();
                continue;
            }

            $quantity = (int) $item['quantity'];
            $quantities[$product->id] = ($quantities[$product->id] ?? 0) + $quantity;

            // Several lines (e.g. two sizes) share the product's stock
            if ($quantities[$product->id] > $product->stock) {
                $errors["items.{$index}.quantity"] = $product->stock > 0
                    ? "Only {$product->stock} of {$product->name} left in stock."
                    : "{$product->name} is out of stock.";
                continue;
            }

            $price = $product->currentPrice();

            $lines[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'image_url' => $product->images->first()?->url,
                'options' => $options,
                'price' => $price,
                'quantity' => $quantity,
                'total' => round($price * $quantity, 2),
            ];
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $subtotal = round(array_sum(array_column($lines, 'total')), 2);

        [$coupon, $couponError] = $this->resolveCoupon($couponCode, $subtotal, $user);

        if ($couponError && $strictCoupon) {
            throw ValidationException::withMessages(['coupon_code' => $couponError]);
        }

        $discount = $coupon?->discountFor($subtotal) ?? 0.0;
        $shipping = $this->shippingCost($subtotal, $shippingMethod, $coupon);

        return new CartQuote(
            lines: $lines,
            subtotal: $subtotal,
            discount: $discount,
            shipping: $shipping,
            total: round(max($subtotal - $discount, 0) + $shipping, 2),
            shippingMethod: $shippingMethod,
            coupon: $coupon,
            couponError: $couponError,
        );
    }

    public function shippingCost(float $subtotal, ShippingMethod $method, ?Coupon $coupon = null): float
    {
        if ($subtotal <= 0 || $coupon?->type === CouponType::FreeShipping) {
            return 0.0;
        }

        // Free shipping covers standard delivery only; express is always charged.
        if ($method === ShippingMethod::Standard && $subtotal >= (float) Setting::value('free_shipping_threshold')) {
            return 0.0;
        }

        return (float) Setting::value($method === ShippingMethod::Express ? 'express_shipping_fee' : 'standard_shipping_fee');
    }

    /**
     * @return array{0: ?Coupon, 1: ?string}
     */
    protected function resolveCoupon(?string $code, float $subtotal, ?User $user): array
    {
        if (blank($code)) {
            return [null, null];
        }

        $coupon = Coupon::findByCode($code);

        if (!$coupon) {
            return [null, 'This coupon code is not valid.'];
        }

        $reason = $coupon->unusableReason($subtotal, $user);

        return $reason ? [null, $reason] : [$coupon, null];
    }
}
