<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Exception;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::query();

        if ($request->boolean('trashed')) {
             $tenants = $query->onlyTrashed()->orderBy('name')->get();
        } else {
             $tenants = $query
                 ->orderBy('name')
                 ->get();
        }

        return view('tenants.index', [
            'tenants' => $tenants,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|alpha_dash|unique:tenants,slug',
            ]);

            Tenant::create($data);

            return Redirect::route('tenants.index')
                ->with('success', 'API Client created successfully!');

        } catch (Exception $e) {
            Log::error('Tenant creation failed: ' . $e->getMessage(), ['exception' => $e]);
            return Redirect::route('tenants.index')
                ->with('error', 'Failed to create API Client. Please try again.');
        }
    }

    public function update(Request $request, Tenant $tenant)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => "required|string|alpha_dash|unique:tenants,slug,{$tenant->id}",
            ]);

            $tenant->update($data);

            return Redirect::route('tenants.index')
                ->with('success', 'API Client updated successfully!');

        } catch (Exception $e) {
            Log::error('Tenant update failed: ' . $e->getMessage(), ['exception' => $e]);
            return Redirect::route('tenants.index')
                ->with('error', 'Failed to update API Client. Please try again.');
        }
    }

    public function destroy(Tenant $tenant)
    {
        try {
            $tenant->delete();
            
            return Redirect::route('tenants.index')
                ->with('success', 'API Client deleted successfully!');
                
        } catch (Exception $e) {
            Log::error('Tenant deletion failed: ' . $e->getMessage(), ['exception' => $e]);
            return Redirect::route('tenants.index')
                ->with('error', 'Failed to delete API Client. It may have associated data.');
        }
    }

    public function restore($id)
    {
        try {
            $restoredCount = Tenant::withTrashed()->where('id', $id)->restore();
            if ($restoredCount > 0) {
                 return Redirect::back()->with('success', 'API Client restored successfully.');
            }
            return Redirect::back()->with('error', 'API Client not found for restoration or already active.');

        } catch (Exception $e) {
            Log::error('Tenant restoration failed: ' . $e->getMessage(), ['exception' => $e]);
            return Redirect::back()->with('error', 'Failed to restore API Client.');
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $data = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:tenants,id',
            ]);
            
            $deletedCount = Tenant::whereIn('id', $data['ids'])->delete();
            
            return Redirect::back()->with('success', $deletedCount . ' API Clients deleted successfully.');
            
        } catch (Exception $e) {
            Log::error('Tenant bulk deletion failed: ' . $e->getMessage(), ['exception' => $e]);
            return Redirect::back()->with('error', 'Failed to perform bulk delete operation.');
        }
    }

    public function bulkRestore(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:tenants,id',
            ]);
            
            $restoredCount = Tenant::withTrashed()->whereIn('id', $request->ids)->restore();
            
            return Redirect::route('tenants.index')->with('success', $restoredCount . ' API Clients restored successfully.');
            
        } catch (Exception $e) {
            Log::error('Tenant bulk restoration failed: ' . $e->getMessage(), ['exception' => $e]);
            return Redirect::back()->with('error', 'Failed to perform bulk restore operation.');
        }
    }
}
