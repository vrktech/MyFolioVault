/**
 * MyFolioVault - Main Application Scripts
 * 100% Offline Compatible
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Sidebar Toggle for Mobile & Responsive Views
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }

    // 2. Auto-dismiss standard alerts after 8 seconds if not interacted with
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = bootstrap.Alert.getInstance(alert);
                if (bsAlert) {
                    bsAlert.close();
                }
            }
        }, 8000);
    });

    // 3. Password Visibility Toggle
    const toggleBtn = document.getElementById('togglePassword');
    const passInput = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    if (toggleBtn && passInput && icon) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passInput.getAttribute('type') === 'password';
            passInput.setAttribute('type', isPassword ? 'text' : 'password');
            icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }
});

/**
 * Universal Table Pagination Engine
 * Handles dynamic page sizes [20, 40, 50, 80, 100], responsive page buttons, and filter sync.
 *
 * @param {Object} config
 *   - tableId: string (ID of <table> element)
 *   - rowSelector: string (Optional, default: 'tbody tr:not(.no-pagination)')
 *   - pageSize: number (Optional, default: 20)
 *   - infoId: string (ID of showing info text element)
 *   - sizeSelectId: string (ID of page size <select>)
 *   - controlsId: string (ID of <ul> pagination controls element)
 *   - getMatchingRows: function (Optional custom row filter)
 */
window.initTablePagination = function(config) {
    const table = document.getElementById(config.tableId);
    if (!table) return null;

    const rowSelector = config.rowSelector || 'tbody tr:not(.no-pagination)';
    let pageSize = parseInt(config.pageSize || 20);
    let currentPage = 1;

    const infoEl = document.getElementById(config.infoId);
    const sizeSelectEl = document.getElementById(config.sizeSelectId);
    const controlsEl = document.getElementById(config.controlsId);

    function getVisibleMatchingRows() {
        const allRows = Array.from(table.querySelectorAll(rowSelector));
        if (typeof config.getMatchingRows === 'function') {
            return config.getMatchingRows(allRows);
        }
        return allRows.filter(r => r.dataset.filtered !== 'true' && !r.classList.contains('d-none-filter') && !r.classList.contains('d-none'));
    }

    function render() {
        const rows = getVisibleMatchingRows();
        const total = rows.length;
        const totalPages = Math.max(1, Math.ceil(total / pageSize));

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIdx = (currentPage - 1) * pageSize;
        const endIdx = startIdx + pageSize;

        // Hide all rows from this selector first
        table.querySelectorAll(rowSelector).forEach(r => {
            r.style.display = 'none';
        });

        // Show only current page matching rows
        rows.slice(startIdx, endIdx).forEach(r => {
            r.style.display = '';
        });

        // Update Info Label
        if (infoEl) {
            const startDisplay = total === 0 ? 0 : startIdx + 1;
            const endDisplay = Math.min(endIdx, total);
            infoEl.textContent = `Showing ${startDisplay} to ${endDisplay} of ${total} entries`;
        }

        // Render page navigation controls
        if (controlsEl) {
            controlsEl.innerHTML = '';
            if (totalPages <= 1) return;

            const makeItem = (label, page, disabled, active) => {
                const li = document.createElement('li');
                li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;
                const a = document.createElement('a');
                a.className = 'page-link py-1 px-2.5';
                a.href = 'javascript:void(0)';
                a.innerHTML = label;
                if (!disabled && !active) {
                    a.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = page;
                        render();
                    });
                }
                li.appendChild(a);
                return li;
            };

            // First & Prev
            controlsEl.appendChild(makeItem('&laquo;', 1, currentPage === 1, false));
            controlsEl.appendChild(makeItem('&lsaquo;', currentPage - 1, currentPage === 1, false));

            // Page numbers with ellipsis
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) {
                controlsEl.appendChild(makeItem('1', 1, false, false));
                if (startPage > 2) {
                    const el = document.createElement('li');
                    el.className = 'page-item disabled';
                    el.innerHTML = '<span class="page-link py-1 px-2">...</span>';
                    controlsEl.appendChild(el);
                }
            }

            for (let p = startPage; p <= endPage; p++) {
                controlsEl.appendChild(makeItem(p.toString(), p, false, p === currentPage));
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const el = document.createElement('li');
                    el.className = 'page-item disabled';
                    el.innerHTML = '<span class="page-link py-1 px-2">...</span>';
                    controlsEl.appendChild(el);
                }
                controlsEl.appendChild(makeItem(totalPages.toString(), totalPages, false, false));
            }

            // Next & Last
            controlsEl.appendChild(makeItem('&rsaquo;', currentPage + 1, currentPage === totalPages, false));
            controlsEl.appendChild(makeItem('&raquo;', totalPages, currentPage === totalPages, false));
        }
    }

    if (sizeSelectEl) {
        sizeSelectEl.value = pageSize;
        sizeSelectEl.addEventListener('change', function() {
            pageSize = parseInt(this.value);
            currentPage = 1;
            render();
        });
    }

    render();

    return {
        refresh: () => { currentPage = 1; render(); },
        update: render,
        setPage: (p) => { currentPage = p; render(); }
    };
};
