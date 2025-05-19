<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Like;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Display a listing of the user's likes.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $likes = $user->likes()->with('likeable')->paginate(10);
        
        return response()->json($likes);
    }

    /**
     * Toggle like for a product.
     */
    public function toggleProductLike(Request $request, Product $product)
    {
        $user = $request->user();
        
        $like = Like::where('user_id', $user->id)
            ->where('likeable_type', Product::class)
            ->where('likeable_id', $product->id)
            ->first();
            
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'likeable_type' => Product::class,
                'likeable_id' => $product->id,
            ]);
            $liked = true;
        }
        
        $count = Like::where('likeable_type', Product::class)
            ->where('likeable_id', $product->id)
            ->count();
            
        return response()->json([
            'liked' => $liked,
            'count' => $count,
        ]);
    }

    /**
     * Get product likes count.
     */
    public function getProductLikesCount(Product $product)
    {
        $count = Like::where('likeable_type', Product::class)
            ->where('likeable_id', $product->id)
            ->count();
            
        return response()->json(['count' => $count]);
    }

    /**
     * Check if user liked a product.
     */
    public function checkProductLike(Request $request, Product $product)
    {
        $user = $request->user();
        
        $liked = Like::where('user_id', $user->id)
            ->where('likeable_type', Product::class)
            ->where('likeable_id', $product->id)
            ->exists();
            
        return response()->json(['liked' => $liked]);
    }

    /**
     * Toggle like for a blog post.
     */
    public function toggleBlogPostLike(Request $request, BlogPost $blogPost)
    {
        $user = $request->user();
        
        $like = Like::where('user_id', $user->id)
            ->where('likeable_type', BlogPost::class)
            ->where('likeable_id', $blogPost->id)
            ->first();
            
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'likeable_type' => BlogPost::class,
                'likeable_id' => $blogPost->id,
            ]);
            $liked = true;
        }
        
        $count = Like::where('likeable_type', BlogPost::class)
            ->where('likeable_id', $blogPost->id)
            ->count();
            
        return response()->json([
            'liked' => $liked,
            'count' => $count,
        ]);
    }

    /**
     * Get blog post likes count.
     */
    public function getBlogPostLikesCount(BlogPost $blogPost)
    {
        $count = Like::where('likeable_type', BlogPost::class)
            ->where('likeable_id', $blogPost->id)
            ->count();
            
        return response()->json(['count' => $count]);
    }

    /**
     * Check if user liked a blog post.
     */
    public function checkBlogPostLike(Request $request, BlogPost $blogPost)
    {
        $user = $request->user();
        
        $liked = Like::where('user_id', $user->id)
            ->where('likeable_type', BlogPost::class)
            ->where('likeable_id', $blogPost->id)
            ->exists();
            
        return response()->json(['liked' => $liked]);
    }

    /**
     * Toggle like for a review.
     */
    public function toggleReviewLike(Request $request, Review $review)
    {
        $user = $request->user();
        
        $like = Like::where('user_id', $user->id)
            ->where('likeable_type', Review::class)
            ->where('likeable_id', $review->id)
            ->first();
            
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'likeable_type' => Review::class,
                'likeable_id' => $review->id,
            ]);
            $liked = true;
        }
        
        $count = Like::where('likeable_type', Review::class)
            ->where('likeable_id', $review->id)
            ->count();
            
        return response()->json([
            'liked' => $liked,
            'count' => $count,
        ]);
    }

    /**
     * Get review likes count.
     */
    public function getReviewLikesCount(Review $review)
    {
        $count = Like::where('likeable_type', Review::class)
            ->where('likeable_id', $review->id)
            ->count();
            
        return response()->json(['count' => $count]);
    }

    /**
     * Check if user liked a review.
     */
    public function checkReviewLike(Request $request, Review $review)
    {
        $user = $request->user();
        
        $liked = Like::where('user_id', $user->id)
            ->where('likeable_type', Review::class)
            ->where('likeable_id', $review->id)
            ->exists();
            
        return response()->json(['liked' => $liked]);
    }
}
