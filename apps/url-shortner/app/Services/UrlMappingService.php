<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class UrlMappingService
{
    public function find(string $id)
    {
        $result = DB::table('url_mapping')
            ->select('id', 'tenant_id', 'original_url', 'short_code', 'short_url', 'method', 'created_by', 'is_active', 'created_at', 'updated_at')
            ->where('id', $id)
            ->first();

        if (!$result) {
            throw new RuntimeException('Mapping not found');
        }

        return $result;
    }
    
    public function create(
        string $originalUrl,
        int $tenantId,
        int $createdBy,
        string $method = 'POST',
    ): array {

        $existing = DB::table('url_mapping')
            ->where('tenant_id', $tenantId)
            ->where('original_url', $originalUrl)
            ->where('method', strtoupper($method))
            ->first();

        if ($existing) {
            return [
                'short_code' => $existing->short_code,
                'short_url'  => $existing->short_url,
                'duplicate'  => true,
            ];
        }

        $tenant = DB::table('tenants')
            ->select('id', 'slug')
            ->where('id', $tenantId)
            ->first();

        if (!$tenant || empty($tenant->slug)) {
            throw new RuntimeException('Invalid tenant or missing slug');
        }

        do {
            $shortCode = Str::random(8);
        } while (
            DB::table('url_mapping')
                ->where('short_code', $shortCode)
                ->exists()
        );

        $shortUrl = $this->buildGatewayUrl(
            $tenant->slug,
            $shortCode
        );

        DB::table('url_mapping')->insert([
            'tenant_id'    => $tenantId,
            'original_url' => $originalUrl,
            'short_code'   => $shortCode,
            'short_url'    => $shortUrl,
            'method'       => strtoupper($method),
            'created_by'   => $createdBy,
            'is_active'    => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return [
            'short_code' => $shortCode,
            'short_url'  => $shortUrl,
            'duplicate'  => false,
        ];
    }

    /**
     * Build tenant-aware API Gateway URL
     */
    private function buildGatewayUrl(
        string $tenantSlug,
        string $shortCode
    ): string {
        return rtrim(config('services.api_gateway.base_url'), '/')
            . '/'
            . strtolower($tenantSlug)
            . '/'
            . $shortCode;
    }
}
