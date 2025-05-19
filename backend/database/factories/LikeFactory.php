<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Product;
use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 */
class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'likeable_id' => Product::factory(),
            'likeable_type' => Product::class,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Like $like) {
            //
        })->afterCreating(function (Like $like) {
            //
        });
    }

    /**
     * Indicate that the like is for a product.
     */
    public function product(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'likeable_id' => Product::factory(),
                'likeable_type' => Product::class,
            ];
        });
    }

    /**
     * Indicate that the like is for a blog post.
     */
    public function blogPost(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'likeable_id' => BlogPost::factory(),
                'likeable_type' => BlogPost::class,
            ];
        });
    }
}
