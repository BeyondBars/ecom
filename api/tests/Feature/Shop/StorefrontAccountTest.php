<?php

namespace Tests\Feature\Shop;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\SignupVerifyEmailNotification;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StorefrontAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function mailUrl($notification, $notifiable): string
    {
        return $notification->toMail($notifiable)->viewData['url'];
    }

    public function testSignUpLinkOpensTheStorefront()
    {
        Notification::fake();
        config(['shop.storefront_url' => 'https://shop.test']);

        $this->postJson('shop/register', ['email' => 'new@example.com'])->assertSuccessful();

        Notification::assertSentTo(new AnonymousNotifiable, SignupVerifyEmailNotification::class, function ($notification, $channels, $notifiable) {
            return str_starts_with($this->mailUrl($notification, $notifiable), 'https://shop.test/register/verify/');
        });
    }

    public function testCustomerPasswordResetLinksToTheStorefrontAndStaffToTheAdmin()
    {
        Notification::fake();
        config(['shop.storefront_url' => 'https://shop.test', 'app.js_app_url' => 'https://admin.test']);
        $customer = $this->createStandardUser(['email' => 'shopper@example.com']);
        $staff = $this->createUserWithRole(UserRole::Staff, ['email' => 'staff@example.com']);

        $this->postJson('password/forgot', ['email' => 'shopper@example.com'])->assertSuccessful();
        $this->postJson('password/forgot', ['email' => 'staff@example.com'])->assertSuccessful();

        Notification::assertSentTo($customer, ResetPasswordNotification::class,
            fn ($n) => str_starts_with($this->mailUrl($n, $customer), 'https://shop.test/reset-password/'));
        Notification::assertSentTo($staff, ResetPasswordNotification::class,
            fn ($n) => str_starts_with($this->mailUrl($n, $staff), 'https://admin.test/set-password/'));
    }

    public function testIndianMobileNumbersAreAccepted()
    {
        $user = $this->createStandardUser();

        foreach (['98765 43210', '+91 98765 43210', '09876543210'] as $mobile) {
            $this->be($user)->putJson("users/{$user->id}", ['mobile' => $mobile])->assertSuccessful();
        }

        $this->be($user)->putJson("users/{$user->id}", ['mobile' => '12345'])->assertJsonValidationErrors(['mobile']);
    }

    public function testCustomerCanDeleteTheirAccountAndOrdersAreKept()
    {
        $customer = $this->createStandardUser(['password' => 'Secret*12345']);
        $order = Order::factory()->create(['user_id' => $customer->id]);

        $this->be($customer)->deleteJson('shop/account', ['password' => 'wrong'])->assertJsonValidationErrors(['password']);
        $this->be($customer)->deleteJson('shop/account', ['password' => 'Secret*12345'])->assertNoContent();

        $this->assertModelMissing($customer);
        $this->assertNull($order->fresh()->user_id);
    }

    public function testStaffCannotDeleteThemselvesFromTheShop()
    {
        $staff = $this->createUserWithRole(UserRole::Staff, ['password' => 'Secret*12345']);

        $this->be($staff)->deleteJson('shop/account', ['password' => 'Secret*12345'])->assertForbidden();
        $this->assertModelExists($staff);
    }

    public function testStorefrontServerGetsAHigherRateLimit()
    {
        config(['shop.storefront_server_key' => 'secret-key']);
        $limiter = \Illuminate\Support\Facades\RateLimiter::limiter('api');

        $browser = \Illuminate\Http\Request::create('/shop/products', 'GET', server: ['REMOTE_ADDR' => '10.0.0.1']);
        $server = \Illuminate\Http\Request::create('/shop/products', 'GET', server: ['REMOTE_ADDR' => '10.0.0.1', 'HTTP_X_STOREFRONT_KEY' => 'secret-key']);
        $wrongKey = \Illuminate\Http\Request::create('/shop/products', 'GET', server: ['REMOTE_ADDR' => '10.0.0.1', 'HTTP_X_STOREFRONT_KEY' => 'guess']);

        $this->assertSame(120, $limiter($browser)->maxAttempts);
        $this->assertSame(3000, $limiter($server)->maxAttempts);
        $this->assertSame(120, $limiter($wrongKey)->maxAttempts);
    }

    public function testOrderItemsLinkToTheirProduct()
    {
        $customer = $this->createStandardUser();
        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $customer->id]);
        $order->items()->create(['product_id' => $product->id, 'name' => $product->name, 'price' => 1, 'quantity' => 1, 'total' => 1]);

        $this->be($customer)->getJson("shop/orders/{$order->number}")
            ->assertJsonPath('data.items.0.slug', $product->slug);
    }
}
