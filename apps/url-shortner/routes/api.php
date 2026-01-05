<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Internal\ShortenController;

Route::get('/ping', function () {
    return response()->json([
        'status' => 'success',
        'service' => 'running fine'
    ]);
});
Route::middleware(['internal.auth'])->group(function () {
    Route::post('/shorten', [ShortenController::class, 'store']);
});