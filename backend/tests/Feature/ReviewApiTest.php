<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReviewApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_reviews_for_product()
    {
        $product = Product::factory()->create();
        $reviews = Review::factory()->count(3)->create([
            'product_id' => $product->id,
            'approved' => true,
        ]);

        $response = $this->getJson("/api/products/{$product->id}/reviews");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'product_id',
                        'user_id',
                        'rating',
                        'title',
                        'comment',
                        'verified_purchase',
                        'approved',
                        'featured',
                        'created_at',
                        'updated_at',
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ],
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_can_get_product_rating_stats()
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

        $response = $this->getJson("/api/products/{$product->id}/rating-stats");

        $response->assertStatus(200)
            ->assertJson([
                'average_rating' => 4.0,
                'total_reviews' => 3,
                'rating_distribution' => [
                    '5' => 1,
                  => 3,
                'rating_distribution' => [
                    '5' => 1,
                    '4' => 1,
                    '3' => 1,
                    '2' => 0,
                    '1' => 0,
                ],
            ]);
    }

    public function test_authenticated_user_can_create_review()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/products/' . $product->id . '/reviews', [
            'product_id' => $product->id,
            'rating' => 4,
            'title' => 'Good product',
            'comment' => 'I really like this product.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'rating' => 4,
                'title' => 'Good product',
                'comment' => 'I really like this product.',
                'approved' => false,
            ]);

        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 4,
            'title' => 'Good product',
            'comment' => 'I really like this product.',
        ]);
    }

    public function test_user_cannot_review_same_product_twice()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        Review::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/products/' . $product->id . '/reviews', [
            'product_id' => $product->id,
            'rating' => 4,
            'title' => 'Good product',
            'comment' => 'I really like this product.',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'You have already reviewed this product.',
            ]);
    }

    public function test_admin_can_approve_review()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(1); // Assuming role_id 1 is admin
        
        $review = Review::factory()->create(['approved' => false]);

        Sanctum::actingAs($admin);

        $response = $this->patchJson('/api/reviews/' . $review->id . '/approve');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $review->id,
                'approved' => true,
            ]);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'approved' => true,
        ]);
    }

    public function test_admin_can_feature_review()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(1); // Assuming role_id 1 is admin
        
        $review = Review::factory()->create(['featured' => false]);

        Sanctum::actingAs($admin);

        $response = $this->patchJson('/api/reviews/' . $review->id . '/feature');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $review->id,
                'featured' => true,
            ]);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'featured' => true,
        ]);
    }

    public function test_user_can_update_own_review()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/reviews/' . $review->id, [
            'rating' => 3,
            'title' => 'Updated title',
            'comment' => 'Updated comment',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $review->id,
                'rating' => 3,
                'title' => 'Updated title',
                'comment' => 'Updated comment',
            ]);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 3,
            'title' => 'Updated title',
            'comment' => 'Updated comment',
        ]);
    }

    public function test_user_cannot_update_others_review()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user1->id]);

        Sanctum::actingAs($user2);

        $response = $this->putJson('/api/reviews/' . $review->id, [
            'rating' => 3,
            'title' => 'Updated title',
            'comment' => 'Updated comment',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_any_review()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->roles()->attach(1); // Assuming role_id 1 is admin
        
        $review = Review::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($admin);

        $response = $this->putJson('/api/reviews/' . $review->id, [
            'rating' => 3,
            'title' => 'Admin updated title',
            'comment' => 'Admin updated comment',
            'approved' => true,
            'featured' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $review->id,
                'rating' => 3,
                'title' => 'Admin updated title',
                'comment' => 'Admin updated comment',
                'approved' => true,
                'featured' => true,
            ]);
    }

    public function test_user_can_delete_own_review()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/reviews/' . $review->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_admin_can_delete_any_review()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->roles()->attach(1); // Assuming role_id 1 is admin
        
        $review = Review::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($admin);

        $response = $this->deleteJson('/api/reviews/' . $review->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}
