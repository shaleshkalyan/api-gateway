<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiProxyController;

Route::middleware('gateway.auth')->group(function () {

    Route::any('/{tenantSlug}/{shortCode}', [ApiProxyController::class, 'handle'])
        ->where([
            'tenantSlug' => '[a-zA-Z0-9\-]+',
            'shortCode'  => '[A-Za-z0-9]+',
        ]);
});
