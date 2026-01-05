<x-layout>
    <div x-data="tenantCrud()" x-init="load()">

        <h1>Tenants</h1>
        <button class="btn" @click="openCreate()">+ New Tenant</button>

        <table class="mt-4">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="tenant in tenants" :key="tenant.id">
                    <tr>
                        <td x-text="tenant.name"></td>
                        <td x-text="tenant.slug"></td>
                        <td>
                            <button @click="openEdit(tenant)">Edit</button>
                            <button class="btn-danger" @click="remove(tenant.id)">Delete</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- MODAL -->
        <div x-show="showModal" class="modal-backdrop">
            <div class="modal">
                <h2 x-text="form.id ? 'Edit Tenant' : 'New Tenant'"></h2>

                <input x-model="form.name" placeholder="Name">
                <input x-model="form.slug" placeholder="Slug">

                <div class="mt-4">
                    <button class="btn" @click="save()">Save</button>
                    <button @click="close()">Cancel</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function tenantCrud() {
            return {
                tenants: [],
                selected: [],
                showDeleted: false,
                showModal: false,

                form: {
                    id: null,
                    name: '',
                    slug: ''
                },

                async load() {
                    const url = this.showDeleted ?
                        '/admin/api/tenants?trashed=1' :
                        '/admin/api/tenants';

                    const res = await fetch(url);
                    this.tenants = await res.json();
                },

                openCreate() {
                    this.form = {
                        id: null,
                        name: '',
                        slug: ''
                    };
                    this.showModal = true;
                },

                openEdit(tenant) {
                    this.form = {
                        id: tenant.id,
                        name: tenant.name,
                        slug: tenant.slug
                    };
                    this.showModal = true;
                },

                close() {
                    this.showModal = false;
                },

                async save() {
                    const method = this.form.id ? 'PUT' : 'POST';
                    const url = this.form.id ?
                        `/admin/api/tenants/${this.form.id}` :
                        '/admin/api/tenants';

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

                async remove(id) {
                    await fetch(`/admin/api/tenants/${id}`, {
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