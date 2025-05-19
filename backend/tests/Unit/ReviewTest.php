<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_review()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Great product',
            'comment' => 'This is an amazing product!',
        ]);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Great product',
            'comment' => 'This is an amazing product!',
        ]);
    }

    public function test_review_belongs_to_user()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $review->user);
        $this->assertEquals($user->id, $review->user->id);
    }

    public function test_review_belongs_to_product()
    {
        $product = Product::factory()->create();
        $review = Review::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $review->product);
        $this->assertEquals($product->id, $review->product->id);
    }

    public function test_product_has_many_reviews()
    {
        $product = Product::factory()->create();
        $reviews = Review::factory()->count(3)->create(['product_id' => $product->id]);

        $this->assertCount(3, $product->reviews);
        $this->assertInstanceOf(Review::class, $product->reviews->first());
    }

    public function test_product_can_calculate_average_rating()
    {
        $product = Product::factory()->create();
        
        Review::factory()->create([
            'product_id' => $product->id,
            'rating' => 5,
            'approved' => true,
        ]);
        
        Review::factory()->create([
            'product_id' => $product->id,
            'rating' => 3,
            'approved' => true,
        ]);
        
        Review::factory()->create([
            'product_id' => $product->id,
            'rating' => 4,
            'approved' => true,
        ]);
        
        // This review should not be counted as it's not approved
        Review::factory()->create([
            'product_id' => $product->id,
            'rating' => 1,
            'approved' => false,
        ]);

        $this->assertEquals(4, $product->getAverageRatingAttribute());
        $this->assertEquals(3, $product->getReviewsCountAttribute());
    }

    public function test_review_scopes()
    {
        Review::factory()->count(3)->create(['approved' => true]);
        Review::factory()->count(2)->create(['approved' => false]);
        Review::factory()->count(2)->create(['featured' => true, 'approved' => true]);
        Review::factory()->count(4)->create(['verified_purchase' => true]);

        $this->assertCount(5, Review::approved()->get());
        $this->assertCount(2, Review::featured()->get());
        $this->assertCount(4, Review::verified()->get());
    }
}
