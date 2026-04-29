<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\UnitController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/login', function () {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Please login first.',
            'code' => 403
        ], 403);
    })->name('login');

    
    // Protected
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('categories', CategoryController::class)->parameters([
            'categories' => 'category:uuid'
        ]);    

        Route::apiResource('units', UnitController::class)->parameters([
            'units' => 'unit:uuid'
        ]);     
    });
});