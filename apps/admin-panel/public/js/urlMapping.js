document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("urlMappingModal");
    const form = document.getElementById("urlMappingForm");
    const modalTitle = document.getElementById("urlMappingModalLabel");
    const saveButton = document.getElementById("saveButton");
    const methodField = form.querySelector('input[name="_method"]');

    const selectAllCheckbox = document.getElementById("selectAllUrls");
    const urlCheckboxes = document.querySelectorAll(".url-checkbox");
    const bulkActionSelect = document.getElementById("bulkActionSelect");
    const bulkActionButton = document.getElementById("bulkActionButton");
    const bulkActionForm = document.getElementById("bulkActionForm");
    const bulkUpdateParam = document.getElementById("bulkUpdateParam");

    if (modal) {
        modal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const action = button.getAttribute("data-action");

            form.reset();
            methodField.value = "POST";

            const storeRoute = form.getAttribute("action");

            if (action === "create") {
                modalTitle.textContent = "New API Mapping";
                form.action = storeRoute;
                saveButton.textContent = "Save Mapping";
            } else if (action === "edit") {
                modalTitle.textContent = "Edit API Mapping";

                const id = button.getAttribute("data-id");
                const indexRoute = button.getAttribute("data-index-route");

                const updateRoute = indexRoute.endsWith("/")
                    ? indexRoute.slice(0, -1) + "/" + id
                    : indexRoute + "/" + id;

                form.action = updateRoute;
                methodField.value = "PUT";
                saveButton.textContent = "Update Mapping";

                document.getElementById("tenant_id").value =
                    button.getAttribute("data-tenant-id");
                document.getElementById("method").value =
                    button.getAttribute("data-method");
                document.getElementById("original_url").value =
                    button.getAttribute("data-original-url");

                const isActive = button.getAttribute("data-is-active") === "1";
                document.getElementById("is_active").checked = isActive;
            }
        });
    }

    function updateBulkControls() {
        const checkedCount = document.querySelectorAll(
            ".url-checkbox:checked"
        ).length;
        bulkActionButton.disabled =
            checkedCount === 0 || bulkActionSelect.value === "";
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            urlCheckboxes.forEach((checkbox) => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkControls();
        });
    }

    urlCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", updateBulkControls);
    });

    if (bulkActionSelect) {
        bulkActionSelect.addEventListener("change", updateBulkControls);
    }

    if (bulkActionForm) {
        bulkActionForm.addEventListener("submit", function (e) {
            if (bulkActionSelect.value === "") {
                e.preventDefault();
                return;
            }

            e.preventDefault();

            if (
                !confirm("Are you sure you want to perform this bulk action?")
            ) {
                return;
            }

            const selectedOption =
                bulkActionSelect.options[bulkActionSelect.selectedIndex];
            const actionParam =
                selectedOption.getAttribute("data-action-param");

            bulkActionForm.action = bulkActionSelect.value;

            bulkUpdateParam.name = "";

            if (bulkActionSelect.value.includes("bulkUpdate")) {
                if (actionParam) {
                    const [key, value] = actionParam.split("=");
                    bulkUpdateParam.name = key;
                    bulkUpdateParam.value = value;
                }
            }

            bulkActionForm.submit();
        });
    }

    updateBulkControls();
});
