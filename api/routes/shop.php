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
});
