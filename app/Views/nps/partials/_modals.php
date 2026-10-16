<!-- ========================================================================= -->
<!-- MINIMAL NPS TRANSACTION MODAL (Add Contribution / Quarterly Fee Deduction) -->
<!-- ========================================================================= -->
<div class="modal fade" id="npsTransactionModal" tabindex="-1" aria-labelledby="npsTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="npsTransactionModalLabel">
                        Add NPS Transaction
                    </h5>
                    <div class="text-muted small">PRAN: <?= esc($account['pran']) ?> &bull; <?= esc($account['pfm_name']) ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('nps/transaction') ?>" method="POST" id="npsTransForm">
                <?= csrf_field() ?>

                <div class="modal-body p-4">
                    <!-- Segmented Action Switcher -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actNpsContrib" value="CONTRIBUTION" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actNpsContrib">
                            <i class="bi bi-wallet2 me-1"></i>Add Contribution (Buy)
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actNpsDeduct" value="UNIT_DEDUCTION" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actNpsDeduct">
                            <i class="bi bi-receipt-cutoff me-1"></i>Quarterly Unit Deduction (Fees)
                        </label>
                    </div>

                    <!-- SUB-PANEL 1: ADD CONTRIBUTION -->
                    <div id="panelNpsContrib" class="nps-trans-panel">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Contribution Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="contribution_type" id="modalContribType" required>
                                    <option value="VOLUNTARY" selected>Voluntary (Subscriber)</option>
                                    <option value="EMPLOYEE">Employee</option>
                                    <option value="EMPLOYER">Employer</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Deposit Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="modalTxDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Gross Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="gross_amount" id="modalGrossAmt" min="1" step="0.01" placeholder="e.g. 10000" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Receipt / Ack No. (Optional)</label>
                                <input type="text" class="form-control" name="acknowledgement_no" placeholder="e.g. ACK-2026-1234">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Monthly voluntary contribution">
                            </div>

                            <div class="col-12">
                                <div class="collapse" id="modalChargesCollapse">
                                    <div class="card card-body bg-light border-0 py-2 px-3 mb-1">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-5">
                                                <label class="form-label small fw-semibold text-secondary mb-0">Optional Cash Charges (₹):</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control form-control-sm" name="optional_cash_charges" min="0" step="0.01" value="0.00">
                                            </div>
                                            <div class="col-md-3 text-muted small">Defaults to 0.</div>
                                        </div>
                                    </div>
                                </div>
                                <a class="text-decoration-none small text-secondary" data-bs-toggle="collapse" href="#modalChargesCollapse" role="button">
                                    <i class="bi bi-sliders me-1"></i>Optional upfront cash charges?
                                </a>
                            </div>
                        </div>

                        <!-- Editable Scheme Split Table -->
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                            <span class="small fw-bold text-dark text-uppercase">
                                <i class="bi bi-table me-1 text-primary"></i>Scheme Allotments (Manually Editable)
                            </span>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-3 py-0 px-2" id="btnModalAutoSplit" style="font-size: 0.75rem;">
                                <i class="bi bi-magic me-1"></i>Auto-Split using Target %
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0 small">
                                <thead class="table-light text-muted" style="font-size: 0.72rem;">
                                    <tr>
                                        <th>Scheme</th>
                                        <th>Target %</th>
                                        <th style="width: 25%;">Amount (₹)</th>
                                        <th style="width: 25%;">Allotment NAV (₹)</th>
                                        <th style="width: 25%;">Units Allotted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Scheme E -->
                                    <tr>
                                        <td><strong>Scheme E</strong> (Equity)</td>
                                        <td><?= (int)$account['alloc_equity'] ?>%</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-amt" name="amount_scheme_e" id="mAmtE" step="0.01" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-nav" name="nav_scheme_e" id="mNavE" step="0.0001" value="<?= $schemes['SCHEME_E']['current_nav'] ?>">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-units font-monospace fw-semibold" name="units_scheme_e" id="mUnitsE" step="0.0001" placeholder="0.0000">
                                        </td>
                                    </tr>
                                    <!-- Scheme C -->
                                    <tr>
                                        <td><strong>Scheme C</strong> (Corp Debt)</td>
                                        <td><?= (int)$account['alloc_corporate_debt'] ?>%</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-amt" name="amount_scheme_c" id="mAmtC" step="0.01" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-nav" name="nav_scheme_c" id="mNavC" step="0.0001" value="<?= $schemes['SCHEME_C']['current_nav'] ?>">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-units font-monospace fw-semibold" name="units_scheme_c" id="mUnitsC" step="0.0001" placeholder="0.0000">
                                        </td>
                                    </tr>
                                    <!-- Scheme G -->
                                    <tr>
                                        <td><strong>Scheme G</strong> (Govt Bonds)</td>
                                        <td><?= (int)$account['alloc_govt_bonds'] ?>%</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-amt" name="amount_scheme_g" id="mAmtG" step="0.01" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-nav" name="nav_scheme_g" id="mNavG" step="0.0001" value="<?= $schemes['SCHEME_G']['current_nav'] ?>">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-units font-monospace fw-semibold" name="units_scheme_g" id="mUnitsG" step="0.0001" placeholder="0.0000">
                                        </td>
                                    </tr>
                                    <!-- Scheme A -->
                                    <tr>
                                        <td><strong>Scheme A</strong> (Alternative)</td>
                                        <td><?= (int)$account['alloc_alternative'] ?>%</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-amt" name="amount_scheme_a" id="mAmtA" step="0.01" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-nav" name="nav_scheme_a" id="mNavA" step="0.0001" value="<?= $schemes['SCHEME_A']['current_nav'] ?>">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm m-scheme-units font-monospace fw-semibold" name="units_scheme_a" id="mUnitsA" step="0.0001" placeholder="0.0000">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SUB-PANEL 2: QUARTERLY UNIT DEDUCTION (FEES) -->
                    <div id="panelNpsDeduct" class="nps-trans-panel d-none">
                        <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Enter the units deducted from your account for CRA & POP servicing fees as shown in your quarterly SOT.
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Deduction Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="deduct_date" id="modalDeductDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quarter / Description</label>
                                <input type="text" class="form-control" name="quarter_notes" placeholder="e.g. Q1 FY26-27 CRA maintenance fees" disabled>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0 small">
                                <thead class="table-light text-muted" style="font-size: 0.72rem;">
                                    <tr>
                                        <th>Scheme</th>
                                        <th style="width: 50%;">Units to Deduct (Positive number)</th>
                                        <th style="width: 30%;">Prevailing NAV (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-primary-subtle text-primary border">Scheme E</span> Equity</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm deduct-unit-input" name="deduct_units_scheme_e" min="0" step="0.0001" placeholder="e.g. 0.4500" disabled>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" name="deduct_nav_scheme_e" value="<?= $schemes['SCHEME_E']['current_nav'] ?>" step="0.0001" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-info-subtle text-info-emphasis border">Scheme C</span> Corp Debt</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm deduct-unit-input" name="deduct_units_scheme_c" min="0" step="0.0001" placeholder="e.g. 0.3200" disabled>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" name="deduct_nav_scheme_c" value="<?= $schemes['SCHEME_C']['current_nav'] ?>" step="0.0001" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-success-subtle text-success border">Scheme G</span> Govt Bonds</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm deduct-unit-input" name="deduct_units_scheme_g" min="0" step="0.0001" placeholder="e.g. 0.2500" disabled>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" name="deduct_nav_scheme_g" value="<?= $schemes['SCHEME_G']['current_nav'] ?>" step="0.0001" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-warning-subtle text-warning-emphasis border">Scheme A</span> Alternative</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm deduct-unit-input" name="deduct_units_scheme_a" min="0" step="0.0001" placeholder="0.0000" disabled>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" name="deduct_nav_scheme_a" value="<?= $schemes['SCHEME_A']['current_nav'] ?>" step="0.0001" disabled>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitNpsTrans">
                        Confirm Contribution
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- QUICK UPDATE SCHEME NAVS MODAL                                            -->
<!-- ========================================================================= -->
<div class="modal fade" id="npsNavModal" tabindex="-1" aria-labelledby="npsNavModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="npsNavModalLabel">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Update Current Scheme NAVs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('nps/update-navs') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAV Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="nav_date" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Scheme E (Equity) NAV (₹)</label>
                            <input type="number" class="form-control" name="nav_e" step="0.0001" value="<?= $schemes['SCHEME_E']['current_nav'] ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Scheme C (Corp Debt) NAV (₹)</label>
                            <input type="number" class="form-control" name="nav_c" step="0.0001" value="<?= $schemes['SCHEME_C']['current_nav'] ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Scheme G (Govt Bonds) NAV (₹)</label>
                            <input type="number" class="form-control" name="nav_g" step="0.0001" value="<?= $schemes['SCHEME_G']['current_nav'] ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Scheme A (Alternative) NAV (₹)</label>
                            <input type="number" class="form-control" name="nav_a" step="0.0001" value="<?= $schemes['SCHEME_A']['current_nav'] ?>">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        Update NAVs & Recalculate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT NPS ACCOUNT & ALLOCATION MODAL                                       -->
<!-- ========================================================================= -->
<div class="modal fade" id="editNpsAccountModal" tabindex="-1" aria-labelledby="editNpsAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editNpsAccountModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Account &amp; Target Allocation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('nps/update-account') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="account_id" id="editNpsAccountId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Subscriber Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="subscriber_name" id="editNpsSubName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Pension Fund Manager (PFM) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="pfm_name" id="editNpsPfmName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Investment Choice <span class="text-danger">*</span></label>
                            <select class="form-select" name="investment_choice" id="editNpsChoice" required>
                                <option value="ACTIVE">Active Choice (Self Allocation)</option>
                                <option value="AUTO">Auto Choice (Lifecycle)</option>
                            </select>
                        </div>

                        <div class="col-12"><hr class="my-1"></div>
                        <div class="col-12">
                            <span class="small fw-semibold text-dark">Asset Allocation % (Must sum to 100%)</span>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold text-primary">Scheme E (%)</label>
                            <input type="number" class="form-control" name="alloc_equity" id="editNpsAllocE" min="0" max="100" step="1" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold text-info-emphasis">Scheme C (%)</label>
                            <input type="number" class="form-control" name="alloc_corporate_debt" id="editNpsAllocC" min="0" max="100" step="1" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold text-success">Scheme G (%)</label>
                            <input type="number" class="form-control" name="alloc_govt_bonds" id="editNpsAllocG" min="0" max="100" step="1" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold text-warning-emphasis">Scheme A (%)</label>
                            <input type="number" class="form-control" name="alloc_alternative" id="editNpsAllocA" min="0" max="5" step="1" value="0">
                        </div>

                        <div class="col-12 mt-3 pt-2 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="small fw-semibold text-dark">
                                    <i class="bi bi-upc-scan text-primary me-1"></i>NPS Scheme Codes (for npsnav.in Auto-Update)
                                </span>
                                <small class="text-muted" style="font-size: 0.72rem;">e.g. HDFC: SM008001, SM008002, SM008003, SM008008</small>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Scheme E Code</label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="scheme_code_equity" id="editNpsCodeE" placeholder="SM008001">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Scheme C Code</label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="scheme_code_corporate_debt" id="editNpsCodeC" placeholder="SM008002">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Scheme G Code</label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="scheme_code_govt_bonds" id="editNpsCodeG" placeholder="SM008003">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Scheme A Code</label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="scheme_code_alternative" id="editNpsCodeA" placeholder="SM008008">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT NPS CONTRIBUTION MODAL                                               -->
<!-- ========================================================================= -->
<div class="modal fade" id="editNpsContribModal" tabindex="-1" aria-labelledby="editNpsContribModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editNpsContribModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Contribution Transaction
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('nps/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editNpsContribId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editNpsConDate" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Contribution Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="contribution_type" id="editNpsConType" required>
                                <option value="VOLUNTARY">Voluntary (Tier 1)</option>
                                <option value="EMPLOYEE">Employee (Mandatory)</option>
                                <option value="EMPLOYER">Employer (Corporate NPS)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Gross Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-bold" name="gross_amount" id="editNpsConGross" min="1" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Cash Charges Deducted (₹)</label>
                            <input type="number" class="form-control" name="optional_cash_charges" id="editNpsConCharges" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Ack / Receipt No.</label>
                            <input type="text" class="form-control" name="acknowledgement_no" id="editNpsConAck">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editNpsConNotes">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 small">
                            <thead class="table-light text-muted" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Scheme</th>
                                    <th>Allocated Amt (₹)</th>
                                    <th>Purchase NAV (₹)</th>
                                    <th>Units Allotted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-primary-subtle text-primary border">Scheme E</span> Equity</td>
                                    <td><input type="number" class="form-control form-control-sm" name="amount_scheme_e" id="editNpsAmtE" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="nav_scheme_e" id="editNpsNavE" step="0.0001"></td>
                                    <td><input type="number" class="form-control form-control-sm font-monospace fw-semibold" name="units_scheme_e" id="editNpsUnitsE" step="0.0001"></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info-subtle text-info-emphasis border">Scheme C</span> Corp Debt</td>
                                    <td><input type="number" class="form-control form-control-sm" name="amount_scheme_c" id="editNpsAmtC" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="nav_scheme_c" id="editNpsNavC" step="0.0001"></td>
                                    <td><input type="number" class="form-control form-control-sm font-monospace fw-semibold" name="units_scheme_c" id="editNpsUnitsC" step="0.0001"></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success-subtle text-success border">Scheme G</span> Govt Bonds</td>
                                    <td><input type="number" class="form-control form-control-sm" name="amount_scheme_g" id="editNpsAmtG" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="nav_scheme_g" id="editNpsNavG" step="0.0001"></td>
                                    <td><input type="number" class="form-control form-control-sm font-monospace fw-semibold" name="units_scheme_g" id="editNpsUnitsG" step="0.0001"></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning-subtle text-warning-emphasis border">Scheme A</span> Alternative</td>
                                    <td><input type="number" class="form-control form-control-sm" name="amount_scheme_a" id="editNpsAmtA" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="nav_scheme_a" id="editNpsNavA" step="0.0001"></td>
                                    <td><input type="number" class="form-control form-control-sm font-monospace fw-semibold" name="units_scheme_a" id="editNpsUnitsA" step="0.0001"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT NPS QUARTERLY FEE DEDUCTION MODAL                                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="editNpsDeductModal" tabindex="-1" aria-labelledby="editNpsDeductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editNpsDeductModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Quarterly Fee Deduction
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('nps/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editNpsDeductId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Deduction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editNpsDedDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quarter / Description</label>
                            <input type="text" class="form-control" name="notes" id="editNpsDedNotes">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Ack / Ref No.</label>
                            <input type="text" class="form-control" name="acknowledgement_no" id="editNpsDedAck">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 small">
                            <thead class="table-light text-muted" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Scheme</th>
                                    <th style="width: 50%;">Units to Deduct (Positive number)</th>
                                    <th style="width: 30%;">Prevailing NAV (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-primary-subtle text-primary border">Scheme E</span> Equity</td>
                                    <td><input type="number" class="form-control form-control-sm" name="deduct_units_scheme_e" id="editNpsDedUnitsE" min="0" step="0.0001"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="deduct_nav_scheme_e" id="editNpsDedNavE" step="0.0001"></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info-subtle text-info-emphasis border">Scheme C</span> Corp Debt</td>
                                    <td><input type="number" class="form-control form-control-sm" name="deduct_units_scheme_c" id="editNpsDedUnitsC" min="0" step="0.0001"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="deduct_nav_scheme_c" id="editNpsDedNavC" step="0.0001"></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success-subtle text-success border">Scheme G</span> Govt Bonds</td>
                                    <td><input type="number" class="form-control form-control-sm" name="deduct_units_scheme_g" id="editNpsDedUnitsG" min="0" step="0.0001"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="deduct_nav_scheme_g" id="editNpsDedNavG" step="0.0001"></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning-subtle text-warning-emphasis border">Scheme A</span> Alternative</td>
                                    <td><input type="number" class="form-control form-control-sm" name="deduct_units_scheme_a" id="editNpsDedUnitsA" min="0" step="0.0001"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="deduct_nav_scheme_a" id="editNpsDedNavA" step="0.0001"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
