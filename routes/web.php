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
        
        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        
        // Products
        Route::get('/products', [ProductController::class, 'index'])->name('products');
        Route::get('/products/trash', [ProductController::class, 'trash'])->name('products.trash');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('/products/{id}/force', [ProductController::class, 'forceDelete'])->name('products.force-delete');
        
        // Stock Management (pakai StockController)
        Route::get('/stock', [StockController::class, 'index'])->name('stock');
        Route::post('/stock/add', [StockController::class, 'addStock'])->name('stock.add');
        Route::post('/stock/remove', [StockController::class, 'removeStock'])->name('stock.remove');
        Route::get('/stock/history/{productId}', [StockController::class, 'history'])->name('stock.history');
        Route::get('/stock/detail/{productId}', [StockController::class, 'detail'])->name('stock.detail');
        
        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/download', [ReportController::class, 'downloadPdf'])->name('reports.download');
    });

// ========== ADMIN (Role: admin) ==========
Route::middleware(['auth', 'role:admin'])
    ->prefix('contents/admin')
    ->name('contents.admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        
        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        
        // Products
        Route::get('/products', [ProductController::class, 'index'])->name('products');
        
        // Stock Management (pakai StockController)
        Route::get('/stock', [StockController::class, 'index'])->name('stock');
        Route::post('/stock/add', [StockController::class, 'addStock'])->name('stock.add');
        Route::post('/stock/remove', [StockController::class, 'removeStock'])->name('stock.remove');
        Route::get('/stock/history/{productId}', [StockController::class, 'history'])->name('stock.history');
        Route::get('/stock/detail/{productId}', [StockController::class, 'detail'])->name('stock.detail');
    });

// ========== OPERATOR (Role: operator) ==========
Route::middleware(['auth', 'role:operator'])
    ->prefix('contents/operator')
    ->name('contents.operator.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'operatorDashboard'])->name('dashboard');
        
        // Product Management
        Route::get('/productmanage', [ProductController::class, 'index'])->name('productmanage');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        
        // Stock Management (pakai StockController)
        Route::get('/stock', [StockController::class, 'index'])->name('stock');
        Route::post('/stock/add', [StockController::class, 'addStock'])->name('stock.add');
        Route::post('/stock/remove', [StockController::class, 'removeStock'])->name('stock.remove');
        Route::get('/stock/history/{productId}', [StockController::class, 'history'])->name('stock.history');
        Route::get('/stock/detail/{productId}', [StockController::class, 'detail'])->name('stock.detail');
    });

// ========== USER MANAGEMENT (Super Admin & Admin) ==========
Route::middleware(['auth', 'role:super_admin,admin'])
    ->prefix('contents/user-management')
    ->name('contents.user-management.')
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::put('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    });

// ========== MUTASI (Semua Role: Super Admin, Admin, Operator) ==========
Route::middleware(['auth', 'role:super_admin,admin,operator'])
    ->get('/contents/mutasi', function() {
        return view('contents.mutasi');
    })->name('contents.mutasi');

// ========== LOGOUT ==========
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');