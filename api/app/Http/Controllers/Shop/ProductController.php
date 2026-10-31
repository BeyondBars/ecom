<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Support\Sorts\CurrentPriceSort;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Public catalogue for the storefront. Only visible (active) products are returned.
 */
class ProductController extends Controller
{
    public function index()
    {
        $currentPrice = 'CASE WHEN products.sale_price IS NOT NULL AND products.sale_price < products.price THEN products.sale_price ELSE products.price END';

        return ProductResource::collection(
            QueryBuilder::for(Product::query()->visible()->with('images', 'category', 'brand'))
                ->allowedFilters(
                    // Includes products in sub-categories of the chosen category
                    AllowedFilter::callback('category', function (Builder $query, $value) {
                        $slugs = (array) $value;
                        $ids = Category::active()->whereIn('slug', $slugs)->pluck('id');
                        $ids = $ids->merge(Category::active()->whereIn('parent_id', $ids)->pluck('id'));
                        $query->whereIn('category_id', $ids);
                    }),
                    AllowedFilter::callback('brand', fn (Builder $q, $value) => $q->whereHas(
                        'brand',
                        fn ($b) => $b->active()->whereIn('slug', (array) $value)
                    )),
                    AllowedFilter::callback('search', function (Builder $query, $value) {
                        $query->where(fn ($q) => $q
                            ->where('products.name', 'like', "%{$value}%")
                            ->orWhere('products.description', 'like', "%{$value}%")
                            ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$value}%"))
                            ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$value}%")));
                    }),
                    AllowedFilter::callback('min_price', fn (Builder $q, $value) => $q->whereRaw("{$currentPrice} >= CAST(? AS DECIMAL(10,2))", [(float) $value])),
                    AllowedFilter::callback('max_price', fn (Builder $q, $value) => $q->whereRaw("{$currentPrice} <= CAST(? AS DECIMAL(10,2))", [(float) $value])),
                    AllowedFilter::callback('featured', fn (Builder $q, $value) => $value ? $q->where('is_featured', true) : $q),
                    AllowedFilter::callback('on_sale', fn (Builder $q, $value) => $value
                        ? $q->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')
                        : $q),
                    AllowedFilter::callback('in_stock', fn (Builder $q, $value) => $value ? $q->where('stock', '>', 0) : $q),
                )
                ->allowedSorts('name', 'created_at', 'rating', AllowedSort::custom('price', new CurrentPriceSort))
                ->defaultSort('-is_featured', '-created_at')
                ->paginate(min(max((int) request('per_page', 12), 1), 48))
                ->withQueryString()
        );
    }

    public function show(string $slug): ProductResource
    {
        $product = Product::visible()->where('slug', $slug)->with('images', 'category', 'brand')->firstOrFail();

        return new ProductResource($product);
    }

    /**
     * Other products from the same category, best rated first.
     */
    public function related(string $slug)
    {
        $product = Product::visible()->where('slug', $slug)->firstOrFail();

        return ProductResource::collection(
            Product::visible()
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->with('images', 'category', 'brand')
                ->orderByDesc('rating')
                ->limit(4)
                ->get()
        );
    }
}
