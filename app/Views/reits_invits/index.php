<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header & Top Action Buttons -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">InvITs & REITs</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">InvITs & REITs Portfolio</h3>
        <small class="text-muted">Real Estate Investment Trusts & Infrastructure Investment Trusts (NSE / BSE)</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('reits-invits/refresh-prices') ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh Live CMP
        </a>
        <a href="<?= base_url('reits-invits/new') ?>" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i>Add New Trust
        </a>
    </div>
</div>

<!-- Flash Alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm py-2 px-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm py-2 px-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('info')): ?>
    <div class="alert alert-info alert-dismissible fade show rounded-3 shadow-sm py-2 px-3 mb-4" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i><?= session()->getFlashdata('info') ?>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm py-2 px-3 mb-4" role="alert">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i>Please correct the following:</div>
        <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- 5 Executive KPI Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Current Portfolio Value -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Portfolio Value (CMP)</div>
            <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_current_value']) ?></div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                <i class="bi bi-check-circle-fill text-success me-1"></i><?= $summary['active_holdings_count'] ?> Active Holdings
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

    <!-- Cumulative Distributions Earned -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Distributions Earned</div>
            <div class="fs-5 fw-bold text-success">
                <?= format_inr($summary['total_distributions_earned']) ?>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Interest + Div + ROC (Net)</div>
        </div>
    </div>

    <!-- Unrealized Capital Gains -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Unrealized Capital P&L</div>
            <div class="fs-5 fw-bold">
                <?= format_pnl($summary['total_unrealized_pnl'], $summary['unrealized_pnl_percent']) ?>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Open Units</div>
        </div>
    </div>

    <!-- Total Net Return -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Total Net Return</div>
            <div class="fs-5 fw-bold">
                <?= format_pnl($summary['total_net_return']) ?>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Capital P&L + Payouts</div>
        </div>
    </div>
</div>

<!-- Main Tabs Section -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-3 px-4 rounded-top-4">
        <ul class="nav nav-tabs card-header-tabs border-0" id="reitsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="holdings-tab" data-bs-toggle="tab" data-bs-target="#holdings" type="button" role="tab">
                    <i class="bi bi-buildings me-1 text-info"></i>Active Holdings (<?= count($holdings) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="distributions-tab" data-bs-toggle="tab" data-bs-target="#distributions" type="button" role="tab">
                    <i class="bi bi-cash-coin me-1 text-success"></i>Distribution Journal (<?= count($distributions) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="capitalgains-tab" data-bs-toggle="tab" data-bs-target="#capitalgains" type="button" role="tab">
                    <i class="bi bi-clock-history me-1 text-primary"></i>FIFO Tax Log (<?= count($capitalGains) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
                    <i class="bi bi-receipt me-1 text-secondary"></i>Transaction History (<?= count($transactions) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="reitsTabContent">
            
            <!-- TAB 1: ACTIVE HOLDINGS -->
            <div class="tab-pane fade show active p-3" id="holdings" role="tabpanel">
                <?php if (empty($holdings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-buildings fs-1 text-muted mb-3 d-block"></i>
                        <h5>No REIT or InvIT Holdings Found</h5>
                        <p class="text-muted small">You haven't added any REITs or InvITs yet. Click below to add your first holding!</p>
                        <a href="<?= base_url('reits-invits/new') ?>" class="btn btn-primary btn-sm rounded-3">
                            <i class="bi bi-plus-lg me-1"></i>Add New Trust
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Active Holdings Sorting Toolbar -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($holdings) ?></strong> active trusts
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="sortReitsSelect" class="small text-muted mb-0 fw-semibold text-nowrap"><i class="bi bi-sort-down me-1"></i>Sort By:</label>
                            <select id="sortReitsSelect" class="form-select form-select-sm shadow-none border-secondary-subtle" style="width: auto;">
                                <option value="invested_desc" selected>Invested (High → Low)</option>
                                <option value="invested_asc">Invested (Low → High)</option>
                                <option value="current_desc">Current (High → Low)</option>
                                <option value="current_asc">Current (Low → High)</option>
                                <option value="security_asc">Security (A → Z)</option>
                                <option value="security_desc">Security (Z → A)</option>
                                <option value="type_asc">Type (InvIT First)</option>
                                <option value="type_desc">Type (REIT First)</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="reitHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="cursor-pointer sortable-reit-th" data-col="security" role="button" title="Click to sort by Security">
                                        Security / Trust <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="cursor-pointer sortable-reit-th" data-col="type" role="button" title="Click to sort by Type">
                                        Type <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Units</th>
                                    <th class="text-end">Avg Cost (₹)</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end cursor-pointer sortable-reit-th" data-col="invested" role="button" title="Click to sort by Invested Value">
                                        Invested (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end cursor-pointer sortable-reit-th" data-col="current" role="button" title="Click to sort by Current Value">
                                        Current Value (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Unrealized P&L</th>
                                    <th class="text-end">Distributions (₹)</th>
                                    <th class="text-end">Total Return (₹)</th>
                                    <th class="text-center" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reitHoldingsTbody">
                                <?php foreach ($holdings as $h): ?>
                                    <tr class="reit-holding-row"
                                        data-security="<?= esc(strtolower($h['symbol'] . ' ' . $h['trust_name'])) ?>"
                                        data-type="<?= esc(strtolower($h['trust_type'] ?? '')) ?>"
                                        data-invested="<?= (float)($h['invested_value'] ?? 0) ?>"
                                        data-current="<?= (float)($h['current_value'] ?? 0) ?>">
                                        <!-- Security / Trust -->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="fw-bold text-dark">
                                                        <?= esc($h['symbol']) ?>
                                                        <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.68rem;"><?= esc($h['exchange']) ?></span>
                                                    </div>
                                                    <div class="text-secondary small" style="font-size: 0.78rem;">
                                                        <?= esc($h['trust_name']) ?>
                                                    </div>
                                                    <?php if (!empty($h['sponsor'])): ?>
                                                        <div class="text-muted" style="font-size: 0.7rem;">
                                                            Sponsor: <?= esc($h['sponsor']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Type -->
                                        <td>
                                            <?php if ($h['trust_type'] === 'REIT'): ?>
                                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-building me-1"></i>REIT
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-broadcast-pin me-1"></i>InvIT
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Units -->
                                        <td class="text-end fw-semibold">
                                            <?= number_format($h['active_units'], 4) ?>
                                        </td>

                                        <!-- Avg Cost -->
                                        <td class="text-end">
                                            <?= format_inr($h['avg_buy_price']) ?>
                                        </td>

                                        <!-- CMP -->
                                        <td class="text-end">
                                            <div class="fw-bold text-dark"><?= format_inr($h['current_price']) ?></div>
                                            <?php if (!empty($h['last_price_update'])): ?>
                                                <small class="text-muted" style="font-size: 0.68rem;" title="Last price update">
                                                    <?= date('d M H:i', strtotime($h['last_price_update'])) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Invested -->
                                        <td class="text-end">
                                            <?= format_inr($h['invested_value']) ?>
                                        </td>

                                        <!-- Current Value -->
                                        <td class="text-end fw-semibold">
                                            <?= format_inr($h['current_value']) ?>
                                        </td>

                                        <!-- Unrealized P&L -->
                                        <td class="text-end">
                                            <?= format_pnl($h['unrealized_pnl'], $h['unrealized_pnl_percent']) ?>
                                        </td>

                                        <!-- Distributions Earned -->
                                        <td class="text-end text-success fw-semibold">
                                            <?= format_inr($h['distributions_earned']) ?>
                                            <?php if (!empty($h['last_distribution_date'])): ?>
                                                <div class="text-muted" style="font-size: 0.68rem;">
                                                    Last: <?= date('d-M-Y', strtotime($h['last_distribution_date'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Total Return -->
                                        <td class="text-end fw-bold">
                                            <?= format_pnl($h['total_return']) ?>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary rounded-start-3 px-2 open-reit-modal" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#reitTransactionModal"
                                                        data-id="<?= $h['id'] ?>"
                                                        data-symbol="<?= esc($h['symbol']) ?>"
                                                        data-name="<?= esc($h['trust_name']) ?>"
                                                        data-type="<?= esc($h['trust_type']) ?>"
                                                        data-exchange="<?= esc($h['exchange']) ?>"
                                                        data-qty="<?= $h['active_units'] ?>"
                                                        data-cmp="<?= $h['current_price'] ?>"
                                                        data-avg="<?= $h['avg_buy_price'] ?>">
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
                                                        <button class="dropdown-item small open-edit-trust-modal"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editTrustModal"
                                                                data-id="<?= $h['id'] ?>"
                                                                data-name="<?= esc($h['trust_name']) ?>"
                                                                data-symbol="<?= esc($h['symbol']) ?>"
                                                                data-isin="<?= esc($h['isin'] ?? '') ?>"
                                                                data-type="<?= esc($h['trust_type']) ?>"
                                                                data-exchange="<?= esc($h['exchange']) ?>"
                                                                data-sponsor="<?= esc($h['sponsor'] ?? '') ?>"
                                                                data-price="<?= esc($h['current_price'] ?? '') ?>">
                                                            <i class="bi bi-pencil me-2 text-warning"></i>Edit Trust Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('reits-invits?trust_id=' . $h['id'] . '&tab=transactions') ?>" onclick="showTrustLedger(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-success" href="<?= base_url('reits-invits?trust_id=' . $h['id'] . '&tab=distributions') ?>" onclick="showTrustDistributions(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-cash-coin me-2"></i>View Distributions
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                            href="<?= base_url('reits-invits/delete/' . $h['id']) ?>" 
                                                            onclick="return confirm('Delete this trust and all related transaction, payout, and tax records?');">
                                                            <i class="bi bi-trash me-2"></i>Delete Trust
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

            <!-- TAB 2: DISTRIBUTION INCOME JOURNAL -->
            <div class="tab-pane fade p-3" id="distributions" role="tabpanel">
                <div class="alert alert-light border shadow-sm py-2 px-3 mb-3 d-flex align-items-center rounded-3 small">
                    <i class="bi bi-info-circle-fill text-info me-2 fs-5"></i>
                    <div>
                        <strong>Indian REIT / InvIT Distribution Breakdown & Tax Rules:</strong>
                        Distributions typically contain up to four components:
                        <span class="badge bg-secondary-subtle text-secondary border ms-1">Interest</span> (Taxable at slab rates, 10% TDS u/s 194LBA),
                        <span class="badge bg-success-subtle text-success-emphasis border ms-1">Dividend</span> (Tax-exempt if SPV didn't opt for sec 115BAA, or taxable if opted),
                        <span class="badge bg-warning-subtle text-warning-emphasis border ms-1">Return of Capital (ROC)</span> (Reduces acquisition cost, taxable u/s 56(2)(xii) only if cumulative distributions exceed issue price),
                        and <span class="badge bg-light text-dark border ms-1">TDS Credit</span> (Claimable in ITR).
                    </div>
                </div>

                <?php if (empty($distributions)): ?>
                    <p class="text-muted text-center py-4">No quarterly distribution payouts recorded yet. Use <strong>Add Trans &gt; Record Distribution</strong> on any trust.</p>
                <?php else: ?>
                    <?php
                    $fyRanges         = get_fy_ranges();
                    $distTrusts       = [];
                    $overallNetDist   = 0.0;
                    $overallTdsDist   = 0.0;
                    $overallInterest  = 0.0;
                    $overallDividend  = 0.0;
                    $overallRoc       = 0.0;
                    $overallGrossDist = 0.0;

                    foreach ($distributions as $d) {
                        $tId = (int)$d['trust_id'];
                        if (!isset($distTrusts[$tId])) {
                            $distTrusts[$tId] = [
                                'id'     => $tId,
                                'symbol' => $d['symbol'],
                                'name'   => $d['trust_name'],
                                'count'  => 0,
                            ];
                        }
                        $distTrusts[$tId]['count']++;

                        $net   = (float)$d['net_received'];
                        $tds   = (float)$d['tds_deducted'];
                        $int   = (float)$d['interest_component'];
                        $div   = (float)$d['dividend_component'];
                        $roc   = (float)$d['return_of_capital'];
                        $gross = (float)$d['total_amount'];

                        $overallNetDist   += $net;
                        $overallTdsDist   += $tds;
                        $overallInterest  += $int;
                        $overallDividend  += $div;
                        $overallRoc       += $roc;
                        $overallGrossDist += $gross;
                    }
                    uasort($distTrusts, fn($a, $b) => strcmp($a['symbol'], $b['symbol']));
                    $overallTaxable = $overallInterest;
                    $overallExempt  = $overallDividend + $overallRoc;
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
                                            <label class="form-label mb-0 fw-semibold text-dark small">Filter Distributions</label>
                                            <div class="text-muted" style="font-size: 0.72rem;">Filter by trust &amp; financial year</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                        <div style="min-width: 220px; max-width: 300px;" class="flex-grow-1">
                                            <select id="distTrustFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">-- All Trusts (<?= count($distributions) ?> Payouts) --</option>
                                                <?php foreach ($distTrusts as $tId => $t): ?>
                                                    <option value="<?= $tId ?>" data-symbol="<?= esc($t['symbol']) ?>">
                                                        <?= esc($t['symbol']) ?> — <?= esc($t['name']) ?> (<?= $t['count'] ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div style="min-width: 170px; max-width: 200px;">
                                            <select id="distDateFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">All Dates (All Time)</option>
                                                <option value="CURRENT_FY">Current FY (<?= $fyRanges['current']['label'] ?>)</option>
                                                <option value="LAST_FY">Last FY (<?= $fyRanges['last']['label'] ?>)</option>
                                            </select>
                                        </div>
                                        <button type="button" id="btnResetDistFilter" class="btn btn-sm btn-outline-secondary d-none" title="Reset all filters">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                        </button>
                                        <span id="filteredDistBadge" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 small d-none">
                                            <i class="bi bi-check2-circle me-1"></i><span id="filteredDistSymbol"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4 KPI Summary Metric Cards -->
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-success">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Net Distributions Received</span>
                                            <h5 class="fw-bold text-success mb-0 mt-1" id="kpiDistNet"><?= format_inr($overallNetDist) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Actual bank credits
                                            </div>
                                        </div>
                                        <div class="bg-success-subtle text-success rounded-3 p-2">
                                            <i class="bi bi-bank fs-5"></i>
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">TDS Deducted</span>
                                            <h5 class="fw-bold text-danger mb-0 mt-1" id="kpiDistTds"><?= format_inr($overallTdsDist) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                u/s 194LBA TDS credit
                                            </div>
                                        </div>
                                        <div class="bg-danger-subtle text-danger rounded-3 p-2">
                                            <i class="bi bi-shield-x fs-5"></i>
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Taxable Components</span>
                                            <h5 class="fw-bold text-primary mb-0 mt-1" id="kpiDistTaxable"><?= format_inr($overallTaxable) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Interest &amp; Other Income
                                            </div>
                                        </div>
                                        <div class="bg-primary-subtle text-primary rounded-3 p-2">
                                            <i class="bi bi-receipt fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-warning">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Capital / Exempt</span>
                                            <h5 class="fw-bold text-dark mb-0 mt-1" id="kpiDistExempt"><?= format_inr($overallExempt) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Dividend + Return of Capital
                                            </div>
                                        </div>
                                        <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-2">
                                            <i class="bi bi-piggy-bank fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="reitDistTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Payout Date</th>
                                    <th>Security / Trust</th>
                                    <th>Quarter / Period</th>
                                    <th class="text-end">Eligible Units</th>
                                    <th class="text-end">DPU (₹)</th>
                                    <th class="text-end">Interest (₹)</th>
                                    <th class="text-end">Dividend (₹)</th>
                                    <th class="text-end">ROC (₹)</th>
                                    <th class="text-end">Total Gross (₹)</th>
                                    <th class="text-end">TDS (₹)</th>
                                    <th class="text-end">Net Received (₹)</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($distributions as $d): ?>
                                    <tr class="reit-dist-row"
                                        data-reit-id="<?= $d['trust_id'] ?>"
                                        data-name="<?= esc($d['trust_name']) ?>"
                                        data-symbol="<?= esc($d['symbol']) ?>"
                                        data-date="<?= $d['payout_date'] ?>"
                                        data-interest="<?= (float)$d['interest_component'] ?>"
                                        data-dividend="<?= (float)$d['dividend_component'] ?>"
                                        data-roc="<?= (float)$d['return_of_capital'] ?>"
                                        data-gross="<?= (float)$d['total_amount'] ?>"
                                        data-tds="<?= (float)$d['tds_deducted'] ?>"
                                        data-net="<?= (float)$d['net_received'] ?>">
                                        <td><?= date('d-M-Y', strtotime($d['payout_date'])) ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= esc($d['symbol']) ?></div>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($d['trust_name']) ?></div>
                                        </td>
                                        <td>
                                            <div><?= esc($d['quarter_description'] ?: '—') ?></div>
                                            <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">FY <?= esc($d['financial_year']) ?></span>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?= !empty($d['eligible_units']) && (float)$d['eligible_units'] > 0 ? number_format($d['eligible_units'], 4) : '—' ?>
                                        </td>
                                        <td class="text-end text-muted">
                                            <?= !empty($d['dpu']) && (float)$d['dpu'] > 0 ? '₹ ' . number_format($d['dpu'], 4) : '—' ?>
                                        </td>
                                        <td class="text-end"><?= format_inr($d['interest_component']) ?></td>
                                        <td class="text-end"><?= format_inr($d['dividend_component']) ?></td>
                                        <td class="text-end"><?= format_inr($d['return_of_capital']) ?></td>
                                        <td class="text-end fw-semibold"><?= format_inr($d['total_amount']) ?></td>
                                        <td class="text-end text-danger"><?= format_inr($d['tds_deducted']) ?></td>
                                        <td class="text-end fw-bold text-success"><?= format_inr($d['net_received']) ?></td>
                                        <td class="text-secondary"><?= esc($d['notes'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-light btn-sm open-edit-reit-dist"
                                                        data-bs-toggle="modal" data-bs-target="#editReitDistModal"
                                                        data-id="<?= $d['id'] ?>"
                                                        data-trust="<?= esc($d['trust_name']) ?> (<?= esc($d['symbol']) ?>)"
                                                        data-payout-date="<?= esc($d['payout_date']) ?>"
                                                        data-record-date="<?= esc($d['record_date'] ?? '') ?>"
                                                        data-eligible-units="<?= esc($d['eligible_units'] ?? '') ?>"
                                                        data-dpu="<?= esc($d['dpu'] ?? '') ?>"
                                                        data-total-amount="<?= esc($d['total_amount']) ?>"
                                                        data-net-received="<?= esc($d['net_received']) ?>"
                                                        data-interest="<?= esc($d['interest_component'] ?? '0') ?>"
                                                        data-dividend="<?= esc($d['dividend_component'] ?? '0') ?>"
                                                        data-roc="<?= esc($d['return_of_capital'] ?? '0') ?>"
                                                        data-other="<?= esc($d['other_income'] ?? '0') ?>"
                                                        data-tds="<?= esc($d['tds_deducted'] ?? '0') ?>"
                                                        data-quarter="<?= esc($d['quarter_description'] ?? '') ?>"
                                                        data-notes="<?= esc($d['notes'] ?? '') ?>"
                                                        title="Edit Distribution">
                                                    <i class="bi bi-pencil text-primary"></i>
                                                </button>
                                                <a href="<?= base_url('reits-invits/delete-distribution/' . $d['id']) ?>" 
                                                   class="btn btn-light btn-sm text-danger" 
                                                   onclick="return confirm('Delete this distribution payment record?');"
                                                   title="Delete Distribution">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="distNoRows" class="d-none text-center py-4">
                                    <td colspan="13" class="text-muted py-4">
                                        <i class="bi bi-funnel text-secondary fs-4 d-block mb-1"></i>
                                        No distributions found matching the selected trust and date filter.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light fw-semibold border-top-2">
                                <tr>
                                    <td colspan="5">
                                        <span id="footerDistSummaryTitle">Total (<?= count($distributions) ?> Distributions)</span>
                                    </td>
                                    <td class="text-end" id="footerDistInterest"><?= format_inr($overallInterest) ?></td>
                                    <td class="text-end" id="footerDistDividend"><?= format_inr($overallDividend) ?></td>
                                    <td class="text-end" id="footerDistRoc"><?= format_inr($overallRoc) ?></td>
                                    <td class="text-end fw-semibold" id="footerDistGross"><?= format_inr($overallGrossDist) ?></td>
                                    <td class="text-end text-danger" id="footerDistTds"><?= format_inr($overallTdsDist) ?></td>
                                    <td class="text-end fw-bold text-success fs-6" id="footerDistNet"><?= format_inr($overallNetDist) ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'reitDist']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 3: FIFO TAX & CAPITAL GAINS LOG -->
            <div class="tab-pane fade p-3" id="capitalgains" role="tabpanel">
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center rounded-3 small">
                    <i class="bi bi-shield-check me-2 fs-5"></i>
                    <div>
                        <strong>Indian Capital Gains Tax on Business Trusts (REITs & InvITs):</strong>
                        Units sold on stock exchange with STT paid are matched via First-In, First-Out (FIFO).
                        Holding &gt; 12 months = <strong>LTCG</strong> (Taxed at 12.5% u/s 112A for sales post 23-Jul-2024);
                        Holding &le; 12 months = <strong>STCG</strong> (Taxed at 20% u/s 111A post 23-Jul-2024).
                    </div>
                </div>

                <?php if (empty($capitalGains)): ?>
                    <p class="text-muted text-center py-4">No realized sales recorded yet. When you sell units via the modal, FIFO tax audit records will appear here.</p>
                <?php else: ?>
                    <?php
                    $fyRanges          = get_fy_ranges();
                    $taxTrusts         = [];
                    $totalMatchedUnits = 0.0;
                    $totalCostBasis    = 0.0;
                    $totalNetProceeds  = 0.0;
                    $totalRealizedGain = 0.0;
                    $totalStcg         = 0.0;
                    $totalLtcg         = 0.0;

                    foreach ($capitalGains as $cg) {
                        $tId = (int)$cg['trust_id'];
                        if (!isset($taxTrusts[$tId])) {
                            $taxTrusts[$tId] = [
                                'id'     => $tId,
                                'symbol' => $cg['symbol'],
                                'name'   => $cg['trust_name'],
                                'count'  => 0,
                            ];
                        }
                        $taxTrusts[$tId]['count']++;
                        $qty      = (float)($cg['quantity_matched'] ?? 0);
                        $cost     = $qty * (float)($cg['buy_price'] ?? 0);
                        $proceeds = $qty * (float)($cg['sell_price'] ?? 0);
                        $gain     = (float)($cg['realized_gain'] ?? 0);

                        $totalMatchedUnits += $qty;
                        $totalCostBasis    += $cost;
                        $totalNetProceeds  += $proceeds;
                        $totalRealizedGain += $gain;
                        if ($cg['gain_type'] === 'LTCG') {
                            $totalLtcg += $gain;
                        } else {
                            $totalStcg += $gain;
                        }
                    }
                    ?>

                    <!-- Filter Toolbar: Trust Selector + Gain Type + Date Range -->
                    <div class="bg-light p-3 rounded-4 border mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="taxLogReitFilter" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-filter me-1"></i>Filter by Trust / Security
                                </label>
                                <select class="form-select form-select-sm shadow-sm" id="taxLogReitFilter">
                                    <option value="ALL">All Trusts (<?= count($capitalGains) ?> lots)</option>
                                    <?php foreach ($taxTrusts as $tt): ?>
                                        <option value="<?= $tt['id'] ?>" data-symbol="<?= esc($tt['symbol']) ?>">
                                            <?= esc($tt['symbol']) ?> &mdash; <?= esc($tt['name']) ?> (<?= $tt['count'] ?> lots)
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
                                    <option value="STCG">STCG (&le; 12 Months)</option>
                                    <option value="LTCG">LTCG (&gt; 12 Months)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="taxLogDateFilter" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-calendar-range me-1"></i>Date Range (Sell Date)
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
                                <i class="bi bi-funnel-fill me-1"></i><span id="filteredTaxReitSpan">Active Filter</span>
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
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Cost Basis</div>
                                <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxCost"><?= format_inr($totalCostBasis) ?></div>
                                <div class="text-muted small" style="font-size: 0.72rem;">Original purchase cost</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-info border-4">
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Net Proceeds</div>
                                <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxProceeds"><?= format_inr($totalNetProceeds) ?></div>
                                <div class="text-muted small" style="font-size: 0.72rem;">Gross exit value</div>
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
                        <table class="table table-hover align-middle mb-0 small" id="reitTaxLogTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Security / Trust</th>
                                    <th class="text-end">Units Sold</th>
                                    <th>Buy Date</th>
                                    <th>Sell Date</th>
                                    <th class="text-center">Holding Period</th>
                                    <th class="text-end">Buy Price (₹)</th>
                                    <th class="text-end">Sell Price (₹)</th>
                                    <th class="text-end">Cost Basis (₹)</th>
                                    <th class="text-end">Net Proceeds (₹)</th>
                                    <th class="text-end">Realized P&amp;L (₹)</th>
                                    <th class="text-center">Tax Type</th>
                                    <th>FY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($capitalGains as $cg): 
                                    $qMatched   = (float) ($cg['quantity_matched'] ?? 0);
                                    $bPrice     = (float) ($cg['buy_price'] ?? 0);
                                    $sPrice     = (float) ($cg['sell_price'] ?? 0);
                                    $costBasis  = $qMatched * $bPrice;
                                    $proceeds   = $qMatched * $sPrice;
                                    $gain       = (float) ($cg['realized_gain'] ?? 0);
                                    $fy         = $cg['financial_year'] ?? get_financial_year($cg['sell_date']);
                                ?>
                                    <tr class="reit-taxlog-row"
                                        data-trust-id="<?= $cg['trust_id'] ?>"
                                        data-symbol="<?= esc($cg['symbol']) ?>"
                                        data-gain-type="<?= esc($cg['gain_type']) ?>"
                                        data-date="<?= $cg['sell_date'] ?>"
                                        data-units="<?= $qMatched ?>"
                                        data-cost="<?= $costBasis ?>"
                                        data-proceeds="<?= $proceeds ?>"
                                        data-gain="<?= $gain ?>">
                                        <td>
                                            <div class="fw-bold text-dark"><?= esc($cg['symbol']) ?></div>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($cg['trust_name']) ?></div>
                                        </td>
                                        <td class="text-end fw-semibold"><?= number_format($qMatched, 4) ?></td>
                                        <td><?= date('d-M-Y', strtotime($cg['buy_date'])) ?></td>
                                        <td><?= date('d-M-Y', strtotime($cg['sell_date'])) ?></td>
                                        <td class="text-center"><?= (int)($cg['holding_days'] ?? 0) ?> days</td>
                                        <td class="text-end"><?= format_inr($bPrice) ?></td>
                                        <td class="text-end"><?= format_inr($sPrice) ?></td>
                                        <td class="text-end"><?= format_inr($costBasis) ?></td>
                                        <td class="text-end"><?= format_inr($proceeds) ?></td>
                                        <td class="text-end fw-bold">
                                            <?= format_pnl($gain) ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($cg['gain_type'] === 'LTCG'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.68rem;">LTCG</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5" style="font-size: 0.68rem;">STCG</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">FY <?= esc($fy) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-group-divider bg-light fw-semibold" id="reitTaxFooter">
                                <tr>
                                    <td colspan="5" class="py-3">
                                        <span class="fw-bold text-dark" id="footerTaxSummaryTitle">Total (<?= count($capitalGains) ?> Lots)</span>
                                        <span class="ms-2 badge bg-light text-secondary border fw-normal" id="footerTaxBreakdown">
                                            All FIFO lots
                                        </span>
                                    </td>
                                    <td colspan="2"></td>
                                    <td class="text-end py-3 text-muted" id="footerTaxCost"><?= format_inr($totalCostBasis) ?></td>
                                    <td class="text-end py-3 text-muted" id="footerTaxProceeds"><?= format_inr($totalNetProceeds) ?></td>
                                    <td class="text-end py-3 fs-6 fw-bold <?= $totalRealizedGain >= 0 ? 'text-success' : 'text-danger' ?>" id="footerTaxGain">
                                        <?= ($totalRealizedGain >= 0 ? '+' : '') . format_inr($totalRealizedGain) ?>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'reitTax']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 4: TRANSACTION HISTORY -->
            <div class="tab-pane fade p-3" id="transactions" role="tabpanel">
                <?php if (empty($transactions)): ?>
                    <p class="text-muted text-center py-4">No transactions recorded yet.</p>
                <?php else: ?>
                    <?php
                    $fyRanges          = get_fy_ranges();
                    $ledgerTrusts      = [];
                    $overallBuyAmount  = 0.0;
                    $overallSellAmount = 0.0;
                    $overallBuyQty     = 0.0;
                    $overallSellQty    = 0.0;
                    $overallBuyCount   = 0;
                    $overallSellCount  = 0;
                    $overallCharges    = 0.0;

                    foreach ($transactions as $t) {
                        $tId = (int)$t['trust_id'];
                        if (!isset($ledgerTrusts[$tId])) {
                            $ledgerTrusts[$tId] = [
                                'id'     => $tId,
                                'symbol' => $t['symbol'],
                                'name'   => $t['trust_name'],
                                'count'  => 0,
                            ];
                        }
                        $ledgerTrusts[$tId]['count']++;

                        $qty     = (float)$t['quantity'];
                        $amt     = (float)$t['total_amount'];
                        $charges = (float)$t['brokerage'] + (float)$t['stt_taxes'];
                        $overallCharges += $charges;

                        if ($t['transaction_type'] === 'BUY') {
                            $overallBuyAmount += $amt;
                            $overallBuyQty    += $qty;
                            $overallBuyCount++;
                        } else {
                            $overallSellAmount += $amt;
                            $overallSellQty    += $qty;
                            $overallSellCount++;
                        }
                    }
                    uasort($ledgerTrusts, fn($a, $b) => strcmp($a['symbol'], $b['symbol']));
                    $overallNetOutlay = $overallBuyAmount - $overallSellAmount;
                    ?>

                    <!-- Filter Toolbar -->
                    <div class="card border shadow-sm mb-3 bg-body-tertiary rounded-3">
                        <div class="card-body p-3">
                            <div class="row align-items-center g-2">
                                <div class="col-md-4 col-lg-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                            <i class="bi bi-funnel-fill fs-6"></i>
                                        </div>
                                        <div>
                                            <label class="form-label mb-0 fw-semibold text-dark small">Filter Trades</label>
                                            <div class="text-muted" style="font-size: 0.72rem;">Filter by trust &amp; financial year</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                        <div style="min-width: 220px; max-width: 300px;" class="flex-grow-1">
                                            <select id="ledgerTrustFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">-- All Trusts (<?= count($transactions) ?> Trades) --</option>
                                                <?php foreach ($ledgerTrusts as $tId => $t): ?>
                                                    <option value="<?= $tId ?>" data-symbol="<?= esc($t['symbol']) ?>">
                                                        <?= esc($t['symbol']) ?> — <?= esc($t['name']) ?> (<?= $t['count'] ?>)
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
                                        <button type="button" id="btnResetTrustFilter" class="btn btn-sm btn-outline-secondary d-none" title="Reset all filters">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                        </button>
                                        <span id="filteredTrustBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5 small d-none">
                                            <i class="bi bi-check2-circle me-1"></i><span id="filteredTrustSymbol"></span>
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Purchases (BUY)</span>
                                            <h5 class="fw-bold text-success mb-0 mt-1" id="kpiBuyAmount"><?= format_inr($overallBuyAmount) ?></h5>
                                            <div class="text-muted small mt-1" id="kpiBuyDetails" style="font-size: 0.75rem;">
                                                <?= $overallBuyCount ?> buys • <?= number_format($overallBuyQty, 2) ?> units
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Sales (SELL)</span>
                                            <h5 class="fw-bold text-danger mb-0 mt-1" id="kpiSellAmount"><?= format_inr($overallSellAmount) ?></h5>
                                            <div class="text-muted small mt-1" id="kpiSellDetails" style="font-size: 0.75rem;">
                                                <?= $overallSellCount ?> sells • <?= number_format($overallSellQty, 2) ?> units
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
                                                Net cash invested in period
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Brokerage &amp; STT</span>
                                            <h5 class="fw-bold text-dark mb-0 mt-1" id="kpiCharges"><?= format_inr($overallCharges) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Total trade charges
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
                        <table class="table table-hover align-middle mb-0 small" id="reitLedgerTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Date</th>
                                    <th>Security / Trust</th>
                                    <th class="text-center">Action</th>
                                    <th class="text-end">Units</th>
                                    <th class="text-end">Price (₹)</th>
                                    <th class="text-end">Brokerage &amp; STT (₹)</th>
                                    <th class="text-end">Total Amount (₹)</th>
                                    <th class="text-end">Remaining Lot</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $t): ?>
                                    <tr class="reit-ledger-row"
                                        data-reit-id="<?= $t['trust_id'] ?>"
                                        data-name="<?= esc($t['trust_name']) ?>"
                                        data-symbol="<?= esc($t['symbol']) ?>"
                                        data-date="<?= $t['transaction_date'] ?>"
                                        data-type="<?= $t['transaction_type'] ?>"
                                        data-qty="<?= (float)$t['quantity'] ?>"
                                        data-charges="<?= (float)$t['brokerage'] + (float)$t['stt_taxes'] ?>"
                                        data-amount="<?= (float)$t['total_amount'] ?>">
                                        <td><?= date('d-M-Y', strtotime($t['transaction_date'])) ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= esc($t['symbol']) ?></div>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($t['trust_name']) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($t['transaction_type'] === 'BUY'): ?>
                                                 <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5">BUY</span>
                                            <?php else: ?>
                                                 <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5">SELL</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-semibold"><?= number_format($t['quantity'], 4) ?></td>
                                        <td class="text-end"><?= format_inr($t['price']) ?></td>
                                        <td class="text-end text-muted"><?= format_inr(((float)$t['brokerage']) + ((float)$t['stt_taxes'])) ?></td>
                                        <td class="text-end fw-semibold"><?= format_inr($t['total_amount']) ?></td>
                                        <td class="text-end text-muted">
                                            <?= $t['transaction_type'] === 'BUY' ? number_format($t['remaining_quantity'], 4) : '—' ?>
                                        </td>
                                        <td class="text-secondary"><?= esc($t['notes'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-light btn-sm open-edit-reit-trans"
                                                        data-bs-toggle="modal" data-bs-target="#editReitTransModal"
                                                        data-id="<?= $t['id'] ?>"
                                                        data-trust="<?= esc($t['trust_name']) ?> (<?= esc($t['symbol']) ?>)"
                                                        data-type="<?= esc($t['transaction_type']) ?>"
                                                        data-date="<?= esc($t['transaction_date']) ?>"
                                                        data-quantity="<?= esc($t['quantity']) ?>"
                                                        data-price="<?= esc($t['price']) ?>"
                                                        data-brokerage="<?= esc($t['brokerage'] ?? '0') ?>"
                                                        data-stt="<?= esc($t['stt_taxes'] ?? '0') ?>"
                                                        data-notes="<?= esc($t['notes'] ?? '') ?>"
                                                        title="Edit Transaction">
                                                    <i class="bi bi-pencil text-primary"></i>
                                                </button>
                                                <a href="<?= base_url('reits-invits/delete-transaction/' . $t['id']) ?>" 
                                                   class="btn btn-light btn-sm text-danger" 
                                                   onclick="return confirm('Delete this transaction? All chronological FIFO allocations and realized tax gains will be rebalanced.');"
                                                   title="Delete Transaction">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="ledgerNoRows" class="d-none text-center py-4">
                                    <td colspan="10" class="text-muted py-4">
                                        <i class="bi bi-funnel text-secondary fs-4 d-block mb-1"></i>
                                        No transactions found matching the selected trust and date filter.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light fw-semibold border-top-2">
                                <tr>
                                    <td colspan="3">
                                        <span id="footerSummaryTitle">Total (<?= count($transactions) ?> Trades)</span>
                                        <div class="text-muted fw-normal" style="font-size: 0.72rem;" id="footerActionBreakdown">
                                            <span class="text-success"><?= $overallBuyCount ?> BUY</span> &bull; <span class="text-danger"><?= $overallSellCount ?> SELL</span>
                                        </div>
                                    </td>
                                    <td class="text-end" id="footerTotalQty"><?= number_format($overallBuyQty + $overallSellQty, 2) ?></td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-end text-muted" id="footerTotalCharges"><?= format_inr($overallCharges) ?></td>
                                    <td class="text-end fw-bold fs-6" id="footerTotalAmount"><?= format_inr($overallNetOutlay) ?></td>
                                    <td colspan="3" class="text-secondary small" id="footerNote"><?= $overallNetOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized' ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'reitLedger']) ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MINIMAL POPUP TRANSACTION MODAL (BUY MORE / SELL FIFO / DISTRIBUTION)     -->
<!-- ========================================================================= -->
<div class="modal fade" id="reitTransactionModal" tabindex="-1" aria-labelledby="reitTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="reitTransactionModalLabel">
                        <i class="bi bi-buildings text-info me-2"></i><span id="modalReitTitle">Manage Holding</span>
                    </h5>
                    <div class="text-muted small" id="modalReitSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('reits-invits/transaction') ?>" method="POST" id="reitTransForm">
                <?= csrf_field() ?>
                <input type="hidden" name="trust_id" id="modalTrustId" value="">

                <div class="modal-body p-4">
                    <!-- Segmented 3-way Action Switcher -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actReitBuy" value="BUY" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actReitBuy">
                            <i class="bi bi-cart-plus me-1"></i>Buy More
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actReitSell" value="SELL" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actReitSell">
                            <i class="bi bi-cart-dash me-1"></i>Sell (FIFO)
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actReitDist" value="DISTRIBUTION" autocomplete="off">
                        <label class="btn btn-outline-primary fw-semibold" for="actReitDist">
                            <i class="bi bi-cash-coin me-1"></i>Record Distribution
                        </label>
                    </div>

                    <!-- SUB-PANEL 1: BUY MORE -->
                    <div id="panelReitBuy" class="reit-trans-panel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="buyReitDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity (Units) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="buyReitQty" min="0.0001" step="0.0001" placeholder="e.g. 50" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Buy Price per Unit (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="buyReitPrice" min="0.01" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="buyReitBrokerage" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">STT / Taxes (₹)</label>
                                <input type="number" class="form-control" name="stt_taxes" id="buyReitStt" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Dip accumulation / SIP lot">
                            </div>
                        </div>

                        <!-- Buy Outlay Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Total Outlay:</span>
                                <span class="fw-bold text-success fs-5" id="buyReitTotalPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-PANEL 2: SELL (FIFO) -->
                    <div id="panelReitSell" class="reit-trans-panel d-none">
                        <div class="alert alert-warning py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Available Units to Sell:</span>
                            <strong id="sellReitAvailableBadge">0.0000 units</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Sale Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="sellReitDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity to Sell <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="sellReitQty" min="0.0001" step="0.0001" placeholder="Quantity" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Sell Price per Unit (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="sellReitPrice" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="sellReitBrokerage" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">STT / Taxes (₹)</label>
                                <input type="number" class="form-control" name="stt_taxes" id="sellReitStt" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Profit booking" disabled>
                            </div>
                        </div>

                        <!-- Sell Proceeds Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Estimated Net Proceeds:</span>
                                <span class="fw-bold text-dark" id="sellReitProceedsPreview">₹ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Realized P&L:</span>
                                <span class="small fw-semibold" id="sellReitPnlPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-PANEL 3: RECORD DISTRIBUTION -->
                    <div id="panelReitDist" class="reit-trans-panel d-none">
                        <div class="alert alert-info py-2 px-3 mb-3 small rounded-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Record Date Eligibility:</strong> Distributions are paid only on units held on the <strong>Record Date</strong>. If you acquired additional units between record date and payout date, or are logging past payouts, adjust <strong>Eligible Held Quantity</strong> to match your distribution statement.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Payout Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="payout_date" id="distPayoutDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Record Date (Optional)</label>
                                <input type="date" class="form-control" name="record_date" id="distRecordDate" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Eligible Held Quantity (Units) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control fw-semibold" name="eligible_units" id="distEligibleUnits" min="0.0001" step="0.0001" placeholder="Units held on Record Date" required disabled>
                                    <span class="input-group-text small text-muted">units</span>
                                </div>
                                <div class="form-text small" style="font-size: 0.72rem;">Units held on Record Date (edit if you bought more later)</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Distribution Per Unit - DPU (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text small text-muted">₹</span>
                                    <input type="number" class="form-control" name="dpu" id="distDpu" min="0" step="0.0001" placeholder="e.g. 5.20" disabled>
                                    <span class="input-group-text small text-muted">/ unit</span>
                                </div>
                                <div class="form-text small" style="font-size: 0.72rem;">Auto-calculates with Gross Amount &amp; Eligible Units</div>
                            </div>

                            <!-- 4 Component Breakdown -->
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Interest (₹)</label>
                                <input type="number" class="form-control dist-component" name="interest_component" id="distInterest" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Dividend (₹)</label>
                                <input type="number" class="form-control dist-component" name="dividend_component" id="distDividend" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Return of Capital (₹)</label>
                                <input type="number" class="form-control dist-component" name="return_of_capital" id="distRoc" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Other Income (₹)</label>
                                <input type="number" class="form-control dist-component" name="other_income" id="distOther" min="0" step="0.01" value="0.00" disabled>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Total Gross Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control fw-semibold" name="total_amount" id="distTotal" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                                <input type="number" class="form-control text-danger" name="tds_deducted" id="distTds" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Net Received in Bank (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control fw-bold text-success" name="net_received" id="distNet" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quarter / Period Description</label>
                                <input type="text" class="form-control" name="quarter_description" placeholder="e.g. Q3 FY 2025-26" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Quarterly distribution credited via NEFT" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitReitTrans">
                        Confirm Purchase Lot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT TRUST METADATA MODAL                                                 -->
<!-- ========================================================================= -->
<div class="modal fade" id="editTrustModal" tabindex="-1" aria-labelledby="editTrustModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editTrustModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Trust Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('reits-invits/update-trust') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="trust_id" id="editTrustId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">Trust / Enterprise Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="trust_name" id="editTrustName" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Trust Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="trust_type" id="editTrustType" required>
                                <option value="REIT">REIT (Real Estate)</option>
                                <option value="INVIT">InvIT (Infrastructure)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NSE / BSE Symbol <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="symbol" id="editTrustSymbol" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ISIN Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="isin" id="editTrustIsin" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Exchange <span class="text-danger">*</span></label>
                            <select class="form-select" name="exchange" id="editTrustExchange" required>
                                <option value="NSE">NSE</option>
                                <option value="BSE">BSE</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Sponsor / Manager</label>
                            <input type="text" class="form-control" name="sponsor" id="editTrustSponsor">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Current Market Price (₹)</label>
                            <input type="number" class="form-control" name="current_price" id="editTrustPrice" min="0.01" step="0.01">
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
<!-- EDIT REIT/INVIT TRANSACTION MODAL (WITH FIFO REBUILD)                     -->
<!-- ========================================================================= -->
<div class="modal fade" id="editReitTransModal" tabindex="-1" aria-labelledby="editReitTransModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editReitTransModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i><span id="editReitTransTitle">Edit Transaction</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('reits-invits/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editReitTransId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Saving changes will automatically <strong>rebalance FIFO lots and recalculate capital gains</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editReitTransDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quantity (Units) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="quantity" id="editReitTransQty" min="0.0001" step="0.0001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Price per Unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="editReitTransPrice" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                            <input type="number" class="form-control" name="brokerage" id="editReitTransBrokerage" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">STT / Taxes (₹)</label>
                            <input type="number" class="form-control" name="stt_taxes" id="editReitTransStt" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editReitTransNotes">
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

<!-- ========================================================================= -->
<!-- EDIT DISTRIBUTION ENTRY MODAL                                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="editReitDistModal" tabindex="-1" aria-labelledby="editReitDistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="editReitDistModalLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i>Edit Distribution Payout
                    </h5>
                    <div class="text-muted small" id="editReitDistSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('reits-invits/update-distribution') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="distribution_id" id="editReitDistId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Payout Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="payout_date" id="editDistPayoutDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Record Date</label>
                            <input type="date" class="form-control" name="record_date" id="editDistRecordDate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Eligible Units Held</label>
                            <input type="number" class="form-control" name="eligible_units" id="editDistEligibleUnits" min="0" step="0.0001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">DPU - Distribution Per Unit (₹)</label>
                            <input type="number" class="form-control" name="dpu" id="editDistDpu" min="0" step="0.0001">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Interest Component (₹)</label>
                            <input type="number" class="form-control" name="interest_component" id="editDistInterest" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Dividend Component (₹)</label>
                            <input type="number" class="form-control" name="dividend_component" id="editDistDividend" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Return of Capital (₹)</label>
                            <input type="number" class="form-control" name="return_of_capital" id="editDistRoc" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Other Income (₹)</label>
                            <input type="number" class="form-control" name="other_income" id="editDistOther" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Total Gross Distribution (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-bold" name="total_amount" id="editDistTotal" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                            <input type="number" class="form-control text-danger" name="tds_deducted" id="editDistTds" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Net Received (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-bold text-success" name="net_received" id="editDistNet" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quarter / Description</label>
                            <input type="text" class="form-control" name="quarter_description" id="editDistQuarter" placeholder="e.g. Q3 FY25 Interim Payout">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editDistNotes">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentTrust = {};

    // 1. Hook "Add Trans" buttons to populate modal
    document.querySelectorAll('.open-reit-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentTrust = {
                id: this.dataset.id,
                symbol: this.dataset.symbol,
                name: this.dataset.name,
                type: this.dataset.type,
                exchange: this.dataset.exchange,
                qty: parseFloat(this.dataset.qty) || 0,
                cmp: parseFloat(this.dataset.cmp) || 0,
                avgPrice: parseFloat(this.dataset.avg) || 0
            };

            document.getElementById('modalTrustId').value = currentTrust.id;
            document.getElementById('modalReitTitle').textContent = currentTrust.symbol + ' (' + currentTrust.exchange + ')';
            document.getElementById('modalReitSubtitle').textContent = currentTrust.name + ' | Held: ' + currentTrust.qty.toFixed(4) + ' units | CMP: ₹ ' + currentTrust.cmp.toFixed(2);

            // Set defaults in Buy Panel
            document.getElementById('buyReitPrice').value = currentTrust.cmp > 0 ? currentTrust.cmp.toFixed(2) : '';
            document.getElementById('buyReitQty').value = '';
            document.getElementById('buyReitBrokerage').value = '0.00';
            document.getElementById('buyReitStt').value = '0.00';
            document.getElementById('buyReitTotalPreview').textContent = '₹ 0.00';

            // Set defaults in Sell Panel
            document.getElementById('sellReitAvailableBadge').textContent = currentTrust.qty.toFixed(4) + ' units';
            document.getElementById('sellReitQty').max = currentTrust.qty;
            document.getElementById('sellReitQty').value = '';
            document.getElementById('sellReitPrice').value = currentTrust.cmp > 0 ? currentTrust.cmp.toFixed(2) : '';
            document.getElementById('sellReitBrokerage').value = '0.00';
            document.getElementById('sellReitStt').value = '0.00';
            document.getElementById('sellReitProceedsPreview').textContent = '₹ 0.00';
            document.getElementById('sellReitPnlPreview').textContent = '₹ 0.00';

            // Reset Distribution Panel
            document.getElementById('distEligibleUnits').value = currentTrust.qty > 0 ? currentTrust.qty.toFixed(4) : '';
            document.getElementById('distDpu').value = '';
            document.getElementById('distInterest').value = '0.00';
            document.getElementById('distDividend').value = '0.00';
            document.getElementById('distRoc').value = '0.00';
            document.getElementById('distOther').value = '0.00';
            document.getElementById('distTotal').value = '';
            document.getElementById('distTds').value = '0.00';
            document.getElementById('distNet').value = '';

            // Reset to Buy panel
            document.getElementById('actReitBuy').checked = true;
            switchReitAction('BUY');
        });
    });

    // 2. Action Tab Switching
    const actRadios = document.querySelectorAll('input[name="action_type"]');
    actRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            switchReitAction(this.value);
        });
    });

    function switchReitAction(type) {
        const pBuy = document.getElementById('panelReitBuy');
        const pSell = document.getElementById('panelReitSell');
        const pDist = document.getElementById('panelReitDist');
        const submitBtn = document.getElementById('btnSubmitReitTrans');

        // Hide all & disable
        [pBuy, pSell, pDist].forEach(p => {
            p.classList.add('d-none');
            setInputsDisabled(p, true);
        });

        if (type === 'BUY') {
            pBuy.classList.remove('d-none');
            setInputsDisabled(pBuy, false);
            submitBtn.className = 'btn btn-success btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Confirm Purchase Lot';
        } else if (type === 'SELL') {
            pSell.classList.remove('d-none');
            setInputsDisabled(pSell, false);
            submitBtn.className = 'btn btn-danger btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Confirm Sell Order (FIFO)';
        } else if (type === 'DISTRIBUTION') {
            pDist.classList.remove('d-none');
            setInputsDisabled(pDist, false);
            submitBtn.className = 'btn btn-primary btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Record Distribution Payout';
        }
    }

    function setInputsDisabled(panel, disabled) {
        panel.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    // 3. Live Buy Outlay Calculation
    const buyQty = document.getElementById('buyReitQty');
    const buyPrice = document.getElementById('buyReitPrice');
    const buyBrok = document.getElementById('buyReitBrokerage');
    const buyStt = document.getElementById('buyReitStt');
    const buyPreview = document.getElementById('buyReitTotalPreview');

    function calcBuyTotal() {
        const q = parseFloat(buyQty.value) || 0;
        const p = parseFloat(buyPrice.value) || 0;
        const b = parseFloat(buyBrok.value) || 0;
        const s = parseFloat(buyStt.value) || 0;
        const total = (q * p) + b + s;
        buyPreview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    [buyQty, buyPrice, buyBrok, buyStt].forEach(el => {
        el.addEventListener('input', calcBuyTotal);
    });

    // 4. Live Sell Proceeds & P&L Calculation
    const sellQty = document.getElementById('sellReitQty');
    const sellPrice = document.getElementById('sellReitPrice');
    const sellBrok = document.getElementById('sellReitBrokerage');
    const sellStt = document.getElementById('sellReitStt');
    const sellProceedsPreview = document.getElementById('sellReitProceedsPreview');
    const sellPnlPreview = document.getElementById('sellReitPnlPreview');

    function calcSellProceeds() {
        const q = parseFloat(sellQty.value) || 0;
        const p = parseFloat(sellPrice.value) || 0;
        const b = parseFloat(sellBrok.value) || 0;
        const s = parseFloat(sellStt.value) || 0;
        const proceeds = (q * p) - b - s;
        sellProceedsPreview.textContent = '₹ ' + proceeds.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (currentTrust.avgPrice > 0 && q > 0) {
            const cost = q * currentTrust.avgPrice;
            const gain = proceeds - cost;
            const sign = gain >= 0 ? '+' : '';
            sellPnlPreview.className = 'small fw-semibold ' + (gain >= 0 ? 'text-success' : 'text-danger');
            sellPnlPreview.textContent = sign + '₹ ' + gain.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' (Estimated)';
        } else {
            sellPnlPreview.textContent = '₹ 0.00';
            sellPnlPreview.className = 'small fw-semibold text-muted';
        }
    }

    [sellQty, sellPrice, sellBrok, sellStt].forEach(el => {
        el.addEventListener('input', calcSellProceeds);
    });

    // 5. Distribution Component Auto-Sum & DPU Sync
    const distEligibleUnits = document.getElementById('distEligibleUnits');
    const distDpu = document.getElementById('distDpu');
    const distInterest = document.getElementById('distInterest');
    const distDividend = document.getElementById('distDividend');
    const distRoc = document.getElementById('distRoc');
    const distOther = document.getElementById('distOther');
    const distTotal = document.getElementById('distTotal');
    const distTds = document.getElementById('distTds');
    const distNet = document.getElementById('distNet');

    function calcDistTotals() {
        const interest = parseFloat(distInterest.value) || 0;
        const dividend = parseFloat(distDividend.value) || 0;
        const roc = parseFloat(distRoc.value) || 0;
        const other = parseFloat(distOther.value) || 0;

        const sumComponents = interest + dividend + roc + other;
        if (sumComponents > 0) {
            distTotal.value = sumComponents.toFixed(2);
        }

        const gross = parseFloat(distTotal.value) || 0;
        const tds = parseFloat(distTds.value) || 0;
        const net = Math.max(0, gross - tds);
        distNet.value = net > 0 ? net.toFixed(2) : '';

        // Auto-calculate DPU if eligible units entered
        const units = parseFloat(distEligibleUnits.value) || 0;
        if (units > 0 && gross > 0) {
            distDpu.value = (gross / units).toFixed(4);
        }
    }

    [distInterest, distDividend, distRoc, distOther, distTotal, distTds].forEach(el => {
        el.addEventListener('input', calcDistTotals);
    });

    // When DPU is entered directly, calculate Gross & Net
    distDpu.addEventListener('input', function() {
        const dpuVal = parseFloat(distDpu.value) || 0;
        const units = parseFloat(distEligibleUnits.value) || 0;
        if (dpuVal > 0 && units > 0) {
            const calculatedGross = dpuVal * units;
            distTotal.value = calculatedGross.toFixed(2);
            const tds = parseFloat(distTds.value) || 0;
            distNet.value = Math.max(0, calculatedGross - tds).toFixed(2);
        }
    });

    // When Eligible Units changes, re-sync with DPU or Gross
    distEligibleUnits.addEventListener('input', function() {
        const units = parseFloat(distEligibleUnits.value) || 0;
        const dpuVal = parseFloat(distDpu.value) || 0;
        const gross = parseFloat(distTotal.value) || 0;
        if (dpuVal > 0 && units > 0) {
            const calculatedGross = dpuVal * units;
            distTotal.value = calculatedGross.toFixed(2);
            const tds = parseFloat(distTds.value) || 0;
            distNet.value = Math.max(0, calculatedGross - tds).toFixed(2);
        } else if (gross > 0 && units > 0) {
            distDpu.value = (gross / units).toFixed(4);
        }
    });

    // 6. Edit Trust Details Modal Handler
    document.querySelectorAll('.open-edit-trust-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editTrustId').value = this.dataset.id;
            document.getElementById('editTrustName').value = this.dataset.name;
            document.getElementById('editTrustSymbol').value = this.dataset.symbol;
            document.getElementById('editTrustIsin').value = this.dataset.isin;
            document.getElementById('editTrustType').value = this.dataset.type;
            document.getElementById('editTrustExchange').value = this.dataset.exchange;
            document.getElementById('editTrustSponsor').value = this.dataset.sponsor || '';
            document.getElementById('editTrustPrice').value = this.dataset.price || '';
        });
    });

    // 7. Edit REIT Transaction Modal Handler
    document.querySelectorAll('.open-edit-reit-trans').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editReitTransId').value = this.dataset.id;
            document.getElementById('editReitTransTitle').textContent = 'Edit ' + this.dataset.type + ' — ' + this.dataset.trust;
            document.getElementById('editReitTransDate').value = this.dataset.date;
            document.getElementById('editReitTransQty').value = this.dataset.quantity;
            document.getElementById('editReitTransPrice').value = this.dataset.price;
            document.getElementById('editReitTransBrokerage').value = this.dataset.brokerage || '0.00';
            document.getElementById('editReitTransStt').value = this.dataset.stt || '0.00';
            document.getElementById('editReitTransNotes').value = this.dataset.notes || '';
        });
    });

    // 8. Edit Distribution Modal Handler & Auto-calculations
    const editDistEligibleUnits = document.getElementById('editDistEligibleUnits');
    const editDistDpu           = document.getElementById('editDistDpu');
    const editDistInterest      = document.getElementById('editDistInterest');
    const editDistDividend      = document.getElementById('editDistDividend');
    const editDistRoc           = document.getElementById('editDistRoc');
    const editDistOther         = document.getElementById('editDistOther');
    const editDistTotal         = document.getElementById('editDistTotal');
    const editDistTds           = document.getElementById('editDistTds');
    const editDistNet           = document.getElementById('editDistNet');

    function calcEditDistTotals() {
        const interest = parseFloat(editDistInterest.value) || 0;
        const dividend = parseFloat(editDistDividend.value) || 0;
        const roc = parseFloat(editDistRoc.value) || 0;
        const other = parseFloat(editDistOther.value) || 0;

        const sumComponents = interest + dividend + roc + other;
        if (sumComponents > 0) {
            editDistTotal.value = sumComponents.toFixed(2);
        }

        const gross = parseFloat(editDistTotal.value) || 0;
        const tds = parseFloat(editDistTds.value) || 0;
        const net = Math.max(0, gross - tds);
        editDistNet.value = net > 0 ? net.toFixed(2) : '';

        const units = parseFloat(editDistEligibleUnits.value) || 0;
        if (units > 0 && gross > 0) {
            editDistDpu.value = (gross / units).toFixed(4);
        }
    }

    [editDistInterest, editDistDividend, editDistRoc, editDistOther, editDistTotal, editDistTds].forEach(el => {
        el.addEventListener('input', calcEditDistTotals);
    });

    editDistDpu.addEventListener('input', function() {
        const dpuVal = parseFloat(editDistDpu.value) || 0;
        const units = parseFloat(editDistEligibleUnits.value) || 0;
        if (dpuVal > 0 && units > 0) {
            const calculatedGross = dpuVal * units;
            editDistTotal.value = calculatedGross.toFixed(2);
            const tds = parseFloat(editDistTds.value) || 0;
            editDistNet.value = Math.max(0, calculatedGross - tds).toFixed(2);
        }
    });

    editDistEligibleUnits.addEventListener('input', function() {
        const units = parseFloat(editDistEligibleUnits.value) || 0;
        const dpuVal = parseFloat(editDistDpu.value) || 0;
        const gross = parseFloat(editDistTotal.value) || 0;
        if (dpuVal > 0 && units > 0) {
            const calculatedGross = dpuVal * units;
            editDistTotal.value = calculatedGross.toFixed(2);
            const tds = parseFloat(editDistTds.value) || 0;
            editDistNet.value = Math.max(0, calculatedGross - tds).toFixed(2);
        } else if (gross > 0 && units > 0) {
            editDistDpu.value = (gross / units).toFixed(4);
        }
    });

    document.querySelectorAll('.open-edit-reit-dist').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editReitDistId').value = this.dataset.id;
            document.getElementById('editReitDistSubtitle').textContent = this.dataset.trust;
            document.getElementById('editDistPayoutDate').value = this.dataset.payoutDate;
            document.getElementById('editDistRecordDate').value = this.dataset.recordDate || '';
            document.getElementById('editDistEligibleUnits').value = this.dataset.eligibleUnits || '';
            document.getElementById('editDistDpu').value = this.dataset.dpu || '';
            document.getElementById('editDistInterest').value = this.dataset.interest || '0.00';
            document.getElementById('editDistDividend').value = this.dataset.dividend || '0.00';
            document.getElementById('editDistRoc').value = this.dataset.roc || '0.00';
            document.getElementById('editDistOther').value = this.dataset.other || '0.00';
            document.getElementById('editDistTotal').value = this.dataset.totalAmount || '';
            document.getElementById('editDistTds').value = this.dataset.tds || '0.00';
            document.getElementById('editDistNet').value = this.dataset.netReceived || '';
            document.getElementById('editDistQuarter').value = this.dataset.quarter || '';
            document.getElementById('editDistNotes').value = this.dataset.notes || '';
        });
    });

    // =========================================================================
    // 5. REITS/INVITS: DUAL FILTER & TOTAL AMOUNTS (DISTRIBUTIONS & TRADE LEDGER)
    // =========================================================================
    const FY_RANGES = <?= json_encode(get_fy_ranges()) ?>;

    function formatINR(val) {
        return '₹ ' + Number(val).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // --- A. DISTRIBUTION JOURNAL FILTER ENGINE ---
    const distTrustFilter = document.getElementById('distTrustFilter');
    const distDateFilter  = document.getElementById('distDateFilter');
    const distTable       = document.getElementById('reitDistTable');
    const distResetBtn    = document.getElementById('btnResetDistFilter');
    const distBadge       = document.getElementById('filteredDistBadge');
    const distSymbolSpan  = document.getElementById('filteredDistSymbol');
    const distNoRowsEl    = document.getElementById('distNoRows');

    const kpiDistNet     = document.getElementById('kpiDistNet');
    const kpiDistTds     = document.getElementById('kpiDistTds');
    const kpiDistTaxable = document.getElementById('kpiDistTaxable');
    const kpiDistExempt  = document.getElementById('kpiDistExempt');

    const footerDistSummaryTitle = document.getElementById('footerDistSummaryTitle');
    const footerDistInterest     = document.getElementById('footerDistInterest');
    const footerDistDividend     = document.getElementById('footerDistDividend');
    const footerDistRoc          = document.getElementById('footerDistRoc');
    const footerDistGross        = document.getElementById('footerDistGross');
    const footerDistTds          = document.getElementById('footerDistTds');
    const footerDistNet          = document.getElementById('footerDistNet');

    function applyDistFilters(updateUrl = true) {
        if (!distTable) return;

        const selectedTrust  = distTrustFilter ? distTrustFilter.value : 'ALL';
        const selectedFy     = distDateFilter ? distDateFilter.value : 'ALL';
        const selectedOption = distTrustFilter ? distTrustFilter.options[distTrustFilter.selectedIndex] : null;
        const selectedSymbol = selectedOption ? (selectedOption.dataset.symbol || '') : '';

        let fyStart = null;
        let fyEnd   = null;
        if (selectedFy === 'CURRENT_FY' && FY_RANGES.current) {
            fyStart = FY_RANGES.current.start;
            fyEnd   = FY_RANGES.current.end;
        } else if (selectedFy === 'LAST_FY' && FY_RANGES.last) {
            fyStart = FY_RANGES.last.start;
            fyEnd   = FY_RANGES.last.end;
        }

        const rows = distTable.querySelectorAll('tbody tr.reit-dist-row');
        let visibleCount = 0;
        let totalNet     = 0.0;
        let totalTds     = 0.0;
        let totalInt     = 0.0;
        let totalDiv     = 0.0;
        let totalRoc     = 0.0;
        let totalGross   = 0.0;

        rows.forEach(row => {
            const rowReitId = row.dataset.reitId;
            const rowDate   = row.dataset.date;
            const rowInt    = parseFloat(row.dataset.interest) || 0.0;
            const rowDiv    = parseFloat(row.dataset.dividend) || 0.0;
            const rowRoc    = parseFloat(row.dataset.roc) || 0.0;
            const rowGross  = parseFloat(row.dataset.gross) || 0.0;
            const rowTds    = parseFloat(row.dataset.tds) || 0.0;
            const rowNet    = parseFloat(row.dataset.net) || 0.0;

            const matchesTrust = (selectedTrust === 'ALL' || rowReitId === selectedTrust);
            let matchesDate    = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesTrust && matchesDate) {
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;
                totalNet   += rowNet;
                totalTds   += rowTds;
                totalInt   += rowInt;
                totalDiv   += rowDiv;
                totalRoc   += rowRoc;
                totalGross += rowGross;
            } else {
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        if (distNoRowsEl) {
            distNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        const taxableTotal = totalInt;
        const exemptTotal  = totalDiv + totalRoc;

        // Update KPI Cards
        if (kpiDistNet)     kpiDistNet.textContent     = formatINR(totalNet);
        if (kpiDistTds)     kpiDistTds.textContent     = formatINR(totalTds);
        if (kpiDistTaxable) kpiDistTaxable.textContent = formatINR(taxableTotal);
        if (kpiDistExempt)  kpiDistExempt.textContent  = formatINR(exemptTotal);

        // Update Footer Totals
        if (footerDistSummaryTitle) {
            let label = selectedTrust === 'ALL' ? 'Total' : selectedSymbol;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerDistSummaryTitle.textContent = `${label} (${visibleCount} Distributions)`;
        }
        if (footerDistInterest) footerDistInterest.textContent = formatINR(totalInt);
        if (footerDistDividend) footerDistDividend.textContent = formatINR(totalDiv);
        if (footerDistRoc)      footerDistRoc.textContent      = formatINR(totalRoc);
        if (footerDistGross)    footerDistGross.textContent    = formatINR(totalGross);
        if (footerDistTds)      footerDistTds.textContent      = formatINR(totalTds);
        if (footerDistNet)      footerDistNet.textContent      = formatINR(totalNet);

        // Refresh pagination slice
        if (window.reitDistPager) {
            window.reitDistPager.refresh();
        }

        // Toggle reset button & filtered badge
        const hasActiveFilter = (selectedTrust !== 'ALL' || selectedFy !== 'ALL');
        if (distResetBtn) distResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (distBadge) {
            distBadge.classList.toggle('d-none', !hasActiveFilter);
            if (distSymbolSpan) {
                let badgeText = selectedTrust !== 'ALL' ? selectedSymbol : 'All Trusts';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                distSymbolSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedTrust === 'ALL') url.searchParams.delete('trust_id');
            else url.searchParams.set('trust_id', selectedTrust);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (distTrustFilter) distTrustFilter.addEventListener('change', () => applyDistFilters(true));
    if (distDateFilter)  distDateFilter.addEventListener('change', () => applyDistFilters(true));
    if (distResetBtn) {
        distResetBtn.addEventListener('click', () => {
            if (distTrustFilter) distTrustFilter.value = 'ALL';
            if (distDateFilter)  distDateFilter.value  = 'ALL';
            applyDistFilters(true);
        });
    }

    // --- B. TRANSACTION HISTORY (TRADE LEDGER) FILTER ENGINE ---
    const ledgerTrustFilter = document.getElementById('ledgerTrustFilter');
    const ledgerDateFilter  = document.getElementById('ledgerDateFilter');
    const ledgerTable       = document.getElementById('reitLedgerTable');
    const ledgerResetBtn    = document.getElementById('btnResetTrustFilter');
    const ledgerBadge       = document.getElementById('filteredTrustBadge');
    const ledgerSymbolSpan  = document.getElementById('filteredTrustSymbol');
    const ledgerNoRowsEl    = document.getElementById('ledgerNoRows');

    const kpiBuyAmount   = document.getElementById('kpiBuyAmount');
    const kpiBuyDetails  = document.getElementById('kpiBuyDetails');
    const kpiSellAmount  = document.getElementById('kpiSellAmount');
    const kpiSellDetails = document.getElementById('kpiSellDetails');
    const kpiNetOutlay   = document.getElementById('kpiNetOutlay');
    const kpiNetDetails  = document.getElementById('kpiNetDetails');
    const kpiCharges     = document.getElementById('kpiCharges');

    const footerSummaryTitle    = document.getElementById('footerSummaryTitle');
    const footerActionBreakdown = document.getElementById('footerActionBreakdown');
    const footerTotalQty        = document.getElementById('footerTotalQty');
    const footerTotalCharges    = document.getElementById('footerTotalCharges');
    const footerTotalAmount     = document.getElementById('footerTotalAmount');
    const footerNote            = document.getElementById('footerNote');

    function applyLedgerFilters(updateUrl = true) {
        if (!ledgerTable) return;

        const selectedTrust  = ledgerTrustFilter ? ledgerTrustFilter.value : 'ALL';
        const selectedFy     = ledgerDateFilter ? ledgerDateFilter.value : 'ALL';
        const selectedOption = ledgerTrustFilter ? ledgerTrustFilter.options[ledgerTrustFilter.selectedIndex] : null;
        const selectedSymbol = selectedOption ? (selectedOption.dataset.symbol || '') : '';

        let fyStart = null;
        let fyEnd   = null;
        if (selectedFy === 'CURRENT_FY' && FY_RANGES.current) {
            fyStart = FY_RANGES.current.start;
            fyEnd   = FY_RANGES.current.end;
        } else if (selectedFy === 'LAST_FY' && FY_RANGES.last) {
            fyStart = FY_RANGES.last.start;
            fyEnd   = FY_RANGES.last.end;
        }

        const rows = ledgerTable.querySelectorAll('tbody tr.reit-ledger-row');
        let visibleCount = 0;
        let buyAmount    = 0.0;
        let sellAmount   = 0.0;
        let buyQty       = 0.0;
        let sellQty      = 0.0;
        let buyCount     = 0;
        let sellCount    = 0;
        let totalCharges = 0.0;

        rows.forEach(row => {
            const rowReitId = row.dataset.reitId;
            const rowDate   = row.dataset.date;
            const rowType   = row.dataset.type;
            const rowQty    = parseFloat(row.dataset.qty) || 0.0;
            const rowAmt    = parseFloat(row.dataset.amount) || 0.0;
            const rowChg    = parseFloat(row.dataset.charges) || 0.0;

            const matchesTrust = (selectedTrust === 'ALL' || rowReitId === selectedTrust);
            let matchesDate    = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesTrust && matchesDate) {
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;
                totalCharges += rowChg;

                if (rowType === 'BUY') {
                    buyAmount += rowAmt;
                    buyQty    += rowQty;
                    buyCount++;
                } else {
                    sellAmount += rowAmt;
                    sellQty    += rowQty;
                    sellCount++;
                }
            } else {
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        if (ledgerNoRowsEl) {
            ledgerNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        const netOutlay = buyAmount - sellAmount;

        // Update KPI Cards
        if (kpiBuyAmount) kpiBuyAmount.textContent = formatINR(buyAmount);
        if (kpiBuyDetails) kpiBuyDetails.textContent = `${buyCount} buys • ${buyQty.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} units`;

        if (kpiSellAmount) kpiSellAmount.textContent = formatINR(sellAmount);
        if (kpiSellDetails) kpiSellDetails.textContent = `${sellCount} sells • ${sellQty.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} units`;

        if (kpiNetOutlay) {
            kpiNetOutlay.textContent = formatINR(netOutlay);
            kpiNetOutlay.className = 'fw-bold mb-0 mt-1 ' + (netOutlay >= 0 ? 'text-primary' : 'text-success');
        }
        if (kpiNetDetails) {
            if (selectedTrust === 'ALL') {
                kpiNetDetails.textContent = 'Net cash invested in period';
            } else {
                const heldDiff = buyQty - sellQty;
                kpiNetDetails.textContent = `Net held from trades: ${heldDiff.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} units`;
            }
        }

        if (kpiCharges) kpiCharges.textContent = formatINR(totalCharges);

        // Update Footer Totals
        if (footerSummaryTitle) {
            let label = selectedTrust === 'ALL' ? 'Total' : selectedSymbol;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerSummaryTitle.textContent = `${label} (${visibleCount} Trades)`;
        }

        if (footerActionBreakdown) {
            footerActionBreakdown.innerHTML = `<span class="text-success">${buyCount} BUY</span> &bull; <span class="text-danger">${sellCount} SELL</span>`;
        }

        if (footerTotalQty) footerTotalQty.textContent = (buyQty + sellQty).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (footerTotalCharges) footerTotalCharges.textContent = formatINR(totalCharges);
        if (footerTotalAmount) {
            footerTotalAmount.textContent = formatINR(netOutlay);
            footerTotalAmount.className = 'text-end fw-bold fs-6 ' + (netOutlay >= 0 ? 'text-dark' : 'text-success');
        }
        if (footerNote) footerNote.textContent = netOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized';

        // Refresh pagination slice
        if (window.reitLedgerPager) {
            window.reitLedgerPager.refresh();
        }

        // Toggle reset button & filtered badge
        const hasActiveFilter = (selectedTrust !== 'ALL' || selectedFy !== 'ALL');
        if (ledgerResetBtn) ledgerResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (ledgerBadge) {
            ledgerBadge.classList.toggle('d-none', !hasActiveFilter);
            if (ledgerSymbolSpan) {
                let badgeText = selectedTrust !== 'ALL' ? selectedSymbol : 'All Trusts';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                ledgerSymbolSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedTrust === 'ALL') url.searchParams.delete('trust_id');
            else url.searchParams.set('trust_id', selectedTrust);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (ledgerTrustFilter) ledgerTrustFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerDateFilter)  ledgerDateFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerResetBtn) {
        ledgerResetBtn.addEventListener('click', () => {
            if (ledgerTrustFilter) ledgerTrustFilter.value = 'ALL';
            if (ledgerDateFilter)  ledgerDateFilter.value  = 'ALL';
            applyLedgerFilters(true);
        });
    }

    // Shortcuts from Holdings dropdown
    window.showTrustLedger = function(trustId) {
        const transTabBtn = document.getElementById('transactions-tab');
        if (transTabBtn) {
            bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        }
        if (ledgerTrustFilter) {
            ledgerTrustFilter.value = String(trustId);
            applyLedgerFilters(true);
        }
    };

    window.showTrustDistributions = function(trustId) {
        const distTabBtn = document.getElementById('distributions-tab');
        if (distTabBtn) {
            bootstrap.Tab.getOrCreateInstance(distTabBtn).show();
        }
        if (distTrustFilter) {
            distTrustFilter.value = String(trustId);
            applyDistFilters(true);
        }
    };

    // ==========================================
    // REIT / INVIT HOLDINGS SORTING
    // ==========================================
    const sortSelect = document.getElementById('sortReitsSelect');
    const holdingsTbody = document.getElementById('reitHoldingsTbody');
    const sortableHeaders = document.querySelectorAll('.sortable-reit-th');

    function sortReitsHoldings(sortBy) {
        if (!holdingsTbody) return;
        const rows = Array.from(holdingsTbody.querySelectorAll('tr.reit-holding-row'));
        if (rows.length === 0) return;

        const [field, dir] = sortBy.split('_');
        const isAsc = dir === 'asc';

        rows.sort((a, b) => {
            let valA, valB;
            if (field === 'security') {
                valA = (a.dataset.security || '').toLowerCase();
                valB = (b.dataset.security || '').toLowerCase();
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (field === 'type') {
                valA = (a.dataset.type || '').toLowerCase();
                valB = (b.dataset.type || '').toLowerCase();
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (field === 'invested') {
                valA = parseFloat(a.dataset.invested) || 0;
                valB = parseFloat(b.dataset.invested) || 0;
                return isAsc ? valA - valB : valB - valA;
            } else if (field === 'current') {
                valA = parseFloat(a.dataset.current) || 0;
                valB = parseFloat(b.dataset.current) || 0;
                return isAsc ? valA - valB : valB - valA;
            }
            return 0;
        });

        rows.forEach(r => holdingsTbody.appendChild(r));

        sortableHeaders.forEach(th => {
            const col = th.dataset.col;
            const icon = th.querySelector('i');
            if (icon) {
                if (col === field) {
                    icon.className = isAsc ? 'bi bi-sort-down text-primary small ms-1' : 'bi bi-sort-up text-primary small ms-1';
                } else {
                    icon.className = 'bi bi-arrow-down-up text-muted small ms-1';
                }
            }
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortReitsHoldings(this.value);
        });
    }

    sortableHeaders.forEach(th => {
        th.addEventListener('click', function() {
            const col = this.dataset.col;
            const currentVal = sortSelect ? sortSelect.value : '';
            let newDir = 'asc';
            if (currentVal.startsWith(col)) {
                newDir = currentVal.endsWith('asc') ? 'desc' : 'asc';
            } else if (col === 'invested' || col === 'current') {
                newDir = 'desc';
            }
            const newVal = `${col}_${newDir}`;
            if (sortSelect) sortSelect.value = newVal;
            sortReitsHoldings(newVal);
        });
    });

    // ==========================================
    // FIFO TAX LOG FILTERING (Trust, Gain Type, Date Range)
    // ==========================================
    const taxReitFilter  = document.getElementById('taxLogReitFilter');
    const taxTypeFilter  = document.getElementById('taxLogTypeFilter');
    const taxDateFilter  = document.getElementById('taxLogDateFilter');
    const taxResetBtn    = document.getElementById('btnResetTaxFilter');
    const taxBadge       = document.getElementById('filteredTaxBadge');
    const taxReitSpan    = document.getElementById('filteredTaxReitSpan');
    const taxTable       = document.getElementById('reitTaxLogTable');

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
    const footerTaxCost         = document.getElementById('footerTaxCost');
    const footerTaxProceeds     = document.getElementById('footerTaxProceeds');
    const footerTaxGain         = document.getElementById('footerTaxGain');

    function applyReitTaxFilters(updateUrl = true) {
        if (!taxTable) return;

        const selectedTrust = taxReitFilter ? taxReitFilter.value : 'ALL';
        const selectedType  = taxTypeFilter ? taxTypeFilter.value : 'ALL';
        const selectedFy    = taxDateFilter ? taxDateFilter.value : 'ALL';

        let fyStart = null;
        let fyEnd   = null;
        if (selectedFy === 'CURRENT_FY' && FY_RANGES.current) {
            fyStart = FY_RANGES.current.start;
            fyEnd   = FY_RANGES.current.end;
        } else if (selectedFy === 'LAST_FY' && FY_RANGES.last) {
            fyStart = FY_RANGES.last.start;
            fyEnd   = FY_RANGES.last.end;
        }

        const rows = taxTable.querySelectorAll('tbody tr.reit-taxlog-row');
        let visibleCount = 0;
        let totalMatchedUnits = 0;
        let totalCostBasis = 0;
        let totalNetProceeds = 0;
        let totalRealizedGain = 0;
        let totalStcg = 0;
        let totalLtcg = 0;
        let selectedSymbol = '';

        if (selectedTrust !== 'ALL' && taxReitFilter) {
            const opt = taxReitFilter.querySelector(`option[value="${selectedTrust}"]`);
            if (opt) selectedSymbol = opt.dataset.symbol || opt.textContent.split('—')[0].trim();
        }

        rows.forEach(row => {
            const rowTrustId  = row.dataset.trustId;
            const rowGainType = row.dataset.gainType;
            const rowDate     = row.dataset.date;

            const matchesTrust = (selectedTrust === 'ALL' || rowTrustId === selectedTrust);
            const matchesType  = (selectedType === 'ALL' || rowGainType === selectedType);
            let matchesDate    = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesTrust && matchesType && matchesDate) {
                row.classList.remove('d-none');
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;

                const units    = parseFloat(row.dataset.units) || 0;
                const cost     = parseFloat(row.dataset.cost) || 0;
                const proceeds = parseFloat(row.dataset.proceeds) || 0;
                const gain     = parseFloat(row.dataset.gain) || 0;

                totalMatchedUnits += units;
                totalCostBasis    += cost;
                totalNetProceeds  += proceeds;
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
        if (kpiTaxCost) kpiTaxCost.textContent = formatINR(totalCostBasis);
        if (kpiTaxProceeds) kpiTaxProceeds.textContent = formatINR(totalNetProceeds);
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
            let label = selectedTrust === 'ALL' ? 'Total' : selectedSymbol;
            if (selectedType !== 'ALL') label += ` [${selectedType}]`;
            if (selectedFy !== 'ALL') label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            footerTaxSummaryTitle.textContent = `${label} (${visibleCount} Lots)`;
        }
        if (footerTaxBreakdown) {
            footerTaxBreakdown.textContent = `STCG: ${formatINR(totalStcg)} • LTCG: ${formatINR(totalLtcg)}`;
        }
        if (footerTaxCost) footerTaxCost.textContent = formatINR(totalCostBasis);
        if (footerTaxProceeds) footerTaxProceeds.textContent = formatINR(totalNetProceeds);
        if (footerTaxGain) {
            footerTaxGain.textContent = (totalRealizedGain >= 0 ? '+' : '') + formatINR(totalRealizedGain);
            footerTaxGain.className = 'text-end py-3 fs-6 fw-bold ' + (totalRealizedGain >= 0 ? 'text-success' : 'text-danger');
        }

        // Refresh pagination slice
        if (window.reitTaxPager) {
            window.reitTaxPager.refresh();
        }

        // Toggle reset button & active filter badge
        const hasActiveFilter = (selectedTrust !== 'ALL' || selectedType !== 'ALL' || selectedFy !== 'ALL');
        if (taxResetBtn) taxResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (taxBadge) {
            taxBadge.classList.toggle('d-none', !hasActiveFilter);
            if (taxReitSpan) {
                let badgeParts = [];
                if (selectedTrust !== 'ALL') badgeParts.push(selectedSymbol);
                if (selectedType !== 'ALL') badgeParts.push(selectedType);
                if (selectedFy !== 'ALL') badgeParts.push(selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                taxReitSpan.textContent = badgeParts.join(' • ');
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedTrust === 'ALL') url.searchParams.delete('tax_trust_id');
            else url.searchParams.set('tax_trust_id', selectedTrust);

            if (selectedType === 'ALL') url.searchParams.delete('tax_type');
            else url.searchParams.set('tax_type', selectedType);

            if (selectedFy === 'ALL') url.searchParams.delete('tax_fy');
            else url.searchParams.set('tax_fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (taxReitFilter) taxReitFilter.addEventListener('change', () => applyReitTaxFilters(true));
    if (taxTypeFilter) taxTypeFilter.addEventListener('change', () => applyReitTaxFilters(true));
    if (taxDateFilter) taxDateFilter.addEventListener('change', () => applyReitTaxFilters(true));
    if (taxResetBtn) {
        taxResetBtn.addEventListener('click', () => {
            if (taxReitFilter) taxReitFilter.value = 'ALL';
            if (taxTypeFilter) taxTypeFilter.value = 'ALL';
            if (taxDateFilter) taxDateFilter.value = 'ALL';
            applyReitTaxFilters(true);
        });
    }

    // Deep link handling on page load
    const urlParams = new URLSearchParams(window.location.search);
    const initialTrustId = urlParams.get('trust_id');
    const initialFy      = urlParams.get('fy');
    const initialTab     = urlParams.get('tab');
    const hash           = window.location.hash;

    if (initialTrustId) {
        if (ledgerTrustFilter) ledgerTrustFilter.value = initialTrustId;
        if (distTrustFilter)   distTrustFilter.value   = initialTrustId;
    }
    if (initialFy) {
        if (ledgerDateFilter) ledgerDateFilter.value = initialFy;
        if (distDateFilter)   distDateFilter.value   = initialFy;
    }

    if ((initialTrustId || initialFy || initialTab || hash) && (ledgerTable || distTable)) {
        if (ledgerTable) applyLedgerFilters(false);
        if (distTable)   applyDistFilters(false);

        if (initialTab === 'transactions' || (initialTrustId && !initialTab) || hash === '#transactions') {
            const transTabBtn = document.getElementById('transactions-tab');
            if (transTabBtn) bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        } else if (initialTab === 'distributions' || hash === '#distributions') {
            const distTabBtn = document.getElementById('distributions-tab');
            if (distTabBtn) bootstrap.Tab.getOrCreateInstance(distTabBtn).show();
        }
    }

    const taxReitParam = urlParams.get('tax_trust_id');
    const taxTypeParam = urlParams.get('tax_type');
    const taxFyParam   = urlParams.get('tax_fy');
    if (taxReitParam || taxTypeParam || taxFyParam || initialTab === 'capitalgains' || hash === '#capitalgains') {
        const taxTabBtn = document.getElementById('capitalgains-tab');
        if (taxTabBtn && (initialTab === 'capitalgains' || hash === '#capitalgains')) {
            bootstrap.Tab.getOrCreateInstance(taxTabBtn).show();
        }
        if (taxReitParam && taxReitFilter) taxReitFilter.value = taxReitParam;
        if (taxTypeParam && taxTypeFilter) taxTypeFilter.value = taxTypeParam;
        if (taxFyParam && taxDateFilter) taxDateFilter.value = taxFyParam;
        applyReitTaxFilters(false);
    }

    // ==========================================
    // TABLE PAGINATION INITIALIZATION
    // ==========================================
    if (typeof window.initTablePagination === 'function') {
        window.reitLedgerPager = window.initTablePagination({
            tableId: 'reitLedgerTable',
            rowSelector: '.reit-ledger-row',
            infoId: 'reitLedgerPageInfo',
            sizeSelectId: 'reitLedgerPageSize',
            controlsId: 'reitLedgerPageControls'
        });

        window.reitTaxPager = window.initTablePagination({
            tableId: 'reitTaxLogTable',
            rowSelector: '.reit-taxlog-row',
            infoId: 'reitTaxPageInfo',
            sizeSelectId: 'reitTaxPageSize',
            controlsId: 'reitTaxPageControls'
        });

        window.reitDistPager = window.initTablePagination({
            tableId: 'reitDistTable',
            rowSelector: '.reit-dist-row',
            infoId: 'reitDistPageInfo',
            sizeSelectId: 'reitDistPageSize',
            controlsId: 'reitDistPageControls'
        });
    }
});
</script>

<?= $this->endSection() ?>
