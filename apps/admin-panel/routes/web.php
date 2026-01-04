<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class,'show'])->name('login');
Route::post('/login', [LoginController::class,'store'])->name('login.store');
Route::post('/logout', [LoginController::class,'destroy'])->name('logout');

Route::middleware(['auth:sanctum','admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::resource('/url', UrlController::class)->only(['index','create','store']);
    Route::get('/tenants', [TenantController::class,'index'])->name('tenants.index');
    Route::post('/tenants', [TenantController::class,'store'])->name('tenants.store');
});