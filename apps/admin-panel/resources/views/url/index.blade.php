<x-layout>
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">API URL Mappings</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#urlMappingModal" data-action="create">
                + New API Mapping
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        <div class="card shadow-sm mt-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Tenant</th>
                                <th>Method</th>
                                <th>Public URL</th>
                                <th>Original URL</th>
                                <th>Status</th>
                                <th style="width: 200px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($urls as $url) 
                                <tr>
                                    <td>{{ $url->tenant->name ?? 'N/A' }}</td>

                                    <td>
                                        <span class="badge bg-primary">{{ $url->method }}</span>
                                    </td>

                                    <td class="font-monospace small">{{ $url->short_url ?? 'Generating…' }}</td>

                                    <td class="text-secondary small">{{ $url->original_url }}</td>

                                    <td>
                                        <span class="{{ $url->is_active ? 'text-success' : 'text-danger' }}">
                                            {{ $url->is_active ? 'Active' : 'Disabled' }}
                                        </span>
                                    </td>

                                    <td>
                                        <button class="btn btn-sm btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#urlMappingModal" 
                                            data-action="edit"
                                            data-id="{{ $url->id }}"
                                            data-tenant-id="{{ $url->tenant_id }}"
                                            data-method="{{ $url->method }}"
                                            data-original-url="{{ $url->original_url }}"
                                            data-is-active="{{ $url->is_active ? '1' : '0' }}"
                                            data-index-route="{{ route('url.index') }}"
                                            >
                                            Edit
                                        </button>
                                        
                                        <form action="{{ route('url.toggleStatus', $url) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $url->is_active ? 'btn-warning' : 'btn-success' }}">
                                                {{ $url->is_active ? 'Disable' : 'Activate' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('url.destroy', $url) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this mapping?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-4">No API URL Mappings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="urlMappingModal" tabindex="-1" aria-labelledby="urlMappingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="urlMappingForm" method="POST" action="{{ route('url.store') }}">
                    @csrf
                    @method('POST')

                    <div class="modal-header">
                        <h5 class="modal-title" id="urlMappingModalLabel">New API Mapping</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label for="tenant_id" class="form-label">Select Tenant</label>
                            <select name="tenant_id" id="tenant_id" class="form-select" required>
                                <option value="">Select Tenant</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="method" class="form-label">HTTP Method</label>
                            <select name="method" id="method" class="form-select" required>
                                <option value="">HTTP Method</option>
                                <option>GET</option>
                                <option>POST</option>
                                <option>PUT</option>
                                <option>PATCH</option>
                                <option>DELETE</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="original_url" class="form-label">Original API URL</label>
                            <input type="url" name="original_url" id="original_url" class="form-control" placeholder="e.g., https://api.service.com/v1/data" required>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveButton">Save Mapping</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/urlMapping.js') }}"></script>
</x-layout>