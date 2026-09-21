<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-cash-coin text-success me-2"></i>Passive Income &amp; Distribution Report
            </h3>
            <p class="text-secondary small mb-0">
                Complete accounting of non-dilutive passive cash income: Equities dividends, Bond coupon interest, and REIT/InvIT distributions.
            </p>
        </div>
    </div>

    <!-- Multipage Sub-Navigation -->
    <?= $this->include('reports/_nav') ?>

    <!-- Date Range Filter Toolbar -->
    <?= $this->include('reports/_date_filter') ?>

    <!-- 4 Hero Income KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Gross Income -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-success border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Gross Income</div>
                        <h4 class="fw-bold text-success mb-0 mt-1"><?= format_inr($grandTotals['gross']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Gross earnings before tax deduction</div>
                    </div>
                    <div class="bg-success-subtle text-success rounded-3 p-2">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. TDS Withheld -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-warning border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">TDS Deducted at Source</div>
                        <h4 class="fw-bold text-warning-emphasis mb-0 mt-1"><?= format_inr($grandTotals['tds']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Withheld tax credit (Form 26AS/AIS)</div>
                    </div>
                    <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-2">
                        <i class="bi bi-shield-x fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Net Cash Received -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-primary border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Net Cash Received</div>
                        <h4 class="fw-bold text-primary mb-0 mt-1"><?= format_inr($grandTotals['net']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Directly credited to bank account</div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2">
                        <i class="bi bi-bank fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Payouts Frequency -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-info border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Income Distributions</div>
                        <h4 class="fw-bold text-info-emphasis mb-0 mt-1"><?= number_format($grandTotals['payouts']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Total cash distribution events</div>
                    </div>
                    <div class="bg-info-subtle text-info-emphasis rounded-3 p-2">
                        <i class="bi bi-calendar2-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Income Categories Summary Breakdown Cards -->
    <div class="row g-3 mb-4">
        <!-- Equities -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-top border-primary border-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-dark">
                        <i class="bi bi-graph-up-arrow text-primary me-1.5"></i>Equities Dividends
                    </span>
                    <span class="badge bg-primary-subtle text-primary"><?= $eqTotals['count'] ?> payouts</span>
                </div>
                <div class="fs-5 fw-bold text-dark"><?= format_inr($eqTotals['gross']) ?></div>
                <div class="text-muted small mt-1">
                    TDS: <?= format_inr($eqTotals['tds']) ?> &bull; Net: <strong class="text-success"><?= format_inr($eqTotals['net']) ?></strong>
                </div>
            </div>
        </div>

        <!-- REITs/InvITs -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-top border-info border-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-dark">
                        <i class="bi bi-buildings text-info me-1.5"></i>InvITs &amp; REITs Distributions
                    </span>
                    <span class="badge bg-info-subtle text-info-emphasis"><?= $reitTotals['count'] ?> payouts</span>
                </div>
                <div class="fs-5 fw-bold text-dark"><?= format_inr($reitTotals['gross']) ?></div>
                <div class="text-muted small mt-1">
                    TDS: <?= format_inr($reitTotals['tds']) ?> &bull; Net: <strong class="text-success"><?= format_inr($reitTotals['net']) ?></strong>
                </div>
            </div>
        </div>

        <!-- Bonds -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 border-top border-dark border-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-dark">
                        <i class="bi bi-bank text-dark me-1.5"></i>Bond Coupon Interest
                    </span>
                    <span class="badge bg-dark-subtle text-dark"><?= $bondTotals['count'] ?> payouts</span>
                </div>
                <div class="fs-5 fw-bold text-dark"><?= format_inr($bondTotals['gross']) ?></div>
                <div class="text-muted small mt-1">
                    TDS: <?= format_inr($bondTotals['tds']) ?> &bull; Net: <strong class="text-success"><?= format_inr($bondTotals['net']) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: Equities Dividends Ledger -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-graph-up-arrow text-primary me-2"></i>Equities Cash Dividends Ledger (<?= count($eqDividends) ?> Payouts)
            </h6>
            <a href="<?= base_url('equities?tab=dividends') ?>" class="btn btn-sm btn-light border px-2.5 py-1 text-primary shadow-sm">
                Equities Dividends <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th class="ps-4">Payout Date</th>
                        <th>Stock / Symbol</th>
                        <th class="text-end">Shares Held</th>
                        <th class="text-end">DPS (₹)</th>
                        <th class="text-end">Gross Dividend (₹)</th>
                        <th class="text-end">TDS (₹)</th>
                        <th class="text-end pe-4">Net Received (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($eqDividends)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No equity dividends received in this period.</td></tr>
                    <?php else: ?>
                        <?php foreach ($eqDividends as $ed): ?>
                            <tr>
                                <td class="ps-4"><?= date('d-M-Y', strtotime($ed['dividend_date'])) ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($ed['symbol']) ?></div>
                                    <div class="text-muted" style="font-size: 0.72rem;"><?= esc($ed['company_name'] ?? '') ?></div>
                                </td>
                                <td class="text-end"><?= number_format($ed['shares_held']) ?></td>
                                <td class="text-end text-muted">₹<?= number_format($ed['amount_per_share'], 2) ?></td>
                                <td class="text-end fw-semibold text-dark"><?= format_inr($ed['total_amount']) ?></td>
                                <td class="text-end text-danger"><?= format_inr($ed['tds_deducted']) ?></td>
                                <td class="text-end pe-4 fw-bold text-success"><?= format_inr($ed['total_amount'] - $ed['tds_deducted']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($eqDividends)): ?>
                    <tfoot class="table-light fw-bold border-top-2" style="font-size: 0.8rem;">
                        <tr>
                            <td class="ps-4" colspan="4">TOTAL EQUITIES DIVIDENDS</td>
                            <td class="text-end text-dark"><?= format_inr($eqTotals['gross']) ?></td>
                            <td class="text-end text-danger"><?= format_inr($eqTotals['tds']) ?></td>
                            <td class="text-end pe-4 text-success"><?= format_inr($eqTotals['net']) ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <!-- Section 2: InvITs & REITs Distributions Ledger -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-buildings text-info me-2"></i>InvITs &amp; REITs Distributions Ledger (<?= count($reitDists) ?> Payouts)
            </h6>
            <a href="<?= base_url('reits-invits?tab=distributions') ?>" class="btn btn-sm btn-light border px-2.5 py-1 text-primary shadow-sm">
                Trust Distributions <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th class="ps-4">Payout Date</th>
                        <th>Trust / Symbol</th>
                        <th>Quarter</th>
                        <th class="text-end">Interest (₹)</th>
                        <th class="text-end">Dividend (₹)</th>
                        <th class="text-end">ROC (₹)</th>
                        <th class="text-end">Total Gross (₹)</th>
                        <th class="text-end">TDS (₹)</th>
                        <th class="text-end pe-4">Net Received (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reitDists)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">No trust distributions received in this period.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reitDists as $rd): ?>
                            <tr>
                                <td class="ps-4"><?= date('d-M-Y', strtotime($rd['payout_date'])) ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($rd['symbol']) ?></div>
                                    <div class="text-muted" style="font-size: 0.72rem;"><?= esc($rd['trust_name']) ?></div>
                                </td>
                                <td class="text-secondary"><?= esc($rd['quarter_description'] ?: '—') ?></td>
                                <td class="text-end"><?= format_inr($rd['interest_component']) ?></td>
                                <td class="text-end"><?= format_inr($rd['dividend_component']) ?></td>
                                <td class="text-end"><?= format_inr($rd['return_of_capital']) ?></td>
                                <td class="text-end fw-semibold text-dark"><?= format_inr($rd['total_amount']) ?></td>
                                <td class="text-end text-danger"><?= format_inr($rd['tds_deducted']) ?></td>
                                <td class="text-end pe-4 fw-bold text-success"><?= format_inr($rd['net_received']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($reitDists)): ?>
                    <tfoot class="table-light fw-bold border-top-2" style="font-size: 0.8rem;">
                        <tr>
                            <td class="ps-4" colspan="3">TOTAL TRUST DISTRIBUTIONS</td>
                            <td class="text-end"><?= format_inr($reitTotals['interest']) ?></td>
                            <td class="text-end"><?= format_inr($reitTotals['dividend']) ?></td>
                            <td class="text-end"><?= format_inr($reitTotals['roc']) ?></td>
                            <td class="text-end text-dark"><?= format_inr($reitTotals['gross']) ?></td>
                            <td class="text-end text-danger"><?= format_inr($reitTotals['tds']) ?></td>
                            <td class="text-end pe-4 text-success"><?= format_inr($reitTotals['net']) ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <!-- Section 3: Bond Coupon Interest Ledger -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-bank text-dark me-2"></i>Bonds Coupon Interest Ledger (<?= count($bondCoupons) ?> Payouts)
            </h6>
            <a href="<?= base_url('bonds?tab=interest') ?>" class="btn btn-sm btn-light border px-2.5 py-1 text-primary shadow-sm">
                Bonds Interest <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th class="ps-4">Payout Date</th>
                        <th>Bond / Security</th>
                        <th class="text-center">Coupon Rate</th>
                        <th>Period</th>
                        <th class="text-end">Gross Interest (₹)</th>
                        <th class="text-end">TDS (₹)</th>
                        <th class="text-end pe-4">Net Interest (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bondCoupons)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No bond coupon interest received in this period.</td></tr>
                    <?php else: ?>
                        <?php foreach ($bondCoupons as $bc): ?>
                            <tr>
                                <td class="ps-4"><?= date('d-M-Y', strtotime($bc['payout_date'])) ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($bc['bond_name']) ?></div>
                                    <div class="text-muted font-monospace" style="font-size: 0.7rem;"><?= esc($bc['isin']) ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border"><?= number_format($bc['coupon_rate'], 2) ?>% p.a.</span>
                                </td>
                                <td class="text-secondary"><?= esc($bc['period_description'] ?: '—') ?></td>
                                <td class="text-end fw-semibold text-dark"><?= format_inr($bc['gross_interest']) ?></td>
                                <td class="text-end text-danger"><?= format_inr($bc['tds_deducted']) ?></td>
                                <td class="text-end pe-4 fw-bold text-success"><?= format_inr($bc['net_interest']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($bondCoupons)): ?>
                    <tfoot class="table-light fw-bold border-top-2" style="font-size: 0.8rem;">
                        <tr>
                            <td class="ps-4" colspan="4">TOTAL BOND COUPON INTEREST</td>
                            <td class="text-end text-dark"><?= format_inr($bondTotals['gross']) ?></td>
                            <td class="text-end text-danger"><?= format_inr($bondTotals['tds']) ?></td>
                            <td class="text-end pe-4 text-success"><?= format_inr($bondTotals['net']) ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
