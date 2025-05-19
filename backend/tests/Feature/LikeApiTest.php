<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Like;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LikeApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all likes.
     */
    public function test_can_get_all_likes()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $blogPost = BlogPost::factory()->create();
        
        Like::factory()->create([
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        Like::factory()->create([
            'user_id' => $user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
        
        Sanctum::actingAs($user);
        
        $response = $this->getJson('/api/likes');
        
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * Test toggling like for a product.
     */
    public function test_can_toggle_product_like()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        Sanctum::actingAs($user);
        
        // Like the product
        $response = $this->postJson("/api/products/{$product->id}/like");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => true,
                'likes_count' => 1,
            ]);
            
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        // Unlike the product
        $response = $this->postJson("/api/products/{$product->id}/like");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => false,
                'likes_count' => 0,
            ]);
            
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
    }

    /**
     * Test toggling like for a blog post.
     */
    public function test_can_toggle_blog_post_like()
    {
        $user = User::factory()->create();
        $blogPost = BlogPost::factory()->create();
        
        Sanctum::actingAs($user);
        
        // Like the blog post
        $response = $this->postJson("/api/blog-posts/{$blogPost->id}/like");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => true,
                'likes_count' => 1,
            ]);
            
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
        
        // Unlike the blog post
        $response = $this->postJson("/api/blog-posts/{$blogPost->id}/like");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => false,
                'likes_count' => 0,
            ]);
            
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
    }

    /**
     * Test checking if user has liked a product.
     */
    public function test_can_check_product_like()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        Like::create([
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        Sanctum::actingAs($user);
        
        $response = $this->getJson("/api/products/{$product->id}/like");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => true,
                'likes_count' => 1,
            ]);
    }

    /**
     * Test checking if user has liked a blog post.
     */
    public function test_can_check_blog_post_like()
    {
        $user = User::factory()->create();
        $blogPost = BlogPost::factory()->create();
        
        Like::create([
            'user_id' => $user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
        
        Sanctum::actingAs($user);
        
        $response = $this->getJson("/api/blog-posts/{$blogPost->id}/like");
        
        $response->assertStatus(200)
            ->assertJson([
                'liked' => true,
                'likes_count' => 1,
            ]);
    }
}
