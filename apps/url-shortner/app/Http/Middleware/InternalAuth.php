<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InternalAuth
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(
            hash_equals(
                (string) $request->bearerToken(),
                (string) config('services.internal.token')
            ),
            401,
            'Unauthorized internal request'
        );

        return $next($request);
    }
}
