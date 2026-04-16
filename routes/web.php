<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Operator\ProductController;

// ========== LOGIN ==========
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);

// ========== MANAGE ACCOUNT (Semua Role) ==========
Route::middleware(['auth'])
    ->prefix('contents')
    ->group(function () {
        Route::get('/manage-account', [AccountController::class, 'index'])->name('manage-account');
        Route::put('/manage-account/username', [AccountController::class, 'updateUsername'])->name('manage-account.update-username');
        Route::put('/manage-account/password', [AccountController::class, 'updatePassword'])->name('manage-account.update-password');
    });

// ========== SEMUA ROUTE UNTUK SEMUA ROLE (dengan pengecekan di controller) ==========
Route::middleware(['auth'])
    ->prefix('contents')
    ->name('contents.')
    ->group(function () {

        // Dashboard - semua role bisa akses
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Categories - hanya admin & super_admin
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // User Management - hanya admin & super_admin
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::put('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Products - semua role bisa akses
        Route::get('/productmanage', [ProductController::class, 'index'])->name('productmanage');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Trash - hanya super_admin
        Route::get('/productmanage/trash', [ProductController::class, 'trash'])->name('productmanage.trash');
        Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('/products/{id}/force', [ProductController::class, 'forceDelete'])->name('products.force-delete');

// Stock Management - semua role bisa akses
Route::get('/stock', [StockController::class, 'index'])->name('stock');
Route::get('/stockmanage', [StockController::class, 'index'])->name('stockmanage');
Route::post('/stock/add', [StockController::class, 'addStock'])->name('stock.add');
Route::post('/stock/remove', [StockController::class, 'removeStock'])->name('stock.remove');
Route::get('/stock/history/{productId}', [StockController::class, 'history'])->name('stock.history');
Route::get('/stock/detail/{productId}', [StockController::class, 'detail'])->name('stock.detail');

        // Mutasi - semua role bisa akses
        Route::get('/mutasi', function () {
            return view('contents.mutasi');
        })->name('mutasi');

        // Reports - semua role bisa akses
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/download', [ReportController::class, 'downloadPdf'])->name('reports.download');
    });


    
// ========== LOGOUT ==========
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');