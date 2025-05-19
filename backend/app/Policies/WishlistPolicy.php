<?php

namespace App\Policies;

use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WishlistPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-wishlists');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Wishlist $wishlist): bool
    {
        return $user->hasPermission('view-wishlists') && 
               ($user->id === $wishlist->user_id || $wishlist->is_public || $user->hasRole('Admin'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-wishlists');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Wishlist $wishlist): bool
    {
        return $user->hasPermission('update-wishlists') && 
               ($user->id === $wishlist->user_id || $user->hasRole('Admin'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wishlist $wishlist): bool
    {
        return $user->hasPermission('delete-wishlists') && 
               ($user->id === $wishlist->user_id || $user->hasRole('Admin'));
    }
}
