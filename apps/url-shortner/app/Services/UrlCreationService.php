<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class UrlCreationService
{
    public function create(
        string $originalUrl,
        int $tenantId,
        int $createdBy,
        string $method = 'POST',
    ): array {

        $existing = DB::table('url_mapping')
        ->where('tenant_id', $tenantId)
        ->where('original_url', $originalUrl)
        ->where('method', $method)
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

        if (!$tenant) {
            throw new RuntimeException('Invalid tenant');
        }

        do {
            $shortCode = Str::random(6);
        } while (
            DB::table('url_mapping')
                ->where('tenant_id', $tenantId)
                ->where('short_code', $shortCode)
                ->exists()
        );

        $shortUrl = $this->buildTenantShortUrl(
            $tenant->slug,
            $shortCode
        );

        DB::table('url_mapping')->insert([
            'tenant_id'    => $tenantId,
            'original_url' => $originalUrl,
            'short_code'   => $shortCode,
            'short_url'    => $shortUrl,
            'method'       => $method,
            'created_by'   => $createdBy,
            'is_active'    => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return [
            'short_code' => $shortCode,
            'short_url'  => $shortUrl,
        ];
    }

    private function buildTenantShortUrl(
        string $tenantSlug,
        string $shortCode
    ): string {
        return 'https://' . strtolower($tenantSlug) . '/' . $shortCode;
    }
}
