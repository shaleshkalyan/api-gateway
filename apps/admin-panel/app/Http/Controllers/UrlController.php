<?php

namespace App\Http\Controllers;

use App\Models\UrlMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\UrlShortnerService;

class UrlController extends Controller
{
    public function index(Request $request)
    {
        $query = UrlMapping::with('tenant');

        if ($request->boolean('trashed')) {
            return $query->onlyTrashed()->latest()->get();
        }

        return $query->latest()->get();
    }

    public function store(Request $request, UrlShortnerService $client)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'original_url' => 'required|url',
            'method' => 'required|in:POST,GET,PUT,DELETE,PATCH',
        ]);

        return $client->shorten(
            $data['original_url'],
            Auth::id(),
            $data['tenant_id'],
            $data['method'],
        );
    }

    public function destroy(UrlMapping $url)
    {
        $url->delete();
        return response()->noContent();
    }

    public function restore($id)
    {
        UrlMapping::withTrashed()->where('id', $id)->restore();
        return response()->noContent();
    }

    public function bulkDelete(Request $request)
    {
        UrlMapping::whereIn('id', $request->ids)->delete();
        return response()->noContent();
    }

    public function bulkRestore(Request $request)
    {
        UrlMapping::withTrashed()->whereIn('id', $request->ids)->restore();
        return response()->noContent();
    }
}
