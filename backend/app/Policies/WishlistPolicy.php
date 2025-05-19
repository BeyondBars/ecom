<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Auth\Access\Response;

class WishlistPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_wishlists');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Wishlist $wishlist): bool
    {
        // Admin can view any wishlist
        if ($user->hasPermission('view_wishlists')) {
            return true;
        }

        // Users can view their own wishlists
        if ($user->id === $wishlist->user_id) {
            return true;
        }

        // Users can view public wishlists
        return $wishlist->is_public;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_wishlists');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Wishlist $wishlist): bool
    {
        // Admin can update any wishlist
        if ($user->hasPermission('update_wishlists')) {
            return true;
        }

        // Users can update their own wishlists
        return $user->id === $wishlist->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wishlist $wishlist): bool
    {
        // Admin can delete any wishlist
        if ($user->hasPermission('delete_wishlists')) {
            return true;
        }

        // Users can delete their own wishlists
        return $user->id === $wishlist->user_id;
    }
}
