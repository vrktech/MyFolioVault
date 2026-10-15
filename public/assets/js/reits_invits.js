/**
 * MyFolioVault - REITs & InvITs Module Scripts
 * 100% Offline Compatible
 */
document.addEventListener('DOMContentLoaded', function() {
    let currentTrust = {};

    // 1. Hook "Add Trans" buttons to populate modal
    document.querySelectorAll('.open-reit-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentTrust = {
                id: this.dataset.id,
                symbol: this.dataset.symbol,
                name: this.dataset.name,
                type: this.dataset.type,
                exchange: this.dataset.exchange,
                qty: parseFloat(this.dataset.qty) || 0,
                cmp: parseFloat(this.dataset.cmp) || 0,
                avgPrice: parseFloat(this.dataset.avg) || 0
            };

            document.getElementById('modalTrustId').value = currentTrust.id;
            document.getElementById('modalReitTitle').textContent = currentTrust.symbol + ' (' + currentTrust.exchange + ')';
            document.getElementById('modalReitSubtitle').textContent = currentTrust.name + ' | Held: ' + Math.round(currentTrust.qty) + ' units | CMP: ₹ ' + currentTrust.cmp.toFixed(2);

            // Set defaults in Buy Panel
            document.getElementById('buyReitPrice').value = currentTrust.cmp > 0 ? currentTrust.cmp.toFixed(2) : '';
            document.getElementById('buyReitQty').value = '';
            document.getElementById('buyReitBrokerage').value = '0.00';
            document.getElementById('buyReitStt').value = '0.00';
            document.getElementById('buyReitTotalPreview').textContent = '₹ 0.00';

            // Set defaults in Sell Panel
            document.getElementById('sellReitAvailableBadge').textContent = Math.round(currentTrust.qty) + ' units';
            document.getElementById('sellReitQty').max = currentTrust.qty;
            document.getElementById('sellReitQty').value = '';
            document.getElementById('sellReitPrice').value = currentTrust.cmp > 0 ? currentTrust.cmp.toFixed(2) : '';
            document.getElementById('sellReitBrokerage').value = '0.00';
            document.getElementById('sellReitStt').value = '0.00';
            document.getElementById('sellReitProceedsPreview').textContent = '₹ 0.00';
            document.getElementById('sellReitPnlPreview').textContent = '₹ 0.00';

            // Reset Distribution Panel
            document.getElementById('distEligibleUnits').value = currentTrust.qty > 0 ? Math.round(currentTrust.qty) : '';
            document.getElementById('distDpu').value = '';
            document.getElementById('distInterest').value = '0.00';
            document.getElementById('distDividend').value = '0.00';
            document.getElementById('distRoc').value = '0.00';
            document.getElementById('distOther').value = '0.00';
            document.getElementById('distTotal').value = '';
            document.getElementById('distTds').value = '0.00';
            document.getElementById('distNet').value = '';

            // Reset to Buy panel
            document.getElementById('actReitBuy').checked = true;
            switchReitAction('BUY');
        });
    });

    // 2. Action Tab Switching
    const actRadios = document.querySelectorAll('input[name="action_type"]');
    actRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            switchReitAction(this.value);
        });
    });

    function switchReitAction(type) {
        const pBuy = document.getElementById('panelReitBuy');
        const pSell = document.getElementById('panelReitSell');
        const pDist = document.getElementById('panelReitDist');
        const submitBtn = document.getElementById('btnSubmitReitTrans');

        // Hide all & disable
        [pBuy, pSell, pDist].forEach(p => {
            p.classList.add('d-none');
            setInputsDisabled(p, true);
        });

        if (type === 'BUY') {
            pBuy.classList.remove('d-none');
            setInputsDisabled(pBuy, false);
            submitBtn.className = 'btn btn-success btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Confirm Purchase Lot';
        } else if (type === 'SELL') {
            pSell.classList.remove('d-none');
            setInputsDisabled(pSell, false);
            submitBtn.className = 'btn btn-danger btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Confirm Sell Order (FIFO)';
        } else if (type === 'DISTRIBUTION') {
            pDist.classList.remove('d-none');
            setInputsDisabled(pDist, false);
            submitBtn.className = 'btn btn-primary btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Record Distribution Payout';
        }
    }

    function setInputsDisabled(panel, disabled) {
        panel.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    // 3. Live Buy Outlay Calculation
    const buyQty = document.getElementById('buyReitQty');
    const buyPrice = document.getElementById('buyReitPrice');
    const buyBrok = document.getElementById('buyReitBrokerage');
    const buyStt = document.getElementById('buyReitStt');
    const buyPreview = document.getElementById('buyReitTotalPreview');

    function calcBuyTotal() {
        const q = parseFloat(buyQty.value) || 0;
        const p = parseFloat(buyPrice.value) || 0;
        const b = parseFloat(buyBrok.value) || 0;
        const s = parseFloat(buyStt.value) || 0;
        const total = (q * p) + b + s;
        buyPreview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    [buyQty, buyPrice, buyBrok, buyStt].forEach(el => {
        el.addEventListener('input', calcBuyTotal);
    });

    // 4. Live Sell Proceeds & P&L Calculation
    const sellQty = document.getElementById('sellReitQty');
    const sellPrice = document.getElementById('sellReitPrice');
    const sellBrok = document.getElementById('sellReitBrokerage');
    const sellStt = document.getElementById('sellReitStt');
    const sellProceedsPreview = document.getElementById('sellReitProceedsPreview');
    const sellPnlPreview = document.getElementById('sellReitPnlPreview');

    function calcSellProceeds() {
        const q = parseFloat(sellQty.value) || 0;
        const p = parseFloat(sellPrice.value) || 0;
        const b = parseFloat(sellBrok.value) || 0;
        const s = parseFloat(sellStt.value) || 0;
        const proceeds = (q * p) - b - s;
        sellProceedsPreview.textContent = '₹ ' + proceeds.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (currentTrust.avgPrice > 0 && q > 0) {
            const cost = q * currentTrust.avgPrice;
            const gain = proceeds - cost;
            const sign = gain >= 0 ? '+' : '';
            sellPnlPreview.className = 'small fw-semibold ' + (gain >= 0 ? 'text-success' : 'text-danger');
            sellPnlPreview.textContent = sign + '₹ ' + gain.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' (Estimated)';
        } else {
            sellPnlPreview.textContent = '₹ 0.00';
            sellPnlPreview.className = 'small fw-semibold text-muted';
        }
    }

    [sellQty, sellPrice, sellBrok, sellStt].forEach(el => {
        el.addEventListener('input', calcSellProceeds);
    });

    // 5. Distribution Component Auto-Sum & DPU Sync
    const distEligibleUnits = document.getElementById('distEligibleUnits');
    const distDpu = document.getElementById('distDpu');
    const distInterest = document.getElementById('distInterest');
    const distDividend = document.getElementById('distDividend');
    const distRoc = document.getElementById('distRoc');
    const distOther = document.getElementById('distOther');
    const distTotal = document.getElementById('distTotal');
    const distTds = document.getElementById('distTds');
    const distNet = document.getElementById('distNet');

    function calcDistTotals() {
        const interest = parseFloat(distInterest.value) || 0;
        const dividend = parseFloat(distDividend.value) || 0;
        const roc = parseFloat(distRoc.value) || 0;
        const other = parseFloat(distOther.value) || 0;

        const sumComponents = interest + dividend + roc + other;
        if (sumComponents > 0) {
            distTotal.value = sumComponents.toFixed(2);
        }

        const gross = parseFloat(distTotal.value) || 0;
        const tds = parseFloat(distTds.value) || 0;
        const net = Math.max(0, gross - tds);
        distNet.value = net > 0 ? net.toFixed(2) : '';

        // Auto-calculate DPU if eligible units entered
        const units = parseFloat(distEligibleUnits.value) || 0;
        if (units > 0 && gross > 0) {
            distDpu.value = (gross / units).toFixed(4);
        }
    }

    [distInterest, distDividend, distRoc, distOther, distTotal, distTds].forEach(el => {
        el.addEventListener('input', calcDistTotals);
    });

    // When DPU is entered directly, calculate Gross & Net
    distDpu.addEventListener('input', function() {
        const dpuVal = parseFloat(distDpu.value) || 0;
        const units = parseFloat(distEligibleUnits.value) || 0;
        if (dpuVal > 0 && units > 0) {
            const calculatedGross = dpuVal * units;
            distTotal.value = calculatedGross.toFixed(2);
            const tds = parseFloat(distTds.value) || 0;
            distNet.value = Math.max(0, calculatedGross - tds).toFixed(2);
        }
    });

    // When Eligible Units changes, re-sync with DPU or Gross
    distEligibleUnits.addEventListener('input', function() {
        const units = parseFloat(distEligibleUnits.value) || 0;
        const dpuVal = parseFloat(distDpu.value) || 0;
        const gross = parseFloat(distTotal.value) || 0;
        if (dpuVal > 0 && units > 0) {
            const calculatedGross = dpuVal * units;
            distTotal.value = calculatedGross.toFixed(2);
            const tds = parseFloat(distTds.value) || 0;
            distNet.value = Math.max(0, calculatedGross - tds).toFixed(2);
        } else if (gross > 0 && units > 0) {
            distDpu.value = (gross / units).toFixed(4);
        }
    });

    // 6. Edit Trust Details Modal Handler
    document.querySelectorAll('.open-edit-trust-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editTrustId').value = this.dataset.id;
            document.getElementById('editTrustName').value = this.dataset.name;
            document.getElementById('editTrustSymbol').value = this.dataset.symbol;
            document.getElementById('editTrustIsin').value = this.dataset.isin;
            document.getElementById('editTrustType').value = this.dataset.type;
            document.getElementById('editTrustExchange').value = this.dataset.exchange;
            document.getElementById('editTrustSponsor').value = this.dataset.sponsor || '';
            document.getElementById('editTrustPrice').value = this.dataset.price || '';
        });
    });

    // 7. Edit REIT Transaction Modal Handler
    document.querySelectorAll('.open-edit-reit-trans').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editReitTransId').value = this.dataset.id;
            document.getElementById('editReitTransTitle').textContent = 'Edit ' + this.dataset.type + ' — ' + this.dataset.trust;
            document.getElementById('editReitTransDate').value = this.dataset.date;
            document.getElementById('editReitTransQty').value = this.dataset.quantity;
            document.getElementById('editReitTransPrice').value = this.dataset.price;
            document.getElementById('editReitTransBrokerage').value = this.dataset.brokerage || '0.00';
            document.getElementById('editReitTransStt').value = this.dataset.stt || '0.00';
            document.getElementById('editReitTransNotes').value = this.dataset.notes || '';
        });
    });

    // 8. Edit Distribution Modal Handler & Auto-calculations
    const editDistEligibleUnits = document.getElementById('editDistEligibleUnits');
    const editDistDpu           = document.getElementById('editDistDpu');
    const editDistInterest      = document.getElementById('editDistInterest');
    const editDistDividend      = document.getElementById('editDistDividend');
    const editDistRoc           = document.getElementById('editDistRoc');
    const editDistOther         = document.getElementById('editDistOther');
    const editDistTotal         = document.getElementById('editDistTotal');
    const editDistTds           = document.getElementById('editDistTds');
    const editDistNet           = document.getElementById('editDistNet');

    function calcEditDistTotals() {
        const interest = parseFloat(editDistInterest.value) || 0;
        const dividend = parseFloat(editDistDividend.value) || 0;
        const roc = parseFloat(editDistRoc.value) || 0;
        const other = parseFloat(editDistOther.value) || 0;

        const sumComponents = interest + dividend + roc + other;
        if (sumComponents > 0) {
            editDistTotal.value = sumComponents.toFixed(2);
        }

        const gross = parseFloat(editDistTotal.value) || 0;
        const tds = parseFloat(editDistTds.value) || 0;
        const net = Math.max(0, gross - tds);
        editDistNet.value = net > 0 ? net.toFixed(2) : '';

        const units = parseFloat(editDistEligibleUnits.value) || 0;
        if (units > 0 && gross > 0) {
            editDistDpu.value = (gross / units).toFixed(4);
        }
    }

    [editDistInterest, editDistDividend, editDistRoc, editDistOther, editDistTotal, editDistTds].forEach(el => {
        el.addEventListener('input', calcEditDistTotals);
    });

    editDistDpu.addEventListener('input', function() {
        const dpuVal = parseFloat(editDistDpu.value) || 0;
        const units = parseFloat(editDistEligibleUnits.value) || 0;
        if (dpuVal > 0 && units > 0) {
            const calculatedGross = dpuVal * units;
            editDistTotal.value = calculatedGross.toFixed(2);
            const tds = parseFloat(editDistTds.value) || 0;
            editDistNet.value = Math.max(0, calculatedGross - tds).toFixed(2);
        }
    });

    editDistEligibleUnits.addEventListener('input', function() {
        const units = parseFloat(editDistEligibleUnits.value) || 0;
        const dpuVal = parseFloat(editDistDpu.value) || 0;
        const gross = parseFloat(editDistTotal.value) || 0;
        if (dpuVal > 0 && units > 0) {
            const calculatedGross = dpuVal * units;
            editDistTotal.value = calculatedGross.toFixed(2);
            const tds = parseFloat(editDistTds.value) || 0;
            editDistNet.value = Math.max(0, calculatedGross - tds).toFixed(2);
        } else if (gross > 0 && units > 0) {
            editDistDpu.value = (gross / units).toFixed(4);
        }
    });

    document.querySelectorAll('.open-edit-reit-dist').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editReitDistId').value = this.dataset.id;
            document.getElementById('editReitDistSubtitle').textContent = this.dataset.trust;
            document.getElementById('editDistPayoutDate').value = this.dataset.payoutDate;
            document.getElementById('editDistRecordDate').value = this.dataset.recordDate || '';
            document.getElementById('editDistEligibleUnits').value = this.dataset.eligibleUnits || '';
            document.getElementById('editDistDpu').value = this.dataset.dpu || '';
            document.getElementById('editDistInterest').value = this.dataset.interest || '0.00';
            document.getElementById('editDistDividend').value = this.dataset.dividend || '0.00';
            document.getElementById('editDistRoc').value = this.dataset.roc || '0.00';
            document.getElementById('editDistOther').value = this.dataset.other || '0.00';
            document.getElementById('editDistTotal').value = this.dataset.totalAmount || '';
            document.getElementById('editDistTds').value = this.dataset.tds || '0.00';
            document.getElementById('editDistNet').value = this.dataset.netReceived || '';
            document.getElementById('editDistQuarter').value = this.dataset.quarter || '';
            document.getElementById('editDistNotes').value = this.dataset.notes || '';
        });
    });

    // =========================================================================
    // 5. REITS/INVITS: DUAL FILTER & TOTAL AMOUNTS (DISTRIBUTIONS & TRADE LEDGER)
    // =========================================================================
    const cfg = window.REITS_CONFIG || {};
    const FY_RANGES = cfg.fyRanges || (typeof window.FY_RANGES !== 'undefined' ? window.FY_RANGES : {});

    function formatINR(val) {
        return '₹ ' + Number(val).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // --- A. DISTRIBUTION JOURNAL FILTER ENGINE ---
    const distTrustFilter = document.getElementById('distTrustFilter');
    const distDateFilter  = document.getElementById('distDateFilter');
    const distTable       = document.getElementById('reitDistTable');
    const distResetBtn    = document.getElementById('btnResetDistFilter');
    const distBadge       = document.getElementById('filteredDistBadge');
    const distSymbolSpan  = document.getElementById('filteredDistSymbol');
    const distNoRowsEl    = document.getElementById('distNoRows');

    const kpiDistNet     = document.getElementById('kpiDistNet');
    const kpiDistTds     = document.getElementById('kpiDistTds');
    const kpiDistTaxable = document.getElementById('kpiDistTaxable');
    const kpiDistExempt  = document.getElementById('kpiDistExempt');

    const footerDistSummaryTitle = document.getElementById('footerDistSummaryTitle');
    const footerDistInterest     = document.getElementById('footerDistInterest');
    const footerDistDividend     = document.getElementById('footerDistDividend');
    const footerDistRoc          = document.getElementById('footerDistRoc');
    const footerDistGross        = document.getElementById('footerDistGross');
    const footerDistTds          = document.getElementById('footerDistTds');
    const footerDistNet          = document.getElementById('footerDistNet');

    function applyDistFilters(updateUrl = true) {
        if (!distTable) return;

        const selectedTrust  = distTrustFilter ? distTrustFilter.value : 'ALL';
        const selectedFy     = distDateFilter ? distDateFilter.value : 'ALL';
        const selectedOption = distTrustFilter ? distTrustFilter.options[distTrustFilter.selectedIndex] : null;
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

        const rows = distTable.querySelectorAll('tbody tr.reit-dist-row');
        let visibleCount = 0;
        let totalNet     = 0.0;
        let totalTds     = 0.0;
        let totalInt     = 0.0;
        let totalDiv     = 0.0;
        let totalRoc     = 0.0;
        let totalGross   = 0.0;

        rows.forEach(row => {
            const rowReitId = row.dataset.reitId;
            const rowDate   = row.dataset.date;
            const rowInt    = parseFloat(row.dataset.interest) || 0.0;
            const rowDiv    = parseFloat(row.dataset.dividend) || 0.0;
            const rowRoc    = parseFloat(row.dataset.roc) || 0.0;
            const rowGross  = parseFloat(row.dataset.gross) || 0.0;
            const rowTds    = parseFloat(row.dataset.tds) || 0.0;
            const rowNet    = parseFloat(row.dataset.net) || 0.0;

            const matchesTrust = (selectedTrust === 'ALL' || rowReitId === selectedTrust);
            let matchesDate    = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesTrust && matchesDate) {
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;
                totalNet   += rowNet;
                totalTds   += rowTds;
                totalInt   += rowInt;
                totalDiv   += rowDiv;
                totalRoc   += rowRoc;
                totalGross += rowGross;
            } else {
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        if (distNoRowsEl) {
            distNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        const taxableTotal = totalInt;
        const exemptTotal  = totalDiv + totalRoc;

        // Update KPI Cards
        if (kpiDistNet)     kpiDistNet.textContent     = formatINR(totalNet);
        if (kpiDistTds)     kpiDistTds.textContent     = formatINR(totalTds);
        if (kpiDistTaxable) kpiDistTaxable.textContent = formatINR(taxableTotal);
        if (kpiDistExempt)  kpiDistExempt.textContent  = formatINR(exemptTotal);

        // Update Footer Totals
        if (footerDistSummaryTitle) {
            let label = selectedTrust === 'ALL' ? 'Total' : selectedSymbol;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerDistSummaryTitle.textContent = `${label} (${visibleCount} Distributions)`;
        }
        if (footerDistInterest) footerDistInterest.textContent = formatINR(totalInt);
        if (footerDistDividend) footerDistDividend.textContent = formatINR(totalDiv);
        if (footerDistRoc)      footerDistRoc.textContent      = formatINR(totalRoc);
        if (footerDistGross)    footerDistGross.textContent    = formatINR(totalGross);
        if (footerDistTds)      footerDistTds.textContent      = formatINR(totalTds);
        if (footerDistNet)      footerDistNet.textContent      = formatINR(totalNet);

        // Refresh pagination slice
        if (window.reitDistPager) {
            window.reitDistPager.refresh();
        }

        // Toggle reset button & filtered badge
        const hasActiveFilter = (selectedTrust !== 'ALL' || selectedFy !== 'ALL');
        if (distResetBtn) distResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (distBadge) {
            distBadge.classList.toggle('d-none', !hasActiveFilter);
            if (distSymbolSpan) {
                let badgeText = selectedTrust !== 'ALL' ? selectedSymbol : 'All Trusts';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                distSymbolSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedTrust === 'ALL') url.searchParams.delete('trust_id');
            else url.searchParams.set('trust_id', selectedTrust);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (distTrustFilter) distTrustFilter.addEventListener('change', () => applyDistFilters(true));
    if (distDateFilter)  distDateFilter.addEventListener('change', () => applyDistFilters(true));
    if (distResetBtn) {
        distResetBtn.addEventListener('click', () => {
            if (distTrustFilter) distTrustFilter.value = 'ALL';
            if (distDateFilter)  distDateFilter.value  = 'ALL';
            applyDistFilters(true);
        });
    }

    // --- B. TRANSACTION HISTORY (TRADE LEDGER) FILTER ENGINE ---
    const ledgerTrustFilter = document.getElementById('ledgerTrustFilter');
    const ledgerDateFilter  = document.getElementById('ledgerDateFilter');
    const ledgerTable       = document.getElementById('reitLedgerTable');
    const ledgerResetBtn    = document.getElementById('btnResetTrustFilter');
    const ledgerBadge       = document.getElementById('filteredTrustBadge');
    const ledgerSymbolSpan  = document.getElementById('filteredTrustSymbol');
    const ledgerNoRowsEl    = document.getElementById('ledgerNoRows');

    const kpiBuyAmount   = document.getElementById('kpiBuyAmount');
    const kpiBuyDetails  = document.getElementById('kpiBuyDetails');
    const kpiSellAmount  = document.getElementById('kpiSellAmount');
    const kpiSellDetails = document.getElementById('kpiSellDetails');
    const kpiNetOutlay   = document.getElementById('kpiNetOutlay');
    const kpiNetDetails  = document.getElementById('kpiNetDetails');
    const kpiCharges     = document.getElementById('kpiCharges');

    const footerSummaryTitle    = document.getElementById('footerSummaryTitle');
    const footerActionBreakdown = document.getElementById('footerActionBreakdown');
    const footerTotalQty        = document.getElementById('footerTotalQty');
    const footerTotalCharges    = document.getElementById('footerTotalCharges');
    const footerTotalAmount     = document.getElementById('footerTotalAmount');
    const footerNote            = document.getElementById('footerNote');

    function applyLedgerFilters(updateUrl = true) {
        if (!ledgerTable) return;

        const selectedTrust  = ledgerTrustFilter ? ledgerTrustFilter.value : 'ALL';
        const selectedFy     = ledgerDateFilter ? ledgerDateFilter.value : 'ALL';
        const selectedOption = ledgerTrustFilter ? ledgerTrustFilter.options[ledgerTrustFilter.selectedIndex] : null;
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

        const rows = ledgerTable.querySelectorAll('tbody tr.reit-ledger-row');
        let visibleCount = 0;
        let buyAmount    = 0.0;
        let sellAmount   = 0.0;
        let buyQty       = 0.0;
        let sellQty      = 0.0;
        let buyCount     = 0;
        let sellCount    = 0;
        let totalCharges = 0.0;

        rows.forEach(row => {
            const rowReitId = row.dataset.reitId;
            const rowDate   = row.dataset.date;
            const rowType   = row.dataset.type;
            const rowQty    = parseFloat(row.dataset.qty) || 0.0;
            const rowAmt    = parseFloat(row.dataset.amount) || 0.0;
            const rowChg    = parseFloat(row.dataset.charges) || 0.0;

            const matchesTrust = (selectedTrust === 'ALL' || rowReitId === selectedTrust);
            let matchesDate    = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesTrust && matchesDate) {
                row.classList.remove('d-none-filter');
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
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        if (ledgerNoRowsEl) {
            ledgerNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        const netOutlay = buyAmount - sellAmount;

        // Update KPI Cards
        if (kpiBuyAmount) kpiBuyAmount.textContent = formatINR(buyAmount);
        if (kpiBuyDetails) kpiBuyDetails.textContent = `${buyCount} buys • ${buyQty.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} units`;

        if (kpiSellAmount) kpiSellAmount.textContent = formatINR(sellAmount);
        if (kpiSellDetails) kpiSellDetails.textContent = `${sellCount} sells • ${sellQty.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} units`;

        if (kpiNetOutlay) {
            kpiNetOutlay.textContent = formatINR(netOutlay);
            kpiNetOutlay.className = 'fw-bold mb-0 mt-1 ' + (netOutlay >= 0 ? 'text-primary' : 'text-success');
        }
        if (kpiNetDetails) {
            if (selectedTrust === 'ALL') {
                kpiNetDetails.textContent = 'Net cash invested in period';
            } else {
                const heldDiff = buyQty - sellQty;
                kpiNetDetails.textContent = `Net held from trades: ${heldDiff.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} units`;
            }
        }

        if (kpiCharges) kpiCharges.textContent = formatINR(totalCharges);

        // Update Footer Totals
        if (footerSummaryTitle) {
            let label = selectedTrust === 'ALL' ? 'Total' : selectedSymbol;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerSummaryTitle.textContent = `${label} (${visibleCount} Trades)`;
        }

        if (footerActionBreakdown) {
            footerActionBreakdown.innerHTML = `<span class="text-success">${buyCount} BUY</span> &bull; <span class="text-danger">${sellCount} SELL</span>`;
        }

        if (footerTotalQty) footerTotalQty.textContent = (buyQty + sellQty).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (footerTotalCharges) footerTotalCharges.textContent = formatINR(totalCharges);
        if (footerTotalAmount) {
            footerTotalAmount.textContent = formatINR(netOutlay);
            footerTotalAmount.className = 'text-end fw-bold fs-6 ' + (netOutlay >= 0 ? 'text-dark' : 'text-success');
        }
        if (footerNote) footerNote.textContent = netOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized';

        // Refresh pagination slice
        if (window.reitLedgerPager) {
            window.reitLedgerPager.refresh();
        }

        // Toggle reset button & filtered badge
        const hasActiveFilter = (selectedTrust !== 'ALL' || selectedFy !== 'ALL');
        if (ledgerResetBtn) ledgerResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (ledgerBadge) {
            ledgerBadge.classList.toggle('d-none', !hasActiveFilter);
            if (ledgerSymbolSpan) {
                let badgeText = selectedTrust !== 'ALL' ? selectedSymbol : 'All Trusts';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                ledgerSymbolSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedTrust === 'ALL') url.searchParams.delete('trust_id');
            else url.searchParams.set('trust_id', selectedTrust);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (ledgerTrustFilter) ledgerTrustFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerDateFilter)  ledgerDateFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerResetBtn) {
        ledgerResetBtn.addEventListener('click', () => {
            if (ledgerTrustFilter) ledgerTrustFilter.value = 'ALL';
            if (ledgerDateFilter)  ledgerDateFilter.value  = 'ALL';
            applyLedgerFilters(true);
        });
    }

    // Shortcuts from Holdings dropdown
    window.showTrustLedger = function(trustId) {
        const transTabBtn = document.getElementById('transactions-tab');
        if (transTabBtn) {
            bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        }
        if (ledgerTrustFilter) {
            ledgerTrustFilter.value = String(trustId);
            applyLedgerFilters(true);
        }
    };

    window.showTrustDistributions = function(trustId) {
        const distTabBtn = document.getElementById('distributions-tab');
        if (distTabBtn) {
            bootstrap.Tab.getOrCreateInstance(distTabBtn).show();
        }
        if (distTrustFilter) {
            distTrustFilter.value = String(trustId);
            applyDistFilters(true);
        }
    };

    // ==========================================
    // REIT / INVIT HOLDINGS SORTING
    // ==========================================
    const sortSelect = document.getElementById('sortReitsSelect');
    const holdingsTbody = document.getElementById('reitHoldingsTbody');
    const sortableHeaders = document.querySelectorAll('.sortable-reit-th');

    function sortReitsHoldings(sortBy) {
        if (!holdingsTbody) return;
        const rows = Array.from(holdingsTbody.querySelectorAll('tr.reit-holding-row'));
        if (rows.length === 0) return;

        const [field, dir] = sortBy.split('_');
        const isAsc = dir === 'asc';

        rows.sort((a, b) => {
            let valA, valB;
            if (field === 'security') {
                valA = (a.dataset.security || '').toLowerCase();
                valB = (b.dataset.security || '').toLowerCase();
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (field === 'type') {
                valA = (a.dataset.type || '').toLowerCase();
                valB = (b.dataset.type || '').toLowerCase();
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
            sortReitsHoldings(this.value);
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
            sortReitsHoldings(newVal);
        });
    });

    // ==========================================
    // FIFO TAX LOG FILTERING (Trust, Gain Type, Date Range)
    // ==========================================
    const taxReitFilter  = document.getElementById('taxLogReitFilter');
    const taxTypeFilter  = document.getElementById('taxLogTypeFilter');
    const taxDateFilter  = document.getElementById('taxLogDateFilter');
    const taxResetBtn    = document.getElementById('btnResetTaxFilter');
    const taxBadge       = document.getElementById('filteredTaxBadge');
    const taxReitSpan    = document.getElementById('filteredTaxReitSpan');
    const taxTable       = document.getElementById('reitTaxLogTable');

    // KPI Cards
    const kpiTaxUnits     = document.getElementById('kpiTaxUnits');
    const kpiTaxQtyCount  = document.getElementById('kpiTaxQtyCount');
    const kpiTaxCost      = document.getElementById('kpiTaxCost');
    const kpiTaxProceeds  = document.getElementById('kpiTaxProceeds');
    const kpiTaxGain      = document.getElementById('kpiTaxGain');
    const kpiTaxBreakdown = document.getElementById('kpiTaxBreakdown');

    // Dynamic Footer
    const footerTaxSummaryTitle = document.getElementById('footerTaxSummaryTitle');
    const footerTaxBreakdown    = document.getElementById('footerTaxBreakdown');
    const footerTaxCost         = document.getElementById('footerTaxCost');
    const footerTaxProceeds     = document.getElementById('footerTaxProceeds');
    const footerTaxGain         = document.getElementById('footerTaxGain');

    function applyReitTaxFilters(updateUrl = true) {
        if (!taxTable) return;

        const selectedTrust = taxReitFilter ? taxReitFilter.value : 'ALL';
        const selectedType  = taxTypeFilter ? taxTypeFilter.value : 'ALL';
        const selectedFy    = taxDateFilter ? taxDateFilter.value : 'ALL';

        let fyStart = null;
        let fyEnd   = null;
        if (selectedFy === 'CURRENT_FY' && FY_RANGES.current) {
            fyStart = FY_RANGES.current.start;
            fyEnd   = FY_RANGES.current.end;
        } else if (selectedFy === 'LAST_FY' && FY_RANGES.last) {
            fyStart = FY_RANGES.last.start;
            fyEnd   = FY_RANGES.last.end;
        }

        const rows = taxTable.querySelectorAll('tbody tr.reit-taxlog-row');
        let visibleCount = 0;
        let totalMatchedUnits = 0;
        let totalCostBasis = 0;
        let totalNetProceeds = 0;
        let totalRealizedGain = 0;
        let totalStcg = 0;
        let totalLtcg = 0;
        let selectedSymbol = '';

        if (selectedTrust !== 'ALL' && taxReitFilter) {
            const opt = taxReitFilter.querySelector(`option[value="${selectedTrust}"]`);
            if (opt) selectedSymbol = opt.dataset.symbol || opt.textContent.split('—')[0].trim();
        }

        rows.forEach(row => {
            const rowTrustId  = row.dataset.trustId;
            const rowGainType = row.dataset.gainType;
            const rowDate     = row.dataset.date;

            const matchesTrust = (selectedTrust === 'ALL' || rowTrustId === selectedTrust);
            const matchesType  = (selectedType === 'ALL' || rowGainType === selectedType);
            let matchesDate    = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesTrust && matchesType && matchesDate) {
                row.classList.remove('d-none');
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;

                const units    = parseFloat(row.dataset.units) || 0;
                const cost     = parseFloat(row.dataset.cost) || 0;
                const proceeds = parseFloat(row.dataset.proceeds) || 0;
                const gain     = parseFloat(row.dataset.gain) || 0;

                totalMatchedUnits += units;
                totalCostBasis    += cost;
                totalNetProceeds  += proceeds;
                totalRealizedGain += gain;

                if (rowGainType === 'LTCG') {
                    totalLtcg += gain;
                } else {
                    totalStcg += gain;
                }
            } else {
                row.classList.add('d-none');
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        // Update 4 KPI Cards
        if (kpiTaxUnits) kpiTaxUnits.textContent = totalMatchedUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
        if (kpiTaxQtyCount) kpiTaxQtyCount.textContent = `${visibleCount} FIFO lots`;
        if (kpiTaxCost) kpiTaxCost.textContent = formatINR(totalCostBasis);
        if (kpiTaxProceeds) kpiTaxProceeds.textContent = formatINR(totalNetProceeds);
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

        // Update Dynamic Footer
        if (footerTaxSummaryTitle) {
            let label = selectedTrust === 'ALL' ? 'Total' : selectedSymbol;
            if (selectedType !== 'ALL') label += ` [${selectedType}]`;
            if (selectedFy !== 'ALL') label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            footerTaxSummaryTitle.textContent = `${label} (${visibleCount} Lots)`;
        }
        if (footerTaxBreakdown) {
            footerTaxBreakdown.textContent = `STCG: ${formatINR(totalStcg)} • LTCG: ${formatINR(totalLtcg)}`;
        }
        if (footerTaxCost) footerTaxCost.textContent = formatINR(totalCostBasis);
        if (footerTaxProceeds) footerTaxProceeds.textContent = formatINR(totalNetProceeds);
        if (footerTaxGain) {
            footerTaxGain.textContent = (totalRealizedGain >= 0 ? '+' : '') + formatINR(totalRealizedGain);
            footerTaxGain.className = 'text-end py-3 fs-6 fw-bold ' + (totalRealizedGain >= 0 ? 'text-success' : 'text-danger');
        }

        // Refresh pagination slice
        if (window.reitTaxPager) {
            window.reitTaxPager.refresh();
        }

        // Toggle reset button & active filter badge
        const hasActiveFilter = (selectedTrust !== 'ALL' || selectedType !== 'ALL' || selectedFy !== 'ALL');
        if (taxResetBtn) taxResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (taxBadge) {
            taxBadge.classList.toggle('d-none', !hasActiveFilter);
            if (taxReitSpan) {
                let badgeParts = [];
                if (selectedTrust !== 'ALL') badgeParts.push(selectedSymbol);
                if (selectedType !== 'ALL') badgeParts.push(selectedType);
                if (selectedFy !== 'ALL') badgeParts.push(selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                taxReitSpan.textContent = badgeParts.join(' • ');
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedTrust === 'ALL') url.searchParams.delete('tax_trust_id');
            else url.searchParams.set('tax_trust_id', selectedTrust);

            if (selectedType === 'ALL') url.searchParams.delete('tax_type');
            else url.searchParams.set('tax_type', selectedType);

            if (selectedFy === 'ALL') url.searchParams.delete('tax_fy');
            else url.searchParams.set('tax_fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (taxReitFilter) taxReitFilter.addEventListener('change', () => applyReitTaxFilters(true));
    if (taxTypeFilter) taxTypeFilter.addEventListener('change', () => applyReitTaxFilters(true));
    if (taxDateFilter) taxDateFilter.addEventListener('change', () => applyReitTaxFilters(true));
    if (taxResetBtn) {
        taxResetBtn.addEventListener('click', () => {
            if (taxReitFilter) taxReitFilter.value = 'ALL';
            if (taxTypeFilter) taxTypeFilter.value = 'ALL';
            if (taxDateFilter) taxDateFilter.value = 'ALL';
            applyReitTaxFilters(true);
        });
    }

    // ==========================================
    // TABLE PAGINATION INITIALIZATION
    // ==========================================
    if (typeof window.initTablePagination === 'function') {
        window.reitLedgerPager = window.initTablePagination({
            tableId: 'reitLedgerTable',
            rowSelector: '.reit-ledger-row',
            infoId: 'reitLedgerPageInfo',
            sizeSelectId: 'reitLedgerPageSize',
            controlsId: 'reitLedgerPageControls'
        });

        window.reitTaxPager = window.initTablePagination({
            tableId: 'reitTaxLogTable',
            rowSelector: '.reit-taxlog-row',
            infoId: 'reitTaxPageInfo',
            sizeSelectId: 'reitTaxPageSize',
            controlsId: 'reitTaxPageControls'
        });

        window.reitDistPager = window.initTablePagination({
            tableId: 'reitDistTable',
            rowSelector: '.reit-dist-row',
            infoId: 'reitDistPageInfo',
            sizeSelectId: 'reitDistPageSize',
            controlsId: 'reitDistPageControls'
        });
    }

    // Deep link handling on page load
    const urlParams = new URLSearchParams(window.location.search);
    const initialTrustId = urlParams.get('trust_id');
    const initialFy      = urlParams.get('fy');
    const initialTab     = urlParams.get('tab');
    const hash           = window.location.hash;

    if (initialTrustId) {
        if (ledgerTrustFilter) ledgerTrustFilter.value = initialTrustId;
        if (distTrustFilter)   distTrustFilter.value   = initialTrustId;
    }
    if (initialFy) {
        if (ledgerDateFilter) ledgerDateFilter.value = initialFy;
        if (distDateFilter)   distDateFilter.value   = initialFy;
    }

    if ((initialTrustId || initialFy || initialTab || hash) && (ledgerTable || distTable)) {
        if (ledgerTable) applyLedgerFilters(false);
        if (distTable)   applyDistFilters(false);

        if (initialTab === 'past-holdings' || hash === '#past-holdings') {
            const pastTabBtn = document.getElementById('past-holdings-tab');
            if (pastTabBtn) bootstrap.Tab.getOrCreateInstance(pastTabBtn).show();
        } else if (initialTab === 'transactions' || (initialTrustId && !initialTab) || hash === '#transactions') {
            const transTabBtn = document.getElementById('transactions-tab');
            if (transTabBtn) bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        } else if (initialTab === 'distributions' || hash === '#distributions') {
            const distTabBtn = document.getElementById('distributions-tab');
            if (distTabBtn) bootstrap.Tab.getOrCreateInstance(distTabBtn).show();
        }
    }

    const taxReitParam = urlParams.get('tax_trust_id');
    const taxTypeParam = urlParams.get('tax_type');
    const taxFyParam   = urlParams.get('tax_fy');
    if (taxReitParam || taxTypeParam || taxFyParam || initialTab === 'capitalgains' || hash === '#capitalgains') {
        const taxTabBtn = document.getElementById('capitalgains-tab');
        if (taxTabBtn && (initialTab === 'capitalgains' || hash === '#capitalgains')) {
            bootstrap.Tab.getOrCreateInstance(taxTabBtn).show();
        }
        if (taxReitParam && taxReitFilter) taxReitFilter.value = taxReitParam;
        if (taxTypeParam && taxTypeFilter) taxTypeFilter.value = taxTypeParam;
        if (taxFyParam && taxDateFilter) taxDateFilter.value = taxFyParam;
        applyReitTaxFilters(false);
    }
});
