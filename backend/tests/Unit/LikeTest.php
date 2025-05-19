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
    public function test_can_like_product()
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
        
        $this->assertTrue($product->likes()->where('user_id', $user->id)->exists());
    }

    /**
     * Test liking a blog post.
     */
    public function test_can_like_blog_post()
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
        
        $this->assertTrue($blogPost->likes()->where('user_id', $user->id)->exists());
    }

    /**
     * Test unliking a product.
     */
    public function test_can_unlike_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $like = Like::create([
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        $this->assertTrue($product->likes()->where('user_id', $user->id)->exists());
        
        $like->delete();
        
        $this->assertFalse($product->likes()->where('user_id', $user->id)->exists());
        $this->assertDatabaseMissing('likes', [
            'id' => $like->id,
        ]);
    }

    /**
     * Test user relationship with likes.
     */
    public function test_user_has_likes()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $blogPost = BlogPost::factory()->create();
        
        Like::create([
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        Like::create([
            'user_id' => $user->id,
            'likeable_id' => $blogPost->id,
            'likeable_type' => BlogPost::class,
        ]);
        
        $this->assertEquals(2, $user->likes()->count());
    }

    /**
     * Test product relationship with likes.
     */
    public function test_product_has_likes()
    {
        $product = Product::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Like::create([
            'user_id' => $user1->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        Like::create([
            'user_id' => $user2->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        $this->assertEquals(2, $product->likes()->count());
    }
}
