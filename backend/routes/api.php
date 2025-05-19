<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Users
    Route::apiResource('users', UserController::class);

    // Roles
    Route::apiResource('roles', RoleController::class);

    // Permissions
    Route::apiResource('permissions', PermissionController::class);

    // Products
    Route::apiResource('products', ProductController::class);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Brands
    Route::apiResource('brands', BrandController::class);

    // Orders
    Route::apiResource('orders', OrderController::class);

    // Blog Posts
    Route::apiResource('blog-posts', BlogPostController::class);

    // Comments
    Route::apiResource('comments', CommentController::class);

    // Settings
    Route::apiResource('settings', SettingController::class);

    // Invoices
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/generate', [InvoiceController::class, 'generate']);

    // Wishlists
    Route::apiResource('wishlists', WishlistController::class);
    Route::post('/wishlists/{wishlist}/products', [WishlistController::class, 'addProduct']);
    Route::delete('/wishlists/{wishlist}/products/{product}', [WishlistController::class, 'removeProduct']);
    Route::put('/wishlists/{wishlist}/products/{product}', [WishlistController::class, 'updateProduct']);

    // Likes
    Route::get('/likes', [LikeController::class, 'index']);
    
    // Product Likes
    Route::post('/products/{product}/like', [LikeController::class, 'toggleProductLike']);
    Route::get('/products/{product}/likes/count', [LikeController::class, 'getProductLikesCount']);
    Route::get('/products/{product}/liked', [LikeController::class, 'checkProductLike']);
    
    // Blog Post Likes
    Route::post('/blog-posts/{blogPost}/like', [LikeController::class, 'toggleBlogPostLike']);
    Route::get('/blog-posts/{blogPost}/likes/count', [LikeController::class, 'getBlogPostLikesCount']);
    Route::get('/blog-posts/{blogPost}/liked', [LikeController::class, 'checkBlogPostLike']);
});
