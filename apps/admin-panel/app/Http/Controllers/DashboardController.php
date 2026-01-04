<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class DashboardController
{
    public function index()
    {
        $urls = DB::table('url_mapping')
            ->latest()
            ->limit(10)
            ->get();

        $tenants = Tenant::latest()
            ->limit(10)
            ->get();

        return view('dashboard.index', compact('urls', 'tenants'));
    }
}
