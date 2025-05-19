<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'sale_price' => $this->faker->optional(0.3)->randomFloat(2, 5, 900),
            'cost' => $this->faker->randomFloat(2, 5, 500),
            'quantity' => $this->faker->numberBetween(0, 100),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'sku' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'featured' => $this->faker->boolean(20),
            'status' => $this->faker->randomElement(['active', 'inactive', 'draft']),
            'images' => json_encode([
                $this->faker->imageUrl(640, 480, 'products', true),
                $this->faker->optional(0.7)->imageUrl(640, 480, 'products', true),
                $this->faker->optional(0.5)->imageUrl(640, 480, 'products', true),
            ]),
            'specifications' => json_encode([
                'weight' => $this->faker->randomFloat(2, 0.1, 10) . ' kg',
                'dimensions' => $this->faker->numberBetween(10, 50) . ' x ' . 
                                $this->faker->numberBetween(10, 50) . ' x ' . 
                                $this->faker->numberBetween(10, 50) . ' cm',
                'color' => $this->faker->colorName(),
                'material' => $this->faker->word(),
            ]),
        ];
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    /**
     * Indicate that the product is on sale.
     */
    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            $price = $attributes['price'] ?? $this->faker->randomFloat(2, 10, 1000);
            $salePrice = $price * 0.8; // 20% discount
            
            return [
                'price' => $price,
                'sale_price' => $salePrice,
            ];
        });
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 0,
        ]);
    }
}
