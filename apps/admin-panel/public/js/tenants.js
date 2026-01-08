document.addEventListener('DOMContentLoaded', function () {
    const bulkActionForm = document.getElementById('bulkActionForm');
    const selectAll = document.getElementById('selectAllCheckboxes');
    const checkboxes = document.querySelectorAll('.tenant-checkbox');
    const bulkActionsDropdownButton = document.getElementById('bulkActionsDropdown');
    const bulkActionLinks = document.querySelectorAll('[data-bulk-action]');

    const route = '/tenants';

    function updateBulkButtonState() {
        const checkedCount = document.querySelectorAll('.tenant-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkActionsDropdownButton.removeAttribute('disabled');
            bulkActionsDropdownButton.textContent = `Bulk Actions (${checkedCount})`;
        } else {
            bulkActionsDropdownButton.setAttribute('disabled', 'disabled');
            bulkActionsDropdownButton.textContent = 'Bulk Actions';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkButtonState();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!cb.checked && selectAll) {
                selectAll.checked = false;
            }
            if (selectAll && document.querySelectorAll('.tenant-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateBulkButtonState();
        });
    });

    bulkActionLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const action = this.getAttribute('data-bulk-action');
            const confirmMessage = this.getAttribute('data-confirm-message');

            if (confirm(confirmMessage)) {
                bulkActionForm.action = `${route}/bulk-${action}`;
                bulkActionForm.submit();
            }
        });
    });

    const tenantModal = document.getElementById('tenantModal');
    if (tenantModal) {
        const tenantForm = document.getElementById('tenantForm');
        const modalTitle = document.getElementById('tenantModalLabel');
        const nameInput = document.getElementById('tenant-name');
        const slugInput = document.getElementById('tenant-slug');
        const methodInput = tenantForm.querySelector('input[name="_method"]');

        tenantModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const actionType = button.getAttribute('data-action-type');

            tenantForm.reset();

            if (actionType === 'create') {
                modalTitle.textContent = 'New API Client';
                tenantForm.action = route;
                methodInput.value = 'POST';
                slugInput.removeAttribute('readonly');
            } else if (actionType === 'edit') {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const slug = button.getAttribute('data-slug');

                modalTitle.textContent = 'Edit API Client';
                tenantForm.action = `${route}/${id}`;
                methodInput.value = 'PUT';
                nameInput.value = name;
                slugInput.value = slug;
                slugInput.setAttribute('readonly', 'readonly');
            }
        });
    }

    updateBulkButtonState();
});