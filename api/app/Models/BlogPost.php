<?php

namespace App\Models;

use App\Support\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    use HasFactory;
    use HasUniqueSlug;

    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'cover_image_url', 'status', 'published_at'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::saving(function (BlogPost $post) {
            if ($post->status === 'published' && !$post->published_at) {
                $post->published_at = now();
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function scopePublished($query): void
    {
        $query->where('status', 'published')->where('published_at', '<=', now());
    }
}
