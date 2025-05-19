<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-comments');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Comment $comment): bool
    {
        return $user->hasPermission('view-comments');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-comments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Authors can update their own comments
        if ($user->id === $comment->author_id) {
            return true;
        }
        
        return $user->hasPermission('update-comments');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Authors can delete their own comments
        if ($user->id === $comment->author_id) {
            return true;
        }
        
        // Content owners can delete comments on their content
        if ($comment->commentable_type === 'App\\Models\\BlogPost') {
            $post = $comment->commentable;
            if ($user->id === $post->author_id) {
                return true;
            }
        }
        
        return $user->hasPermission('delete-comments');
    }
}
