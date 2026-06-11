<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\JobRequestController;
use App\Http\Controllers\Api\V1\CleanerController;

Route::get('/health', function () {
    return response()->json([
        'status' => true,
        'message' => 'BenimElemanım Service API çalışıyor.',
        'version' => 'v1',
    ]);
});

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:120,1')->group(function () {
        Route::get('/locations/cities', [LocationController::class, 'cities']);

        Route::get('/locations/cities/{cityId}/districts', [LocationController::class, 'districts'])
            ->whereNumber('cityId');

        Route::get('/cleaners', [CleanerController::class, 'index']);
        Route::get('/cleaners/{id}', [CleanerController::class, 'show'])->whereNumber('id');
    });

    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/auth/register/customer', [AuthController::class, 'registerCustomer']);
        Route::post('/auth/register/cleaner', [AuthController::class, 'registerCleaner']);
        Route::post('/auth/login', [AuthController::class, 'login']);
    });

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/job-requests', [JobRequestController::class, 'index']);
        Route::post('/job-requests', [JobRequestController::class, 'store']);
        Route::get('/my-job-requests', [JobRequestController::class, 'myRequests']);
    });
});