<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WishlistStoreRequest;
use App\Http\Requests\WishlistUpdateRequest;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Wishlist::class, 'wishlist');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Wishlist::class);

        $query = Wishlist::with(['user', 'items.product']);

        // Filter by user_id if provided
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $wishlists = $query->paginate($request->input('per_page', 15));

        return response()->json($wishlists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WishlistStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Wishlist::class);

        $wishlist = Wishlist::create($request->validated());

        // Add products to wishlist if provided
        if ($request->has('product_ids')) {
            foreach ($request->product_ids as $productId) {
                WishlistItem::create([
                    'wishlist_id' => $wishlist->id,
                    'product_id' => $productId,
                ]);
            }
        }

        return response()->json($wishlist, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Wishlist $wishlist): JsonResponse
    {
        $this->authorize('view', $wishlist);

        $wishlist->load(['user', 'items.product']);

        return response()->json($wishlist);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WishlistUpdateRequest $request, Wishlist $wishlist): JsonResponse
    {
        $this->authorize('update', $wishlist);

        $wishlist->update($request->validated());

        return response()->json($wishlist);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wishlist $wishlist): JsonResponse
    {
        $this->authorize('delete', $wishlist);

        $wishlist->delete();

        return response()->json(null, 204);
    }

    /**
     * Add a product to the wishlist.
     */
    public function addProduct(Request $request, Wishlist $wishlist): JsonResponse
    {
        $this->authorize('update', $wishlist);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'notes' => 'nullable|string',
            'priority' => 'nullable|integer|min:0|max:10',
        ]);

        // Check if product already exists in wishlist
        $existingItem = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $request->input('product_id'))
            ->first();

        if ($existingItem) {
            return response()->json(['message' => 'Product already in wishlist'], 422);
        }

        $wishlistItem = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $request->input('product_id'),
            'notes' => $request->input('notes'),
            'priority' => $request->input('priority', 0),
        ]);

        return response()->json($wishlistItem, 201);
    }

    /**
     * Remove a product from the wishlist.
     */
    public function removeProduct(Wishlist $wishlist, $productId): JsonResponse
    {
        $this->authorize('update', $wishlist);

        $deleted = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $productId)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Product not found in wishlist'], 404);
        }

        return response()->json(null, 204);
    }

    /**
     * Update a product in the wishlist.
     */
    public function updateProduct(Request $request, Wishlist $wishlist, $productId): JsonResponse
    {
        $this->authorize('update', $wishlist);

        $request->validate([
            'notes' => 'nullable|string',
            'priority' => 'nullable|integer|min:0|max:10',
        ]);

        $wishlistItem = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $productId)
            ->first();

        if (!$wishlistItem) {
            return response()->json(['message' => 'Product not found in wishlist'], 404);
        }

        $wishlistItem->update([
            'notes' => $request->input('notes', $wishlistItem->notes),
            'priority' => $request->input('priority', $wishlistItem->priority),
        ]);

        return response()->json($wishlistItem);
    }

    /**
     * Get public wishlists.
     */
    public function publicIndex(Request $request): JsonResponse
    {
        $query = Wishlist::with(['items.product', 'user'])
            ->where('is_public', true);

        // Apply filters
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $wishlists = $query->paginate($perPage);

        return response()->json($wishlists);
    }
}
