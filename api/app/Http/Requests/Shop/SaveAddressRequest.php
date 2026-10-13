<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class SaveAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        $address = $this->route('address');

        return !$address || $address->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            ...AddressRules::rules(required: !$this->route('address')),
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return AddressRules::messages();
    }
}
