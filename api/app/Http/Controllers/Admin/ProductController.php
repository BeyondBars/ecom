<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\SaveProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Support\Sorts\CurrentPriceSort;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Product::class);

        return ProductResource::collection(
            QueryBuilder::for(Product::query()->with('images', 'category', 'brand'))
                ->allowedFilters(
                    AllowedFilter::exact('status'),
                    AllowedFilter::exact('category_id'),
                    AllowedFilter::exact('brand_id'),
                    AllowedFilter::exact('is_featured'),
                    AllowedFilter::callback('low_stock', fn ($q, $value) => $value
                        ? $q->where('stock', '<=', Product::LOW_STOCK_THRESHOLD)
                        : $q),
                    AllowedFilter::callback('search', function ($query, $value) {
                        $query->where(fn ($q) => $q->where('name', 'like', "%{$value}%")->orWhere('sku', 'like', "%{$value}%"));
                    }),
                )
                ->allowedSorts(
                    'name',
                    'stock',
                    'created_at',
                    'rating',
                    AllowedSort::custom('price', new CurrentPriceSort),
                )
                ->defaultSort('-created_at')
                ->paginate(min((int) request('per_page', 15), 100))
        );
    }

    public function store(SaveProductRequest $request): JsonResponse
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create(Arr::except($request->validated(), ['images']));
            $this->syncImages($product, $request->validated('images', []));

            return $product;
        });

        return (new ProductResource($product->load('images', 'category', 'brand')))->response()->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        $this->authorize('view', $product);

        return new ProductResource($product->load('images', 'category', 'brand'));
    }

    public function update(SaveProductRequest $request, Product $product): ProductResource
    {
        DB::transaction(function () use ($request, $product) {
            $product->update(Arr::except($request->validated(), ['images']));

            if ($request->has('images')) {
                $this->syncImages($product, $request->validated('images', []));
            }
        });

        return new ProductResource($product->load('images', 'category', 'brand'));
    }

    public function destroy(Product $product): Response
    {
        $this->authorize('delete', $product);

        // Order lines keep their own copy of name/price, so past orders stay intact
        $product->delete();

        return response()->noContent();
    }

    /**
     * Replace the product's gallery with the given images, in order.
     */
    protected function syncImages(Product $product, array $images): void
    {
        $product->images()->delete();

        foreach (array_values($images) as $index => $image) {
            $product->images()->create([
                'url' => $image['url'],
                'alt' => $image['alt'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }
}
