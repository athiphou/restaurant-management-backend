<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Food
use App\Http\Controllers\Api\FoodCategoryController;
use App\Http\Controllers\Api\Menu\FoodController;
use App\Http\Controllers\Api\Menu\FoodVariantController;
use App\Http\Controllers\Api\Menu\FoodAddonController;
use App\Http\Controllers\Api\Menu\FoodAddonItemController;

// Customer
use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Customer\CustomerAddressController;

// Promotion
use App\Http\Controllers\Api\Promotion\PromotionController;

// Order
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Order\OrderItemController;

// Restaurant
use App\Http\Controllers\Api\Restaurant\RestaurantController;
use App\Http\Controllers\Api\Restaurant\RestaurantBranchController;
use App\Http\Controllers\Api\Restaurant\RestaurantTableController;

// Staff
use App\Http\Controllers\Api\Staff\StaffPositionController;
use App\Http\Controllers\Api\Staff\EmployeeController;
use App\Http\Controllers\Api\Staff\DeliveryDriverController;

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

// Restaurant
Route::apiResource('restaurants', RestaurantController::class);
Route::apiResource('restaurant-branches', RestaurantBranchController::class);
Route::apiResource('restaurant-tables', RestaurantTableController::class);

// Staff
Route::apiResource('staff-positions', StaffPositionController::class);
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('delivery-drivers', DeliveryDriverController::class);