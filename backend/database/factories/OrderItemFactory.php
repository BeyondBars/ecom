<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first() ?? Product::factory()->create();
        $quantity = $this->faker->numberBetween(1, 5);
        $price = $product->sale_price ?? $product->price;
        $subtotal = $price * $quantity;
        
        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $price,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'options' => json_encode([
                'color' => $this->faker->optional()->colorName(),
                'size' => $this->faker->optional()->randomElement(['S', 'M', 'L', 'XL']),
            ]),
        ];
    }
}
