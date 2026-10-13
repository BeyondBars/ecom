<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Shop;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront: public
|--------------------------------------------------------------------------
*/

Route::get('/shop/products', [Shop\ProductController::class, 'index']);
Route::get('/shop/products/{slug}', [Shop\ProductController::class, 'show']);
Route::get('/shop/products/{slug}/related', [Shop\ProductController::class, 'related']);
Route::get('/shop/categories', [Shop\CategoryController::class, 'index']);
Route::get('/shop/categories/{slug}', [Shop\CategoryController::class, 'show']);
Route::get('/shop/brands', [Shop\BrandController::class, 'index']);
Route::get('/shop/brands/{slug}', [Shop\BrandController::class, 'show']);
Route::get('/shop/banners', [Shop\BannerController::class, 'index']);

Route::get('/shop/products/{slug}/reviews', [Shop\ReviewController::class, 'index']);
Route::get('/shop/blog', [Shop\BlogController::class, 'index']);
Route::get('/shop/blog/{slug}', [Shop\BlogController::class, 'show']);
Route::get('/shop/blog/{slug}/comments', [Shop\BlogController::class, 'comments']);
Route::get('/shop/settings', [Shop\SettingsController::class, 'show']);

Route::middleware('throttle:shop-forms')->group(function () {
    Route::post('/shop/newsletter', [Shop\EngagementController::class, 'subscribe']);
    Route::post('/shop/contact', [Shop\EngagementController::class, 'contact']);
    Route::post('/shop/products/{slug}/stock-alerts', [Shop\EngagementController::class, 'stockAlert']);
});

// Live cart prices (works for guests; coupon per-customer limits apply when signed in)
Route::post('/shop/cart/quote', [Shop\CartController::class, 'quote']);

// Razorpay server-to-server webhook (signature checked against RAZORPAY_WEBHOOK_SECRET)
Route::post('/shop/payments/razorpay/webhook', [Shop\PaymentController::class, 'razorpayWebhook']);

/*
|--------------------------------------------------------------------------
| Storefront: signed-in customers
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', '2fa'])->prefix('shop')->group(function () {
    Route::get('addresses', [Shop\AddressController::class, 'index']);
    Route::post('addresses', [Shop\AddressController::class, 'store']);
    Route::put('addresses/{address}', [Shop\AddressController::class, 'update']);
    Route::delete('addresses/{address}', [Shop\AddressController::class, 'destroy']);

    Route::post('checkout', [Shop\CheckoutController::class, 'store'])->middleware('throttle:checkout');

    Route::get('orders', [Shop\OrderController::class, 'index']);
    Route::get('orders/{order}', [Shop\OrderController::class, 'show']);
    Route::post('orders/{order}/cancel', [Shop\OrderController::class, 'cancel']);
    Route::post('orders/{order}/return', [Shop\OrderController::class, 'requestReturn']);
    Route::post('orders/{order}/payments/razorpay', [Shop\PaymentController::class, 'razorpay']);

    Route::post('products/{slug}/reviews', [Shop\ReviewController::class, 'store'])->middleware('throttle:shop-forms');
    Route::post('blog/{slug}/comments', [Shop\BlogController::class, 'storeComment'])->middleware('throttle:shop-forms');

    Route::get('wishlist', [Shop\WishlistController::class, 'index']);
    Route::post('wishlist', [Shop\WishlistController::class, 'store']);
    Route::delete('wishlist/{product}', [Shop\WishlistController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Admin back office
|--------------------------------------------------------------------------
| Each controller authorises against the shop abilities (see UserAbility).
*/

Route::middleware(['auth:sanctum', '2fa'])->prefix('admin')->name('admin.')->group(function () {
    Route::apiResource('categories', Admin\CategoryController::class);
    Route::apiResource('brands', Admin\BrandController::class);
    Route::apiResource('products', Admin\ProductController::class);
    Route::apiResource('banners', Admin\BannerController::class);
    Route::post('uploads', [Admin\UploadController::class, 'store']);

    Route::apiResource('coupons', Admin\CouponController::class);

    Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}/status', [Admin\OrderController::class, 'updateStatus']);
    Route::post('orders/{order}/mark-paid', [Admin\OrderController::class, 'markPaid']);
    Route::post('orders/{order}/return', [Admin\OrderController::class, 'resolveReturn']);
    Route::post('orders/{order}/refunded', [Admin\OrderController::class, 'markRefunded']);
    Route::post('orders/{order}/notes', [Admin\OrderController::class, 'addNote']);

    Route::get('dashboard', Admin\DashboardController::class);

    Route::get('reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::put('reviews/{review}', [Admin\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::apiResource('blog-posts', Admin\BlogPostController::class)->parameters(['blog-posts' => 'blogPost']);
    Route::get('blog-comments', [Admin\BlogCommentController::class, 'index'])->name('blog-comments.index');
    Route::put('blog-comments/{comment}', [Admin\BlogCommentController::class, 'update'])->name('blog-comments.update');
    Route::delete('blog-comments/{comment}', [Admin\BlogCommentController::class, 'destroy'])->name('blog-comments.destroy');

    Route::get('messages', [Admin\InboxController::class, 'messages']);
    Route::put('messages/{message}', [Admin\InboxController::class, 'updateMessage']);
    Route::delete('messages/{message}', [Admin\InboxController::class, 'destroyMessage']);
    Route::get('subscribers', [Admin\InboxController::class, 'subscribers']);
    Route::get('subscribers/export', [Admin\InboxController::class, 'exportSubscribers']);
    Route::delete('subscribers/{subscriber}', [Admin\InboxController::class, 'destroySubscriber']);
    Route::get('stock-alerts', [Admin\InboxController::class, 'stockAlerts']);

    Route::get('settings', [Admin\SettingsController::class, 'show']);
    Route::put('settings', [Admin\SettingsController::class, 'update']);
});
