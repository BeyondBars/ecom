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
        // Get all users, products, and blog posts
        $users = User::all();
        $products = Product::all();
        $blogPosts = BlogPost::all();

        // Each user likes some products
        foreach ($users as $user) {
            // User likes 0-10 random products
            $likedProductsCount = rand(0, 10);
            $randomProducts = $products->random(min($likedProductsCount, $products->count()));
            
            foreach ($randomProducts as $product) {
                Like::create([
                    'user_id' => $user->id,
                    'likeable_id' => $product->id,
                    'likeable_type' => Product::class,
                ]);
            }

            // User likes 0-5 random blog posts
            $likedBlogPostsCount = rand(0, 5);
            $randomBlogPosts = $blogPosts->random(min($likedBlogPostsCount, $blogPosts->count()));
            
            foreach ($randomBlogPosts as $blogPost) {
                Like::create([
                    'user_id' => $user->id,
                    'likeable_id' => $blogPost->id,
                    'likeable_type' => BlogPost::class,
                ]);
            }
        }
    }
}
