<?php

/*
|--------------------------------------------------------------------------
| Shop defaults
|--------------------------------------------------------------------------
| Admins can override the settings below from the Settings page (stored in the `settings` table).
| These values are used until they do.
*/

return [
    // The customer-facing storefront; links in order emails point here.
    'storefront_url' => env('STOREFRONT_URL', 'http://localhost:3000'),

    'currency' => 'INR',

    // Order numbers look like LFC-100042
    'order_prefix' => env('SHOP_ORDER_PREFIX', 'LFC'),

    // Unpaid online orders are cancelled (and their stock released) after this many minutes.
    'unpaid_order_timeout_minutes' => 30,

    'defaults' => [
        'store_name' => env('APP_NAME', 'Ladies First Couture'),
        'support_email' => env('MAIL_FROM_ADDRESS', 'support@example.com'),
        'support_phone' => null,
        'store_address' => null,
        'free_shipping_threshold' => 999,
        'standard_shipping_fee' => 99,
        'express_shipping_fee' => 199,
        'cod_enabled' => true,
        'cod_max_order_total' => 20000,
        'return_window_days' => 30,
    ],

    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID'),
        'key_secret' => env('RAZORPAY_KEY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],
];
