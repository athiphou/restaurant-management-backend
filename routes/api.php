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
