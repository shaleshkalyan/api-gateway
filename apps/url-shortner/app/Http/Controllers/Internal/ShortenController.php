<?php

namespace App\Http\Controllers\Internal;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ShortenController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
            'tenant_id'    => ['required', 'integer'],
            'created_by'   => ['required', 'integer'],
        ]);

        abort_unless(
            Tenant::where('id', $validated['tenant_id'])->exists(),
            422,
            'Invalid tenant'
        );
        Log::info('ShortenController::store', [
            'original_url' => $validated['original_url'],
            'tenant_id'    => $validated['tenant_id'],
            'created_by'   => $validated['created_by'],
        ]);
        do {
            $shortCode = Str::random(6);
        } while (
            DB::table('url_mapping')
                ->where('short_code', $shortCode)
                ->where('tenant_id', $validated['tenant_id'])
                ->exists()
        );

        DB::table('url_mapping')->insert([
            'tenant_id'    => $validated['tenant_id'],
            'original_url' => $validated['original_url'],
            'short_code'   => $shortCode,
            'created_by'   => $validated['created_by'],
            'is_active'    => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json([
            'short_code' => $shortCode,
            'short_url'  => url("/{$shortCode}"),
        ]);
    }
}
