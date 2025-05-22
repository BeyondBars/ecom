<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('coupons')->ignore($this->coupon),
            ],
            'discount_type' => 'sometimes|required|in:percentage,fixed',
            'discount_value' => 'sometimes|required|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'start_date' => 'sometimes|required|date',
            'expiry_date' => 'sometimes|required|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'status' => 'sometimes|required|in:active,inactive',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'code' => 'coupon code',
            'discount_type' => 'discount type',
            'discount_value' => 'discount value',
            'minimum_order_amount' => 'minimum order amount',
            'start_date' => 'start date',
            'expiry_date' => 'expiry date',
            'usage_limit' => 'usage limit',
            'status' => 'status',
        ];
    }
}
