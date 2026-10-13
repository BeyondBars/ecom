@component('mail::message')
# Good news!

**{{ $product->name }}** is back in stock at ₹{{ number_format($product->currentPrice(), 2) }}. Popular items sell out quickly, so grab yours soon.

@component('mail::button', ['url' => $url])
Shop now
@endcomponent

You're receiving this because you asked to be told when this item was available again.
@endcomponent
