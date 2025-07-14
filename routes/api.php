<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CountryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
});

Route::apiResource('/countries', CountryController::class);
