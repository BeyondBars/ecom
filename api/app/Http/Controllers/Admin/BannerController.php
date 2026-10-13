<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\SaveBannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BannerController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Banner::class);

        return BannerResource::collection(
            QueryBuilder::for(Banner::class)
                ->allowedFilters(AllowedFilter::exact('placement'), AllowedFilter::exact('is_active'))
                ->defaultSort('placement', 'sort_order')
                ->allowedSorts('sort_order', 'created_at', 'placement')
                ->get()
        );
    }

    public function store(SaveBannerRequest $request): JsonResponse
    {
        return (new BannerResource(Banner::create($request->validated())))->response()->setStatusCode(201);
    }

    public function show(Banner $banner): BannerResource
    {
        $this->authorize('view', $banner);

        return new BannerResource($banner);
    }

    public function update(SaveBannerRequest $request, Banner $banner): BannerResource
    {
        $banner->update($request->validated());

        return new BannerResource($banner);
    }

    public function destroy(Banner $banner): Response
    {
        $this->authorize('delete', $banner);
        $banner->delete();

        return response()->noContent();
    }
}
