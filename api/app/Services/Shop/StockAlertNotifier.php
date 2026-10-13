<?php

namespace App\Services\Shop;

use App\Models\Product;
use App\Notifications\Shop\BackInStockNotification;
use Illuminate\Support\Facades\Notification;

class StockAlertNotifier
{
    public function notify(Product $product): void
    {
        if (!$product->isVisible()) {
            return;
        }

        $product->stockAlerts()->whereNull('notified_at')->each(function ($alert) use ($product) {
            Notification::route('mail', $alert->email)->notify(new BackInStockNotification($product));
            $alert->update(['notified_at' => now()]);
        });
    }
}
