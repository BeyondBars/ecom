<?php

namespace App\Policies;

use App\Models\Like;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LikePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-likes');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Like $like): bool
    {
        return $user->hasPermission('view-likes');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-likes');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Like $like): bool
    {
        return $user->hasPermission('delete-likes') && 
               ($user->id === $like->user_id || $user->hasRole('Admin'));
    }
}
