<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ContactMessage */
class ContactMessageResource extends JsonResource
{
    public function toArray($request): array
    {
        return $this->only(['id', 'name', 'email', 'phone', 'subject', 'message', 'status', 'created_at']);
    }
}
