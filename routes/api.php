<?php

use App\Http\Controllers\Api\FoodCategoryController;
use App\Http\Controllers\Api\Menu\FoodController;
use App\Http\Controllers\Api\Menu\FoodVariantController;
use App\Http\Controllers\Api\Menu\FoodAddonController;
use App\Http\Controllers\Api\Menu\FoodAddonItemController;
use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Customer\CustomerAddressController;
use App\Http\Controllers\Api\Promotion\PromotionController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Order\OrderItemController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Food
Route::apiResource('food-categories', FoodCategoryController::class);
Route::apiResource('foods', FoodController::class);
Route::apiResource('food-variants', FoodVariantController::class);
Route::apiResource('food-addons', FoodAddonController::class);
Route::apiResource('food-addon-items', FoodAddonItemController::class);

// Customer
Route::apiResource('customers', CustomerController::class);
Route::apiResource('customer-addresses', CustomerAddressController::class);

// Promotion
Route::apiResource('promotions', PromotionController::class);

// Order
Route::apiResource('orders', OrderController::class);

Route::apiResource('order-items', OrderItemController::class);