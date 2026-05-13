<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryApiController;
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
});

