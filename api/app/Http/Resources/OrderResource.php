<?php

namespace App\Http\Resources;

use App\Support\Enums\UserAbility;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        $isStaff = (bool) $request->user()?->can(UserAbility::ManageOrders->value);

        return [
            'id' => $this->id,
            'number' => $this->number,
            'email' => $this->email,
            'status' => $this->status->value,
            'payment_method' => $this->payment_method->value,
            'payment_status' => $this->payment_status->value,
            'payment_reference' => $this->payment_reference,
            'paid_at' => $this->paid_at,
            'shipping_method' => $this->shipping_method->value,
            'shipping_address' => $this->shipping_address,
            'coupon_code' => $this->coupon_code,
            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'shipping' => (float) $this->shipping,
            'total' => (float) $this->total,
            'currency' => $this->currency,
            'courier' => $this->courier,
            'tracking_number' => $this->tracking_number,
            'shipped_at' => $this->shipped_at,
            'delivered_at' => $this->delivered_at,
            'cancelled_at' => $this->cancelled_at,
            'cancel_reason' => $this->cancel_reason,
            'return_status' => $this->return_status?->value,
            'return_reason' => $this->return_reason,
            'return_requested_at' => $this->return_requested_at,
            'customer_note' => $this->customer_note,
            'can_cancel' => $this->canBeCancelledByCustomer(),
            'can_return' => $this->canRequestReturn(),
            'items_count' => $this->whenCounted('items'),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->name,
                // Link back to the product while it still exists
                'slug' => $item->relationLoaded('product') ? $item->product?->slug : null,
                'sku' => $item->sku,
                'image_url' => $item->image_url,
                'options' => $item->options ?? [],
                'price' => (float) $item->price,
                'quantity' => $item->quantity,
                'total' => (float) $item->total,
            ])),
            'events' => $this->whenLoaded('events', fn () => $this->events
                ->when(!$isStaff, fn ($events) => $events->where('is_internal', false))
                ->values()
                ->map(fn ($event) => [
                    'type' => $event->type,
                    'note' => $event->note,
                    'is_internal' => $event->is_internal,
                    'by' => $isStaff ? $event->user?->name : null,
                    'created_at' => $event->created_at,
                ])),
            'customer' => $this->when($isStaff, fn () => $this->whenLoaded('user', fn () => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'mobile' => $this->user->mobile,
            ] : null)),
            'admin_note' => $this->when($isStaff, $this->admin_note),
            'next_statuses' => $this->when($isStaff, fn () => array_map(fn ($s) => $s->value, $this->status->nextStatuses())),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
