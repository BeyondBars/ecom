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
});
