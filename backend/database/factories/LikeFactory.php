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
        $likeableTypes = [
            Product::class,
            BlogPost::class,
        ];
        
        $likeableType = $this->faker->randomElement($likeableTypes);
        $likeableId = null;
        
        if ($likeableType === Product::class) {
            $likeableId = Product::factory();
        } elseif ($likeableType === BlogPost::class) {
            $likeableId = BlogPost::factory();
        }
        
        return [
            'user_id' => User::factory(),
            'likeable_type' => $likeableType,
            'likeable_id' => $likeableId,
        ];
    }
    
    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Like $like) {
            if ($like->likeable_id instanceof Factory) {
                $likeable = $like->likeable_id->create();
                $like->likeable_id = $likeable->id;
            }
        });
    }
    
    /**
     * Indicate that the like is for a product.
     */
    public function forProduct(Product $product = null): static
    {
        return $this->state(function (array $attributes) use ($product) {
            return [
                'likeable_type' => Product::class,
                'likeable_id' => $product ? $product->id : Product::factory(),
            ];
        });
    }
    
    /**
     * Indicate that the like is for a blog post.
     */
    public function forBlogPost(BlogPost $blogPost = null): static
    {
        return $this->state(function (array $attributes) use ($blogPost) {
            return [
                'likeable_type' => BlogPost::class,
                'likeable_id' => $blogPost ? $blogPost->id : BlogPost::factory(),
            ];
        });
    }
}
