<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::post('/tenants', [TenantController::class, 'store']);
    Route::put('/tenants/{tenant}', [TenantController::class, 'update']);
    Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
    Route::post('/tenants/{id}/restore', [TenantController::class, 'restore'])->name('tenants.restore');
    Route::post('/tenants/bulk-delete', [TenantController::class, 'bulkDelete']);
    Route::post('/tenants/bulk-restore', [TenantController::class, 'bulkRestore']);

    Route::get('/urls', [UrlController::class, 'index'])->name('url.index');
    Route::post('/urls', [UrlController::class, 'store'])->name('url.store');
    Route::put('/urls/{url}', [UrlController::class, 'update'])->name('url.update');
    Route::post('/urls/{url}', [UrlController::class, 'toggleStatus'])->name('url.toggleStatus'); // For Activate/Disable
    Route::delete('/urls/{url}', [UrlController::class, 'destroy'])->name('url.destroy');
    Route::post('/urls/{id}/restore', [UrlController::class, 'restore'])->name('url.restore');
    Route::post('/urls/bulk-delete', [UrlController::class, 'bulkDelete'])->name('url.bulkDelete');
    Route::post('/urls/bulk-restore', [UrlController::class, 'bulkRestore'])->name('url.bulkRestore');
});
