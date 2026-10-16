/**
 * MyFolioVault - Shared Table Sorter Utility
 * Enables dynamic column sorting and dropdown sync across portfolio tables.
 */
window.TableSorter = (function() {
    'use strict';

    function init(options) {
        const {
            tbodyId,
            rowSelector,
            sortSelectId,
            headerSelector,
            numericFields = ['invested', 'current', 'units', 'pnl', 'return_pct', 'avg_buy', 'cmp']
        } = options;

        const tbody = document.getElementById(tbodyId);
        if (!tbody) return null;

        const sortSelect = sortSelectId ? document.getElementById(sortSelectId) : null;
        const sortableHeaders = headerSelector ? document.querySelectorAll(headerSelector) : [];

        function sort(sortBy) {
            const rows = Array.from(tbody.querySelectorAll(rowSelector));
            if (rows.length === 0) return;

            const [field, dir] = sortBy.split('_');
            const isAsc = dir === 'asc';

            rows.sort((a, b) => {
                const isNumeric = numericFields.includes(field);
                if (isNumeric) {
                    const valA = parseFloat(a.dataset[field]) || 0;
                    const valB = parseFloat(b.dataset[field]) || 0;
                    return isAsc ? valA - valB : valB - valA;
                } else {
                    const valA = (a.dataset[field] || '').toLowerCase();
                    const valB = (b.dataset[field] || '').toLowerCase();
                    return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
                }
            });

            rows.forEach(r => tbody.appendChild(r));

            if (sortableHeaders.length > 0) {
                sortableHeaders.forEach(th => {
                    const col = th.dataset.col;
                    const icon = th.querySelector('i');
                    if (icon) {
                        if (col === field) {
                            icon.className = isAsc ? 'bi bi-sort-down text-primary small ms-1' : 'bi bi-sort-up text-primary small ms-1';
                        } else {
                            icon.className = 'bi bi-arrow-down-up text-muted small ms-1';
                        }
                    }
                });
            }
        }

        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                sort(this.value);
            });
        }

        if (sortableHeaders.length > 0) {
            sortableHeaders.forEach(th => {
                th.addEventListener('click', function() {
                    const col = this.dataset.col;
                    const currentVal = sortSelect ? sortSelect.value : '';
                    let newDir = 'asc';
                    if (currentVal.startsWith(col)) {
                        newDir = currentVal.endsWith('asc') ? 'desc' : 'asc';
                    } else if (numericFields.includes(col)) {
                        newDir = 'desc';
                    }
                    const newVal = `${col}_${newDir}`;
                    if (sortSelect) sortSelect.value = newVal;
                    sort(newVal);
                });
            });
        }

        return { sort };
    }

    return { init };
})();

