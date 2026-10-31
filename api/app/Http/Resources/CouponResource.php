<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Coupon */
class CouponResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'type' => $this->type->value,
            'value' => (float) $this->value,
            'min_subtotal' => (float) $this->min_subtotal,
            'max_discount' => $this->max_discount !== null ? (float) $this->max_discount : null,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'usage_limit' => $this->usage_limit,
            'usage_limit_per_user' => $this->usage_limit_per_user,
            'used_count' => $this->used_count,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
