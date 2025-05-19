<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a wishlist.
     */
    public function test_can_create_wishlist()
    {
        $user = User::factory()->create();
        
        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'Test Wishlist',
            'is_public' => true,
            'description' => 'This is a test wishlist',
        ]);
        
        $this->assertDatabaseHas('wishlists', [
            'id' => $wishlist->id,
            'user_id' => $user->id,
            'name' => 'Test Wishlist',
            'is_public' => true,
            'description' => 'This is a test wishlist',
        ]);
    }

    /**
     * Test adding a product to a wishlist.
     */
    public function test_can_add_product_to_wishlist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'Test Wishlist',
        ]);
        
        $wishlist->products()->attach($product->id, [
            'notes' => 'Test notes',
        ]);
        
        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
            'notes' => 'Test notes',
        ]);
        
        $this->assertTrue($wishlist->products->contains($product->id));
    }

    /**
     * Test removing a product from a wishlist.
     */
    public function test_can_remove_product_from_wishlist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'Test Wishlist',
        ]);
        
        $wishlist->products()->attach($product->id);
        $this->assertTrue($wishlist->products->contains($product->id));
        
        $wishlist->products()->detach($product->id);
        $wishlist->refresh();
        
        $this->assertFalse($wishlist->products->contains($product->id));
        $this->assertDatabaseMissing('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);
    }

    /**
     * Test user relationship with wishlists.
     */
    public function test_user_has_wishlists()
    {
        $user = User::factory()->create();
        
        Wishlist::create([
            'user_id' => $user->id,
            'name' => 'Wishlist 1',
        ]);
        
        Wishlist::create([
            'user_id' => $user->id,
            'name' => 'Wishlist 2',
        ]);
        
        $this->assertEquals(2, $user->wishlists()->count());
    }

    /**
     * Test product relationship with wishlists.
     */
    public function test_product_has_wishlists()
    {
        $product = Product::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $wishlist1 = Wishlist::create([
            'user_id' => $user1->id,
            'name' => 'Wishlist 1',
        ]);
        
        $wishlist2 = Wishlist::create([
            'user_id' => $user2->id,
            'name' => 'Wishlist 2',
        ]);
        
        $wishlist1->products()->attach($product->id);
        $wishlist2->products()->attach($product->id);
        
        $this->assertEquals(2, $product->wishlists()->count());
    }
}
