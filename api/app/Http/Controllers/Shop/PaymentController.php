<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Shop\OrderManager;
use App\Services\Shop\RazorpayGateway;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    /**
     * Called by the storefront after Razorpay Checkout succeeds.
     */
    public function razorpay(Request $request, Order $order, RazorpayGateway $razorpay, OrderManager $orders): OrderResource
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $data = $request->validate([
            'razorpay_payment_id' => ['required', 'string', 'max:100'],
            'razorpay_order_id' => ['required', 'string', 'max:100'],
            'razorpay_signature' => ['required', 'string', 'max:255'],
        ]);

        $valid = $data['razorpay_order_id'] === $order->razorpay_order_id
            && $razorpay->verifyPaymentSignature($data['razorpay_order_id'], $data['razorpay_payment_id'], $data['razorpay_signature']);

        if (!$valid) {
            throw ValidationException::withMessages(['razorpay_signature' => 'We could not verify this payment. If money was deducted, please contact support.']);
        }

        $this->recordPayment($order, $data['razorpay_payment_id'], $orders);

        return new OrderResource($order->refresh()->load('items.product:id,slug', 'events'));
    }

    /**
     * Razorpay webhook (payment.captured / order.paid), so an order is marked paid even if the
     * customer closes the browser before the storefront reports back.
     */
    public function razorpayWebhook(Request $request, RazorpayGateway $razorpay, OrderManager $orders): JsonResponse
    {
        abort_unless($razorpay->verifyWebhookSignature($request->getContent(), $request->header('X-Razorpay-Signature')), 400);

        $payment = $request->input('payload.payment.entity');

        if (in_array($request->input('event'), ['payment.captured', 'order.paid'], true) && !empty($payment['order_id'])) {
            $order = Order::where('razorpay_order_id', $payment['order_id'])->first();

            if ($order) {
                $this->recordPayment($order, $payment['id'], $orders);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    protected function recordPayment(Order $order, string $paymentId, OrderManager $orders): void
    {
        if (in_array($order->payment_status, [PaymentStatus::Paid, PaymentStatus::RefundPending, PaymentStatus::Refunded], true)) {
            return;
        }

        // Paid after the unpaid order was released: keep it cancelled and owe a refund
        if ($order->status === OrderStatus::Cancelled) {
            $order->update(['payment_status' => PaymentStatus::RefundPending, 'payment_reference' => $paymentId, 'paid_at' => now()]);
            $order->record('paid', 'Payment received after the order was cancelled; refund due');

            return;
        }

        $orders->markPaid($order, $paymentId);
    }
}
