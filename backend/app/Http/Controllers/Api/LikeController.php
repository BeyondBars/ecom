<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Like::class);

        $query = Like::with(['user', 'likeable']);

        // Filter by user_id if provided
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by likeable_type if provided
        if ($request->has('likeable_type')) {
            $query->where('likeable_type', $request->likeable_type);
        }

        $likes = $query->paginate($request->per_page ?? 15);

        return response()->json($likes);
    }

    /**
     * Toggle like for a product.
     */
    public function toggleProductLike(Request $request, Product $product): JsonResponse
    {
        $this->authorize('like', $product);

        $userId = $request->user()->id;

        $like = Like::where('user_id', $userId)
            ->where('likeable_id', $product->id)
            ->where('likeable_type', Product::class)
            ->first();

        if ($like) {
            $like->delete();
            return response()->json(['liked' => false]);
        } else {
            Like::create([
                'user_id' => $userId,
                'likeable_id' => $product->id,
                'likeable_type' => Product::class,
            ]);
            return response()->json(['liked' => true]);
        }
    }

    /**
     * Toggle like for a blog post.
     */
    public function toggleBlogPostLike(Request $request, BlogPost $blogPost): JsonResponse
    {
        $this->authorize('like', $blogPost);

        $userId = $request->user()->id;

        $like = Like::where('user_id', $userId)
            ->where('likeable_id', $blogPost->id)
            ->where('likeable_type', BlogPost::class)
            ->first();

        if ($like) {
            $like->delete();
            return response()->json(['liked' => false]);
        } else {
            Like::create([
                'user_id' => $userId,
                'likeable_id' => $blogPost->id,
                'likeable_type' => BlogPost::class,
            ]);
            return response()->json(['liked' => true]);
        }
    }

    /**
     * Get likes count for a product.
     */
    public function getProductLikesCount(Product $product): JsonResponse
    {
        $count = $product->likes()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Get likes count for a blog post.
     */
    public function getBlogPostLikesCount(BlogPost $blogPost): JsonResponse
    {
        $count = $blogPost->likes()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Check if a user has liked a product.
     */
    public function checkProductLike(Request $request, Product $product): JsonResponse
    {
        $userId = $request->user()->id;

        $liked = Like::where('user_id', $userId)
            ->where('likeable_id', $product->id)
            ->where('likeable_type', Product::class)
            ->exists();

        return response()->json(['liked' => $liked]);
    }

    /**
     * Check if a user has liked a blog post.
     */
    public function checkBlogPostLike(Request $request, BlogPost $blogPost): JsonResponse
    {
        $userId = $request->user()->id;

        $liked = Like::where('user_id', $userId)
            ->where('likeable_id', $blogPost->id)
            ->where('likeable_type', BlogPost::class)
            ->exists();

        return response()->json(['liked' => $liked]);
    }
}
