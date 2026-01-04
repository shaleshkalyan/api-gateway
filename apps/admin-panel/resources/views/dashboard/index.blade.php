<x-layout>
    <h1>Dashboard</h1>

    <p class="mt-4">
        Welcome back, <strong>{{ auth()->user()->name }}</strong>
    </p>

    <div class="mt-6">
        <h2>Tenants</h2>

        @if($tenants->isEmpty())
            <p>No tenants found.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tenants as $tenant)
                        <tr>
                            <td>{{ $tenant->name }}</td>
                            <td>{{ $tenant->slug }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <a href="{{ route('tenants.index') }}" class="btn mt-4">
                View all tenants
            </a>
        @endif
    </div>

    <div class="mt-6">
        <h2>Recent URL Mappings</h2>

        @if($urls->isEmpty())
            <p>No URLs created yet.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Short Code</th>
                        <th>Original URL</th>
                        <th>Tenant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($urls as $url)
                        <tr>
                            <td>{{ $url->short_code }}</td>
                            <td>{{ $url->original_url }}</td>
                            <td>{{ $url->tenant_id }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <a href="{{ route('url.index') }}" class="btn mt-4">
                View all URLs
            </a>
        @endif
    </div>
</x-layout>
