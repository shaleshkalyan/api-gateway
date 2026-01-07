document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('urlMappingModal');
    const form = document.getElementById('urlMappingForm');
    const modalTitle = document.getElementById('urlMappingModalLabel');
    const saveButton = document.getElementById('saveButton');

    const initialButton = document.querySelector('[data-action="edit"]') || document.querySelector('[data-action="create"]');
    
    if (!initialButton) {
        return; 
    }

    const updateRoutePrefix = initialButton.getAttribute('data-index-route');

    modalElement.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const action = button.getAttribute('data-action');
        
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        if (action === 'create') {
            modalTitle.textContent = 'New API Mapping';
            form.action = form.getAttribute('action');
            form.querySelector('input[name="_method"]').value = 'POST';
            form.reset();
            document.getElementById('is_active').checked = true;
            saveButton.textContent = 'Create Mapping';
            
        } else if (action === 'edit') {
            const id = button.getAttribute('data-id');
            const tenantId = button.getAttribute('data-tenant-id');
            const method = button.getAttribute('data-method');
            const originalUrl = button.getAttribute('data-original-url');
            const isActive = button.getAttribute('data-is-active') === '1';

            modalTitle.textContent = 'Edit API Mapping';
            form.action = updateRoutePrefix + '/' + id;
            form.querySelector('input[name="_method"]').value = 'PUT';
            saveButton.textContent = 'Update Mapping';
            
            document.getElementById('tenant_id').value = tenantId;
            document.getElementById('method').value = method;
            document.getElementById('original_url').value = originalUrl;
            document.getElementById('is_active').checked = isActive;
        }
    });
    
    modalElement.addEventListener('hidden.bs.modal', function () {
        form.reset();
    });
});