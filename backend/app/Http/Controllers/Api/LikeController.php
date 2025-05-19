<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Product;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Like::class);

        $query = Like::with(['user', 'likeable']);

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by likeable type
        if ($request->has('likeable_type')) {
            $query->where('likeable_type', $request->input('likeable_type'));
        }

        // Filter by likeable id
        if ($request->has('likeable_id')) {
            $query->where('likeable_id', $request->input('likeable_id'));
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $likes = $query->paginate($perPage);

        return response()->json($likes);
    }

    /**
     * Toggle like for a product.
     */
    public function toggleProductLike(Request $request, Product $product)
    {
        $this->authorize('like', $product);

        $user = $request->user();
        $like = Like::where('user_id', $user->id)
            ->where('likeable_id', $product->id)
            ->where('likeable_type', get_class($product))
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $product->id,
                'likeable_type' => get_class($product),
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $product->likes()->count(),
        ]);
    }

    /**
     * Toggle like for a blog post.
     */
    public function toggleBlogPostLike(Request $request, BlogPost $blogPost)
    {
        $this->authorize('like', $blogPost);

        $user = $request->user();
        $like = Like::where('user_id', $user->id)
            ->where('likeable_id', $blogPost->id)
            ->where('likeable_type', get_class($blogPost))
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $blogPost->id,
                'likeable_type' => get_class($blogPost),
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $blogPost->likes()->count(),
        ]);
    }

    /**
     * Check if user has liked a product.
     */
    public function checkProductLike(Request $request, Product $product)
    {
        $user = $request->user();
        $liked = Like::where('user_id', $user->id)
            ->where('likeable_id', $product->id)
            ->where('likeable_type', get_class($product))
            ->exists();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $product->likes()->count(),
        ]);
    }

    /**
     * Check if user has liked a blog post.
     */
    public function checkBlogPostLike(Request $request, BlogPost $blogPost)
    {
        $user = $request->user();
        $liked = Like::where('user_id', $user->id)
            ->where('likeable_id', $blogPost->id)
            ->where('likeable_type', get_class($blogPost))
            ->exists();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $blogPost->likes()->count(),
        ]);
    }
}
