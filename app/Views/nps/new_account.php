<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('nps') ?>" class="text-decoration-none">NPS</a></li>
                <li class="breadcrumb-item active" aria-current="page">Setup Tier 1 Account</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">Setup NPS Tier 1 Account</h3>
        <p class="text-secondary small mb-0">Use this form <strong>only once</strong> to register your 12-digit PRAN and record your initial contribution.</p>
    </div>
    <a href="<?= base_url('nps') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                <div class="d-flex align-items-center">
                    <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3 me-3">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Tier 1 PRAN Registration & Asset Allocation</h5>
                        <small class="text-muted">National Pension System &bull; PFRDA Regulated Retirement Corpus</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('nps/new') ?>" method="POST" id="newNpsForm">
                    <?= csrf_field() ?>

                    <!-- Section 1: PRAN & Subscriber Identity -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-person-badge me-1 text-primary"></i> 1. PRAN Identity & Pension Fund Manager
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="pran" class="form-label small fw-semibold text-secondary">PRAN (12 digits) <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control font-monospace" 
                                   id="pran" 
                                   name="pran" 
                                   maxlength="12" 
                                   pattern="\d{10,12}" 
                                   placeholder="e.g. 110055228833" 
                                   value="<?= old('pran') ?>" 
                                   required 
                                   autofocus>
                            <div class="form-text small">Permanent Retirement Account Number</div>
                        </div>

                        <div class="col-md-4">
                            <label for="subscriber_name" class="form-label small fw-semibold text-secondary">Subscriber Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="subscriber_name" 
                                   name="subscriber_name" 
                                   placeholder="Your full name" 
                                   value="<?= old('subscriber_name', $defaultName) ?>" 
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label for="pfm_name" class="form-label small fw-semibold text-secondary">Pension Fund Manager (PFM) <span class="text-danger">*</span></label>
                            <select class="form-select" id="pfm_name" name="pfm_name" required>
                                <option value="HDFC Pension Management" <?= old('pfm_name') === 'HDFC Pension Management' ? 'selected' : '' ?>>HDFC Pension Management</option>
                                <option value="SBI Pension Funds" <?= old('pfm_name') === 'SBI Pension Funds' ? 'selected' : '' ?>>SBI Pension Funds</option>
                                <option value="ICICI Prudential Pension Fund" <?= old('pfm_name') === 'ICICI Prudential Pension Fund' ? 'selected' : '' ?>>ICICI Prudential Pension Fund</option>
                                <option value="UTI Retirement Solutions" <?= old('pfm_name') === 'UTI Retirement Solutions' ? 'selected' : '' ?>>UTI Retirement Solutions</option>
                                <option value="Kotak Mahindra Pension Fund" <?= old('pfm_name') === 'Kotak Mahindra Pension Fund' ? 'selected' : '' ?>>Kotak Mahindra Pension Fund</option>
                                <option value="Aditya Birla Sun Life Pension" <?= old('pfm_name') === 'Aditya Birla Sun Life Pension' ? 'selected' : '' ?>>Aditya Birla Sun Life Pension</option>
                                <option value="Axis Pension Fund" <?= old('pfm_name') === 'Axis Pension Fund' ? 'selected' : '' ?>>Axis Pension Fund</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 2: Target Asset Allocation -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-pie-chart me-1 text-primary"></i> 2. Target Asset Allocation % (Active Choice)
                    </h6>

                    <div class="alert alert-light border py-2 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                        <span>Allocation across Scheme E, C, G, A must total <strong>100%</strong>:</span>
                        <span class="badge bg-primary fs-6 px-3 py-1" id="allocTotalBadge">Total: 100%</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">
                                <span class="badge bg-primary-subtle text-primary border me-1">E</span> Scheme E (Equity) % <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control alloc-input" name="alloc_equity" id="allocE" min="0" max="75" step="0.5" value="<?= old('alloc_equity', '50.00') ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text small">Max 75% for Tier 1</div>
                        </div>

                        <div class="col-sm-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">
                                <span class="badge bg-info-subtle text-info-emphasis border me-1">C</span> Scheme C (Corp Debt) % <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control alloc-input" name="alloc_corp_debt" id="allocC" min="0" max="100" step="0.5" value="<?= old('alloc_corp_debt', '30.00') ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">
                                <span class="badge bg-success-subtle text-success border me-1">G</span> Scheme G (Govt Bonds) % <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control alloc-input" name="alloc_govt_bonds" id="allocG" min="0" max="100" step="0.5" value="<?= old('alloc_govt_bonds', '20.00') ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">
                                <span class="badge bg-warning-subtle text-warning-emphasis border me-1">A</span> Scheme A (Alternative) %
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control alloc-input" name="alloc_alternative" id="allocA" min="0" max="5" step="0.5" value="<?= old('alloc_alternative', '0.00') ?>">
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text small">Max 5%</div>
                        </div>
                    </div>

                    <!-- Section: NPS Scheme Codes (Auto-Update NAVs via npsnav.in) -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-upc-scan me-1 text-primary"></i> 3. NPS Scheme Codes (Auto NAV Update via npsnav.in)
                    </h6>
                    <div class="alert alert-light border py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-info-circle text-primary me-1"></i> Save your scheme codes to enable one-click automatic daily NAV updates from <strong>npsnav.in</strong>. (e.g. HDFC Tier I: <code>SM008001</code>, <code>SM008002</code>, <code>SM008003</code>, <code>SM008008</code>).
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Scheme E Code</label>
                            <input type="text" class="form-control text-uppercase font-monospace" name="scheme_code_equity" placeholder="e.g. SM008001" value="<?= old('scheme_code_equity', 'SM008001') ?>">
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Scheme C Code</label>
                            <input type="text" class="form-control text-uppercase font-monospace" name="scheme_code_corporate_debt" placeholder="e.g. SM008002" value="<?= old('scheme_code_corporate_debt', 'SM008002') ?>">
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Scheme G Code</label>
                            <input type="text" class="form-control text-uppercase font-monospace" name="scheme_code_govt_bonds" placeholder="e.g. SM008003" value="<?= old('scheme_code_govt_bonds', 'SM008003') ?>">
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Scheme A Code</label>
                            <input type="text" class="form-control text-uppercase font-monospace" name="scheme_code_alternative" placeholder="e.g. SM008008" value="<?= old('scheme_code_alternative', 'SM008008') ?>">
                        </div>
                    </div>

                    <!-- Section 4: Initial Contribution Details -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-wallet2 me-1 text-primary"></i> 4. Initial Contribution Order
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="contribution_type" class="form-label small fw-semibold text-secondary">Contribution Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="contribution_type" name="contribution_type" required>
                                <option value="VOLUNTARY" selected>Voluntary (Subscriber)</option>
                                <option value="EMPLOYEE">Employee Contribution</option>
                                <option value="EMPLOYER">Employer Contribution</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="transaction_date" class="form-label small fw-semibold text-secondary">Deposit Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="<?= old('transaction_date', date('Y-m-d')) ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label for="gross_amount" class="form-label small fw-semibold text-secondary">Gross Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="gross_amount" name="gross_amount" min="1" step="0.01" placeholder="e.g. 50000" value="<?= old('gross_amount') ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label for="acknowledgement_no" class="form-label small fw-semibold text-secondary">Receipt / Ack No. (Optional)</label>
                            <input type="text" class="form-control" id="acknowledgement_no" name="acknowledgement_no" placeholder="e.g. ACK2024-9988" value="<?= old('acknowledgement_no') ?>">
                        </div>

                        <div class="col-12">
                            <div class="collapse" id="optionalChargesCollapse">
                                <div class="card card-body bg-light border-0 py-2 px-3 mb-2">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary mb-0">Optional Upfront Cash Charges (₹):</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" class="form-control form-control-sm" name="optional_cash_charges" id="optionalCharges" min="0" step="0.01" value="0.00">
                                        </div>
                                        <div class="col-md-4 text-muted small">
                                            Only if POP deducted cash fees at contribution. (NPS usually debits quarterly units).
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a class="text-decoration-none small text-secondary" data-bs-toggle="collapse" href="#optionalChargesCollapse" role="button">
                                <i class="bi bi-sliders me-1"></i>Need upfront cash charges deduction? Click here (Optional)
                            </a>
                        </div>
                    </div>

                    <!-- Section 4: Scheme Units Allotment (Manually Editable) -->
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-0">
                            <i class="bi bi-table me-1 text-primary"></i> 4. Scheme Split (Manually Editable NAV & Units Allotted)
                        </h6>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-3 py-0 px-2" id="btnAutoSplit" style="font-size: 0.78rem;">
                            <i class="bi bi-magic me-1"></i>Auto-Split from Target %
                        </button>
                    </div>
                    <p class="text-muted small mb-3">
                        Enter the exact Allotment NAV and Units allotted from your NSDL / KFintech statement for each scheme.
                    </p>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle mb-0" id="schemeSplitTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th style="width: 25%;">Asset Scheme</th>
                                    <th style="width: 25%;">Allocated Amount (₹)</th>
                                    <th style="width: 25%;">Allotment NAV (₹) <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">Units Allotted <span class="text-danger">*</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Scheme E -->
                                <tr>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">Scheme E</span>
                                        <strong>Equity</strong>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-amt" name="amount_scheme_e" id="amtE" step="0.01" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-nav" name="nav_scheme_e" id="navE" step="0.0001" placeholder="NAV e.g. 45.2000">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-units font-monospace fw-semibold" name="units_scheme_e" id="unitsE" step="0.0001" placeholder="0.0000">
                                    </td>
                                </tr>
                                <!-- Scheme C -->
                                <tr>
                                    <td>
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle me-1">Scheme C</span>
                                        <strong>Corporate Debt</strong>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-amt" name="amount_scheme_c" id="amtC" step="0.01" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-nav" name="nav_scheme_c" id="navC" step="0.0001" placeholder="NAV e.g. 28.5000">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-units font-monospace fw-semibold" name="units_scheme_c" id="unitsC" step="0.0001" placeholder="0.0000">
                                    </td>
                                </tr>
                                <!-- Scheme G -->
                                <tr>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle me-1">Scheme G</span>
                                        <strong>Govt Securities</strong>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-amt" name="amount_scheme_g" id="amtG" step="0.01" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-nav" name="nav_scheme_g" id="navG" step="0.0001" placeholder="NAV e.g. 22.4000">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-units font-monospace fw-semibold" name="units_scheme_g" id="unitsG" step="0.0001" placeholder="0.0000">
                                    </td>
                                </tr>
                                <!-- Scheme A -->
                                <tr>
                                    <td>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle me-1">Scheme A</span>
                                        <strong>Alternative Assets</strong>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-amt" name="amount_scheme_a" id="amtA" step="0.01" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-nav" name="nav_scheme_a" id="navA" step="0.0001" placeholder="NAV (if applicable)">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm scheme-units font-monospace fw-semibold" name="units_scheme_a" id="unitsA" step="0.0001" placeholder="0.0000">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('nps') ?>" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold" id="btnSubmitNps">
                            <i class="bi bi-check-lg me-1"></i>Save & Setup Tier 1 PRAN
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const allocInputs = document.querySelectorAll('.alloc-input');
    const badge = document.getElementById('allocTotalBadge');
    const grossAmtInput = document.getElementById('gross_amount');
    const btnAutoSplit = document.getElementById('btnAutoSplit');

    // 1. Allocation % Total Validation
    function updateAllocTotal() {
        let total = 0;
        allocInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        badge.textContent = 'Total: ' + total.toFixed(1) + '%';
        if (Math.abs(total - 100.0) < 0.01) {
            badge.className = 'badge bg-success fs-6 px-3 py-1';
        } else {
            badge.className = 'badge bg-danger fs-6 px-3 py-1';
        }
    }
    allocInputs.forEach(input => input.addEventListener('input', updateAllocTotal));
    updateAllocTotal();

    // 2. Auto-Split Button: distributes gross amount into scheme amounts based on target %
    btnAutoSplit.addEventListener('click', function() {
        const gross = parseFloat(grossAmtInput.value) || 0;
        if (gross <= 0) {
            alert('Please enter Gross Amount first.');
            grossAmtInput.focus();
            return;
        }

        const ePct = (parseFloat(document.getElementById('allocE').value) || 0) / 100;
        const cPct = (parseFloat(document.getElementById('allocC').value) || 0) / 100;
        const gPct = (parseFloat(document.getElementById('allocG').value) || 0) / 100;
        const aPct = (parseFloat(document.getElementById('allocA').value) || 0) / 100;

        document.getElementById('amtE').value = (gross * ePct).toFixed(2);
        document.getElementById('amtC').value = (gross * cPct).toFixed(2);
        document.getElementById('amtG').value = (gross * gPct).toFixed(2);
        document.getElementById('amtA').value = (gross * aPct).toFixed(2);

        // Calculate units if NAV is present
        ['E', 'C', 'G', 'A'].forEach(s => {
            calcUnitsFromAmtAndNav(s);
        });
    });

    // 3. Dynamic unit calculation helper
    function calcUnitsFromAmtAndNav(scheme) {
        const amt = parseFloat(document.getElementById('amt' + scheme).value) || 0;
        const nav = parseFloat(document.getElementById('nav' + scheme).value) || 0;
        const unitsInput = document.getElementById('units' + scheme);

        if (amt > 0 && nav > 0) {
            unitsInput.value = (amt / nav).toFixed(4);
        }
    }

    ['E', 'C', 'G', 'A'].forEach(s => {
        const navEl = document.getElementById('nav' + s);
        const amtEl = document.getElementById('amt' + s);
        const unitsEl = document.getElementById('units' + s);

        navEl.addEventListener('input', () => calcUnitsFromAmtAndNav(s));
        amtEl.addEventListener('input', () => calcUnitsFromAmtAndNav(s));

        // If user manually edits units and nav is present, update amount
        unitsEl.addEventListener('input', function() {
            const u = parseFloat(this.value) || 0;
            const nav = parseFloat(navEl.value) || 0;
            if (u > 0 && nav > 0) {
                amtEl.value = (u * nav).toFixed(2);
            }
        });
    });
});
</script>

<?= $this->endSection() ?>

