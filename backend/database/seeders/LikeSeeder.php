<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Like;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        // Get all products
        $products = Product::all();
        
        // Get all blog posts
        $blogPosts = BlogPost::all();
        
        // Create likes for products
        foreach ($products as $product) {
            // Randomly select 0-5 users to like this product
            $likeCount = rand(0, 5);
            $randomUsers = $users->random($likeCount);
            
            foreach ($randomUsers as $user) {
                Like::create([
                    'user_id' => $user->id,
                    'likeable_id' => $product->id,
                    'likeable_type' => Product::class,
                ]);
            }
        }
        
        // Create likes for blog posts
        foreach ($blogPosts as $blogPost) {
            // Randomly select 0-10 users to like this blog post
            $likeCount = rand(0, 10);
            $randomUsers = $users->random($likeCount);
            
            foreach ($randomUsers as $user) {
                Like::create([
                    'user_id' => $user->id,
                    'likeable_id' => $blogPost->id,
                    'likeable_type' => BlogPost::class,
                ]);
            }
        }
    }
}
