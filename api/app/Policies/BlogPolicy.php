<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\User;
use App\Support\Enums\UserRole;

class BlogPolicy
{
    /**
     * Roles allowed to manage blog posts.
     */
    protected const EDITORS = [UserRole::SuperAdmin, UserRole::Blogger];

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Blog $blog): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function update(User $user, Blog $blog): bool
    {
        return $this->isEditor($user);
    }

    public function delete(User $user, Blog $blog): bool
    {
        return $this->isEditor($user);
    }

    protected function isEditor(User $user): bool
    {
        return in_array($user->role?->enum(), self::EDITORS, true);
    }
}
