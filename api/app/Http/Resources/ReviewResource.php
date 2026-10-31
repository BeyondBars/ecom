<?php

namespace App\Http\Resources;

use App\Support\Enums\UserAbility;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Review */
class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        $isStaff = (bool) $request->user()?->can(UserAbility::ManageReviews->value);

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'status' => $this->status->value,
            'verified_purchase' => $this->verified_purchase,
            'is_featured' => $this->is_featured,
            // Customers see a first name and initial only
            'author' => $this->whenLoaded('user', fn () => $isStaff
                ? $this->user->name
                : trim($this->user->first_name.' '.mb_substr((string) $this->user->last_name, 0, 1).'.')),
            'author_email' => $this->when($isStaff, fn () => $this->user?->email),
            'product' => $this->whenLoaded('product', fn () => ['id' => $this->product->id, 'name' => $this->product->name, 'slug' => $this->product->slug]),
            'created_at' => $this->created_at,
        ];
    }
}
