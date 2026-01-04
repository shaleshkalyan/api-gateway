<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantController
{
    public function index()
    {
        $tenants = Tenant::latest()->paginate(20);
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:tenants,slug',
        ]);

        Tenant::create($request->only('name', 'slug'));

        return back()->with('success', 'Tenant created');
    }
}

