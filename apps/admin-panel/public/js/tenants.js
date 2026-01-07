document.addEventListener('DOMContentLoaded', function () {
    const tenantModal = document.getElementById('tenantModal');
    
    if (!tenantModal) {
        return; 
    }
    
    const routes = {
        store: '/tenants',
        updatePrefix: '/tenants'
    };

    tenantModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const actionType = button.getAttribute('data-action-type');
        
        const modalTitle = tenantModal.querySelector('.modal-title');
        const tenantForm = document.getElementById('tenantForm');
        const nameInput = document.getElementById('tenant-name');
        const slugInput = document.getElementById('tenant-slug');
        const methodInput = tenantForm.querySelector('input[name="_method"]');

        if (actionType === 'create') {
            modalTitle.textContent = 'New Tenant';
            tenantForm.action = routes.store;
            nameInput.value = '';
            slugInput.value = '';
            methodInput.value = 'POST';
        } else if (actionType === 'edit') {
            const tenantId = button.getAttribute('data-id');
            const tenantName = button.getAttribute('data-name');
            const tenantSlug = button.getAttribute('data-slug');
            
            modalTitle.textContent = 'Edit Tenant: ' + tenantName;
            tenantForm.action = routes.updatePrefix + '/' + tenantId;
            nameInput.value = tenantName;
            slugInput.value = tenantSlug;
            methodInput.value = 'PUT';
        }
    });
    
    tenantModal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('tenantForm').reset();
    });
});