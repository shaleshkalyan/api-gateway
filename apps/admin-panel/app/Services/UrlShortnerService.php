<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class UrlShortnerService
{
    public function create(string $url, int $userId, int $tenantId): array
    {
        $response = Http::withToken(config('services.url_shortner.token'))
            ->post(config('services.url_shortner.endpoint').'/internal/shorten', [
                'original_url' => $url,
                'created_by' => $userId,
                'tenant_id' => $tenantId,
            ]);

        $response->throw();

        return $response->json();
    }
}
