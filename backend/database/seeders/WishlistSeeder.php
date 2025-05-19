<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
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
        
        // Create 1-3 wishlists for each user
        foreach ($users as $user) {
            $wishlistCount = rand(1, 3);
            
            for ($i = 0; $i < $wishlistCount; $i++) {
                $isDefault = $i === 0;
                $name = $isDefault ? 'My Wishlist' : 'Wishlist ' . ($i + 1);
                
                $wishlist = Wishlist::create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'is_public' => rand(0, 1) === 1,
                    'description' => $isDefault ? 'My default wishlist' : null,
                ]);
                
                // Add 1-5 random products to each wishlist
                $productCount = rand(1, 5);
                $randomProducts = $products->random($productCount);
                
                foreach ($randomProducts as $product) {
                    $wishlist->products()->attach($product->id, [
                        'notes' => rand(0, 1) === 1 ? 'I want this!' : null,
                    ]);
                }
            }
        }
    }
}
