<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
            return Redirect::route('tenants.index')
                ->with('error', 'Failed to delete API Client. It may have associated data.');
        }
    }

    public function restore($id)
    {
        try {
            Tenant::withTrashed()->where('id', $id)->restore();
            
            return Redirect::back()->with('success', 'API Client restored.');

        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to restore API Client.');
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:tenants,id',
            ]);
            
            $deletedCount = Tenant::whereIn('id', $request->ids)->delete();
            
            return Redirect::back()->with('success', $deletedCount . ' API Clients deleted.');
            
        } catch (Exception $e) {
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
            
            Tenant::withTrashed()->whereIn('id', $request->ids)->restore();
            
            return Redirect::back()->with('success', count($request->ids) . ' API Clients restored.');
            
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Failed to perform bulk restore operation.');
        }
    }
}
