<?php

namespace App\Notifications\Shop;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackInStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(email_subject("{$this->product->name} is back in stock"))
            ->markdown('mail.shop.back_in_stock', [
                'product' => $this->product,
                'url' => rtrim(config('shop.storefront_url'), '/').'/products/'.$this->product->slug,
            ]);
    }
}
