<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\GlobalStockController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\InventoryTransactionController;
use App\Http\Controllers\Api\InventoryTransferController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\LowStockReportController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\WarehouseController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResource('/countries', CountryController::class);
    Route::apiResource('/warehouses', WarehouseController::class);
    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/inventories', InventoryController::class);
    Route::apiResource('/suppliers', SupplierController::class);
    Route::apiResource('/inventory-transactions', InventoryTransactionController::class)
        ->except(['update', 'delete']);
    Route::post('/inventory-transfer', InventoryTransferController::class);
    Route::get('/inventory/global-view', GlobalStockController::class);
    Route::get('/reports/low-stock', LowStockReportController::class);
});
