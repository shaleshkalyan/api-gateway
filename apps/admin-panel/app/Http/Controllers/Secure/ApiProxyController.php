<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\UrlMapping;
use App\Models\Tenant;

class ApiProxyController extends Controller
{
    public function handle(Request $request)
    {
        $method     = strtoupper($request->method());
        $tenantSlug = $request->route('tenantSlug');
        $shortCode  = $request->route('shortCode');

        $client = Tenant::where('status', 'active')
            ->where('slug', $tenantSlug)
            ->firstOrFail();

        $mapping = UrlMapping::where('short_code', $shortCode)
            ->where('method', $method)
            ->where('tenant_id', $client->id)
            ->where('is_active', 1)
            ->firstOrFail();

        try {
            $response = Http::withToken(config('services.internal.token'))
                ->timeout(10)
                ->send(
                    $method,
                    $mapping->original_url,
                    [
                        'query' => $request->query(),
                        'json' => $request->all(),
                        'headers' => [
                            'X-CLIENT-ID' => $client->id,
                        ]
                    ]
                );

            return response($response->body(), $response->status())
                ->withHeaders(
                    collect($response->headers())
                        ->map(fn($v) => $v[0])
                        ->toArray()
                );
        } catch (\Throwable $e) {
            Log::error('Gateway forwarding failed', [
                'tenant' => $tenantSlug,
                'short_code' => $shortCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Service unavailable',
            ], 503);
        }
    }
}
