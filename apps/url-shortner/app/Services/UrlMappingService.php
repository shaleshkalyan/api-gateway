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
            ->select('id', 'tenant_id', 'original_url', 'short_code', 'short_url', 'method', 'created_by', 'is_active', 'created_at', 'updated_at', 'deleted_at')
            ->where('id', $id)
            ->first();

        if (!$result) {
            throw new RuntimeException('Mapping not found');
        }

        return (array) $result;
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

    public function update(
        int $id,
        int $tenantId,
        array $data
    ): void {
        $updateData = collect($data)
            ->put('updated_at', now())
            ->toArray();

        $updated = DB::table('url_mapping')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->update($updateData);

        if ($updated === 0) {
            throw new RuntimeException('URL mapping not found or tenant mismatch');
        }
    }

    public function delete(
        int $id
    ): void {
        $deleted = DB::table('url_mapping')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
            ]);

        if ($deleted === 0) {
            throw new RuntimeException('URL mapping not found or already deleted');
        }
    }
    
    public function bulkDelete(
        array $ids
    ): int {
        return DB::table('url_mapping')
            ->whereIn('id', $ids)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
            ]);
    }
    
    public function bulkUpdate(
        array $ids,
        array $data
    ): int {
        $updateData = collect($data)
            ->put('updated_at', now());
        
        // Handle restoration (set deleted_at to null)
        if (array_key_exists('deleted_at', $data) && is_null($data['deleted_at'])) {
            $updateData->put('deleted_at', null);
        }

        return DB::table('url_mapping')
            ->whereIn('id', $ids)
            ->update($updateData->toArray());
    }

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
