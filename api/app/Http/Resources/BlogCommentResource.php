<?php

namespace App\Http\Resources;

use App\Support\Enums\UserAbility;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BlogComment */
class BlogCommentResource extends JsonResource
{
    public function toArray($request): array
    {
        $isStaff = (bool) $request->user()?->can(UserAbility::ManageContent->value);

        return [
            'id' => $this->id,
            'content' => $this->content,
            'status' => $this->status->value,
            'author' => $this->whenLoaded('user', fn () => $isStaff ? $this->user->name : $this->user->first_name),
            'author_email' => $this->when($isStaff, fn () => $this->user?->email),
            'post' => $this->whenLoaded('post', fn () => ['id' => $this->post->id, 'title' => $this->post->title, 'slug' => $this->post->slug]),
            'created_at' => $this->created_at,
        ];
    }
}
