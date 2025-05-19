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
        // Get users, products, and blog posts
        $users = User::all();
        $products = Product::all();
        $blogPosts = BlogPost::all();
        
        if ($users->isEmpty() || ($products->isEmpty() && $blogPosts->isEmpty())) {
            return;
        }
        
        // Each user likes some products
        foreach ($users as $user) {
            // Like 30% of products
            $likedProducts = $products->random(max(1, intval($products->count() * 0.3)));
            
            foreach ($likedProducts as $product) {
                Like::create([
                    'user_id' => $user->id,
                    'likeable_id' => $product->id,
                    'likeable_type' => Product::class,
                ]);
            }
            
            // Like 40% of blog posts
            if ($blogPosts->isNotEmpty()) {
                $likedBlogPosts = $blogPosts->random(max(1, intval($blogPosts->count() * 0.4)));
                
                foreach ($likedBlogPosts as $blogPost) {
                    Like::create([
                        'user_id' => $user->id,
                        'likeable_id' => $blogPost->id,
                        'likeable_type' => BlogPost::class,
                    ]);
                }
            }
        }
    }
}
