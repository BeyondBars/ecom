<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled', 'refunded']);
        $paymentStatus = $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']);
        
        // If order is completed, payment should be paid
        if ($status === 'completed') {
            $paymentStatus = 'paid';
        }
        
        // If order is cancelled, payment should be refunded or failed
        if ($status === 'cancelled') {
            $paymentStatus = $this->faker->randomElement(['failed', 'refunded']);
        }
        
        $subtotal = $this->faker->randomFloat(2, 10, 1000);
        $tax = $subtotal * 0.1; // 10% tax
        $shipping = $this->faker->randomFloat(2, 5, 50);
        $total = $subtotal + $tax + $shipping;
        
        return [
            'number' => 'ORD-' . $this->faker->unique()->numerify('######'),
            'user_id' => User::factory(),
            'status' => $status,
            'payment_status' => $paymentStatus,
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer', 'cash_on_delivery']),
            'shipping_method' => $this->faker->randomElement(['standard', 'express', 'next_day']),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'discount' => 0,
            'total' => $total,
            'notes' => $this->faker->optional(0.3)->sentence(),
            'shipping_address' => json_encode([
                'name' => $this->faker->name(),
                'address' => $this->faker->streetAddress(),
                'city' => $this->faker->city(),
                'state' => $this->faker->state(),
                'postal_code' => $this->faker->postcode(),
                'country' => $this->faker->country(),
                'phone' => $this->faker->phoneNumber(),
            ]),
            'billing_address' => json_encode([
                'name' => $this->faker->name(),
                'address' => $this->faker->streetAddress(),
                'city' => $this->faker->city(),
                'state' => $this->faker->state(),
                'postal_code' => $this->faker->postcode(),
                'country' => $this->faker->country(),
                'phone' => $this->faker->phoneNumber(),
            ]),
        ];
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'payment_status' => $this->faker->randomElement(['failed', 'refunded']),
        ]);
    }
}
