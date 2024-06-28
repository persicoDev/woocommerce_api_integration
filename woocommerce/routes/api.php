<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\CustomerController;

Route::prefix('v1')->group(function () {
    // Product routes
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::patch('products/{id}/cost', [ProductController::class, 'updateCost']);

    // Order routes
    Route::get('orders/analytics', [OrderController::class, 'getAnalytics']);

    // Customer routes
    Route::get('customers', [CustomerController::class, 'index']);
    Route::patch('customers/{id}/ltv', [CustomerController::class, 'updateLTV']);
});
