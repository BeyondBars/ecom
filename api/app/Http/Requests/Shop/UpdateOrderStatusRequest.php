<?php

namespace App\Http\Requests\Shop;

use App\Support\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('order'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(OrderStatus::class)->except([OrderStatus::Pending, OrderStatus::Returned])],
            'note' => ['nullable', 'string', 'max:500'],
            'courier' => ['nullable', 'string', 'max:100'],
            'tracking_number' => [
                Rule::requiredIf($this->input('status') === OrderStatus::Shipped->value),
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }
}
