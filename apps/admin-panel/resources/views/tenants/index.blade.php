<x-layout>
    <h1>View All Tenants</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Tenant Name</th>
                <th>Slug</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tenants as $tenant)
                <tr>
                    <td>{{ $tenant->name }}</td>
                    <td>{{ $tenant->slug }}</td>
                    <td>{{ $tenant->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layout>
