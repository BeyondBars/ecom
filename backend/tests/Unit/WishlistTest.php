<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a wishlist.
     */
    public function test_can_create_wishlist(): void
    {
        $user = User::factory()->create();
        
        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'Test Wishlist',
            'description' => 'This is a test wishlist',
            'is_public' => true,
        ]);
        
        $this->assertDatabaseHas('wishlists', [
            'id' => $wishlist->id,
            'user_id' => $user->id,
            'name' => 'Test Wishlist',
            'description' => 'This is a test wishlist',
            'is_public' => true,
        ]);
    }
    
    /**
     * Test adding a product to a wishlist.
     */
    public function test_can_add_product_to_wishlist(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        
        $wishlistItem = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
            'notes' => 'I want this!',
            'priority' => 5,
        ]);
        
        $this->assertDatabaseHas('wishlist_items', [
            'id' => $wishlistItem->id,
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
            'notes' => 'I want this!',
            'priority' => 5,
        ]);
    }
    
    /**
     * Test retrieving products from a wishlist.
     */
    public function test_can_retrieve_products_from_wishlist(): void
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(3)->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        
        foreach ($products as $product) {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $product->id,
            ]);
        }
        
        $wishlistProducts = $wishlist->products;
        
        $this->assertCount(3, $wishlistProducts);
        $this->assertTrue($wishlistProducts->contains($products[0]));
        $this->assertTrue($wishlistProducts->contains($products[1]));
        $this->assertTrue($wishlistProducts->contains($products[2]));
    }
    
    /**
     * Test retrieving wishlists for a user.
     */
    public function test_can_retrieve_wishlists_for_user(): void
    {
        $user = User::factory()->create();
        Wishlist::factory()->count(3)->create(['user_id' => $user->id]);
        
        $userWishlists = $user->wishlists;
        
        $this->assertCount(3, $userWishlists);
    }
    
    /**
     * Test deleting a wishlist.
     */
    public function test_deleting_wishlist_deletes_items(): void
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(3)->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        
        foreach ($products as $product) {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $product->id,
            ]);
        }
        
        $this->assertDatabaseCount('wishlist_items', 3);
        
        $wishlist->delete();
        
        $this->assertDatabaseCount('wishlist_items', 0);
        $this->assertDatabaseMissing('wishlists', ['id' => $wishlist->id]);
    }
}
