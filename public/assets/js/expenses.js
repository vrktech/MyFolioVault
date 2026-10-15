/**
 * MyFolioVault - Expenses Module Scripts
 * 100% Offline Compatible
 */

document.addEventListener('DOMContentLoaded', function () {
    const cfg = window.EXPENSES_CONFIG || {};
    const deleteBaseUrl = (cfg.deleteBaseUrl || 'expenses/delete').replace(/\/+$/, '');

    // Edit Expense modal binding
    const editModalEl = document.getElementById('editExpenseModal');
    const editModal = editModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(editModalEl) : null;

    document.querySelectorAll('.btn-edit-expense').forEach(button => {
        button.addEventListener('click', function () {
            const idField     = document.getElementById('edit_expense_id');
            const dateField   = document.getElementById('edit_expense_date');
            const amountField = document.getElementById('edit_expense_amount');
            const typeField   = document.getElementById('edit_expense_type');
            const moduleField = document.getElementById('edit_expense_module');
            const notesField  = document.getElementById('edit_expense_notes');

            if (idField)     idField.value     = this.dataset.id;
            if (dateField)   dateField.value   = this.dataset.date;
            if (amountField) amountField.value = this.dataset.amount;
            if (typeField)   typeField.value   = this.dataset.type;
            if (moduleField) moduleField.value = this.dataset.module;
            if (notesField)  notesField.value  = this.dataset.notes || '';

            if (editModal) editModal.show();
        });
    });

    // Delete Expense modal binding
    const deleteModalEl = document.getElementById('deleteExpenseModal');
    const deleteModal = deleteModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(deleteModalEl) : null;
    const deleteConfirmBtn = document.getElementById('delete_expense_confirm_btn');
    const deleteDescText = document.getElementById('delete_expense_desc_text');

    document.querySelectorAll('.btn-delete-expense').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const desc = this.dataset.desc || '';
            if (deleteDescText) {
                deleteDescText.textContent = `Are you sure you want to delete expense "${desc}"? This action cannot be undone.`;
            }
            if (deleteConfirmBtn) {
                deleteConfirmBtn.href = `${deleteBaseUrl}/${id}`;
            }
            if (deleteModal) deleteModal.show();
        });
    });
});

