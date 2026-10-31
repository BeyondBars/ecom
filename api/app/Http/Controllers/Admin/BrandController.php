<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\SaveBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BrandController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Brand::class);

        return BrandResource::collection(
            QueryBuilder::for(Brand::query()->withCount('products'))
                ->allowedFilters(AllowedFilter::exact('is_active'), AllowedFilter::partial('search', 'name'))
                ->allowedSorts('name', 'created_at')
                ->defaultSort('name')
                ->get()
        );
    }

    public function store(SaveBrandRequest $request): JsonResponse
    {
        return (new BrandResource(Brand::create($request->validated())))->response()->setStatusCode(201);
    }

    public function show(Brand $brand): BrandResource
    {
        $this->authorize('view', $brand);

        return new BrandResource($brand->loadCount('products'));
    }

    public function update(SaveBrandRequest $request, Brand $brand): BrandResource
    {
        $brand->update($request->validated());

        return new BrandResource($brand);
    }

    public function destroy(Brand $brand): Response
    {
        $this->authorize('delete', $brand);

        // Products keep existing without a brand (brand_id is nulled by the foreign key)
        $brand->delete();

        return response()->noContent();
    }
}
