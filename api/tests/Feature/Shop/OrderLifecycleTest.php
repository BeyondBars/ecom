<?php

namespace Tests\Feature\Shop;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\Shop\OrderStatusNotification;
use App\Support\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function orderWithItem(array $orderAttributes = [], int $quantity = 2, int $stock = 3): array
    {
        $product = Product::factory()->create(['stock' => $stock]);
        $order = Order::factory()->create($orderAttributes);
        $order->items()->create(['product_id' => $product->id, 'name' => $product->name, 'price' => 500, 'quantity' => $quantity, 'total' => 500 * $quantity]);

        return [$order, $product];
    }

    public function testCustomersOnlySeeTheirOwnOrders()
    {
        $customer = $this->createStandardUser();
        $mine = Order::factory()->create(['user_id' => $customer->id]);
        $theirs = Order::factory()->create();

        $this->be($customer);
        $this->getJson('shop/orders')->assertJsonCount(1, 'data')->assertJsonPath('data.0.number', $mine->number);
        $this->getJson("shop/orders/{$mine->number}")->assertSuccessful();
        $this->getJson("shop/orders/{$theirs->number}")->assertNotFound();
        $this->postJson("shop/orders/{$theirs->number}/cancel")->assertNotFound();
    }

    public function testCustomerCancelRestoresStockAndCoupon()
    {
        Notification::fake();
        $customer = $this->createStandardUser();
        $coupon = Coupon::factory()->create(['used_count' => 1]);
        [$order, $product] = $this->orderWithItem(['user_id' => $customer->id, 'coupon_id' => $coupon->id]);

        $this->be($customer)->postJson("shop/orders/{$order->number}/cancel", ['reason' => 'Changed my mind'])
            ->assertSuccessful()
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('data.can_cancel', false);

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(0, $coupon->fresh()->used_count);
        Notification::assertSentTo($customer, OrderStatusNotification::class);
    }

    public function testCancellingAPaidOrderMarksRefundDue()
    {
        Notification::fake();
        $customer = $this->createStandardUser();
        [$order] = $this->orderWithItem(['user_id' => $customer->id, 'payment_method' => 'razorpay', 'payment_status' => 'paid', 'status' => 'processing']);

        $this->be($customer)->postJson("shop/orders/{$order->number}/cancel")
            ->assertJsonPath('data.payment_status', 'refund_pending');
    }

    public function testShippedOrdersCannotBeCancelled()
    {
        $customer = $this->createStandardUser();
        $order = Order::factory()->status('shipped')->create(['user_id' => $customer->id]);

        $this->be($customer)->postJson("shop/orders/{$order->number}/cancel")->assertJsonValidationErrors(['status']);
    }

    public function testAdminMovesOrderThroughFulfilment()
    {
        Notification::fake();
        $customer = $this->createStandardUser();
        $order = Order::factory()->create(['user_id' => $customer->id]);
        $this->be($this->createUserWithRole(UserRole::Staff));

        $this->putJson("admin/orders/{$order->number}/status", ['status' => 'shipped'])
            ->assertJsonValidationErrors(['tracking_number']);
        $this->putJson("admin/orders/{$order->number}/status", ['status' => 'delivered'])
            ->assertJsonValidationErrors(['status']); // pending can't jump to delivered

        $this->putJson("admin/orders/{$order->number}/status", ['status' => 'processing'])
            ->assertJsonPath('data.status', 'processing')
            ->assertJsonPath('data.next_statuses', ['shipped', 'cancelled']);

        $this->putJson("admin/orders/{$order->number}/status", ['status' => 'shipped', 'courier' => 'Delhivery', 'tracking_number' => 'DLV1'])
            ->assertJsonPath('data.tracking_number', 'DLV1');

        $this->putJson("admin/orders/{$order->number}/status", ['status' => 'delivered'])
            ->assertJsonPath('data.status', 'delivered')
            ->assertJsonPath('data.payment_status', 'paid'); // COD collected on delivery

        Notification::assertSentToTimes($customer, OrderStatusNotification::class, 2);
    }

    public function testUnpaidOnlineOrdersCannotBeFulfilled()
    {
        $order = Order::factory()->create(['payment_method' => 'razorpay']);
        $this->beAdmin();

        $this->putJson("admin/orders/{$order->number}/status", ['status' => 'processing'])
            ->assertJsonValidationErrors(['status']);
    }

    public function testAdminListHidesAbandonedOnlineOrders()
    {
        Order::factory()->create();
        Order::factory()->create(['payment_method' => 'razorpay']);
        $this->beAdmin();

        $this->getJson('admin/orders')->assertJsonCount(1, 'data');
        $this->getJson('admin/orders?filter[include_unpaid]=1')->assertJsonCount(2, 'data');
    }

    public function testReturnFlowRestocksAndRefunds()
    {
        Notification::fake();
        $customer = $this->createStandardUser();
        [$order, $product] = $this->orderWithItem([
            'user_id' => $customer->id, 'status' => 'delivered', 'delivered_at' => now()->subDays(2), 'payment_status' => 'paid',
        ]);

        $this->be($customer)->postJson("shop/orders/{$order->number}/return", ['reason' => 'Too small'])
            ->assertJsonPath('data.return_status', 'requested')
            ->assertJsonPath('data.can_return', false);

        $this->beAdmin();
        $this->postJson("admin/orders/{$order->number}/return", ['decision' => 'completed'])
            ->assertJsonValidationErrors(['decision']); // must approve first
        $this->postJson("admin/orders/{$order->number}/return", ['decision' => 'approved'])
            ->assertJsonPath('data.return_status', 'approved');
        $this->postJson("admin/orders/{$order->number}/return", ['decision' => 'completed'])
            ->assertJsonPath('data.status', 'returned')
            ->assertJsonPath('data.payment_status', 'refund_pending');
        $this->assertSame(5, $product->fresh()->stock);

        $this->postJson("admin/orders/{$order->number}/refunded", ['reference' => 'rfnd_1'])
            ->assertJsonPath('data.payment_status', 'refunded');
    }

    public function testReturnsCloseAfterTheReturnWindow()
    {
        $customer = $this->createStandardUser();
        $order = Order::factory()->status('delivered', ['delivered_at' => now()->subDays(31)])->create(['user_id' => $customer->id]);

        $this->be($customer)->postJson("shop/orders/{$order->number}/return", ['reason' => 'Too small'])
            ->assertJsonValidationErrors(['reason']);
    }

    public function testInternalNotesAreHiddenFromCustomers()
    {
        $customer = $this->createStandardUser();
        $order = Order::factory()->create(['user_id' => $customer->id]);

        $this->beAdmin()->postJson("admin/orders/{$order->number}/notes", ['note' => 'Fragile, double wrap'])
            ->assertJsonFragment(['note' => 'Fragile, double wrap']);

        $this->be($customer)->getJson("shop/orders/{$order->number}")
            ->assertJsonMissing(['note' => 'Fragile, double wrap'])
            ->assertJsonMissingPath('data.admin_note');
    }

    public function testCustomersCannotUseOrderAdmin()
    {
        $order = Order::factory()->create();
        $this->beUser();

        $this->getJson('admin/orders')->assertForbidden();
        $this->putJson("admin/orders/{$order->number}/status", ['status' => 'processing'])->assertForbidden();
    }
}
