<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view reviews
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Review $review): bool
    {
        return true; // Anyone can view a review
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create a review
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        // Admin can update any review
        if ($user->hasPermission('update_reviews')) {
            return true;
        }
        
        // Users can only update their own reviews
        return $user->id === $review->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        // Admin can delete any review
        if ($user->hasPermission('delete_reviews')) {
            return true;
        }
        
        // Users can only delete their own reviews
        return $user->id === $review->user_id;
    }

    /**
     * Determine whether the user can approve reviews.
     */
    public function approve(User $user, Review $review): bool
    {
        return $user->hasPermission('approve_reviews');
    }

    /**
     * Determine whether the user can feature reviews.
     */
    public function feature(User $user, Review $review): bool
    {
        return $user->hasPermission('feature_reviews');
    }
}
