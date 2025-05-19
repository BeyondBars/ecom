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
    }
    
    /**
     * Test retrieving likes for a product.
     */
    public function test_can_retrieve_likes_for_product(): void
    {
        $users = User::factory()->count(3)->create();
        $product = Product::factory()->create();
        
        foreach ($users as $user) {
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $product->id,
                'likeable_type' => Product::class,
            ]);
        }
        
        $productLikes = $product->likes;
        
        $this->assertCount(3, $productLikes);
    }
    
    /**
     * Test retrieving likes for a blog post.
     */
    public function test_can_retrieve_likes_for_blog_post(): void
    {
        $users = User::factory()->count(3)->create();
        $blogPost = BlogPost::factory()->create();
        
        foreach ($users as $user) {
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $blogPost->id,
                'likeable_type' => BlogPost::class,
            ]);
        }
        
        $blogPostLikes = $blogPost->likes;
        
        $this->assertCount(3, $blogPostLikes);
    }
    
    /**
     * Test retrieving likes for a user.
     */
    public function test_can_retrieve_likes_for_user(): void
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(2)->create();
        $blogPosts = BlogPost::factory()->count(2)->create();
        
        foreach ($products as $product) {
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $product->id,
                'likeable_type' => Product::class,
            ]);
        }
        
        foreach ($blogPosts as $blogPost) {
            Like::create([
                'user_id' => $user->id,
                'likeable_id' => $blogPost->id,
                'likeable_type' => BlogPost::class,
            ]);
        }
        
        $userLikes = $user->likes;
        
        $this->assertCount(4, $userLikes);
    }
    
    /**
     * Test deleting a like.
     */
    public function test_can_delete_like(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $like = Like::create([
            'user_id' => $user->id,
            'likeable_id' => $product->id,
            'likeable_type' => Product::class,
        ]);
        
        $this->assertDatabaseHas('likes', ['id' => $like->id]);
        
        $like->delete();
        
        $this->assertDatabaseMissing('likes', ['id' => $like->id]);
    }
}
