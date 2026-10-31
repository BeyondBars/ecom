<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Support\Enums\ModerationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ReviewController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Review::class);

        return ReviewResource::collection(
            QueryBuilder::for(Review::query()->with('user', 'product'))
                ->allowedFilters(
                    AllowedFilter::exact('status'),
                    AllowedFilter::exact('product_id'),
                    AllowedFilter::exact('rating'),
                    AllowedFilter::exact('verified_purchase'),
                    AllowedFilter::partial('search', 'comment'),
                )
                ->allowedSorts('created_at', 'rating')
                ->defaultSort('-created_at')
                ->paginate(min((int) request('per_page', 15), 100))
        );
    }

    public function update(Request $request, Review $review): ReviewResource
    {
        $this->authorize('update', $review);

        $review->update($request->validate([
            'status' => ['sometimes', Rule::enum(ModerationStatus::class)],
            'is_featured' => ['sometimes', 'boolean'],
        ]));

        return new ReviewResource($review->load('user', 'product'));
    }

    public function destroy(Review $review): Response
    {
        $this->authorize('delete', $review);
        $review->delete();

        return response()->noContent();
    }
}
