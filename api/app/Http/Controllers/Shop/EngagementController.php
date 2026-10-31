<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\Product;
use App\Models\StockAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public forms on the storefront: newsletter, contact, back-in-stock alerts.
 */
class EngagementController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'source' => ['nullable', 'string', 'max:50'],
        ]);

        NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower($data['email'])],
            ['source' => $data['source'] ?? 'storefront', 'unsubscribed_at' => null],
        );

        // Same response whether or not they were already subscribed
        return response()->json(['message' => 'Thanks for subscribing!'], 201);
    }

    public function contact(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        ContactMessage::create($data);

        return response()->json(['message' => 'Thanks! We will get back to you within one business day.'], 201);
    }

    public function stockAlert(Request $request, string $slug): JsonResponse
    {
        $product = Product::visible()->where('slug', $slug)->firstOrFail();
        $data = $request->validate(['email' => ['required', 'email:rfc', 'max:255']]);

        StockAlert::updateOrCreate(
            ['product_id' => $product->id, 'email' => strtolower($data['email'])],
            ['notified_at' => null],
        );

        return response()->json(['message' => 'We will email you when it is back in stock.'], 201);
    }
}
