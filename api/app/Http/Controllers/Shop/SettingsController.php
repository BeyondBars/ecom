<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Shop\RazorpayGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class SettingsController extends Controller
{
    /**
     * Store details the storefront shows (contact info, shipping rates, payment options).
     */
    public function show(RazorpayGateway $razorpay): JsonResponse
    {
        $settings = Setting::allWithDefaults();

        return response()->json(['data' => [
            ...Arr::only($settings, [
                'store_name', 'support_email', 'support_phone', 'store_address', 'announcement',
                'free_shipping_threshold', 'standard_shipping_fee', 'express_shipping_fee',
                'cod_enabled', 'cod_max_order_total', 'return_window_days',
                'instagram_url', 'facebook_url', 'youtube_url', 'twitter_url',
            ]),
            'currency' => config('shop.currency'),
            'online_payments_enabled' => $razorpay->isConfigured(),
            'razorpay_key_id' => $razorpay->keyId(),
        ]]);
    }
}
