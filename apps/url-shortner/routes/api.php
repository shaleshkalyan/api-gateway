<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Internal\ShortenController;

Route::middleware('internal.auth')->group(function () {
    Route::get('/ping', function () {
        return response()->json([
            'status' => 'success',
            'service' => 'running fine'
        ]);
    });
});

Route::middleware(['trace', 'internal.auth'])->group(function () {
    Route::post('/mappings', [ShortenController::class, 'store']);
    Route::put('/mappings/{id}', [ShortenController::class, 'update']);
    Route::delete('/mappings/{id}', [ShortenController::class, 'destroy']);

    // Bulk operations
    Route::delete('/mappings/bulk-delete', [ShortenController::class, 'bulkDelete']);
    Route::patch('/mappings/bulk-update', [ShortenController::class, 'bulkUpdate']);
});
