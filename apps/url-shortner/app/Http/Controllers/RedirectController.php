<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedirectController
{
    public function handle(string $short_code, Request $request)
    {
        $url = DB::table('url_mapping')
            ->where('short_code', $short_code)
            ->where('is_active', true)
            ->first();

        abort_if(!$url, 404);

        DB::table('url_monitoring')->insert([
            'url_id'     => $url->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'created_at' => now(),
        ]);

        return redirect()->away($url->original_url, 301);
    }
}
