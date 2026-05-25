<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryApiController; 
use App\Http\Controllers\Api\DishApiController;
use App\Http\Controllers\Api\TableManageApiController;
use App\Http\Controllers\Api\RestaurantStaffApiController;
use App\Http\Controllers\Api\RestaurantProfileApiController;
use App\Http\Controllers\Api\SupportTicketApiController;
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

// Public route - no authentication needed
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {

    Route::get('profile', [AuthController::class, 'profile']);

    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('refresh', [AuthController::class, 'refresh']);

    // Category APIs
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryApiController::class, 'index']);
        Route::post('/', [CategoryApiController::class, 'store']);
        Route::get('/{id}', [CategoryApiController::class, 'show']);
        Route::put('/{id}', [CategoryApiController::class, 'update']);
        Route::delete('/{id}', [CategoryApiController::class, 'destroy']);
    });

    // Dish APIs (Subcategory)
    Route::prefix('dishes')->group(function () {
        Route::get('/', [DishApiController::class, 'index']); // Get all dishes
        Route::get('/category/{category_id}', [DishApiController::class, 'index']); // Get dishes by category
        Route::post('/', [DishApiController::class, 'store']); // Create new dish
        Route::get('/{id}', [DishApiController::class, 'show']); // Get single dish
        Route::put('/{id}', [DishApiController::class, 'update']); // Update dish
        Route::delete('/{id}', [DishApiController::class, 'destroy']); // Delete dish
        Route::patch('/{id}/status', [DishApiController::class, 'updateStatus']); // Update dish status
        Route::post('/update-discount-status', [DishApiController::class, 'updateDiscount']);
    });


// Table Management APIs
Route::prefix('tables')->group(function () {
    Route::get('/', [TableManageApiController::class, 'index']);           // List tables
    Route::post('/store', [TableManageApiController::class, 'store']);     // Create table
    Route::post('/update/{id}', [TableManageApiController::class, 'update']); // Update table
    Route::post('/status/{id}', [TableManageApiController::class, 'status']); // Change status
    Route::delete('/delete/{id}', [TableManageApiController::class, 'delete']); // Delete table
});






// Restaurant Staff Management API Routes
Route::prefix('restaurant/staff')->group(function () {
    Route::get('/', [RestaurantStaffApiController::class, 'index']);                    // Get all staff
    Route::get('/{id}', [RestaurantStaffApiController::class, 'show']);                // Get single staff
    Route::post('/store', [RestaurantStaffApiController::class, 'store']);             // Create staff
    Route::put('/update/{id}', [RestaurantStaffApiController::class, 'update']);       // Update staff
    Route::delete('/delete/{id}', [RestaurantStaffApiController::class, 'delete']);    // Delete staff
    Route::post('/status/{id}', [RestaurantStaffApiController::class, 'changeStatus']); // Change staff status
    Route::post('/bulk-delete', [RestaurantStaffApiController::class, 'bulkDelete']);  // Bulk delete staff
});

// Restaurant Profile API Routes
Route::prefix('restaurant/profile')->group(function () {
    Route::get('/', [RestaurantProfileApiController::class, 'getProfile']);           // Get profile
    Route::put('/update', [RestaurantProfileApiController::class, 'updateProfile']);   // Update profile
    Route::post('/change-password', [RestaurantProfileApiController::class, 'updatePassword']); // Change password
});




// Support Ticket API Routes (Restaurant)
Route::prefix('support')->group(function () {
    // Ticket Management
    Route::get('/tickets', [SupportTicketApiController::class, 'index']);           // List tickets
    Route::post('/tickets/store', [SupportTicketApiController::class, 'store']);    // Create ticket
    Route::get('/tickets/{id}', [SupportTicketApiController::class, 'show']);       // View ticket details
    Route::post('/tickets/{id}/comment', [SupportTicketApiController::class, 'addComment']);  // Add comment
    Route::post('/tickets/{id}/resolve', [SupportTicketApiController::class, 'markResolved']); // Mark resolved
    Route::delete('/tickets/{id}', [SupportTicketApiController::class, 'destroy']); // Delete ticket
});


});

