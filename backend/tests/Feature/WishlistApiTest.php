<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WishlistApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin role with all permissions
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        
        // Create admin user
        $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        
        // Create regular user
        $this->user = User::factory()->create();
        
        // Create products
        $this->products = Product::factory()->count(5)->create();
    }

    /**
     * Test admin can view all wishlists.
     */
    public function test_admin_can_view_all_wishlists(): void
    {
        Sanctum::actingAs($this->admin);
        
        // Create wishlists for different users
        Wishlist::factory()->count(3)->create();
        
        $response = $this->getJson('/api/wishlists');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'total',
            ]);
        
        $this->assertEquals(3, $response->json('total'));
    }
    
    /**
     * Test user can create wishlist.
     */
    public function test_user_can_create_wishlist(): void
    {
        Sanctum::actingAs($this->user);
        
        $wishlistData = [
            'user_id' => $this->user->id,
            'name' => 'My Test Wishlist',
            'description' => 'This is a test wishlist',
            'is_public' => false,
            'product_ids' => [$this->products[0]->id, $this->products[1]->id],
        ];
        
        $response = $this->post  => [$this->products[0]->id, $this->products[1]->id],
        ];
        
        $response = $this->postJson('/api/wishlists', $wishlistData);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'name',
                'description',
                'is_public',
                'created_at',
                'updated_at',
            ]);
        
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'name' => 'My Test Wishlist',
            'description' => 'This is a test wishlist',
            'is_public' => false,
        ]);
        
        // Check if products were added to wishlist
        $wishlistId = $response->json('id');
        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlistId,
            'product_id' => $this->products[0]->id,
        ]);
        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlistId,
            'product_id' => $this->products[1]->id,
        ]);
    }
    
    /**
     * Test user can add product to wishlist.
     */
    public function test_user_can_add_product_to_wishlist(): void
    {
        Sanctum::actingAs($this->user);
        
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id]);
        
        $productData = [
            'product_id' => $this->products[0]->id,
            'notes' => 'I want this for my birthday',
            'priority' => 8,
        ];
        
        $response = $this->postJson("/api/wishlists/{$wishlist->id}/products", $productData);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'wishlist_id',
                'product_id',
                'notes',
                'priority',
                'created_at',
                'updated_at',
            ]);
        
        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $this->products[0]->id,
            'notes' => 'I want this for my birthday',
            'priority' => 8,
        ]);
    }
    
    /**
     * Test user can remove product from wishlist.
     */
    public function test_user_can_remove_product_from_wishlist(): void
    {
        Sanctum::actingAs($this->user);
        
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id]);
        $wishlistItem = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $this->products[0]->id,
        ]);
        
        $response = $this->deleteJson("/api/wishlists/{$wishlist->id}/products/{$this->products[0]->id}");
        
        $response->assertStatus(204);
        
        $this->assertDatabaseMissing('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $this->products[0]->id,
        ]);
    }
    
    /**
     * Test user can update product in wishlist.
     */
    public function test_user_can_update_product_in_wishlist(): void
    {
        Sanctum::actingAs($this->user);
        
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id]);
        $wishlistItem = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $this->products[0]->id,
            'notes' => 'Original note',
            'priority' => 3,
        ]);
        
        $updateData = [
            'notes' => 'Updated note',
            'priority' => 9,
        ];
        
        $response = $this->putJson("/api/wishlists/{$wishlist->id}/products/{$this->products[0]->id}", $updateData);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'wishlist_id',
                'product_id',
                'notes',
                'priority',
                'created_at',
                'updated_at',
            ]);
        
        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $this->products[0]->id,
            'notes' => 'Updated note',
            'priority' => 9,
        ]);
    }
}
