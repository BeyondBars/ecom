<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Enums\UserAbility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        abort_unless($request->user()->can(UserAbility::ManageSettings->value), 403);

        return response()->json(['data' => Setting::allWithDefaults()]);
    }

    public function update(Request $request): JsonResponse
    {
        abort_unless($request->user()->can(UserAbility::ManageSettings->value), 403);

        $data = $request->validate([
            'store_name' => ['sometimes', 'string', 'max:100'],
            'support_email' => ['sometimes', 'email', 'max:255'],
            'support_phone' => ['nullable', 'string', 'max:30'],
            'store_address' => ['nullable', 'string', 'max:500'],
            'announcement' => ['nullable', 'string', 'max:200'],
            'free_shipping_threshold' => ['sometimes', 'numeric', 'min:0'],
            'standard_shipping_fee' => ['sometimes', 'numeric', 'min:0'],
            'express_shipping_fee' => ['sometimes', 'numeric', 'min:0'],
            'cod_enabled' => ['sometimes', 'boolean'],
            'cod_max_order_total' => ['nullable', 'numeric', 'min:0'],
            'return_window_days' => ['sometimes', 'integer', 'min:0', 'max:365'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
        ]);

        Setting::put($data);

        return response()->json(['data' => Setting::allWithDefaults()]);
    }
}
