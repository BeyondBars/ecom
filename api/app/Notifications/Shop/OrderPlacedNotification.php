<?php

namespace App\Notifications\Shop;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
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
            ->subject(email_subject("Order {$this->order->number} confirmed"))
            ->markdown('mail.shop.order_placed', [
                'order' => $this->order->loadMissing('items'),
                'name' => $notifiable->first_name ?? null,
                'url' => $this->order->storefrontUrl(),
            ]);
    }
}
