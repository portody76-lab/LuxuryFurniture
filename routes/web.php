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

// ========== ADMIN (Role: admin) ==========
Route::middleware(['auth', 'role:admin'])
    ->prefix('contents')
    ->name('contents.admin.')
    ->group(function () {

        // Dashboard Admin
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])
            ->name('dashboard');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::put('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

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

// ========== REPORTS dan MANAGE ACCOUNT (Gabung Admin & Operator) ==========
Route::middleware(['auth'])
    ->group(function () {

        // REPORTS
        Route::get('/contents/reports', [App\Http\Controllers\ReportController::class, 'index'])
            ->name('contents.reports');
        Route::get('/contents/reports/download', [App\Http\Controllers\ReportController::class, 'downloadPdf'])
            ->name('contents.reports.download');

        // MANAGE ACCOUNT
        Route::get('/contents/manage-account', [AccountController::class, 'index'])
            ->name('manage-account');
        Route::put('/contents/manage-account/username', [AccountController::class, 'updateUsername'])
            ->name('manage-account.update-username');
        Route::put('/contents/manage-account/password', [AccountController::class, 'updatePassword'])
            ->name('manage-account.update-password');
    });

// ========== LOGOUT ==========
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
