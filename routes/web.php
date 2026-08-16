<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Stocks\StockController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [LoginController::class, 'login'])->middleware('guest');
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', fn () => redirect()->route('stocks.index'))->name('home');
    Route::get('dashboard', fn () => redirect()->route('stocks.index'))->name('dashboard');

    Route::group(['prefix' => 'stocks'], function () {
        Route::get('/', [StockController::class, 'index'])->name('stocks.index');
        Route::get('{ticker}', [StockController::class, 'show'])->name('stocks.show');
    });
});
