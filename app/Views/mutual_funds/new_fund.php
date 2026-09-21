<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('mutual-funds') ?>" class="text-decoration-none">Mutual Funds</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Scheme Entry</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">Add New Mutual Fund Investment</h3>
        <p class="text-secondary small mb-0">Use this form <strong>only once</strong> when adding a new fund scheme and folio to your portfolio.</p>
    </div>
    <a href="<?= base_url('mutual-funds') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Holdings
    </a>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                <div class="d-flex align-items-center">
                    <div class="p-2 bg-success bg-opacity-10 text-success rounded-3 me-3">
                        <i class="bi bi-briefcase-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Scheme & Initial Purchase Information</h5>
                        <small class="text-muted">Subsequent SIPs and redemptions will use the quick popup modal from the dashboard</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('mutual-funds/new') ?>" method="POST" id="newFundForm">
                    <?= csrf_field() ?>

                    <!-- Section 1: AMFI Code Lookup & Scheme Master -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-search me-1 text-primary"></i> 1. AMFI Code & Scheme Identity
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-5">
                            <label for="amfi_code" class="form-label small fw-semibold text-secondary">AMFI Scheme Code <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="amfi_code" 
                                       name="amfi_code" 
                                       placeholder="e.g. 122639" 
                                       value="<?= old('amfi_code') ?>" 
                                       required 
                                       autofocus>
                                <button class="btn btn-outline-primary" type="button" id="btnLookupAmfi">
                                    <i class="bi bi-arrow-repeat" id="lookupSpinner"></i> Lookup
                                </button>
                            </div>
                            <div class="form-text small" id="amfiLookupHelp">Enter 6-digit AMFI code (e.g. 122639 for Parag Parikh Flexi Cap)</div>
                        </div>

                        <div class="col-md-7">
                            <label for="folio_number" class="form-label small fw-semibold text-secondary">Folio Number <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="folio_number" 
                                   name="folio_number" 
                                   placeholder="e.g. 10294857/01" 
                                   value="<?= old('folio_number') ?>" 
                                   required>
                        </div>

                        <div class="col-12">
                            <label for="scheme_name" class="form-label small fw-semibold text-secondary">Scheme Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="scheme_name" 
                                   name="scheme_name" 
                                   placeholder="Auto-populated or enter manually" 
                                   value="<?= old('scheme_name') ?>" 
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label for="category" class="form-label small fw-semibold text-secondary">Scheme Category <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="category" 
                                   name="category" 
                                   placeholder="e.g. Flexi Cap Fund, Large Cap, ELSS" 
                                   value="<?= old('category') ?>" 
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label for="fund_house" class="form-label small fw-semibold text-secondary">Fund House / AMC</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="fund_house" 
                                   name="fund_house" 
                                   placeholder="e.g. PPFAS Mutual Fund, HDFC, SBI" 
                                   value="<?= old('fund_house') ?>">
                        </div>
                    </div>

                    <!-- Section 2: Initial Purchase Details (FIFO Lot 1) -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-cart-check me-1 text-success"></i> 2. Initial Purchase Order (First FIFO Lot)
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="transaction_type" class="form-label small fw-semibold text-secondary">Investment Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="transaction_type" name="transaction_type" required>
                                <option value="BUY_SIP" <?= old('transaction_type') === 'BUY_SIP' ? 'selected' : '' ?>>SIP (Systematic Plan)</option>
                                <option value="BUY_LUMPSUM" <?= old('transaction_type', 'BUY_LUMPSUM') === 'BUY_LUMPSUM' ? 'selected' : '' ?>>Lumpsum (One-Time)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="transaction_date" class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control" 
                                   id="transaction_date" 
                                   name="transaction_date" 
                                   value="<?= old('transaction_date', date('Y-m-d')) ?>" 
                                   max="<?= date('Y-m-d') ?>" 
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label for="nav" class="form-label small fw-semibold text-secondary">Purchase NAV (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="nav" 
                                       name="nav" 
                                       min="0.0001" 
                                       step="0.0001" 
                                       placeholder="0.0000" 
                                       value="<?= old('nav') ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="amount" class="form-label small fw-semibold text-secondary">Invested Amount (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="amount" 
                                       name="amount" 
                                       min="1" 
                                       step="0.01" 
                                       placeholder="e.g. 5000" 
                                       value="<?= old('amount') ?>" 
                                       required>
                            </div>
                            <div class="form-text small">Typing amount auto-calculates units</div>
                        </div>

                        <div class="col-md-6">
                            <label for="units" class="form-label small fw-semibold text-secondary">Units Allocated <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="units" 
                                   name="units" 
                                   min="0.0001" 
                                   step="0.0001" 
                                   placeholder="0.0000" 
                                   value="<?= old('units') ?>" 
                                   required>
                            <div class="form-text small">Typing units auto-calculates amount</div>
                        </div>

                        <div class="col-md-6">
                            <label for="charges" class="form-label small fw-semibold text-secondary">Stamp Duty / Charges (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="charges" 
                                       name="charges" 
                                       min="0" 
                                       step="0.01" 
                                       placeholder="0.00" 
                                       value="<?= old('charges', '0.00') ?>">
                            </div>
                            <div class="form-text small">Standard MF stamp duty is 0.005%</div>
                        </div>

                        <div class="col-md-6">
                            <label for="notes" class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="notes" 
                                   name="notes" 
                                   placeholder="e.g. Monthly SIP installment #1" 
                                   value="<?= old('notes') ?>">
                        </div>
                    </div>

                    <!-- Live Calculation Preview Card -->
                    <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Total Net Outlay:</span>
                            <span class="fs-5 fw-bold text-success" id="previewTotal">₹ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Total Units Allocated:</span>
                            <span class="small fw-semibold text-dark" id="previewUnits">0.0000 units</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i>Save Mutual Fund & Add Initial Lot
                        </button>
                        <a href="<?= base_url('mutual-funds') ?>" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amfiInput = document.getElementById('amfi_code');
    const btnLookup = document.getElementById('btnLookupAmfi');
    const helpText  = document.getElementById('amfiLookupHelp');
    const spinner   = document.getElementById('lookupSpinner');

    const schemeInput = document.getElementById('scheme_name');
    const catInput    = document.getElementById('category');
    const amcInput    = document.getElementById('fund_house');
    const navInput    = document.getElementById('nav');

    const amountInput = document.getElementById('amount');
    const unitsInput  = document.getElementById('units');
    const chargesInput = document.getElementById('charges');
    const totalElem   = document.getElementById('previewTotal');
    const unitsElem   = document.getElementById('previewUnits');

    // 1. AMFI Code Lookup
    btnLookup.addEventListener('click', performLookup);
    amfiInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performLookup();
        }
    });

    function performLookup() {
        const code = amfiInput.value.trim();
        if (!code) return;

        spinner.className = 'spinner-border spinner-border-sm';
        btnLookup.disabled = true;
        helpText.textContent = 'Contacting AMFI server...';
        helpText.className = 'form-text small text-info';

        fetch('<?= base_url('mutual-funds/lookup-amfi') ?>/' + encodeURIComponent(code))
            .then(res => res.json())
            .then(data => {
                spinner.className = 'bi bi-arrow-repeat';
                btnLookup.disabled = false;

                if (data.status === 'SUCCESS') {
                    schemeInput.value = data.scheme_name;
                    catInput.value = data.scheme_category;
                    amcInput.value = data.fund_house;
                    if (data.nav > 0) {
                        navInput.value = parseFloat(data.nav).toFixed(4);
                        recalcFromAmount();
                    }
                    helpText.textContent = '✓ Scheme details retrieved! Latest NAV: ₹ ' + data.nav + ' (' + data.nav_date + ')';
                    helpText.className = 'form-text small text-success fw-medium';
                } else {
                    helpText.textContent = '✕ ' + (data.message || 'Scheme not found.');
                    helpText.className = 'form-text small text-danger';
                }
            })
            .catch(() => {
                spinner.className = 'bi bi-arrow-repeat';
                btnLookup.disabled = false;
                helpText.textContent = '✕ Network error while contacting AMFI API.';
                helpText.className = 'form-text small text-danger';
            });
    }

    // 2. Dual Amount <-> Units Auto-Sync
    amountInput.addEventListener('input', recalcFromAmount);
    unitsInput.addEventListener('input', recalcFromUnits);
    navInput.addEventListener('input', recalcFromAmount);
    chargesInput.addEventListener('input', updatePreview);

    function recalcFromAmount() {
        const amt = parseFloat(amountInput.value) || 0;
        const nav = parseFloat(navInput.value) || 0;
        if (amt > 0 && nav > 0) {
            unitsInput.value = (amt / nav).toFixed(4);
            // Default 0.005% stamp duty
            chargesInput.value = (amt * 0.00005).toFixed(2);
        }
        updatePreview();
    }

    function recalcFromUnits() {
        const u = parseFloat(unitsInput.value) || 0;
        const nav = parseFloat(navInput.value) || 0;
        if (u > 0 && nav > 0) {
            amountInput.value = (u * nav).toFixed(2);
            chargesInput.value = ((u * nav) * 0.00005).toFixed(2);
        }
        updatePreview();
    }

    function updatePreview() {
        const amt = parseFloat(amountInput.value) || 0;
        const u = parseFloat(unitsInput.value) || 0;
        const c = parseFloat(chargesInput.value) || 0;
        const totalOutlay = amt + c;

        totalElem.textContent = '₹ ' + totalOutlay.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        unitsElem.textContent = u.toFixed(4) + ' units';
    }
});
</script>

<?= $this->endSection() ?>

