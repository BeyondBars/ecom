<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update-orders');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'in:pending,processing,completed,cancelled,refunded'],
            'payment_status' => ['sometimes', 'in:pending,paid,failed,refunded'],
            'payment_method' => ['sometimes', 'string', 'max:255'],
            'shipping_method' => ['sometimes', 'string', 'max:255'],
            'shipping_address' => ['sometimes', 'array'],
            'shipping_address.name' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.address' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.city' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.state' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.postal_code' => ['required_with:shipping_address', 'string', 'max:20'],
            'shipping_address.country' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.phone' => ['nullable', 'string', 'max:20'],
            'billing_address' => ['sometimes', 'array'],
            'billing_address.name' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.address' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.city' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.state' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.postal_code' => ['required_with:billing_address', 'string', 'max:20'],
            'billing_address.country' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.phone' => ['nullable', 'string', 'max:20'],
            'shipping' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
