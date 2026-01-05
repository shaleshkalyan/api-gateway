<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RedirectResolver
{
    public function resolve(string $shortCode): ?object
    {
        return Cache::remember(
            "redirect:{$shortCode}",
            now()->addMinutes(10),
            fn () => DB::table('url_mapping')
                ->where('short_code', $shortCode)
                ->where('is_active', true)
                ->first()
        );
    }
}
