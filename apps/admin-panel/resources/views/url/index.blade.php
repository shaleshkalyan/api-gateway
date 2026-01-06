<x-layout>
    <div x-data="urlCrud()" x-init="load()">

        <div class="flex justify-between items-center">
            <h1>API URL Mappings</h1>
            <button class="btn" @click="openCreate()">+ New API Mapping</button>
        </div>

        <table class="mt-4">
            <thead>
                <tr>
                    <th>Tenant</th>
                    <th>Method</th>
                    <th>Public URL</th>
                    <th>Original URL</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <template x-for="url in urls" :key="url.id">
                    <tr>
                        <td x-text="url.tenant.name"></td>

                        <td>
                            <span class="badge" x-text="url.method"></span>
                        </td>

                        <td class="font-mono text-sm"
                            x-text="url.short_url ?? 'Generating…'">
                        </td>

                        <td class="text-xs text-gray-600"
                            x-text="url.original_url">
                        </td>

                        <td>
                            <span
                                :class="url.is_active ? 'text-green-600' : 'text-red-600'"
                                x-text="url.is_active ? 'Active' : 'Disabled'">
                            </span>
                        </td>

                        <td>
                            <button class="btn-danger" @click="toggle(url)">
                                Toggle
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- MODAL -->
        <div x-show="showModal" class="modal-backdrop">
            <div class="modal">
                <h2 x-text="form.id ? 'Edit API Mapping' : 'New API Mapping'"></h2>

                <select x-model="form.tenant_id">
                    <option value="">Select Tenant</option>
                    <template x-for="t in tenants" :key="t.id">
                        <option :value="t.id" x-text="t.name"></option>
                    </template>
                </select>

                <select x-model="form.method">
                    <option value="">HTTP Method</option>
                    <option>GET</option>
                    <option>POST</option>
                    <option>PUT</option>
                    <option>PATCH</option>
                    <option>DELETE</option>
                </select>

                <input
                    x-model="form.original_url"
                    placeholder="Original API URL (hidden from clients)"
                >

                <label class="flex items-center mt-2">
                    <input type="checkbox" x-model="form.is_active">
                    <span class="ml-2">Active</span>
                </label>

                <div class="mt-4">
                    <button class="btn" @click="save()">Save</button>
                    <button @click="close()">Cancel</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function urlCrud() {
            return {
                urls: [],
                tenants: [],
                showModal: false,

                form: {
                    id: null,
                    tenant_id: '',
                    method: '',
                    original_url: '',
                    is_active: true
                },

                async load() {
                    this.urls = await fetch('/api/urls').then(r => r.json());
                    this.tenants = await fetch('/api/tenants').then(r => r.json());
                },

                openCreate() {
                    this.form = {
                        id: null,
                        tenant_id: '',
                        method: '',
                        original_url: '',
                        is_active: true
                    };
                    this.showModal = true;
                },

                close() {
                    this.showModal = false;
                },

                async save() {
                    const method = this.form.id ? 'PUT' : 'POST';
                    const url = this.form.id
                        ? `/api/urls/${this.form.id}`
                        : '/api/urls';

                    await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name=csrf-token]')
                                .content
                        },
                        body: JSON.stringify(this.form)
                    });

                    this.close();
                    this.load();
                },

                async toggle(item) {
                    await fetch(`/api/urls/${item.id}/toggle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name=csrf-token]')
                                .content
                        }
                    });

                    this.load();
                }
            }
        }
    </script>
</x-layout>
