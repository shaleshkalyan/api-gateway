<x-layout>
    <h1>Dashboard</h1>

    <div class="grid">
        <div class="card">Tenants<br><strong>{{ $stats['tenants'] }}</strong></div>
        <div class="card">Active Tenants<br><strong>{{ $stats['active_tenants'] }}</strong></div>
        <div class="card">Total URLs<br><strong>{{ $stats['urls'] }}</strong></div>
        <div class="card">URLs Today<br><strong>{{ $stats['urls_today'] }}</strong></div>
    </div>

    <h2 class="mt-6">Recent URLs</h2>

    <table>
        <thead>
            <tr>
                <th>Tenant</th>
                <th>Short URL</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentUrls as $url)
            <tr>
                <td>{{ $url->tenant->name }}</td>
                <td>{{ $url->short_url }}</td>
                <td>{{ $url->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layout>