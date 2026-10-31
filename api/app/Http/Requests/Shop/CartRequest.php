<?php

namespace App\Http\Requests\Shop;

use App\Support\Enums\ShippingMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Cart contents sent by the storefront for a price quote.
 */
class CartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.options' => ['sometimes', 'array'],
            'items.*.options.*' => ['string', 'max:50'],
            'shipping_method' => ['sometimes', Rule::enum(ShippingMethod::class)],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ];
    }
}
