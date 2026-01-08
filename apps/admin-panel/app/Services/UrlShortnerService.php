<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class UrlShortnerService
{
    private function getBaseEndpoint(): string
    {
        return rtrim(config('services.internal.endpoint'), '/');
    }

    private function getHttpClient()
    {
        return Http::timeout(5)
            ->retry(2, 200)
            ->withToken(config('services.internal.token'));
    }

    private function handleServiceError($response, $context)
    {
        Log::error('URL shortner service failed', [
            'status' => $response->status(),
            'body' => $response->body(),
            'context' => $context,
        ]);

        throw new \RuntimeException(
            'URL shortner service error (HTTP ' . $response->status() . ')'
        );
    }

    private function handleServiceException(Throwable $e, $context)
    {
        Log::critical('URL shortner service exception', [
            'message' => $e->getMessage(),
            'context' => $context,
            'trace' => $e->getTraceAsString(),
        ]);

        throw $e;
    }
    
    public function store(string $url, int $userId, int $tenantId, string $method = 'POST'): array
    {
        $endpoint = $this->getBaseEndpoint() . '/api/mappings';
        $context = [
            'original_url' => $url,
            'user_id' => $userId,
            'tenant_id' => $tenantId,
        ];

        try {
            $response = $this->getHttpClient()
                ->post($endpoint, [
                    'original_url' => $url,
                    'method' => $method,
                    'created_by' => $userId,
                    'tenant_id' => $tenantId,
                ]);

            if ($response->failed()) {
                $this->handleServiceError($response, $context);
            }

            return $response->json();

        } catch (Throwable $e) {
            $this->handleServiceException($e, $context);
        }
    }

    public function update(int $urlId, string $url, int $userId, int $tenantId, string $method, bool $isActive): array
    {
        $endpoint = $this->getBaseEndpoint() . "/api/mappings/{$urlId}";
        $context = [
            'url_id' => $urlId,
            'original_url' => $url,
            'user_id' => $userId,
            'tenant_id' => $tenantId,
        ];

        try {
            $response = $this->getHttpClient()
                ->put($endpoint, [
                    'original_url' => $url,
                    'method' => $method,
                    'is_active' => $isActive,
                    'updated_by' => $userId,
                    'tenant_id' => $tenantId,
                ]);

            if ($response->failed()) {
                $this->handleServiceError($response, $context);
            }

            return $response->json();

        } catch (Throwable $e) {
            $this->handleServiceException($e, $context);
        }
    }

    public function delete(int $urlId, int $userId, int $tenantId): array
    {
        $endpoint = $this->getBaseEndpoint() . "/api/mappings/{$urlId}";
        $context = [
            'url_id' => $urlId,
            'user_id' => $userId,
            'tenant_id' => $tenantId,
        ];

        try {
            $response = $this->getHttpClient()
                ->delete($endpoint, [
                    'deleted_by' => $userId,
                    'tenant_id' => $tenantId,
                ]);

            if ($response->failed()) {
                $this->handleServiceError($response, $context);
            }

            return $response->json();

        } catch (Throwable $e) {
            $this->handleServiceException($e, $context);
        }
    }
    
    public function bulkDelete(array $ids, int $userId): array
    {
        $endpoint = $this->getBaseEndpoint() . "/api/mappings/bulk-delete";
        $context = ['ids' => $ids, 'user_id' => $userId];

        try {
            $response = $this->getHttpClient()
                ->delete($endpoint, [
                    'ids' => $ids,
                    'deleted_by' => $userId,
                ]);

            if ($response->failed()) {
                $this->handleServiceError($response, $context);
            }

            return $response->json();

        } catch (Throwable $e) {
            $this->handleServiceException($e, $context);
        }
    }

    public function bulkUpdate(array $ids, array $data, int $userId): array
    {
        $endpoint = $this->getBaseEndpoint() . "/api/mappings/bulk-update";
        $context = ['ids' => $ids, 'data' => $data, 'user_id' => $userId];

        try {
            $updateData = array_merge($data, [
                'ids' => $ids,
                'updated_by' => $userId,
            ]);
            
            $response = $this->getHttpClient()
                ->patch($endpoint, $updateData);

            if ($response->failed()) {
                $this->handleServiceError($response, $context);
            }

            return $response->json();

        } catch (Throwable $e) {
            $this->handleServiceException($e, $context);
        }
    }
}
