<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-4 py-3">

    <!-- Page Header & Action Controls -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                    <i class="bi bi-wallet2 me-1"></i> Charges Ledger
                </span>
                <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                    <?= esc($range['shortLabel']) ?>
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-1">Consolidated Brokerage &amp; Expenses</h3>
            <p class="text-secondary small mb-0">
                Record and manage daily contract note fees, turnover taxes, stamp duties, and platform charges across your portfolio.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- FY Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-3 px-3 py-1.5 shadow-sm fw-medium" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-calendar3 me-1 text-primary"></i> <?= esc($range['shortLabel']) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 mt-1">
                    <li><h6 class="dropdown-header text-uppercase" style="font-size: 0.68rem;">Financial Year Filter</h6></li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center small <?= $range['key'] === 'CURRENT_FY' ? 'active fw-semibold' : '' ?>" 
                           href="<?= base_url('expenses?fy=CURRENT_FY&module=' . urlencode($selectedModule) . '&type=' . urlencode($selectedType)) ?>">
                            <span>Current FY (<?= $range['fyRanges']['current']['label'] ?>)</span>
                            <?php if ($range['key'] === 'CURRENT_FY'): ?><i class="bi bi-check2"></i><?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center small <?= $range['key'] === 'LAST_FY' ? 'active fw-semibold' : '' ?>" 
                           href="<?= base_url('expenses?fy=LAST_FY&module=' . urlencode($selectedModule) . '&type=' . urlencode($selectedType)) ?>">
                            <span>Last FY (<?= $range['fyRanges']['last']['label'] ?>)</span>
                            <?php if ($range['key'] === 'LAST_FY'): ?><i class="bi bi-check2"></i><?php endif; ?>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center small <?= $range['key'] === 'ALL' ? 'active fw-semibold' : '' ?>" 
                           href="<?= base_url('expenses?fy=ALL&module=' . urlencode($selectedModule) . '&type=' . urlencode($selectedType)) ?>">
                            <span>All Time (Full History)</span>
                            <?php if ($range['key'] === 'ALL'): ?><i class="bi bi-check2"></i><?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Reports Shortcut -->
            <a href="<?= base_url('reports/expenses?fy=' . esc($range['key'])) ?>" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-1.5 shadow-sm fw-medium" title="View Full Friction Audit">
                <i class="bi bi-receipt-cutoff me-1 text-danger"></i> Friction Report
            </a>

            <!-- Add Expense Button -->
            <button type="button" class="btn btn-danger btn-sm rounded-3 px-3 py-1.5 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="bi bi-plus-circle me-1"></i> Record Expense
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm py-2 px-3 mb-4 small" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close py-2.5" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm py-2 px-3 mb-4 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close py-2.5" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm py-2 px-3 mb-4 small" role="alert">
            <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the following errors:</div>
            <ul class="mb-0 ps-3">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close py-2.5" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 4 KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Expenses -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-danger border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Recorded Expenses</div>
                        <h4 class="fw-bold text-danger mb-0 mt-1"><?= format_inr($summary['total_amount']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;"><?= number_format($summary['total_count']) ?> recorded transactions</div>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded-3 p-2">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Brokerage Charges -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-primary border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Brokerage Charges</div>
                        <h4 class="fw-bold text-primary mb-0 mt-1"><?= format_inr($summary['total_brokerage']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Broker execution commissions</div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2">
                        <i class="bi bi-cash-coin fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. STT & Statutory Taxes -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-info border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">STT &amp; Govt Taxes</div>
                        <h4 class="fw-bold text-info-emphasis mb-0 mt-1"><?= format_inr($summary['total_stt']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Turnover charges, stamp duty &amp; GST</div>
                    </div>
                    <div class="bg-info-subtle text-info-emphasis rounded-3 p-2">
                        <i class="bi bi-file-earmark-ruled fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Platform & Misc Fees -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-secondary border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Platform &amp; Misc Fees</div>
                        <h4 class="fw-bold text-dark mb-0 mt-1"><?= format_inr($summary['total_platform']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Demat AMC, DP &amp; payment charges</div>
                    </div>
                    <div class="bg-light text-secondary rounded-3 p-2">
                        <i class="bi bi-gear fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar & Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        
        <!-- Filter Toolbar Header -->
        <div class="card-header bg-white border-bottom py-3 px-4">
            <form action="<?= base_url('expenses') ?>" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="fy" value="<?= esc($range['key']) ?>">

                <div class="col-auto">
                    <span class="text-muted small fw-semibold"><i class="bi bi-funnel me-1"></i> Filter By:</span>
                </div>

                <!-- Module Filter -->
                <div class="col-sm-auto">
                    <select name="module" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                        <option value="ALL" <?= $selectedModule === 'ALL' ? 'selected' : '' ?>>All Investment Modules</option>
                        <option value="equity" <?= $selectedModule === 'equity' ? 'selected' : '' ?>>Equities (Stocks)</option>
                        <option value="etf" <?= $selectedModule === 'etf' ? 'selected' : '' ?>>Exchange Traded Funds (ETFs)</option>
                        <option value="bond" <?= $selectedModule === 'bond' ? 'selected' : '' ?>>Bonds &amp; Fixed Income</option>
                        <option value="reit_invit" <?= $selectedModule === 'reit_invit' ? 'selected' : '' ?>>InvITs &amp; REITs</option>
                        <option value="mutual_fund" <?= $selectedModule === 'mutual_fund' ? 'selected' : '' ?>>Mutual Funds</option>
                    </select>
                </div>

                <!-- Expense Type Filter -->
                <div class="col-sm-auto">
                    <select name="type" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                        <option value="ALL" <?= $selectedType === 'ALL' ? 'selected' : '' ?>>All Expense Types</option>
                        <option value="BROKERAGE" <?= $selectedType === 'BROKERAGE' ? 'selected' : '' ?>>Brokerage Charges</option>
                        <option value="STT_TAXES" <?= $selectedType === 'STT_TAXES' ? 'selected' : '' ?>>STT &amp; Taxes</option>
                        <option value="PLATFORM_MISC" <?= $selectedType === 'PLATFORM_MISC' ? 'selected' : '' ?>>Platform &amp; Misc</option>
                    </select>
                </div>

                <?php if ($selectedModule !== 'ALL' || $selectedType !== 'ALL'): ?>
                    <div class="col-auto">
                        <a href="<?= base_url('expenses?fy=' . esc($range['key'])) ?>" class="btn btn-light btn-sm rounded-3 text-danger border" title="Reset Filters">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </a>
                    </div>
                <?php endif; ?>

                <div class="col text-end d-none d-md-block">
                    <span class="badge bg-light text-muted border px-2.5 py-1">
                        Showing <?= count($expenses) ?> transaction<?= count($expenses) === 1 ? '' : 's' ?>
                    </span>
                </div>
            </form>
        </div>

        <!-- Interactive Expenses Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th class="ps-4" style="width: 130px;">Date</th>
                        <th style="width: 200px;">Related Module</th>
                        <th style="width: 200px;">Expense Type</th>
                        <th class="text-end" style="width: 160px;">Amount (₹)</th>
                        <th>Notes &amp; Contract Details</th>
                        <th class="text-center pe-4" style="width: 110px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($expenses)): ?>
                        <?php foreach ($expenses as $e): ?>
                            <?php 
                            $mod = $moduleLabels[$e['module']] ?? ['label' => ucfirst($e['module']), 'icon' => 'bi bi-tag', 'color' => 'secondary'];
                            $typ = $typeLabels[$e['expense_type']] ?? ['label' => $e['expense_type'], 'badge' => 'secondary', 'icon' => 'bi bi-receipt'];
                            ?>
                            <tr>
                                <!-- Date -->
                                <td class="ps-4 text-nowrap fw-medium text-dark">
                                    <?= date('d-M-Y', strtotime($e['expense_date'])) ?>
                                </td>

                                <!-- Related Module -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-<?= esc($mod['color']) ?>-subtle text-<?= esc($mod['color']) ?> rounded-2 p-1.5 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                            <i class="<?= esc($mod['icon']) ?>" style="font-size: 0.85rem;"></i>
                                        </div>
                                        <span class="fw-semibold text-dark"><?= esc($mod['label']) ?></span>
                                    </div>
                                </td>

                                <!-- Expense Type -->
                                <td>
                                    <span class="badge bg-<?= esc($typ['badge']) ?>-subtle text-<?= esc($typ['badge']) ?> border border-<?= esc($typ['badge']) ?>-subtle px-2 py-1 rounded-pill">
                                        <i class="<?= esc($typ['icon']) ?> me-1"></i><?= esc($typ['label']) ?>
                                    </span>
                                </td>

                                <!-- Amount -->
                                <td class="text-end fw-bold text-danger text-nowrap">
                                    - <?= format_inr($e['amount']) ?>
                                </td>

                                <!-- Notes -->
                                <td>
                                    <?php if (!empty($e['notes'])): ?>
                                        <span class="text-secondary"><?= esc($e['notes']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic" style="font-size: 0.78rem;">No notes recorded</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions -->
                                <td class="text-center pe-4 text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-outline-secondary btn-edit-expense py-1 px-2 rounded-start"
                                                data-id="<?= (int)$e['id'] ?>"
                                                data-date="<?= esc($e['expense_date']) ?>"
                                                data-amount="<?= esc($e['amount']) ?>"
                                                data-type="<?= esc($e['expense_type']) ?>"
                                                data-module="<?= esc($e['module']) ?>"
                                                data-notes="<?= esc($e['notes'] ?? '') ?>"
                                                title="Edit Expense">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-delete-expense py-1 px-2 rounded-end"
                                                data-id="<?= (int)$e['id'] ?>"
                                                data-desc="<?= esc(date('d-M-Y', strtotime($e['expense_date'])) . ' - ' . $mod['label'] . ' (' . format_inr($e['amount']) . ')') ?>"
                                                title="Delete Expense">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3 text-muted">
                                        <i class="bi bi-wallet2 fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">No Expense Entries Found</h6>
                                    <p class="text-muted small mb-3">
                                        <?php if ($selectedModule !== 'ALL' || $selectedType !== 'ALL'): ?>
                                            No charges match the selected filter criteria for this period.
                                        <?php else: ?>
                                            You haven't recorded any consolidated contract note charges or brokerage expenses for this period.
                                        <?php endif; ?>
                                    </p>
                                    <button type="button" class="btn btn-danger btn-sm rounded-3 px-3 py-1.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                        <i class="bi bi-plus-circle me-1"></i> Record First Expense
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ======================================================================= -->
<!-- MODAL: ADD EXPENSE                                                      -->
<!-- ======================================================================= -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('expenses/add') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="modal-header bg-danger text-white py-3 px-4">
                    <h5 class="modal-title fw-bold" id="addExpenseModalLabel">
                        <i class="bi bi-wallet2 me-2"></i>Record Consolidated Expense
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-light border border-subtle small mb-3 text-muted">
                        <i class="bi bi-info-circle me-1 text-primary"></i> Record contract note turnover fees, broker commissions, STT, and demat charges. These automatically add up to your report friction.
                    </div>

                    <!-- Date & Amount -->
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-dark">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control form-control-sm rounded-3" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-dark">Amount (₹) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted">₹</span>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control rounded-end-3" placeholder="e.g. 47.20" required>
                            </div>
                        </div>
                    </div>

                    <!-- Expense Type & Related Module -->
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-dark">Expense Type <span class="text-danger">*</span></label>
                            <select name="expense_type" class="form-select form-select-sm rounded-3" required>
                                <option value="BROKERAGE">Brokerage Charges</option>
                                <option value="STT_TAXES">STT &amp; Statutory Taxes</option>
                                <option value="PLATFORM_MISC">Platform &amp; Misc Fees</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-dark">Related Module <span class="text-danger">*</span></label>
                            <select name="module" class="form-select form-select-sm rounded-3" required>
                                <option value="equity">Equities</option>
                                <option value="etf">Exchange Traded Funds (ETFs)</option>
                                <option value="bond">Bonds &amp; Fixed Income</option>
                                <option value="reit_invit">InvITs &amp; REITs</option>
                                <option value="mutual_fund">Mutual Funds</option>
                            </select>
                        </div>
                    </div>

                    <!-- Notes / Description -->
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-dark">Notes / Reference <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" name="notes" class="form-control form-control-sm rounded-3" placeholder="e.g. Zerodha Contract Note #20260923, CDSL DP charges">
                        <div class="form-text text-muted" style="font-size: 0.72rem;">Enter contract note number, broker name, or reason for future audit reference.</div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-3 px-4 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i> Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================================= -->
<!-- MODAL: EDIT EXPENSE                                                     -->
<!-- ======================================================================= -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= base_url('expenses/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="expense_id" id="edit_expense_id">

                <div class="modal-header bg-dark text-white py-3 px-4">
                    <h5 class="modal-title fw-bold" id="editExpenseModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Expense Record
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Date & Amount -->
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-dark">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" id="edit_expense_date" class="form-control form-control-sm rounded-3" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-dark">Amount (₹) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted">₹</span>
                                <input type="number" step="0.01" min="0.01" name="amount" id="edit_expense_amount" class="form-control rounded-end-3" required>
                            </div>
                        </div>
                    </div>

                    <!-- Expense Type & Related Module -->
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-dark">Expense Type <span class="text-danger">*</span></label>
                            <select name="expense_type" id="edit_expense_type" class="form-select form-select-sm rounded-3" required>
                                <option value="BROKERAGE">Brokerage Charges</option>
                                <option value="STT_TAXES">STT &amp; Statutory Taxes</option>
                                <option value="PLATFORM_MISC">Platform &amp; Misc Fees</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-dark">Related Module <span class="text-danger">*</span></label>
                            <select name="module" id="edit_expense_module" class="form-select form-select-sm rounded-3" required>
                                <option value="equity">Equities</option>
                                <option value="etf">Exchange Traded Funds (ETFs)</option>
                                <option value="bond">Bonds &amp; Fixed Income</option>
                                <option value="reit_invit">InvITs &amp; REITs</option>
                                <option value="mutual_fund">Mutual Funds</option>
                            </select>
                        </div>
                    </div>

                    <!-- Notes / Description -->
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-dark">Notes / Reference <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" name="notes" id="edit_expense_notes" class="form-control form-control-sm rounded-3">
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-4 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i> Update Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================================= -->
<!-- MODAL: DELETE CONFIRMATION                                              -->
<!-- ======================================================================= -->
<div class="modal fade" id="deleteExpenseModal" tabindex="-1" aria-labelledby="deleteExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-center p-4">
            <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3 mx-auto" style="width: 56px; height: 56px;">
                <i class="bi bi-exclamation-triangle fs-3"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Delete Expense?</h5>
            <p class="text-muted small mb-3" id="delete_expense_desc_text">
                Are you sure you want to permanently delete this expense transaction?
            </p>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="delete_expense_confirm_btn" class="btn btn-danger btn-sm rounded-3 px-3 fw-semibold">
                    <i class="bi bi-trash me-1"></i> Delete
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Client-side script for Modal Population -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit Expense modal binding
    const editModalEl = document.getElementById('editExpenseModal');
    const editModal = new bootstrap.Modal(editModalEl);

    document.querySelectorAll('.btn-edit-expense').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('edit_expense_id').value     = this.dataset.id;
            document.getElementById('edit_expense_date').value   = this.dataset.date;
            document.getElementById('edit_expense_amount').value = this.dataset.amount;
            document.getElementById('edit_expense_type').value   = this.dataset.type;
            document.getElementById('edit_expense_module').value = this.dataset.module;
            document.getElementById('edit_expense_notes').value  = this.dataset.notes || '';
            editModal.show();
        });
    });

    // Delete Expense modal binding
    const deleteModalEl = document.getElementById('deleteExpenseModal');
    const deleteModal = new bootstrap.Modal(deleteModalEl);
    const deleteConfirmBtn = document.getElementById('delete_expense_confirm_btn');
    const deleteDescText = document.getElementById('delete_expense_desc_text');

    document.querySelectorAll('.btn-delete-expense').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const desc = this.dataset.desc;
            deleteDescText.textContent = `Are you sure you want to delete expense "${desc}"? This action cannot be undone.`;
            deleteConfirmBtn.href = `<?= base_url('expenses/delete/') ?>/${id}`;
            deleteModal.show();
        });
    });
});
</script>

<?= $this->endSection() ?>

