<?php

namespace App\Services\Shop;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Notifications\Shop\OrderStatusNotification;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentMethod;
use App\Support\Enums\PaymentStatus;
use App\Support\Enums\ReturnStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * All order state changes go through here so stock, coupons, payments, the timeline and the
 * customer emails stay consistent.
 */
class OrderManager
{
    public function markPaid(Order $order, string $paymentReference, ?User $by = null): Order
    {
        if ($order->payment_status === PaymentStatus::Paid) {
            return $order;
        }

        $wasAwaitingPayment = $order->isAwaitingOnlinePayment();

        $order->update([
            'payment_status' => PaymentStatus::Paid,
            'payment_reference' => $paymentReference,
            'paid_at' => now(),
        ]);
        $order->record('paid', $order->payment_method === PaymentMethod::CashOnDelivery ? 'Cash collected' : 'Payment received', $by);

        // An online order is only "placed" for the customer once it's paid
        if ($wasAwaitingPayment) {
            app(CheckoutService::class)->sendOrderPlacedNotifications($order);
        }

        return $order;
    }

    public function updateStatus(Order $order, OrderStatus $status, ?User $by = null, array $details = []): Order
    {
        if ($status === OrderStatus::Cancelled) {
            return $this->cancel($order, $details['note'] ?? 'Cancelled by the store', $by);
        }

        if (!$order->status->canTransitionTo($status)) {
            throw ValidationException::withMessages(['status' => "An order that is {$order->status->value} can't be marked {$status->value}."]);
        }

        if ($order->isAwaitingOnlinePayment()) {
            throw ValidationException::withMessages(['status' => 'This order is still waiting for online payment.']);
        }

        $order->fill(['status' => $status]);

        if ($status === OrderStatus::Shipped) {
            $order->fill([
                'shipped_at' => now(),
                'courier' => $details['courier'] ?? $order->courier,
                'tracking_number' => $details['tracking_number'] ?? $order->tracking_number,
            ]);
        }

        if ($status === OrderStatus::Delivered) {
            $order->delivered_at = now();

            // Cash on delivery is collected when the parcel is handed over
            if ($order->payment_method === PaymentMethod::CashOnDelivery && $order->payment_status === PaymentStatus::Pending) {
                $order->fill(['payment_status' => PaymentStatus::Paid, 'paid_at' => now()]);
            }
        }

        $order->save();
        $order->record($status->value, $details['note'] ?? null, $by);

        if (in_array($status, [OrderStatus::Shipped, OrderStatus::Delivered], true)) {
            $order->user?->notify(new OrderStatusNotification($order, $status->value));
        }

        return $order;
    }

    public function cancel(Order $order, ?string $reason = null, ?User $by = null, bool $notify = true): Order
    {
        if (!$order->status->isCancellableByCustomer()) {
            throw ValidationException::withMessages(['status' => 'This order has already shipped and can no longer be cancelled.']);
        }

        DB::transaction(function () use ($order, $reason, $by) {
            $order->load('items');
            $order->restock();

            if ($order->coupon_id) {
                Coupon::whereKey($order->coupon_id)->where('used_count', '>', 0)->decrement('used_count');
            }

            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
                'payment_status' => match ($order->payment_status) {
                    PaymentStatus::Paid => PaymentStatus::RefundPending,
                    PaymentStatus::Pending => PaymentStatus::Failed,
                    default => $order->payment_status,
                },
            ]);

            $order->record('cancelled', $reason, $by);
        });

        if ($notify) {
            $order->user?->notify(new OrderStatusNotification($order, 'cancelled'));
        }

        return $order;
    }

    public function requestReturn(Order $order, string $reason, User $by): Order
    {
        if (!$order->canRequestReturn()) {
            throw ValidationException::withMessages(['reason' => 'This order is not eligible for a return.']);
        }

        $order->update([
            'return_status' => ReturnStatus::Requested,
            'return_reason' => $reason,
            'return_requested_at' => now(),
        ]);
        $order->record('return_requested', $reason, $by);

        return $order;
    }

    /**
     * Admin decision on a return request: approve (arrange pickup), reject, or complete
     * (items received: restock and mark for refund).
     */
    public function resolveReturn(Order $order, ReturnStatus $decision, ?string $note, User $by): Order
    {
        $allowed = match ($order->return_status) {
            ReturnStatus::Requested => [ReturnStatus::Approved, ReturnStatus::Rejected],
            ReturnStatus::Approved => [ReturnStatus::Completed],
            default => [],
        };

        if (!in_array($decision, $allowed, true)) {
            throw ValidationException::withMessages(['decision' => 'This return can\'t be changed that way.']);
        }

        DB::transaction(function () use ($order, $decision, $note, $by) {
            $order->return_status = $decision;

            if ($decision === ReturnStatus::Completed) {
                $order->load('items');
                $order->restock();
                $order->status = OrderStatus::Returned;
                $order->payment_status = PaymentStatus::RefundPending;
            }

            $order->save();
            $order->record("return_{$decision->value}", $note, $by);
        });

        $order->user?->notify(new OrderStatusNotification($order, "return_{$decision->value}"));

        return $order;
    }

    public function markRefunded(Order $order, ?string $reference, User $by): Order
    {
        if ($order->payment_status !== PaymentStatus::RefundPending) {
            throw ValidationException::withMessages(['payment_status' => 'There is no refund due on this order.']);
        }

        $order->update(['payment_status' => PaymentStatus::Refunded]);
        $order->record('refunded', $reference ? "Refund reference: {$reference}" : 'Refund issued', $by);
        $order->user?->notify(new OrderStatusNotification($order, 'refunded'));

        return $order;
    }

    public function addNote(Order $order, string $note, User $by): Order
    {
        $order->record('note', $note, $by, internal: true);

        return $order;
    }
}
