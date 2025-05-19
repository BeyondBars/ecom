<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Randomly choose between a blog post or product for the commentable
        $commentableType = $this->faker->randomElement([
            BlogPost::class,
            Product::class,
        ]);
        
        // Get a random model of the chosen type
        $commentable = $commentableType::inRandomOrder()->first();
        
        // If no model exists, create one
        if (!$commentable) {
            $commentable = $commentableType::factory()->create();
        }
        
        // Determine if this comment is from a registered user or a guest
        $isRegisteredUser = $this->faker->boolean(70);
        
        $data = [
            'content' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['approved', 'pending', 'rejected']),
            'commentable_id' => $commentable->id,
            'commentable_type' => $commentableType,
        ];
        
        if ($isRegisteredUser) {
            $data['author_id'] = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;
        } else {
            $data['author_name'] = $this->faker->name();
            $data['author_email'] = $this->faker->email();
        }
        
        return $data;
    }

    /**
     * Indicate that the comment is for a blog post.
     */
    public function forBlogPost(): static
    {
        return $this->state(function (array $attributes) {
            $blogPost = BlogPost::inRandomOrder()->first() ?? BlogPost::factory()->create();
            
            return [
                'commentable_id' => $blogPost->id,
                'commentable_type' => BlogPost::class,
            ];
        });
    }

    /**
     * Indicate that the comment is for a product.
     */
    public function forProduct(): static
    {
        return $this->state(function (array $attributes) {
            $product = Product::inRandomOrder()->first() ?? Product::factory()->create();
            
            return [
                'commentable_id' => $product->id,
                'commentable_type' => Product::class,
            ];
        });
    }

    /**
     * Indicate that the comment is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the comment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the comment is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }
}
