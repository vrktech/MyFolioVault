<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-wallet2 text-primary me-2"></i>Portfolio Reports &amp; Analytics
            </h3>
            <p class="text-secondary small mb-0">
                Audited financial reporting, capital flow tracking, and tax accounting across all 6 investment modules.
            </p>
        </div>
    </div>

    <!-- Multipage Sub-Navigation -->
    <?= $this->include('reports/_nav') ?>

    <!-- Date Range Filter Toolbar -->
    <?= $this->include('reports/_date_filter') ?>

    <!-- 4 Hero KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Purchases -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-primary border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Purchases / Outlay</div>
                        <h4 class="fw-bold text-dark mb-0 mt-1"><?= format_inr($totals['purchase']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Capital deployed in new lots</div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2">
                        <i class="bi bi-cart-plus fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Sales / Redemptions -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-info border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Sales &amp; Redemptions</div>
                        <h4 class="fw-bold text-info-emphasis mb-0 mt-1"><?= format_inr($totals['sold']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Gross principal &amp; redemption value</div>
                    </div>
                    <div class="bg-info-subtle text-info-emphasis rounded-3 p-2">
                        <i class="bi bi-box-arrow-up-right fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Income -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-success border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Passive Income</div>
                        <h4 class="fw-bold text-success mb-0 mt-1">+<?= format_inr($totals['income']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Dividends, coupons &amp; distributions</div>
                    </div>
                    <div class="bg-success-subtle text-success rounded-3 p-2">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Expenses & Taxes -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-danger border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Expenses &amp; Friction</div>
                        <h4 class="fw-bold text-danger mb-0 mt-1"><?= format_inr($totals['expense']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Brokerage, STT &amp; TDS withheld</div>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded-3 p-2">
                        <i class="bi bi-receipt fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Capital Flow Summary Alert Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Net Portfolio Cash Flow (Selected Period)</span>
                    <div class="d-flex align-items-baseline gap-2 mt-1">
                        <h3 class="fw-bold mb-0 <?= $totals['net_flow'] >= 0 ? 'text-success' : 'text-primary' ?>">
                            <?= ($totals['net_flow'] >= 0 ? '+' : '') . format_inr($totals['net_flow']) ?>
                        </h3>
                        <span class="badge <?= $totals['net_flow'] >= 0 ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' ?> px-2.5 py-1">
                            <?= $totals['net_flow'] >= 0 ? 'Net Cash Inflow' : 'Net Capital Outlay (Invested)' ?>
                        </span>
                    </div>
                    <p class="text-secondary small mb-0 mt-1">
                        Calculated as <code>(Total Sold + Passive Income) &minus; (Total Purchases + Expenses &amp; TDS)</code>.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-md-end">
                        <div class="text-muted small">Total Recorded Activities</div>
                        <div class="fw-bold text-dark fs-5"><?= number_format($totals['activity']) ?> transactions &amp; payouts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module-by-Module Summary Table -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Module-wise Capital Activity Breakdown</h5>
                <small class="text-muted">Breakdown of purchases, sales, passive income, and transaction costs for all 6 modules</small>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill">
                6 Investment Modules
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th class="ps-4">Asset Class / Module</th>
                        <th class="text-end">Total Purchase (₹)</th>
                        <th class="text-end">Total Sold (₹)</th>
                        <th class="text-end">Total Income (₹)</th>
                        <th class="text-end">Total Expense (₹)</th>
                        <th class="text-end">Net Flow (₹)</th>
                        <th class="text-center">Activities</th>
                        <th class="text-center pe-4" style="width: 130px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $m): ?>
                        <tr>
                            <!-- Module -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="bg-<?= esc($m['color']) ?>-subtle text-<?= esc($m['color']) ?> rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                        <i class="<?= esc($m['icon']) ?> fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="fw-bold text-dark fs-6"><?= esc($m['name']) ?></span>
                                            <?php if ($m['key'] === 'equities' && !empty($equitiesSectors)): ?>
                                                <button class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target=".equities-sector-cf-row" aria-expanded="false" style="font-size: 0.65rem;" title="Toggle Equities Sector Breakdown">
                                                    <i class="bi bi-diagram-3 me-1"></i>Sectors (<?= count($equitiesSectors) ?>) <i class="bi bi-chevron-down ms-1" style="font-size: 0.55rem;"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;">
                                            <?= esc($m['badge']) ?>
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Total Purchase -->
                            <td class="text-end fw-semibold">
                                <?= format_inr($m['purchase']) ?>
                            </td>

                            <!-- Total Sold -->
                            <td class="text-end fw-semibold text-info-emphasis">
                                <?= format_inr($m['sold']) ?>
                            </td>

                            <!-- Total Income -->
                            <td class="text-end">
                                <?php if ($m['income'] > 0): ?>
                                    <div class="fw-bold text-success">+<?= format_inr($m['income']) ?></div>
                                    <div class="text-muted" style="font-size: 0.68rem;"><?= esc($m['income_desc']) ?></div>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                    <div class="text-muted" style="font-size: 0.65rem;"><?= esc($m['income_desc']) ?></div>
                                <?php endif; ?>
                            </td>

                            <!-- Total Expense -->
                            <td class="text-end text-danger">
                                <?= $m['expense'] > 0 ? format_inr($m['expense']) : '₹0.00' ?>
                            </td>

                            <!-- Net Flow -->
                            <td class="text-end fw-bold <?= $m['net_flow'] >= 0 ? 'text-success' : 'text-primary' ?>">
                                <?= ($m['net_flow'] >= 0 ? '+' : '') . format_inr($m['net_flow']) ?>
                            </td>

                            <!-- Activity Count -->
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">
                                    <?= number_format($m['activity']) ?>
                                </span>
                            </td>

                            <!-- Action -->
                            <td class="text-center pe-4">
                                <a href="<?= esc($m['ledger_url']) ?>" class="btn btn-light btn-sm border px-2.5 py-1 text-primary shadow-sm" title="View module trade ledger">
                                    <i class="bi bi-journal-text me-1"></i>Ledger
                                </a>
                            </td>
                        </tr>
                        <?php if ($m['key'] === 'equities' && !empty($equitiesSectors)): ?>
                            <?php foreach ($equitiesSectors as $sec): ?>
                                <tr class="collapse equities-sector-cf-row bg-light bg-opacity-75 border-start border-primary border-3">
                                    <td class="ps-5">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-arrow-return-right text-primary opacity-75"></i>
                                            <div>
                                                <span class="fw-semibold text-dark"><?= esc($sec['name']) ?></span>
                                                <span class="badge bg-secondary-subtle text-secondary ms-1" style="font-size: 0.62rem;">Sector</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end text-muted small"><?= format_inr($sec['purchase']) ?></td>
                                    <td class="text-end text-info-emphasis small"><?= format_inr($sec['sold']) ?></td>
                                    <td class="text-end text-success small"><?= $sec['income'] > 0 ? '+' . format_inr($sec['income']) : '—' ?></td>
                                    <td class="text-end text-danger small"><?= $sec['expense'] > 0 ? format_inr($sec['expense']) : '₹0.00' ?></td>
                                    <td class="text-end fw-semibold <?= $sec['net_flow'] >= 0 ? 'text-success' : 'text-primary' ?> small">
                                        <?= ($sec['net_flow'] >= 0 ? '+' : '') . format_inr($sec['net_flow']) ?>
                                    </td>
                                    <td class="text-center text-muted small"><?= number_format($sec['activity']) ?></td>
                                    <td class="text-center pe-4">
                                        <span class="text-muted" style="font-size: 0.70rem;">Sector Subtotal</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-bold border-top-2" style="font-size: 0.85rem;">
                    <tr>
                        <td class="ps-4">CONSOLIDATED PORTFOLIO TOTAL</td>
                        <td class="text-end text-dark"><?= format_inr($totals['purchase']) ?></td>
                        <td class="text-end text-info-emphasis"><?= format_inr($totals['sold']) ?></td>
                        <td class="text-end text-success">+<?= format_inr($totals['income']) ?></td>
                        <td class="text-end text-danger"><?= format_inr($totals['expense']) ?></td>
                        <td class="text-end <?= $totals['net_flow'] >= 0 ? 'text-success' : 'text-primary' ?> fs-6">
                            <?= ($totals['net_flow'] >= 0 ? '+' : '') . format_inr($totals['net_flow']) ?>
                        </td>
                        <td class="text-center"><?= number_format($totals['activity']) ?></td>
                        <td class="pe-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Income & Expense Decomposition Cards -->
    <div class="row g-4 mb-4">
        <!-- Income Breakdown -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-pie-chart text-success me-2"></i>Passive Income Sources
                    </h6>
                    <a href="<?= base_url('reports/income?fy=' . ($range['key'] ?? 'CURRENT_FY')) ?>" class="small text-decoration-none text-primary">
                        Detailed Ledger <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary p-1.5 rounded-circle"><i class="bi bi-building"></i></span>
                            <span class="text-secondary">Equities Cash Dividends</span>
                        </div>
                        <span class="fw-bold text-dark"><?= format_inr($incomeBreakdown['dividends']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark-subtle text-dark p-1.5 rounded-circle"><i class="bi bi-bank"></i></span>
                            <span class="text-secondary">Bond Coupon Interest</span>
                        </div>
                        <span class="fw-bold text-dark"><?= format_inr($incomeBreakdown['bond_coupons']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info-subtle text-info p-1.5 rounded-circle"><i class="bi bi-buildings"></i></span>
                            <span class="text-secondary">InvITs &amp; REITs Distributions</span>
                        </div>
                        <span class="fw-bold text-dark"><?= format_inr($incomeBreakdown['distributions']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 fw-bold text-success fs-6">
                        <span>Total Passive Income</span>
                        <span>+<?= format_inr($totals['income']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Breakdown -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-receipt-cutoff text-danger me-2"></i>Friction &amp; Statutory Deductions
                    </h6>
                    <a href="<?= base_url('reports/expenses?fy=' . ($range['key'] ?? 'CURRENT_FY')) ?>" class="small text-decoration-none text-primary">
                        Detailed Audit <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger-subtle text-danger p-1.5 rounded-circle"><i class="bi bi-percent"></i></span>
                            <span class="text-secondary">Brokerage &amp; STT (Turnover Taxes)</span>
                        </div>
                        <span class="fw-bold text-dark"><?= format_inr($expenseBreakdown['brokerage_stt']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning-subtle text-warning-emphasis p-1.5 rounded-circle"><i class="bi bi-shield-x"></i></span>
                            <span class="text-secondary">TDS Withheld at Source (Tax Credits)</span>
                        </div>
                        <span class="fw-bold text-dark"><?= format_inr($expenseBreakdown['tds_deducted']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary-subtle text-secondary p-1.5 rounded-circle"><i class="bi bi-gear"></i></span>
                            <span class="text-secondary">Platform, Stamp Duty &amp; NPS Fees</span>
                        </div>
                        <span class="fw-bold text-dark"><?= format_inr($expenseBreakdown['charges_fees']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 fw-bold text-danger fs-6">
                        <span>Total Statutory &amp; Platform Expenses</span>
                        <span><?= format_inr($totals['expense']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

