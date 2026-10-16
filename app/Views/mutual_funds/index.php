<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header & Top Action Buttons -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mutual Funds</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">Mutual Funds Portfolio</h3>
        <small class="text-muted">Open-ended equity, hybrid, and debt schemes tracked with Folio numbers and FIFO tax lots</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('mutual-funds/refresh-navs') ?>" class="btn btn-light border shadow-sm btn-sm rounded-3 px-3">
            <i class="bi bi-arrow-clockwise me-1 text-primary"></i>Refresh AMFI NAVs
        </a>
        <a href="<?= base_url('mutual-funds/export-holdings') ?>" class="btn btn-outline-success btn-sm rounded-3 px-3 fw-semibold shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
        </a>
        <a href="<?= base_url('mutual-funds/new') ?>" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i>Add New Scheme
        </a>
    </div>
</div>

<!-- 5 Executive KPI Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Current Portfolio Value -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Portfolio Value (NAV)</div>
            <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_current_value']) ?></div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                <i class="bi bi-check-circle-fill text-success me-1"></i><?= $summary['active_holdings_count'] ?> Active Schemes
            </div>
        </div>
    </div>

    <!-- Total Invested Capital -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Invested Capital</div>
            <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_invested']) ?></div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">FIFO Cost Basis</div>
        </div>
    </div>

    <!-- Total Unrealized P&L -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Unrealized P&L</div>
            <div class="fs-5 fw-bold">
                <?= format_pnl($summary['total_unrealized_pnl'], $summary['unrealized_pnl_percent']) ?>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Open Units</div>
        </div>
    </div>

    <!-- Realized P&L (FIFO) -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Realized Gains (FIFO)</div>
            <div class="fs-5 fw-bold">
                <?= format_pnl($summary['total_realized_pnl']) ?>
            </div>
            <div class="d-flex gap-1 mt-1" style="font-size: 0.7rem;">
                <span class="badge bg-light text-dark border">STCG: <?= format_inr_short($summary['total_stcg']) ?></span>
                <span class="badge bg-light text-dark border">LTCG: <?= format_inr_short($summary['total_ltcg']) ?></span>
            </div>
        </div>
    </div>

    <!-- Total Net Gain -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Total Net Gains</div>
            <div class="fs-5 fw-bold">
                <?= format_pnl($summary['total_net_gain']) ?>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Realized + Unrealized</div>
        </div>
    </div>
</div>

<!-- Main Tabs Section -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-3 px-4 rounded-top-4">
        <ul class="nav nav-tabs card-header-tabs border-0" id="mfTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="holdings-tab" data-bs-toggle="tab" data-bs-target="#holdings" type="button" role="tab">
                    <i class="bi bi-briefcase me-1 text-success"></i>Active Holdings (<?= count($activeHoldings ?? $holdings) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="past-holdings-tab" data-bs-toggle="tab" data-bs-target="#past-holdings" type="button" role="tab">
                    <i class="bi bi-archive me-1 text-secondary"></i>Past Holdings (<?= count($pastHoldings ?? []) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
                    <i class="bi bi-clock-history me-1"></i>Trade Ledger (<?= count($transactions) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="capitalgains-tab" data-bs-toggle="tab" data-bs-target="#capitalgains" type="button" role="tab">
                    <i class="bi bi-receipt-cutoff me-1 text-primary"></i>FIFO Tax Log (STCG / LTCG) (<?= count($capitalGains) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="mfTabContent">
            
            <!-- TAB 1: ACTIVE HOLDINGS -->
            <div class="tab-pane fade show active p-3" id="holdings" role="tabpanel">
                <?php if (empty($activeHoldings ?? $holdings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-briefcase fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Active Mutual Fund Schemes Found</h5>
                        <p class="text-muted small">You don't have any active mutual fund schemes currently. Click below to add your first scheme!</p>
                        <a href="<?= base_url('mutual-funds/new') ?>" class="btn btn-primary btn-sm rounded-3">
                            <i class="bi bi-plus-lg me-1"></i>Add New Scheme
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Scheme / AMFI Code</th>
                                    <th class="text-end">Units Held</th>
                                    <th class="text-end">Avg NAV (₹)</th>
                                    <th class="text-end">Latest NAV (₹)</th>
                                    <th class="text-end">Invested (₹)</th>
                                    <th class="text-end">Current (₹)</th>
                                    <th class="text-end">Unrealized P&L</th>
                                    <th class="text-center" style="min-width: 160px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($activeHoldings ?? $holdings) as $h): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <div class="brand-badge-sm me-2 bg-success bg-opacity-10 text-success rounded px-2 py-1 small fw-bold">
                                                    <?= esc($h['amfi_code']) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark mb-0"><?= esc($h['scheme_name']) ?></div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                            <?= esc($h['category']) ?>
                                                        </span>
                                                        <?php if (!empty($h['folio_number'])): ?>
                                                            <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                                Folio: <?= esc($h['folio_number']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($h['nav_date'])): ?>
                                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                                NAV as of <?= date('d-M-Y', strtotime($h['nav_date'])) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?= number_format($h['active_units'], 4) ?>
                                        </td>
                                        <td class="text-end text-muted small">
                                            <?= format_inr($h['avg_purchase_nav']) ?>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <?= format_inr($h['current_nav']) ?>
                                        </td>
                                        <td class="text-end small text-muted">
                                            <?= format_inr($h['invested_value']) ?>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            <?= format_inr($h['current_value']) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($h['unrealized_pnl'], $h['unrealized_pnl_percent'], false, true) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success rounded-start-3 px-2 py-1 open-mf-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#mfTransactionModal"
                                                        data-id="<?= $h['id'] ?>"
                                                        data-code="<?= esc($h['amfi_code']) ?>"
                                                        data-name="<?= esc($h['scheme_name']) ?>"
                                                        data-folio="<?= esc($h['folio_number']) ?>"
                                                        data-units="<?= $h['active_units'] ?>"
                                                        data-nav="<?= $h['current_nav'] ?>"
                                                        data-avg="<?= $h['avg_purchase_nav'] ?>">
                                                    <i class="bi bi-lightning-charge-fill me-1"></i>Add Trans
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split rounded-end-3" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <button class="dropdown-item small open-edit-mf-modal"
                                                                data-id="<?= $h['id'] ?>"
                                                                data-name="<?= esc($h['scheme_name']) ?>"
                                                                data-category="<?= esc($h['category']) ?>"
                                                                data-folio="<?= esc($h['folio_number'] ?? '') ?>"
                                                                data-amc="<?= esc($h['fund_house'] ?? '') ?>"
                                                                data-amfi="<?= esc($h['amfi_code'] ?? '') ?>"
                                                                data-isin="<?= esc($h['isin'] ?? '') ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editMfModal">
                                                            <i class="bi bi-pencil me-2 text-primary"></i>Edit Scheme Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('mutual-funds?fund_id=' . $h['id'] . '&tab=transactions') ?>" onclick="showMfLedger(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                            href="<?= base_url('mutual-funds/delete/' . $h['id']) ?>" 
                                                            onclick="return confirm('Delete this Mutual Fund scheme and all its transaction records?');">
                                                            <i class="bi bi-trash me-2"></i>Delete Scheme
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 1B: PAST HOLDINGS (FULLY REDEEMED SCHEMES) -->
            <div class="tab-pane fade p-3" id="past-holdings" role="tabpanel">
                <?php if (empty($pastHoldings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-archive fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Past Mutual Fund Holdings Found</h5>
                        <p class="text-muted small">You don't have any fully redeemed mutual fund schemes yet. When you redeem 100% of your units in a scheme, it will appear here along with your lifetime realized profit/loss.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($pastHoldings) ?></strong> past / closed schemes
                        </div>
                        <div class="text-muted small">
                            <span class="badge bg-secondary-subtle text-secondary border"><i class="bi bi-info-circle me-1"></i>0 Active Units &bull; Lifetime Closed Trades</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="mfPastHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Scheme / AMFI Code</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Latest NAV (₹)</th>
                                    <th class="text-end">Realized P&L</th>
                                    <th class="text-center" style="min-width: 160px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pastHoldings as $ph): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <div class="brand-badge-sm me-2 bg-secondary bg-opacity-10 text-secondary rounded px-2 py-1 small fw-bold">
                                                    <?= esc($ph['amfi_code']) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark mb-0"><?= esc($ph['scheme_name']) ?></div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                                        <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                            <?= esc($ph['category']) ?>
                                                        </span>
                                                        <?php if (!empty($ph['folio_number'])): ?>
                                                            <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                                Folio: <?= esc($ph['folio_number']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($ph['nav_date'])): ?>
                                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                                NAV as of <?= date('d-M-Y', strtotime($ph['nav_date'])) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                <i class="bi bi-check2-all me-1"></i>Closed
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <?= format_inr($ph['current_nav']) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($ph['realized_pnl']) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success rounded-start-3 px-2 py-1 open-mf-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#mfTransactionModal"
                                                        data-id="<?= $ph['id'] ?>"
                                                        data-code="<?= esc($ph['amfi_code']) ?>"
                                                        data-name="<?= esc($ph['scheme_name']) ?>"
                                                        data-folio="<?= esc($ph['folio_number']) ?>"
                                                        data-units="0"
                                                        data-nav="<?= $ph['current_nav'] ?>"
                                                        data-avg="0">
                                                    <i class="bi bi-plus-circle me-1"></i>Invest Again
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split rounded-end-3" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <button class="dropdown-item small open-edit-fund-modal"
                                                                data-id="<?= $ph['id'] ?>"
                                                                data-name="<?= esc($ph['scheme_name']) ?>"
                                                                data-amfi="<?= esc($ph['amfi_code']) ?>"
                                                                data-folio="<?= esc($ph['folio_number']) ?>"
                                                                data-category="<?= esc($ph['category']) ?>"
                                                                data-amc="<?= esc($ph['amc_name'] ?? '') ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editFundModal">
                                                            <i class="bi bi-pencil me-2 text-primary"></i>Edit Scheme Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('mutual-funds?mf_id=' . $ph['id'] . '&tab=transactions') ?>" onclick="showMfLedger(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-info" href="<?= base_url('mutual-funds?tax_mf_id=' . $ph['id'] . '&tab=capitalgains') ?>">
                                                            <i class="bi bi-receipt-cutoff me-2"></i>FIFO Tax Lots
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 2: TRADE LEDGER -->
            <div class="tab-pane fade p-3" id="transactions" role="tabpanel">
                <?php if (empty($transactions)): ?>
                    <p class="text-muted text-center py-4">No transactions recorded yet.</p>
                <?php else: ?>
                    <?php
                    $fyRanges            = get_fy_ranges();
                    $ledgerSchemes       = [];
                    $overallBuyAmount    = 0.0;
                    $overallRedeemAmount = 0.0;
                    $overallBuyUnits     = 0.0;
                    $overallRedeemUnits  = 0.0;
                    $overallBuyCount     = 0;
                    $overallRedeemCount  = 0;
                    $overallCharges      = 0.0;

                    foreach ($transactions as $t) {
                        $fId = (int)$t['mutual_fund_id'];
                        if (!isset($ledgerSchemes[$fId])) {
                            $ledgerSchemes[$fId] = [
                                'id'     => $fId,
                                'name'   => $t['scheme_name'],
                                'folio'  => $t['folio_number'],
                                'amfi'   => $t['amfi_code'],
                                'count'  => 0,
                            ];
                        }
                        $ledgerSchemes[$fId]['count']++;

                        $units   = (float)$t['units'];
                        $amt     = (float)$t['net_amount'];
                        $charges = (float)$t['charges'];
                        $overallCharges += $charges;

                        if (in_array($t['transaction_type'], ['BUY_SIP', 'BUY_LUMPSUM'], true)) {
                            $overallBuyAmount += $amt;
                            $overallBuyUnits  += $units;
                            $overallBuyCount++;
                        } else {
                            $overallRedeemAmount += $amt;
                            $overallRedeemUnits  += $units;
                            $overallRedeemCount++;
                        }
                    }
                    uasort($ledgerSchemes, fn($a, $b) => strcmp($a['name'], $b['name']));
                    $overallNetOutlay = $overallBuyAmount - $overallRedeemAmount;
                    ?>

                    <!-- Filter Toolbar -->
                    <div class="card border shadow-sm mb-3 bg-body-tertiary rounded-3">
                        <div class="card-body p-3">
                            <div class="row align-items-center g-2">
                                <div class="col-md-4 col-lg-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-success-subtle text-success rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                            <i class="bi bi-funnel-fill fs-6"></i>
                                        </div>
                                        <div>
                                            <label class="form-label mb-0 fw-semibold text-dark small">Filter Trades</label>
                                            <div class="text-muted" style="font-size: 0.72rem;">Filter by scheme &amp; financial year</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                        <div style="min-width: 220px; max-width: 300px;" class="flex-grow-1">
                                            <select id="ledgerFundFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">-- All Schemes (<?= count($transactions) ?> Trades) --</option>
                                                <?php foreach ($ledgerSchemes as $fId => $s): ?>
                                                    <option value="<?= $fId ?>" data-name="<?= esc($s['name']) ?>">
                                                        <?= esc($s['name']) ?> (Folio: <?= esc($s['folio']) ?>) (<?= $s['count'] ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div style="min-width: 170px; max-width: 200px;">
                                            <select id="ledgerDateFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">All Dates (All Time)</option>
                                                <option value="CURRENT_FY">Current FY (<?= $fyRanges['current']['label'] ?>)</option>
                                                <option value="LAST_FY">Last FY (<?= $fyRanges['last']['label'] ?>)</option>
                                            </select>
                                        </div>
                                        <button type="button" id="btnResetFundFilter" class="btn btn-sm btn-outline-secondary d-none" title="Reset all filters">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                        </button>
                                        <span id="filteredFundBadge" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 small d-none">
                                            <i class="bi bi-check2-circle me-1"></i><span id="filteredFundName"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Amounts Summary Cards -->
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-success">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Investments</span>
                                            <h5 class="fw-bold text-success mb-0 mt-1" id="kpiBuyAmount"><?= format_inr($overallBuyAmount) ?></h5>
                                            <div class="text-muted small mt-1" id="kpiBuyDetails" style="font-size: 0.75rem;">
                                                <?= $overallBuyCount ?> orders • <?= number_format($overallBuyUnits, 4) ?> units
                                            </div>
                                        </div>
                                        <div class="bg-success-subtle text-success rounded-3 p-2">
                                            <i class="bi bi-arrow-down-left-circle-fill fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-danger">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Redemptions</span>
                                            <h5 class="fw-bold text-danger mb-0 mt-1" id="kpiRedeemAmount"><?= format_inr($overallRedeemAmount) ?></h5>
                                            <div class="text-muted small mt-1" id="kpiRedeemDetails" style="font-size: 0.75rem;">
                                                <?= $overallRedeemCount ?> redemptions • <?= number_format($overallRedeemUnits, 4) ?> units
                                            </div>
                                        </div>
                                        <div class="bg-danger-subtle text-danger rounded-3 p-2">
                                            <i class="bi bi-arrow-up-right-circle-fill fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-primary">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Net Outlay</span>
                                            <h5 class="fw-bold <?= $overallNetOutlay >= 0 ? 'text-primary' : 'text-success' ?> mb-0 mt-1" id="kpiNetOutlay"><?= format_inr($overallNetOutlay) ?></h5>
                                            <div class="text-muted small mt-1" id="kpiNetDetails" style="font-size: 0.75rem;">
                                                Net capital outlay in period
                                            </div>
                                        </div>
                                        <div class="bg-primary-subtle text-primary rounded-3 p-2">
                                            <i class="bi bi-wallet2 fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-secondary">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Stamp Duty / Charges</span>
                                            <h5 class="fw-bold text-dark mb-0 mt-1" id="kpiCharges"><?= format_inr($overallCharges) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Total regulatory charges
                                            </div>
                                        </div>
                                        <div class="bg-secondary-subtle text-secondary rounded-3 p-2">
                                            <i class="bi bi-receipt fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="mfLedgerTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Date</th>
                                    <th>Scheme Name / Folio</th>
                                    <th>Action</th>
                                    <th class="text-end">Units</th>
                                    <th class="text-end">NAV (₹)</th>
                                    <th class="text-end">Stamp Duty / Charges</th>
                                    <th class="text-end">Net Outlay / Proceeds</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $t): ?>
                                    <tr class="mf-ledger-row"
                                        data-fund-id="<?= $t['mutual_fund_id'] ?>"
                                        data-name="<?= esc($t['scheme_name']) ?>"
                                        data-date="<?= $t['transaction_date'] ?>"
                                        data-type="<?= $t['transaction_type'] ?>"
                                        data-units="<?= (float)$t['units'] ?>"
                                        data-charges="<?= (float)$t['charges'] ?>"
                                        data-amount="<?= (float)$t['net_amount'] ?>">
                                        <td><?= date('d-M-Y', strtotime($t['transaction_date'])) ?></td>
                                        <td>
                                            <strong><?= esc($t['scheme_name']) ?></strong>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                Folio: <?= esc($t['folio_number']) ?> &bull; AMFI: <?= esc($t['amfi_code']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($t['transaction_type'] === 'BUY_SIP'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="bi bi-arrow-repeat me-1"></i>SIP BUY
                                                </span>
                                            <?php elseif ($t['transaction_type'] === 'BUY_LUMPSUM'): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                    <i class="bi bi-cash-stack me-1"></i>LUMPSUM
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i>REDEEM
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-semibold"><?= number_format($t['units'], 4) ?></td>
                                        <td class="text-end"><?= format_inr($t['nav']) ?></td>
                                        <td class="text-end text-muted"><?= format_inr((float)$t['charges']) ?></td>
                                        <td class="text-end fw-bold text-dark"><?= format_inr($t['net_amount']) ?></td>
                                        <td class="text-secondary"><?= esc($t['notes'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary py-0 px-1.5 open-edit-mf-trans"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editMfTransModal"
                                                    data-id="<?= $t['id'] ?>"
                                                    data-name="<?= esc($t['scheme_name']) ?>"
                                                    data-type="<?= esc($t['transaction_type']) ?>"
                                                    data-date="<?= esc($t['transaction_date']) ?>"
                                                    data-units="<?= (float)$t['units'] ?>"
                                                    data-nav="<?= (float)$t['nav'] ?>"
                                                    data-charges="<?= (float)$t['charges'] ?>"
                                                    data-notes="<?= esc($t['notes'] ?? '') ?>"
                                                    title="Edit Transaction">
                                                <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <a href="<?= base_url('mutual-funds/delete-transaction/' . $t['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger py-0 px-1.5 ms-1"
                                               onclick="return confirm('Delete this <?= esc($t['transaction_type']) ?> transaction? FIFO lots and capital gains will be automatically recalculated.');"
                                               title="Delete Transaction">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="ledgerNoRows" class="d-none text-center py-4">
                                    <td colspan="9" class="text-muted py-4">
                                        <i class="bi bi-funnel text-secondary fs-4 d-block mb-1"></i>
                                        No transactions found matching the selected scheme and date filter.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light fw-semibold border-top-2">
                                <tr>
                                    <td colspan="3">
                                        <span id="footerSummaryTitle">Total (<?= count($transactions) ?> Trades)</span>
                                        <div class="text-muted fw-normal" style="font-size: 0.72rem;" id="footerActionBreakdown">
                                            <span class="text-success"><?= $overallBuyCount ?> BUY</span> &bull; <span class="text-danger"><?= $overallRedeemCount ?> REDEEM</span>
                                        </div>
                                    </td>
                                    <td class="text-end" id="footerTotalUnits"><?= number_format($overallBuyUnits + $overallRedeemUnits, 4) ?></td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-end text-muted" id="footerTotalCharges"><?= format_inr($overallCharges) ?></td>
                                    <td class="text-end fw-bold fs-6" id="footerTotalAmount"><?= format_inr($overallNetOutlay) ?></td>
                                    <td class="text-secondary small" id="footerNote"><?= $overallNetOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized' ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'mfLedger']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 3: FIFO TAX LOG (STCG vs LTCG) -->
            <div class="tab-pane fade p-3" id="capitalgains" role="tabpanel">
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center rounded-3 small">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>FIFO Capital Gains Rules:</strong> Mutual fund units are redeemed in First-In, First-Out order.
                        For equity-oriented funds, holding period <strong>&le; 365 days = STCG</strong>. Holding period <strong>&gt; 365 days = LTCG</strong>.
                    </div>
                </div>

                <?php if (empty($capitalGains)): ?>
                    <p class="text-muted text-center py-4">No realized redemptions recorded yet. When mutual fund units are redeemed, FIFO matched tax lots will appear here.</p>
                <?php else: ?>
                    <?php
                    $fyRanges          = get_fy_ranges();
                    $taxSchemes        = [];
                    $totalMatchedUnits = 0.0;
                    $totalBuyCost      = 0.0;
                    $totalSellProceeds = 0.0;
                    $totalRealizedGain = 0.0;
                    $totalStcg         = 0.0;
                    $totalLtcg         = 0.0;

                    foreach ($capitalGains as $cg) {
                        $mId = (int)($cg['mutual_fund_id'] ?? $cg['mf_id'] ?? 0);
                        if (!isset($taxSchemes[$mId])) {
                            $taxSchemes[$mId] = [
                                'id'     => $mId,
                                'name'   => $cg['scheme_name'],
                                'folio'  => $cg['folio_number'],
                                'count'  => 0,
                            ];
                        }
                        $taxSchemes[$mId]['count']++;
                        $units    = (float)$cg['units_matched'];
                        $cost     = $units * (float)$cg['buy_nav'];
                        $proceeds = $units * (float)$cg['redeem_nav'];
                        $gain     = (float)$cg['realized_gain'];

                        $totalMatchedUnits += $units;
                        $totalBuyCost      += $cost;
                        $totalSellProceeds += $proceeds;
                        $totalRealizedGain += $gain;
                        if ($cg['gain_type'] === 'LTCG') {
                            $totalLtcg += $gain;
                        } else {
                            $totalStcg += $gain;
                        }
                    }
                    ?>

                    <!-- Filter Toolbar: Scheme Selector + Gain Type + Date Range -->
                    <div class="bg-light p-3 rounded-4 border mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="taxLogMfFilter" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-filter me-1"></i>Filter by Scheme
                                </label>
                                <select class="form-select form-select-sm shadow-sm" id="taxLogMfFilter">
                                    <option value="ALL">All Schemes (<?= count($capitalGains) ?> lots)</option>
                                    <?php foreach ($taxSchemes as $sc): ?>
                                        <option value="<?= $sc['id'] ?>" data-name="<?= esc($sc['name']) ?>">
                                            <?= esc($sc['name']) ?> (<?= $sc['count'] ?> lots)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="taxLogTypeFilter" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-tags me-1"></i>Tax Category
                                </label>
                                <select class="form-select form-select-sm shadow-sm" id="taxLogTypeFilter">
                                    <option value="ALL">All Categories (STCG &amp; LTCG)</option>
                                    <option value="STCG">STCG (&le; 365 Days)</option>
                                    <option value="LTCG">LTCG (&gt; 365 Days)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="taxLogDateFilter" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-calendar-range me-1"></i>Date Range (Redeem Date)
                                </label>
                                <select class="form-select form-select-sm shadow-sm" id="taxLogDateFilter">
                                    <option value="ALL">All Time</option>
                                    <option value="CURRENT_FY">Current FY (<?= esc($fyRanges['current']['label']) ?>)</option>
                                    <option value="LAST_FY">Last FY (<?= esc($fyRanges['last']['label']) ?>)</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 shadow-sm d-none" id="btnResetTaxFilter">
                                    <i class="bi bi-x-circle me-1"></i>Reset
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 d-none" id="filteredTaxBadge">
                            <span class="badge bg-primary text-white px-2.5 py-1.5 fw-normal">
                                <i class="bi bi-funnel-fill me-1"></i><span id="filteredTaxSchemeSpan">Active Filter</span>
                            </span>
                        </div>
                    </div>

                    <!-- 4 Responsive KPI Metric Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-primary border-4">
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Matched Units</div>
                                <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxUnits"><?= number_format($totalMatchedUnits, 4) ?></div>
                                <div class="text-muted small" style="font-size: 0.72rem;" id="kpiTaxQtyCount"><?= count($capitalGains) ?> FIFO lots</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-secondary border-4">
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Purchase Cost</div>
                                <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxCost"><?= format_inr($totalBuyCost) ?></div>
                                <div class="text-muted small" style="font-size: 0.72rem;">Original purchase cost</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-info border-4">
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Redemption Value</div>
                                <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxProceeds"><?= format_inr($totalSellProceeds) ?></div>
                                <div class="text-muted small" style="font-size: 0.72rem;">Gross redemption value</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-<?= $totalRealizedGain >= 0 ? 'success' : 'danger' ?> border-4">
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Net Realized P&amp;L</div>
                                <div class="fs-5 fw-bold <?= $totalRealizedGain >= 0 ? 'text-success' : 'text-danger' ?> mb-0 mt-1" id="kpiTaxGain">
                                    <?= ($totalRealizedGain >= 0 ? '+' : '') . format_inr($totalRealizedGain) ?>
                                </div>
                                <div class="text-muted small" style="font-size: 0.72rem;" id="kpiTaxBreakdown">
                                    STCG: <?= format_inr($totalStcg) ?> &bull; LTCG: <?= format_inr($totalLtcg) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="mfTaxLogTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Scheme / Folio</th>
                                    <th>Purchase Date</th>
                                    <th>Redeem Date</th>
                                    <th class="text-center">Days Held</th>
                                    <th class="text-center">Tax Category</th>
                                    <th class="text-end">Matched Units</th>
                                    <th class="text-end">Purchase NAV (₹)</th>
                                    <th class="text-end">Redeem NAV (₹)</th>
                                    <th class="text-end">Realized P&amp;L (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($capitalGains as $cg): ?>
                                    <?php
                                    $units    = (float)$cg['units_matched'];
                                    $cost     = $units * (float)$cg['buy_nav'];
                                    $proceeds = $units * (float)$cg['redeem_nav'];
                                    $gain     = (float)$cg['realized_gain'];
                                    ?>
                                    <tr class="mf-taxlog-row"
                                        data-mf-id="<?= $cg['mutual_fund_id'] ?? $cg['mf_id'] ?? '' ?>"
                                        data-scheme-name="<?= esc($cg['scheme_name']) ?>"
                                        data-gain-type="<?= esc($cg['gain_type']) ?>"
                                        data-date="<?= $cg['redeem_date'] ?>"
                                        data-units="<?= $units ?>"
                                        data-cost="<?= $cost ?>"
                                        data-proceeds="<?= $proceeds ?>"
                                        data-gain="<?= $gain ?>">
                                        <td>
                                            <strong><?= esc($cg['scheme_name']) ?></strong>
                                            <div class="text-muted" style="font-size: 0.75rem;">Folio: <?= esc($cg['folio_number']) ?></div>
                                        </td>
                                        <td><?= date('d-M-Y', strtotime($cg['buy_date'])) ?></td>
                                        <td><?= date('d-M-Y', strtotime($cg['redeem_date'])) ?></td>
                                        <td class="text-center fw-medium"><?= $cg['holding_days'] ?> days</td>
                                        <td class="text-center">
                                            <?php if ($cg['gain_type'] === 'LTCG'): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                    LTCG (&gt;365d)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">
                                                    STCG (&le;365d)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-semibold"><?= number_format($units, 4) ?></td>
                                        <td class="text-end text-muted"><?= format_inr($cg['buy_nav']) ?></td>
                                        <td class="text-end fw-medium"><?= format_inr($cg['redeem_nav']) ?></td>
                                        <td class="text-end">
                                            <?= format_pnl($gain) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-group-divider bg-light fw-semibold" id="mfTaxFooter">
                                <tr>
                                    <td colspan="5" class="py-3">
                                        <span class="fw-bold text-dark" id="footerTaxSummaryTitle">Total (<?= count($capitalGains) ?> Lots)</span>
                                        <span class="ms-2 badge bg-light text-secondary border fw-normal" id="footerTaxBreakdown">
                                            All FIFO lots
                                        </span>
                                    </td>
                                    <td class="text-end py-3 fw-bold" id="footerTaxUnits"><?= number_format($totalMatchedUnits, 4) ?></td>
                                    <td class="text-end py-3 text-muted" id="footerTaxCost"><?= format_inr($totalBuyCost) ?></td>
                                    <td class="text-end py-3 text-muted" id="footerTaxProceeds"><?= format_inr($totalSellProceeds) ?></td>
                                    <td class="text-end py-3 fs-6 fw-bold <?= $totalRealizedGain >= 0 ? 'text-success' : 'text-danger' ?>" id="footerTaxGain">
                                        <?= ($totalRealizedGain >= 0 ? '+' : '') . format_inr($totalRealizedGain) ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'mfTax']) ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MINIMAL MUTUAL FUND TRANSACTION POPUP MODAL (Buy More / Redeem FIFO)      -->
<!-- ========================================================================= -->
<div class="modal fade" id="mfTransactionModal" tabindex="-1" aria-labelledby="mfTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="mfTransactionModalLabel">
                        Add Transaction: <span id="modalMfCode" class="text-success"></span>
                    </h5>
                    <div class="text-muted small" id="modalMfSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('mutual-funds/transaction') ?>" method="POST" id="mfTransForm">
                <?= csrf_field() ?>
                <input type="hidden" name="mutual_fund_id" id="modalMfId" value="">

                <div class="modal-body p-4">
                    <!-- Segmented Action Tabs -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actMfBuy" value="BUY" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actMfBuy">
                            <i class="bi bi-cart-plus me-1"></i>Buy (SIP / Lumpsum)
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actMfRedeem" value="REDEEM" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actMfRedeem">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Redeem (FIFO)
                        </label>
                    </div>

                    <!-- SUB-FORM 1: BUY (SIP / LUMPSUM) -->
                    <div id="panelMfBuy" class="mf-trans-panel">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Purchase Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="buy_type" id="buyMfType" required>
                                    <option value="BUY_LUMPSUM" selected>Lumpsum Purchase</option>
                                    <option value="BUY_SIP">SIP Installment</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="buyMfDate" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Invested Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount" id="buyMfAmount" min="1" step="0.01" placeholder="e.g. 5000" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Allotted Units <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="units" id="buyMfUnits" min="0.0001" step="0.0001" placeholder="0.0000" required>
                            </div>

                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Allotment NAV (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="nav" id="buyMfNav" min="0.0001" step="0.0001" placeholder="0.0000" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Stamp Duty (0.005%) (₹)</label>
                                <input type="number" class="form-control" name="charges" id="buyMfCharges" min="0" step="0.01" value="0.00">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Monthly automated SIP allotment">
                            </div>
                        </div>

                        <!-- Buy Summary Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Total Cash Outflow:</span>
                                <span class="fw-bold text-success" id="buyMfTotalPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-FORM 2: REDEEM (FIFO) -->
                    <div id="panelMfRedeem" class="mf-trans-panel d-none">
                        <div class="alert alert-warning py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Available Units in Folio:</span>
                            <strong id="redeemMfAvailableUnitsBadge">0.0000 units</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Redemption Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="redeemMfDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Units to Redeem <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="units" id="redeemMfUnits" min="0.0001" step="0.0001" placeholder="Units" disabled>
                            </div>

                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Redemption NAV (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="nav" id="redeemMfNav" min="0.0001" step="0.0001" placeholder="0.0000" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Exit Load / Charges (₹)</label>
                                <input type="number" class="form-control" name="charges" id="redeemMfCharges" min="0" step="0.01" value="0.00" disabled>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Goal attainment or rebalancing" disabled>
                            </div>
                        </div>

                        <!-- Redeem Proceeds Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Estimated Net Proceeds:</span>
                                <span class="fw-bold text-dark" id="redeemMfTotalPreview">₹ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Realized P&L:</span>
                                <span class="small fw-semibold" id="redeemMfPnlPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitMfTrans">
                        Confirm Purchase Lot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT MUTUAL FUND SCHEME METADATA MODAL                                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="editMfModal" tabindex="-1" aria-labelledby="editMfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editMfModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Scheme Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('mutual-funds/update-fund') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="fund_id" id="editMfId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Scheme Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="scheme_name" id="editMfSchemeName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" id="editMfCategory" required>
                                <option value="Equity - Large Cap">Equity - Large Cap</option>
                                <option value="Equity - Mid Cap">Equity - Mid Cap</option>
                                <option value="Equity - Small Cap">Equity - Small Cap</option>
                                <option value="Equity - Flexi Cap">Equity - Flexi Cap</option>
                                <option value="Equity - ELSS">Equity - ELSS</option>
                                <option value="Hybrid - Aggressive">Hybrid - Aggressive</option>
                                <option value="Hybrid - Balanced Advantage">Hybrid - Balanced Advantage</option>
                                <option value="Debt - Liquid">Debt - Liquid</option>
                                <option value="Debt - Short Term">Debt - Short Term</option>
                                <option value="Index Fund">Index Fund</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Folio Number</label>
                            <input type="text" class="form-control" name="folio_number" id="editMfFolio">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Fund House (AMC)</label>
                            <input type="text" class="form-control" name="fund_house" id="editMfFundHouse" placeholder="e.g. Parag Parikh, HDFC">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">AMFI Scheme Code</label>
                            <input type="text" class="form-control font-monospace" name="amfi_code" id="editMfAmfiCode" placeholder="e.g. 122639">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ISIN Code</label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="isin" id="editMfIsin" placeholder="e.g. INF846K01164">
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
<!-- EDIT MUTUAL FUND TRANSACTION MODAL (WITH FIFO REBUILD)                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="editMfTransModal" tabindex="-1" aria-labelledby="editMfTransModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editMfTransModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i><span id="editMfTransTitle">Edit Transaction</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('mutual-funds/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editMfTransId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Saving changes will automatically <strong>rebalance FIFO lots and recalculate capital gains</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editMfTransDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Units <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="units" id="editMfTransUnits" min="0.0001" step="0.0001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NAV (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="nav" id="editMfTransNav" min="0.0001" step="0.0001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Stamp Duty / Charges (₹)</label>
                            <input type="number" class="form-control" name="charges" id="editMfTransCharges" min="0" step="0.01">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editMfTransNotes">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">Update &amp; Rebalance FIFO</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    window.MUTUAL_FUNDS_CONFIG = {
        fyRanges: <?= json_encode(get_fy_ranges()) ?>
    };
</script>
<script src="<?= base_url('assets/js/mutual_funds.js') ?>"></script>

<?= $this->endSection() ?>

