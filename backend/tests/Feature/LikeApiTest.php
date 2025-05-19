<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Like;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LikeApiTest extends TestCase
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
        
        // Create product and blog post
        $this->product = Product::factory()->create();
        $this->blogPost = BlogPost::factory()->create();
    }

    /**
     * Test admin can view all likes.
     */
    public function test_admin_can_view_all_likes(): void
    {
        Sanctum::actingAs($this->admin);
        
        // Create likes
        Like::factory()->count(3)->create();
        
        $response = $this->getJson('/api/likes');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'total',
            ]);
        
        $this->assertEquals(3, $response->json('total'));
    }
    
    /**
     * Test user can like a product.
     */
    public function test_user_can_like_product(): void
    {
        Sanctum::actingAs($this->user);
        
        $response = $this->postJson("/api/products/{$this->product->id}/like");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => true,
            ]);
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $this->product->id,
            'likeable_type' => Product::class,
        ]);
    }
    
    /**
     * Test user can unlike a product.
     */
    public function test_user_can_unlike_product(): void
    {
        Sanctum::actingAs($this->user);
        
        // First like the product
        Like::create([
            'user_id' => $this->user->id,
            'likeable_id' => $this->product->id,
            'likeable_type' => Product::class,
        ]);
        
        // Then unlike it
        $response = $this->postJson("/api/products/{$this->product->id}/like");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => false,
            ]);
        
        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $this->product->id,
            'likeable_type' => Product::class,
        ]);
    }
    
    /**
     * Test user can like a blog post.
     */
    public function test_user_can_like_blog_post(): void
    {
        Sanctum::actingAs($this->user);
        
        $response = $this->postJson("/api/blog-posts/{$this->blogPost->id}/like");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => true,
            ]);
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $this->blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
    }
    
    /**
     * Test getting product likes count.
     */
    public function test_can_get_product_likes_count(): void
    {
        Sanctum::actingAs($this->user);
        
        // Create 3 likes for the product
        Like::factory()->count(3)->create([
            'likeable_id' => $this->product->id,
            'likeable_type' => Product::class,
        ]);
        
        $response = $this->getJson("/api/products/{$this->product->id}/likes/count");
        
        $response->assertStatus(200)
            ->assertJson([
                'count' => 3,
            ]);
    }
    
    /**
     * Test checking if user liked a product.
     */
    public function test_can_check_if_user_liked_product(): void
    {
        Sanctum::actingAs($this->user);
        
        // User hasn't liked the product yet
        $response = $this->getJson("/api/products/{$this->product->id}/liked");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => false,
            ]);
        
        // User likes the product
        Like::create([
            'user_id' => $this->user->id,
            'likeable_id' => $this->product->id,
            'likeable_type' => Product::class,
        ]);
        
        $response = $this->getJson("/api/products/{$this->product->id}/liked");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => true,
            ]);
    }
}
