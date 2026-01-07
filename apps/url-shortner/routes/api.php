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
Route::middleware(['trace', 'internal.auth'])->post('/shorten', [ShortenController::class, 'store']);
