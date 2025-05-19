<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'parent_id' => null,
            'image' => $this->faker->imageUrl(640, 480, 'categories', true),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Indicate that the category is a child category.
     */
    public function child(): static
    {
        return $this->state(function (array $attributes) {
            // Get a random parent category or create one if none exists
            $parent = Category::where('parent_id', null)->inRandomOrder()->first();
            
            if (!$parent) {
                $parent = Category::factory()->create(['parent_id' => null]);
            }
            
            return [
                'parent_id' => $parent->id,
            ];
        });
    }
}
