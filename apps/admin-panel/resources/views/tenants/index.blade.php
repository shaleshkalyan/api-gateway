<x-layout>
    <h1>Tenants</h1>

    <form method="POST" action="{{ route('tenants.store') }}">
        @csrf

        <div class="form-group">
            <label class="label">Tenant Name</label>
            <input class="input" name="name" required>
        </div>

        <div class="form-group">
            <label class="label">Slug</label>
            <input class="input" name="slug" required>
        </div>

        <button class="btn">Create Tenant</button>
    </form>
</x-layout>
