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
        Route::get('contents.admin.dashboard', [DashboardController::class, 'index'])
        ->name('contents.admin.dashboard');
    Route::get('contents.admin.dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('contents.admin.dashboard');

    Route::view('/manage-admin', 'contents.admin.manage-admin')->name('contents.admin.manage-admin');

    Route::view('/users', 'contents.admin.users')->name('contents.admin.users');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('contents.admin.categories');

    Route::post('/categories', [CategoryController::class, 'store'])
        ->name('contents.admin.categories.store');

    Route::put('/categories/{id}', [CategoryController::class, 'update'])
        ->name('contents.admin.categories.update');

    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
        ->name('contents.admin.categories.destroy');

    Route::view('/reports', 'contents.admin.reports')->name('contents.admin.reports');
});

// OPERATOR
Route::prefix('operator')->middleware(['auth', 'role:operator'])->group(function () {
    Route::get('/dashboard', function () {
        return view('operator.dashboard');
    });
});

// LOGOUT
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');
