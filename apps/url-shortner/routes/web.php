<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;

Route::get('/{short_code}', [RedirectController::class, 'handle'])
    ->where('short_code', '[A-Za-z0-9]+');

