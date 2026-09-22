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
        <a href="<?= base_url('mutual-funds/new') ?>" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i>Add New Scheme
        </a>
    </div>
</div>

<!-- 5 Executive KPI Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Current Portfolio Value -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Portfolio Value (NAV)</div>
            <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_current_value']) ?></div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                <i class="bi bi-check-circle-fill text-success me-1"></i><?= $summary['active_holdings_count'] ?> Active Schemes
            </div>
        </div>
    </div>

    <!-- Total Invested Capital -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Invested Capital</div>
            <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_invested']) ?></div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">FIFO Cost Basis</div>
        </div>
    </div>

    <!-- Total Unrealized P&L -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Unrealized P&L</div>
            <div class="fs-5 fw-bold">
                <?= format_pnl($summary['total_unrealized_pnl'], $summary['unrealized_pnl_percent']) ?>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Open Units</div>
        </div>
    </div>

    <!-- Realized P&L (FIFO) -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
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
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
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
                        <strong>Indian Income Tax Section 45 FIFO Rules:</strong> Mutual fund units are redeemed in First-In, First-Out order.
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
document.addEventListener('DOMContentLoaded', function() {
    let currentMf = {};

    // Hook Edit Scheme buttons
    document.querySelectorAll('.open-edit-mf-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editMfId').value = this.dataset.id;
            document.getElementById('editMfSchemeName').value = this.dataset.name;
            document.getElementById('editMfCategory').value = this.dataset.category;
            document.getElementById('editMfFolio').value = this.dataset.folio || '';
            document.getElementById('editMfFundHouse').value = this.dataset.amc || '';
            document.getElementById('editMfAmfiCode').value = this.dataset.amfi || '';
            document.getElementById('editMfIsin').value = this.dataset.isin || '';
        });
    });

    // Hook Edit Transaction buttons
    document.querySelectorAll('.open-edit-mf-trans').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editMfTransId').value = this.dataset.id;
            document.getElementById('editMfTransTitle').textContent = 'Edit ' + this.dataset.type + ': ' + this.dataset.name;
            document.getElementById('editMfTransDate').value = this.dataset.date;
            document.getElementById('editMfTransUnits').value = parseFloat(this.dataset.units).toFixed(4);
            document.getElementById('editMfTransNav').value = parseFloat(this.dataset.nav).toFixed(4);
            document.getElementById('editMfTransCharges').value = parseFloat(this.dataset.charges || 0).toFixed(2);
            document.getElementById('editMfTransNotes').value = this.dataset.notes || '';
        });
    });

    // 1. Hook "Add Trans" buttons to populate modal
    document.querySelectorAll('.open-mf-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentMf = {
                id: this.dataset.id,
                code: this.dataset.code,
                name: this.dataset.name,
                folio: this.dataset.folio,
                units: parseFloat(this.dataset.units) || 0,
                nav: parseFloat(this.dataset.nav) || 0,
                avgNav: parseFloat(this.dataset.avg) || 0
            };

            document.getElementById('modalMfId').value = currentMf.id;
            document.getElementById('modalMfCode').textContent = currentMf.code;
            document.getElementById('modalMfSubtitle').textContent = currentMf.name + ' (Folio: ' + currentMf.folio + ')';
            
            // Set defaults in inputs
            const buyTypeEl = document.getElementById('buyMfType');
            if (buyTypeEl) buyTypeEl.value = 'BUY_LUMPSUM';
            document.getElementById('buyMfNav').value = currentMf.nav.toFixed(4);
            document.getElementById('buyMfAmount').value = '';
            document.getElementById('buyMfUnits').value = '';
            document.getElementById('buyMfCharges').value = '0.00';
            document.getElementById('buyMfTotalPreview').textContent = '₹ 0.00';

            document.getElementById('redeemMfNav').value = currentMf.nav.toFixed(4);
            document.getElementById('redeemMfAvailableUnitsBadge').textContent = currentMf.units.toFixed(4) + ' units';
            document.getElementById('redeemMfUnits').max = currentMf.units;
            document.getElementById('redeemMfUnits').value = '';
            document.getElementById('redeemMfCharges').value = '0.00';
            document.getElementById('redeemMfTotalPreview').textContent = '₹ 0.00';
            document.getElementById('redeemMfPnlPreview').textContent = '₹ 0.00';

            // Reset to Buy panel
            document.getElementById('actMfBuy').checked = true;
            switchMfAction('BUY');
        });
    });

    // 2. Action Tab Switching (Buy More / Redeem)
    const actRadios = document.querySelectorAll('input[name="action_type"]');
    actRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            switchMfAction(this.value);
        });
    });

    function switchMfAction(type) {
        const pBuy = document.getElementById('panelMfBuy');
        const pRedeem = document.getElementById('panelMfRedeem');
        const submitBtn = document.getElementById('btnSubmitMfTrans');

        if (type === 'BUY') {
            pBuy.classList.remove('d-none');
            pRedeem.classList.add('d-none');
            setMfInputsDisabled(pBuy, false);
            setMfInputsDisabled(pRedeem, true);
            submitBtn.className = 'btn btn-success btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Confirm Purchase Lot';
        } else if (type === 'REDEEM') {
            pBuy.classList.add('d-none');
            pRedeem.classList.remove('d-none');
            setMfInputsDisabled(pBuy, true);
            setMfInputsDisabled(pRedeem, false);
            submitBtn.className = 'btn btn-danger btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Execute FIFO Redeem';
        }
    }

    function setMfInputsDisabled(container, disabled) {
        container.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    // 3. Live calculations in BUY modal
    const buyAmount = document.getElementById('buyMfAmount');
    const buyUnits = document.getElementById('buyMfUnits');
    const buyNav = document.getElementById('buyMfNav');
    const buyCharges = document.getElementById('buyMfCharges');
    const buyTotalPreview = document.getElementById('buyMfTotalPreview');

    let activeInputSource = 'units';

    function calcBuyTotal() {
        const amt = parseFloat(buyAmount ? buyAmount.value : 0) || 0;
        const chg = parseFloat(buyCharges ? buyCharges.value : 0) || 0;
        const total = amt + chg;
        if (buyTotalPreview) {
            buyTotalPreview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }

    function recalcFromUnits() {
        const u = parseFloat(buyUnits ? buyUnits.value : 0) || 0;
        const nav = parseFloat(buyNav ? buyNav.value : 0) || 0;
        if (u > 0 && nav > 0) {
            const grossAmt = +(u * nav).toFixed(2);
            if (buyAmount) buyAmount.value = grossAmt.toFixed(2);
            const stampDuty = +(grossAmt * 0.00005).toFixed(2);
            if (buyCharges) buyCharges.value = stampDuty.toFixed(2);
        } else if (u === 0) {
            if (buyAmount) buyAmount.value = '';
            if (buyCharges) buyCharges.value = '0.00';
        }
        calcBuyTotal();
    }

    function recalcFromAmount() {
        const amt = parseFloat(buyAmount ? buyAmount.value : 0) || 0;
        const nav = parseFloat(buyNav ? buyNav.value : 0) || 0;
        const stampDuty = +(amt * 0.00005).toFixed(2);
        if (buyCharges) buyCharges.value = stampDuty.toFixed(2);

        if (amt > 0 && nav > 0) {
            const units = amt / nav;
            if (buyUnits) buyUnits.value = units.toFixed(4);
        } else if (amt === 0) {
            if (buyUnits) buyUnits.value = '';
        }
        calcBuyTotal();
    }

    if (buyUnits) {
        buyUnits.addEventListener('input', function() {
            activeInputSource = 'units';
            recalcFromUnits();
        });
    }

    if (buyAmount) {
        buyAmount.addEventListener('input', function() {
            activeInputSource = 'amount';
            recalcFromAmount();
        });
    }

    if (buyNav) {
        buyNav.addEventListener('input', function() {
            if (activeInputSource === 'units' && buyUnits && parseFloat(buyUnits.value) > 0) {
                recalcFromUnits();
            } else if (buyAmount && parseFloat(buyAmount.value) > 0) {
                recalcFromAmount();
            } else if (buyUnits && parseFloat(buyUnits.value) > 0) {
                recalcFromUnits();
            }
        });
    }

    if (buyCharges) {
        buyCharges.addEventListener('input', calcBuyTotal);
    }

    // 4. Live calculations in REDEEM modal
    const redeemUnits = document.getElementById('redeemMfUnits');
    const redeemNav = document.getElementById('redeemMfNav');
    const redeemCharges = document.getElementById('redeemMfCharges');
    const redeemPreview = document.getElementById('redeemMfTotalPreview');
    const redeemPnlPreview = document.getElementById('redeemMfPnlPreview');

    function calcRedeem() {
        const u = parseFloat(redeemUnits.value) || 0;
        const nav = parseFloat(redeemNav.value) || 0;
        const chg = parseFloat(redeemCharges.value) || 0;

        const grossProceeds = u * nav;
        const netProceeds = Math.max(0, grossProceeds - chg);
        const costBasis = u * (currentMf.avgNav || 0);
        const estGain = netProceeds - costBasis;

        redeemPreview.textContent = '₹ ' + netProceeds.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const sign = estGain >= 0 ? '+' : '';
        redeemPnlPreview.textContent = sign + '₹ ' + estGain.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        redeemPnlPreview.className = 'small fw-semibold ' + (estGain >= 0 ? 'text-success' : 'text-danger');
    }

    [redeemUnits, redeemNav, redeemCharges].forEach(el => el.addEventListener('input', calcRedeem));

    // =========================================================================
    // 5. TRADE LEDGER: DUAL FILTER (SCHEME + FY) & LIVE TOTAL AMOUNTS
    // =========================================================================
    const FY_RANGES = <?= json_encode(get_fy_ranges()) ?>;

    const ledgerFundFilter = document.getElementById('ledgerFundFilter');
    const ledgerDateFilter = document.getElementById('ledgerDateFilter');
    const ledgerTable      = document.getElementById('mfLedgerTable');
    const ledgerResetBtn   = document.getElementById('btnResetFundFilter');
    const ledgerBadge      = document.getElementById('filteredFundBadge');
    const ledgerNameSpan   = document.getElementById('filteredFundName');
    const ledgerNoRowsEl   = document.getElementById('ledgerNoRows');

    // KPI Elements
    const kpiBuyAmount      = document.getElementById('kpiBuyAmount');
    const kpiBuyDetails     = document.getElementById('kpiBuyDetails');
    const kpiRedeemAmount   = document.getElementById('kpiRedeemAmount');
    const kpiRedeemDetails  = document.getElementById('kpiRedeemDetails');
    const kpiNetOutlay      = document.getElementById('kpiNetOutlay');
    const kpiNetDetails     = document.getElementById('kpiNetDetails');
    const kpiCharges        = document.getElementById('kpiCharges');

    // Footer Elements
    const footerSummaryTitle     = document.getElementById('footerSummaryTitle');
    const footerActionBreakdown  = document.getElementById('footerActionBreakdown');
    const footerTotalUnits       = document.getElementById('footerTotalUnits');
    const footerTotalCharges     = document.getElementById('footerTotalCharges');
    const footerTotalAmount      = document.getElementById('footerTotalAmount');
    const footerNote             = document.getElementById('footerNote');

    function formatINR(val) {
        return '₹ ' + Number(val).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function applyLedgerFilters(updateUrl = true) {
        if (!ledgerTable) return;

        const selectedFund   = ledgerFundFilter ? ledgerFundFilter.value : 'ALL';
        const selectedFy     = ledgerDateFilter ? ledgerDateFilter.value : 'ALL';
        const selectedOption = ledgerFundFilter ? ledgerFundFilter.options[ledgerFundFilter.selectedIndex] : null;
        const selectedName   = selectedOption ? (selectedOption.dataset.name || '') : '';

        let fyStart = null;
        let fyEnd   = null;
        if (selectedFy === 'CURRENT_FY' && FY_RANGES.current) {
            fyStart = FY_RANGES.current.start;
            fyEnd   = FY_RANGES.current.end;
        } else if (selectedFy === 'LAST_FY' && FY_RANGES.last) {
            fyStart = FY_RANGES.last.start;
            fyEnd   = FY_RANGES.last.end;
        }

        const rows = ledgerTable.querySelectorAll('tbody tr.mf-ledger-row');
        let visibleCount = 0;
        let buyAmount    = 0.0;
        let redeemAmount = 0.0;
        let buyUnits     = 0.0;
        let redeemUnits  = 0.0;
        let buyCount     = 0;
        let redeemCount  = 0;
        let totalCharges = 0.0;

        rows.forEach(row => {
            const rowFundId = row.dataset.fundId;
            const rowDate   = row.dataset.date;
            const rowType   = row.dataset.type;
            const rowUnits  = parseFloat(row.dataset.units) || 0.0;
            const rowAmt    = parseFloat(row.dataset.amount) || 0.0;
            const rowChg    = parseFloat(row.dataset.charges) || 0.0;

            const matchesFund = (selectedFund === 'ALL' || rowFundId === selectedFund);
            let matchesDate   = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesFund && matchesDate) {
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;
                totalCharges += rowChg;

                if (rowType === 'BUY_SIP' || rowType === 'BUY_LUMPSUM') {
                    buyAmount += rowAmt;
                    buyUnits  += rowUnits;
                    buyCount++;
                } else {
                    redeemAmount += rowAmt;
                    redeemUnits  += rowUnits;
                    redeemCount++;
                }
            } else {
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        if (ledgerNoRowsEl) {
            ledgerNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        const netOutlay = buyAmount - redeemAmount;

        // Update KPI Cards
        if (kpiBuyAmount) kpiBuyAmount.textContent = formatINR(buyAmount);
        if (kpiBuyDetails) kpiBuyDetails.textContent = `${buyCount} orders • ${buyUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 })} units`;

        if (kpiRedeemAmount) kpiRedeemAmount.textContent = formatINR(redeemAmount);
        if (kpiRedeemDetails) kpiRedeemDetails.textContent = `${redeemCount} redemptions • ${redeemUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 })} units`;

        if (kpiNetOutlay) {
            kpiNetOutlay.textContent = formatINR(netOutlay);
            kpiNetOutlay.className = 'fw-bold mb-0 mt-1 ' + (netOutlay >= 0 ? 'text-primary' : 'text-success');
        }
        if (kpiNetDetails) {
            if (selectedFund === 'ALL') {
                kpiNetDetails.textContent = 'Net capital outlay in period';
            } else {
                const heldDiff = buyUnits - redeemUnits;
                kpiNetDetails.textContent = `Net units from trades: ${heldDiff.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 })}`;
            }
        }

        if (kpiCharges) kpiCharges.textContent = formatINR(totalCharges);

        // Update Footer Totals
        if (footerSummaryTitle) {
            let label = selectedFund === 'ALL' ? 'Total' : selectedName;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerSummaryTitle.textContent = `${label} (${visibleCount} Trades)`;
        }

        if (footerActionBreakdown) {
            footerActionBreakdown.innerHTML = `<span class="text-success">${buyCount} BUY</span> &bull; <span class="text-danger">${redeemCount} REDEEM</span>`;
        }

        if (footerTotalUnits) footerTotalUnits.textContent = (buyUnits + redeemUnits).toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
        if (footerTotalCharges) footerTotalCharges.textContent = formatINR(totalCharges);
        if (footerTotalAmount) {
            footerTotalAmount.textContent = formatINR(netOutlay);
            footerTotalAmount.className = 'text-end fw-bold fs-6 ' + (netOutlay >= 0 ? 'text-dark' : 'text-success');
        }
        if (footerNote) footerNote.textContent = netOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized';

        // Refresh pagination slice
        if (window.mfLedgerPager) {
            window.mfLedgerPager.refresh();
        }

        // Toggle reset button & filtered badge
        const hasActiveFilter = (selectedFund !== 'ALL' || selectedFy !== 'ALL');
        if (ledgerResetBtn) ledgerResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (ledgerBadge) {
            ledgerBadge.classList.toggle('d-none', !hasActiveFilter);
            if (ledgerNameSpan) {
                let badgeText = selectedFund !== 'ALL' ? selectedName : 'All Schemes';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                ledgerNameSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedFund === 'ALL') url.searchParams.delete('fund_id');
            else url.searchParams.set('fund_id', selectedFund);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (ledgerFundFilter) ledgerFundFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerDateFilter) ledgerDateFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerResetBtn) {
        ledgerResetBtn.addEventListener('click', () => {
            if (ledgerFundFilter) ledgerFundFilter.value = 'ALL';
            if (ledgerDateFilter) ledgerDateFilter.value = 'ALL';
            applyLedgerFilters(true);
        });
    }

    // Shortcut from Holdings dropdown
    window.showMfLedger = function(fundId) {
        const transTabBtn = document.getElementById('transactions-tab');
        if (transTabBtn) {
            bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        }
        if (ledgerFundFilter) {
            ledgerFundFilter.value = String(fundId);
            applyLedgerFilters(true);
        }
    };

    // ==========================================
    // FIFO TAX LOG FILTERING (Scheme, Gain Type, Date Range)
    // ==========================================
    const taxMfFilter    = document.getElementById('taxLogMfFilter');
    const taxTypeFilter  = document.getElementById('taxLogTypeFilter');
    const taxDateFilter  = document.getElementById('taxLogDateFilter');
    const taxResetBtn    = document.getElementById('btnResetTaxFilter');
    const taxBadge       = document.getElementById('filteredTaxBadge');
    const taxSchemeSpan  = document.getElementById('filteredTaxSchemeSpan');
    const taxTable       = document.getElementById('mfTaxLogTable');

    // KPI Cards
    const kpiTaxUnits     = document.getElementById('kpiTaxUnits');
    const kpiTaxQtyCount  = document.getElementById('kpiTaxQtyCount');
    const kpiTaxCost      = document.getElementById('kpiTaxCost');
    const kpiTaxProceeds  = document.getElementById('kpiTaxProceeds');
    const kpiTaxGain      = document.getElementById('kpiTaxGain');
    const kpiTaxBreakdown = document.getElementById('kpiTaxBreakdown');

    // Dynamic Footer
    const footerTaxSummaryTitle = document.getElementById('footerTaxSummaryTitle');
    const footerTaxBreakdown    = document.getElementById('footerTaxBreakdown');
    const footerTaxUnits        = document.getElementById('footerTaxUnits');
    const footerTaxCost         = document.getElementById('footerTaxCost');
    const footerTaxProceeds     = document.getElementById('footerTaxProceeds');
    const footerTaxGain         = document.getElementById('footerTaxGain');

    function applyMfTaxFilters(updateUrl = true) {
        if (!taxTable) return;

        const selectedMf   = taxMfFilter ? taxMfFilter.value : 'ALL';
        const selectedType = taxTypeFilter ? taxTypeFilter.value : 'ALL';
        const selectedFy   = taxDateFilter ? taxDateFilter.value : 'ALL';

        const rows = taxTable.querySelectorAll('tbody tr.mf-taxlog-row');
        let visibleCount = 0;
        let totalMatchedUnits = 0;
        let totalBuyCost = 0;
        let totalSellProceeds = 0;
        let totalRealizedGain = 0;
        let totalStcg = 0;
        let totalLtcg = 0;
        let selectedSchemeName = '';

        if (selectedMf !== 'ALL' && taxMfFilter) {
            const opt = taxMfFilter.querySelector(`option[value="${selectedMf}"]`);
            if (opt) selectedSchemeName = opt.dataset.name || opt.textContent.split('(')[0].trim();
        }

        rows.forEach(row => {
            const rowMfId     = row.dataset.mfId;
            const rowGainType = row.dataset.gainType;
            const rowDate     = row.dataset.date;

            const matchesMf   = (selectedMf === 'ALL' || rowMfId === selectedMf);
            const matchesType = (selectedType === 'ALL' || rowGainType === selectedType);
            const matchesDate = matchFy(rowDate, selectedFy);

            if (matchesMf && matchesType && matchesDate) {
                row.classList.remove('d-none');
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;

                const units    = parseFloat(row.dataset.units) || 0;
                const cost     = parseFloat(row.dataset.cost) || 0;
                const proceeds = parseFloat(row.dataset.proceeds) || 0;
                const gain     = parseFloat(row.dataset.gain) || 0;

                totalMatchedUnits += units;
                totalBuyCost      += cost;
                totalSellProceeds += proceeds;
                totalRealizedGain += gain;

                if (rowGainType === 'LTCG') {
                    totalLtcg += gain;
                } else {
                    totalStcg += gain;
                }
            } else {
                row.classList.add('d-none');
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        // Update 4 KPI Cards
        if (kpiTaxUnits) kpiTaxUnits.textContent = totalMatchedUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
        if (kpiTaxQtyCount) kpiTaxQtyCount.textContent = `${visibleCount} FIFO lots`;
        if (kpiTaxCost) kpiTaxCost.textContent = formatINR(totalBuyCost);
        if (kpiTaxProceeds) kpiTaxProceeds.textContent = formatINR(totalSellProceeds);
        if (kpiTaxGain) {
            kpiTaxGain.textContent = (totalRealizedGain >= 0 ? '+' : '') + formatINR(totalRealizedGain);
            kpiTaxGain.className = 'fs-5 fw-bold mb-0 mt-1 ' + (totalRealizedGain >= 0 ? 'text-success' : 'text-danger');
            const cardWrapper = kpiTaxGain.closest('.card');
            if (cardWrapper) {
                cardWrapper.classList.remove('border-success', 'border-danger');
                cardWrapper.classList.add(totalRealizedGain >= 0 ? 'border-success' : 'border-danger');
            }
        }
        if (kpiTaxBreakdown) {
            kpiTaxBreakdown.textContent = `STCG: ${formatINR(totalStcg)} • LTCG: ${formatINR(totalLtcg)}`;
        }

        // Update Dynamic Footer
        if (footerTaxSummaryTitle) {
            let label = selectedMf === 'ALL' ? 'Total' : selectedSchemeName;
            if (selectedType !== 'ALL') label += ` [${selectedType}]`;
            if (selectedFy !== 'ALL') label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            footerTaxSummaryTitle.textContent = `${label} (${visibleCount} Lots)`;
        }
        if (footerTaxBreakdown) {
            footerTaxBreakdown.textContent = `STCG: ${formatINR(totalStcg)} • LTCG: ${formatINR(totalLtcg)}`;
        }
        if (footerTaxUnits) footerTaxUnits.textContent = totalMatchedUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
        if (footerTaxCost) footerTaxCost.textContent = formatINR(totalBuyCost);
        if (footerTaxProceeds) footerTaxProceeds.textContent = formatINR(totalSellProceeds);
        if (footerTaxGain) {
            footerTaxGain.textContent = (totalRealizedGain >= 0 ? '+' : '') + formatINR(totalRealizedGain);
            footerTaxGain.className = 'text-end py-3 fs-6 fw-bold ' + (totalRealizedGain >= 0 ? 'text-success' : 'text-danger');
        }

        // Refresh pagination slice
        if (window.mfTaxPager) {
            window.mfTaxPager.refresh();
        }

        // Toggle reset button & active filter badge
        const hasActiveFilter = (selectedMf !== 'ALL' || selectedType !== 'ALL' || selectedFy !== 'ALL');
        if (taxResetBtn) taxResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (taxBadge) {
            taxBadge.classList.toggle('d-none', !hasActiveFilter);
            if (taxSchemeSpan) {
                let badgeParts = [];
                if (selectedMf !== 'ALL') badgeParts.push(selectedSchemeName);
                if (selectedType !== 'ALL') badgeParts.push(selectedType);
                if (selectedFy !== 'ALL') badgeParts.push(selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                taxSchemeSpan.textContent = badgeParts.join(' • ');
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedMf === 'ALL') url.searchParams.delete('tax_mf_id');
            else url.searchParams.set('tax_mf_id', selectedMf);

            if (selectedType === 'ALL') url.searchParams.delete('tax_type');
            else url.searchParams.set('tax_type', selectedType);

            if (selectedFy === 'ALL') url.searchParams.delete('tax_fy');
            else url.searchParams.set('tax_fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (taxMfFilter)   taxMfFilter.addEventListener('change', () => applyMfTaxFilters(true));
    if (taxTypeFilter) taxTypeFilter.addEventListener('change', () => applyMfTaxFilters(true));
    if (taxDateFilter) taxDateFilter.addEventListener('change', () => applyMfTaxFilters(true));
    if (taxResetBtn) {
        taxResetBtn.addEventListener('click', () => {
            if (taxMfFilter)   taxMfFilter.value = 'ALL';
            if (taxTypeFilter) taxTypeFilter.value = 'ALL';
            if (taxDateFilter) taxDateFilter.value = 'ALL';
            applyMfTaxFilters(true);
        });
    }

    // Deep link handling on page load
    const urlParams = new URLSearchParams(window.location.search);
    const initialFundId = urlParams.get('fund_id');
    const initialFy     = urlParams.get('fy');
    const initialTab    = urlParams.get('tab');
    const hash          = window.location.hash;

    if (initialTab === 'past-holdings' || hash === '#past-holdings') {
        const pastTabBtn = document.getElementById('past-holdings-tab');
        if (pastTabBtn) {
            bootstrap.Tab.getOrCreateInstance(pastTabBtn).show();
        }
    }

    if (initialFundId && ledgerFundFilter) {
        ledgerFundFilter.value = initialFundId;
    }
    if (initialFy && ledgerDateFilter) {
        ledgerDateFilter.value = initialFy;
    }
    if ((initialFundId || initialFy || initialTab === 'transactions' || hash === '#transactions') && ledgerTable) {
        applyLedgerFilters(false);
        if (initialTab === 'transactions' || initialFundId || hash === '#transactions') {
            const transTabBtn = document.getElementById('transactions-tab');
            if (transTabBtn) {
                bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
            }
        }
    }

    const taxMfParam   = urlParams.get('tax_mf_id');
    const taxTypeParam = urlParams.get('tax_type');
    const taxFyParam   = urlParams.get('tax_fy');
    if (taxMfParam || taxTypeParam || taxFyParam || initialTab === 'capitalgains' || hash === '#capitalgains') {
        const taxTabBtn = document.getElementById('capitalgains-tab');
        if (taxTabBtn && (initialTab === 'capitalgains' || hash === '#capitalgains')) {
            bootstrap.Tab.getOrCreateInstance(taxTabBtn).show();
        }
        if (taxMfParam && taxMfFilter) taxMfFilter.value = taxMfParam;
        if (taxTypeParam && taxTypeFilter) taxTypeFilter.value = taxTypeParam;
        if (taxFyParam && taxDateFilter) taxDateFilter.value = taxFyParam;
        applyMfTaxFilters(false);
    }

    // ==========================================
    // TABLE PAGINATION INITIALIZATION
    // ==========================================
    if (typeof window.initTablePagination === 'function') {
        window.mfLedgerPager = window.initTablePagination({
            tableId: 'mfLedgerTable',
            rowSelector: '.mf-ledger-row',
            infoId: 'mfLedgerPageInfo',
            sizeSelectId: 'mfLedgerPageSize',
            controlsId: 'mfLedgerPageControls'
        });

        window.mfTaxPager = window.initTablePagination({
            tableId: 'mfTaxLogTable',
            rowSelector: '.mf-taxlog-row',
            infoId: 'mfTaxPageInfo',
            sizeSelectId: 'mfTaxPageSize',
            controlsId: 'mfTaxPageControls'
        });
    }
});
</script>

<?= $this->endSection() ?>

