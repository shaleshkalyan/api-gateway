<x-layout>
    <h1>Create Short URL</h1>

    <form method="POST" action="{{ route('url.store') }}">
        @csrf

        <div class="form-group">
            <label class="label">Original URL</label>
            <input class="input" name="original_url" required>
        </div>

        <div class="form-group">
            <label class="label">Tenant</label>
            <select class="input" name="tenant_id" required>
                @foreach ($tenants as $tenant)
                    <option value="{{ $tenant->id }}">
                        {{ $tenant->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn">Create</button>
    </form>
</x-layout>
