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
        // Get all users and products
        $users = User::all();
        $products = Product::all();

        // Create 1-3 wishlists for each user
        foreach ($users as $user) {
            $wishlistCount = rand(1, 3);
            
            for ($i = 0; $i < $wishlistCount; $i++) {
                $wishlist = Wishlist::create([
                    'user_id' => $user->id,
                    'name' => $i === 0 ? 'My Wishlist' : 'Wishlist ' . ($i + 1),
                    'description' => $i === 0 ? 'My default wishlist' : 'Custom wishlist ' . ($i + 1),
                    'is_public' => $i !== 0 && rand(0, 1) === 1, // First wishlist is private, others have 50% chance
                ]);

                // Add 1-5 random products to each wishlist
                $productCount = rand(1, 5);
                $randomProducts = $products->random($productCount);
                
                foreach ($randomProducts as $product) {
                    WishlistItem::create([
                        'wishlist_id' => $wishlist->id,
                        'product_id' => $product->id,
                        'notes' => rand(0, 1) === 1 ? 'I want this!' : null,
                        'priority' => rand(0, 10),
                    ]);
                }
            }
        }
    }
}
