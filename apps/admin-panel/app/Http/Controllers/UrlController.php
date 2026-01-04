<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use App\Services\UrlShortnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UrlController
{
    /**
     * List URLs (read-only)
     */
    public function index()
    {
        $urls = DB::table('url_mapping')
            ->latest()
            ->paginate(20);

        return view('url.index', compact('urls'));
    }

    /**
     * Show create URL form
     * (NEW: load tenants for dropdown)
     */
    public function create()
    {
        $tenants = Tenant::orderBy('name')->get();

        return view('url.create', compact('tenants'));
    }

    /**
     * Store new short URL
     * (NEW: accept + forward tenant_id)
     */
    public function store(Request $request, UrlShortnerService $service)
    {
        $validated = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
            'tenant_id'    => ['required', 'exists:tenants,id'],
        ]);

        $response = $service->create(
            $validated['original_url'],
            Auth::id(),
            $validated['tenant_id']
        );
        Log::info('URL Shortner response', [
            'status' => $response['status'],
            'body' => $response['body'],
        ]);
        return redirect()
            ->route('url.index')
            ->with('success', 'Short URL created successfully');
    }
}
