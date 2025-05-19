<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        // Get all blog posts
        $blogPosts = BlogPost::where('status', 'published')->get();
        
        // Get all products
        $products = Product::where('status', 'active')->get();
        
        // Create comments for blog posts
        foreach ($blogPosts as $blogPost) {
            // Create 0-5 comments per blog post
            $commentCount = rand(0, 5);
            
            for ($i = 0; $i < $commentCount; $i++) {
                // Determine if comment is from registered user or guest
                $isRegisteredUser = rand(0, 1);
                
                $commentData = [
                    'content' => fake()->paragraph(),
                    'status' => fake()->randomElement(['approved', 'pending', 'rejected']),
                    'commentable_id' => $blogPost->id,
                    'commentable_type' => BlogPost::class,
                ];
                
                if ($isRegisteredUser) {
                    $commentData['author_id'] = $users->random()->id;
                } else {
                    $commentData['author_name'] = fake()->name();
                    $commentData['author_email'] = fake()->email();
                }
                
                Comment::create($commentData);
            }
        }
        
        // Create comments for products
        foreach ($products as $product) {
            // Create 0-10 comments per product
            $commentCount = rand(0, 10);
            
            for ($i = 0; $i < $commentCount; $i++) {
                // Determine if comment is from registered user or guest
                $isRegisteredUser = rand(0, 1);
                
                $commentData = [
                    'content' => fake()->paragraph(),
                    'status' => fake()->randomElement(['approved', 'pending', 'rejected']),
                    'commentable_id' => $product->id,
                    'commentable_type' => Product::class,
                ];
                
                if ($isRegisteredUser) {
                    $commentData['author_id'] = $users->random()->id;
                } else {
                    $commentData['author_name'] = fake()->name();
                    $commentData['author_email'] = fake()->email();
                }
                
                Comment::create($commentData);
            }
        }
        
        // Create additional random comments
        Comment::factory()->count(50)->create();
        
        // Create some approved comments
        Comment::factory()->count(30)->approved()->create();
        
        // Create some pending comments
        Comment::factory()->count(20)->pending()->create();
        
        // Create some rejected comments
        Comment::factory()->count(10)->rejected()->create();
    }
}
