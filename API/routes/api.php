<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\PriceCategoryController;
use App\Http\Controllers\PriceController;
use Illuminate\Support\Facades\Route;

Route::prefix('account')->group(function () {
    Route::post('/register', [AccountController::class, 'register']);
    Route::post('/login', [AccountController::class, 'login']);
});

Route::middleware('api.token')->prefix('manage')->group(function () {
    Route::prefix('prices/categories')->group(function () {
        Route::get('/', [PriceCategoryController::class, 'index']);
        Route::post('/', [PriceCategoryController::class, 'store']);
        Route::get('/{category}', [PriceCategoryController::class, 'show']);
        Route::match(['put', 'patch'], '/{category}', [PriceCategoryController::class, 'update']);
        Route::delete('/{category}', [PriceCategoryController::class, 'destroy']);
    });

    Route::prefix('prices')->group(function () {
        Route::get('/', [PriceController::class, 'index']);
        Route::post('/', [PriceController::class, 'store']);
        Route::get('/{price}', [PriceController::class, 'show']);
        Route::match(['put', 'patch'], '/{price}', [PriceController::class, 'update']);
        Route::delete('/{price}', [PriceController::class, 'destroy']);
    });
});
