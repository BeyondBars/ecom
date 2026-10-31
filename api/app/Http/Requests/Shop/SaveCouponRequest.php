<?php

namespace App\Http\Requests\Shop;

use App\Models\Coupon;
use App\Support\Enums\CouponType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        $coupon = $this->route('coupon');

        return $coupon ? $this->user()->can('update', $coupon) : $this->user()->can('create', Coupon::class);
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->input('code')))]);
        }
    }

    public function rules(): array
    {
        $coupon = $this->route('coupon');
        $required = $coupon ? 'sometimes' : 'required';
        $type = $this->input('type', $coupon?->type?->value);

        return [
            'code' => [$required, 'string', 'max:30', 'alpha_dash', Rule::unique('coupons', 'code')->ignore($coupon)],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => [$required, Rule::enum(CouponType::class)],
            'value' => [
                $type === CouponType::FreeShipping->value ? 'nullable' : $required,
                'numeric',
                'min:0',
                ...($type === CouponType::Percent->value ? ['max:100'] : []),
            ],
            'min_subtotal' => ['sometimes', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', ...($this->filled('starts_at') ? ['after:starts_at'] : [])],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_user' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
