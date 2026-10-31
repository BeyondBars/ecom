<?php

namespace Tests\Feature\Shop;

use App\Models\BlogPost;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Notifications\Shop\BackInStockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EngagementTest extends TestCase
{
    use RefreshDatabase;

    public function testReviewsArePendingUntilApprovedAndUpdateTheRating()
    {
        $product = Product::factory()->create();
        $customer = $this->createStandardUser(['first_name' => 'Priya', 'last_name' => 'Sharma']);
        $order = Order::factory()->status('delivered')->create(['user_id' => $customer->id]);
        $order->items()->create(['product_id' => $product->id, 'name' => 'x', 'price' => 1, 'quantity' => 1, 'total' => 1]);

        $review = $this->be($customer)->postJson("shop/products/{$product->slug}/reviews", ['rating' => 4, 'comment' => 'Lovely colour, runs large.'])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.verified_purchase', true)
            ->json('data');

        $this->postJson("shop/products/{$product->slug}/reviews", ['rating' => 5, 'comment' => 'Second review attempt'])
            ->assertJsonValidationErrors(['comment']);

        $this->getJson("shop/products/{$product->slug}/reviews")->assertJsonCount(0, 'data');

        $this->beAdmin()->putJson("admin/reviews/{$review['id']}", ['status' => 'approved'])->assertSuccessful();

        $this->getJson("shop/products/{$product->slug}/reviews")
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('summary.count', 1)
            ->assertJsonPath('summary.breakdown.4', 1);

        $this->assertEquals(4.0, (float) $product->fresh()->rating);
    }

    public function testPublicReviewsHideCustomerDetails()
    {
        $review = Review::factory()->create();
        $this->getJson("shop/products/{$review->product->slug}/reviews")
            ->assertJsonMissingPath('data.0.author_email');
    }

    public function testWishlistIsPerCustomer()
    {
        $product = Product::factory()->create();
        $hidden = Product::factory()->draft()->create();
        $this->beUser();

        $this->postJson('shop/wishlist', ['product_id' => $product->id])->assertNoContent();
        $this->postJson('shop/wishlist', ['product_id' => $product->id])->assertNoContent(); // no duplicates
        $this->postJson('shop/wishlist', ['product_id' => $hidden->id])->assertNotFound();
        $this->getJson('shop/wishlist')->assertJsonCount(1, 'data');

        $this->deleteJson("shop/wishlist/{$product->id}")->assertNoContent();
        $this->getJson('shop/wishlist')->assertJsonCount(0, 'data');
    }

    public function testBackInStockAlertsAreSentOnce()
    {
        Notification::fake();
        $product = Product::factory()->outOfStock()->create();

        $this->postJson("shop/products/{$product->slug}/stock-alerts", ['email' => 'Shopper@Example.com'])->assertCreated();
        $this->postJson("shop/products/{$product->slug}/stock-alerts", ['email' => 'shopper@example.com'])->assertCreated();

        $product->update(['stock' => 5]);
        $product->update(['stock' => 0]);
        $product->update(['stock' => 3]);

        Notification::assertSentTimes(BackInStockNotification::class, 1);
        Notification::assertSentTo(new AnonymousNotifiable, BackInStockNotification::class,
            fn ($n, $c, $notifiable) => $notifiable->routes['mail'] === 'shopper@example.com');
    }

    public function testNewsletterAndContactForms()
    {
        $this->postJson('shop/newsletter', ['email' => 'fan@example.com'])->assertCreated();
        $this->postJson('shop/newsletter', ['email' => 'fan@example.com'])->assertCreated();
        $this->postJson('shop/contact', ['name' => 'A', 'email' => 'a@example.com', 'subject' => 'Size', 'message' => 'Do you have XL?'])
            ->assertCreated();
        $this->postJson('shop/contact', ['name' => 'A', 'email' => 'bad', 'subject' => 'Size', 'message' => 'short'])
            ->assertJsonValidationErrors(['email', 'message']);

        $this->beAdmin();
        $this->getJson('admin/subscribers')->assertJsonCount(1, 'data');
        $this->getJson('admin/messages')->assertJsonCount(1, 'data');
        $this->get('admin/subscribers/export')->assertSuccessful()->assertDownload('newsletter-subscribers.csv');
    }

    public function testOnlyPublishedPostsAndApprovedCommentsArePublic()
    {
        $post = BlogPost::factory()->create();
        $draft = BlogPost::factory()->draft()->create();
        $scheduled = BlogPost::factory()->create(['published_at' => now()->addDay()]);

        $this->getJson('shop/blog')->assertJsonCount(1, 'data');
        $this->getJson("shop/blog/{$draft->slug}")->assertNotFound();
        $this->getJson("shop/blog/{$scheduled->slug}")->assertNotFound();

        $comment = $this->beUser()->postJson("shop/blog/{$post->slug}/comments", ['content' => 'Love this look!'])
            ->assertCreated()->json('data');
        $this->getJson("shop/blog/{$post->slug}/comments")->assertJsonCount(0, 'data');

        $this->beAdmin()->putJson("admin/blog-comments/{$comment['id']}", ['status' => 'approved'])->assertSuccessful();
        $this->getJson("shop/blog/{$post->slug}/comments")->assertJsonCount(1, 'data');
    }

    public function testAdminWritesBlogPosts()
    {
        $admin = $this->createAdminUser(['first_name' => 'Anju', 'last_name' => 'S']);

        $this->be($admin)->postJson('admin/blog-posts', ['title' => 'Monsoon Edit', 'content' => 'Our picks...', 'status' => 'published'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'monsoon-edit')
            ->assertJsonPath('data.author', 'Anju S');

        $this->assertNotNull(BlogPost::first()->published_at);
    }

    public function testSettingsDriveShippingAndArePublic()
    {
        $this->beAdmin()->putJson('admin/settings', ['free_shipping_threshold' => 1499, 'support_phone' => '+91 98765 43210'])
            ->assertJsonPath('data.free_shipping_threshold', 1499);

        $this->getJson('shop/settings')
            ->assertJsonPath('data.free_shipping_threshold', 1499)
            ->assertJsonPath('data.support_phone', '+91 98765 43210')
            ->assertJsonMissingPath('data.razorpay_key_secret');

        $product = Product::factory()->withoutOptions()->create(['price' => 1200]);
        $this->postJson('shop/cart/quote', ['items' => [['product_id' => $product->id, 'quantity' => 1]]])
            ->assertJsonPath('data.shipping', 99);
    }

    public function testOnlySuperAdminsChangeSettings()
    {
        $this->be($this->createUserWithRole(\App\Support\Enums\UserRole::Staff))->getJson('admin/settings')->assertForbidden();
    }

    public function testDashboardSummarisesSales()
    {
        Order::factory()->create(['total' => 1500]);
        Order::factory()->create(['total' => 999, 'status' => 'cancelled']);
        Order::factory()->create(['total' => 5000, 'payment_method' => 'razorpay']); // never paid
        Review::factory()->pending()->create();

        $this->beAdmin()->getJson('admin/dashboard?days=7')
            ->assertSuccessful()
            ->assertJsonPath('data.revenue', 1500)
            ->assertJsonPath('data.orders', 1)
            ->assertJsonPath('data.to_do.to_ship', 1)
            ->assertJsonPath('data.to_do.reviews', 1)
            ->assertJsonCount(7, 'data.series');

        $this->beUser()->getJson('admin/dashboard')->assertForbidden();
    }
}
