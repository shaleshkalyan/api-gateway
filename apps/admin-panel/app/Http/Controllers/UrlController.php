<?php

namespace App\Http\Controllers;

use App\Models\UrlMapping;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Services\UrlShortnerService;
use Exception;

class UrlController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = UrlMapping::with('tenant');

            if ($request->boolean('trashed')) {
                $urls = $query->onlyTrashed()->latest()->get();
            } else {
                $urls = $query->latest()->get();
            }
            
            $tenants = Tenant::orderBy('name')->get(); 

            return view('url.index', [
                'urls' => $urls,
                'tenants' => $tenants,
            ]);
            
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to load URL mappings. Database error.');
        }
    }

    public function store(Request $request, UrlShortnerService $client)
    {
        try {
            $data = $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
                'original_url' => 'required|url',
                'method' => 'required|in:POST,GET,PUT,DELETE,PATCH',
                'is_active' => 'nullable|boolean', 
            ]);

            $client->store(
                $data['original_url'],
                Auth::id(),
                $data['tenant_id'],
                $data['method'],
                $data['is_active'] ?? true 
            );
            
            return Redirect::route('url.index')->with('success', 'URL Mapping created and short URL generated.');

        } catch (Exception $e) {
            return Redirect::back()->withInput()->with('error', 'Failed to create URL mapping: ' . $e->getMessage());
        }
    }
    
    public function update(Request $request, UrlMapping $url, UrlShortnerService $client)
    {
        try {
            $data = $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
                'original_url' => 'required|url',
                'method' => 'required|in:POST,GET,PUT,DELETE,PATCH',
                'is_active' => 'nullable|boolean', 
            ]);

            $client->update(
                $url->id,
                $data['original_url'],
                Auth::id(),
                $data['tenant_id'],
                $data['method'],
                $data['is_active'] ?? false
            );
            
            return Redirect::route('url.index')->with('success', 'URL Mapping updated successfully.');
            
        } catch (Exception $e) {
            return Redirect::back()->withInput()->with('error', 'Failed to update URL mapping: ' . $e->getMessage());
        }
    }

    public function destroy(UrlMapping $url, UrlShortnerService $client)
    {
        try {
            $client->delete($url->id, Auth::id(), $url->tenant_id);

            return Redirect::back()->with('success', 'URL Mapping deleted successfully.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to delete URL Mapping: ' . $e->getMessage());
        }
    }

    public function restore($id, UrlShortnerService $client)
    {
        try {
            $client->bulkUpdate(
                [$id], 
                ['deleted_at' => null],
                Auth::id()
            );

            return Redirect::back()->with('success', 'URL Mapping restored.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to restore URL Mapping: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request, UrlShortnerService $client)
    {
        try {
            $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

            $result = $client->bulkDelete($request->ids, Auth::id());
            $count = $result['deleted_count'] ?? count($request->ids);

            return Redirect::back()->with('success', $count . ' URL Mappings deleted remotely.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to perform bulk delete: ' . $e->getMessage());
        }
    }

    public function bulkRestore(Request $request, UrlShortnerService $client)
    {
        try {
            $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

            $result = $client->bulkUpdate(
                $request->ids, 
                ['deleted_at' => null], 
                Auth::id()
            );
            $count = $result['updated_count'] ?? count($request->ids);

            return Redirect::back()->with('success', $count . ' URL Mappings restored remotely.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to perform bulk restore: ' . $e->getMessage());
        }
    }
    
    public function toggleStatus(UrlMapping $url, UrlShortnerService $client)
    {
        try {
            $newStatus = !$url->is_active;

            $client->bulkUpdate(
                [$url->id], 
                ['is_active' => $newStatus], 
                Auth::id()
            );
            
            $url->is_active = $newStatus;
            $status = $url->is_active ? 'Activated' : 'Disabled';
            
            return Redirect::back()->with('success', "Mapping successfully {$status}.");
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to toggle mapping status: ' . $e->getMessage());
        }
    }
}
