<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
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

// Public routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/reviews', [ReviewController::class, 'productReviews']);
Route::get('/products/{product}/rating-stats', [ReviewController::class, 'productRatingStats']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/{brand}', [BrandController::class, 'show']);
Route::get('/blog-posts', [BlogPostController::class, 'index']);
Route::get('/blog-posts/{blogPost}', [BlogPostController::class, 'show']);

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

    // Products (admin operations)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Categories (admin operations)
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Brands (admin operations)
    Route::post('/brands', [BrandController::class, 'store']);
    Route::put('/brands/{brand}', [BrandController::class, 'update']);
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy']);

    // Orders
    Route::apiResource('orders', OrderController::class);

    // Blog Posts (admin operations)
    Route::post('/blog-posts', [BlogPostController::class, 'store']);
    Route::put('/blog-posts/{blogPost}', [BlogPostController::class, 'update']);
    Route::delete('/blog-posts/{blogPost}', [BlogPostController::class, 'destroy']);

    // Comments
    Route::apiResource('comments', CommentController::class);

    // Settings
    Route::apiResource('settings', SettingController::class);

    // Invoices
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/generate', [InvoiceController::class, 'generate']);

    // Reviews
    Route::apiResource('reviews', ReviewController::class);
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);
    Route::patch('/reviews/{review}/approve', [ReviewController::class, 'approve']);
    Route::patch('/reviews/{review}/reject', [ReviewController::class, 'reject']);
    Route::patch('/reviews/{review}/feature', [ReviewController::class, 'feature']);
    Route::patch('/reviews/{review}/unfeature', [ReviewController::class, 'unfeature']);

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
    
    // Review Likes
    Route::post('/reviews/{review}/like', [LikeController::class, 'toggleReviewLike']);
    Route::get('/reviews/{review}/likes/count', [LikeController::class, 'getReviewLikesCount']);
    Route::get('/reviews/{review}/liked', [LikeController::class, 'checkReviewLike']);
    
    // Notifications
    Route::apiResource('notifications', NotificationController::class);
    Route::patch('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/{notification}/mark-as-unread', [NotificationController::class, 'markAsUnread']);
    Route::patch('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
});
