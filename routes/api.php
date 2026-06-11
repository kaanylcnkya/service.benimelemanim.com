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
    /*
    |--------------------------------------------------------------------------
    | Public Location Routes
    |--------------------------------------------------------------------------
    | İl / ilçe seçimleri kayıt formlarında kullanılacağı için giriş gerektirmez.
    */
    Route::get('/locations/cities', [LocationController::class, 'cities']);

    Route::get('/locations/cities/{cityId}/districts', [LocationController::class, 'districts'])
        ->whereNumber('cityId');

    /*
    |--------------------------------------------------------------------------
    | Public Auth Routes
    |--------------------------------------------------------------------------
    | Kayıt ve giriş herkese açık ama AppServiceProvider içinde auth-api rate limit tanımlı olmalı.
    */
    Route::middleware('throttle:auth-api')->group(function () {
        Route::post('/auth/register/customer', [AuthController::class, 'registerCustomer']);
        Route::post('/auth/register/cleaner', [AuthController::class, 'registerCleaner']);
        Route::post('/auth/login', [AuthController::class, 'login']);
    });

    /*
    |--------------------------------------------------------------------------
    | Protected Auth Routes
    |--------------------------------------------------------------------------
    | Bunlar token olmadan çalışmaz.
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });
});