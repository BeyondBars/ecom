<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Enums\UserAbility;

/**
 * Admin-only resources: every action needs the one ability that manages them.
 * Public (storefront) reads don't go through these policies.
 */
abstract class ManagedByAbility
{
    abstract protected function ability(): UserAbility;

    protected function allowed(User $user): bool
    {
        return $user->can($this->ability()->value);
    }

    public function viewAny(User $user): bool
    {
        return $this->allowed($user);
    }

    public function view(User $user, $model = null): bool
    {
        return $this->allowed($user);
    }

    public function create(User $user): bool
    {
        return $this->allowed($user);
    }

    public function update(User $user, $model = null): bool
    {
        return $this->allowed($user);
    }

    public function delete(User $user, $model = null): bool
    {
        return $this->allowed($user);
    }
}
