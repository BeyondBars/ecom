@component('mail::message')
# Hi{{ $name ? " {$name}" : '' }},

{{ $message }}

**Order:** {{ $order->number }}<br>
@if ($event === 'shipped' && $order->tracking_number)
**Courier:** {{ $order->courier ?? 'Our delivery partner' }}<br>
**Tracking number:** {{ $order->tracking_number }}<br>
@endif
@if ($event === 'cancelled' && $order->payment_status->value === 'refund_pending')
Your payment will be refunded to the original payment method.<br>
@endif

@component('mail::button', ['url' => $url])
View your order
@endcomponent
@endcomponent
