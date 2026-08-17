<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Learning\LearningDashboardController;
use App\Http\Controllers\Learning\LearningGlossaryController;
use App\Http\Controllers\Learning\LearningLessonController;
use App\Http\Controllers\Learning\LearningModuleController;
use App\Http\Controllers\Learning\LearningQuizAttemptController;
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

    Route::prefix('learning')->name('learning.')->group(function () {
        Route::get('/', [LearningDashboardController::class, 'index'])->name('index');
        Route::get('glossary', [LearningGlossaryController::class, 'index'])->name('glossary');
        Route::post('quizzes/{quiz}/attempts', [LearningQuizAttemptController::class, 'store'])->name('quizzes.attempts.store');

        // These wildcard routes must stay below the fixed-path routes above
        // (glossary, quizzes/...) or Laravel would match "glossary" etc. as
        // {module} instead.
        Route::get('{module}', [LearningModuleController::class, 'show'])->name('modules.show');
        Route::get('{module}/{lesson}', [LearningLessonController::class, 'show'])->name('lessons.show');
        Route::post('{module}/{lesson}/complete', [LearningLessonController::class, 'complete'])->name('lessons.complete');
    });
});
