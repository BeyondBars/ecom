<?php

namespace Tests\Feature\Shop;

use App\Models\Address;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressAndCouponTest extends TestCase
{
    use RefreshDatabase;

    protected array $address = [
        'label' => 'Home', 'first_name' => 'Priya', 'last_name' => 'Sharma', 'phone' => '9876543210',
        'line1' => '12 MG Road', 'city' => 'Bengaluru', 'state' => 'Karnataka', 'postal_code' => '560001',
    ];

    public function testFirstAddressBecomesDefaultAndDefaultMoves()
    {
        $user = $this->createStandardUser();
        $this->be($user);

        $first = $this->postJson('shop/addresses', $this->address)->assertCreated()->assertJsonPath('data.is_default', true)->json('data.id');
        $second = $this->postJson('shop/addresses', [...$this->address, 'label' => 'Office', 'is_default' => true])->json('data.id');

        $this->assertFalse(Address::find($first)->is_default);
        $this->assertTrue(Address::find($second)->is_default);

        $this->deleteJson("shop/addresses/{$second}")->assertNoContent();
        $this->assertTrue(Address::find($first)->is_default);
    }

    public function testCustomersCannotTouchOtherAddresses()
    {
        $other = Address::factory()->create();
        $this->beUser();

        $this->getJson('shop/addresses')->assertJsonCount(0, 'data');
        $this->putJson("shop/addresses/{$other->id}", ['city' => 'Pune'])->assertForbidden();
        $this->deleteJson("shop/addresses/{$other->id}")->assertNotFound();
    }

    public function testAdminManagesCoupons()
    {
        $this->beAdmin();

        $this->postJson('admin/coupons', ['code' => 'welcome10', 'type' => 'percent', 'value' => 10, 'min_subtotal' => 999])
            ->assertCreated()
            ->assertJsonPath('data.code', 'WELCOME10');

        $this->postJson('admin/coupons', ['code' => 'WELCOME10', 'type' => 'fixed', 'value' => 50])
            ->assertJsonValidationErrors(['code']);
        $this->postJson('admin/coupons', ['code' => 'BIG', 'type' => 'percent', 'value' => 150])
            ->assertJsonValidationErrors(['value']);
        $this->postJson('admin/coupons', ['code' => 'SHIP', 'type' => 'free_shipping'])
            ->assertCreated();
    }

    public function testPercentCouponsRespectMaxDiscount()
    {
        $coupon = Coupon::factory()->make(['type' => 'percent', 'value' => 50, 'max_discount' => 300]);

        $this->assertSame(300.0, $coupon->discountFor(2000));
    }

    public function testCustomersCannotManageCoupons()
    {
        $this->beUser()->getJson('admin/coupons')->assertForbidden();
    }
}
