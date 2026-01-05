<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RedirectResolver;
use App\Jobs\RecordClickJob;

class RedirectController
{
    public function handle(string $short_code, Request $request, RedirectResolver $resolver)
    {
        $url = $resolver->resolve($short_code);

        abort_if(!$url, 404);

        RecordClickJob::dispatch(
            $url->id,
            $request->ip(),
            substr((string) $request->userAgent(), 0, 255)
        );

        return redirect()->away($url->original_url, 301);
    }
}
