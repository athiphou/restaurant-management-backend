<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
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
use App\Http\Controllers\Api\Order\OrderItemAddonController;
use App\Http\Controllers\Api\Order\OrderStatusHistoryController;
use App\Http\Controllers\Api\Order\DeliveryController;
use App\Http\Controllers\Api\Role\RoleController;
use App\Http\Controllers\Api\Role\UserRoleController;
use App\Http\Controllers\Api\DashboardController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/otp', [AuthController::class, 'sendOtp']);
Route::post('/login/otp/resend', [AuthController::class, 'resendOtp']);
Route::post('/login/verify', [AuthController::class, 'verifyOtp']);
Route::post('/auth/google', [GoogleAuthController::class, 'signIn']);

// Public read-only endpoints (browsing menu & locations without login)
Route::get('menu/tree', [FoodController::class, 'tree']);
Route::get('foods', [FoodController::class, 'index']);
Route::get('foods/{food}/full', [FoodController::class, 'full']);
Route::get('foods/{food}/variants', [FoodController::class, 'variants']);
Route::get('foods/{food}/addons', [FoodController::class, 'addons']);
Route::get('foods/{food}', [FoodController::class, 'show']);
Route::get('food-categories', [FoodCategoryController::class, 'index']);
Route::get('food-categories/{category}/foods', [FoodCategoryController::class, 'foods']);
Route::get('food-categories/{category}', [FoodCategoryController::class, 'show']);
Route::get('restaurants', [RestaurantController::class, 'index']);
Route::get('restaurants/{restaurant}', [RestaurantController::class, 'show']);
Route::get('restaurant-branches', [RestaurantBranchController::class, 'index']);
Route::get('restaurant-branches/{branch}', [RestaurantBranchController::class, 'show']);
Route::get('promotions', [PromotionController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    // Food
    Route::patch('foods/{food}/toggle-availability', [FoodController::class, 'toggleAvailability']);
    Route::patch('food-categories/{category}/toggle-active', [FoodCategoryController::class, 'toggleActive']);
    Route::patch('food-variants/{food_variant}/toggle-availability', [FoodVariantController::class, 'toggleAvailability']);
    Route::get('food-addons/{food_addon}/items', [FoodAddonController::class, 'items']);
    Route::patch('food-addons/{food_addon}/toggle-availability', [FoodAddonController::class, 'toggleAvailability']);
    Route::patch('food-addon-items/{food_addon_item}/toggle-availability', [FoodAddonItemController::class, 'toggleAvailability']);

    Route::apiResource('food-categories', FoodCategoryController::class)->except(['index', 'show']);
    Route::apiResource('foods', FoodController::class)->except(['index', 'show']);
    Route::apiResource('food-variants', FoodVariantController::class);
    Route::apiResource('food-addons', FoodAddonController::class);
    Route::apiResource('food-addon-items', FoodAddonItemController::class);

    // Customer
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('customer-addresses', CustomerAddressController::class);

    // Promotion
    Route::apiResource('promotions', PromotionController::class)->except(['index']);

    // Order
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order-items', OrderItemController::class);

    // Restaurant
    Route::apiResource('restaurants', RestaurantController::class)->except(['index', 'show']);
    Route::apiResource('restaurant-branches', RestaurantBranchController::class)->except(['index', 'show']);
    Route::apiResource('restaurant-tables', RestaurantTableController::class);

    // Staff
    Route::apiResource('staff-positions', StaffPositionController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('delivery-drivers', DeliveryDriverController::class);

    // Inventory
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('ingredients', IngredientController::class);
    Route::apiResource('inventory', InventoryController::class);
    Route::apiResource('inventory-transactions', InventoryTransactionController::class);
    Route::apiResource('recipes', RecipeController::class);
    Route::apiResource('recipe-items', RecipeItemController::class);

    // Payment
    Route::apiResource('payments', PaymentController::class);

    // Order extras
    Route::apiResource('order-item-addons', OrderItemAddonController::class);
    Route::apiResource('order-status-history', OrderStatusHistoryController::class);
    Route::apiResource('deliveries', DeliveryController::class);

    // Role
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('user-roles', UserRoleController::class);

    // Dashboard
    Route::get('dashboard/overview', [DashboardController::class, 'overview']);
    Route::get('dashboard/revenue', [DashboardController::class, 'revenue']);
    Route::get('dashboard/orders', [DashboardController::class, 'orders']);
    Route::get('dashboard/top-foods', [DashboardController::class, 'topFoods']);
    Route::get('dashboard/recent-orders', [DashboardController::class, 'recentOrders']);
    Route::get('dashboard/recent-customers', [DashboardController::class, 'recentCustomers']);
    Route::get('dashboard/inventory', [DashboardController::class, 'inventory']);
    Route::get('dashboard/payments', [DashboardController::class, 'payments']);
});
