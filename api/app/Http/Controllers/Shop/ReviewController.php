<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Support\Enums\ModerationStatus;
use App\Support\Enums\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Approved reviews for a product, plus the star breakdown.
     */
    public function index(string $slug): JsonResponse
    {
        $product = Product::visible()->where('slug', $slug)->firstOrFail();
        $approved = $product->reviews()->approved();

        $reviews = $approved->clone()
            ->with('user')
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(10);

        $breakdown = $approved->clone()
            ->selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        return ReviewResource::collection($reviews)
            ->additional(['summary' => [
                'average' => (float) $product->rating,
                'count' => $product->review_count,
                'breakdown' => collect([5, 4, 3, 2, 1])->mapWithKeys(fn ($stars) => [$stars => (int) ($breakdown[$stars] ?? 0)]),
            ]])
            ->response();
    }

    /**
     * One review per customer per product. Reviews appear once a moderator approves them.
     */
    public function store(Request $request, string $slug): JsonResponse
    {
        $product = Product::visible()->where('slug', $slug)->firstOrFail();
        $user = $request->user();

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:120'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        if ($product->reviews()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You have already reviewed this product.', 'errors' => ['comment' => ['You have already reviewed this product.']]], 422);
        }

        $verified = $user->orders()
            ->where('status', OrderStatus::Delivered->value)
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();

        $review = $product->reviews()->make([...$data, 'status' => ModerationStatus::Pending, 'verified_purchase' => $verified]);
        $review->user()->associate($user)->save();

        return (new ReviewResource($review->load('user')))->response()->setStatusCode(201);
    }
}
