<?php

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlogPostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-blog-posts');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BlogPost $blogPost): bool
    {
        return $user->hasPermission('view-blog-posts');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-blog-posts');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BlogPost $blogPost): bool
    {
        return $user->hasPermission('update-blog-posts') && 
               ($user->id === $blogPost->author_id || $user->hasRole('Admin'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BlogPost $blogPost): bool
    {
        return $user->hasPermission('delete-blog-posts') && 
               ($user->id === $blogPost->author_id || $user->hasRole('Admin'));
    }

    /**
     * Determine whether the user can like the model.
     */
    public function like(User $user, BlogPost $blogPost): bool
    {
        return $user->hasPermission('like-blog-posts');
    }
}
