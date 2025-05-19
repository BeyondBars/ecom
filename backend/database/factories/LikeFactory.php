<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\Product;
use App\Models\User;
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
        // Randomly choose between Product and BlogPost for the likeable type
        $likeableType = $this->faker->randomElement([
            Product::class,
            BlogPost::class,
        ]);

        // Get a random ID based on the chosen type
        $likeableId = match ($likeableType) {
            Product::class => Product::factory(),
            BlogPost::class => BlogPost::factory(),
            default => null,
        };

        return [
            'user_id' => User::factory(),
            'likeable_type' => $likeableType,
            'likeable_id' => $likeableId,
        ];
    }

    /**
     * Configure the model factory to create a like for a product.
     */
    public function forProduct(): Factory
    {
        return $this->state(function () {
            return [
                'likeable_type' => Product::class,
                'likeable_id' => Product::factory(),
            ];
        });
    }

    /**
     * Configure the model factory to create a like for a blog post.
     */
    public function forBlogPost(): Factory
    {
        return $this->state(function () {
            return [
                'likeable_type' => BlogPost::class,
                'likeable_id' => BlogPost::factory(),
            ];
        });
    }
}
