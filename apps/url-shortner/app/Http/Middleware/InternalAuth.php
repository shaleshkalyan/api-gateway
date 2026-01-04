<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InternalAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        abort_unless(
            $token && hash_equals($token, config('services.internal.token')),
            401,
            'Unauthorized internal request'
        );

        return $next($request);
    }
}
