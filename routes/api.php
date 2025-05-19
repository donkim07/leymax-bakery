<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\AssembledItemController;

Route::middleware('auth:sanctum')->group(function () {
    // Business routes
    Route::apiResource('businesses', BusinessController::class);
    Route::post('businesses/{business}/toggle-status', [BusinessController::class, 'toggleStatus']);
    Route::get('businesses/{business}/statistics', [BusinessController::class, 'statistics']);

    // Store routes
    Route::apiResource('businesses.stores', StoreController::class);
    Route::post('stores/{store}/toggle-status', [StoreController::class, 'toggleStatus']);
    Route::get('stores/{store}/inventory', [StoreController::class, 'inventory']);

    // Category routes
    Route::apiResource('businesses.categories', CategoryController::class);
    Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus']);
    Route::post('categories/reorder', [CategoryController::class, 'reorder']);

    // Product routes
    Route::apiResource('businesses.products', ProductController::class);
    Route::post('businesses/{business}/products/{product}/toggle-status', [ProductController::class, 'toggleStatus']);
    Route::post('businesses/{business}/products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured']);
    Route::post('businesses/{business}/products/{product}/update-prices', [ProductController::class, 'updatePrices']);

    // Inventory routes
    Route::post('businesses/{business}/stores/{store}/products/{product}/adjust-stock', [InventoryController::class, 'adjustStock']);
    Route::post('businesses/{business}/inventory/transfer', [InventoryController::class, 'transfer']);
    Route::get('businesses/{business}/inventory/movements', [InventoryController::class, 'movements']);
    Route::get('businesses/{business}/inventory/alerts', [InventoryController::class, 'alerts']);

    // Assembled Items API
    Route::get('/assembled-items/{id}', [AssembledItemController::class, 'show']);
    Route::get('/assembled-items/{id}/ingredients', [AssembledItemController::class, 'ingredients']);
    Route::get('/assembled-items/{id}/paste-divisions', [AssembledItemController::class, 'pasteDivisions']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Dashboard Filter Routes
    Route::get('/dashboard/tools/filter', [DashboardController::class, 'filterToolsData'])->name('api.dashboard.tools.filter');
});