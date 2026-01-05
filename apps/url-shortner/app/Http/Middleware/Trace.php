<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Trace
{
    public function handle($request, Closure $next)
    {
        $traceId = $request->header('X-Trace-ID') ?? Str::uuid()->toString();

        Log::withContext(['trace_id' => $traceId]);

        return $next($request);
    }
}
