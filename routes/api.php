<?php

use App\Http\Controllers\Api\Stocks\StockController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['transform.response.keys'])->group(function () {
    Route::prefix('stocks')->middleware(['api.key:stocks.read'])->group(function () {
        Route::get('/', [StockController::class, 'index']);
        Route::get('{ticker}', [StockController::class, 'show']);
    });
});
