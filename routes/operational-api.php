<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1/operational')->middleware(['throttle:api'])->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::group(['middleware' => ['role:SUPERADMIN,OWNER,MARKETING']], function () {});
    });
});
