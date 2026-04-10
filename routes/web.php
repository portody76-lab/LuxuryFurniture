<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);

Route::prefix('admin')->middleware(['auth','role:admin'])->group(function(){
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });
});

// Route untuk operator dengan middleware role operator
Route::prefix('operator')->middleware(['auth','role:operator'])->group(function(){
    // Dashboard operator (menampilkan list produk)
    Route::get('/dashboard', [ProductController::class, 'index'])->name('operator.dashboard');
    
    // CRUD Products
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');