<x-layout>
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">API Clients Management</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tenantModal" data-action-type="create">
                <i class="bi bi-plus-lg me-1"></i> + New API Client
            </button>
        </div>

        <form id="bulkActionForm" method="POST" action="">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <div class="dropdown me-2">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="bulkActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                            Bulk Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="bulkActionsDropdown">
                            <li><a class="dropdown-item text-danger" href="#" data-bulk-action="delete" data-confirm-message="Are you sure you want to delete the selected API Clients?">Bulk Delete</a></li>
                            @if(request()->boolean('trashed'))
                            <li><a class="dropdown-item" href="#" data-bulk-action="restore" data-confirm-message="Are you sure you want to restore the selected API Clients?">Bulk Restore</a></li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="btn-group" role="group">
                    <a href="{{ route('tenants.index') }}" class="btn btn-sm {{ request()->boolean('trashed') ? 'btn-outline-secondary' : 'btn-secondary' }}">Active</a>
                    <a href="{{ route('tenants.index', ['trashed' => true]) }}" class="btn btn-sm {{ request()->boolean('trashed') ? 'btn-secondary' : 'btn-outline-secondary' }}">Trashed</a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" style="width: 50px;">
                                        <input type="checkbox" id="selectAllCheckboxes" class="form-check-input">
                                    </th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Client ID / Slug</th>
                                    @if(request()->boolean('trashed'))
                                    <th scope="col" style="width: 150px;">Deleted At</th>
                                    @endif
                                    <th scope="col" style="width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenants as $tenant)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $tenant->id }}" class="tenant-checkbox form-check-input">
                                    </td>
                                    <td>{{ $tenant->name }}</td>
                                    <td>{{ $tenant->slug }}</td>
                                    @if(request()->boolean('trashed'))
                                    <td>{{ $tenant->deleted_at->diffForHumans() }}</td>
                                    @endif
                                    <td>
                                        @if(!request()->boolean('trashed'))
                                        <button type="button" class="btn btn-sm btn-outline-info me-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#tenantModal"
                                            data-action-type="edit"
                                            data-id="{{ $tenant->id }}"
                                            data-name="{{ $tenant->name }}"
                                            data-slug="{{ $tenant->slug }}">
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('tenants.destroy', $tenant->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this API Client?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                        @else
                                        <form method="POST" action="{{ route('tenants.restore', $tenant->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to restore this API Client?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">Restore</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ request()->boolean('trashed') ? 5 : 4 }}" class="text-center text-muted p-4">No API clients found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </form>

    </div>

    <div class="modal fade" id="tenantModal" tabindex="-1" aria-labelledby="tenantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="tenantForm" method="POST" action="">
                    @csrf
                    @method('POST')

                    <div class="modal-header">
                        <h5 class="modal-title" id="tenantModalLabel">New API Client</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="tenant-name" class="form-label">Client Name</label>
                            <input id="tenant-name" name="name" type="text" class="form-control" required placeholder="Client Name">
                        </div>

                        <div class="mb-3">
                            <label for="tenant-slug" class="form-label">Client ID/Slug</label>
                            <input id="tenant-slug" name="slug" type="text" class="form-control" required placeholder="unique-client-slug">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/tenants.js') }}"></script>
</x-layout>