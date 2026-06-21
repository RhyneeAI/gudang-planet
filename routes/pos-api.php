<?php

use App\Http\Controllers\Api\Pos\CategoryController;
use App\Http\Controllers\Api\Pos\CustomerController;
use App\Http\Controllers\Api\Pos\CustomerTypeController;
use App\Http\Controllers\Api\Pos\MarketingController;
use App\Http\Controllers\Api\Pos\MarketingProductController;
use App\Http\Controllers\Api\Pos\ProductController;
use App\Http\Controllers\Api\Pos\PurchaseInstallmentController;
use App\Http\Controllers\Api\Pos\PurchaseTransactionController;
use App\Http\Controllers\Api\Pos\SalesInstallmentController;
use App\Http\Controllers\Api\Pos\SalesTransactionController;
use App\Http\Controllers\Api\Pos\StockMutationController;
use App\Http\Controllers\Api\Pos\SupplierController;
use App\Http\Controllers\Api\Pos\UnitController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    Route::group(['middleware' => ['role:SUPERADMIN,OWNER,MARKETING']], function () {
        Route::apiResource('categories', CategoryController::class)->parameters([
            'categories' => 'category:uuid',
        ])->only(['index', 'show']);

        Route::apiResource('units', UnitController::class)->parameters([
            'units' => 'unit:uuid',
        ])->only(['index', 'show']);

        Route::get('products/generate-code', [ProductController::class, 'generateCode']);
        Route::apiResource('products', ProductController::class)->parameters([
            'products' => 'product:uuid',
        ])->only(['index', 'show']);

        Route::apiResource('suppliers', SupplierController::class)->parameters([
            'suppliers' => 'supplier:uuid',
        ])->only(['index', 'show']);

        Route::apiResource('customers', CustomerController::class)->parameters([
            'customers' => 'customer:uuid',
        ])->only(['index', 'show']);

        Route::apiResource('customer-types', CustomerTypeController::class)->parameters([
            'customer-types' => 'customerType:uuid',
        ])->only(['index', 'show']);

        Route::apiResource('marketings', MarketingController::class)->parameters([
            'marketings' => 'marketing:uuid',
        ])->only(['index', 'show']);

        Route::apiResource('marketing-products', MarketingProductController::class)->parameters([
            'marketing-products' => 'marketingProduct:uuid',
        ])->only(['index', 'show']);
    });

    Route::group(['middleware' => ['role:SUPERADMIN,OWNER']], function () {
        Route::apiResource('categories', CategoryController::class)->parameters([
            'categories' => 'category:uuid',
        ])->except(['index', 'show']);

        Route::apiResource('units', UnitController::class)->parameters([
            'units' => 'unit:uuid',
        ])->except(['index', 'show']);

        Route::apiResource('products', ProductController::class)->parameters([
            'products' => 'product:uuid',
        ])->except(['index', 'show']);

        Route::apiResource('suppliers', SupplierController::class)->parameters([
            'suppliers' => 'supplier:uuid',
        ])->except(['index', 'show']);

        Route::apiResource('customers', CustomerController::class)->parameters([
            'customers' => 'customer:uuid',
        ])->except(['index', 'show']);

        Route::apiResource('customer-types', CustomerTypeController::class)->parameters([
            'customer-types' => 'customerType:uuid',
        ])->except(['index', 'show']);

        Route::apiResource('marketings', MarketingController::class)->parameters([
            'marketings' => 'marketing:uuid',
        ])->except(['index', 'show']);

        Route::apiResource('marketing-products', MarketingProductController::class)->parameters([
            'marketing-products' => 'marketingProduct:uuid',
        ])->except(['index', 'show']);

        Route::prefix('stock-mutations')->group(function () {
            Route::post('/', [StockMutationController::class, 'store']);
            Route::get('/products', [StockMutationController::class, 'index']);
            Route::get('/products/{product:uuid}', [StockMutationController::class, 'show']);
        });
    });

    Route::group(['middleware' => ['role:SUPERADMIN,OWNER,MARKETING']], function () {
        Route::prefix('purchase-transactions')->group(function () {
            Route::get('/', [PurchaseTransactionController::class, 'index']);
            Route::post('/',[PurchaseTransactionController::class, 'store']);
            Route::get('/{purchaseTransaction:ulid}', [PurchaseTransactionController::class, 'show']);
            Route::patch('/{purchaseTransaction:ulid}/cancel', [PurchaseTransactionController::class, 'cancel']);
        });

        Route::prefix('purchase-installments')->group(function () {
            Route::get('/',                                    [PurchaseInstallmentController::class, 'index']);
            Route::get('/{purchaseInstallmentPlan:ulid}',      [PurchaseInstallmentController::class, 'show']);
            Route::post('/{purchaseInstallmentPlan:ulid}/pay', [PurchaseInstallmentController::class, 'pay']);
        });

        Route::prefix('sales-transactions')->group(function () {
            Route::get('/', [SalesTransactionController::class, 'index']);
            Route::post('/', [SalesTransactionController::class, 'store']);
            Route::get('/{salesTransaction:ulid}', [SalesTransactionController::class, 'show']);
            Route::patch('/{salesTransaction:ulid}/cancel', [SalesTransactionController::class, 'cancel']);
        });

        Route::prefix('sales-installments')->group(function () {
            Route::get('/',                                 [SalesInstallmentController::class, 'index']);
            Route::get('/{salesInstallmentPlan:ulid}',      [SalesInstallmentController::class, 'show']);
            Route::post('/{salesInstallmentPlan:ulid}/pay', [SalesInstallmentController::class, 'pay']);
        });

        Route::prefix('reports')->group(function () {
            Route::get('/marketing-commission', [ReportController::class, 'marketingCommission']);
            Route::get('/sales-revenue',        [ReportController::class, 'salesRevenue']);
        });
    });
});
