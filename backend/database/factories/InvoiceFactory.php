<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 10, 1000);
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax;
        
        $issueDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $dueDate = clone $issueDate;
        $dueDate->modify('+30 days');
        
        return [
            'number' => 'INV-' . $this->faker->unique()->numerify('######'),
            'customer_id' => User::factory(),
            'order_id' => Order::factory(),
            'status' => $this->faker->randomElement(['draft', 'pending', 'paid', 'cancelled']),
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'notes' => $this->faker->optional(0.3)->sentence(),
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
     * Indicate that the invoice is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }

    /**
     * Indicate that the invoice is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the invoice is overdue.
     */
    public function overdue(): static
    {
        return $this->state(function (array $attributes) {
            $issueDate = $this->faker->dateTimeBetween('-2 months', '-1 month');
            $dueDate = clone $issueDate;
            $dueDate->modify('+15 days');
            
            return [
                'status' => 'pending',
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
            ];
        });
    }
}
