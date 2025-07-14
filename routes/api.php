<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\WarehouseController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResource('/countries', CountryController::class);
    Route::apiResource('/warehouses', WarehouseController::class);
    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/suppliers', SupplierController::class);
});

