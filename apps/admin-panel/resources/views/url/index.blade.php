<x-layout>
    <div x-data="urlCrud()" x-init="load()">

        <h1>URLs</h1>
        <button class="btn" @click="openCreate()">+ New URL</button>

        <table class="mt-4">
            <thead>
                <tr>
                    <th>Tenant</th>
                    <th>Short URL</th>
                    <th>Original URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="url in urls" :key="url.id">
                    <tr>
                        <td x-text="url.tenant.name"></td>
                        <td x-text="url.short_url"></td>
                        <td x-text="url.original_url"></td>
                        <td>
                            <button class="btn-danger" @click="remove(url.id)">Delete</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- MODAL -->
        <div x-show="showModal" class="modal-backdrop">
            <div class="modal">
                <h2>New URL</h2>

                <select x-model="form.tenant_id">
                    <option value="">Select Tenant</option>
                    <template x-for="t in tenants" :key="t.id">
                        <option :value="t.id" x-text="t.name"></option>
                    </template>
                </select>

                <input x-model="form.original_url" placeholder="Original URL">

                <div class="mt-4">
                    <button class="btn" @click="save()">Create</button>
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
                selected: [],
                showDeleted: false,
                showModal: false,

                form: {
                    tenant_id: '',
                    original_url: ''
                },

                async load() {
                    const urlEndpoint = this.showDeleted ?
                        '/api/urls?trashed=1' :
                        '/api/urls';

                    this.urls = await fetch(urlEndpoint).then(r => r.json());
                    this.tenants = await fetch('/api/tenants').then(r => r.json());
                },

                openCreate() {
                    this.form = {
                        tenant_id: '',
                        original_url: ''
                    };
                    this.showModal = true;
                },

                close() {
                    this.showModal = false;
                },

                async save() {
                    await fetch('/api/urls', {
                        method: 'POST',
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

                async remove(id) {
                    await fetch(`/api/urls/${id}`, {
                        method: 'DELETE',
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