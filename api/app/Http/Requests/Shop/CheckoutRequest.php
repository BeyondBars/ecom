<?php

namespace App\Http\Requests\Shop;

use App\Support\Enums\PaymentMethod;
use App\Support\Enums\ShippingMethod;
use Illuminate\Validation\Rule;

class CheckoutRequest extends CartRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'shipping_method' => ['required', Rule::enum(ShippingMethod::class)],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'address_id' => ['required_without:address', 'nullable', 'integer'],
            'address' => ['required_without:address_id', 'array'],
            ...AddressRules::rules('address.', required: !$this->filled('address_id')),
            'save_address' => ['sometimes', 'boolean'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return AddressRules::messages('address.');
    }
}
