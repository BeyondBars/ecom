<?php

namespace App\Services\Shop;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class RazorpayGateway
{
    public function isConfigured(): bool
    {
        return filled(config('shop.razorpay.key_id')) && filled(config('shop.razorpay.key_secret'));
    }

    public function keyId(): ?string
    {
        return config('shop.razorpay.key_id');
    }

    /**
     * Create a Razorpay order for the amount (in rupees). Returns Razorpay's order id.
     */
    public function createOrder(float $amount, string $receipt): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Online payments are not configured.');
        }

        $response = Http::withBasicAuth(config('shop.razorpay.key_id'), config('shop.razorpay.key_secret'))
            ->acceptJson()
            ->timeout(15)
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => (int) round($amount * 100), // paise
                'currency' => config('shop.currency'),
                'receipt' => $receipt,
            ]);

        if (!$response->successful() || !$response->json('id')) {
            throw new RuntimeException('Could not start the online payment. Please try again.');
        }

        return $response->json('id');
    }

    /**
     * Checks the signature Razorpay Checkout returns after a successful payment.
     */
    public function verifyPaymentSignature(string $razorpayOrderId, string $paymentId, string $signature): bool
    {
        $secret = (string) config('shop.razorpay.key_secret');

        if ($secret === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha256', "{$razorpayOrderId}|{$paymentId}", $secret), $signature);
    }

    /**
     * Checks the X-Razorpay-Signature header of a webhook against the raw request body.
     */
    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        $secret = (string) config('shop.razorpay.webhook_secret');

        if ($secret === '' || blank($signature)) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $payload, $secret), $signature);
    }
}
