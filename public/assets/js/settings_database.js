/**
 * Settings - Database Backup & Restore UI Logic
 */
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('backup_zip');
    const btnTrigger = document.getElementById('btnTriggerRestore');
    const selectedFileName = document.getElementById('selectedFileName');
    const confirmCheckbox = document.getElementById('confirmCheckbox');
    const btnConfirmSubmit = document.getElementById('btnConfirmSubmit');
    const restoreForm = document.getElementById('restoreForm');
    const modalEl = document.getElementById('confirmRestoreModal');

    if (!fileInput || !btnTrigger || !modalEl) {
        return;
    }

    const restoreModal = new bootstrap.Modal(modalEl);

    fileInput.addEventListener('change', function () {
        if (this.files && this.files.length > 0) {
            btnTrigger.disabled = false;
            btnTrigger.classList.remove('btn-outline-danger');
            btnTrigger.classList.add('btn-danger');
            if (selectedFileName) {
                selectedFileName.textContent = this.files[0].name + ' (' + (this.files[0].size / (1024 * 1024)).toFixed(2) + ' MB)';
            }
        } else {
            btnTrigger.disabled = true;
            btnTrigger.classList.remove('btn-danger');
            btnTrigger.classList.add('btn-outline-danger');
            if (selectedFileName) {
                selectedFileName.textContent = '';
            }
        }
    });

    btnTrigger.addEventListener('click', function () {
        if (confirmCheckbox) {
            confirmCheckbox.checked = false;
        }
        if (btnConfirmSubmit) {
            btnConfirmSubmit.disabled = true;
        }
        restoreModal.show();
    });

    if (confirmCheckbox && btnConfirmSubmit) {
        confirmCheckbox.addEventListener('change', function () {
            btnConfirmSubmit.disabled = !this.checked;
        });
    }

    if (btnConfirmSubmit && restoreForm) {
        btnConfirmSubmit.addEventListener('click', function () {
            const spinner = document.getElementById('btnRestoreSpinner');
            const text = document.getElementById('btnRestoreText');
            if (spinner) {
                spinner.classList.remove('d-none');
            }
            if (text) {
                text.textContent = 'Restoring... Please wait';
            }
            this.disabled = true;
            restoreForm.submit();
        });
    }
});

