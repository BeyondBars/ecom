<?php

namespace App\Policies;

use App\Models\Like;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LikePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_likes');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Like $like): bool
    {
        // Admin can view any like
        if ($user->hasPermission('view_likes')) {
            return true;
        }

        // Users can view their own likes
        return $user->id === $like->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create likes
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Like $like): bool
    {
        // Admin can delete any like
        if ($user->hasPermission('delete_likes')) {
            return true;
        }

        // Users can delete their own likes
        return $user->id === $like->user_id;
    }
}
