<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Shop\OrderManager;
use App\Support\Enums\OrderStatus;
use App\Support\Enums\PaymentMethod;
use App\Support\Enums\PaymentStatus;
use Illuminate\Console\Command;

/**
 * Online orders reserve stock as soon as they're placed. If the customer never completes the
 * payment, cancel the order so the stock goes back on sale.
 */
class ReleaseUnpaidOrders extends Command
{
    protected $signature = 'orders:release-unpaid';

    protected $description = 'Cancel online orders left unpaid and release their stock';

    public function handle(OrderManager $orders): void
    {
        $count = 0;

        Order::query()
            ->where('payment_method', PaymentMethod::Razorpay->value)
            ->where('payment_status', PaymentStatus::Pending->value)
            ->where('status', OrderStatus::Pending->value)
            ->where('created_at', '<', now()->subMinutes(config('shop.unpaid_order_timeout_minutes')))
            ->each(function (Order $order) use ($orders, &$count) {
                $orders->cancel($order, 'Payment not completed', notify: false);
                $count++;
            });

        $this->info("Released {$count} unpaid order(s).");
    }
}
