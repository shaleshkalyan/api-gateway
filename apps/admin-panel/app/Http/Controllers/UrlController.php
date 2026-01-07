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

            $client->shorten(
                $data['original_url'],
                Auth::id(),
                $data['tenant_id'],
                $data['method'],
                $data['is_active'] ?? true 
            );
            
            return Redirect::route('url.index')->with('success', 'URL Mapping created and short URL generated.');

        } catch (Exception $e) {
            return Redirect::back()->withInput()->with('error', 'Failed to create URL mapping. Please check inputs.');
        }
    }
    
    public function update(Request $request, UrlMapping $url)
    {
        try {
            $data = $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
                'original_url' => 'required|url',
                'method' => 'required|in:POST,GET,PUT,DELETE,PATCH',
                'is_active' => 'nullable|boolean', 
            ]);

            $url->update($data);
            
            return Redirect::route('url.index')->with('success', 'URL Mapping updated successfully.');
            
        } catch (Exception $e) {
            return Redirect::back()->withInput()->with('error', 'Failed to update URL mapping.');
        }
    }

    public function destroy(UrlMapping $url)
    {
        try {
            $url->delete();
            return Redirect::back()->with('success', 'URL Mapping deleted successfully.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to delete URL Mapping. It may have associated records.');
        }
    }

    public function restore($id)
    {
        try {
            UrlMapping::withTrashed()->where('id', $id)->restore();
            return Redirect::back()->with('success', 'URL Mapping restored.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to restore URL Mapping.');
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:url_mappings,id']);
            $count = UrlMapping::whereIn('id', $request->ids)->delete();
            return Redirect::back()->with('success', $count . ' URL Mappings deleted.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to perform bulk delete.');
        }
    }

    public function bulkRestore(Request $request)
    {
        try {
            $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:url_mappings,id']);
            $count = UrlMapping::withTrashed()->whereIn('id', $request->ids)->restore();
            return Redirect::back()->with('success', $count . ' URL Mappings restored.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to perform bulk restore.');
        }
    }
    
    public function toggleStatus(UrlMapping $url)
    {
        try {
            $url->is_active = !$url->is_active;
            $url->save();
            $status = $url->is_active ? 'Activated' : 'Disabled';
            
            return Redirect::back()->with('success', "Mapping successfully {$status}.");
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to toggle mapping status.');
        }
    }
}
