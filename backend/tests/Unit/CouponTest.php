<?php

namespace Tests\Unit;

use App\Models\Coupon;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_determine_if_a_coupon_is_valid()
    {
        // Create an active coupon
        $activeCoupon = Coupon::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->subDay(),
            'expiry_date' => Carbon::now()->addDay(),
            'usage_limit' => 10,
            'usage_count' => 5,
        ]);

        $this->assertTrue($activeCoupon->isValid());

        // Create an inactive coupon
        $inactiveCoupon = Coupon::factory()->create([
            'status' => 'inactive',
            'start_date' => Carbon::now()->subDay(),
            'expiry_date' => Carbon::now()->addDay(),
        ]);

        $this->assertFalse($inactiveCoupon->isValid());

        // Create an expired coupon
        $expiredCoupon = Coupon::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(2),
            'expiry_date' => Carbon::now()->subDay(),
        ]);

        $this->assertFalse($expiredCoupon->isValid());

        // Create a coupon that hasn't started yet
        $futureStartCoupon = Coupon::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->addDay(),
            'expiry_date' => Carbon::now()->addDays(2),
        ]);

        $this->assertFalse($futureStartCoupon->isValid());

        // Create a coupon that has reached its usage limit
        $usageLimitReachedCoupon = Coupon::factory()->create([
            'status' => 'active',
            'start_date' => Carbon::now()->subDay(),
            'expiry_date' => Carbon::now()->addDay(),
            'usage_limit' => 10,
            'usage_count' => 10,
        ]);

        $this->assertFalse($usageLimitReachedCoupon->isValid());
    }

    /** @test */
    public function it_can_determine_if_a_coupon_is_expired()
    {
        // Create an expired coupon
        $expiredCoupon = Coupon::factory()->create([
            'expiry_date' => Carbon::now()->subDay(),
        ]);

        $this->assertTrue($expiredCoupon->isExpired());

        // Create a non-expired coupon
        $validCoupon = Coupon::factory()->create([
            'expiry_date' => Carbon::now()->addDay(),
        ]);

        $this->assertFalse($validCoupon->isExpired());
    }

    /** @test */
    public function it_can_determine_if_a_coupon_has_reached_usage_limit()
    {
        // Create a coupon with usage limit reached
        $limitReachedCoupon = Coupon::factory()->create([
            'usage_limit' => 10,
            'usage_count' => 10,
        ]);

        $this->assertTrue($limitReachedCoupon->hasReachedUsageLimit());

        // Create a coupon with usage limit not reached
        $limitNotReachedCoupon = Coupon::factory()->create([
            'usage_limit' => 10,
            'usage_count' => 5,
        ]);

        $this->assertFalse($limitNotReachedCoupon->hasReachedUsageLimit());

        // Create a coupon with no usage limit
        $noLimitCoupon = Coupon::factory()->create([
            'usage_limit' => null,
            'usage_count' => 100,
        ]);

        $this->assertFalse($noLimitCoupon->hasReachedUsageLimit());
    }

    /** @test */
    public function it_can_increment_usage_count()
    {
        $coupon = Coupon::factory()->create([
            'usage_count' => 5,
        ]);

        $this->assertEquals(5, $coupon->usage_count);

        $coupon->incrementUsage();

        $this->assertEquals(6, $coupon->usage_count);
        $this->assertEquals(6, $coupon->fresh()->usage_count);
    }
}
