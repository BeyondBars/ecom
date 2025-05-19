<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WishlistStoreRequest;
use App\Http\Requests\WishlistUpdateRequest;
use App\Models\Wishlist;
use App\Models\Product;
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
    public function index(Request $request)
    {
        $query = Wishlist::with(['items.product']);

        // Filter by user if not admin
        if (!$request->user()->hasRole('Admin')) {
            $query->where('user_id', $request->user()->id);
        } else if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Apply filters
        if ($request->has('is_public')) {
            $query->where('is_public', $request->boolean('is_public'));
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(WishlistStoreRequest $request)
    {
        $data = $request->validated();
        
        // Set user_id to current user if not provided
        if (!isset($data['user_id'])) {
            $data['user_id'] = $request->user()->id;
        }

        $wishlist = Wishlist::create($data);
        $wishlist->load('items.product');

        return response()->json($wishlist, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Wishlist $wishlist)
    {
        $wishlist->load('items.product');
        
        return response()->json($wishlist);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WishlistUpdateRequest $request, Wishlist $wishlist)
    {
        $data = $request->validated();
        
        $wishlist->update($data);
        $wishlist->load('items.product');

        return response()->json($wishlist);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wishlist $wishlist)
    {
        $wishlist->delete();

        return response()->json(null, 204);
    }

    /**
     * Add a product to the wishlist.
     */
    public function addProduct(Request $request, Wishlist $wishlist)
    {
        $this->authorize('update', $wishlist);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->input('product_id'));

        // Check if product already exists in wishlist
        if (!$wishlist->products()->where('product_id', $product->id)->exists()) {
            $wishlist->products()->attach($product->id, [
                'notes' => $request->input('notes'),
            ]);
        }

        $wishlist->load('items.product');

        return response()->json($wishlist);
    }

    /**
     * Remove a product from the wishlist.
     */
    public function removeProduct(Request $request, Wishlist $wishlist)
    {
        $this->authorize('update', $wishlist);

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->input('product_id'));

        $wishlist->products()->detach($product->id);
        $wishlist->load('items.product');

        return response()->json($wishlist);
    }

    /**
     * Get public wishlists.
     */
    public function publicIndex(Request $request)
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
