<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Add coupon routes to the API routes

// Coupon routes
Route::apiResource('coupons', \App\Http\Controllers\Api\CouponController::class);
Route::put('coupons/{coupon}/status', [\App\Http\Controllers\Api\CouponController::class, 'updateStatus']);
Route::post('coupons/validate', [\App\Http\Controllers\Api\CouponController::class, 'validate']);
