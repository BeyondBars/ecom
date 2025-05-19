<?php

namespace Tests\Feature;

use App\Models\Permission;
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

    protected $user;
    protected $adminRole;
    protected $userRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        $permissions = [
            'view_wishlists',
            'create_wishlists',
            'update_wishlists',
            'delete_wishlists',
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Create roles
        $this->adminRole = Role::create(['name' => 'Admin']);
        $this->userRole = Role::create(['name' => 'User']);
        
        // Assign permissions to roles
        $this->adminRole->permissions()->attach(Permission::all());
        $this->userRole->permissions()->attach(Permission::where('name', 'like', '%wishlists')->get());
        
        // Create user
        $this->user = User::factory()->create(['role_id' => $this->userRole->id]);
    }

    /**
     * Test index endpoint.
     */
    public function test_index_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        Wishlist::factory()->count(3)->create(['user_id' => $this->user->id]);
        
        $response = $this->getJson('/api/wishlists');
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
    
    /**
     * Test store endpoint.
     */
    public function test_store_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $data = [
            'name' => 'Test Wishlist',
            'description' => 'This is a test wishlist',
            'is_public' => true,
        ];
        
        $response = $this->postJson('/api/wishlists', $data);
        
        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Test Wishlist',
                'description' => 'This is a test wishlist',
                'is_public' => true,
                'user_id' => $this->user->id,
            ]);
    }
    
    /**
     * Test show endpoint.
     */
    public function test_show_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id]);
        
        $response = $this->getJson("/api/wishlists/{$wishlist->id}");
        
        $response->assertStatus(200)
            ->assertJson([
                'id' => $wishlist->id,
                'name' => $wishlist->name,
                'user_id' => $this->user->id,
            ]);
    }
    
    /**
     * Test update endpoint.
     */
    public function test_update_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id]);
        
        $data = [
            'name' => 'Updated Wishlist',
            'description' => 'This wishlist has been updated',
            'is_public' => false,
        ];
        
        $response = $this->putJson("/api/wishlists/{$wishlist->id}", $data);
        
        $response->assertStatus(200)
            ->assertJson([
                'id' => $wishlist->id,
                'name' => 'Updated Wishlist',
                'description' => 'This wishlist has been updated',
                'is_public' => false,
            ]);
    }
    
    /**
     * Test delete endpoint.
     */
    public function test_delete_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id]);
        
        $response = $this->deleteJson("/api/wishlists/{$wishlist->id}");
        
        $response->assertStatus(204);
        $this->assertDatabaseMissing('wishlists', ['id' => $wishlist->id]);
    }
    
    /**
     * Test add product endpoint.
     */
    public function test_add_product_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();
        
        $data = [
            'product_id' => $product->id,
            'notes' => 'I want this!',
            'priority' => 5,
        ];
        
        $response = $this->postJson("/api/wishlists/{$wishlist->id}/products", $data);
        
        $response->assertStatus(201)
            ->assertJson([
                'wishlist_id' => $wishlist->id,
                'product_id' => $product->id,
                'notes' => 'I want this!',
                'priority' => 5,
            ]);
    }
    
    /**
     * Test remove product endpoint.
     */
    public function test_remove_product_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();
        
        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);
        
        $response = $this->deleteJson("/api/wishlists/{$wishlist->id}/products/{$product->id}");
        
        $response->assertStatus(204);
        $this->assertDatabaseMissing('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);
    }
    
    /**
     * Test update product endpoint.
     */
    public function test_update_product_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $wishlist = Wishlist::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create();
        
        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
            'notes' => 'Original note',
            'priority' => 1,
        ]);
        
        $data = [
            'notes' => 'Updated note',
            'priority' => 10,
        ];
        
        $response = $this->putJson("/api/wishlists/{$wishlist->id}/products/{$product->id}", $data);
        
        $response->assertStatus(200)
            ->assertJson([
                'wishlist_id' => $wishlist->id,
                'product_id' => $product->id,
                'notes' => 'Updated note',
                'priority' => 10,
            ]);
    }
}
