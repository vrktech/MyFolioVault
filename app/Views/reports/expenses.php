<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-receipt-cutoff text-danger me-2"></i>Expenses &amp; Statutory Friction Report
            </h3>
            <p class="text-secondary small mb-0">
                Audit of all transaction friction, brokerage commissions, Securities Transaction Tax (STT), and tax deducted at source (TDS).
            </p>
        </div>
    </div>

    <!-- Multipage Sub-Navigation -->
    <?= $this->include('reports/_nav') ?>

    <!-- Date Range Filter Toolbar -->
    <?= $this->include('reports/_date_filter') ?>

    <!-- 4 Hero Friction KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Friction -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-danger border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Friction &amp; Taxes</div>
                        <h4 class="fw-bold text-danger mb-0 mt-1"><?= format_inr($grandExpenses['total']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Total statutory &amp; trading drag</div>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded-3 p-2">
                        <i class="bi bi-receipt fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Brokerage -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-primary border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Brokerage Charges</div>
                        <h4 class="fw-bold text-dark mb-0 mt-1"><?= format_inr($grandExpenses['brokerage']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Trading commissions paid</div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2">
                        <i class="bi bi-briefcase fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. STT & Govt Taxes -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-info border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">STT &amp; Stamp Duty</div>
                        <h4 class="fw-bold text-info-emphasis mb-0 mt-1"><?= format_inr($grandExpenses['stt']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Statutory turnover duties &amp; GST</div>
                    </div>
                    <div class="bg-info-subtle text-info-emphasis rounded-3 p-2">
                        <i class="bi bi-file-earmark-ruled fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. TDS Deducted -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-warning border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">TDS Deducted (Tax Credit)</div>
                        <h4 class="fw-bold text-warning-emphasis mb-0 mt-1"><?= format_inr($grandExpenses['tds']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Claimable in ITR u/s 199</div>
                    </div>
                    <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-2">
                        <i class="bi bi-shield-x fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module-by-Module Expenses Breakdown Table -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Module-wise Friction &amp; Expense Summary</h5>
                <small class="text-muted">Breakdown by brokerage, STT, tax withheld, and scheme charges for <?= esc($range['label']) ?></small>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th class="ps-4">Asset Class / Module</th>
                        <th class="text-end">Brokerage (₹)</th>
                        <th class="text-end">STT &amp; Taxes (₹)</th>
                        <th class="text-end">TDS Withheld (₹)</th>
                        <th class="text-end">Platform &amp; Misc (₹)</th>
                        <th class="text-end">Total Friction (₹)</th>
                        <th class="text-end pe-4">Share of Friction</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($moduleExpenses as $me): ?>
                        <?php 
                        $pct = $grandExpenses['total'] > 0 ? (($me['total'] / $grandExpenses['total']) * 100) : 0.0;
                        ?>
                        <tr>
                            <!-- Module -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="bg-light text-secondary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="<?= esc($me['icon']) ?> fs-5"></i>
                                    </div>
                                    <div class="fw-bold text-dark fs-6"><?= esc($me['name']) ?></div>
                                </div>
                            </td>

                            <!-- Brokerage -->
                            <td class="text-end">
                                <?= $me['brokerage'] > 0 ? format_inr($me['brokerage']) : '<span class="text-muted">—</span>' ?>
                            </td>

                            <!-- STT -->
                            <td class="text-end">
                                <?= $me['stt'] > 0 ? format_inr($me['stt']) : '<span class="text-muted">—</span>' ?>
                            </td>

                            <!-- TDS -->
                            <td class="text-end text-warning-emphasis fw-semibold">
                                <?= $me['tds'] > 0 ? format_inr($me['tds']) : '<span class="text-muted">—</span>' ?>
                            </td>

                            <!-- Platform / Misc -->
                            <td class="text-end">
                                <?= $me['other'] > 0 ? format_inr($me['other']) : '<span class="text-muted">—</span>' ?>
                            </td>

                            <!-- Total -->
                            <td class="text-end fw-bold text-danger">
                                <?= format_inr($me['total']) ?>
                            </td>

                            <!-- % Share -->
                            <td class="text-end pe-4 text-secondary">
                                <?= number_format($pct, 1) ?>%
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-bold border-top-2" style="font-size: 0.85rem;">
                    <tr>
                        <td class="ps-4">TOTAL STATUTORY &amp; TRADING EXPENSES</td>
                        <td class="text-end"><?= format_inr($grandExpenses['brokerage']) ?></td>
                        <td class="text-end"><?= format_inr($grandExpenses['stt']) ?></td>
                        <td class="text-end text-warning-emphasis"><?= format_inr($grandExpenses['tds']) ?></td>
                        <td class="text-end"><?= format_inr($grandExpenses['other']) ?></td>
                        <td class="text-end text-danger fs-6"><?= format_inr($grandExpenses['total']) ?></td>
                        <td class="text-end pe-4">100.0%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Income Tax Filing & Form 26AS Tax Credit Advisory Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex align-items-start gap-3">
                <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-3 d-flex align-items-center justify-content-center">
                    <i class="bi bi-lightbulb fs-3"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Income Tax Return (ITR) Filing &amp; TDS Credit Verification</h6>
                    <p class="text-secondary small mb-2">
                        Total TDS deducted across your portfolio for this period is <strong><?= format_inr($grandExpenses['tds']) ?></strong>.
                        Ensure this amount matches your <strong>Annual Information Statement (AIS)</strong> and <strong>Form 26AS</strong> on the Income Tax e-filing portal.
                    </p>
                    <div class="d-flex flex-wrap gap-3 small text-muted">
                        <div><i class="bi bi-check-circle text-success me-1"></i>Sec 194: TDS on Equities Dividends @ 10% (exceeding ₹5,000)</div>
                        <div><i class="bi bi-check-circle text-success me-1"></i>Sec 193: TDS on Bond Interest @ 10%</div>
                        <div><i class="bi bi-check-circle text-success me-1"></i>Sec 194LBA: TDS on Trust Distribution Interest @ 10%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

