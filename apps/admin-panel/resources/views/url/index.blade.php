<x-layout>
    <h1>URLs</h1>

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
</x-layout>
