/**
 * Investment Portfolio Tracker - Equities Module Scripts
 * 100% Offline Compatible
 */

document.addEventListener('DOMContentLoaded', function() {
    let currentStock = {};

    const cfg = window.EQUITIES_CONFIG || {};
    const currentFyStart = cfg.currentFyStart || '';
    const currentFyEnd   = cfg.currentFyEnd || '';
    const lastFyStart    = cfg.lastFyStart || '';
    const lastFyEnd      = cfg.lastFyEnd || '';
    const checkEligibilityUrl = cfg.checkEligibilityUrl || '';

    // ==========================================
    // 1. MODAL DATA BINDINGS
    // ==========================================

    // Hook Edit Stock buttons
    document.querySelectorAll('.open-edit-stock-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editStockId');
            const elSym = document.getElementById('editStockSymbol');
            const elName = document.getElementById('editStockName');
            const elIsin = document.getElementById('editStockIsin');
            const elSec = document.getElementById('editStockSector');
            const elEx = document.getElementById('editStockExchange');

            if (elId) elId.value = this.dataset.id;
            if (elSym) elSym.value = this.dataset.symbol;
            if (elName) elName.value = this.dataset.name;
            if (elIsin) elIsin.value = this.dataset.isin || '';
            if (elSec) elSec.value = this.dataset.sector || '';
            if (elEx) elEx.value = this.dataset.exchange || 'NSE';
        });
    });

    // Hook Edit Transaction buttons
    document.querySelectorAll('.open-edit-equity-trans').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editEquityTransId');
            const elTitle = document.getElementById('editEquityTransTitle');
            const elDate = document.getElementById('editEquityTransDate');
            const elQty = document.getElementById('editEquityTransQty');
            const elPrice = document.getElementById('editEquityTransPrice');
            const elBrok = document.getElementById('editEquityTransBrokerage');
            const elStt = document.getElementById('editEquityTransStt');
            const elNotes = document.getElementById('editEquityTransNotes');

            if (elId) elId.value = this.dataset.id;
            if (elTitle) elTitle.textContent = 'Edit ' + this.dataset.type + ' Order: ' + this.dataset.symbol;
            if (elDate) elDate.value = this.dataset.date;
            if (elQty) elQty.value = this.dataset.qty;
            if (elPrice) elPrice.value = parseFloat(this.dataset.price).toFixed(2);
            if (elBrok) elBrok.value = parseFloat(this.dataset.brokerage || 0).toFixed(2);
            if (elStt) elStt.value = parseFloat(this.dataset.stt || 0).toFixed(2);
            if (elNotes) elNotes.value = this.dataset.notes || '';
        });
    });

    // Hook Edit Dividend buttons
    document.querySelectorAll('.open-edit-dividend').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editEquityDivId');
            const elTitle = document.getElementById('editEquityDivTitle');
            const elDate = document.getElementById('editEquityDivDate');
            const elType = document.getElementById('editEquityDivType');
            const elShares = document.getElementById('editEquityDivShares');
            const elPerShare = document.getElementById('editEquityDivPerShare');
            const elTotal = document.getElementById('editEquityDivTotal');
            const elTds = document.getElementById('editEquityDivTds');
            const elNotes = document.getElementById('editEquityDivNotes');

            if (elId) elId.value = this.dataset.id;
            if (elTitle) elTitle.textContent = 'Edit Dividend: ' + this.dataset.symbol;
            if (elDate) elDate.value = this.dataset.date;
            if (elType) elType.value = this.dataset.type;
            if (elShares) elShares.value = this.dataset.shares || '';
            if (elPerShare) elPerShare.value = this.dataset.perShare ? parseFloat(this.dataset.perShare).toFixed(2) : '';
            if (elTotal) elTotal.value = parseFloat(this.dataset.total).toFixed(2);
            if (elTds) elTds.value = parseFloat(this.dataset.tds || 0).toFixed(2);
            if (elNotes) elNotes.value = this.dataset.notes || '';
        });
    });

    // Hook "Add Trans" buttons to populate modal
    document.querySelectorAll('.open-trans-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentStock = {
                id: this.dataset.id,
                symbol: this.dataset.symbol,
                name: this.dataset.name,
                qty: parseInt(this.dataset.qty) || 0,
                cmp: parseFloat(this.dataset.cmp) || 0,
                avgPrice: parseFloat(this.dataset.avg) || 0
            };

            const elId = document.getElementById('modalEquityId');
            const elSym = document.getElementById('modalStockSymbol');
            const elSub = document.getElementById('modalStockSubtitle');
            const elBuyP = document.getElementById('buyPrice');
            const elSellP = document.getElementById('sellPrice');
            const elBadge = document.getElementById('sellAvailableQtyBadge');
            const elSellQ = document.getElementById('sellQty');
            const elDivS = document.getElementById('divSharesHeld');
            const elActBuy = document.getElementById('actBuy');

            if (elId) elId.value = currentStock.id;
            if (elSym) elSym.textContent = currentStock.symbol;
            if (elSub) elSub.textContent = currentStock.name + ' (Held: ' + currentStock.qty + ' shares)';
            
            if (elBuyP) elBuyP.value = currentStock.cmp.toFixed(2);
            if (elSellP) elSellP.value = currentStock.cmp.toFixed(2);
            if (elBadge) elBadge.textContent = currentStock.qty + ' shares';
            if (elSellQ) elSellQ.max = currentStock.qty;
            if (elDivS) elDivS.value = currentStock.qty;

            if (elActBuy) elActBuy.checked = true;
            switchAction('BUY');
        });
    });

    // Action Tab Switching (Buy More / Sell / Dividend)
    const actRadios = document.querySelectorAll('input[name="action_type"]');
    actRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            switchAction(this.value);
        });
    });

    function switchAction(type) {
        const pBuy = document.getElementById('panelBuy');
        const pSell = document.getElementById('panelSell');
        const pDiv = document.getElementById('panelDividend');
        const submitBtn = document.getElementById('btnSubmitTrans');

        if (!pBuy || !pSell || !pDiv || !submitBtn) return;

        if (type === 'BUY') {
            pBuy.classList.remove('d-none');
            pSell.classList.add('d-none');
            pDiv.classList.add('d-none');
            setInputsDisabled(pBuy, false);
            setInputsDisabled(pSell, true);
            setInputsDisabled(pDiv, true);
            submitBtn.className = 'btn btn-success btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Confirm Purchase Lot';
        } else if (type === 'SELL') {
            pBuy.classList.add('d-none');
            pSell.classList.remove('d-none');
            pDiv.classList.add('d-none');
            setInputsDisabled(pBuy, true);
            setInputsDisabled(pSell, false);
            setInputsDisabled(pDiv, true);
            submitBtn.className = 'btn btn-danger btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Execute FIFO Sell';
        } else if (type === 'DIVIDEND') {
            pBuy.classList.add('d-none');
            pSell.classList.add('d-none');
            pDiv.classList.remove('d-none');
            setInputsDisabled(pBuy, true);
            setInputsDisabled(pSell, true);
            setInputsDisabled(pDiv, false);
            submitBtn.className = 'btn btn-primary btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Record Dividend';
        }
    }

    function setInputsDisabled(container, disabled) {
        if (!container) return;
        container.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    // Live calculations in modal
    const buyQty = document.getElementById('buyQty');
    const buyPrice = document.getElementById('buyPrice');
    const buyCharges = document.getElementById('buyCharges');
    const buyPreview = document.getElementById('buyTotalPreview');

    function calcBuy() {
        if (!buyPreview) return;
        const q = parseFloat(buyQty ? buyQty.value : 0) || 0;
        const p = parseFloat(buyPrice ? buyPrice.value : 0) || 0;
        const c = parseFloat(buyCharges ? buyCharges.value : 0) || 0;
        const total = (q * p) + c;
        buyPreview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    if (buyQty && buyPrice && buyCharges) {
        [buyQty, buyPrice, buyCharges].forEach(el => el.addEventListener('input', calcBuy));
    }

    const sellQty = document.getElementById('sellQty');
    const sellPrice = document.getElementById('sellPrice');
    const sellCharges = document.getElementById('sellCharges');
    const sellPreview = document.getElementById('sellTotalPreview');
    const sellPnlPreview = document.getElementById('sellPnlPreview');

    function calcSell() {
        if (!sellPreview) return;
        const q = parseFloat(sellQty ? sellQty.value : 0) || 0;
        const p = parseFloat(sellPrice ? sellPrice.value : 0) || 0;
        const c = parseFloat(sellCharges ? sellCharges.value : 0) || 0;
        const netProceeds = (q * p) - c;
        const costBasis = q * (currentStock.avgPrice || 0);
        const estGain = netProceeds - costBasis;

        sellPreview.textContent = '₹ ' + netProceeds.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (sellPnlPreview) {
            const sign = estGain >= 0 ? '+' : '';
            sellPnlPreview.textContent = sign + '₹ ' + estGain.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            sellPnlPreview.className = 'small fw-semibold ' + (estGain >= 0 ? 'text-success' : 'text-danger');
        }
    }
    if (sellQty && sellPrice && sellCharges) {
        [sellQty, sellPrice, sellCharges].forEach(el => el.addEventListener('input', calcSell));
    }

    // ==========================================
    // 2. FINANCIAL YEAR & FORMATTING UTILITIES
    // ==========================================
    function matchFy(dateStr, fyMode) {
        if (!fyMode || fyMode === 'ALL') return true;
        if (fyMode === 'CURRENT_FY') return dateStr >= currentFyStart && dateStr <= currentFyEnd;
        if (fyMode === 'LAST_FY') return dateStr >= lastFyStart && dateStr <= lastFyEnd;
        return true;
    }

    function formatINR(amount) {
        return '₹ ' + Number(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ==========================================
    // 3. TRADE LEDGER DUAL FILTER & KPI
    // ==========================================
    const ledgerStockFilter  = document.getElementById('ledgerStockFilter');
    const ledgerDateFilter   = document.getElementById('ledgerDateFilter');
    const ledgerTable        = document.getElementById('ledgerTransactionsTable');
    const ledgerResetBtn     = document.getElementById('btnResetStockFilter');
    const ledgerBadge        = document.getElementById('filteredStockBadge');
    const ledgerSymbolSpan   = document.getElementById('filteredStockSymbol');
    const ledgerNoRowsEl     = document.getElementById('ledgerNoRows');

    const kpiBuyAmount       = document.getElementById('kpiBuyAmount');
    const kpiBuyDetails      = document.getElementById('kpiBuyDetails');
    const kpiSellAmount      = document.getElementById('kpiSellAmount');
    const kpiSellDetails     = document.getElementById('kpiSellDetails');
    const kpiNetOutlay       = document.getElementById('kpiNetOutlay');
    const kpiNetDetails      = document.getElementById('kpiNetDetails');
    const kpiCharges         = document.getElementById('kpiCharges');

    const footerSummaryTitle    = document.getElementById('footerSummaryTitle');
    const footerActionBreakdown = document.getElementById('footerActionBreakdown');
    const footerTotalQty        = document.getElementById('footerTotalQty');
    const footerTotalCharges    = document.getElementById('footerTotalCharges');
    const footerTotalAmount     = document.getElementById('footerTotalAmount');
    const footerNote            = document.getElementById('footerNote');

    function applyLedgerFilters(updateUrl = true) {
        if (!ledgerTable) return;

        const selectedStock = ledgerStockFilter ? ledgerStockFilter.value : 'ALL';
        const selectedFy    = ledgerDateFilter ? ledgerDateFilter.value : 'ALL';

        const rows = ledgerTable.querySelectorAll('tbody tr.ledger-trans-row');
        let visibleCount = 0;
        let buyAmount = 0;
        let sellAmount = 0;
        let buyQty = 0;
        let sellQty = 0;
        let buyCount = 0;
        let sellCount = 0;
        let totalCharges = 0;
        let selectedSymbol = '';

        if (selectedStock !== 'ALL' && ledgerStockFilter) {
            const opt = ledgerStockFilter.querySelector(`option[value="${selectedStock}"]`);
            if (opt) {
                selectedSymbol = opt.dataset.symbol || opt.textContent.split('—')[0].trim();
            }
        }

        rows.forEach(row => {
            const rowStockId = row.dataset.equityId;
            const rowDate    = row.dataset.date;

            const matchesStock = (selectedStock === 'ALL' || rowStockId === selectedStock);
            const matchesDate  = matchFy(rowDate, selectedFy);

            if (matchesStock && matchesDate) {
                row.classList.remove('d-none');
                visibleCount++;

                const type = row.dataset.type;
                const qty = parseInt(row.dataset.qty) || 0;
                const amt = parseFloat(row.dataset.amount) || 0;
                const charges = parseFloat(row.dataset.charges) || 0;
                totalCharges += charges;

                if (type === 'BUY') {
                    buyAmount += amt;
                    buyQty += qty;
                    buyCount++;
                } else {
                    sellAmount += amt;
                    sellQty += qty;
                    sellCount++;
                }
            } else {
                row.classList.add('d-none');
            }
        });

        if (ledgerNoRowsEl) {
            ledgerNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        const netOutlay = buyAmount - sellAmount;

        if (kpiBuyAmount) kpiBuyAmount.textContent = formatINR(buyAmount);
        if (kpiBuyDetails) kpiBuyDetails.textContent = `${buyCount} buys • ${buyQty.toLocaleString('en-IN')} shares`;

        if (kpiSellAmount) kpiSellAmount.textContent = formatINR(sellAmount);
        if (kpiSellDetails) kpiSellDetails.textContent = `${sellCount} sells • ${sellQty.toLocaleString('en-IN')} shares`;

        if (kpiNetOutlay) {
            kpiNetOutlay.textContent = formatINR(netOutlay);
            kpiNetOutlay.className = 'fw-bold mb-0 mt-1 ' + (netOutlay >= 0 ? 'text-primary' : 'text-success');
        }
        if (kpiNetDetails) {
            if (selectedStock === 'ALL') {
                kpiNetDetails.textContent = 'Net cash invested in period';
            } else {
                const heldDiff = buyQty - sellQty;
                kpiNetDetails.textContent = `Net held from trades: ${heldDiff.toLocaleString('en-IN')} shares`;
            }
        }

        if (kpiCharges) kpiCharges.textContent = formatINR(totalCharges);

        if (footerSummaryTitle) {
            let label = selectedStock === 'ALL' ? 'Total' : selectedSymbol;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerSummaryTitle.textContent = `${label} (${visibleCount} Trades)`;
        }

        if (footerActionBreakdown) {
            footerActionBreakdown.innerHTML = `<span class="text-success">${buyCount} BUY</span> &bull; <span class="text-danger">${sellCount} SELL</span>`;
        }

        if (footerTotalQty) footerTotalQty.textContent = (buyQty + sellQty).toLocaleString('en-IN');
        if (footerTotalCharges) footerTotalCharges.textContent = formatINR(totalCharges);
        if (footerTotalAmount) {
            footerTotalAmount.textContent = formatINR(netOutlay);
            footerTotalAmount.className = 'text-end fw-bold fs-6 ' + (netOutlay >= 0 ? 'text-dark' : 'text-success');
        }
        if (footerNote) footerNote.textContent = netOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized';

        const hasActiveFilter = (selectedStock !== 'ALL' || selectedFy !== 'ALL');
        if (ledgerResetBtn) ledgerResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (ledgerBadge) {
            ledgerBadge.classList.toggle('d-none', !hasActiveFilter);
            if (ledgerSymbolSpan) {
                let badgeText = selectedStock !== 'ALL' ? selectedSymbol : 'All Stocks';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                ledgerSymbolSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedStock === 'ALL') url.searchParams.delete('stock_id');
            else url.searchParams.set('stock_id', selectedStock);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }

        if (window.equityLedgerPager) {
            window.equityLedgerPager.refresh();
        }
    }

    if (ledgerStockFilter) ledgerStockFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerDateFilter) ledgerDateFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerResetBtn) {
        ledgerResetBtn.addEventListener('click', () => {
            if (ledgerStockFilter) ledgerStockFilter.value = 'ALL';
            if (ledgerDateFilter) ledgerDateFilter.value = 'ALL';
            applyLedgerFilters(true);
        });
    }

    // ==========================================
    // 4. DIVIDENDS DUAL FILTER & KPI
    // ==========================================
    const divStockFilter  = document.getElementById('divStockFilter');
    const divDateFilter   = document.getElementById('divDateFilter');
    const divTable        = document.getElementById('dividendsTable');
    const divResetBtn     = document.getElementById('btnResetDivFilter');
    const divBadge        = document.getElementById('filteredDivBadge');
    const divSymbolSpan   = document.getElementById('filteredDivSymbol');
    const divNoRowsEl     = document.getElementById('divNoRows');

    const kpiDivGross         = document.getElementById('kpiDivGross');
    const kpiDivGrossSubtitle = document.getElementById('kpiDivGrossSubtitle');
    const kpiDivTds           = document.getElementById('kpiDivTds');
    const kpiDivNet           = document.getElementById('kpiDivNet');
    const kpiDivCount         = document.getElementById('kpiDivCount');

    const footerDivSummaryTitle = document.getElementById('footerDivSummaryTitle');
    const footerDivShares       = document.getElementById('footerDivShares');
    const footerDivTds          = document.getElementById('footerDivTds');
    const footerDivGross        = document.getElementById('footerDivGross');
    const footerDivNet          = document.getElementById('footerDivNet');

    function applyDividendFilters(updateUrl = true) {
        if (!divTable) return;

        const selectedStock = divStockFilter ? divStockFilter.value : 'ALL';
        const selectedFy    = divDateFilter ? divDateFilter.value : 'ALL';

        const rows = divTable.querySelectorAll('tbody tr.div-trans-row');
        let visibleCount = 0;
        let totalGross = 0;
        let totalTds = 0;
        let totalNet = 0;
        let totalShares = 0;
        let selectedSymbol = '';

        if (selectedStock !== 'ALL' && divStockFilter) {
            const opt = divStockFilter.querySelector(`option[value="${selectedStock}"]`);
            if (opt) selectedSymbol = opt.dataset.symbol || opt.textContent.split('(')[0].trim();
        }

        rows.forEach(row => {
            const rowStockId = row.dataset.equityId;
            const rowDate    = row.dataset.date;

            const matchesStock = (selectedStock === 'ALL' || rowStockId === selectedStock);
            const matchesDate  = matchFy(rowDate, selectedFy);

            if (matchesStock && matchesDate) {
                row.classList.remove('d-none');
                visibleCount++;

                const gross = parseFloat(row.dataset.gross) || 0;
                const tds   = parseFloat(row.dataset.tds) || 0;
                const net   = parseFloat(row.dataset.net) || (gross - tds);
                const shares = parseInt(row.dataset.shares) || 0;

                totalGross += gross;
                totalTds += tds;
                totalNet += net;
                totalShares += shares;
            } else {
                row.classList.add('d-none');
            }
        });

        if (divNoRowsEl) {
            divNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        if (kpiDivGross) kpiDivGross.textContent = formatINR(totalGross);
        if (kpiDivGrossSubtitle) kpiDivGrossSubtitle.textContent = `${visibleCount} payouts recorded`;
        if (kpiDivTds) kpiDivTds.textContent = formatINR(totalTds);
        if (kpiDivNet) kpiDivNet.textContent = formatINR(totalNet);
        if (kpiDivCount) kpiDivCount.textContent = visibleCount;

        if (footerDivSummaryTitle) {
            let label = selectedStock === 'ALL' ? 'Total' : selectedSymbol;
            if (selectedFy !== 'ALL') label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            footerDivSummaryTitle.textContent = `${label} (${visibleCount} Payouts)`;
        }
        if (footerDivShares) footerDivShares.textContent = totalShares > 0 ? totalShares.toLocaleString('en-IN') : '—';
        if (footerDivTds) footerDivTds.textContent = formatINR(totalTds);
        if (footerDivGross) footerDivGross.textContent = formatINR(totalGross);
        if (footerDivNet) footerDivNet.textContent = formatINR(totalNet);

        const hasActiveFilter = (selectedStock !== 'ALL' || selectedFy !== 'ALL');
        if (divResetBtn) divResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (divBadge) {
            divBadge.classList.toggle('d-none', !hasActiveFilter);
            if (divSymbolSpan) {
                let badgeText = selectedStock !== 'ALL' ? selectedSymbol : 'All Stocks';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                divSymbolSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedStock === 'ALL') url.searchParams.delete('div_stock_id');
            else url.searchParams.set('div_stock_id', selectedStock);

            if (selectedFy === 'ALL') url.searchParams.delete('div_fy');
            else url.searchParams.set('div_fy', selectedFy);

            window.history.replaceState({}, '', url);
        }

        if (window.equityDivPager) {
            window.equityDivPager.refresh();
        }
    }

    if (divStockFilter) divStockFilter.addEventListener('change', () => applyDividendFilters(true));
    if (divDateFilter) divDateFilter.addEventListener('change', () => applyDividendFilters(true));
    if (divResetBtn) {
        divResetBtn.addEventListener('click', () => {
            if (divStockFilter) divStockFilter.value = 'ALL';
            if (divDateFilter) divDateFilter.value = 'ALL';
            applyDividendFilters(true);
        });
    }

    // ==========================================
    // 5. EQUITIES HOLDINGS SORTING
    // ==========================================
    const sortSelect = document.getElementById('sortEquitiesSelect');
    const holdingsTbody = document.getElementById('equitiesHoldingsTbody');
    const sortableHeaders = document.querySelectorAll('.sortable-equity-th');

    function sortEquitiesHoldings(sortBy) {
        if (!holdingsTbody) return;
        const rows = Array.from(holdingsTbody.querySelectorAll('tr.equity-holding-row'));
        if (rows.length === 0) return;

        const [field, dir] = sortBy.split('_');
        const isAsc = dir === 'asc';

        rows.sort((a, b) => {
            let valA, valB;
            if (field === 'name') {
                valA = (a.dataset.name || '').toLowerCase();
                valB = (b.dataset.name || '').toLowerCase();
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (field === 'sector') {
                valA = (a.dataset.sector || '').toLowerCase();
                valB = (b.dataset.sector || '').toLowerCase();
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (field === 'invested') {
                valA = parseFloat(a.dataset.invested) || 0;
                valB = parseFloat(b.dataset.invested) || 0;
                return isAsc ? valA - valB : valB - valA;
            } else if (field === 'current') {
                valA = parseFloat(a.dataset.current) || 0;
                valB = parseFloat(b.dataset.current) || 0;
                return isAsc ? valA - valB : valB - valA;
            }
            return 0;
        });

        rows.forEach(r => holdingsTbody.appendChild(r));

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

    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortEquitiesHoldings(this.value);
        });
    }

    sortableHeaders.forEach(th => {
        th.addEventListener('click', function() {
            const col = this.dataset.col;
            const currentVal = sortSelect ? sortSelect.value : '';
            let newDir = 'asc';
            if (currentVal.startsWith(col)) {
                newDir = currentVal.endsWith('asc') ? 'desc' : 'asc';
            } else if (col === 'invested' || col === 'current') {
                newDir = 'desc';
            }
            const newVal = `${col}_${newDir}`;
            if (sortSelect) sortSelect.value = newVal;
            sortEquitiesHoldings(newVal);
        });
    });

    // ==========================================
    // 6. FIFO CAPITAL GAINS TAX LOG FILTERING
    // ==========================================
    const taxStockFilter = document.getElementById('taxLogStockFilter');
    const taxTypeFilter  = document.getElementById('taxLogTypeFilter');
    const taxDateFilter  = document.getElementById('taxLogDateFilter');
    const taxResetBtn    = document.getElementById('btnResetTaxFilter');
    const taxBadge       = document.getElementById('filteredTaxBadge');
    const taxSymbolSpan  = document.getElementById('filteredTaxSymbolSpan');
    const taxTable       = document.getElementById('equitiesTaxLogTable');

    const kpiTaxQty       = document.getElementById('kpiTaxQty');
    const kpiTaxQtyCount  = document.getElementById('kpiTaxQtyCount');
    const kpiTaxCost      = document.getElementById('kpiTaxCost');
    const kpiTaxProceeds  = document.getElementById('kpiTaxProceeds');
    const kpiTaxGain      = document.getElementById('kpiTaxGain');
    const kpiTaxBreakdown = document.getElementById('kpiTaxBreakdown');

    const footerTaxSummaryTitle = document.getElementById('footerTaxSummaryTitle');
    const footerTaxBreakdown    = document.getElementById('footerTaxBreakdown');
    const footerTaxQty          = document.getElementById('footerTaxQty');
    const footerTaxCost         = document.getElementById('footerTaxCost');
    const footerTaxProceeds     = document.getElementById('footerTaxProceeds');
    const footerTaxGain         = document.getElementById('footerTaxGain');

    function applyEquityTaxFilters(updateUrl = true) {
        if (!taxTable) return;

        const selectedStock = taxStockFilter ? taxStockFilter.value : 'ALL';
        const selectedType  = taxTypeFilter ? taxTypeFilter.value : 'ALL';
        const selectedFy    = taxDateFilter ? taxDateFilter.value : 'ALL';

        const rows = taxTable.querySelectorAll('tbody tr.equity-taxlog-row');
        let visibleCount = 0;
        let totalMatchedQty = 0;
        let totalBuyCost = 0;
        let totalSellProceeds = 0;
        let totalRealizedGain = 0;
        let totalStcg = 0;
        let totalLtcg = 0;
        let selectedSymbol = '';

        if (selectedStock !== 'ALL' && taxStockFilter) {
            const opt = taxStockFilter.querySelector(`option[value="${selectedStock}"]`);
            if (opt) selectedSymbol = opt.dataset.symbol || opt.textContent.split('—')[0].trim();
        }

        rows.forEach(row => {
            const rowStockId  = row.dataset.stockId;
            const rowGainType = row.dataset.gainType;
            const rowDate     = row.dataset.date;

            const matchesStock = (selectedStock === 'ALL' || rowStockId === selectedStock);
            const matchesType  = (selectedType === 'ALL' || rowGainType === selectedType);
            const matchesDate  = matchFy(rowDate, selectedFy);

            if (matchesStock && matchesType && matchesDate) {
                row.classList.remove('d-none');
                visibleCount++;

                const qty      = parseInt(row.dataset.qty) || 0;
                const cost     = parseFloat(row.dataset.cost) || 0;
                const proceeds = parseFloat(row.dataset.proceeds) || 0;
                const gain     = parseFloat(row.dataset.gain) || 0;

                totalMatchedQty   += qty;
                totalBuyCost      += cost;
                totalSellProceeds += proceeds;
                totalRealizedGain += gain;

                if (rowGainType === 'LTCG') {
                    totalLtcg += gain;
                } else {
                    totalStcg += gain;
                }
            } else {
                row.classList.add('d-none');
            }
        });

        if (kpiTaxQty) kpiTaxQty.textContent = totalMatchedQty.toLocaleString('en-IN');
        if (kpiTaxQtyCount) kpiTaxQtyCount.textContent = `${visibleCount} FIFO lots`;
        if (kpiTaxCost) kpiTaxCost.textContent = formatINR(totalBuyCost);
        if (kpiTaxProceeds) kpiTaxProceeds.textContent = formatINR(totalSellProceeds);
        if (kpiTaxGain) {
            kpiTaxGain.textContent = (totalRealizedGain >= 0 ? '+' : '') + formatINR(totalRealizedGain);
            kpiTaxGain.className = 'fs-5 fw-bold mb-0 mt-1 ' + (totalRealizedGain >= 0 ? 'text-success' : 'text-danger');
            const cardWrapper = kpiTaxGain.closest('.card');
            if (cardWrapper) {
                cardWrapper.classList.remove('border-success', 'border-danger');
                cardWrapper.classList.add(totalRealizedGain >= 0 ? 'border-success' : 'border-danger');
            }
        }
        if (kpiTaxBreakdown) {
            kpiTaxBreakdown.textContent = `STCG: ${formatINR(totalStcg)} • LTCG: ${formatINR(totalLtcg)}`;
        }

        if (footerTaxSummaryTitle) {
            let label = selectedStock === 'ALL' ? 'Total' : selectedSymbol;
            if (selectedType !== 'ALL') label += ` [${selectedType}]`;
            if (selectedFy !== 'ALL') label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            footerTaxSummaryTitle.textContent = `${label} (${visibleCount} Lots)`;
        }
        if (footerTaxBreakdown) {
            footerTaxBreakdown.textContent = `STCG: ${formatINR(totalStcg)} • LTCG: ${formatINR(totalLtcg)}`;
        }
        if (footerTaxQty) footerTaxQty.textContent = totalMatchedQty.toLocaleString('en-IN');
        if (footerTaxCost) footerTaxCost.textContent = formatINR(totalBuyCost);
        if (footerTaxProceeds) footerTaxProceeds.textContent = formatINR(totalSellProceeds);
        if (footerTaxGain) {
            footerTaxGain.textContent = (totalRealizedGain >= 0 ? '+' : '') + formatINR(totalRealizedGain);
            footerTaxGain.className = 'text-end py-3 fs-6 fw-bold ' + (totalRealizedGain >= 0 ? 'text-success' : 'text-danger');
        }

        const hasActiveFilter = (selectedStock !== 'ALL' || selectedType !== 'ALL' || selectedFy !== 'ALL');
        if (taxResetBtn) taxResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (taxBadge) {
            taxBadge.classList.toggle('d-none', !hasActiveFilter);
            if (taxSymbolSpan) {
                let badgeParts = [];
                if (selectedStock !== 'ALL') badgeParts.push(selectedSymbol);
                if (selectedType !== 'ALL') badgeParts.push(selectedType);
                if (selectedFy !== 'ALL') badgeParts.push(selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                taxSymbolSpan.textContent = badgeParts.join(' • ');
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedStock === 'ALL') url.searchParams.delete('tax_stock_id');
            else url.searchParams.set('tax_stock_id', selectedStock);

            if (selectedType === 'ALL') url.searchParams.delete('tax_type');
            else url.searchParams.set('tax_type', selectedType);

            if (selectedFy === 'ALL') url.searchParams.delete('tax_fy');
            else url.searchParams.set('tax_fy', selectedFy);

            window.history.replaceState({}, '', url);
        }

        if (window.equityTaxPager) {
            window.equityTaxPager.refresh();
        }
    }

    if (taxStockFilter) taxStockFilter.addEventListener('change', () => applyEquityTaxFilters(true));
    if (taxTypeFilter)  taxTypeFilter.addEventListener('change', () => applyEquityTaxFilters(true));
    if (taxDateFilter)  taxDateFilter.addEventListener('change', () => applyEquityTaxFilters(true));
    if (taxResetBtn) {
        taxResetBtn.addEventListener('click', () => {
            if (taxStockFilter) taxStockFilter.value = 'ALL';
            if (taxTypeFilter)  taxTypeFilter.value = 'ALL';
            if (taxDateFilter)  taxDateFilter.value = 'ALL';
            applyEquityTaxFilters(true);
        });
    }

    window.showStockLedger = function(stockId) {
        const transTabBtn = document.getElementById('transactions-tab');
        if (transTabBtn) {
            const tab = new bootstrap.Tab(transTabBtn);
            tab.show();
        }
        if (ledgerStockFilter) {
            ledgerStockFilter.value = stockId;
            applyLedgerFilters(true);
        }
    };

    document.querySelectorAll('.view-stock-ledger').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const stockId = this.dataset.stockId;
            window.showStockLedger(stockId);
        });
    });

    // Deep link parameters
    const urlParams = new URLSearchParams(window.location.search);
    const stockParam = urlParams.get('stock_id') || urlParams.get('stock');
    const fyParam    = urlParams.get('fy');
    const tabParam   = urlParams.get('tab');
    const hash       = window.location.hash;

    if (stockParam || fyParam || tabParam === 'transactions' || hash === '#transactions') {
        const transTabBtn = document.getElementById('transactions-tab');
        if (transTabBtn) {
            const tab = new bootstrap.Tab(transTabBtn);
            tab.show();
        }
        if (stockParam && ledgerStockFilter) ledgerStockFilter.value = stockParam;
        if (fyParam && ledgerDateFilter) ledgerDateFilter.value = fyParam;
        applyLedgerFilters(false);
    }

    const divStockParam = urlParams.get('div_stock_id');
    const divFyParam    = urlParams.get('div_fy');
    if (divStockParam || divFyParam || tabParam === 'dividends' || hash === '#dividends') {
        const divTabBtn = document.getElementById('dividends-tab');
        if (divTabBtn && (tabParam === 'dividends' || hash === '#dividends')) {
            const tab = new bootstrap.Tab(divTabBtn);
            tab.show();
        }
        if (divStockParam && divStockFilter) divStockFilter.value = divStockParam;
        if (divFyParam && divDateFilter) divDateFilter.value = divFyParam;
        applyDividendFilters(false);
    }

    const taxStockParam = urlParams.get('tax_stock_id');
    const taxTypeParam  = urlParams.get('tax_type');
    const taxFyParam    = urlParams.get('tax_fy');
    if (taxStockParam || taxTypeParam || taxFyParam || tabParam === 'capitalgains' || hash === '#capitalgains') {
        const taxTabBtn = document.getElementById('capitalgains-tab');
        if (taxTabBtn && (tabParam === 'capitalgains' || hash === '#capitalgains')) {
            const tab = new bootstrap.Tab(taxTabBtn);
            tab.show();
        }
        if (taxStockParam && taxStockFilter) taxStockFilter.value = taxStockParam;
        if (taxTypeParam && taxTypeFilter) taxTypeFilter.value = taxTypeParam;
        if (taxFyParam && taxDateFilter) taxDateFilter.value = taxFyParam;
        applyEquityTaxFilters(false);
    }

    // ==========================================
    // 7. INITIALIZE TABLE PAGINATION
    // ==========================================
    if (typeof window.initTablePagination === 'function') {
        window.equityLedgerPager = window.initTablePagination({
            tableId: 'ledgerTransactionsTable',
            rowSelector: '.ledger-trans-row',
            infoId: 'equityLedgerPageInfo',
            sizeSelectId: 'equityLedgerPageSize',
            controlsId: 'equityLedgerPageControls'
        });

        window.equityTaxPager = window.initTablePagination({
            tableId: 'equitiesTaxLogTable',
            rowSelector: '.tax-lot-row',
            infoId: 'equityTaxPageInfo',
            sizeSelectId: 'equityTaxPageSize',
            controlsId: 'equityTaxPageControls'
        });

        window.equityDivPager = window.initTablePagination({
            tableId: 'dividendsTable',
            rowSelector: '.div-row',
            infoId: 'equityDivPageInfo',
            sizeSelectId: 'equityDivPageSize',
            controlsId: 'equityDivPageControls'
        });

        window.equityCAPager = window.initTablePagination({
            tableId: 'equityCorporateActionsTable',
            rowSelector: '.ca-row',
            infoId: 'equityCAPageInfo',
            sizeSelectId: 'equityCAPageSize',
            controlsId: 'equityCAPageControls'
        });
    }

    // ==========================================
    // 8. CORPORATE ACTIONS & REAL-TIME ELIGIBILITY
    // ==========================================
    const caStockSelect = document.getElementById('ca_equity_id');
    const caActionType  = document.getElementById('ca_action_type');
    const caRecordDate  = document.getElementById('ca_record_date');
    const caEligibleBadge = document.getElementById('ca_eligible_badge');
    const caRatioGroup  = document.getElementById('ca_ratio_group');
    const caRightsGroup = document.getElementById('ca_rights_group');
    const caTargetGroup = document.getElementById('ca_target_group');
    const caDemergerGroup = document.getElementById('ca_demerger_group');
    const caTargetLabel = document.getElementById('ca_target_label');
    const caRatioNewHelp = document.getElementById('ca_ratio_new_help');

    function updateCorporateActionFields() {
        if (!caActionType) return;
        const action = caActionType.value;

        if (caRatioGroup) caRatioGroup.classList.remove('d-none');
        if (caRightsGroup) caRightsGroup.classList.add('d-none');
        if (caTargetGroup) caTargetGroup.classList.add('d-none');
        if (caDemergerGroup) caDemergerGroup.classList.add('d-none');

        if (action === 'SPLIT') {
            if (caRatioNewHelp) caRatioNewHelp.textContent = 'Post-split shares (e.g. 2 for 1:2 split)';
        } else if (action === 'BONUS') {
            if (caRatioNewHelp) caRatioNewHelp.textContent = 'Bonus shares allotted (e.g. 1 for 1:1 bonus)';
        } else if (action === 'RIGHTS') {
            if (caRatioGroup) caRatioGroup.classList.add('d-none');
            if (caRightsGroup) caRightsGroup.classList.remove('d-none');
        } else if (action === 'MERGER') {
            if (caTargetGroup) caTargetGroup.classList.remove('d-none');
            if (caTargetLabel) caTargetLabel.textContent = 'Destination Merged Stock *';
            if (caRatioNewHelp) caRatioNewHelp.textContent = 'Shares of target received per old shares';
        } else if (action === 'DEMERGER') {
            if (caTargetGroup) caTargetGroup.classList.remove('d-none');
            if (caDemergerGroup) caDemergerGroup.classList.remove('d-none');
            if (caTargetLabel) caTargetLabel.textContent = 'Spun-off Resulting Company Stock *';
            if (caRatioNewHelp) caRatioNewHelp.textContent = 'Shares of spun-off company per parent share';
        }
    }

    async function checkEligibility() {
        if (!caStockSelect || !caRecordDate || !caEligibleBadge) return;
        const equityId = caStockSelect.value;
        const recordDate = caRecordDate.value;

        if (!equityId || !recordDate) {
            caEligibleBadge.innerHTML = '<i class="bi bi-info-circle me-1"></i>Select stock and date to check eligible shares';
            caEligibleBadge.className = 'badge bg-light text-secondary border small';
            return;
        }

        caEligibleBadge.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Checking eligible shares as of record date...';

        try {
            const url = checkEligibilityUrl ? `${checkEligibilityUrl}?equity_id=${equityId}&record_date=${recordDate}` : `equities/check-corporate-action-eligibility?equity_id=${equityId}&record_date=${recordDate}`;
            const resp = await fetch(url);
            const text = await resp.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (jsonErr) {
                const match = text.match(/\{[\s\S]*"status"\s*:\s*"[^"]+"[\s\S]*\}/);
                if (match) {
                    data = JSON.parse(match[0]);
                } else {
                    throw new Error('Invalid JSON response: ' + text.substring(0, 120));
                }
            }

            if (data.status === 'success') {
                caEligibleBadge.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i>Eligible shares on ${data.record_date}: <strong>${Number(data.eligible_shares).toLocaleString('en-IN')} shares</strong>`;
                caEligibleBadge.className = 'badge bg-success-subtle text-success border border-success-subtle small';
            } else {
                caEligibleBadge.innerHTML = `<i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>${data.message || 'Unable to compute'}`;
                caEligibleBadge.className = 'badge bg-warning-subtle text-dark border small';
            }
        } catch (e) {
            console.error('Eligibility check error:', e);
            caEligibleBadge.innerHTML = `<i class="bi bi-info-circle me-1"></i>Could not evaluate eligible shares (${e.message ? e.message.substring(0, 40) : 'Network error'})`;
            caEligibleBadge.className = 'badge bg-light text-muted border small';
        }
    }

    if (caActionType) caActionType.addEventListener('change', updateCorporateActionFields);
    if (caStockSelect) caStockSelect.addEventListener('change', checkEligibility);
    if (caRecordDate) caRecordDate.addEventListener('change', checkEligibility);
    updateCorporateActionFields();
});

