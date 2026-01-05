<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::query();

        if ($request->boolean('trashed')) {
            return $query->onlyTrashed()->orderBy('name')->get();
        }

        return $query
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }

    public function store(Request $request)
    {
        return Tenant::create(
            $request->validate([
                'name' => 'required',
                'slug' => 'required|alpha_dash|unique:tenants,slug',
            ])
        );
    }

    public function update(Request $request, Tenant $tenant)
    {
        $tenant->update(
            $request->validate([
                'name' => 'required',
                'slug' => "required|alpha_dash|unique:tenants,slug,{$tenant->id}",
            ])
        );

        return $tenant;
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return response()->noContent();
    }

    public function restore($id)
    {
        Tenant::withTrashed()->where('id', $id)->restore();
        return response()->noContent();
    }

    public function bulkDelete(Request $request)
    {
        Tenant::whereIn('id', $request->ids)->delete();
        return response()->noContent();
    }

    public function bulkRestore(Request $request)
    {
        Tenant::withTrashed()->whereIn('id', $request->ids)->restore();
        return response()->noContent();
    }
}
