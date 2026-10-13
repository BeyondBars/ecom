<?php

namespace Tests\Feature\Shop;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Notifications\Shop\NewOrderNotification;
use App\Notifications\Shop\OrderPlacedNotification;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function address(array $overrides = []): array
    {
        return [
            'first_name' => 'Priya', 'last_name' => 'Sharma', 'phone' => '9876543210', 'line1' => '221 Linking Road',
            'city' => 'Mumbai', 'state' => 'Maharashtra', 'postal_code' => '400050', ...$overrides,
        ];
    }

    protected function checkout(array $data)
    {
        return $this->postJson('shop/checkout', [
            'shipping_method' => 'standard',
            'payment_method' => 'cod',
            'address' => $this->address(),
            ...$data,
        ]);
    }

    public function testQuoteUsesDatabasePricesCouponAndShipping()
    {
        $product = Product::factory()->create(['price' => 600, 'sale_price' => 500]);
        Coupon::factory()->create(['code' => 'WELCOME10', 'type' => 'percent', 'value' => 10, 'min_subtotal' => 999]);

        $this->postJson('shop/cart/quote', [
            'items' => [['product_id' => $product->id, 'quantity' => 2, 'options' => ['Size' => 'M'], 'price' => 1]],
            'coupon_code' => 'welcome10',
        ])
            ->assertSuccessful()
            ->assertJsonPath('data.items.0.price', 500)
            ->assertJsonPath('data.subtotal', 1000)
            ->assertJsonPath('data.discount', 100)
            ->assertJsonPath('data.shipping', 0) // free over ₹999
            ->assertJsonPath('data.total', 900)
            ->assertJsonPath('data.coupon.code', 'WELCOME10');
    }

    public function testQuoteReportsCouponProblemsWithoutFailing()
    {
        $product = Product::factory()->create(['price' => 500]);
        Coupon::factory()->create(['code' => 'FLAT200', 'type' => 'fixed', 'value' => 200, 'min_subtotal' => 1999]);

        $this->postJson('shop/cart/quote', [
            'items' => [['product_id' => $product->id, 'quantity' => 1, 'options' => ['Size' => 'S']]],
            'coupon_code' => 'FLAT200',
            'shipping_method' => 'express',
        ])
            ->assertSuccessful()
            ->assertJsonPath('data.discount', 0)
            ->assertJsonPath('data.shipping', 199)
            ->assertJsonPath('data.coupon_error', 'Add items worth ₹1,499 more to use FLAT200.');
    }

    public function testOptionsMustBeValid()
    {
        $product = Product::factory()->create();

        $this->postJson('shop/cart/quote', ['items' => [['product_id' => $product->id, 'quantity' => 1]]])
            ->assertJsonValidationErrors(['items.0.options']);
        $this->postJson('shop/cart/quote', ['items' => [['product_id' => $product->id, 'quantity' => 1, 'options' => ['Size' => 'XXL']]]])
            ->assertJsonValidationErrors(['items.0.options']);
        $this->postJson('shop/cart/quote', ['items' => [['product_id' => $product->id, 'quantity' => 1, 'options' => ['Size' => 'M', 'Color' => 'Red']]]])
            ->assertJsonValidationErrors(['items.0.options']);
    }

    public function testCheckoutRequiresSignIn()
    {
        $this->postJson('shop/checkout', [])->assertUnauthorized();
    }

    public function testCodCheckoutCreatesOrderReservesStockAndNotifies()
    {
        Notification::fake();
        $staff = $this->createUserWithRole(UserRole::Staff);
        $customer = $this->createStandardUser();
        $product = Product::factory()->create(['price' => 1499, 'stock' => 5]);

        $this->be($customer);
        $response = $this->checkout([
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2, 'options' => ['Size' => 'M']],
                ['product_id' => $product->id, 'quantity' => 1, 'options' => ['Size' => 'S']],
            ],
            'save_address' => true,
        ])->assertCreated();

        $response
            ->assertJsonPath('data.total', 4497)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.items.1.options.Size', 'S')
            ->assertJsonPath('payment', null);

        $this->assertMatchesRegularExpression('/^LFC-1\d{5}$/', $response->json('data.number'));
        $this->assertSame(2, $product->fresh()->stock);
        $this->assertSame(1, $customer->addresses()->count());
        Notification::assertSentTo($customer, OrderPlacedNotification::class);
        Notification::assertSentTo($staff, NewOrderNotification::class);
    }

    public function testCannotBuyMoreThanInStockAcrossLines()
    {
        $product = Product::factory()->create(['stock' => 2]);
        $this->beUser();

        $this->checkout(['items' => [
            ['product_id' => $product->id, 'quantity' => 2, 'options' => ['Size' => 'M']],
            ['product_id' => $product->id, 'quantity' => 1, 'options' => ['Size' => 'L']],
        ]])->assertJsonValidationErrors(['items.1.quantity']);

        $this->assertSame(2, $product->fresh()->stock);
        $this->assertSame(0, Order::count());
    }

    public function testDraftProductsCannotBeOrdered()
    {
        $product = Product::factory()->draft()->create();
        $this->beUser();

        $this->checkout(['items' => [['product_id' => $product->id, 'quantity' => 1, 'options' => ['Size' => 'M']]]])
            ->assertJsonValidationErrors(['items.0.product_id']);
    }

    public function testInvalidCouponFailsCheckout()
    {
        $product = Product::factory()->create();
        Coupon::factory()->create(['code' => 'OLD', 'expires_at' => now()->subDay()]);
        $this->beUser();

        $this->checkout([
            'items' => [['product_id' => $product->id, 'quantity' => 1, 'options' => ['Size' => 'M']]],
            'coupon_code' => 'OLD',
        ])->assertJsonValidationErrors(['coupon_code']);
    }

    public function testCouponPerCustomerLimit()
    {
        Notification::fake();
        $product = Product::factory()->withoutOptions()->create(['price' => 1000]);
        $coupon = Coupon::factory()->create(['code' => 'ONCE', 'usage_limit_per_user' => 1]);
        $this->beUser();

        $this->checkout(['items' => [['product_id' => $product->id, 'quantity' => 1]], 'coupon_code' => 'ONCE'])
            ->assertCreated()
            ->assertJsonPath('data.discount', 100);
        $this->assertSame(1, $coupon->fresh()->used_count);

        $this->checkout(['items' => [['product_id' => $product->id, 'quantity' => 1]], 'coupon_code' => 'ONCE'])
            ->assertJsonValidationErrors(['coupon_code']);
    }

    public function testSavedAddressMustBelongToTheCustomer()
    {
        $product = Product::factory()->withoutOptions()->create();
        $someoneElses = Address::factory()->create();
        $this->beUser();

        $this->postJson('shop/checkout', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'shipping_method' => 'standard',
            'payment_method' => 'cod',
            'address_id' => $someoneElses->id,
        ])->assertJsonValidationErrors(['address_id']);
    }

    public function testIndianAddressValidation()
    {
        $product = Product::factory()->withoutOptions()->create();
        $this->beUser();

        $this->checkout([
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'address' => $this->address(['postal_code' => '1234', 'phone' => '12345']),
        ])->assertJsonValidationErrors(['address.postal_code', 'address.phone']);
    }

    public function testPhoneNumbersWithSpacesAreAccepted()
    {
        $product = Product::factory()->withoutOptions()->create();
        $this->beUser();

        $this->checkout([
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'address' => $this->address(['phone' => '98765 43210']),
        ])->assertCreated()->assertJsonPath('data.shipping_address.phone', '9876543210');

        $this->postJson('shop/addresses', $this->address(['phone' => '+91 98765-43210']))->assertCreated();
    }

    public function testCodCanBeDisabledOrCapped()
    {
        $product = Product::factory()->withoutOptions()->create(['price' => 30000]);
        $this->beUser();

        $this->checkout(['items' => [['product_id' => $product->id, 'quantity' => 1]]])
            ->assertJsonValidationErrors(['payment_method']);

        Setting::put(['cod_enabled' => false]);
        $cheap = Product::factory()->withoutOptions()->create(['price' => 500]);
        $this->checkout(['items' => [['product_id' => $cheap->id, 'quantity' => 1]]])
            ->assertJsonValidationErrors(['payment_method']);
    }

    public function testRazorpayCheckoutAndPaymentVerification()
    {
        Notification::fake();
        config(['shop.razorpay.key_id' => 'rzp_test_key', 'shop.razorpay.key_secret' => 'secret']);
        Http::fake(['api.razorpay.com/*' => Http::response(['id' => 'order_RZP123'])]);

        $customer = $this->createStandardUser();
        $product = Product::factory()->withoutOptions()->create(['price' => 1500]);
        $this->be($customer);

        $order = $this->checkout([
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'razorpay',
        ])
            ->assertCreated()
            ->assertJsonPath('payment.order_id', 'order_RZP123')
            ->assertJsonPath('payment.amount', 150000)
            ->json('data');

        Http::assertSent(fn ($request) => $request['amount'] === 150000 && $request['currency'] === 'INR');
        Notification::assertNothingSentTo($customer); // not confirmed until paid

        $this->postJson("shop/orders/{$order['number']}/payments/razorpay", [
            'razorpay_order_id' => 'order_RZP123',
            'razorpay_payment_id' => 'pay_1',
            'razorpay_signature' => 'forged',
        ])->assertJsonValidationErrors(['razorpay_signature']);

        $this->postJson("shop/orders/{$order['number']}/payments/razorpay", [
            'razorpay_order_id' => 'order_RZP123',
            'razorpay_payment_id' => 'pay_1',
            'razorpay_signature' => hash_hmac('sha256', 'order_RZP123|pay_1', 'secret'),
        ])
            ->assertSuccessful()
            ->assertJsonPath('data.payment_status', 'paid')
            ->assertJsonPath('data.payment_reference', 'pay_1');

        Notification::assertSentTo($customer, OrderPlacedNotification::class);
    }

    public function testRazorpayWebhookMarksOrderPaid()
    {
        Notification::fake();
        config(['shop.razorpay.webhook_secret' => 'whsec']);
        $order = Order::factory()->create(['payment_method' => 'razorpay', 'razorpay_order_id' => 'order_W1']);

        $payload = json_encode(['event' => 'payment.captured', 'payload' => ['payment' => ['entity' => ['id' => 'pay_W1', 'order_id' => 'order_W1']]]]);

        $this->call('POST', 'shop/payments/razorpay/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_RAZORPAY_SIGNATURE' => 'bad',
        ], $payload)->assertStatus(400);

        $this->call('POST', 'shop/payments/razorpay/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_RAZORPAY_SIGNATURE' => hash_hmac('sha256', $payload, 'whsec'),
        ], $payload)->assertSuccessful();

        $this->assertSame('paid', $order->fresh()->payment_status->value);
    }

    public function testUnpaidOnlineOrdersAreReleased()
    {
        $product = Product::factory()->create(['stock' => 3]);
        $order = Order::factory()->create(['payment_method' => 'razorpay', 'created_at' => now()->subHour()]);
        $order->items()->create(['product_id' => $product->id, 'name' => 'x', 'price' => 1, 'quantity' => 2, 'total' => 2]);

        $this->artisan('orders:release-unpaid')->assertSuccessful();

        $this->assertSame('cancelled', $order->fresh()->status->value);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function testOnlinePaymentNeedsRazorpayKeys()
    {
        config(['shop.razorpay.key_id' => null]);
        $product = Product::factory()->withoutOptions()->create();
        $this->beUser();

        $this->checkout(['items' => [['product_id' => $product->id, 'quantity' => 1]], 'payment_method' => 'razorpay'])
            ->assertJsonValidationErrors(['payment_method']);
    }
}
