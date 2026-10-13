<?php

namespace App\Notifications\Shop;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells staff who manage orders that a new order needs packing.
 */
class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(email_subject("New order {$this->order->number}"))
            ->markdown('mail.shop.new_order', [
                'order' => $this->order->loadMissing('items'),
                'url' => url()->app("/orders/{$this->order->number}"),
            ]);
    }
}
