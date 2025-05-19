<?php

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BlogPostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_blog_posts');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BlogPost $blogPost): bool
    {
        return $user->hasPermission('view_blog_posts');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_blog_posts');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BlogPost $blogPost): bool
    {
        // Admin can update any blog post
        if ($user->hasPermission('update_blog_posts')) {
            return true;
        }

        // Authors can update their own blog posts
        return $user->id === $blogPost->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BlogPost $blogPost): bool
    {
        // Admin can delete any blog post
        if ($user->hasPermission('delete_blog_posts')) {
            return true;
        }

        // Authors can delete their own blog posts
        return $user->id === $blogPost->user_id;
    }

    /**
     * Determine whether the user can like the blog post.
     */
    public function like(User $user, BlogPost $blogPost): bool
    {
        // All authenticated users can like blog posts
        return true;
    }
}
