<x-layout>
    <div class="container-fluid py-4">
        <h1 class="mb-4">Dashboard</h1>

        <div class="row g-4 mb-5">
            
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-uppercase fw-bold small">API Clients</div>
                                <div class="h3 mb-0 fw-bold">{{ $stats['tenants'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-people-fill fa-2x"></i> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white shadow h-100">
                    <div class="card-body">
                        <div class="text-uppercase fw-bold small">Active Clients</div>
                        <div class="h3 mb-0 fw-bold">{{ $stats['active_tenants'] }}</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white shadow h-100">
                    <div class="card-body">
                        <div class="text-uppercase fw-bold small">Total APIs</div>
                        <div class="h3 mb-0 fw-bold">{{ $stats['urls'] }}</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white shadow h-100">
                    <div class="card-body">
                        <div class="text-uppercase fw-bold small">APIs Today</div>
                        <div class="h3 mb-0 fw-bold">{{ $stats['urls_today'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mt-5 mb-3">Recent APIs</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Client</th>
                                <th>Generated API Url</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUrls as $url)
                            <tr>
                                <td>{{ $url->tenant->name ?? 'N/A' }}</td>
                                <td><a href="{{ $url->short_url }}" target="_blank">{{ $url->short_url }}</a></td>
                                <td>{{ $url->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @empty($recentUrls)
                    <p class="text-center text-muted m-3">No recent APIs created.</p>
                @endempty
            </div>
        </div>
    </div>
</x-layout>