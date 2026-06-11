<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\LocationController;

Route::get('/health', function () {
    return response()->json([
        'status' => true,
        'message' => 'BenimElemanım Service API çalışıyor.',
        'version' => 'v1',
    ]);
});

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:api')->group(function () {
        Route::get('/locations/cities', [LocationController::class, 'cities']);

        Route::get('/locations/cities/{cityId}/districts', [LocationController::class, 'districts'])
            ->whereNumber('cityId');
    });

    Route::middleware('throttle:auth-api')->group(function () {
        Route::post('/auth/register/customer', [AuthController::class, 'registerCustomer']);
        Route::post('/auth/register/cleaner', [AuthController::class, 'registerCleaner']);
        Route::post('/auth/login', [AuthController::class, 'login']);
    });

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });
});