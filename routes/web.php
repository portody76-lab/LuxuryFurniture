<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;

// LOGIN
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);

// ADMIN
Route::prefix('contents')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('contents.dashboard', [DashboardController::class, 'index'])
        ->name('contents.dashboard');
    Route::get('contents.dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('contents.dashboard');

    Route::view('/manage-admin', 'admin.manage-admin')->name('admin.manage-admin');

    Route::view('/users', 'admin.users')->name('admin.users');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('contents.categories');

    Route::post('/categories', [CategoryController::class, 'store'])
        ->name('contents.categories.store');

    Route::put('/categories/{id}', [CategoryController::class, 'update'])
        ->name('contents.categories.update');

    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
        ->name('contents.categories.destroy');

    Route::view('/reports', 'contents.reports')->name('contents.reports');
});

// OPERATOR
Route::prefix('operator')->middleware(['auth', 'role:operator'])->group(function () {
    Route::get('/dashboard', function () {
        return view('operator.dashboard');
    });
});

// LOGOUT
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');


Route::get('/content', function () {
    return view('contents.dashboard');
});
