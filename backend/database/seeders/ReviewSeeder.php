<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products and users
        $products = Product::all();
        $users = User::all();
        
        if ($products->isEmpty() || $users->isEmpty()) {
            // Create some products and users if none exist
            if ($products->isEmpty()) {
                $products = Product::factory()->count(10)->create();
            }
            
            if ($users->isEmpty()) {
                $users = User::factory()->count(20)->create();
            }
        }
        
        // Create reviews for each product
        foreach ($products as $product) {
            // Get a random subset of users to review this product
            $reviewers = $users->random(rand(5, 15));
            
            foreach ($reviewers as $user) {
                Review::factory()->create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                ]);
            }
            
            // Create a few featured reviews
            Review::factory()->count(2)->featured()->create([
                'product_id' => $product->id,
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
