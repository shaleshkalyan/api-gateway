<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\UrlMapping;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'stats' => [
                'tenants' => Tenant::count(),
                'active_tenants' => Tenant::where('status', 'active')->count(),
                'urls' => UrlMapping::count(),
                'urls_today' => UrlMapping::whereDate('created_at', today())->count(),
            ],
            'recentUrls' => UrlMapping::with('tenant')
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
