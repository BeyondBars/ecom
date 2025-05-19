<?php

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
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User routes
    Route::apiResource('users', UserController::class);
    
    // Role routes
    Route::apiResource('roles', RoleController::class);
    
    // Permission routes
    Route::apiResource('permissions', PermissionController::class);
    
    // Product routes
    Route::apiResource('products', ProductController::class);
    Route::get('products/featured', [ProductController::class, 'featured']);
    Route::get('products/on-sale', [ProductController::class, 'onSale']);
    
    // Category routes
    Route::apiResource('categories', CategoryController::class);
    Route::get('categories/tree', [CategoryController::class, 'tree']);
    
    // Brand routes
    Route::apiResource('brands', BrandController::class);
    
    // Order routes
    Route::apiResource('orders', OrderController::class);
    Route::get('orders/stats', [OrderController::class, 'stats']);
    
    // Blog post routes
    Route::apiResource('blog-posts', BlogPostController::class);
    
    // Comment routes
    Route::apiResource('comments', CommentController::class);
    Route::patch('comments/{comment}/approve', [CommentController::class, 'approve']);
    Route::patch('comments/{comment}/reject', [CommentController::class, 'reject']);
    
    // Setting routes
    Route::get('settings', [SettingController::class, 'index']);
    Route::get('settings/{key}', [SettingController::class, 'show']);
    Route::post('settings', [SettingController::class, 'update']);
    
    // Invoice routes
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/generate-pdf', [InvoiceController::class, 'generatePdf']);
    
    // Wishlist routes
    Route::apiResource('wishlists', WishlistController::class);
    Route::post('wishlists/{wishlist}/products', [WishlistController::class, 'addProduct']);
    Route::delete('wishlists/{wishlist}/products', [WishlistController::class, 'removeProduct']);
    
    // Like routes
    Route::get('likes', [LikeController::class, 'index']);
    Route::post('products/{product}/like', [LikeController::class, 'toggleProductLike']);
    Route::post('blog-posts/{blogPost}/like', [LikeController::class, 'toggleBlogPostLike']);
    Route::get('products/{product}/like', [LikeController::class, 'checkProductLike']);
    Route::get('blog-posts/{blogPost}/like', [LikeController::class, 'checkBlogPostLike']);
});

// Public routes
Route::get('products/public', [ProductController::class, 'publicIndex']);
Route::get('products/public/{product}', [ProductController::class, 'publicShow']);
Route::get('categories/public', [CategoryController::class, 'publicIndex']);
Route::get('brands/public', [BrandController::class, 'publicIndex']);
Route::get('blog-posts/public', [BlogPostController::class, 'publicIndex']);
Route::get('blog-posts/public/{blogPost}', [BlogPostController::class, 'publicShow']);
Route::get('comments/public', [CommentController::class, 'publicIndex']);
Route::post('comments/public', [CommentController::class, 'publicStore']);
Route::get('wishlists/public', [WishlistController::class, 'publicIndex']);
