<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CouponApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** @test */
    public function it_can_list_coupons()
    {
        Coupon::factory()->count(5)->create();
        
        Sanctum::actingAs($this->admin);
        
        $response = $this->getJson('/api/coupons');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'code',
                        'discount_type',
                        'discount_value',
                        'minimum_order_amount',
                        'start_date',
                        'expiry_date',
                        'usage_limit',
                        'usage_count',
                        'status',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'links',
                'meta',
            ]);
    }

    /** @test */
    public function it_can_create_a_coupon()
    {
        Sanctum::actingAs($this->admin);
        
        $couponData = [
            'code' => 'TEST100',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'minimum_order_amount' => 50,
            'start_date' => Carbon::now()->format('Y-m-d'),
            'expiry_date' => Carbon::now()->addMonth()->format('Y-m-d'),
            'usage_limit' => 100,
            'status' => 'active',
        ];
        
        $response = $this->postJson('/api/coupons', $couponData);
        
        $response->assertStatus(201)
            ->assertJsonFragment([
                'code' => 'TEST100',
                'discount_type' => 'percentage',
                'discount_value' => '10.00',
                'minimum_order_amount' => '50.00',
                'usage_limit' => 100,
                'status' => 'active',
            ]);
            
        $this->assertDatabaseHas('coupons', [
            'code' => 'TEST100',
        ]);
    }

    /** @test */
    public function it_can_show_a_coupon()
    {
        $coupon = Coupon::factory()->create();
        
        Sanctum::actingAs($this->admin);
        
        $response = $this->getJson("/api/coupons/{$coupon->id}");
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $coupon->id,
                'code' => $coupon->code,
            ]);
    }

    /** @test */
    public function it_can_update_a_coupon()
    {
        $coupon = Coupon::factory()->create();
        
        Sanctum::actingAs($this->admin);
        
        $updateData = [
            'code' => 'UPDATED100',
            'discount_value' => 20,
            'status' => 'inactive',
        ];
        
        $response = $this->putJson("/api/coupons/{$coupon->id}", $updateData);
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'code' => 'UPDATED100',
                'discount_value' => '20.00',
                'status' => 'inactive',
            ]);
            
        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'code' => 'UPDATED100',
            'status' => 'inactive',
        ]);
    }

    /** @test */
    public function it_can_delete_a_coupon()
    {
        $coupon = Coupon::factory()->create();
        
        Sanctum::actingAs($this->admin);
        
        $response = $this->deleteJson("/api/coupons/{$coupon->id}");
        
        $response->assertStatus(204);
        
        $this->assertDatabaseMissing('coupons', [
            'id' => $coupon->id,
        ]);
    }

    /** @test */
    public function it_can_update_coupon_status()
    {
        $coupon = Coupon::factory()->create(['status' => 'active']);
        
        Sanctum::actingAs($this->admin);
        
        $response = $this->putJson("/api/coupons/{$coupon->id}/status", [
            'status' => 'inactive',
        ]);
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'status' => 'inactive',
            ]);
            
        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'status' => 'inactive',
        ]);
    }

    /** @test */
    public function it_can_validate_a_coupon()
    {
        // Create a valid coupon
        $validCoupon = Coupon::factory()->create([
            'code' => 'VALID100',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'minimum_order_amount' => 50,
            'start_date' => Carbon::now()->subDay(),
            'expiry_date' => Carbon::now()->addDay(),
            'status' => 'active',
        ]);
        
        // Create an expired coupon
        $expiredCoupon = Coupon::factory()->create([
            'code' => 'EXPIRED100',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'minimum_order_amount' => 50,
            'start_date' => Carbon::now()->subDays(10),
            'expiry_date' => Carbon::now()->subDay(),
            'status' => 'active',
        ]);
        
        Sanctum::actingAs($this->admin);
        
        // Test valid coupon with sufficient order amount
        $response = $this->postJson('/api/coupons/validate', [
            'code' => 'VALID100',
            'order_amount' => 100,
        ]);
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'valid' => true,
                'discount_type' => 'percentage',
                'discount_value' => '10.00',
            ]);
            
        // Test valid coupon with insufficient order amount
        $response = $this->postJson('/api/coupons/validate', [
            'code' => 'VALID100',
            'order_amount' => 40,
        ]);
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'valid' => false,
                'message' => 'Minimum order amount of $50.00 required.',
            ]);
            
        // Test expired coupon
        $response = $this->postJson('/api/coupons/validate', [
            'code' => 'EXPIRED100',
            'order_amount' => 100,
        ]);
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'valid' => false,
                'message' => 'Invalid or expired coupon code.',
            ]);
    }
}
