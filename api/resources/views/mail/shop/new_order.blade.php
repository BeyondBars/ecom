@component('mail::message')
# New order {{ $order->number }}

{{ $order->email }} placed an order for **₹{{ number_format((float) $order->total, 2) }}**
({{ $order->payment_method->value === 'cod' ? 'Cash on Delivery' : 'paid online' }}, {{ $order->shipping_method->value }} shipping).

@include('mail.shop._items')

@component('mail::button', ['url' => $url])
Open in admin
@endcomponent
@endcomponent
