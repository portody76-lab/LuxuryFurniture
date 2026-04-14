<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Operator\ProductController;
use App\Http\Controllers\Operator\StockController;

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

// ========== REPORTS (Semua Role) ==========
Route::middleware(['auth'])
    ->prefix('contents')
    ->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('contents.reports');
        Route::get('/reports/download', [ReportController::class, 'downloadPdf'])->name('contents.reports.download');
    });

// ========== SUPER ADMIN (Role: super_admin) ==========
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('contents/super-admin')
    ->name('contents.super_admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::put('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::get('/products', [ProductController::class, 'index'])->name('products');
        Route::get('/products/trash', [ProductController::class, 'trash'])->name('products.trash');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('/products/{id}/force', [ProductController::class, 'forceDelete'])->name('products.force-delete');
        Route::get('/stock', [StockController::class, 'index'])->name('stock');
        Route::post('/stock/add', [StockController::class, 'addStock'])->name('stock.add');
        Route::post('/stock/remove', [StockController::class, 'removeStock'])->name('stock.remove');
        
        // REPORTS untuk SUPER ADMIN
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/download', [ReportController::class, 'downloadPdf'])->name('reports.download');
    });

// ========== ADMIN (Role: admin) ==========
Route::middleware(['auth', 'role:admin'])
    ->prefix('contents/admin')
    ->name('contents.admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::put('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

// ========== OPERATOR (Role: operator) ==========
Route::middleware(['auth', 'role:operator'])
    ->prefix('contents/operator')
    ->name('contents.operator.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'operatorDashboard'])->name('dashboard');
        Route::get('/productmanage', [ProductController::class, 'index'])->name('productmanage');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/stock', [StockController::class, 'index'])->name('stock');
        Route::post('/stock/add', [StockController::class, 'addStock'])->name('stock.add');
        Route::post('/stock/remove', [StockController::class, 'removeStock'])->name('stock.remove');
    });

// ========== LOGOUT ==========
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');