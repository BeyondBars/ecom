<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Coupon::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discountType = $this->faker->randomElement(['percentage', 'fixed']);
        $discountValue = $discountType === 'percentage' 
            ? $this->faker->numberBetween(5, 50) 
            : $this->faker->randomFloat(2, 5, 50);
        
        $startDate = $this->faker->dateTimeBetween('-3 months', '+1 month');
        $expiryDate = $this->faker->dateTimeBetween($startDate, '+6 months');
        
        $status = 'active';
        $now = now();
        
        if ($now->greaterThan($expiryDate)) {
            $status = 'expired';
        } elseif ($this->faker->boolean(20)) {
            $status = 'inactive';
        }
        
        $usageLimit = $this->faker->boolean(70) ? $this->faker->numberBetween(50, 1000) : null;
        $usageCount = $usageLimit ? $this->faker->numberBetween(0, $usageLimit) : $this->faker->numberBetween(0, 500);
        
        return [
            'code' => strtoupper($this->faker->bothify('???###')),
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'minimum_order_amount' => $this->faker->randomFloat(2, 0, 100),
            'start_date' => $startDate,
            'expiry_date' => $expiryDate,
            'usage_limit' => $usageLimit,
            'usage_count' => $usageCount,
            'status' => $status,
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the coupon is active.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
                'start_date' => now()->subDays(7),
                'expiry_date' => now()->addMonths(1),
            ];
        });
    }

    /**
     * Indicate that the coupon is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
            ];
        });
    }

    /**
     * Indicate that the coupon is expired.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'expired',
                'start_date' => now()->subMonths(3),
                'expiry_date' => now()->subDays(7),
            ];
        });
    }
}
