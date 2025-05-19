<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WishlistApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all wishlists.
     */
    public function test_can_get_all_wishlists()
    {
        $user = User::factory()->create();
        Wishlist::factory()->count(3)->create(['user_id' => $user->id]);
        
        Sanctum::actingAs($user);
        
        $response = $this->getJson('/api/wishlists');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test creating a wishlist.
     */
    public function test_can_create_wishlist()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $response = $this->postJson('/api/wishlists', [
            'name' => 'Test Wishlist',
            'is_public' => true,
            'description' => 'This is a test wishlist',
        ]);
        
        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Test Wishlist',
                'is_public' => true,
                'description' => 'This is a test wishlist',
            ]);
            
        $this->assertDatabaseHas('wishlists', [
            'name' => 'Test Wishlist',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test getting a specific wishlist.
     */
    public function test_can_get_specific_wishlist()
    {
        $user = User::factory()->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        
        Sanctum::actingAs($user);
        
        $response = $this->getJson("/api/wishlists/{$wishlist->id}");
        
        $response->assertStatus(200)
            ->assertJson([
                'id' => $wishlist->id,
                'name' => $wishlist->name,
            ]);
    }

    /**
     * Test updating a wishlist.
     */
    public function test_can_update_wishlist()
    {
        $user = User::factory()->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        
        Sanctum::actingAs($user);
        
        $response = $this->putJson("/api/wishlists/{$wishlist->id}", [
            'name' => 'Updated Wishlist',
            'is_public' => false,
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'id' => $wishlist->id,
                'name' => 'Updated Wishlist',
                'is_public' => false,
            ]);
            
        $this->assertDatabaseHas('wishlists', [
            'id' => $wishlist->id,
            'name' => 'Updated Wishlist',
            'is_public' => false,
        ]);
    }

    /**
     * Test deleting a wishlist.
     */
    public function test_can_delete_wishlist()
    {
        $user = User::factory()->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        
        Sanctum::actingAs($user);
        
        $response = $this->deleteJson("/api/wishlists/{$wishlist->id}");
        
        $response->assertStatus(204);
        
        $this->assertDatabaseMissing('wishlists', [
            'id' => $wishlist->id,
        ]);
    }

    /**
     * Test adding a product to a wishlist.
     */
    public function test_can_add_product_to_wishlist()
    {
        $user = User::factory()->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        
        Sanctum::actingAs($user);
        
        $response = $this->postJson("/api/wishlists/{$wishlist->id}/products", [
            'product_id' => $product->id,
            'notes' => 'Test notes',
        ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
            'notes' => 'Test notes',
        ]);
    }

    /**
     * Test removing a product from a wishlist.
     */
    public function test_can_remove_product_from_wishlist()
    {
        $user = User::factory()->create();
        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        
        $wishlist->products()->attach($product->id, [
            'notes' => 'Test notes',
        ]);
        
        Sanctum::actingAs($user);
        
        $response = $this->deleteJson("/api/wishlists/{$wishlist->id}/products", [
            'product_id' => $product->id,
        ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);
    }
}
