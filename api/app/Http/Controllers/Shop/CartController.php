<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CartRequest;
use App\Services\Shop\CartPricer;
use App\Support\Enums\ShippingMethod;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Live prices, stock checks, coupon and shipping for the storefront's cart and checkout pages.
     * Coupon problems are reported in `coupon_error` rather than failing the request.
     */
    public function quote(CartRequest $request, CartPricer $pricer): JsonResponse
    {
        $quote = $pricer->price(
            $request->validated('items'),
            ShippingMethod::from($request->validated('shipping_method', ShippingMethod::Standard->value)),
            $request->validated('coupon_code'),
            $request->user(),
        );

        return response()->json(['data' => $quote->toArray()]);
    }
}
