<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewStoreRequest;
use App\Http\Requests\ReviewUpdateRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Review::class, 'review', [
            'except' => ['index', 'productReviews']
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user']);

        // Apply filters
        if ($request->has('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        if ($request->has('approved')) {
            $query->where('approved', $request->boolean('approved'));
        }

        if ($request->has('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        if ($request->has('verified_purchase')) {
            $query->where('verified_purchase', $request->boolean('verified_purchase'));
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $reviews = $query->paginate($perPage);

        return response()->json($reviews);
    }

    /**
     * Get reviews for a specific product.
     */
    public function productReviews(Request $request, Product $product)
    {
        $query = $product->reviews();

        // By default, only show approved reviews
        if (!$request->has('approved') && !$request->user()?->hasPermission('view_unapproved_reviews')) {
            $query->where('approved', true);
        } elseif ($request->has('approved')) {
            $query->where('approved', $request->boolean('approved'));
        }

        if ($request->has('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        if ($request->has('verified_purchase')) {
            $query->where('verified_purchase', $request->boolean('verified_purchase'));
        }

        if ($request->has('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Include user data
        $query->with('user:id,name,email');

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $reviews = $query->paginate($perPage);

        return response()->json($reviews);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewStoreRequest $request)
    {
        $data = $request->validated();
        
        // Set the user ID to the current user
        $data['user_id'] = $request->user()->id;
        
        // Check if the user has already reviewed this product
        $existingReview = Review::where('product_id', $data['product_id'])
            ->where('user_id', $data['user_id'])
            ->first();
            
        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this product.',
            ], 422);
        }
        
        // Check if the user has purchased the product
        // In a real app, you'd verify against orders
        // For now, we'll just set verified_purchase based on the request or default to false
        if (!isset($data['verified_purchase'])) {
            $data['verified_purchase'] = false;
        }
        
        // Set approval status
        // If the user is an admin or moderator, auto-approve
        if ($request->user()->hasPermission('approve_reviews')) {
            $data['approved'] = $data['approved'] ?? true;
        } else {
            // Regular users' reviews need approval
            $data['approved'] = false;
        }
        
        $review = Review::create($data);
        $review->load('user:id,name,email');
        
        return response()->json($review, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $review->load(['product', 'user:id,name,email']);
        
        return response()->json($review);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReviewUpdateRequest $request, Review $review)
    {
        $data = $request->validated();
        
        $review->update($data);
        $review->load(['product', 'user:id,name,email']);
        
        return response()->json($review);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();
        
        return response()->json(null, 204);
    }

    /**
     * Approve a review.
     */
    public function approve(Request $request, Review $review)
    {
        $this->authorize('approve', $review);
        
        $review->update(['approved' => true]);
        
        return response()->json($review);
    }

    /**
     * Reject a review.
     */
    public function reject(Request $request, Review $review)
    {
        $this->authorize('approve', $review);
        
        $review->update(['approved' => false]);
        
        return response()->json($review);
    }

    /**
     * Feature a review.
     */
    public function feature(Request $request, Review $review)
    {
        $this->authorize('feature', $review);
        
        $review->update(['featured' => true]);
        
        return response()->json($review);
    }

    /**
     * Unfeature a review.
     */
    public function unfeature(Request $request, Review $review)
    {
        $this->authorize('feature', $review);
        
        $review->update(['featured' => false]);
        
        return response()->json($review);
    }

    /**
     * Get product rating statistics.
     */
    public function productRatingStats(Request $request, Product $product)
    {
        $approvedReviews = $product->reviews()->approved();
        
        $stats = [
            'average_rating' => round($approvedReviews->avg('rating') ?: 0, 1),
            'total_reviews' => $approvedReviews->count(),
            'rating_distribution' => [
                5 => $approvedReviews->where('rating', 5)->count(),
                4 => $approvedReviews->where('rating', 4)->count(),
                3 => $approvedReviews->where('rating', 3)->count(),
                2 => $approvedReviews->where('rating', 2)->count(),
                1 => $approvedReviews->where('rating', 1)->count(),
            ],
        ];
        
        return response()->json($stats);
    }
}
