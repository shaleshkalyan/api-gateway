<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiProxyController;

Route::any('/{shortCode}', [ApiProxyController::class, 'handle'])
    ->where('shortCode', '[A-Za-z0-9]+');
