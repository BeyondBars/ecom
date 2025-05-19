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
     * Test wishlist creation.
     */
    public function test_can_create_wishlist(): void
    {
        $user = User::factory()->create();
        
        $wishlist = Wishlist::factory()->create([
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
     * Test adding products to wishlist.
     */
    public function test_can_add_products_to_wishlist(): void
    {
        $user = User::factory()->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        
        $wishlistItem = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
            'notes' => 'Test notes',
            'priority' => 5,
        ]);
        
        $this->assertDatabaseHas('wishlist_items', [
            'id' => $wishlistItem->id,
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
            'notes' => 'Test notes',
            'priority' => 5,
        ]);
        
        // Test relationship
        $this->assertTrue($wishlist->products->contains($product->id));
        $this->assertEquals(1, $wishlist->products->count());
    }
    
    /**
     * Test user-wishlist relationship.
     */
    public function test_user_wishlist_relationship(): void
    {
        $user = User::factory()->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        
        $this->assertTrue($user->wishlists->contains($wishlist->id));
        $this->assertEquals($user->id, $wishlist->user->id);
    }
    
    /**
     * Test removing products from wishlist.
     */
    public function test_can_remove_products_from_wishlist(): void
    {
        $user = User::factory()->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        
        $wishlistItem = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);
        
        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);
        
        $wishlistItem->delete();
        
        $this->assertDatabaseMissing('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);
    }
}
