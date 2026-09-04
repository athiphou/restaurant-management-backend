<?php

use App\Http\Controllers\Api\FoodCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Restaurant\RestaurantController;
use App\Http\Controllers\Api\Restaurant\RestaurantBranchController;
use App\Http\Controllers\Api\Restaurant\RestaurantTableController;

use App\Http\Controllers\Api\Staff\StaffPositionController;
use App\Http\Controllers\Api\Staff\EmployeeController;
use App\Http\Controllers\Api\Staff\DeliveryDriverController;

use App\Http\Controllers\Api\Inventory\SupplierController;
use App\Http\Controllers\Api\Inventory\IngredientController;
use App\Http\Controllers\Api\Inventory\InventoryController;
use App\Http\Controllers\Api\Inventory\InventoryTransactionController;
use App\Http\Controllers\Api\Inventory\RecipeController;
use App\Http\Controllers\Api\Inventory\RecipeItemController;

use App\Http\Controllers\Api\Payment\PaymentController;
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

Route::apiResource('food-categories', FoodCategoryController::class);
//restaurant
Route::apiResource('restaurants', RestaurantController::class);
Route::apiResource('restaurants_branch', RestaurantBranchController::class);
Route::apiResource('restaurants_table', RestaurantTableController::class);
//staff
Route::apiResource('staff', StaffPositionController::class);
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('delivery-drivers', DeliveryDriverController::class);
//inventory
Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('ingredients', IngredientController::class);
Route::apiResource('inventory', InventoryController::class);
Route::apiResource('inventory-transactions', InventoryTransactionController::class);
Route::apiResource('recipes', RecipeController::class);
Route::apiResource('recipe-items', RecipeItemController::class);
//payment
Route::apiResource('payments', PaymentController::class);
