<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/', [LoginController::class, 'showLoginForm']);
Route::post('/', [LoginController::class, 'login']);

Route::prefix('admin')->middleware(['auth','role:admin'])->group(function(){
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });
});

Route::prefix('operator')->middleware(['auth','role:operator'])->group(function(){
    Route::get('/dashboard', function () {
        return view('operator.dashboard');
    });
});

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');