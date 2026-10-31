<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => 'LFC-'.fake()->unique()->numberBetween(200000, 999999),
            'user_id' => User::factory(),
            'email' => fake()->safeEmail(),
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'shipping_method' => 'standard',
            'shipping_address' => [
                'first_name' => 'Priya', 'last_name' => 'Sharma', 'phone' => '9876543210', 'line1' => '12 MG Road',
                'city' => 'Bengaluru', 'state' => 'Karnataka', 'postal_code' => '560001', 'country' => 'India',
            ],
            'subtotal' => 1000,
            'discount' => 0,
            'shipping' => 0,
            'total' => 1000,
            'currency' => 'INR',
        ];
    }

    public function status(string $status, array $extra = []): static
    {
        return $this->state(['status' => $status, ...$extra]);
    }
}
