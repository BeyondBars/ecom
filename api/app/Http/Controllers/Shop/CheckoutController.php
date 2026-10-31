<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Services\Shop\CheckoutService;
use App\Services\Shop\RazorpayGateway;
use App\Support\Enums\PaymentMethod;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    /**
     * Place an order. For online payment the response includes what Razorpay Checkout needs;
     * the storefront then calls POST /shop/orders/{number}/payments/razorpay with the result.
     */
    public function store(CheckoutRequest $request, CheckoutService $checkout, RazorpayGateway $razorpay): JsonResponse
    {
        $order = $checkout->placeOrder($request->user(), $request->validated());

        return (new OrderResource($order))
            ->additional(['payment' => $order->payment_method === PaymentMethod::Razorpay ? [
                'provider' => 'razorpay',
                'key_id' => $razorpay->keyId(),
                'order_id' => $order->razorpay_order_id,
                'amount' => (int) round((float) $order->total * 100),
                'currency' => $order->currency,
            ] : null])
            ->response()
            ->setStatusCode(201);
    }
}
