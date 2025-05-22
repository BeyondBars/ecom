<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponStoreRequest;
use App\Http\Requests\CouponUpdateRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Coupon::query();

        // Apply filters
        if ($request->has('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->has('expiry_date')) {
            $query->where('expiry_date', '<=', $request->expiry_date);
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $coupons = $query->paginate($perPage);

        return response()->json($coupons);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponStoreRequest $request): JsonResponse
    {
        $coupon = Coupon::create($request->validated());
        return response()->json($coupon, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon): JsonResponse
    {
        return response()->json($coupon);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CouponUpdateRequest $request, Coupon $coupon): JsonResponse
    {
        $coupon->update($request->validated());
        return response()->json($coupon);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();
        return response()->json(null, 204);
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, Coupon $coupon): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $coupon->status = $request->status;
        $coupon->save();

        return response()->json($coupon);
    }

    /**
     * Validate a coupon code.
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('expiry_date', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired coupon code.',
            ]);
        }

        if ($coupon->minimum_order_amount > $request->order_amount) {
            return response()->json([
                'valid' => false,
                'message' => "Minimum order amount of \${$coupon->minimum_order_amount} required.",
                'minimum_order_amount' => $coupon->minimum_order_amount,
            ]);
        }

        if ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon usage limit reached.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'coupon' => $coupon,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
        ]);
    }
}
