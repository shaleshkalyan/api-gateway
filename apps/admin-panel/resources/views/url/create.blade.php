<div class="form-group">
    <label class="label">Tenant</label>
    <select name="tenant_id" class="input" required>
        @foreach ($tenants as $tenant)
            <option value="{{ $tenant->id }}">
                {{ $tenant->name }}
            </option>
        @endforeach
    </select>
</div>
