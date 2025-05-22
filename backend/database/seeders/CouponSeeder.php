<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some predefined coupons
        Coupon::create([
            'code' => 'WELCOME10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'minimum_order_amount' => 0,
            'start_date' => now()->startOfYear(),
            'expiry_date' => now()->endOfYear(),
            'usage_limit' => null,
            'usage_count' => 245,
            'status' => 'active',
        ]);

        Coupon::create([
            'code' => 'SUMMER2023',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'minimum_order_amount' => 50,
            'start_date' => now()->startOfMonth()->subMonth(),
            'expiry_date' => now()->startOfMonth()->addMonth(),
            'usage_limit' => 1000,
            'usage_count' => 450,
            'status' => 'active',
        ]);

        Coupon::create([
            'code' => 'FREESHIP',
            'discount_type' => 'fixed',
            'discount_value' => 15,
            'minimum_order_amount' => 75,
            'start_date' => now()->subMonths(3),
            'expiry_date' => now()->subDays(15),
            'usage_limit' => 500,
            'usage_count' => 500,
            'status' => 'expired',
        ]);

        // Create random coupons
        Coupon::factory()->count(20)->create();
        
        // Create some active coupons
        Coupon::factory()->active()->count(5)->create();
        
        // Create some inactive coupons
        Coupon::factory()->inactive()->count(3)->create();
        
        // Create some expired coupons
        Coupon::factory()->expired()->count(2)->create();
    }
}
