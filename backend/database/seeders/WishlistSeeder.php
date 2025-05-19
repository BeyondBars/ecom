<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and products
        $users = User::all();
        $products = Product::all();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }
        
        // Create wishlists for each user
        foreach ($users as $user) {
            // Create 1-3 wishlists per user
            $wishlistCount = rand(1, 3);
            
            for ($i = 0; $i < $wishlistCount; $i++) {
                $wishlist = Wishlist::create([
                    'user_id' => $user->id,
                    'name' => $i === 0 ? 'My Wishlist' : 'Wishlist ' . ($i + 1),
                    'description' => $i === 0 ? 'My default wishlist' : 'Custom wishlist ' . ($i + 1),
                    'is_public' => $i === 0 ? false : (bool)rand(0, 1),
                ]);
                
                // Add 3-8 random products to each wishlist
                $productCount = rand(3, 8);
                $randomProducts = $products->random($productCount);
                
                foreach ($randomProducts as $product) {
                    WishlistItem::create([
                        'wishlist_id' => $wishlist->id,
                        'product_id' => $product->id,
                        'notes' => rand(0, 1) ? 'I want this!' : null,
                        'priority' => rand(0, 10),
                    ]);
                }
            }
        }
    }
}
