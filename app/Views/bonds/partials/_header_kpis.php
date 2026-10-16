<!-- Header & Top Action Buttons -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bonds</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">Bonds & Fixed Income Portfolio</h3>
        <small class="text-muted">Sovereign Gold Bonds (SGB), Government Securities (G-Secs), Corporate NCDs & Tax-Free Bonds</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('bonds/export-holdings') ?>" class="btn btn-outline-success btn-sm rounded-3 px-3 fw-semibold shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
        </a>
        <a href="<?= base_url('bonds/new') ?>" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i>Add New Bond
        </a>
    </div>
</div>

<!-- 5 Executive KPI Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Current Portfolio Value -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Portfolio Value (CMP)</div>
                    <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_current_value']) ?></div>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                        <i class="bi bi-check-circle-fill text-success me-1"></i><?= $summary['active_holdings_count'] ?> Active Bonds
                    </div>
                </div>
                <div class="bg-primary-subtle text-primary rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-receipt fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Invested Capital -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Invested Capital</div>
                    <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_invested']) ?></div>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">FIFO Cost Basis</div>
                </div>
                <div class="bg-info-subtle text-info-emphasis rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-cash-coin fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Cumulative Interest Earned -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Interest Earned (Net)</div>
                    <div class="fs-5 fw-bold text-success">
                        <?= format_inr($summary['total_interest_earned']) ?>
                    </div>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">Total Coupons Received</div>
                </div>
                <div class="bg-success-subtle text-success rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-bank fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Unrealized Capital Gains -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-<?= $summary['total_unrealized_pnl'] >= 0 ? 'success' : 'danger' ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Unrealized Capital P&L</div>
                    <div class="fs-5 fw-bold">
                        <?= format_pnl($summary['total_unrealized_pnl'], $summary['unrealized_pnl_percent']) ?>
                    </div>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">Open Units</div>
                </div>
                <div class="bg-<?= $summary['total_unrealized_pnl'] >= 0 ? 'success' : 'danger' ?>-subtle text-<?= $summary['total_unrealized_pnl'] >= 0 ? 'success' : 'danger' ?> rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-graph-<?= $summary['total_unrealized_pnl'] >= 0 ? 'up' : 'down' ?>-arrow fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Net Return -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-<?= $summary['total_net_return'] >= 0 ? 'success' : 'danger' ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Total Net Gains</div>
                    <div class="fs-5 fw-bold">
                        <?= format_pnl($summary['total_net_return']) ?>
                    </div>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">Capital P&L + Interest</div>
                </div>
                <div class="bg-<?= $summary['total_net_return'] >= 0 ? 'success' : 'danger' ?>-subtle text-<?= $summary['total_net_return'] >= 0 ? 'success' : 'danger' ?> rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-trophy fs-5"></i>
                </div>
            </div>
        </div>
    </div>
</div>
