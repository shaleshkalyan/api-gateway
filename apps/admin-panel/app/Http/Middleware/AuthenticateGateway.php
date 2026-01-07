<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ClientToken;
use App\Models\Tenant;

class AuthenticateGateway
{
    public function handle(Request $request, Closure $next)
    {
        $rawToken = $request->bearerToken();

        if (!$rawToken) {
            return response()->json([
                'message' => 'Missing API token'
            ], 401);
        }
        abort_unless(
            hash_equals(
                (string) $request->bearerToken(),
                (string) config('services.gateway.token')
            ),
            401,
            'Unauthorized request'
        );

        $tenantSlug = $request->route('tenantSlug');

        if (!$tenantSlug) {
            return response()->json([
                'message' => 'Tenant not specified'
            ], 400);
        }

        $tenant = Tenant::where('status', 'active')
            ->where('slug', $tenantSlug)
            ->first();

        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant mismatch'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Bind context to request (IMPORTANT)
        |--------------------------------------------------------------------------
        */
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
