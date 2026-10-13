<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\Shop\NewOrderNotification;
use App\Notifications\Shop\OrderPlacedNotification;
use App\Notifications\Shop\OrderStatusNotification;
use App\Support\EmailTemplates\EmailTemplate;
use App\Support\EmailTemplates\EmailTemplateRegistry;
use App\Support\Enums\EmailTemplateType;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * Shop wiring that sits alongside the starter's providers (kept separate so starter updates merge
 * cleanly): the shop's rate limits and its emails on the Email Templates page.
 */
class ShopServiceProvider extends ServiceProvider
{
    public function boot(EmailTemplateRegistry $templates): void
    {
        $this->configureRateLimiters();
        $this->registerEmailTemplates($templates);
    }

    protected function configureRateLimiters(): void
    {
        RateLimiter::for('checkout', fn (Request $request) => Limit::perMinute(10)->by('checkout:'.$request->user()?->id));

        // Public forms: newsletter, contact, back-in-stock alerts, blog comments
        RateLimiter::for('shop-forms', fn (Request $request) => [
            Limit::perMinute(5)->by('shop-forms:'.$request->ip()),
            Limit::perHour(30)->by('shop-forms-h:'.$request->ip()),
        ]);
    }

    protected function registerEmailTemplates(EmailTemplateRegistry $templates): void
    {
        $templates->register(new EmailTemplate(
            key: 'order_placed',
            name: 'Order Confirmation',
            type: EmailTemplateType::Transactional,
            triggeredBy: 'order.placed',
            description: 'Sent to the customer when a Cash on Delivery order is placed or an online payment succeeds.',
            sample: fn () => new OrderPlacedNotification(static::sampleOrder()),
        ));

        $templates->register(new EmailTemplate(
            key: 'new_order_staff',
            name: 'New Order (staff)',
            type: EmailTemplateType::Transactional,
            triggeredBy: 'order.placed',
            description: 'Alerts staff who manage orders that a new order is ready to pack.',
            sample: fn () => new NewOrderNotification(static::sampleOrder()),
        ));

        foreach (OrderStatusNotification::MESSAGES as $event => [$subject]) {
            $templates->register(new EmailTemplate(
                key: "order_{$event}",
                name: 'Order: '.ucwords(str_replace('_', ' ', $event)),
                type: EmailTemplateType::Transactional,
                triggeredBy: "order.{$event}",
                description: "{$subject}.",
                sample: fn () => new OrderStatusNotification(static::sampleOrder(), $event),
            ));
        }
    }

    /**
     * An unsaved order used for previews and test sends.
     */
    public static function sampleOrder(): Order
    {
        $order = new Order([
            'number' => config('shop.order_prefix').'-100042',
            'email' => 'customer@example.com',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'shipped',
            'shipping_method' => 'standard',
            'shipping_address' => [
                'first_name' => 'Priya', 'last_name' => 'Sharma', 'phone' => '9876543210', 'line1' => '12 MG Road',
                'city' => 'Bengaluru', 'state' => 'Karnataka', 'postal_code' => '560001', 'country' => 'India',
            ],
            'coupon_code' => 'WELCOME10',
            'subtotal' => 2498,
            'discount' => 250,
            'shipping' => 0,
            'total' => 2248,
            'courier' => 'Delhivery',
            'tracking_number' => 'DLV0000000000',
        ]);

        $order->setRelation('items', collect([
            new OrderItem(['name' => 'Floral Print Maxi Dress', 'options' => ['Size' => 'M', 'Color' => 'Rose'], 'price' => 1499, 'quantity' => 1, 'total' => 1499]),
            new OrderItem(['name' => 'Silk Wrap Blouse', 'options' => ['Size' => 'S'], 'price' => 999, 'quantity' => 1, 'total' => 999]),
        ]));

        return $order;
    }
}
