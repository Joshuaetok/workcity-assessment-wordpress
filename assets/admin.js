document.addEventListener('DOMContentLoaded', () => {

    // --- Logic for "Select All" checkbox in the projects list ---
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.projects-table .row-checkbox');

    if (selectAllCheckbox && rowCheckboxes.length) {
        selectAllCheckbox.addEventListener('change', function () {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

});