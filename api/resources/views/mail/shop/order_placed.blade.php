@component('mail::message')
# Thank you for your order{{ $name ? ", {$name}" : '' }}!

We've received order **{{ $order->number }}** and will let you know as soon as it ships.

@include('mail.shop._items')

**Payment:** {{ $order->payment_method->value === 'cod' ? 'Cash on Delivery' : 'Paid online' }}<br>
**Delivering to:** {{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}, {{ $order->shipping_address['city'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}

@component('mail::button', ['url' => $url])
View your order
@endcomponent
@endcomponent
