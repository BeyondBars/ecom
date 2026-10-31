@component('mail::table')
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach ($order->items as $item)
| {{ $item->name }}@if (!empty($item->options)) ({{ collect($item->options)->map(fn ($v, $k) => "$k: $v")->implode(', ') }})@endif | {{ $item->quantity }} | ₹{{ number_format((float) $item->total, 2) }} |
@endforeach
| Subtotal | | ₹{{ number_format((float) $order->subtotal, 2) }} |
@if ((float) $order->discount > 0)
| Discount{{ $order->coupon_code ? " ({$order->coupon_code})" : '' }} | | -₹{{ number_format((float) $order->discount, 2) }} |
@endif
| Shipping | | {{ (float) $order->shipping > 0 ? '₹'.number_format((float) $order->shipping, 2) : 'Free' }} |
| **Total** | | **₹{{ number_format((float) $order->total, 2) }}** |
@endcomponent
