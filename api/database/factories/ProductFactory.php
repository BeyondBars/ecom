<?php

namespace Database\Factories;

use App\Models\Category;
use App\Support\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst(fake()->unique()->words(3, true)),
            'sku' => strtoupper(fake()->unique()->bothify('LFC-####-??')),
            'description' => fake()->paragraph(),
            'price' => fake()->randomElement([799, 999, 1499, 1999, 2499]),
            'sale_price' => null,
            'stock' => 20,
            'status' => ProductStatus::Active->value,
            'options' => [
                ['name' => 'Size', 'values' => ['S', 'M', 'L']],
            ],
            'specifications' => [['name' => 'Material', 'value' => 'Cotton']],
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => ProductStatus::Draft->value]);
    }

    public function onSale(float $salePrice): static
    {
        return $this->state(['sale_price' => $salePrice]);
    }

    public function withoutOptions(): static
    {
        return $this->state(['options' => []]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }
}
