<?php

namespace Tests\Unit;

use App\Models\BlogPost;
use App\Models\Like;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test liking a product.
     */
    public function test_can_like_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $like = Like::create([
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        $this->assertDatabaseHas('likes', [
            'id' => $like->id,
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        // Test relationship
        $this->assertTrue($product->likes->contains($like->id));
        $this->assertEquals(1, $product->likes->count());
    }
    
    /**
     * Test liking a blog post.
     */
    public function test_can_like_blog_post(): void
    {
        $user = User::factory()->create();
        $blogPost = BlogPost::factory()->create();
        
        $like = Like::create([
            'user_id' => $user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
        
        $this->assertDatabaseHas('likes', [
            'id' => $like->id,
            'user_id' => $user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
        
        // Test relationship
        $this->assertTrue($blogPost->likes->contains($like->id));
        $this->assertEquals(1, $blogPost->likes->count());
    }
    
    /**
     * Test user-like relationship.
     */
    public function test_user_like_relationship(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $like = Like::create([
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        $this->assertTrue($user->likes->contains($like->id));
        $this->assertEquals($user->id, $like->user->id);
    }
    
    /**
     * Test unliking a product.
     */
    public function test_can_unlike_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $like = Like::create([
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        $like->delete();
        
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
    }
}
