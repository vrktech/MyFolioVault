/**
 * MyFolioVault - Bonds Module Scripts
 * 100% Offline Compatible
 */

document.addEventListener('DOMContentLoaded', function() {
    let currentBond = {};

    const cfg = window.BONDS_CONFIG || {};
    const FY_RANGES = cfg.fyRanges || (typeof window.FY_RANGES !== 'undefined' ? window.FY_RANGES : {});

    // Hook Edit Bond buttons
    document.querySelectorAll('.open-edit-bond-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editBondId');
            const elName = document.getElementById('editBondName');
            const elIsin = document.getElementById('editBondIsin');
            const elSymbol = document.getElementById('editBondSymbol');
            const elCat = document.getElementById('editBondCategory');
            const elIssuer = document.getElementById('editBondIssuer');
            const elFace = document.getElementById('editBondFaceValue');
            const elRate = document.getElementById('editBondCouponRate');
            const elFreq = document.getElementById('editBondFrequency');
            const elMaturity = document.getElementById('editBondMaturityDate');
            const elIssue = document.getElementById('editBondIssueDate');

            if (elId) elId.value = this.dataset.id;
            if (elName) elName.value = this.dataset.name;
            if (elIsin) elIsin.value = this.dataset.isin;
            if (elSymbol) elSymbol.value = this.dataset.symbol || '';
            if (elCat) elCat.value = this.dataset.cat;
            if (elIssuer) elIssuer.value = this.dataset.issuer || '';
            if (elFace) elFace.value = parseFloat(this.dataset.face || 0).toFixed(2);
            if (elRate) elRate.value = parseFloat(this.dataset.rate || 0).toFixed(2);
            if (elFreq) elFreq.value = this.dataset.freq;
            if (elMaturity) elMaturity.value = this.dataset.maturity;
            if (elIssue) elIssue.value = this.dataset.issue || '';
        });
    });

    // Hook Edit Transaction buttons
    document.querySelectorAll('.open-edit-bond-trans').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editBondTransId');
            const elTitle = document.getElementById('editBondTransTitle');
            const elDate = document.getElementById('editBondTransDate');
            const elQty = document.getElementById('editBondTransQty');
            const elPrice = document.getElementById('editBondTransPrice');
            const elCharges = document.getElementById('editBondTransCharges');
            const elAccrued = document.getElementById('editBondTransAccrued');
            const elNotes = document.getElementById('editBondTransNotes');

            if (elId) elId.value = this.dataset.id;
            if (elTitle) elTitle.textContent = 'Edit ' + this.dataset.type + ': ' + this.dataset.name;
            if (elDate) elDate.value = this.dataset.date;
            if (elQty) elQty.value = parseFloat(this.dataset.qty);
            if (elPrice) elPrice.value = parseFloat(this.dataset.price || 0).toFixed(2);
            if (elCharges) elCharges.value = parseFloat(this.dataset.charges || 0).toFixed(2);
            if (elAccrued) elAccrued.value = parseFloat(this.dataset.accrued || 0).toFixed(2);
            if (elNotes) elNotes.value = this.dataset.notes || '';
        });
    });

    // Hook Edit Interest buttons
    document.querySelectorAll('.open-edit-bond-interest').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editBondIntId');
            const elTitle = document.getElementById('editBondIntTitle');
            const elDate = document.getElementById('editBondIntDate');
            const elRate = document.getElementById('editBondIntRate');
            const elGross = document.getElementById('editBondIntGross');
            const elTds = document.getElementById('editBondIntTds');
            const elDesc = document.getElementById('editBondIntDesc');
            const elNotes = document.getElementById('editBondIntNotes');

            if (elId) elId.value = this.dataset.id;
            if (elTitle) elTitle.textContent = 'Edit Interest Payment: ' + this.dataset.name;
            if (elDate) elDate.value = this.dataset.date;
            if (elRate) elRate.value = parseFloat(this.dataset.rate || 0).toFixed(2);
            if (elGross) elGross.value = parseFloat(this.dataset.gross || 0).toFixed(2);
            if (elTds) elTds.value = parseFloat(this.dataset.tds || 0).toFixed(2);
            if (elDesc) elDesc.value = this.dataset.desc || '';
            if (elNotes) elNotes.value = this.dataset.notes || '';
        });
    });

    // 1. Hook "Add Trans" buttons to populate modal
    document.querySelectorAll('.open-bond-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentBond = {
                id: this.dataset.id,
                name: this.dataset.name,
                isin: this.dataset.isin,
                cat: this.dataset.cat,
                qty: parseFloat(this.dataset.qty) || 0,
                cmp: parseFloat(this.dataset.cmp) || 0,
                avgPrice: parseFloat(this.dataset.avg) || 0,
                faceValue: parseFloat(this.dataset.face) || 1000,
                couponRate: parseFloat(this.dataset.rate) || 0,
                frequency: this.dataset.freq || 'SEMI_ANNUAL'
            };

            const elId = document.getElementById('modalBondId');
            const elName = document.getElementById('modalBondName');
            const elSubtitle = document.getElementById('modalBondSubtitle');
            if (elId) elId.value = currentBond.id;
            if (elName) elName.textContent = currentBond.name;
            if (elSubtitle) elSubtitle.textContent = 'ISIN: ' + currentBond.isin + ' | Available: ' + Math.round(currentBond.qty) + ' | CMP: ₹ ' + currentBond.cmp.toFixed(2);

            // Set defaults in Buy Panel
            const buyPriceEl = document.getElementById('buyBondPrice');
            const buyQtyEl = document.getElementById('buyBondQty');
            const buyChgEl = document.getElementById('buyBondCharges');
            const buyAccEl = document.getElementById('buyBondAccrued');
            const buyTotalEl = document.getElementById('buyBondTotalPreview');

            if (buyPriceEl) buyPriceEl.value = currentBond.cmp.toFixed(2);
            if (buyQtyEl) buyQtyEl.value = '';
            if (buyChgEl) buyChgEl.value = '0.00';
            if (buyAccEl) buyAccEl.value = '0.00';
            if (buyTotalEl) buyTotalEl.textContent = '₹ 0.00';

            // Set defaults in Sell Panel
            const sellBadge = document.getElementById('sellBondAvailableBadge');
            const sellQtyEl = document.getElementById('sellBondQty');
            const sellPriceEl = document.getElementById('sellBondPrice');
            const sellChgEl = document.getElementById('sellBondCharges');
            const sellProceedsEl = document.getElementById('sellBondProceedsPreview');
            const sellPnlEl = document.getElementById('sellBondPnlPreview');

            if (sellBadge) sellBadge.textContent = Math.round(currentBond.qty) + ' units/grams';
            if (sellQtyEl) {
                sellQtyEl.max = currentBond.qty;
                sellQtyEl.value = '';
            }
            if (sellPriceEl) sellPriceEl.value = currentBond.cmp.toFixed(2);
            if (sellChgEl) sellChgEl.value = '0.00';
            if (sellProceedsEl) sellProceedsEl.textContent = '₹ 0.00';
            if (sellPnlEl) sellPnlEl.textContent = '₹ 0.00';

            // Set defaults in Interest Panel
            const intBadge = document.getElementById('interestBondCalcBadge');
            if (intBadge) intBadge.textContent = currentBond.couponRate.toFixed(2) + '% p.a. (' + currentBond.frequency + ')';

            let periodsPerYear = 1;
            if (currentBond.frequency === 'MONTHLY') periodsPerYear = 12;
            else if (currentBond.frequency === 'QUARTERLY') periodsPerYear = 4;
            else if (currentBond.frequency === 'SEMI_ANNUAL') periodsPerYear = 2;

            const estGross = (currentBond.qty * currentBond.faceValue * (currentBond.couponRate / 100)) / periodsPerYear;
            const intGrossEl = document.getElementById('interestGross');
            const intTdsEl = document.getElementById('interestTds');
            const intNetEl = document.getElementById('interestNet');

            if (intGrossEl) intGrossEl.value = estGross > 0 ? estGross.toFixed(2) : '';
            if (intTdsEl) intTdsEl.value = '0.00';
            if (intNetEl) intNetEl.value = estGross > 0 ? estGross.toFixed(2) : '';

            // Reset to Buy panel
            const actBuyRadio = document.getElementById('actBondBuy');
            if (actBuyRadio) actBuyRadio.checked = true;
            switchBondAction('BUY');
        });
    });

    // 2. Action Tab Switching (Buy / Sell / Interest)
    const actRadios = document.querySelectorAll('input[name="action_type"]');
    actRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            switchBondAction(this.value);
        });
    });

    function switchBondAction(type) {
        const pBuy = document.getElementById('panelBondBuy');
        const pSell = document.getElementById('panelBondSell');
        const pInterest = document.getElementById('panelBondInterest');
        const submitBtn = document.getElementById('btnSubmitBondTrans');

        [pBuy, pSell, pInterest].forEach(p => {
            if (p) {
                p.classList.add('d-none');
                setInputsDisabled(p, true);
            }
        });

        if (type === 'BUY') {
            if (pBuy) pBuy.classList.remove('d-none');
            setInputsDisabled(pBuy, false);
            if (submitBtn) {
                submitBtn.className = 'btn btn-success btn-sm px-4 fw-semibold';
                submitBtn.textContent = 'Confirm Purchase Lot';
            }
        } else if (type === 'SELL') {
            if (pSell) pSell.classList.remove('d-none');
            setInputsDisabled(pSell, false);
            if (submitBtn) {
                submitBtn.className = 'btn btn-danger btn-sm px-4 fw-semibold';
                submitBtn.textContent = 'Execute FIFO Sell';
            }
        } else if (type === 'INTEREST') {
            if (pInterest) pInterest.classList.remove('d-none');
            setInputsDisabled(pInterest, false);
            if (submitBtn) {
                submitBtn.className = 'btn btn-primary btn-sm px-4 fw-semibold';
                submitBtn.textContent = 'Record Interest Payout';
            }
        }
    }

    function setInputsDisabled(container, disabled) {
        if (!container) return;
        container.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    // 3. Live calculations in BUY modal
    const buyQty = document.getElementById('buyBondQty');
    const buyPrice = document.getElementById('buyBondPrice');
    const buyCharges = document.getElementById('buyBondCharges');
    const buyAccrued = document.getElementById('buyBondAccrued');
    const buyPreview = document.getElementById('buyBondTotalPreview');

    function calcBuy() {
        if (!buyQty || !buyPrice || !buyPreview) return;
        const q = parseFloat(buyQty.value) || 0;
        const p = parseFloat(buyPrice.value) || 0;
        const c = parseFloat(buyCharges ? buyCharges.value : 0) || 0;
        const a = parseFloat(buyAccrued ? buyAccrued.value : 0) || 0;
        const total = (q * p) + c + a;
        buyPreview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    [buyQty, buyPrice, buyCharges, buyAccrued].forEach(el => {
        if (el) el.addEventListener('input', calcBuy);
    });

    // 4. Live calculations in SELL modal
    const sellQty = document.getElementById('sellBondQty');
    const sellPrice = document.getElementById('sellBondPrice');
    const sellCharges = document.getElementById('sellBondCharges');
    const sellProceeds = document.getElementById('sellBondProceedsPreview');
    const sellPnl = document.getElementById('sellBondPnlPreview');

    function calcSell() {
        if (!sellQty || !sellPrice || !sellProceeds) return;
        const q = parseFloat(sellQty.value) || 0;
        const p = parseFloat(sellPrice.value) || 0;
        const c = parseFloat(sellCharges ? sellCharges.value : 0) || 0;
        const net = Math.max(0, (q * p) - c);
        const costBasis = q * (currentBond.avgPrice || 0);
        const estGain = net - costBasis;

        sellProceeds.textContent = '₹ ' + net.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (sellPnl) {
            const sign = estGain >= 0 ? '+' : '';
            sellPnl.textContent = sign + '₹ ' + estGain.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            sellPnl.className = 'small fw-semibold ' + (estGain >= 0 ? 'text-success' : 'text-danger');
        }
    }
    [sellQty, sellPrice, sellCharges].forEach(el => {
        if (el) el.addEventListener('input', calcSell);
    });

    // 5. Live calculations in INTEREST modal
    const interestGross = document.getElementById('interestGross');
    const interestTds = document.getElementById('interestTds');
    const interestNet = document.getElementById('interestNet');

    function calcInterest() {
        if (!interestGross || !interestTds || !interestNet) return;
        const g = parseFloat(interestGross.value) || 0;
        const t = parseFloat(interestTds.value) || 0;
        const net = Math.max(0, g - t);
        interestNet.value = net.toFixed(2);
    }
    if (interestGross) interestGross.addEventListener('input', calcInterest);
    if (interestTds) interestTds.addEventListener('input', calcInterest);

    // 6. Dedicated Maturity Redemption Modal
    const matQty = document.getElementById('maturityRedeemQty');
    const matPrice = document.getElementById('maturityRedeemPrice');
    const matPreview = document.getElementById('maturityProceedsPreview');

    function calcMaturityProceeds() {
        if (!matQty || !matPrice || !matPreview) return;
        const q = parseFloat(matQty.value) || 0;
        const p = parseFloat(matPrice.value) || 0;
        const total = q * p;
        matPreview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    if (matQty) matQty.addEventListener('input', calcMaturityProceeds);
    if (matPrice) matPrice.addEventListener('input', calcMaturityProceeds);

    document.querySelectorAll('.open-maturity-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const isin = this.dataset.isin || '';
            const cat = this.dataset.cat || '';
            const qty = parseFloat(this.dataset.qty) || 0;
            const face = parseFloat(this.dataset.face) || 100;
            const cmp = parseFloat(this.dataset.cmp) || face;
            const maturity = this.dataset.maturity || '';

            const elBondId = document.getElementById('maturityRedeemBondId');
            const elName = document.getElementById('maturityModalBondName');
            const elSub = document.getElementById('maturityModalBondSubtitle');
            const elHint = document.getElementById('maturityMaxQtyHint');

            if (elBondId) elBondId.value = id;
            if (elName) elName.textContent = name;
            if (elSub) elSub.textContent = `${cat} • ISIN: ${isin} • Matured on ${maturity}`;

            const defPrice = (cat === 'SGB' ? cmp : face);
            if (matQty) {
                matQty.value = Math.round(qty);
                matQty.max = qty;
            }
            if (matPrice) matPrice.value = defPrice.toFixed(2);
            if (elHint) elHint.textContent = `Max eligible units: ${Math.round(qty)} ${cat === 'SGB' ? 'grams' : 'units'}`;

            const sgbAlert = document.getElementById('maturitySgbExemptAlert');
            if (sgbAlert) sgbAlert.classList.toggle('d-none', cat !== 'SGB');

            calcMaturityProceeds();
        });
    });

    // 7. Quick CMP Modal population
    document.querySelectorAll('.open-price-modal').forEach(a => {
        a.addEventListener('click', function() {
            const elId = document.getElementById('priceModalBondId');
            const elName = document.getElementById('priceModalBondName');
            const elPrice = document.getElementById('priceModalPrice');

            if (elId) elId.value = this.dataset.id;
            if (elName) elName.textContent = this.dataset.name;
            if (elPrice) elPrice.value = parseFloat(this.dataset.price || 0).toFixed(2);
        });
    });

    // =========================================================================
    // 8. BONDS: DUAL FILTER & TOTAL AMOUNTS (INTEREST PAYOUTS & TRADE LEDGER)
    // =========================================================================
    function formatINR(val) {
        return '₹ ' + Number(val).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function matchFy(dateStr, fyMode) {
        if (!fyMode || fyMode === 'ALL') return true;
        if (!dateStr) return false;
        let range = null;
        if (fyMode === 'CURRENT_FY') range = FY_RANGES.current;
        else if (fyMode === 'LAST_FY') range = FY_RANGES.last;
        if (!range || !range.start || !range.end) return true;
        return dateStr >= range.start && dateStr <= range.end;
    }

    // --- A. INTEREST PAYOUTS FILTER ENGINE ---
    const payoutBondFilter = document.getElementById('payoutBondFilter');
    const payoutDateFilter = document.getElementById('payoutDateFilter');
    const payoutTable      = document.getElementById('bondPayoutsTable');
    const payoutResetBtn   = document.getElementById('btnResetPayoutFilter');
    const payoutBadge      = document.getElementById('filteredPayoutBadge');
    const payoutNameSpan   = document.getElementById('filteredPayoutName');
    const payoutNoRowsEl   = document.getElementById('payoutNoRows');

    const kpiPayoutGross = document.getElementById('kpiPayoutGross');
    const kpiPayoutTds   = document.getElementById('kpiPayoutTds');
    const kpiPayoutNet   = document.getElementById('kpiPayoutNet');
    const kpiPayoutCount = document.getElementById('kpiPayoutCount');

    const footerPayoutSummaryTitle = document.getElementById('footerPayoutSummaryTitle');
    const footerPayoutGross        = document.getElementById('footerPayoutGross');
    const footerPayoutTds          = document.getElementById('footerPayoutTds');
    const footerPayoutNet          = document.getElementById('footerPayoutNet');

    function applyPayoutFilters(updateUrl = true) {
        if (!payoutTable) return;

        const selectedBond   = payoutBondFilter ? payoutBondFilter.value : 'ALL';
        const selectedFy     = payoutDateFilter ? payoutDateFilter.value : 'ALL';
        const selectedOption = payoutBondFilter ? payoutBondFilter.options[payoutBondFilter.selectedIndex] : null;
        const selectedName   = selectedOption ? (selectedOption.dataset.name || '') : '';

        const rows = payoutTable.querySelectorAll('tbody tr.bond-payout-row');
        let visibleCount = 0;
        let totalGross   = 0.0;
        let totalTds     = 0.0;
        let totalNet     = 0.0;

        rows.forEach(row => {
            const rowBondId = row.dataset.bondId;
            const rowDate   = row.dataset.date;
            const rowGross  = parseFloat(row.dataset.gross) || 0.0;
            const rowTds    = parseFloat(row.dataset.tds) || 0.0;
            const rowNet    = parseFloat(row.dataset.net) || 0.0;

            const matchesBond = (selectedBond === 'ALL' || rowBondId === selectedBond);
            const matchesDate = matchFy(rowDate, selectedFy);

            if (matchesBond && matchesDate) {
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;
                totalGross += rowGross;
                totalTds   += rowTds;
                totalNet   += rowNet;
            } else {
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        if (payoutNoRowsEl) {
            payoutNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        // Update KPI Cards
        if (kpiPayoutGross) kpiPayoutGross.textContent = formatINR(totalGross);
        if (kpiPayoutTds)   kpiPayoutTds.textContent   = formatINR(totalTds);
        if (kpiPayoutNet)   kpiPayoutNet.textContent   = formatINR(totalNet);
        if (kpiPayoutCount) kpiPayoutCount.textContent = `${visibleCount} Credits`;

        // Update Footer Totals
        if (footerPayoutSummaryTitle) {
            let label = selectedBond === 'ALL' ? 'Total' : selectedName;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerPayoutSummaryTitle.textContent = `${label} (${visibleCount} Payouts)`;
        }
        if (footerPayoutGross) footerPayoutGross.textContent = formatINR(totalGross);
        if (footerPayoutTds)   footerPayoutTds.textContent   = formatINR(totalTds);
        if (footerPayoutNet)   footerPayoutNet.textContent   = formatINR(totalNet);

        // Refresh pagination slice
        if (window.bondPayoutPager) {
            window.bondPayoutPager.refresh();
        }

        // Toggle reset button & filtered badge
        const hasActiveFilter = (selectedBond !== 'ALL' || selectedFy !== 'ALL');
        if (payoutResetBtn) payoutResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (payoutBadge) {
            payoutBadge.classList.toggle('d-none', !hasActiveFilter);
            if (payoutNameSpan) {
                let badgeText = selectedBond !== 'ALL' ? selectedName : 'All Bonds';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                payoutNameSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedBond === 'ALL') url.searchParams.delete('bond_id');
            else url.searchParams.set('bond_id', selectedBond);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (payoutBondFilter) payoutBondFilter.addEventListener('change', () => applyPayoutFilters(true));
    if (payoutDateFilter) payoutDateFilter.addEventListener('change', () => applyPayoutFilters(true));
    if (payoutResetBtn) {
        payoutResetBtn.addEventListener('click', () => {
            if (payoutBondFilter) payoutBondFilter.value = 'ALL';
            if (payoutDateFilter) payoutDateFilter.value = 'ALL';
            applyPayoutFilters(true);
        });
    }

    // --- B. TRADE LEDGER FILTER ENGINE ---
    const ledgerBondFilter = document.getElementById('ledgerBondFilter');
    const ledgerDateFilter = document.getElementById('ledgerDateFilter');
    const ledgerTable      = document.getElementById('bondLedgerTable');
    const ledgerResetBtn   = document.getElementById('btnResetBondFilter');
    const ledgerBadge      = document.getElementById('filteredBondBadge');
    const ledgerNameSpan   = document.getElementById('filteredBondName');
    const ledgerNoRowsEl   = document.getElementById('ledgerNoRows');

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

        const selectedBond   = ledgerBondFilter ? ledgerBondFilter.value : 'ALL';
        const selectedFy     = ledgerDateFilter ? ledgerDateFilter.value : 'ALL';
        const selectedOption = ledgerBondFilter ? ledgerBondFilter.options[ledgerBondFilter.selectedIndex] : null;
        const selectedName   = selectedOption ? (selectedOption.dataset.name || '') : '';

        const rows = ledgerTable.querySelectorAll('tbody tr.bond-ledger-row');
        let visibleCount = 0;
        let buyAmount    = 0.0;
        let sellAmount   = 0.0;
        let buyQty       = 0.0;
        let sellQty      = 0.0;
        let buyCount     = 0;
        let sellCount    = 0;
        let totalCharges = 0.0;

        rows.forEach(row => {
            const rowBondId = row.dataset.bondId;
            const rowDate   = row.dataset.date;
            const rowType   = row.dataset.type;
            const rowQty    = parseFloat(row.dataset.qty) || 0.0;
            const rowAmt    = parseFloat(row.dataset.amount) || 0.0;
            const rowChg    = parseFloat(row.dataset.charges) || 0.0;

            const matchesBond = (selectedBond === 'ALL' || rowBondId === selectedBond);
            const matchesDate = matchFy(rowDate, selectedFy);

            if (matchesBond && matchesDate) {
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
        if (kpiBuyDetails) kpiBuyDetails.textContent = `${buyCount} buys • ${buyQty.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} bonds`;

        if (kpiSellAmount) kpiSellAmount.textContent = formatINR(sellAmount);
        if (kpiSellDetails) kpiSellDetails.textContent = `${sellCount} exits • ${sellQty.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} bonds`;

        if (kpiNetOutlay) {
            kpiNetOutlay.textContent = formatINR(netOutlay);
            kpiNetOutlay.className = 'fw-bold mb-0 mt-1 ' + (netOutlay >= 0 ? 'text-primary' : 'text-success');
        }
        if (kpiNetDetails) {
            if (selectedBond === 'ALL') {
                kpiNetDetails.textContent = 'Net cash invested in period';
            } else {
                const heldDiff = buyQty - sellQty;
                kpiNetDetails.textContent = `Net held from trades: ${heldDiff.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} bonds`;
            }
        }

        if (kpiCharges) kpiCharges.textContent = formatINR(totalCharges);

        // Update Footer Totals
        if (footerSummaryTitle) {
            let label = selectedBond === 'ALL' ? 'Total' : selectedName;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerSummaryTitle.textContent = `${label} (${visibleCount} Trades)`;
        }

        if (footerActionBreakdown) {
            footerActionBreakdown.innerHTML = `<span class="text-success">${buyCount} BUY</span> &bull; <span class="text-danger">${sellCount} SELL/REDEEM</span>`;
        }

        if (footerTotalQty) footerTotalQty.textContent = (buyQty + sellQty).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (footerTotalCharges) footerTotalCharges.textContent = formatINR(totalCharges);
        if (footerTotalAmount) {
            footerTotalAmount.textContent = formatINR(netOutlay);
            footerTotalAmount.className = 'text-end fw-bold fs-6 ' + (netOutlay >= 0 ? 'text-dark' : 'text-success');
        }
        if (footerNote) footerNote.textContent = netOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized';

        // Refresh pagination slice
        if (window.bondLedgerPager) {
            window.bondLedgerPager.refresh();
        }

        // Toggle reset button & filtered badge
        const hasActiveFilter = (selectedBond !== 'ALL' || selectedFy !== 'ALL');
        if (ledgerResetBtn) ledgerResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (ledgerBadge) {
            ledgerBadge.classList.toggle('d-none', !hasActiveFilter);
            if (ledgerNameSpan) {
                let badgeText = selectedBond !== 'ALL' ? selectedName : 'All Bonds';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                ledgerNameSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedBond === 'ALL') url.searchParams.delete('bond_id');
            else url.searchParams.set('bond_id', selectedBond);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (ledgerBondFilter) ledgerBondFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerDateFilter) ledgerDateFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerResetBtn) {
        ledgerResetBtn.addEventListener('click', () => {
            if (ledgerBondFilter) ledgerBondFilter.value = 'ALL';
            if (ledgerDateFilter) ledgerDateFilter.value = 'ALL';
            applyLedgerFilters(true);
        });
    }

    // Shortcuts from Holdings dropdown
    window.showBondLedger = function(bondId) {
        const transTabBtn = document.getElementById('transactions-tab');
        if (transTabBtn) {
            bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        }
        if (ledgerBondFilter) {
            ledgerBondFilter.value = String(bondId);
            applyLedgerFilters(true);
        }
    };

    window.showBondPayouts = function(bondId) {
        const payoutsTabBtn = document.getElementById('payouts-tab');
        if (payoutsTabBtn) {
            bootstrap.Tab.getOrCreateInstance(payoutsTabBtn).show();
        }
        if (payoutBondFilter) {
            payoutBondFilter.value = String(bondId);
            applyPayoutFilters(true);
        }
    };

    window.showBondTaxLog = function(bondId) {
        const taxTabBtn = document.getElementById('capitalgains-tab');
        if (taxTabBtn) {
            bootstrap.Tab.getOrCreateInstance(taxTabBtn).show();
        }
        if (taxBondFilter) {
            taxBondFilter.value = String(bondId);
            applyBondTaxFilters(true);
        }
    };

    // ==========================================
    // BOND HOLDINGS SORTING
    // ==========================================
    const sortSelect = document.getElementById('sortBondsSelect');
    const holdingsTbody = document.getElementById('bondHoldingsTbody');
    const sortableHeaders = document.querySelectorAll('.sortable-bond-th');

    function sortBondsHoldings(sortBy) {
        if (!holdingsTbody) return;
        const rows = Array.from(holdingsTbody.querySelectorAll('tr.bond-holding-row'));
        if (rows.length === 0) return;

        const [field, dir] = sortBy.split('_');
        const isAsc = dir === 'asc';

        rows.sort((a, b) => {
            let valA, valB;
            if (field === 'name') {
                valA = (a.dataset.name || '').toLowerCase();
                valB = (b.dataset.name || '').toLowerCase();
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (field === 'maturity') {
                valA = a.dataset.maturity || '9999-99-99';
                valB = b.dataset.maturity || '9999-99-99';
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (field === 'interest') {
                valA = a.dataset.interest || '';
                valB = b.dataset.interest || '';
                if (!valA) return 1;
                if (!valB) return -1;
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
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
            sortBondsHoldings(this.value);
        });
    }

    sortableHeaders.forEach(th => {
        th.addEventListener('click', function() {
            const col = this.dataset.col;
            const currentVal = sortSelect ? sortSelect.value : '';
            let newDir = 'asc';
            if (currentVal.startsWith(col)) {
                newDir = currentVal.endsWith('asc') ? 'desc' : 'asc';
            } else if (col === 'current' || col === 'interest') {
                newDir = 'desc';
            }
            const newVal = `${col}_${newDir}`;
            if (sortSelect) sortSelect.value = newVal;
            sortBondsHoldings(newVal);
        });
    });

    // ==========================================
    // FIFO TAX LOG FILTERING (Bond, Gain Type, Date Range)
    // ==========================================
    const taxBondFilter  = document.getElementById('taxLogBondFilter');
    const taxTypeFilter  = document.getElementById('taxLogTypeFilter');
    const taxDateFilter  = document.getElementById('taxLogDateFilter');
    const taxResetBtn    = document.getElementById('btnResetTaxFilter');
    const taxBadge       = document.getElementById('filteredTaxBadge');
    const taxBondSpan    = document.getElementById('filteredTaxBondSpan');
    const taxTable       = document.getElementById('bondTaxLogTable');

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
    const footerTaxQty          = document.getElementById('footerTaxQty');
    const footerTaxCost         = document.getElementById('footerTaxCost');
    const footerTaxProceeds     = document.getElementById('footerTaxProceeds');
    const footerTaxGain         = document.getElementById('footerTaxGain');

    function applyBondTaxFilters(updateUrl = true) {
        if (!taxTable) return;

        const selectedBond = taxBondFilter ? taxBondFilter.value : 'ALL';
        const selectedType = taxTypeFilter ? taxTypeFilter.value : 'ALL';
        const selectedFy   = taxDateFilter ? taxDateFilter.value : 'ALL';

        const rows = taxTable.querySelectorAll('tbody tr.bond-taxlog-row');
        let visibleCount = 0;
        let totalMatchedUnits = 0;
        let totalBuyCost = 0;
        let totalExitProceeds = 0;
        let totalRealizedGain = 0;
        let totalStcg = 0;
        let totalLtcg = 0;
        let totalExempt = 0;
        let selectedBondName = '';

        if (selectedBond !== 'ALL' && taxBondFilter) {
            const opt = taxBondFilter.querySelector(`option[value="${selectedBond}"]`);
            if (opt) selectedBondName = opt.dataset.name || opt.textContent.split('(')[0].trim();
        }

        rows.forEach(row => {
            const rowBondId   = row.dataset.bondId;
            const rowGainType = row.dataset.gainType;
            const rowDate     = row.dataset.date;

            const matchesBond = (selectedBond === 'ALL' || rowBondId === selectedBond);
            const matchesType = (selectedType === 'ALL' || rowGainType === selectedType);
            const matchesDate = matchFy(rowDate, selectedFy);

            if (matchesBond && matchesType && matchesDate) {
                row.classList.remove('d-none');
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;

                const qty      = parseFloat(row.dataset.qty) || 0;
                const cost     = parseFloat(row.dataset.cost) || 0;
                const proceeds = parseFloat(row.dataset.proceeds) || 0;
                const gain     = parseFloat(row.dataset.gain) || 0;

                totalMatchedUnits += qty;
                totalBuyCost      += cost;
                totalExitProceeds += proceeds;
                totalRealizedGain += gain;

                if (rowGainType === 'EXEMPT_SGB_MATURITY') {
                    totalExempt += gain;
                } else if (rowGainType === 'LTCG') {
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
        if (kpiTaxCost) kpiTaxCost.textContent = formatINR(totalBuyCost);
        if (kpiTaxProceeds) kpiTaxProceeds.textContent = formatINR(totalExitProceeds);
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
            kpiTaxBreakdown.textContent = `Exempt: ${formatINR(totalExempt)} • LTCG: ${formatINR(totalLtcg)} • STCG: ${formatINR(totalStcg)}`;
        }

        // Update Dynamic Footer
        if (footerTaxSummaryTitle) {
            let label = selectedBond === 'ALL' ? 'Total' : selectedBondName;
            if (selectedType !== 'ALL') {
                label += ` [${selectedType === 'EXEMPT_SGB_MATURITY' ? 'Exempt SGB' : selectedType}]`;
            }
            if (selectedFy !== 'ALL') label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            footerTaxSummaryTitle.textContent = `${label} (${visibleCount} Lots)`;
        }
        if (footerTaxBreakdown) {
            footerTaxBreakdown.textContent = `Exempt: ${formatINR(totalExempt)} • LTCG: ${formatINR(totalLtcg)} • STCG: ${formatINR(totalStcg)}`;
        }
        if (footerTaxQty) footerTaxQty.textContent = totalMatchedUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
        if (footerTaxCost) footerTaxCost.textContent = formatINR(totalBuyCost);
        if (footerTaxProceeds) footerTaxProceeds.textContent = formatINR(totalExitProceeds);
        if (footerTaxGain) {
            footerTaxGain.textContent = (totalRealizedGain >= 0 ? '+' : '') + formatINR(totalRealizedGain);
            footerTaxGain.className = 'text-end py-3 fs-6 fw-bold ' + (totalRealizedGain >= 0 ? 'text-success' : 'text-danger');
        }

        // Refresh pagination slice
        if (window.bondTaxPager) {
            window.bondTaxPager.refresh();
        }

        // Toggle reset button & active filter badge
        const hasActiveFilter = (selectedBond !== 'ALL' || selectedType !== 'ALL' || selectedFy !== 'ALL');
        if (taxResetBtn) taxResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (taxBadge) {
            taxBadge.classList.toggle('d-none', !hasActiveFilter);
            if (taxBondSpan) {
                let badgeParts = [];
                if (selectedBond !== 'ALL') badgeParts.push(selectedBondName);
                if (selectedType !== 'ALL') badgeParts.push(selectedType === 'EXEMPT_SGB_MATURITY' ? 'Tax-Exempt' : selectedType);
                if (selectedFy !== 'ALL') badgeParts.push(selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                taxBondSpan.textContent = badgeParts.join(' • ');
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedBond === 'ALL') url.searchParams.delete('tax_bond_id');
            else url.searchParams.set('tax_bond_id', selectedBond);

            if (selectedType === 'ALL') url.searchParams.delete('tax_type');
            else url.searchParams.set('tax_type', selectedType);

            if (selectedFy === 'ALL') url.searchParams.delete('tax_fy');
            else url.searchParams.set('tax_fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (taxBondFilter) taxBondFilter.addEventListener('change', () => applyBondTaxFilters(true));
    if (taxTypeFilter) taxTypeFilter.addEventListener('change', () => applyBondTaxFilters(true));
    if (taxDateFilter) taxDateFilter.addEventListener('change', () => applyBondTaxFilters(true));
    if (taxResetBtn) {
        taxResetBtn.addEventListener('click', () => {
            if (taxBondFilter) taxBondFilter.value = 'ALL';
            if (taxTypeFilter) taxTypeFilter.value = 'ALL';
            if (taxDateFilter) taxDateFilter.value = 'ALL';
            applyBondTaxFilters(true);
        });
    }

    // ==========================================
    // TABLE PAGINATION INITIALIZATION
    // ==========================================
    if (typeof window.initTablePagination === 'function') {
        window.bondLedgerPager = window.initTablePagination({
            tableId: 'bondLedgerTable',
            rowSelector: '.bond-ledger-row',
            infoId: 'bondLedgerPageInfo',
            sizeSelectId: 'bondLedgerPageSize',
            controlsId: 'bondLedgerPageControls'
        });

        window.bondTaxPager = window.initTablePagination({
            tableId: 'bondTaxLogTable',
            rowSelector: '.bond-taxlog-row',
            infoId: 'bondTaxPageInfo',
            sizeSelectId: 'bondTaxPageSize',
            controlsId: 'bondTaxPageControls'
        });

        window.bondPayoutPager = window.initTablePagination({
            tableId: 'bondPayoutsTable',
            rowSelector: '.bond-payout-row',
            infoId: 'bondPayoutPageInfo',
            sizeSelectId: 'bondPayoutPageSize',
            controlsId: 'bondPayoutPageControls'
        });
    }

    // Deep link handling on page load
    const urlParams = new URLSearchParams(window.location.search);
    const initialBondId = urlParams.get('bond_id');
    const initialFy     = urlParams.get('fy');
    const initialTab    = urlParams.get('tab');
    const hash          = window.location.hash;

    if (initialBondId) {
        if (ledgerBondFilter) ledgerBondFilter.value = initialBondId;
        if (payoutBondFilter) payoutBondFilter.value = initialBondId;
    }
    if (initialFy) {
        if (ledgerDateFilter) ledgerDateFilter.value = initialFy;
        if (payoutDateFilter) payoutDateFilter.value = initialFy;
    }

    if ((initialBondId || initialFy || initialTab || hash) && (ledgerTable || payoutTable)) {
        if (ledgerTable) applyLedgerFilters(false);
        if (payoutTable) applyPayoutFilters(false);

        if (initialTab === 'transactions' || (initialBondId && !initialTab) || hash === '#transactions') {
            const transTabBtn = document.getElementById('transactions-tab');
            if (transTabBtn) bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        } else if (initialTab === 'payouts' || hash === '#payouts') {
            const payoutsTabBtn = document.getElementById('payouts-tab');
            if (payoutsTabBtn) bootstrap.Tab.getOrCreateInstance(payoutsTabBtn).show();
        }
    }

    if (initialTab === 'past-holdings' || hash === '#past-holdings') {
        const pastTabBtn = document.getElementById('past-holdings-tab');
        if (pastTabBtn) bootstrap.Tab.getOrCreateInstance(pastTabBtn).show();
    }

    const taxBondParam = urlParams.get('tax_bond_id');
    const taxTypeParam = urlParams.get('tax_type');
    const taxFyParam   = urlParams.get('tax_fy');
    if (taxBondParam || taxTypeParam || taxFyParam || initialTab === 'capitalgains' || hash === '#capitalgains') {
        const taxTabBtn = document.getElementById('capitalgains-tab');
        if (taxTabBtn && (initialTab === 'capitalgains' || hash === '#capitalgains')) {
            bootstrap.Tab.getOrCreateInstance(taxTabBtn).show();
        }
        if (taxBondParam && taxBondFilter) taxBondFilter.value = taxBondParam;
        if (taxTypeParam && taxTypeFilter) taxTypeFilter.value = taxTypeParam;
        if (taxFyParam && taxDateFilter) taxDateFilter.value = taxFyParam;
        applyBondTaxFilters(false);
    }
});

