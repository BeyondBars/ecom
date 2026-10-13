<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        return ProductResource::collection(
            $request->user()->wishlist()->visible()->with('images', 'category', 'brand')->latest('wishlist_items.created_at')->get()
        );
    }

    public function store(Request $request): Response
    {
        $data = $request->validate(['product_id' => ['required', 'integer']]);
        $product = Product::visible()->findOrFail($data['product_id']);

        $request->user()->wishlist()->syncWithoutDetaching([$product->id]);

        return response()->noContent();
    }

    public function destroy(Request $request, Product $product): Response
    {
        $request->user()->wishlist()->detach($product->id);

        return response()->noContent();
    }
}
