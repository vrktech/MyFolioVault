/**
 * MyFolioVault - NPS Module Scripts
 * 100% Offline Compatible
 */

document.addEventListener('DOMContentLoaded', function() {
    const cfg = window.NPS_CONFIG || {};
    const alloc = cfg.allocations || {};
    const allocE = (parseFloat(alloc.equity) || 0) / 100;
    const allocC = (parseFloat(alloc.corporateDebt) || 0) / 100;
    const allocG = (parseFloat(alloc.govtBonds) || 0) / 100;
    const allocA = (parseFloat(alloc.alternative) || 0) / 100;

    // Hook Edit Account button
    document.querySelectorAll('.open-edit-nps-account').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editNpsAccountId');
            const elSub = document.getElementById('editNpsSubName');
            const elPfm = document.getElementById('editNpsPfmName');
            const elChoice = document.getElementById('editNpsChoice');
            const elAllocE = document.getElementById('editNpsAllocE');
            const elAllocC = document.getElementById('editNpsAllocC');
            const elAllocG = document.getElementById('editNpsAllocG');
            const elAllocA = document.getElementById('editNpsAllocA');
            const elCodeE = document.getElementById('editNpsCodeE');
            const elCodeC = document.getElementById('editNpsCodeC');
            const elCodeG = document.getElementById('editNpsCodeG');
            const elCodeA = document.getElementById('editNpsCodeA');

            if (elId) elId.value = this.dataset.id;
            if (elSub) elSub.value = this.dataset.name;
            if (elPfm) elPfm.value = this.dataset.pfm;
            if (elChoice) elChoice.value = this.dataset.choice;
            if (elAllocE) elAllocE.value = this.dataset.e;
            if (elAllocC) elAllocC.value = this.dataset.c;
            if (elAllocG) elAllocG.value = this.dataset.g;
            if (elAllocA) elAllocA.value = this.dataset.a;
            if (elCodeE) elCodeE.value = this.dataset.codeE || '';
            if (elCodeC) elCodeC.value = this.dataset.codeC || '';
            if (elCodeG) elCodeG.value = this.dataset.codeG || '';
            if (elCodeA) elCodeA.value = this.dataset.codeA || '';
        });
    });

    // Hook Edit Contribution buttons
    document.querySelectorAll('.open-edit-nps-contrib').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editNpsContribId');
            const elDate = document.getElementById('editNpsConDate');
            const elType = document.getElementById('editNpsConType');
            const elGross = document.getElementById('editNpsConGross');
            const elCharges = document.getElementById('editNpsConCharges');
            const elAck = document.getElementById('editNpsConAck');
            const elNotes = document.getElementById('editNpsConNotes');

            if (elId) elId.value = this.dataset.id;
            if (elDate) elDate.value = this.dataset.date;
            if (elType) elType.value = this.dataset.type;
            if (elGross) elGross.value = parseFloat(this.dataset.gross || 0).toFixed(2);
            if (elCharges) elCharges.value = parseFloat(this.dataset.charges || 0).toFixed(2);
            if (elAck) elAck.value = this.dataset.ack || '';
            if (elNotes) elNotes.value = this.dataset.notes || '';

            const elAmtE = document.getElementById('editNpsAmtE');
            const elNavE = document.getElementById('editNpsNavE');
            const elUnitsE = document.getElementById('editNpsUnitsE');
            if (elAmtE) elAmtE.value = parseFloat(this.dataset.amtE || 0).toFixed(2);
            if (elNavE) elNavE.value = parseFloat(this.dataset.navE || 0).toFixed(4);
            if (elUnitsE) elUnitsE.value = parseFloat(this.dataset.unitsE || 0).toFixed(4);

            const elAmtC = document.getElementById('editNpsAmtC');
            const elNavC = document.getElementById('editNpsNavC');
            const elUnitsC = document.getElementById('editNpsUnitsC');
            if (elAmtC) elAmtC.value = parseFloat(this.dataset.amtC || 0).toFixed(2);
            if (elNavC) elNavC.value = parseFloat(this.dataset.navC || 0).toFixed(4);
            if (elUnitsC) elUnitsC.value = parseFloat(this.dataset.unitsC || 0).toFixed(4);

            const elAmtG = document.getElementById('editNpsAmtG');
            const elNavG = document.getElementById('editNpsNavG');
            const elUnitsG = document.getElementById('editNpsUnitsG');
            if (elAmtG) elAmtG.value = parseFloat(this.dataset.amtG || 0).toFixed(2);
            if (elNavG) elNavG.value = parseFloat(this.dataset.navG || 0).toFixed(4);
            if (elUnitsG) elUnitsG.value = parseFloat(this.dataset.unitsG || 0).toFixed(4);

            const elAmtA = document.getElementById('editNpsAmtA');
            const elNavA = document.getElementById('editNpsNavA');
            const elUnitsA = document.getElementById('editNpsUnitsA');
            if (elAmtA) elAmtA.value = parseFloat(this.dataset.amtA || 0).toFixed(2);
            if (elNavA) elNavA.value = parseFloat(this.dataset.navA || 0).toFixed(4);
            if (elUnitsA) elUnitsA.value = parseFloat(this.dataset.unitsA || 0).toFixed(4);
        });
    });

    // Hook Edit Fee Deduction buttons
    document.querySelectorAll('.open-edit-nps-deduct').forEach(btn => {
        btn.addEventListener('click', function() {
            const elId = document.getElementById('editNpsDeductId');
            const elDate = document.getElementById('editNpsDedDate');
            const elNotes = document.getElementById('editNpsDedNotes');
            const elAck = document.getElementById('editNpsDedAck');

            if (elId) elId.value = this.dataset.id;
            if (elDate) elDate.value = this.dataset.date;
            if (elNotes) elNotes.value = this.dataset.notes || '';
            if (elAck) elAck.value = this.dataset.ack || '';

            const elUnitsE = document.getElementById('editNpsDedUnitsE');
            const elNavE = document.getElementById('editNpsDedNavE');
            if (elUnitsE) elUnitsE.value = parseFloat(this.dataset.deductUnitsE || 0).toFixed(4);
            if (elNavE) elNavE.value = parseFloat(this.dataset.deductNavE || 0).toFixed(4);

            const elUnitsC = document.getElementById('editNpsDedUnitsC');
            const elNavC = document.getElementById('editNpsDedNavC');
            if (elUnitsC) elUnitsC.value = parseFloat(this.dataset.deductUnitsC || 0).toFixed(4);
            if (elNavC) elNavC.value = parseFloat(this.dataset.deductNavC || 0).toFixed(4);

            const elUnitsG = document.getElementById('editNpsDedUnitsG');
            const elNavG = document.getElementById('editNpsDedNavG');
            if (elUnitsG) elUnitsG.value = parseFloat(this.dataset.deductUnitsG || 0).toFixed(4);
            if (elNavG) elNavG.value = parseFloat(this.dataset.deductNavG || 0).toFixed(4);

            const elUnitsA = document.getElementById('editNpsDedUnitsA');
            const elNavA = document.getElementById('editNpsDedNavA');
            if (elUnitsA) elUnitsA.value = parseFloat(this.dataset.deductUnitsA || 0).toFixed(4);
            if (elNavA) elNavA.value = parseFloat(this.dataset.deductNavA || 0).toFixed(4);
        });
    });

    // 1. Transaction Action Switcher (Contribution vs Unit Deduction)
    const actRadios = document.querySelectorAll('input[name="action_type"]');
    actRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            switchNpsAction(this.value);
        });
    });

    function switchNpsAction(type) {
        const pContrib = document.getElementById('panelNpsContrib');
        const pDeduct = document.getElementById('panelNpsDeduct');
        const submitBtn = document.getElementById('btnSubmitNpsTrans');

        if (type === 'CONTRIBUTION') {
            if (pContrib) pContrib.classList.remove('d-none');
            if (pDeduct) pDeduct.classList.add('d-none');
            if (pContrib) setInputsDisabled(pContrib, false);
            if (pDeduct) setInputsDisabled(pDeduct, true);
            if (submitBtn) {
                submitBtn.className = 'btn btn-success btn-sm px-4 fw-semibold';
                submitBtn.textContent = 'Confirm Contribution';
            }
        } else if (type === 'UNIT_DEDUCTION') {
            if (pContrib) pContrib.classList.add('d-none');
            if (pDeduct) pDeduct.classList.remove('d-none');
            if (pContrib) setInputsDisabled(pContrib, true);
            if (pDeduct) setInputsDisabled(pDeduct, false);
            if (submitBtn) {
                submitBtn.className = 'btn btn-danger btn-sm px-4 fw-semibold';
                submitBtn.textContent = 'Record Unit Deduction';
            }
        }
    }

    function setInputsDisabled(container, disabled) {
        if (!container) return;
        container.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    // 2. Modal Auto-Split Button
    const btnAutoSplit = document.getElementById('btnModalAutoSplit');
    const modalGross = document.getElementById('modalGrossAmt');

    if (btnAutoSplit && modalGross) {
        btnAutoSplit.addEventListener('click', function() {
            const gross = parseFloat(modalGross.value) || 0;
            if (gross <= 0) {
                alert('Please enter Gross Amount first.');
                modalGross.focus();
                return;
            }

            const elAmtE = document.getElementById('mAmtE');
            const elAmtC = document.getElementById('mAmtC');
            const elAmtG = document.getElementById('mAmtG');
            const elAmtA = document.getElementById('mAmtA');

            if (elAmtE) elAmtE.value = (gross * allocE).toFixed(2);
            if (elAmtC) elAmtC.value = (gross * allocC).toFixed(2);
            if (elAmtG) elAmtG.value = (gross * allocG).toFixed(2);
            if (elAmtA) elAmtA.value = (gross * allocA).toFixed(2);

            ['E', 'C', 'G', 'A'].forEach(s => {
                calcModalUnits(s);
            });
        });
    }

    function calcModalUnits(scheme) {
        const amtInput = document.getElementById('mAmt' + scheme);
        const navInput = document.getElementById('mNav' + scheme);
        const unitsInput = document.getElementById('mUnits' + scheme);

        if (!amtInput || !navInput || !unitsInput) return;
        const amt = parseFloat(amtInput.value) || 0;
        const nav = parseFloat(navInput.value) || 0;

        if (amt > 0 && nav > 0) {
            unitsInput.value = (amt / nav).toFixed(4);
        }
    }

    ['E', 'C', 'G', 'A'].forEach(s => {
        const navEl = document.getElementById('mNav' + s);
        const amtEl = document.getElementById('mAmt' + s);
        const unitsEl = document.getElementById('mUnits' + s);

        if (navEl) navEl.addEventListener('input', () => calcModalUnits(s));
        if (amtEl) amtEl.addEventListener('input', () => calcModalUnits(s));

        if (unitsEl) {
            unitsEl.addEventListener('input', function() {
                const u = parseFloat(this.value) || 0;
                const nav = navEl ? (parseFloat(navEl.value) || 0) : 0;
                if (u > 0 && nav > 0 && amtEl) {
                    amtEl.value = (u * nav).toFixed(2);
                }
            });
        }
    });

    // ==========================================
    // TABLE PAGINATION INITIALIZATION
    // ==========================================
    if (typeof window.initTablePagination === 'function') {
        window.npsContribPager = window.initTablePagination({
            tableId: 'npsContributionsTable',
            rowSelector: '.nps-contrib-row',
            infoId: 'npsContribPageInfo',
            sizeSelectId: 'npsContribPageSize',
            controlsId: 'npsContribPageControls'
        });

        window.npsDeductPager = window.initTablePagination({
            tableId: 'npsDeductionsTable',
            rowSelector: '.nps-deduct-row',
            infoId: 'npsDeductPageInfo',
            sizeSelectId: 'npsDeductPageSize',
            controlsId: 'npsDeductPageControls'
        });
    }
});

