<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header & Top Action Buttons -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">NPS (Tier 1)</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold text-dark mb-0">NPS Tier 1 Portfolio</h3>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 small">
                Tier 1 Retirement
            </span>
        </div>
        <small class="text-muted">
            PRAN: <strong class="text-dark font-monospace"><?= esc($account['pran']) ?></strong> &bull; 
            PFM: <strong class="text-dark"><?= esc($account['pfm_name']) ?></strong> &bull; 
            Subscriber: <span class="text-dark"><?= esc($account['subscriber_name']) ?></span>
            <?php if (!empty($account['nav_last_updated'])): ?>
                &bull; <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">
                    <i class="bi bi-clock-history me-1 text-primary"></i>NAV updated: <?= date('d-M-Y', strtotime($account['nav_last_updated'])) ?>
                </span>
            <?php endif; ?>
        </small>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="<?= base_url('nps/update-navs') ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold shadow-sm" id="btnAutoUpdateNav">
            <i class="bi bi-cloud-arrow-down me-1"></i>Auto-Update NAVs (npsnav.in)
        </a>
        <button type="button" class="btn btn-light border shadow-sm btn-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#npsNavModal">
            <i class="bi bi-pencil-square me-1 text-secondary"></i>Manual NAVs
        </button>
        <button type="button" class="btn btn-warning btn-sm rounded-3 px-3 fw-semibold text-dark" data-bs-toggle="modal" data-bs-target="#npsTransactionModal">
            <i class="bi bi-plus-lg me-1"></i>Add Transaction
        </button>
        <div class="dropdown">
            <button class="btn btn-light border shadow-sm btn-sm rounded-3 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li>
                    <button class="dropdown-item small open-edit-nps-account"
                            data-id="<?= $account['id'] ?>"
                            data-pran="<?= esc($account['pran']) ?>"
                            data-name="<?= esc($account['subscriber_name']) ?>"
                            data-pfm="<?= esc($account['pfm_name']) ?>"
                            data-choice="<?= esc($account['investment_choice']) ?>"
                            data-e="<?= (float)$account['alloc_equity'] ?>"
                            data-c="<?= (float)$account['alloc_corporate_debt'] ?>"
                            data-g="<?= (float)$account['alloc_govt_bonds'] ?>"
                            data-a="<?= (float)$account['alloc_alternative'] ?>"
                            data-code-e="<?= esc($account['scheme_code_equity'] ?? '') ?>"
                            data-code-c="<?= esc($account['scheme_code_corporate_debt'] ?? '') ?>"
                            data-code-g="<?= esc($account['scheme_code_govt_bonds'] ?? '') ?>"
                            data-code-a="<?= esc($account['scheme_code_alternative'] ?? '') ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#editNpsAccountModal">
                        <i class="bi bi-pencil me-2 text-primary"></i>Edit Account &amp; Allocation
                    </button>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger small" 
                       href="<?= base_url('nps/delete/' . $account['id']) ?>" 
                       onclick="return confirm('Delete this NPS Tier 1 account and all its transaction history?');">
                        <i class="bi bi-trash me-2"></i>Delete PRAN Account
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- 5 Executive KPI Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Current Valuation -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Portfolio Valuation</div>
            <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_current_value']) ?></div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                <i class="bi bi-shield-check text-warning me-1"></i>Tier 1 Corpus
            </div>
        </div>
    </div>

    <!-- Total Invested Capital -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Net Invested Capital</div>
            <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_invested']) ?></div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Contributions Basis</div>
        </div>
    </div>

    <!-- Total Return / P&L -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Overall Return / P&L</div>
            <div class="fs-5 fw-bold">
                <?= format_pnl($summary['total_pnl'], $summary['overall_return_pct']) ?>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Unrealized Gain</div>
        </div>
    </div>

    <!-- Quarterly Fee Units Deducted -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Fee Units Deducted</div>
            <div class="fs-5 fw-bold text-secondary">
                <?= number_format($summary['total_deducted_units'], 4) ?> <span class="fs-6 fw-normal">units</span>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                &approx; <?= format_inr($summary['total_deducted_value']) ?> CRA/POP fees
            </div>
        </div>
    </div>

    <!-- Target Asset Allocation -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Target Allocation</div>
            <div class="fs-6 fw-bold text-dark text-truncate mt-1">
                E: <?= (int)$account['alloc_equity'] ?>% &bull; C: <?= (int)$account['alloc_corporate_debt'] ?>% &bull; G: <?= (int)$account['alloc_govt_bonds'] ?>%
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Active Choice</div>
        </div>
    </div>
</div>

<!-- Asset Allocation Distribution Progress Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="small fw-semibold text-secondary">Current Scheme Distribution (Actual vs Target)</span>
        <span class="small text-muted">Total Valuation: <?= format_inr($summary['total_current_value']) ?></span>
    </div>
    <div class="progress rounded-3" style="height: 14px;">
        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $schemes['SCHEME_E']['actual_pct'] ?>%;" title="Scheme E: <?= number_format($schemes['SCHEME_E']['actual_pct'], 1) ?>%"></div>
        <div class="progress-bar bg-info" role="progressbar" style="width: <?= $schemes['SCHEME_C']['actual_pct'] ?>%;" title="Scheme C: <?= number_format($schemes['SCHEME_C']['actual_pct'], 1) ?>%"></div>
        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $schemes['SCHEME_G']['actual_pct'] ?>%;" title="Scheme G: <?= number_format($schemes['SCHEME_G']['actual_pct'], 1) ?>%"></div>
        <?php if ($schemes['SCHEME_A']['actual_pct'] > 0): ?>
            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $schemes['SCHEME_A']['actual_pct'] ?>%;" title="Scheme A: <?= number_format($schemes['SCHEME_A']['actual_pct'], 1) ?>%"></div>
        <?php endif; ?>
    </div>
    <div class="d-flex justify-content-between flex-wrap gap-2 mt-2" style="font-size: 0.78rem;">
        <div>
            <span class="badge bg-primary me-1">&nbsp;</span>
            <strong>Scheme E (Equity):</strong> <?= number_format($schemes['SCHEME_E']['actual_pct'], 1) ?>% (Target: <?= (int)$account['alloc_equity'] ?>%)
        </div>
        <div>
            <span class="badge bg-info me-1">&nbsp;</span>
            <strong>Scheme C (Corp Debt):</strong> <?= number_format($schemes['SCHEME_C']['actual_pct'], 1) ?>% (Target: <?= (int)$account['alloc_corporate_debt'] ?>%)
        </div>
        <div>
            <span class="badge bg-success me-1">&nbsp;</span>
            <strong>Scheme G (Govt Bonds):</strong> <?= number_format($schemes['SCHEME_G']['actual_pct'], 1) ?>% (Target: <?= (int)$account['alloc_govt_bonds'] ?>%)
        </div>
        <?php if ($account['alloc_alternative'] > 0 || $schemes['SCHEME_A']['active_units'] > 0): ?>
            <div>
                <span class="badge bg-warning me-1">&nbsp;</span>
                <strong>Scheme A (Alt):</strong> <?= number_format($schemes['SCHEME_A']['actual_pct'], 1) ?>% (Target: <?= (int)$account['alloc_alternative'] ?>%)
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Main Tabs Section -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-3 px-4 rounded-top-4">
        <ul class="nav nav-tabs card-header-tabs border-0" id="npsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="schemes-tab" data-bs-toggle="tab" data-bs-target="#schemes" type="button" role="tab">
                    <i class="bi bi-pie-chart me-1 text-primary"></i>Scheme Portfolios (E, C, G, A)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="contributions-tab" data-bs-toggle="tab" data-bs-target="#contributions" type="button" role="tab">
                    <i class="bi bi-clock-history me-1"></i>Contributions Ledger (<?= count($contributions) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="deductions-tab" data-bs-toggle="tab" data-bs-target="#deductions" type="button" role="tab">
                    <i class="bi bi-receipt-cutoff me-1 text-danger"></i>Quarterly Fee Deductions (<?= count($deductions) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="npsTabContent">
            
            <!-- TAB 1: SCHEME PORTFOLIOS -->
            <div class="tab-pane fade show active p-3" id="schemes" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>Scheme Name</th>
                                <th class="text-center">Target Split</th>
                                <th class="text-end">Units Held (Net)</th>
                                <th class="text-end">Avg Cost NAV (₹)</th>
                                <th class="text-end">Latest NAV (₹)</th>
                                <th class="text-end">Invested (₹)</th>
                                <th class="text-end">Valuation (₹)</th>
                                <th class="text-end">Return / Gain</th>
                                <th class="text-end">Fee Units Deducted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schemes as $s): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge <?= $s['badge'] ?> border px-2 py-1 me-2 small">
                                                <?= esc($s['code']) ?>
                                            </span>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0">
                                                    <?= esc($s['name']) ?>
                                                    <?php if (!empty($s['scheme_code'])): ?>
                                                        <span class="badge bg-light text-secondary border font-monospace ms-1" style="font-size: 0.68rem;" title="npsnav.in Scheme Code">
                                                            <i class="bi bi-upc-scan me-1 text-primary"></i><?= esc($s['scheme_code']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($s['nav_date'])): ?>
                                                    <small class="text-muted" style="font-size: 0.72rem;">
                                                        NAV date: <?= date('d-M-Y', strtotime($s['nav_date'])) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                            <?= $s['target_pct'] ?>%
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">
                                        <?= number_format($s['active_units'], 4) ?>
                                    </td>
                                    <td class="text-end text-muted small">
                                        <?= format_inr($s['avg_nav']) ?>
                                    </td>
                                    <td class="text-end fw-semibold text-dark">
                                        <?= format_inr($s['current_nav']) ?>
                                    </td>
                                    <td class="text-end small text-muted">
                                        <?= format_inr($s['invested_amount']) ?>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        <?= format_inr($s['current_value']) ?>
                                    </td>
                                    <td class="text-end">
                                        <?= format_pnl($s['unrealized_pnl'], $s['return_pct']) ?>
                                    </td>
                                    <td class="text-end text-secondary small">
                                        <?php if ($s['deducted_units'] > 0): ?>
                                            <span class="text-danger">-<?= number_format($s['deducted_units'], 4) ?></span>
                                            <div class="text-muted" style="font-size: 0.7rem;">&approx; <?= format_inr($s['deducted_value']) ?></div>
                                        <?php else: ?>
                                            0.0000
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: CONTRIBUTIONS LEDGER -->
            <div class="tab-pane fade p-3" id="contributions" role="tabpanel">
                <?php if (empty($contributions)): ?>
                    <p class="text-muted text-center py-4">No contributions recorded yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="npsContributionsTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Ack / Receipt No.</th>
                                    <th class="text-end">Gross Amount (₹)</th>
                                    <th class="text-end">Cash Charges</th>
                                    <th class="text-end">Net Invested (₹)</th>
                                    <th>Scheme Allotment Breakdown</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contributions as $c): ?>
                                    <?php
                                    $eUnits = 0; $eNav = 0; $eAmt = 0;
                                    $cUnits = 0; $cNav = 0; $cAmt = 0;
                                    $gUnits = 0; $gNav = 0; $gAmt = 0;
                                    $aUnits = 0; $aNav = 0; $aAmt = 0;
                                    if (!empty($c['allotments'])) {
                                        foreach ($c['allotments'] as $alt) {
                                            if ($alt['scheme_type'] === 'SCHEME_E') { $eUnits = $alt['units']; $eNav = $alt['nav']; $eAmt = $alt['allocated_amount']; }
                                            if ($alt['scheme_type'] === 'SCHEME_C') { $cUnits = $alt['units']; $cNav = $alt['nav']; $cAmt = $alt['allocated_amount']; }
                                            if ($alt['scheme_type'] === 'SCHEME_G') { $gUnits = $alt['units']; $gNav = $alt['nav']; $gAmt = $alt['allocated_amount']; }
                                            if ($alt['scheme_type'] === 'SCHEME_A') { $aUnits = $alt['units']; $aNav = $alt['nav']; $aAmt = $alt['allocated_amount']; }
                                        }
                                    }
                                    ?>
                                    <tr class="nps-contrib-row">
                                        <td><?= date('d-M-Y', strtotime($c['transaction_date'])) ?></td>
                                        <td>
                                            <?php if ($c['contribution_type'] === 'VOLUNTARY'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Voluntary</span>
                                            <?php elseif ($c['contribution_type'] === 'EMPLOYER'): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Employer</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><?= esc($c['contribution_type']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="font-monospace text-muted"><?= esc($c['acknowledgement_no'] ?: '—') ?></td>
                                        <td class="text-end fw-semibold"><?= format_inr($c['gross_amount']) ?></td>
                                        <td class="text-end text-muted"><?= format_inr($c['optional_cash_charges']) ?></td>
                                        <td class="text-end fw-bold text-dark"><?= format_inr($c['net_amount']) ?></td>
                                        <td>
                                            <?php if (!empty($c['allotments'])): ?>
                                                <div class="d-flex flex-column gap-1" style="font-size: 0.75rem;">
                                                    <?php foreach ($c['allotments'] as $alt): ?>
                                                        <div>
                                                            <span class="fw-semibold"><?= substr($alt['scheme_type'], -1) ?>:</span>
                                                            <?= number_format($alt['units'], 4) ?> units @ ₹<?= number_format($alt['nav'], 4) ?>
                                                            <span class="text-muted">(<?= format_inr($alt['allocated_amount']) ?>)</span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-secondary"><?= esc($c['notes'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary py-0 px-1.5 open-edit-nps-contrib"
                                                     data-bs-toggle="modal" 
                                                    data-bs-target="#editNpsContribModal"
                                                    data-id="<?= $c['id'] ?>"
                                                    data-date="<?= esc($c['transaction_date']) ?>"
                                                    data-type="<?= esc($c['contribution_type']) ?>"
                                                    data-gross="<?= (float)$c['gross_amount'] ?>"
                                                    data-charges="<?= (float)$c['optional_cash_charges'] ?>"
                                                    data-ack="<?= esc($c['acknowledgement_no'] ?? '') ?>"
                                                    data-notes="<?= esc($c['notes'] ?? '') ?>"
                                                    data-units-e="<?= (float)$eUnits ?>" data-nav-e="<?= (float)$eNav ?>" data-amt-e="<?= (float)$eAmt ?>"
                                                    data-units-c="<?= (float)$cUnits ?>" data-nav-c="<?= (float)$cNav ?>" data-amt-c="<?= (float)$cAmt ?>"
                                                    data-units-g="<?= (float)$gUnits ?>" data-nav-g="<?= (float)$gNav ?>" data-amt-g="<?= (float)$gAmt ?>"
                                                    data-units-a="<?= (float)$aUnits ?>" data-nav-a="<?= (float)$aNav ?>" data-amt-a="<?= (float)$aAmt ?>"
                                                    title="Edit Contribution">
                                                <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <a href="<?= base_url('nps/delete-transaction/' . $c['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger py-0 px-1.5 ms-1"
                                               onclick="return confirm('Delete this contribution transaction?');"
                                               title="Delete Contribution">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'npsContrib']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 3: QUARTERLY FEE DEDUCTIONS -->
            <div class="tab-pane fade p-3" id="deductions" role="tabpanel">
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center rounded-3 small">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Quarterly CRA & POP Service Charge Mechanism:</strong> Under PFRDA rules, account maintenance and turnover charges are debited directly from your scheme unit holdings at the prevailing quarter-end NAV rather than upfront cash.
                    </div>
                </div>

                <?php if (empty($deductions)): ?>
                    <p class="text-muted text-center py-4">No quarterly unit deductions recorded yet. When CRA deducts maintenance units quarterly, record them here.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="npsDeductionsTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Deduction Date</th>
                                    <th>Quarter / Description</th>
                                    <th>Scheme Units Debited</th>
                                    <th>Ack / Ref No.</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deductions as $d): ?>
                                    <?php
                                    $deUnits = 0; $deNav = 0;
                                    $dcUnits = 0; $dcNav = 0;
                                    $dgUnits = 0; $dgNav = 0;
                                    $daUnits = 0; $daNav = 0;
                                    if (!empty($d['deductions'])) {
                                        foreach ($d['deductions'] as $du) {
                                            if ($du['scheme_type'] === 'SCHEME_E') { $deUnits = abs($du['units']); $deNav = $du['nav']; }
                                            if ($du['scheme_type'] === 'SCHEME_C') { $dcUnits = abs($du['units']); $dcNav = $du['nav']; }
                                            if ($du['scheme_type'] === 'SCHEME_G') { $dgUnits = abs($du['units']); $dgNav = $du['nav']; }
                                            if ($du['scheme_type'] === 'SCHEME_A') { $daUnits = abs($du['units']); $daNav = $du['nav']; }
                                        }
                                    }
                                    ?>
                                    <tr class="nps-deduct-row">
                                        <td><?= date('d-M-Y', strtotime($d['transaction_date'])) ?></td>
                                        <td><strong><?= esc($d['notes'] ?: 'Quarterly service charges') ?></strong></td>
                                        <td>
                                            <?php if (!empty($d['deductions'])): ?>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <?php foreach ($d['deductions'] as $du): ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                            <?= substr($du['scheme_type'], -1) ?>: -<?= number_format(abs($du['units']), 4) ?> units @ ₹<?= number_format($du['nav'], 4) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="font-monospace text-muted"><?= esc($d['acknowledgement_no'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary py-0 px-1.5 open-edit-nps-deduct"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editNpsDeductModal"
                                                    data-id="<?= $d['id'] ?>"
                                                    data-date="<?= esc($d['transaction_date']) ?>"
                                                    data-notes="<?= esc($d['notes'] ?? '') ?>"
                                                    data-ack="<?= esc($d['acknowledgement_no'] ?? '') ?>"
                                                    data-deduct-units-e="<?= (float)$deUnits ?>" data-deduct-nav-e="<?= (float)$deNav ?>"
                                                    data-deduct-units-c="<?= (float)$dcUnits ?>" data-deduct-nav-c="<?= (float)$dcNav ?>"
                                                    data-deduct-units-g="<?= (float)$dgUnits ?>" data-deduct-nav-g="<?= (float)$dgNav ?>"
                                                    data-deduct-units-a="<?= (float)$daUnits ?>" data-deduct-nav-a="<?= (float)$daNav ?>"
                                                    title="Edit Fee Deduction">
                                                <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <a href="<?= base_url('nps/delete-transaction/' . $d['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger py-0 px-1.5 ms-1"
                                               onclick="return confirm('Delete this fee deduction record?');"
                                               title="Delete Deduction">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'npsDeduct']) ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hook Edit Account button
    document.querySelectorAll('.open-edit-nps-account').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editNpsAccountId').value = this.dataset.id;
            document.getElementById('editNpsSubName').value = this.dataset.name;
            document.getElementById('editNpsPfmName').value = this.dataset.pfm;
            document.getElementById('editNpsChoice').value = this.dataset.choice;
            document.getElementById('editNpsAllocE').value = this.dataset.e;
            document.getElementById('editNpsAllocC').value = this.dataset.c;
            document.getElementById('editNpsAllocG').value = this.dataset.g;
            document.getElementById('editNpsAllocA').value = this.dataset.a;
            document.getElementById('editNpsCodeE').value = this.dataset.codeE || '';
            document.getElementById('editNpsCodeC').value = this.dataset.codeC || '';
            document.getElementById('editNpsCodeG').value = this.dataset.codeG || '';
            document.getElementById('editNpsCodeA').value = this.dataset.codeA || '';
        });
    });

    // Hook Edit Contribution buttons
    document.querySelectorAll('.open-edit-nps-contrib').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editNpsContribId').value = this.dataset.id;
            document.getElementById('editNpsConDate').value = this.dataset.date;
            document.getElementById('editNpsConType').value = this.dataset.type;
            document.getElementById('editNpsConGross').value = parseFloat(this.dataset.gross).toFixed(2);
            document.getElementById('editNpsConCharges').value = parseFloat(this.dataset.charges || 0).toFixed(2);
            document.getElementById('editNpsConAck').value = this.dataset.ack || '';
            document.getElementById('editNpsConNotes').value = this.dataset.notes || '';

            document.getElementById('editNpsAmtE').value = parseFloat(this.dataset.amtE || 0).toFixed(2);
            document.getElementById('editNpsNavE').value = parseFloat(this.dataset.navE || 0).toFixed(4);
            document.getElementById('editNpsUnitsE').value = parseFloat(this.dataset.unitsE || 0).toFixed(4);

            document.getElementById('editNpsAmtC').value = parseFloat(this.dataset.amtC || 0).toFixed(2);
            document.getElementById('editNpsNavC').value = parseFloat(this.dataset.navC || 0).toFixed(4);
            document.getElementById('editNpsUnitsC').value = parseFloat(this.dataset.unitsC || 0).toFixed(4);

            document.getElementById('editNpsAmtG').value = parseFloat(this.dataset.amtG || 0).toFixed(2);
            document.getElementById('editNpsNavG').value = parseFloat(this.dataset.navG || 0).toFixed(4);
            document.getElementById('editNpsUnitsG').value = parseFloat(this.dataset.unitsG || 0).toFixed(4);

            document.getElementById('editNpsAmtA').value = parseFloat(this.dataset.amtA || 0).toFixed(2);
            document.getElementById('editNpsNavA').value = parseFloat(this.dataset.navA || 0).toFixed(4);
            document.getElementById('editNpsUnitsA').value = parseFloat(this.dataset.unitsA || 0).toFixed(4);
        });
    });

    // Hook Edit Fee Deduction buttons
    document.querySelectorAll('.open-edit-nps-deduct').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editNpsDeductId').value = this.dataset.id;
            document.getElementById('editNpsDedDate').value = this.dataset.date;
            document.getElementById('editNpsDedNotes').value = this.dataset.notes || '';
            document.getElementById('editNpsDedAck').value = this.dataset.ack || '';

            document.getElementById('editNpsDedUnitsE').value = parseFloat(this.dataset.deductUnitsE || 0).toFixed(4);
            document.getElementById('editNpsDedNavE').value = parseFloat(this.dataset.deductNavE || 0).toFixed(4);

            document.getElementById('editNpsDedUnitsC').value = parseFloat(this.dataset.deductUnitsC || 0).toFixed(4);
            document.getElementById('editNpsDedNavC').value = parseFloat(this.dataset.deductNavC || 0).toFixed(4);

            document.getElementById('editNpsDedUnitsG').value = parseFloat(this.dataset.deductUnitsG || 0).toFixed(4);
            document.getElementById('editNpsDedNavG').value = parseFloat(this.dataset.deductNavG || 0).toFixed(4);

            document.getElementById('editNpsDedUnitsA').value = parseFloat(this.dataset.deductUnitsA || 0).toFixed(4);
            document.getElementById('editNpsDedNavA').value = parseFloat(this.dataset.deductNavA || 0).toFixed(4);
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
            pContrib.classList.remove('d-none');
            pDeduct.classList.add('d-none');
            setInputsDisabled(pContrib, false);
            setInputsDisabled(pDeduct, true);
            submitBtn.className = 'btn btn-success btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Confirm Contribution';
        } else if (type === 'UNIT_DEDUCTION') {
            pContrib.classList.add('d-none');
            pDeduct.classList.remove('d-none');
            setInputsDisabled(pContrib, true);
            setInputsDisabled(pDeduct, false);
            submitBtn.className = 'btn btn-danger btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Record Unit Deduction';
        }
    }

    function setInputsDisabled(container, disabled) {
        container.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    // 2. Modal Auto-Split Button
    const btnAutoSplit = document.getElementById('btnModalAutoSplit');
    const modalGross = document.getElementById('modalGrossAmt');

    const allocE = <?= (float)$account['alloc_equity'] ?> / 100;
    const allocC = <?= (float)$account['alloc_corporate_debt'] ?> / 100;
    const allocG = <?= (float)$account['alloc_govt_bonds'] ?> / 100;
    const allocA = <?= (float)$account['alloc_alternative'] ?> / 100;

    btnAutoSplit.addEventListener('click', function() {
        const gross = parseFloat(modalGross.value) || 0;
        if (gross <= 0) {
            alert('Please enter Gross Amount first.');
            modalGross.focus();
            return;
        }

        document.getElementById('mAmtE').value = (gross * allocE).toFixed(2);
        document.getElementById('mAmtC').value = (gross * allocC).toFixed(2);
        document.getElementById('mAmtG').value = (gross * allocG).toFixed(2);
        document.getElementById('mAmtA').value = (gross * allocA).toFixed(2);

        ['E', 'C', 'G', 'A'].forEach(s => {
            calcModalUnits(s);
        });
    });

    function calcModalUnits(scheme) {
        const amt = parseFloat(document.getElementById('mAmt' + scheme).value) || 0;
        const nav = parseFloat(document.getElementById('mNav' + scheme).value) || 0;
        const unitsInput = document.getElementById('mUnits' + scheme);

        if (amt > 0 && nav > 0) {
            unitsInput.value = (amt / nav).toFixed(4);
        }
    }

    ['E', 'C', 'G', 'A'].forEach(s => {
        const navEl = document.getElementById('mNav' + s);
        const amtEl = document.getElementById('mAmt' + s);
        const unitsEl = document.getElementById('mUnits' + s);

        navEl.addEventListener('input', () => calcModalUnits(s));
        amtEl.addEventListener('input', () => calcModalUnits(s));

        unitsEl.addEventListener('input', function() {
            const u = parseFloat(this.value) || 0;
            const nav = parseFloat(navEl.value) || 0;
            if (u > 0 && nav > 0) {
                amtEl.value = (u * nav).toFixed(2);
            }
        });
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
</script>

<?= $this->endSection() ?>

