<?php

namespace App\Notifications\Shop;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Customer updates: shipped, delivered, cancelled, return decisions and refunds.
 */
class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const MESSAGES = [
        'shipped' => ['Your order is on its way', 'Good news! Your order has been shipped.'],
        'delivered' => ['Your order has been delivered', 'Your order has been delivered. We hope you love it!'],
        'cancelled' => ['Your order has been cancelled', 'Your order has been cancelled.'],
        'return_approved' => ['Your return has been approved', 'Your return request has been approved. Our courier partner will contact you to arrange a pickup.'],
        'return_rejected' => ['About your return request', 'Unfortunately we could not accept your return request.'],
        'return_completed' => ['We have received your return', 'We have received your returned items. Your refund is being processed.'],
        'refunded' => ['Your refund has been issued', 'Your refund has been issued to your original payment method. It can take 5-7 business days to appear.'],
    ];

    public function __construct(public Order $order, public string $event)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        [$subject, $message] = self::MESSAGES[$this->event];

        return (new MailMessage)
            ->subject(email_subject("{$subject} ({$this->order->number})"))
            ->markdown('mail.shop.order_status', [
                'order' => $this->order,
                'event' => $this->event,
                'message' => $message,
                'name' => $notifiable->first_name ?? null,
                'url' => $this->order->storefrontUrl(),
            ]);
    }
}
