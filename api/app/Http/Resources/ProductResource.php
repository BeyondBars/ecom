<?php

namespace App\Http\Resources;

use App\Support\Enums\UserAbility;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Product */
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $canManage = (bool) $request->user()?->can(UserAbility::ManageCatalog->value);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price !== null ? (float) $this->sale_price : null,
            'current_price' => $this->currentPrice(),
            'stock' => $this->stock,
            'in_stock' => $this->isInStock(),
            'is_featured' => $this->is_featured,
            'options' => $this->options ?? [],
            'specifications' => $this->specifications ?? [],
            'rating' => (float) $this->rating,
            'review_count' => $this->review_count,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($image) => [
                'id' => $image->id,
                'url' => $image->url,
                'alt' => $image->alt,
            ])),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Admin-only fields
            'status' => $this->when($canManage, fn () => $this->status->value),
            'cost' => $this->when($canManage, fn () => $this->cost !== null ? (float) $this->cost : null),
        ];
    }
}
