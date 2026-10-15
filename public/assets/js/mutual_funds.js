/**
 * MyFolioVault - Mutual Funds Module Scripts
 * 100% Offline Compatible
 */

document.addEventListener('DOMContentLoaded', function() {
    let currentMf = {};

    const cfg = window.MUTUAL_FUNDS_CONFIG || {};
    const FY_RANGES = cfg.fyRanges || (typeof window.FY_RANGES !== 'undefined' ? window.FY_RANGES : {});

    // Hook Edit Scheme buttons
    document.querySelectorAll('.open-edit-mf-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editMfId');
            const elName = document.getElementById('editMfSchemeName');
            const elCat = document.getElementById('editMfCategory');
            const elFolio = document.getElementById('editMfFolio');
            const elAmc = document.getElementById('editMfFundHouse');
            const elAmfi = document.getElementById('editMfAmfiCode');
            const elIsin = document.getElementById('editMfIsin');

            if (elId) elId.value = this.dataset.id;
            if (elName) elName.value = this.dataset.name;
            if (elCat) elCat.value = this.dataset.category;
            if (elFolio) elFolio.value = this.dataset.folio || '';
            if (elAmc) elAmc.value = this.dataset.amc || '';
            if (elAmfi) elAmfi.value = this.dataset.amfi || '';
            if (elIsin) elIsin.value = this.dataset.isin || '';
        });
    });

    // Hook Edit Transaction buttons
    document.querySelectorAll('.open-edit-mf-trans').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editMfTransId');
            const elTitle = document.getElementById('editMfTransTitle');
            const elDate = document.getElementById('editMfTransDate');
            const elUnits = document.getElementById('editMfTransUnits');
            const elNav = document.getElementById('editMfTransNav');
            const elCharges = document.getElementById('editMfTransCharges');
            const elNotes = document.getElementById('editMfTransNotes');

            if (elId) elId.value = this.dataset.id;
            if (elTitle) elTitle.textContent = 'Edit ' + this.dataset.type + ': ' + this.dataset.name;
            if (elDate) elDate.value = this.dataset.date;
            if (elUnits) elUnits.value = parseFloat(this.dataset.units || 0).toFixed(4);
            if (elNav) elNav.value = parseFloat(this.dataset.nav || 0).toFixed(4);
            if (elCharges) elCharges.value = parseFloat(this.dataset.charges || 0).toFixed(2);
            if (elNotes) elNotes.value = this.dataset.notes || '';
        });
    });

    // 1. Hook "Add Trans" buttons to populate modal
    document.querySelectorAll('.open-mf-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentMf = {
                id: this.dataset.id,
                code: this.dataset.code,
                name: this.dataset.name,
                folio: this.dataset.folio,
                units: parseFloat(this.dataset.units) || 0,
                nav: parseFloat(this.dataset.nav) || 0,
                avgNav: parseFloat(this.dataset.avg) || 0
            };

            const elId = document.getElementById('modalMfId');
            const elCode = document.getElementById('modalMfCode');
            const elSub = document.getElementById('modalMfSubtitle');
            if (elId) elId.value = currentMf.id;
            if (elCode) elCode.textContent = currentMf.code;
            if (elSub) elSub.textContent = currentMf.name + ' (Folio: ' + currentMf.folio + ')';
            
            // Set defaults in inputs
            const buyTypeEl = document.getElementById('buyMfType');
            if (buyTypeEl) buyTypeEl.value = 'BUY_LUMPSUM';

            const buyNavEl = document.getElementById('buyMfNav');
            const buyAmtEl = document.getElementById('buyMfAmount');
            const buyUnitsEl = document.getElementById('buyMfUnits');
            const buyChgEl = document.getElementById('buyMfCharges');
            const buyTotalEl = document.getElementById('buyMfTotalPreview');

            if (buyNavEl) buyNavEl.value = currentMf.nav.toFixed(4);
            if (buyAmtEl) buyAmtEl.value = '';
            if (buyUnitsEl) buyUnitsEl.value = '';
            if (buyChgEl) buyChgEl.value = '0.00';
            if (buyTotalEl) buyTotalEl.textContent = '₹ 0.00';

            const redNavEl = document.getElementById('redeemMfNav');
            const redBadge = document.getElementById('redeemMfAvailableUnitsBadge');
            const redUnitsEl = document.getElementById('redeemMfUnits');
            const redChgEl = document.getElementById('redeemMfCharges');
            const redTotalEl = document.getElementById('redeemMfTotalPreview');
            const redPnlEl = document.getElementById('redeemMfPnlPreview');

            if (redNavEl) redNavEl.value = currentMf.nav.toFixed(4);
            if (redBadge) redBadge.textContent = currentMf.units.toFixed(4) + ' units';
            if (redUnitsEl) {
                redUnitsEl.max = currentMf.units;
                redUnitsEl.value = '';
            }
            if (redChgEl) redChgEl.value = '0.00';
            if (redTotalEl) redTotalEl.textContent = '₹ 0.00';
            if (redPnlEl) redPnlEl.textContent = '₹ 0.00';

            // Reset to Buy panel
            const actBuyRadio = document.getElementById('actMfBuy');
            if (actBuyRadio) actBuyRadio.checked = true;
            switchMfAction('BUY');
        });
    });

    // 2. Action Tab Switching (Buy More / Redeem)
    const actRadios = document.querySelectorAll('input[name="action_type"]');
    actRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            switchMfAction(this.value);
        });
    });

    function switchMfAction(type) {
        const pBuy = document.getElementById('panelMfBuy');
        const pRedeem = document.getElementById('panelMfRedeem');
        const submitBtn = document.getElementById('btnSubmitMfTrans');

        if (type === 'BUY') {
            if (pBuy) pBuy.classList.remove('d-none');
            if (pRedeem) pRedeem.classList.add('d-none');
            if (pBuy) setMfInputsDisabled(pBuy, false);
            if (pRedeem) setMfInputsDisabled(pRedeem, true);
            if (submitBtn) {
                submitBtn.className = 'btn btn-success btn-sm px-4 fw-semibold';
                submitBtn.textContent = 'Confirm Purchase Lot';
            }
        } else if (type === 'REDEEM') {
            if (pBuy) pBuy.classList.add('d-none');
            if (pRedeem) pRedeem.classList.remove('d-none');
            if (pBuy) setMfInputsDisabled(pBuy, true);
            if (pRedeem) setMfInputsDisabled(pRedeem, false);
            if (submitBtn) {
                submitBtn.className = 'btn btn-danger btn-sm px-4 fw-semibold';
                submitBtn.textContent = 'Execute FIFO Redeem';
            }
        }
    }

    function setMfInputsDisabled(container, disabled) {
        if (!container) return;
        container.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    // 3. Live calculations in BUY modal
    const buyAmount = document.getElementById('buyMfAmount');
    const buyUnits = document.getElementById('buyMfUnits');
    const buyNav = document.getElementById('buyMfNav');
    const buyCharges = document.getElementById('buyMfCharges');
    const buyTotalPreview = document.getElementById('buyMfTotalPreview');

    let activeInputSource = 'units';

    function calcBuyTotal() {
        const amt = parseFloat(buyAmount ? buyAmount.value : 0) || 0;
        const chg = parseFloat(buyCharges ? buyCharges.value : 0) || 0;
        const total = amt + chg;
        if (buyTotalPreview) {
            buyTotalPreview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }

    function recalcFromUnits() {
        const u = parseFloat(buyUnits ? buyUnits.value : 0) || 0;
        const nav = parseFloat(buyNav ? buyNav.value : 0) || 0;
        if (u > 0 && nav > 0) {
            const grossAmt = +(u * nav).toFixed(2);
            if (buyAmount) buyAmount.value = grossAmt.toFixed(2);
            const stampDuty = +(grossAmt * 0.00005).toFixed(2);
            if (buyCharges) buyCharges.value = stampDuty.toFixed(2);
        } else if (u === 0) {
            if (buyAmount) buyAmount.value = '';
            if (buyCharges) buyCharges.value = '0.00';
        }
        calcBuyTotal();
    }

    function recalcFromAmount() {
        const amt = parseFloat(buyAmount ? buyAmount.value : 0) || 0;
        const nav = parseFloat(buyNav ? buyNav.value : 0) || 0;
        const stampDuty = +(amt * 0.00005).toFixed(2);
        if (buyCharges) buyCharges.value = stampDuty.toFixed(2);

        if (amt > 0 && nav > 0) {
            const units = amt / nav;
            if (buyUnits) buyUnits.value = units.toFixed(4);
        } else if (amt === 0) {
            if (buyUnits) buyUnits.value = '';
        }
        calcBuyTotal();
    }

    if (buyUnits) {
        buyUnits.addEventListener('input', function() {
            activeInputSource = 'units';
            recalcFromUnits();
        });
    }

    if (buyAmount) {
        buyAmount.addEventListener('input', function() {
            activeInputSource = 'amount';
            recalcFromAmount();
        });
    }

    if (buyNav) {
        buyNav.addEventListener('input', function() {
            if (activeInputSource === 'units' && buyUnits && parseFloat(buyUnits.value) > 0) {
                recalcFromUnits();
            } else if (buyAmount && parseFloat(buyAmount.value) > 0) {
                recalcFromAmount();
            } else if (buyUnits && parseFloat(buyUnits.value) > 0) {
                recalcFromUnits();
            }
        });
    }

    if (buyCharges) {
        buyCharges.addEventListener('input', calcBuyTotal);
    }

    // 4. Live calculations in REDEEM modal
    const redeemUnits = document.getElementById('redeemMfUnits');
    const redeemNav = document.getElementById('redeemMfNav');
    const redeemCharges = document.getElementById('redeemMfCharges');
    const redeemPreview = document.getElementById('redeemMfTotalPreview');
    const redeemPnlPreview = document.getElementById('redeemMfPnlPreview');

    function calcRedeem() {
        if (!redeemUnits || !redeemNav || !redeemCharges) return;
        const u = parseFloat(redeemUnits.value) || 0;
        const nav = parseFloat(redeemNav.value) || 0;
        const chg = parseFloat(redeemCharges.value) || 0;

        const grossProceeds = u * nav;
        const netProceeds = Math.max(0, grossProceeds - chg);
        const costBasis = u * (currentMf.avgNav || 0);
        const estGain = netProceeds - costBasis;

        if (redeemPreview) {
            redeemPreview.textContent = '₹ ' + netProceeds.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        if (redeemPnlPreview) {
            const sign = estGain >= 0 ? '+' : '';
            redeemPnlPreview.textContent = sign + '₹ ' + estGain.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            redeemPnlPreview.className = 'small fw-semibold ' + (estGain >= 0 ? 'text-success' : 'text-danger');
        }
    }

    [redeemUnits, redeemNav, redeemCharges].forEach(el => {
        if (el) el.addEventListener('input', calcRedeem);
    });

    // =========================================================================
    // 5. TRADE LEDGER: DUAL FILTER (SCHEME + FY) & LIVE TOTAL AMOUNTS
    // =========================================================================
    const ledgerFundFilter = document.getElementById('ledgerFundFilter');
    const ledgerDateFilter = document.getElementById('ledgerDateFilter');
    const ledgerTable      = document.getElementById('mfLedgerTable');
    const ledgerResetBtn   = document.getElementById('btnResetFundFilter');
    const ledgerBadge      = document.getElementById('filteredFundBadge');
    const ledgerNameSpan   = document.getElementById('filteredFundName');
    const ledgerNoRowsEl   = document.getElementById('ledgerNoRows');

    // KPI Elements
    const kpiBuyAmount      = document.getElementById('kpiBuyAmount');
    const kpiBuyDetails     = document.getElementById('kpiBuyDetails');
    const kpiRedeemAmount   = document.getElementById('kpiRedeemAmount');
    const kpiRedeemDetails  = document.getElementById('kpiRedeemDetails');
    const kpiNetOutlay      = document.getElementById('kpiNetOutlay');
    const kpiNetDetails     = document.getElementById('kpiNetDetails');
    const kpiCharges        = document.getElementById('kpiCharges');

    // Footer Elements
    const footerSummaryTitle     = document.getElementById('footerSummaryTitle');
    const footerActionBreakdown  = document.getElementById('footerActionBreakdown');
    const footerTotalUnits       = document.getElementById('footerTotalUnits');
    const footerTotalCharges     = document.getElementById('footerTotalCharges');
    const footerTotalAmount      = document.getElementById('footerTotalAmount');
    const footerNote             = document.getElementById('footerNote');

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

    function applyLedgerFilters(updateUrl = true) {
        if (!ledgerTable) return;

        const selectedFund   = ledgerFundFilter ? ledgerFundFilter.value : 'ALL';
        const selectedFy     = ledgerDateFilter ? ledgerDateFilter.value : 'ALL';
        const selectedOption = ledgerFundFilter ? ledgerFundFilter.options[ledgerFundFilter.selectedIndex] : null;
        const selectedName   = selectedOption ? (selectedOption.dataset.name || '') : '';

        const rows = ledgerTable.querySelectorAll('tbody tr.mf-ledger-row');
        let visibleCount = 0;
        let buyAmount    = 0.0;
        let redeemAmount = 0.0;
        let buyUnits     = 0.0;
        let redeemUnits  = 0.0;
        let buyCount     = 0;
        let redeemCount  = 0;
        let totalCharges = 0.0;

        rows.forEach(row => {
            const rowFundId = row.dataset.fundId;
            const rowDate   = row.dataset.date;
            const rowType   = row.dataset.type;
            const rowUnits  = parseFloat(row.dataset.units) || 0.0;
            const rowAmt    = parseFloat(row.dataset.amount) || 0.0;
            const rowChg    = parseFloat(row.dataset.charges) || 0.0;

            const matchesFund = (selectedFund === 'ALL' || rowFundId === selectedFund);
            const matchesDate = matchFy(rowDate, selectedFy);

            if (matchesFund && matchesDate) {
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;
                totalCharges += rowChg;

                if (rowType === 'BUY_SIP' || rowType === 'BUY_LUMPSUM') {
                    buyAmount += rowAmt;
                    buyUnits  += rowUnits;
                    buyCount++;
                } else {
                    redeemAmount += rowAmt;
                    redeemUnits  += rowUnits;
                    redeemCount++;
                }
            } else {
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        if (ledgerNoRowsEl) {
            ledgerNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        const netOutlay = buyAmount - redeemAmount;

        // Update KPI Cards
        if (kpiBuyAmount) kpiBuyAmount.textContent = formatINR(buyAmount);
        if (kpiBuyDetails) kpiBuyDetails.textContent = `${buyCount} orders • ${buyUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 })} units`;

        if (kpiRedeemAmount) kpiRedeemAmount.textContent = formatINR(redeemAmount);
        if (kpiRedeemDetails) kpiRedeemDetails.textContent = `${redeemCount} redemptions • ${redeemUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 })} units`;

        if (kpiNetOutlay) {
            kpiNetOutlay.textContent = formatINR(netOutlay);
            kpiNetOutlay.className = 'fw-bold mb-0 mt-1 ' + (netOutlay >= 0 ? 'text-primary' : 'text-success');
        }
        if (kpiNetDetails) {
            if (selectedFund === 'ALL') {
                kpiNetDetails.textContent = 'Net capital outlay in period';
            } else {
                const heldDiff = buyUnits - redeemUnits;
                kpiNetDetails.textContent = `Net units from trades: ${heldDiff.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 })}`;
            }
        }

        if (kpiCharges) kpiCharges.textContent = formatINR(totalCharges);

        // Update Footer Totals
        if (footerSummaryTitle) {
            let label = selectedFund === 'ALL' ? 'Total' : selectedName;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerSummaryTitle.textContent = `${label} (${visibleCount} Trades)`;
        }

        if (footerActionBreakdown) {
            footerActionBreakdown.innerHTML = `<span class="text-success">${buyCount} BUY</span> &bull; <span class="text-danger">${redeemCount} REDEEM</span>`;
        }

        if (footerTotalUnits) footerTotalUnits.textContent = (buyUnits + redeemUnits).toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
        if (footerTotalCharges) footerTotalCharges.textContent = formatINR(totalCharges);
        if (footerTotalAmount) {
            footerTotalAmount.textContent = formatINR(netOutlay);
            footerTotalAmount.className = 'text-end fw-bold fs-6 ' + (netOutlay >= 0 ? 'text-dark' : 'text-success');
        }
        if (footerNote) footerNote.textContent = netOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized';

        // Refresh pagination slice
        if (window.mfLedgerPager) {
            window.mfLedgerPager.refresh();
        }

        // Toggle reset button & filtered badge
        const hasActiveFilter = (selectedFund !== 'ALL' || selectedFy !== 'ALL');
        if (ledgerResetBtn) ledgerResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (ledgerBadge) {
            ledgerBadge.classList.toggle('d-none', !hasActiveFilter);
            if (ledgerNameSpan) {
                let badgeText = selectedFund !== 'ALL' ? selectedName : 'All Schemes';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                ledgerNameSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedFund === 'ALL') url.searchParams.delete('fund_id');
            else url.searchParams.set('fund_id', selectedFund);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (ledgerFundFilter) ledgerFundFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerDateFilter) ledgerDateFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerResetBtn) {
        ledgerResetBtn.addEventListener('click', () => {
            if (ledgerFundFilter) ledgerFundFilter.value = 'ALL';
            if (ledgerDateFilter) ledgerDateFilter.value = 'ALL';
            applyLedgerFilters(true);
        });
    }

    // Shortcut from Holdings dropdown
    window.showMfLedger = function(fundId) {
        const transTabBtn = document.getElementById('transactions-tab');
        if (transTabBtn) {
            bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        }
        if (ledgerFundFilter) {
            ledgerFundFilter.value = String(fundId);
            applyLedgerFilters(true);
        }
    };

    // ==========================================
    // FIFO TAX LOG FILTERING (Scheme, Gain Type, Date Range)
    // ==========================================
    const taxMfFilter    = document.getElementById('taxLogMfFilter');
    const taxTypeFilter  = document.getElementById('taxLogTypeFilter');
    const taxDateFilter  = document.getElementById('taxLogDateFilter');
    const taxResetBtn    = document.getElementById('btnResetTaxFilter');
    const taxBadge       = document.getElementById('filteredTaxBadge');
    const taxSchemeSpan  = document.getElementById('filteredTaxSchemeSpan');
    const taxTable       = document.getElementById('mfTaxLogTable');

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
    const footerTaxUnits        = document.getElementById('footerTaxUnits');
    const footerTaxCost         = document.getElementById('footerTaxCost');
    const footerTaxProceeds     = document.getElementById('footerTaxProceeds');
    const footerTaxGain         = document.getElementById('footerTaxGain');

    function applyMfTaxFilters(updateUrl = true) {
        if (!taxTable) return;

        const selectedMf   = taxMfFilter ? taxMfFilter.value : 'ALL';
        const selectedType = taxTypeFilter ? taxTypeFilter.value : 'ALL';
        const selectedFy   = taxDateFilter ? taxDateFilter.value : 'ALL';

        const rows = taxTable.querySelectorAll('tbody tr.mf-taxlog-row');
        let visibleCount = 0;
        let totalMatchedUnits = 0;
        let totalBuyCost = 0;
        let totalSellProceeds = 0;
        let totalRealizedGain = 0;
        let totalStcg = 0;
        let totalLtcg = 0;
        let selectedSchemeName = '';

        if (selectedMf !== 'ALL' && taxMfFilter) {
            const opt = taxMfFilter.querySelector(`option[value="${selectedMf}"]`);
            if (opt) selectedSchemeName = opt.dataset.name || opt.textContent.split('(')[0].trim();
        }

        rows.forEach(row => {
            const rowMfId     = row.dataset.mfId;
            const rowGainType = row.dataset.gainType;
            const rowDate     = row.dataset.date;

            const matchesMf   = (selectedMf === 'ALL' || rowMfId === selectedMf);
            const matchesType = (selectedType === 'ALL' || rowGainType === selectedType);
            const matchesDate = matchFy(rowDate, selectedFy);

            if (matchesMf && matchesType && matchesDate) {
                row.classList.remove('d-none');
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;

                const units    = parseFloat(row.dataset.units) || 0;
                const cost     = parseFloat(row.dataset.cost) || 0;
                const proceeds = parseFloat(row.dataset.proceeds) || 0;
                const gain     = parseFloat(row.dataset.gain) || 0;

                totalMatchedUnits += units;
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
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        // Update 4 KPI Cards
        if (kpiTaxUnits) kpiTaxUnits.textContent = totalMatchedUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
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

        // Update Dynamic Footer
        if (footerTaxSummaryTitle) {
            let label = selectedMf === 'ALL' ? 'Total' : selectedSchemeName;
            if (selectedType !== 'ALL') label += ` [${selectedType}]`;
            if (selectedFy !== 'ALL') label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            footerTaxSummaryTitle.textContent = `${label} (${visibleCount} Lots)`;
        }
        if (footerTaxBreakdown) {
            footerTaxBreakdown.textContent = `STCG: ${formatINR(totalStcg)} • LTCG: ${formatINR(totalLtcg)}`;
        }
        if (footerTaxUnits) footerTaxUnits.textContent = totalMatchedUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
        if (footerTaxCost) footerTaxCost.textContent = formatINR(totalBuyCost);
        if (footerTaxProceeds) footerTaxProceeds.textContent = formatINR(totalSellProceeds);
        if (footerTaxGain) {
            footerTaxGain.textContent = (totalRealizedGain >= 0 ? '+' : '') + formatINR(totalRealizedGain);
            footerTaxGain.className = 'text-end py-3 fs-6 fw-bold ' + (totalRealizedGain >= 0 ? 'text-success' : 'text-danger');
        }

        // Refresh pagination slice
        if (window.mfTaxPager) {
            window.mfTaxPager.refresh();
        }

        // Toggle reset button & active filter badge
        const hasActiveFilter = (selectedMf !== 'ALL' || selectedType !== 'ALL' || selectedFy !== 'ALL');
        if (taxResetBtn) taxResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (taxBadge) {
            taxBadge.classList.toggle('d-none', !hasActiveFilter);
            if (taxSchemeSpan) {
                let badgeParts = [];
                if (selectedMf !== 'ALL') badgeParts.push(selectedSchemeName);
                if (selectedType !== 'ALL') badgeParts.push(selectedType);
                if (selectedFy !== 'ALL') badgeParts.push(selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                taxSchemeSpan.textContent = badgeParts.join(' • ');
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedMf === 'ALL') url.searchParams.delete('tax_mf_id');
            else url.searchParams.set('tax_mf_id', selectedMf);

            if (selectedType === 'ALL') url.searchParams.delete('tax_type');
            else url.searchParams.set('tax_type', selectedType);

            if (selectedFy === 'ALL') url.searchParams.delete('tax_fy');
            else url.searchParams.set('tax_fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (taxMfFilter)   taxMfFilter.addEventListener('change', () => applyMfTaxFilters(true));
    if (taxTypeFilter) taxTypeFilter.addEventListener('change', () => applyMfTaxFilters(true));
    if (taxDateFilter) taxDateFilter.addEventListener('change', () => applyMfTaxFilters(true));
    if (taxResetBtn) {
        taxResetBtn.addEventListener('click', () => {
            if (taxMfFilter)   taxMfFilter.value = 'ALL';
            if (taxTypeFilter) taxTypeFilter.value = 'ALL';
            if (taxDateFilter) taxDateFilter.value = 'ALL';
            applyMfTaxFilters(true);
        });
    }

    // ==========================================
    // TABLE PAGINATION INITIALIZATION
    // ==========================================
    if (typeof window.initTablePagination === 'function') {
        window.mfLedgerPager = window.initTablePagination({
            tableId: 'mfLedgerTable',
            rowSelector: '.mf-ledger-row',
            infoId: 'mfLedgerPageInfo',
            sizeSelectId: 'mfLedgerPageSize',
            controlsId: 'mfLedgerPageControls'
        });

        window.mfTaxPager = window.initTablePagination({
            tableId: 'mfTaxLogTable',
            rowSelector: '.mf-taxlog-row',
            infoId: 'mfTaxPageInfo',
            sizeSelectId: 'mfTaxPageSize',
            controlsId: 'mfTaxPageControls'
        });
    }

    // Deep link handling on page load
    const urlParams = new URLSearchParams(window.location.search);
    const initialFundId = urlParams.get('fund_id');
    const initialFy     = urlParams.get('fy');
    const initialTab    = urlParams.get('tab');
    const hash          = window.location.hash;

    if (initialTab === 'past-holdings' || hash === '#past-holdings') {
        const pastTabBtn = document.getElementById('past-holdings-tab');
        if (pastTabBtn) {
            bootstrap.Tab.getOrCreateInstance(pastTabBtn).show();
        }
    }

    if (initialFundId && ledgerFundFilter) {
        ledgerFundFilter.value = initialFundId;
    }
    if (initialFy && ledgerDateFilter) {
        ledgerDateFilter.value = initialFy;
    }
    if ((initialFundId || initialFy || initialTab === 'transactions' || hash === '#transactions') && ledgerTable) {
        applyLedgerFilters(false);
        if (initialTab === 'transactions' || initialFundId || hash === '#transactions') {
            const transTabBtn = document.getElementById('transactions-tab');
            if (transTabBtn) {
                bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
            }
        }
    }

    const taxMfParam   = urlParams.get('tax_mf_id');
    const taxTypeParam = urlParams.get('tax_type');
    const taxFyParam   = urlParams.get('tax_fy');
    if (taxMfParam || taxTypeParam || taxFyParam || initialTab === 'capitalgains' || hash === '#capitalgains') {
        const taxTabBtn = document.getElementById('capitalgains-tab');
        if (taxTabBtn && (initialTab === 'capitalgains' || hash === '#capitalgains')) {
            bootstrap.Tab.getOrCreateInstance(taxTabBtn).show();
        }
        if (taxMfParam && taxMfFilter) taxMfFilter.value = taxMfParam;
        if (taxTypeParam && taxTypeFilter) taxTypeFilter.value = taxTypeParam;
        if (taxFyParam && taxDateFilter) taxDateFilter.value = taxFyParam;
        applyMfTaxFilters(false);
    }
});

