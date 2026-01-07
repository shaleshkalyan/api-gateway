<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\UrlMapping;
use App\Models\Tenant;

class ApiProxyController extends Controller
{
    public function handle(Request $request)
    {
        $publicUrl = $request->url();          // https://gateway.app/x9A2kP
        $method    = strtoupper($request->method());

        $client = Tenant::where('status', 'active')
            ->first();

        $mapping = UrlMapping::where('short_url', $publicUrl)
            ->where('method', $method)
            ->where('tenant_id', $client->id)
            ->where('is_active', 1)
            ->firstOrFail();

        try {
            $response = Http::withToken(config('services.gateway.token'))
                ->timeout(10)
                ->send(
                    $method,
                    $mapping->original_url,
                    [
                        'query' => $request->query(),
                        'json' => $request->all(),
                        'headers' => [
                            'X-Tenant-ID' => $client->tenant_id,
                            'X-Client-ID' => $client->id,
                        ]
                    ]
                );

            return response(
                $response->body(),
                $response->status()
            )->withHeaders(
                collect($response->headers())
                    ->map(fn ($v) => $v[0])
                    ->toArray()
            );

        } catch (\Throwable $e) {
            Log::error('Gateway forwarding failed', [
                'url' => $publicUrl,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Service unavailable'
            ], 503);
        }
    }
}
