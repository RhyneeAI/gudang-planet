<?php

use App\Http\Controllers\Api\Operational\OpsDashboardController;
use App\Http\Controllers\Api\Operational\OpsEditLogController;
use App\Http\Controllers\Api\Operational\OpsExpenseController;
use App\Http\Controllers\Api\Operational\OpsIncomeController;
use App\Http\Controllers\Api\Operational\OpsMandorController;
use App\Http\Controllers\Api\Operational\OpsNotificationController;
use App\Http\Controllers\Api\Operational\OpsTransferConfirmationController;
use App\Http\Controllers\Api\Operational\OpsWalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/operational')->middleware(['auth:sanctum'])->group(function () {

    // ─────────────────────────────────────────────
    // All operational roles: SUPERADMIN, OWNER, ADMIN, MANDOR
    // ─────────────────────────────────────────────
    Route::middleware(['role:SUPERADMIN,OWNER,ADMIN,MANDOR'])->group(function () {

        Route::get('dashboard/admin', [OpsDashboardController::class, 'adminDashboard']);
        Route::get('dashboard/mandor', [OpsDashboardController::class, 'mandorDashboard']);

        Route::apiResource('incomes', OpsIncomeController::class)
            ->parameters(['incomes' => 'uuid']);

        Route::apiResource('expenses', OpsExpenseController::class)
            ->parameters(['expenses' => 'uuid']);

        Route::prefix('notifications')->group(function () {
            Route::get('/', [OpsNotificationController::class, 'index']);
            Route::patch('/read-all', [OpsNotificationController::class, 'markAllAsRead']);
            Route::patch('/{opsNotification:uuid}/read', [OpsNotificationController::class, 'markAsRead']);
        });

        Route::get('transfer-confirmations', [OpsTransferConfirmationController::class, 'index']);
        Route::get('transfer-confirmations/{uuid}', [OpsTransferConfirmationController::class, 'show']);

        Route::get('mandors', [OpsMandorController::class, 'index']);
    });

    // ─────────────────────────────────────────────
    // Admin & owner: SUPERADMIN, OWNER, ADMIN
    // ─────────────────────────────────────────────
    Route::middleware(['role:SUPERADMIN,OWNER,ADMIN'])->group(function () {

        Route::get('edit-logs', [OpsEditLogController::class, 'index']);
    });

    // ─────────────────────────────────────────────
    // Mandor only
    // ─────────────────────────────────────────────
    Route::middleware(['role:MANDOR'])->group(function () {

        Route::get('wallet', [OpsWalletController::class, 'show']);
        Route::get('wallet/transactions', [OpsWalletController::class, 'transactions']);

        Route::post('transfer-confirmations/{opsTransferConfirmation:uuid}/confirm', [OpsTransferConfirmationController::class, 'confirm']);
        Route::post('transfer-confirmations/{opsTransferConfirmation:uuid}/reject', [OpsTransferConfirmationController::class, 'reject']);
    });
});
