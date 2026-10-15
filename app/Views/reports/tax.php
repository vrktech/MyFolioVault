<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-percent text-primary me-2"></i>Capital Gains Report
            </h3>
            <p class="text-secondary small mb-0">
                Audited Long-Term (LTCG) and Short-Term (STCG) capital gains realized across all investment modules.
            </p>
        </div>
    </div>

    <!-- Multipage Sub-Navigation -->
    <?= $this->include('reports/_nav') ?>

    <!-- Date Range Filter Toolbar -->
    <?= $this->include('reports/_date_filter') ?>

    <!-- 3 Hero Tax KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Realized Capital Gains -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-<?= $totals['total_gain'] >= 0 ? 'success' : 'danger' ?> border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Realized Capital Gains</div>
                        <h4 class="fw-bold mb-0 mt-1 <?= $totals['total_gain'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= ($totals['total_gain'] >= 0 ? '+' : '') . format_inr($totals['total_gain']) ?>
                        </h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Net realized profits across all modules</div>
                    </div>
                    <div class="bg-<?= $totals['total_gain'] >= 0 ? 'success' : 'danger' ?>-subtle text-<?= $totals['total_gain'] >= 0 ? 'success' : 'danger' ?> rounded-3 p-2">
                        <i class="bi bi-trophy fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. STCG -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-warning border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Short-Term Gains (STCG)</div>
                        <h4 class="fw-bold text-dark mb-0 mt-1"><?= format_inr($totals['stcg']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Holding &le; 12 months &bull; Short-Term Capital Gains</div>
                    </div>
                    <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-2">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. LTCG -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-primary border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Long-Term Gains (LTCG)</div>
                        <h4 class="fw-bold text-primary mb-0 mt-1"><?= format_inr($totals['ltcg']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Holding &gt; 12 months &bull; Long-Term Capital Gains</div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2">
                        <i class="bi bi-calendar-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module-wise Capital Gains Summary Table -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Module-wise Capital Gains (LTCG &amp; STCG)</h5>
                <small class="text-muted">FIFO matched audit across all asset classes for <?= esc($range['label']) ?></small>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill">
                <?= number_format($totals['lots']) ?> Matched Lots
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th class="ps-4">Asset Class / Module</th>
                        <th class="text-end">STCG (&le;1 yr)</th>
                        <th class="text-end">LTCG (&gt;1 yr)</th>
                        <th class="text-end">Total Realized Gain</th>
                        <th class="text-end">Sales Value (₹)</th>
                        <th class="text-end">Cost Basis (₹)</th>
                        <th class="text-center">Lots</th>
                        <th class="text-center pe-4" style="width: 110px;">Tax Log</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $m): ?>
                        <tr>
                            <!-- Module -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="bg-<?= esc($m['color']) ?>-subtle text-<?= esc($m['color']) ?> rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="<?= esc($m['icon']) ?> fs-5"></i>
                                    </div>
                                    <div class="fw-bold text-dark fs-6"><?= esc($m['name']) ?></div>
                                </div>
                            </td>

                            <!-- STCG -->
                            <td class="text-end fw-semibold <?= $m['stcg'] > 0 ? 'text-dark' : 'text-muted' ?>">
                                <?= $m['stcg'] != 0 ? format_inr($m['stcg']) : '₹0.00' ?>
                            </td>

                            <!-- LTCG -->
                            <td class="text-end fw-semibold <?= $m['ltcg'] > 0 ? 'text-primary' : 'text-muted' ?>">
                                <?= $m['ltcg'] != 0 ? format_inr($m['ltcg']) : '₹0.00' ?>
                            </td>

                            <!-- Total Realized Gain -->
                            <td class="text-end fw-bold <?= $m['total_gain'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= ($m['total_gain'] >= 0 ? '+' : '') . format_inr($m['total_gain']) ?>
                            </td>

                            <!-- Matched Sales Value -->
                            <td class="text-end text-secondary">
                                <?= format_inr($m['sales']) ?>
                            </td>

                            <!-- Matched Cost Basis -->
                            <td class="text-end text-secondary">
                                <?= format_inr($m['cost']) ?>
                            </td>

                            <!-- Lots Count -->
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">
                                    <?= number_format($m['lots']) ?>
                                </span>
                            </td>

                            <!-- Action / Link -->
                            <td class="text-center pe-4">
                                <?php if ($m['lots'] > 0): ?>
                                    <a href="<?= esc($m['log_url']) ?>" class="btn btn-light btn-sm border px-2.5 py-1 text-primary shadow-sm" title="View module capital gains log">
                                        <i class="bi bi-journal-text me-1"></i>Tax Log
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-bold border-top-2" style="font-size: 0.85rem;">
                    <tr>
                        <td class="ps-4">CONSOLIDATED CAPITAL GAINS TOTAL</td>
                        <td class="text-end text-dark"><?= format_inr($totals['stcg']) ?></td>
                        <td class="text-end text-primary"><?= format_inr($totals['ltcg']) ?></td>
                        <td class="text-end <?= $totals['total_gain'] >= 0 ? 'text-success' : 'text-danger' ?> fs-6">
                            <?= ($totals['total_gain'] >= 0 ? '+' : '') . format_inr($totals['total_gain']) ?>
                        </td>
                        <td class="text-end text-secondary"><?= format_inr($totals['sales']) ?></td>
                        <td class="text-end text-secondary"><?= format_inr($totals['cost']) ?></td>
                        <td class="text-center"><?= number_format($totals['lots']) ?></td>
                        <td class="pe-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
