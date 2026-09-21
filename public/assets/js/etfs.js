/**
 * RupeeFolio - ETFs Module Scripts
 * 100% Offline Compatible
 */

document.addEventListener('DOMContentLoaded', function() {
    let currentEtf = {};

    const cfg = window.ETFS_CONFIG || {};
    const FY_RANGES = cfg.fyRanges || {};
    const checkSplitUrl = cfg.checkSplitUrl || '';

    // ==========================================
    // 1. MODAL DATA BINDINGS
    // ==========================================

    // Hook Edit ETF buttons
    document.querySelectorAll('.open-edit-etf-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editEtfId');
            const elSym = document.getElementById('editEtfSymbol');
            const elName = document.getElementById('editEtfName');
            const elCat = document.getElementById('editEtfCategory');
            const elAmc = document.getElementById('editEtfAmc');
            const elEx = document.getElementById('editEtfExchange');

            if (elId) elId.value = this.dataset.id;
            if (elSym) elSym.value = this.dataset.symbol;
            if (elName) elName.value = this.dataset.name;
            if (elCat) elCat.value = this.dataset.category;
            if (elAmc) elAmc.value = this.dataset.amc || '';
            if (elEx) elEx.value = this.dataset.exchange || 'NSE';
        });
    });

    // Hook Edit Transaction buttons
    document.querySelectorAll('.open-edit-etf-trans').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editEtfTransId');
            const elTitle = document.getElementById('editEtfTransTitle');
            const elDate = document.getElementById('editEtfTransDate');
            const elQty = document.getElementById('editEtfTransQty');
            const elPrice = document.getElementById('editEtfTransPrice');
            const elBrok = document.getElementById('editEtfTransBrokerage');
            const elStt = document.getElementById('editEtfTransStt');
            const elNotes = document.getElementById('editEtfTransNotes');

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

    // Hook "Add Trans" buttons to populate modal
    document.querySelectorAll('.open-etf-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentEtf = {
                id: this.dataset.id,
                symbol: this.dataset.symbol,
                name: this.dataset.name,
                qty: parseInt(this.dataset.qty) || 0,
                cmp: parseFloat(this.dataset.cmp) || 0,
                avgPrice: parseFloat(this.dataset.avg) || 0
            };

            const elId = document.getElementById('modalEtfId');
            const elSym = document.getElementById('modalEtfSymbol');
            const elSub = document.getElementById('modalEtfSubtitle');
            const elBuyP = document.getElementById('buyEtfPrice');
            const elSellP = document.getElementById('sellEtfPrice');
            const elBadge = document.getElementById('sellEtfAvailableQtyBadge');
            const elSellQ = document.getElementById('sellEtfQty');
            const elActBuy = document.getElementById('actEtfBuy');

            if (elId) elId.value = currentEtf.id;
            if (elSym) elSym.textContent = currentEtf.symbol;
            if (elSub) elSub.textContent = currentEtf.name + ' (Held: ' + currentEtf.qty + ' units)';
            
            if (elBuyP) elBuyP.value = currentEtf.cmp.toFixed(2);
            if (elSellP) elSellP.value = currentEtf.cmp.toFixed(2);
            if (elBadge) elBadge.textContent = currentEtf.qty + ' units';
            if (elSellQ) elSellQ.max = currentEtf.qty;

            if (elActBuy) elActBuy.checked = true;
            switchEtfAction('BUY');
        });
    });

    // Action Tab Switching (Buy More / Sell)
    const actRadios = document.querySelectorAll('input[name="action_type"]');
    actRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            switchEtfAction(this.value);
        });
    });

    function switchEtfAction(type) {
        const pBuy = document.getElementById('panelEtfBuy');
        const pSell = document.getElementById('panelEtfSell');
        const submitBtn = document.getElementById('btnSubmitEtfTrans');

        if (!pBuy || !pSell || !submitBtn) return;

        if (type === 'BUY') {
            pBuy.classList.remove('d-none');
            pSell.classList.add('d-none');
            setEtfInputsDisabled(pBuy, false);
            setEtfInputsDisabled(pSell, true);
            submitBtn.className = 'btn btn-success btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Confirm Purchase Lot';
        } else if (type === 'SELL') {
            pBuy.classList.add('d-none');
            pSell.classList.remove('d-none');
            setEtfInputsDisabled(pBuy, true);
            setEtfInputsDisabled(pSell, false);
            submitBtn.className = 'btn btn-danger btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Execute FIFO Sell';
        }
    }

    function setEtfInputsDisabled(container, disabled) {
        if (!container) return;
        container.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    // Live calculations in modal
    const buyQty = document.getElementById('buyEtfQty');
    const buyPrice = document.getElementById('buyEtfPrice');
    const buyCharges = document.getElementById('buyEtfCharges');
    const buyPreview = document.getElementById('buyEtfTotalPreview');

    function calcEtfBuy() {
        if (!buyPreview) return;
        const q = parseFloat(buyQty ? buyQty.value : 0) || 0;
        const p = parseFloat(buyPrice ? buyPrice.value : 0) || 0;
        const c = parseFloat(buyCharges ? buyCharges.value : 0) || 0;
        const total = (q * p) + c;
        buyPreview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    if (buyQty && buyPrice && buyCharges) {
        [buyQty, buyPrice, buyCharges].forEach(el => el.addEventListener('input', calcEtfBuy));
    }

    const sellQty = document.getElementById('sellEtfQty');
    const sellPrice = document.getElementById('sellEtfPrice');
    const sellCharges = document.getElementById('sellEtfCharges');
    const sellPreview = document.getElementById('sellEtfTotalPreview');
    const sellPnlPreview = document.getElementById('sellEtfPnlPreview');

    function calcEtfSell() {
        if (!sellPreview) return;
        const q = parseFloat(sellQty ? sellQty.value : 0) || 0;
        const p = parseFloat(sellPrice ? sellPrice.value : 0) || 0;
        const c = parseFloat(sellCharges ? sellCharges.value : 0) || 0;
        const netProceeds = (q * p) - c;
        const costBasis = q * (currentEtf.avgPrice || 0);
        const estGain = netProceeds - costBasis;

        sellPreview.textContent = '₹ ' + netProceeds.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (sellPnlPreview) {
            const sign = estGain >= 0 ? '+' : '';
            sellPnlPreview.textContent = sign + '₹ ' + estGain.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            sellPnlPreview.className = 'small fw-semibold ' + (estGain >= 0 ? 'text-success' : 'text-danger');
        }
    }
    if (sellQty && sellPrice && sellCharges) {
        [sellQty, sellPrice, sellCharges].forEach(el => el.addEventListener('input', calcEtfSell));
    }

    // ==========================================
    // 2. TRADE LEDGER DUAL FILTER & KPI
    // ==========================================
    const ledgerEtfFilter  = document.getElementById('ledgerEtfFilter');
    const ledgerDateFilter = document.getElementById('ledgerDateFilter');
    const ledgerTable      = document.getElementById('etfLedgerTable');
    const ledgerResetBtn   = document.getElementById('btnResetEtfFilter');
    const ledgerBadge      = document.getElementById('filteredEtfBadge');
    const ledgerSymbolSpan = document.getElementById('filteredEtfSymbol');
    const ledgerNoRowsEl   = document.getElementById('ledgerNoRows');

    const kpiBuyAmount   = document.getElementById('kpiBuyAmount');
    const kpiBuyDetails  = document.getElementById('kpiBuyDetails');
    const kpiSellAmount  = document.getElementById('kpiSellAmount');
    const kpiSellDetails = document.getElementById('kpiSellDetails');
    const kpiNetOutlay   = document.getElementById('kpiNetOutlay');
    const kpiNetDetails  = document.getElementById('kpiNetDetails');
    const kpiCharges     = document.getElementById('kpiCharges');

    const footerSummaryTitle     = document.getElementById('footerSummaryTitle');
    const footerActionBreakdown  = document.getElementById('footerActionBreakdown');
    const footerTotalQty         = document.getElementById('footerTotalQty');
    const footerTotalCharges     = document.getElementById('footerTotalCharges');
    const footerTotalAmount      = document.getElementById('footerTotalAmount');
    const footerNote             = document.getElementById('footerNote');

    function formatINR(val) {
        return '₹ ' + Number(val).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function applyLedgerFilters(updateUrl = true) {
        if (!ledgerTable) return;

        const selectedEtf = ledgerEtfFilter ? ledgerEtfFilter.value : 'ALL';
        const selectedFy  = ledgerDateFilter ? ledgerDateFilter.value : 'ALL';
        const selectedOption = ledgerEtfFilter ? ledgerEtfFilter.options[ledgerEtfFilter.selectedIndex] : null;
        const selectedSymbol = selectedOption ? (selectedOption.dataset.symbol || '') : '';

        let fyStart = null;
        let fyEnd   = null;
        if (selectedFy === 'CURRENT_FY' && FY_RANGES.current) {
            fyStart = FY_RANGES.current.start;
            fyEnd   = FY_RANGES.current.end;
        } else if (selectedFy === 'LAST_FY' && FY_RANGES.last) {
            fyStart = FY_RANGES.last.start;
            fyEnd   = FY_RANGES.last.end;
        }

        const rows = ledgerTable.querySelectorAll('tbody tr.etf-ledger-row');
        let visibleCount = 0;
        let buyAmount    = 0.0;
        let sellAmount   = 0.0;
        let buyQty       = 0;
        let sellQty      = 0;
        let buyCount     = 0;
        let sellCount    = 0;
        let totalCharges = 0.0;

        rows.forEach(row => {
            const rowEtfId = row.dataset.etfId;
            const rowDate  = row.dataset.date;
            const rowType  = row.dataset.type;
            const rowQty   = parseInt(row.dataset.qty) || 0;
            const rowAmt   = parseFloat(row.dataset.amount) || 0.0;
            const rowChg   = parseFloat(row.dataset.charges) || 0.0;

            const matchesEtf = (selectedEtf === 'ALL' || rowEtfId === selectedEtf);
            let matchesDate  = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesEtf && matchesDate) {
                row.classList.remove('d-none');
                row.dataset.filtered = 'false';
                visibleCount++;
                totalCharges += rowChg;

                if (rowType === 'BUY') {
                    buyAmount += rowAmt;
                    buyQty    += rowQty;
                    buyCount++;
                } else {
                    sellAmount += rowAmt;
                    sellQty    += rowQty;
                    sellCount++;
                }
            } else {
                row.classList.add('d-none');
                row.dataset.filtered = 'true';
            }
        });

        if (ledgerNoRowsEl) {
            ledgerNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        const netOutlay = buyAmount - sellAmount;

        if (kpiBuyAmount) kpiBuyAmount.textContent = formatINR(buyAmount);
        if (kpiBuyDetails) kpiBuyDetails.textContent = `${buyCount} buys • ${buyQty.toLocaleString('en-IN')} units`;

        if (kpiSellAmount) kpiSellAmount.textContent = formatINR(sellAmount);
        if (kpiSellDetails) kpiSellDetails.textContent = `${sellCount} sells • ${sellQty.toLocaleString('en-IN')} units`;

        if (kpiNetOutlay) {
            kpiNetOutlay.textContent = formatINR(netOutlay);
            kpiNetOutlay.className = 'fw-bold mb-0 mt-1 ' + (netOutlay >= 0 ? 'text-primary' : 'text-success');
        }
        if (kpiNetDetails) {
            if (selectedEtf === 'ALL') {
                kpiNetDetails.textContent = 'Net cash invested in period';
            } else {
                const heldDiff = buyQty - sellQty;
                kpiNetDetails.textContent = `Net held from trades: ${heldDiff.toLocaleString('en-IN')} units`;
            }
        }

        if (kpiCharges) kpiCharges.textContent = formatINR(totalCharges);

        if (footerSummaryTitle) {
            let label = selectedEtf === 'ALL' ? 'Total' : selectedSymbol;
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

        const hasActiveFilter = (selectedEtf !== 'ALL' || selectedFy !== 'ALL');
        if (ledgerResetBtn) ledgerResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (ledgerBadge) {
            ledgerBadge.classList.toggle('d-none', !hasActiveFilter);
            if (ledgerSymbolSpan) {
                let badgeText = selectedEtf !== 'ALL' ? selectedSymbol : 'All ETFs';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                ledgerSymbolSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedEtf === 'ALL') url.searchParams.delete('etf_id');
            else url.searchParams.set('etf_id', selectedEtf);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }

        if (window.etfLedgerPager) {
            window.etfLedgerPager.refresh();
        }
    }

    if (ledgerEtfFilter) ledgerEtfFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerDateFilter) ledgerDateFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerResetBtn) {
        ledgerResetBtn.addEventListener('click', () => {
            if (ledgerEtfFilter) ledgerEtfFilter.value = 'ALL';
            if (ledgerDateFilter) ledgerDateFilter.value = 'ALL';
            applyLedgerFilters(true);
        });
    }

    window.showEtfLedger = function(etfId) {
        const transTabBtn = document.getElementById('transactions-tab');
        if (transTabBtn) {
            bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        }
        if (ledgerEtfFilter) {
            ledgerEtfFilter.value = String(etfId);
            applyLedgerFilters(true);
        }
    };

    // ==========================================
    // 3. ETF HOLDINGS SORTING
    // ==========================================
    const sortSelect = document.getElementById('sortEtfsSelect');
    const holdingsTbody = document.getElementById('etfHoldingsTbody');
    const sortableHeaders = document.querySelectorAll('.sortable-etf-th');

    function sortEtfsHoldings(sortBy) {
        if (!holdingsTbody) return;
        const rows = Array.from(holdingsTbody.querySelectorAll('tr.etf-holding-row'));
        if (rows.length === 0) return;

        const [field, dir] = sortBy.split('_');
        const isAsc = dir === 'asc';

        rows.sort((a, b) => {
            let valA, valB;
            if (field === 'name') {
                valA = (a.dataset.name || '').toLowerCase();
                valB = (b.dataset.name || '').toLowerCase();
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
            sortEtfsHoldings(this.value);
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
            sortEtfsHoldings(newVal);
        });
    });

    // ==========================================
    // 4. FIFO TAX LOG FILTERING
    // ==========================================
    const taxEtfFilter   = document.getElementById('taxLogEtfFilter');
    const taxTypeFilter  = document.getElementById('taxLogTypeFilter');
    const taxDateFilter  = document.getElementById('taxLogDateFilter');
    const taxResetBtn    = document.getElementById('btnResetTaxFilter');
    const taxBadge       = document.getElementById('filteredTaxBadge');
    const taxSymbolSpan  = document.getElementById('filteredTaxSymbolSpan');
    const taxTable       = document.getElementById('etfTaxLogTable');

    const kpiTaxUnits     = document.getElementById('kpiTaxUnits');
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

    function applyEtfTaxFilters(updateUrl = true) {
        if (!taxTable) return;

        const selectedEtf  = taxEtfFilter ? taxEtfFilter.value : 'ALL';
        const selectedType = taxTypeFilter ? taxTypeFilter.value : 'ALL';
        const selectedFy   = taxDateFilter ? taxDateFilter.value : 'ALL';

        const rows = taxTable.querySelectorAll('tbody tr.etf-taxlog-row');
        let visibleCount = 0;
        let totalMatchedUnits = 0;
        let totalBuyCost = 0;
        let totalSellProceeds = 0;
        let totalRealizedGain = 0;
        let totalStcg = 0;
        let totalLtcg = 0;
        let selectedSymbol = '';

        if (selectedEtf !== 'ALL' && taxEtfFilter) {
            const opt = taxEtfFilter.querySelector(`option[value="${selectedEtf}"]`);
            if (opt) selectedSymbol = opt.dataset.symbol || opt.textContent.split('—')[0].trim();
        }

        rows.forEach(row => {
            const rowEtfId    = row.dataset.etfId;
            const rowGainType = row.dataset.gainType;
            const rowDate     = row.dataset.date;

            const matchesEtf  = (selectedEtf === 'ALL' || rowEtfId === selectedEtf);
            const matchesType = (selectedType === 'ALL' || rowGainType === selectedType);
            let matchesDate   = true;
            if (selectedFy === 'CURRENT_FY' && FY_RANGES.current) {
                matchesDate = (rowDate >= FY_RANGES.current.start && rowDate <= FY_RANGES.current.end);
            } else if (selectedFy === 'LAST_FY' && FY_RANGES.last) {
                matchesDate = (rowDate >= FY_RANGES.last.start && rowDate <= FY_RANGES.last.end);
            }

            if (matchesEtf && matchesType && matchesDate) {
                row.classList.remove('d-none');
                visibleCount++;

                const qty      = parseInt(row.dataset.qty) || 0;
                const cost     = parseFloat(row.dataset.cost) || 0;
                const proceeds = parseFloat(row.dataset.proceeds) || 0;
                const gain     = parseFloat(row.dataset.gain) || 0;

                totalMatchedUnits += qty;
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

        if (kpiTaxUnits) kpiTaxUnits.textContent = totalMatchedUnits.toLocaleString('en-IN');
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
            let label = selectedEtf === 'ALL' ? 'Total' : selectedSymbol;
            if (selectedType !== 'ALL') label += ` [${selectedType}]`;
            if (selectedFy !== 'ALL') label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            footerTaxSummaryTitle.textContent = `${label} (${visibleCount} Lots)`;
        }
        if (footerTaxBreakdown) {
            footerTaxBreakdown.textContent = `STCG: ${formatINR(totalStcg)} • LTCG: ${formatINR(totalLtcg)}`;
        }
        if (footerTaxQty) footerTaxQty.textContent = totalMatchedUnits.toLocaleString('en-IN');
        if (footerTaxCost) footerTaxCost.textContent = formatINR(totalBuyCost);
        if (footerTaxProceeds) footerTaxProceeds.textContent = formatINR(totalSellProceeds);
        if (footerTaxGain) {
            footerTaxGain.textContent = (totalRealizedGain >= 0 ? '+' : '') + formatINR(totalRealizedGain);
            footerTaxGain.className = 'text-end py-3 fs-6 fw-bold ' + (totalRealizedGain >= 0 ? 'text-success' : 'text-danger');
        }

        const hasActiveFilter = (selectedEtf !== 'ALL' || selectedType !== 'ALL' || selectedFy !== 'ALL');
        if (taxResetBtn) taxResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (taxBadge) {
            taxBadge.classList.toggle('d-none', !hasActiveFilter);
            if (taxSymbolSpan) {
                let badgeParts = [];
                if (selectedEtf !== 'ALL') badgeParts.push(selectedSymbol);
                if (selectedType !== 'ALL') badgeParts.push(selectedType);
                if (selectedFy !== 'ALL') badgeParts.push(selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                taxSymbolSpan.textContent = badgeParts.join(' • ');
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedEtf === 'ALL') url.searchParams.delete('tax_etf_id');
            else url.searchParams.set('tax_etf_id', selectedEtf);

            if (selectedType === 'ALL') url.searchParams.delete('tax_type');
            else url.searchParams.set('tax_type', selectedType);

            if (selectedFy === 'ALL') url.searchParams.delete('tax_fy');
            else url.searchParams.set('tax_fy', selectedFy);

            window.history.replaceState({}, '', url);
        }

        if (window.etfTaxPager) {
            window.etfTaxPager.refresh();
        }
    }

    if (taxEtfFilter)  taxEtfFilter.addEventListener('change', () => applyEtfTaxFilters(true));
    if (taxTypeFilter) taxTypeFilter.addEventListener('change', () => applyEtfTaxFilters(true));
    if (taxDateFilter) taxDateFilter.addEventListener('change', () => applyEtfTaxFilters(true));
    if (taxResetBtn) {
        taxResetBtn.addEventListener('click', () => {
            if (taxEtfFilter)  taxEtfFilter.value = 'ALL';
            if (taxTypeFilter) taxTypeFilter.value = 'ALL';
            if (taxDateFilter) taxDateFilter.value = 'ALL';
            applyEtfTaxFilters(true);
        });
    }

    // ==========================================
    // 5. INITIALIZE TABLE PAGINATION
    // ==========================================
    if (typeof window.initTablePagination === 'function') {
        window.etfLedgerPager = window.initTablePagination({
            tableId: 'etfLedgerTable',
            rowSelector: '.etf-ledger-row',
            infoId: 'etfLedgerPageInfo',
            sizeSelectId: 'etfLedgerPageSize',
            controlsId: 'etfLedgerPageControls'
        });

        window.etfTaxPager = window.initTablePagination({
            tableId: 'etfTaxTable',
            rowSelector: '.etf-taxlog-row',
            infoId: 'etfTaxPageInfo',
            sizeSelectId: 'etfTaxPageSize',
            controlsId: 'etfTaxPageControls'
        });

        window.etfCAPager = window.initTablePagination({
            tableId: 'etfCorporateActionsTable',
            rowSelector: '.ca-etf-row',
            infoId: 'etfCAPageInfo',
            sizeSelectId: 'etfCAPageSize',
            controlsId: 'etfCAPageControls'
        });
    }

    // ==========================================
    // 6. ETF SPLIT MODAL REAL-TIME ELIGIBILITY
    // ==========================================
    const splitEtfSelect     = document.getElementById('split_etf_id');
    const splitRecordDate    = document.getElementById('split_record_date');
    const splitEligibleBadge = document.getElementById('split_eligible_badge');

    async function checkEtfSplitEligibility() {
        if (!splitEtfSelect || !splitRecordDate || !splitEligibleBadge) return;
        const etfId = splitEtfSelect.value;
        const recordDate = splitRecordDate.value;

        if (!etfId || !recordDate) {
            splitEligibleBadge.innerHTML = '<i class="bi bi-info-circle me-1"></i>Select ETF and date to check eligible units';
            splitEligibleBadge.className = 'badge bg-light text-secondary border small';
            return;
        }

        splitEligibleBadge.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Checking eligible units as of record date...';

        try {
            const url = checkSplitUrl ? `${checkSplitUrl}?etf_id=${etfId}&record_date=${recordDate}` : `etfs/check-split-eligibility?etf_id=${etfId}&record_date=${recordDate}`;
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
                splitEligibleBadge.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i>Eligible units on ${data.record_date}: <strong>${Number(data.eligible_units).toLocaleString('en-IN')} units</strong>`;
                splitEligibleBadge.className = 'badge bg-success-subtle text-success border border-success-subtle small';
            } else {
                splitEligibleBadge.innerHTML = `<i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>${data.message || 'Unable to compute'}`;
                splitEligibleBadge.className = 'badge bg-warning-subtle text-dark border small';
            }
        } catch (e) {
            console.error('ETF split eligibility check error:', e);
            splitEligibleBadge.innerHTML = `<i class="bi bi-info-circle me-1"></i>Could not evaluate eligible units (${e.message ? e.message.substring(0, 40) : 'Network error'})`;
            splitEligibleBadge.className = 'badge bg-light text-muted border small';
        }
    }

    if (splitEtfSelect)  splitEtfSelect.addEventListener('change', checkEtfSplitEligibility);
    if (splitRecordDate) splitRecordDate.addEventListener('change', checkEtfSplitEligibility);

    // Deep link handling on page load
    const urlParams = new URLSearchParams(window.location.search);
    const initialEtfId = urlParams.get('etf_id');
    const initialFy    = urlParams.get('fy');
    const initialTab   = urlParams.get('tab');
    const hash         = window.location.hash;

    if (initialTab === 'past-holdings' || hash === '#past-holdings') {
        const pastTabBtn = document.getElementById('past-holdings-tab');
        if (pastTabBtn) {
            bootstrap.Tab.getOrCreateInstance(pastTabBtn).show();
        }
    }

    if (initialEtfId && ledgerEtfFilter) {
        ledgerEtfFilter.value = initialEtfId;
    }
    if (initialFy && ledgerDateFilter) {
        ledgerDateFilter.value = initialFy;
    }
    if ((initialEtfId || initialFy || initialTab === 'transactions' || hash === '#transactions') && ledgerTable) {
        applyLedgerFilters(false);
        if (initialTab === 'transactions' || initialEtfId || hash === '#transactions') {
            const transTabBtn = document.getElementById('transactions-tab');
            if (transTabBtn) {
                bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
            }
        }
    }

    const taxEtfParam  = urlParams.get('tax_etf_id');
    const taxTypeParam = urlParams.get('tax_type');
    const taxFyParam   = urlParams.get('tax_fy');
    if (taxEtfParam || taxTypeParam || taxFyParam || initialTab === 'capitalgains' || hash === '#capitalgains') {
        const taxTabBtn = document.getElementById('capitalgains-tab');
        if (taxTabBtn && (initialTab === 'capitalgains' || hash === '#capitalgains')) {
            bootstrap.Tab.getOrCreateInstance(taxTabBtn).show();
        }
        if (taxEtfParam && taxEtfFilter) taxEtfFilter.value = taxEtfParam;
        if (taxTypeParam && taxTypeFilter) taxTypeFilter.value = taxTypeParam;
        if (taxFyParam && taxDateFilter) taxDateFilter.value = taxFyParam;
        applyEtfTaxFilters(false);
    }
});

