<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class UrlShortnerService
{
    public function shorten(string $url, int $userId, int $tenantId): array
    {
        try {
            $endpoint = rtrim(config('services.url_shortner.endpoint'), '/')
                . '/api/shorten';


            $response = Http::timeout(5)
                ->retry(2, 200)
                ->withToken(config('services.url_shortner.token'))
                ->post($endpoint, [
                    'original_url' => $url,
                    'created_by'   => $userId,
                    'tenant_id'    => $tenantId,
                ]);

            if ($response->failed()) {
                Log::error('URL shortner service failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \RuntimeException(
                    'URL shortner service error (HTTP ' . $response->status() . ')'
                );
            }

            return $response->json();

        } catch (Throwable $e) {
            Log::critical('URL shortner service exception', [
                'message' => $e->getMessage(),
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'original_url' => $url,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
