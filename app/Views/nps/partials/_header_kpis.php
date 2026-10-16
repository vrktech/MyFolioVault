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
        <div class="dropdown">
            <button class="btn btn-light border shadow-sm btn-sm rounded-3 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Account Options">
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
        <button type="button" class="btn btn-light border shadow-sm btn-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#npsNavModal">
            <i class="bi bi-pencil-square me-1 text-secondary"></i>Manual NAVs
        </button>
        <a href="<?= base_url('nps/update-navs') ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold shadow-sm" id="btnAutoUpdateNav">
            <i class="bi bi-cloud-arrow-down me-1"></i>Auto-Update NAVs (npsnav.in)
        </a>
        <a href="<?= base_url('nps/export-holdings') ?>" class="btn btn-outline-success btn-sm rounded-3 px-3 fw-semibold shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
        </a>
        <button type="button" class="btn btn-warning btn-sm rounded-3 px-3 fw-semibold text-dark" data-bs-toggle="modal" data-bs-target="#npsTransactionModal">
            <i class="bi bi-plus-lg me-1"></i>Add Transaction
        </button>
    </div>
</div>

<!-- 5 Executive KPI Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Current Valuation -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Portfolio Valuation</div>
                    <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_current_value']) ?></div>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                        <i class="bi bi-shield-check text-warning me-1"></i>Tier 1 Corpus
                    </div>
                </div>
                <div class="bg-primary-subtle text-primary rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-shield-lock fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Invested Capital -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Net Invested Capital</div>
                    <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_invested']) ?></div>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">Contributions Basis</div>
                </div>
                <div class="bg-info-subtle text-info-emphasis rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-cash-coin fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Return / P&L -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-<?= $summary['total_pnl'] >= 0 ? 'success' : 'danger' ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Overall Return / P&L</div>
                    <div class="fs-5 fw-bold">
                        <?= format_pnl($summary['total_pnl'], $summary['overall_return_pct']) ?>
                    </div>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">Unrealized Gain</div>
                </div>
                <div class="bg-<?= $summary['total_pnl'] >= 0 ? 'success' : 'danger' ?>-subtle text-<?= $summary['total_pnl'] >= 0 ? 'success' : 'danger' ?> rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-graph-<?= $summary['total_pnl'] >= 0 ? 'up' : 'down' ?>-arrow fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quarterly Fee Units Deducted -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-danger">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Fee Units Deducted</div>
                    <div class="fs-5 fw-bold text-danger">
                        <?= number_format($summary['total_deducted_units'], 4) ?> <span class="fs-6 fw-normal">units</span>
                    </div>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                        &approx; <?= format_inr($summary['total_deducted_value']) ?> CRA/POP fees
                    </div>
                </div>
                <div class="bg-danger-subtle text-danger rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-receipt-cutoff fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Target Asset Allocation -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Target Allocation</div>
                    <div class="fs-6 fw-bold text-dark text-truncate mt-1">
                        E: <?= (int)$account['alloc_equity'] ?>% &bull; C: <?= (int)$account['alloc_corporate_debt'] ?>% &bull; G: <?= (int)$account['alloc_govt_bonds'] ?>%
                    </div>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">Active Choice</div>
                </div>
                <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-pie-chart fs-5"></i>
                </div>
            </div>
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
