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

    /*
    |--------------------------------------------------------------------------
    | Pages (Blade views ONLY)
    |--------------------------------------------------------------------------
    */
    Route::view('/tenants', 'tenants.index')->name('tenants.index');
    Route::view('/urls', 'url.index')->name('url.index');

    /*
    |--------------------------------------------------------------------------
    | API (JSON ONLY — Alpine uses these)
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->group(function () {

        // Tenants
        Route::get('/tenants', [TenantController::class, 'index']);
        Route::post('/tenants', [TenantController::class, 'store']);
        Route::put('/tenants/{tenant}', [TenantController::class, 'update']);
        Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy']);
        Route::post('/tenants/{id}/restore', [TenantController::class, 'restore']);
        Route::post('/tenants/bulk-delete', [TenantController::class, 'bulkDelete']);
        Route::post('/tenants/bulk-restore', [TenantController::class, 'bulkRestore']);

        // URLs
        Route::get('/urls', [UrlController::class, 'index']);
        Route::post('/urls', [UrlController::class, 'store']);
        Route::delete('/urls/{url}', [UrlController::class, 'destroy']);
        Route::post('/urls/{id}/restore', [UrlController::class, 'restore']);
        Route::post('/urls/bulk-delete', [UrlController::class, 'bulkDelete']);
        Route::post('/urls/bulk-restore', [UrlController::class, 'bulkRestore']);
    });
});
