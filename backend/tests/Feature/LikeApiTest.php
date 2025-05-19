<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Like;
use App\Models\Permission;
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

    protected $user;
    protected $adminRole;
    protected $userRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        $permissions = [
            'view_likes',
            'create_likes',
            'delete_likes',
            'view_products',
            'view_blog_posts',
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Create roles
        $this->adminRole = Role::create(['name' => 'Admin']);
        $this->userRole = Role::create(['name' => 'User']);
        
        // Assign permissions to roles
        $this->adminRole->permissions()->attach(Permission::all());
        $this->userRole->permissions()->attach(Permission::whereIn('name', [
            'view_likes', 'create_likes', 'delete_likes', 'view_products', 'view_blog_posts'
        ])->get());
        
        // Create user
        $this->user = User::factory()->create(['role_id' => $this->userRole->id]);
    }

    /**
     * Test index endpoint.
     */
    public function test_index_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $product = Product::factory()->create();
        $blogPost = BlogPost::factory()->create();
        
        Like::create([
            'user_id' => $this->user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        Like::create([
            'user_id' => $this->user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
        
        $response = $this->getJson('/api/likes');
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
    
    /**
     * Test toggle product like endpoint.
     */
    public function test_toggle_product_like_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $product = Product::factory()->create();
        
        // Like the product
        $response = $this->postJson("/api/products/{$product->id}/like");
        
        $response->assertStatus(200)
            ->assertJson(['liked' => true]);
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        // Unlike the product
        $response = $this->postJson("/api/products/{$product->id}/like");
        
        $response->assertStatus(200)
            ->assertJson(['liked' => false]);
        
        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
    }
    
    /**
     * Test toggle blog post like endpoint.
     */
    public function test_toggle_blog_post_like_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $blogPost = BlogPost::factory()->create();
        
        // Like the blog post
        $response = $this->postJson("/api/blog-posts/{$blogPost->id}/like");
        
        $response->assertStatus(200)
            ->assertJson(['liked' => true]);
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
        
        // Unlike the blog post
        $response = $this->postJson("/api/blog-posts/{$blogPost->id}/like");
        
        $response->assertStatus(200)
            ->assertJson(['liked' => false]);
        
        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
    }
    
    /**
     * Test get product likes count endpoint.
     */
    public function test_get_product_likes_count_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $product = Product::factory()->create();
        $users = User::factory()->count(3)->create(['role_id' => $this->userRole->id]);
        
        foreach ($users as $user) {
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $product->id,
                'likeable_type' => Product::class,
            ]);
        }
        
        $response = $this->getJson("/api/products/{$product->id}/likes/count");
        
        $response->assertStatus(200)
            ->assertJson(['count' => 3]);
    }
    
    /**
     * Test get blog post likes count endpoint.
     */
    public function test_get_blog_post_likes_count_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $blogPost = BlogPost::factory()->create();
        $users = User::factory()->count(3)->create(['role_id' => $this->userRole->id]);
        
        foreach ($users as $user) {
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $blogPost->id,
                'likeable_type' => BlogPost::class,
            ]);
        }
        
        $response = $this->getJson("/api/blog-posts/{$blogPost->id}/likes/count");
        
        $response->assertStatus(200)
            ->assertJson(['count' => 3]);
    }
    
    /**
     * Test check product like endpoint.
     */
    public function test_check_product_like_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $product = Product::factory()->create();
        
        // User hasn't liked the product yet
        $response = $this->getJson("/api/products/{$product->id}/liked");
        
        $response->assertStatus(200)
            ->assertJson(['liked' => false]);
        
        // User likes the product
        Like::create([
            'user_id' => $this->user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        $response = $this->getJson("/api/products/{$product->id}/liked");
        
        $response->assertStatus(200)
            ->assertJson(['liked' => true]);
    }
    
    /**
     * Test check blog post like endpoint.
     */
    public function test_check_blog_post_like_endpoint(): void
    {
        Sanctum::actingAs($this->user);
        
        $blogPost = BlogPost::factory()->create();
        
        // User hasn't liked the blog post yet
        $response = $this->getJson("/api/blog-posts/{$blogPost->id}/liked");
        
        $response->assertStatus(200)
            ->assertJson(['liked' => false]);
        
        // User likes the blog post
        Like::create([
            'user_id' => $this->user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
        
        $response = $this->getJson("/api/blog-posts/{$blogPost->id}/liked");
        
        $response->assertStatus(200)
            ->assertJson(['liked' => true]);
    }
}
