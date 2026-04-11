<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Operator\ProductController;
use App\Http\Controllers\Operator\StockController;

// ========== LOGIN ==========
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);

// ========== ADMIN (Role: admin) ==========
Route::middleware(['auth', 'role:admin'])
    ->prefix('contents')
    ->name('contents.admin.')
    ->group(function () {

        // Dashboard Admin
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])
            ->name('dashboard');

        // MANAGE ACCOUNT ADMIN (tambahkan ini)
        Route::get('/manage-admin', [App\Http\Controllers\Admin\AccountController::class, 'index'])
            ->name('manage-admin');

        Route::put('/manage-admin/username', [App\Http\Controllers\Admin\AccountController::class, 'updateUsername'])
            ->name('manage-admin.update-username');

        Route::put('/manage-admin/password', [App\Http\Controllers\Admin\AccountController::class, 'updatePassword'])
            ->name('manage-admin.update-password');

        // Users
        Route::view('/users', 'contents.admin.users')
            ->name('users');

        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])
            ->name('categories');
        Route::post('/categories', [CategoryController::class, 'store'])
            ->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])
            ->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy');

        // Reports
        Route::view('/reports', 'contents.admin.reports')
            ->name('reports');
    });

// ========== OPERATOR (Role: operator) ==========
Route::middleware(['auth', 'role:operator'])
    ->prefix('contents')
    ->name('contents.operator.')
    ->group(function () {

        // Dashboard Operator
        Route::get('/operator/dashboard', [DashboardController::class, 'operatorDashboard'])
            ->name('dashboard');

        // Product Management
        Route::get('/operator/productmanage', [ProductController::class, 'index'])
            ->name('productmanage');

        // Trash
        Route::get('/operator/productmanage/trash', [ProductController::class, 'trash'])
            ->name('productmanage.trash');

        // Product CRUD
        Route::post('/operator/products', [ProductController::class, 'store'])
            ->name('products.store');

        Route::put('/operator/products/{id}', [ProductController::class, 'update'])
            ->name('products.update');

        Route::delete('/operator/products/{id}', [ProductController::class, 'destroy'])
            ->name('products.destroy');

        // Restore
        Route::post('/operator/products/{id}/restore', [ProductController::class, 'restore'])
            ->name('products.restore');

        // ========== STOCK MANAGEMENT ==========
        Route::get('/operator/stock', [StockController::class, 'index'])
            ->name('stock');

        Route::post('/operator/stock/add', [StockController::class, 'addStock'])
            ->name('stock.add');

        Route::post('/operator/stock/remove', [StockController::class, 'removeStock'])
            ->name('stock.remove');
    });

// ========== LOGOUT ==========
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
