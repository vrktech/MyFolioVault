<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-pie-chart-fill text-primary me-2"></i>Asset Allocation &amp; Valuation Snapshot
            </h3>
            <p class="text-secondary small mb-0">
                Live mark-to-market portfolio valuation, unrealized profit &amp; loss, and asset class weighting across all 6 investment modules.
            </p>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 shadow-sm d-flex align-items-center gap-1.5"
                    onclick="window.print()" title="Print valuation snapshot">
                <i class="bi bi-printer"></i>
                <span>Print Snapshot</span>
            </button>
        </div>
    </div>

    <!-- Multipage Sub-Navigation -->
    <?= $this->include('reports/_nav') ?>

    <!-- 4 Hero Valuation KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Current Value -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-primary border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Current Portfolio Value</div>
                        <h4 class="fw-bold text-primary mb-0 mt-1"><?= format_inr($totalCurrent) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Live mark-to-market valuation</div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2">
                        <i class="bi bi-gem fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Invested Cost Basis -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-secondary border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Invested Basis</div>
                        <h4 class="fw-bold text-dark mb-0 mt-1"><?= format_inr($totalInvested) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Net principal active capital</div>
                    </div>
                    <div class="bg-secondary-subtle text-secondary rounded-3 p-2">
                        <i class="bi bi-piggy-bank fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Unrealized P&L -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-<?= $totalPnl >= 0 ? 'success' : 'danger' ?> border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Unrealized Profit &amp; Loss</div>
                        <h4 class="fw-bold <?= $totalPnl >= 0 ? 'text-success' : 'text-danger' ?> mb-0 mt-1">
                            <?= ($totalPnl >= 0 ? '+' : '') . format_inr($totalPnl) ?>
                        </h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                            <?= ($totalPnlPct >= 0 ? '+' : '') . number_format($totalPnlPct, 2) ?>% overall return
                        </div>
                    </div>
                    <div class="bg-<?= $totalPnl >= 0 ? 'success' : 'danger' ?>-subtle text-<?= $totalPnl >= 0 ? 'success' : 'danger' ?> rounded-3 p-2">
                        <i class="bi bi-arrow-<?= $totalPnl >= 0 ? 'up-right text-success' : 'down-right text-danger' ?> fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Active Holdings Count -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-info border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Active Positions</div>
                        <?php 
                        $totalPositions = array_sum(array_column($allocations, 'holdings_count'));
                        ?>
                        <h4 class="fw-bold text-info-emphasis mb-0 mt-1"><?= number_format($totalPositions) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Securities, schemes &amp; folios</div>
                    </div>
                    <div class="bg-info-subtle text-info-emphasis rounded-3 p-2">
                        <i class="bi bi-layers fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visual Stacked Asset Allocation Progress Meter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold text-dark mb-0">Asset Class Weighting Distribution</h6>
                <span class="text-muted small">Total: 100.0%</span>
            </div>

            <!-- Progress Meter -->
            <div class="progress rounded-pill shadow-sm" style="height: 18px;">
                <?php foreach ($allocations as $a): ?>
                    <?php if ($a['share_pct'] > 0.05): ?>
                        <div class="progress-bar bg-<?= esc($a['color']) ?>" 
                             role="progressbar" 
                             style="width: <?= number_format($a['share_pct'], 2) ?>%;" 
                             aria-valuenow="<?= number_format($a['share_pct'], 2) ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100" 
                             title="<?= esc($a['name']) ?>: <?= number_format($a['share_pct'], 1) ?>%">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Legend Pills -->
            <div class="d-flex flex-wrap gap-3 mt-3">
                <?php foreach ($allocations as $a): ?>
                    <div class="d-flex align-items-center gap-1.5 small">
                        <span class="d-inline-block rounded-circle bg-<?= esc($a['color']) ?>" style="width: 10px; height: 10px;"></span>
                        <span class="text-secondary"><?= esc($a['name']) ?>:</span>
                        <strong class="text-dark"><?= number_format($a['share_pct'], 1) ?>%</strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Asset Allocation Table -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Portfolio Holdings &amp; Asset Class Summary</h5>
                <small class="text-muted">Breakdown of current capital allocation across all 6 investment modules</small>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th class="ps-4">Asset Class / Module</th>
                        <th class="text-end">Invested Value (₹)</th>
                        <th class="text-end">Current Value (₹)</th>
                        <th class="text-end">Unrealized P&amp;L (₹)</th>
                        <th class="text-end">Weight (%)</th>
                        <th class="text-center">Positions</th>
                        <th class="text-center pe-4" style="width: 120px;">Module</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allocations as $a): ?>
                        <tr>
                            <!-- Module -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="bg-<?= esc($a['color']) ?>-subtle text-<?= esc($a['color']) ?> rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="<?= esc($a['icon']) ?> fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="fw-bold text-dark fs-6"><?= esc($a['name']) ?></span>
                                            <?php if ($a['key'] === 'equities' && !empty($equitiesSectorAllocation)): ?>
                                                <button class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target=".equities-sector-alloc-row" aria-expanded="false" style="font-size: 0.65rem;" title="Toggle Equities Sector Allocation">
                                                    <i class="bi bi-diagram-3 me-1"></i>Sectors (<?= count($equitiesSectorAllocation) ?>) <i class="bi bi-chevron-down ms-1" style="font-size: 0.55rem;"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Invested -->
                            <td class="text-end fw-semibold">
                                <?= format_inr($a['invested']) ?>
                            </td>

                            <!-- Current Value -->
                            <td class="text-end fw-bold text-primary">
                                <?= format_inr($a['current']) ?>
                            </td>

                            <!-- Unrealized P&L -->
                            <td class="text-end">
                                <?= format_pnl($a['unrealized'], $a['unrealized_pct']) ?>
                            </td>

                            <!-- Weight % -->
                            <td class="text-end fw-semibold text-dark">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <span><?= number_format($a['share_pct'], 1) ?>%</span>
                                    <div class="progress rounded-pill d-none d-sm-flex" style="width: 45px; height: 6px;">
                                        <div class="progress-bar bg-<?= esc($a['color']) ?>" style="width: <?= number_format($a['share_pct'], 1) ?>%;"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Positions -->
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">
                                    <?= number_format($a['holdings_count']) ?>
                                </span>
                            </td>

                            <!-- Action -->
                            <td class="text-center pe-4">
                                <a href="<?= esc($a['url']) ?>" class="btn btn-light btn-sm border px-2.5 py-1 text-primary shadow-sm" title="Go to module dashboard">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>View
                                </a>
                            </td>
                        </tr>
                        <?php if ($a['key'] === 'equities' && !empty($equitiesSectorAllocation)): ?>
                            <?php foreach ($equitiesSectorAllocation as $s): ?>
                                <tr class="collapse equities-sector-alloc-row bg-light bg-opacity-75 border-start border-primary border-3">
                                    <td class="ps-5">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-arrow-return-right text-primary opacity-75"></i>
                                            <div>
                                                <span class="fw-semibold text-dark"><?= esc($s['name']) ?></span>
                                                <span class="badge bg-primary-subtle text-primary ms-1" style="font-size: 0.62rem;"><?= number_format($s['equity_share_pct'], 1) ?>% of Equities</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end text-muted small">
                                        <?= format_inr($s['invested']) ?>
                                    </td>
                                    <td class="text-end fw-semibold text-dark small">
                                        <?= format_inr($s['current']) ?>
                                    </td>
                                    <td class="text-end small">
                                        <?= format_pnl($s['unrealized'], $s['unrealized_pct']) ?>
                                    </td>
                                    <td class="text-end fw-semibold text-dark small">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <span><?= number_format($s['share_pct'], 1) ?>%</span>
                                            <div class="progress rounded-pill d-none d-sm-flex" style="width: 35px; height: 5px;">
                                                <div class="progress-bar bg-primary" style="width: <?= number_format($s['share_pct'], 1) ?>%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <?= number_format($s['holdings_count']) ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <span class="text-muted" style="font-size: 0.70rem;">Sector Weight</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-bold border-top-2" style="font-size: 0.85rem;">
                    <tr>
                        <td class="ps-4">TOTAL CONSOLIDATED PORTFOLIO</td>
                        <td class="text-end"><?= format_inr($totalInvested) ?></td>
                        <td class="text-end text-primary fs-6"><?= format_inr($totalCurrent) ?></td>
                        <td class="text-end"><?= format_pnl($totalPnl, $totalPnlPct) ?></td>
                        <td class="text-end">100.0%</td>
                        <td class="text-center"><?= number_format($totalPositions) ?></td>
                        <td class="pe-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

