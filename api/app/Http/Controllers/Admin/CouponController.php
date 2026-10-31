<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\SaveCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CouponController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Coupon::class);

        return CouponResource::collection(
            QueryBuilder::for(Coupon::class)
                ->allowedFilters(AllowedFilter::exact('is_active'), AllowedFilter::exact('type'), AllowedFilter::partial('search', 'code'))
                ->allowedSorts('code', 'created_at', 'expires_at', 'used_count')
                ->defaultSort('-created_at')
                ->paginate(min((int) request('per_page', 15), 100))
        );
    }

    public function store(SaveCouponRequest $request): JsonResponse
    {
        return (new CouponResource(Coupon::create($request->validated())))->response()->setStatusCode(201);
    }

    public function show(Coupon $coupon): CouponResource
    {
        $this->authorize('view', $coupon);

        return new CouponResource($coupon);
    }

    public function update(SaveCouponRequest $request, Coupon $coupon): CouponResource
    {
        $coupon->update($request->validated());

        return new CouponResource($coupon);
    }

    public function destroy(Coupon $coupon): Response
    {
        $this->authorize('delete', $coupon);

        // Past orders keep the code they used (coupon_code); the link is nulled
        $coupon->delete();

        return response()->noContent();
    }
}
