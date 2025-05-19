<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'author_id',
        'author_name',
        'author_email',
        'status',
        'commentable_id',
        'commentable_type',
    ];

    /**
     * Get the author that owns the comment.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the parent commentable model (product or blog post).
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending comments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include rejected comments.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
