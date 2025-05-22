<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponStoreRequest extends FormRequest
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
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
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
