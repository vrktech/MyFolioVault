<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

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
        <a href="<?= base_url('bonds/new') ?>" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i>Add New Bond
        </a>
    </div>
</div>

<!-- 5 Executive KPI Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Current Portfolio Value -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Portfolio Value (CMP)</div>
            <div class="fs-5 fw-bold text-dark"><?= format_inr($summary['total_current_value']) ?></div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                <i class="bi bi-check-circle-fill text-success me-1"></i><?= $summary['active_holdings_count'] ?> Active Bonds
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

    <!-- Cumulative Interest Earned -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Interest Earned (Net)</div>
            <div class="fs-5 fw-bold text-success">
                <?= format_inr($summary['total_interest_earned']) ?>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Total Coupons Received</div>
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
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Total Net Gains</div>
            <div class="fs-5 fw-bold">
                <?= format_pnl($summary['total_net_return']) ?>
            </div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Capital P&L + Interest</div>
        </div>
    </div>
</div>

<!-- Main Tabs Section -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-3 px-4 rounded-top-4">
        <ul class="nav nav-tabs card-header-tabs border-0" id="bondsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="holdings-tab" data-bs-toggle="tab" data-bs-target="#holdings" type="button" role="tab">
                    <i class="bi bi-receipt me-1 text-danger"></i>Active Holdings (<?= count($activeHoldings ?? $holdings) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="past-holdings-tab" data-bs-toggle="tab" data-bs-target="#past-holdings" type="button" role="tab">
                    <i class="bi bi-archive me-1 text-secondary"></i>Past Holdings (<?= count($pastHoldings ?? []) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="payouts-tab" data-bs-toggle="tab" data-bs-target="#payouts" type="button" role="tab">
                    <i class="bi bi-cash-coin me-1 text-success"></i>Interest Payouts Journal (<?= count($payouts) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="capitalgains-tab" data-bs-toggle="tab" data-bs-target="#capitalgains" type="button" role="tab">
                    <i class="bi bi-clock-history me-1 text-primary"></i>FIFO Tax &amp; Redemption Log (<?= count($capitalGains) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
                    <i class="bi bi-card-checklist me-1 text-dark"></i>Trade Ledger (<?= count($transactions) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="bondsTabContent">
            
            <!-- TAB 1: ACTIVE HOLDINGS -->
            <div class="tab-pane fade show active p-3" id="holdings" role="tabpanel">
                <?php if (empty($activeHoldings ?? $holdings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-receipt fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Active Bond Holdings Found</h5>
                        <p class="text-muted small">You don't have any active bond holdings. Click below to add your first bond security!</p>
                        <a href="<?= base_url('bonds/new') ?>" class="btn btn-primary btn-sm rounded-3">
                            <i class="bi bi-plus-lg me-1"></i>Add New Bond
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Active Holdings Sorting Toolbar -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($activeHoldings ?? $holdings) ?></strong> active bond holdings
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="sortBondsSelect" class="small text-muted mb-0 fw-semibold text-nowrap"><i class="bi bi-sort-down me-1"></i>Sort By:</label>
                            <select id="sortBondsSelect" class="form-select form-select-sm shadow-none border-secondary-subtle" style="width: auto;">
                                <option value="current_desc" selected>Current Value (High → Low)</option>
                                <option value="current_asc">Current Value (Low → High)</option>
                                <option value="name_asc">Bond Name (A → Z)</option>
                                <option value="name_desc">Bond Name (Z → A)</option>
                                <option value="interest_desc">Last Interest Date (Recent First)</option>
                                <option value="interest_asc">Last Interest Date (Oldest First)</option>
                                <option value="maturity_asc">Maturity Date (Nearest First)</option>
                                <option value="maturity_desc">Maturity Date (Furthest First)</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="bondHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="cursor-pointer sortable-bond-th" data-col="name" role="button" title="Click to sort by Bond Name">
                                        Bond Name / ISIN <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Units</th>
                                    <th>Coupon</th>
                                    <th class="cursor-pointer sortable-bond-th" data-col="maturity" role="button" title="Click to sort by Maturity Date">
                                        Maturity Date <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Avg Cost (₹)</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end cursor-pointer sortable-bond-th" data-col="current" role="button" title="Click to sort by Current Value">
                                        Current (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Capital P&L</th>
                                    <th class="cursor-pointer sortable-bond-th" data-col="interest" role="button" title="Click to sort by Last Payout">
                                        Last Payout <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-center" style="min-width: 160px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="bondHoldingsTbody">
                                <?php foreach (($activeHoldings ?? $holdings) as $h): ?>
                                    <?php $isMatured = ($h['days_to_maturity'] <= 0 || date('Y-m-d') >= $h['maturity_date']); ?>
                                    <tr class="bond-holding-row"
                                        data-name="<?= esc(strtolower($h['bond_name'])) ?>"
                                        data-maturity="<?= esc($h['maturity_date']) ?>"
                                        data-current="<?= (float)$h['current_value'] ?>"
                                        data-interest="<?= esc($h['last_interest_paid_date'] ?? '') ?>">
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0"><?= esc($h['bond_name']) ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <?php
                                                    $catBadge = match($h['category']) {
                                                        'SGB'            => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                                        'GOVT_SECURITY'  => 'bg-success-subtle text-success border-success-subtle',
                                                        'CORPORATE_NCD'  => 'bg-primary-subtle text-primary border-primary-subtle',
                                                        'TAX_FREE'       => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                                        default          => 'bg-light text-secondary border',
                                                    };
                                                    $catLabel = match($h['category']) {
                                                        'SGB'            => 'SGB Gold',
                                                        'GOVT_SECURITY'  => 'G-Sec',
                                                        'CORPORATE_NCD'  => 'Corp NCD',
                                                        'TAX_FREE'       => 'Tax-Free',
                                                        default          => esc($h['category']),
                                                    };
                                                    ?>
                                                    <span class="badge <?= $catBadge ?> border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= $catLabel ?>
                                                    </span>
                                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.65rem;">
                                                        <?= esc($h['isin']) ?>
                                                    </span>
                                                    <?php if (!empty($h['bond_symbol'])): ?>
                                                        <?= stock_badge($h['bond_symbol']) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?= number_format($h['active_quantity'], 0) ?>
                                            <div class="text-muted small" style="font-size: 0.7rem;">
                                                <?= $h['category'] === 'SGB' ? 'grams' : 'units' ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= number_format($h['coupon_rate'], 2) ?>% p.a.</div>
                                            <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                <?= match($h['interest_frequency']) {
                                                    'MONTHLY'     => 'Monthly',
                                                    'QUARTERLY'   => 'Quarterly',
                                                    'SEMI_ANNUAL' => 'Semi-Annual',
                                                    'ANNUAL'      => 'Annual',
                                                    'CUMULATIVE'  => 'Cumulative',
                                                    default       => esc($h['interest_frequency']),
                                                } ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-dark small"><?= date('d-M-Y', strtotime($h['maturity_date'])) ?></div>
                                            <small class="text-muted" style="font-size: 0.72rem;">
                                                <?php if ($h['days_to_maturity'] > 0): ?>
                                                    <?= $h['days_to_maturity'] ?> days left
                                                <?php else: ?>
                                                    <span class="text-danger fw-bold">Matured</span>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td class="text-end text-muted small">
                                            <?= format_inr($h['avg_buy_price']) ?>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <a href="#" class="text-decoration-none text-dark open-price-modal" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#bondPriceModal"
                                               data-id="<?= $h['id'] ?>"
                                               data-name="<?= esc($h['bond_name']) ?>"
                                               data-price="<?= $h['current_market_price'] ?>"
                                               title="Click to update market price">
                                                <?= format_inr($h['current_market_price']) ?>
                                                <i class="bi bi-pencil-fill text-muted ms-1" style="font-size: 0.65rem;"></i>
                                            </a>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            <?= format_inr($h['current_value']) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($h['unrealized_pnl'], $h['unrealized_pnl_percent'], false, true) ?>
                                        </td>
                                        <td>
                                            <!-- LAST INTEREST PAID DATE (Moved to last before Action) -->
                                            <?php if (!empty($h['last_interest_paid_date'])): ?>
                                                <div class="fw-semibold text-success">
                                                    <i class="bi bi-calendar-check me-1"></i><?= date('d-M-Y', strtotime($h['last_interest_paid_date'])) ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.72rem;">
                                                    <?= format_inr($h['interest_earned']) ?> earned
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted small">— Not yet paid</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger rounded-start-3 px-2 py-1 open-bond-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#bondTransactionModal"
                                                        data-id="<?= $h['id'] ?>"
                                                        data-name="<?= esc($h['bond_name']) ?>"
                                                        data-isin="<?= esc($h['isin']) ?>"
                                                        data-cat="<?= esc($h['category']) ?>"
                                                        data-qty="<?= $h['active_quantity'] ?>"
                                                        data-cmp="<?= $h['current_market_price'] ?>"
                                                        data-avg="<?= $h['avg_buy_price'] ?>"
                                                        data-face="<?= $h['face_value'] ?>"
                                                        data-rate="<?= $h['coupon_rate'] ?>"
                                                        data-freq="<?= esc($h['interest_frequency']) ?>">
                                                    <i class="bi bi-lightning-charge-fill me-1"></i>Add Trans
                                                </button>
                                                <?php if ($isMatured && $h['active_quantity'] > 0): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-dark px-2 py-1 open-maturity-modal"
                                                            data-id="<?= $h['id'] ?>"
                                                            data-name="<?= esc($h['bond_name']) ?>"
                                                            data-isin="<?= esc($h['isin']) ?>"
                                                            data-cat="<?= esc($h['category']) ?>"
                                                            data-qty="<?= $h['active_quantity'] ?>"
                                                            data-face="<?= (float)$h['face_value'] ?>"
                                                            data-cmp="<?= (float)$h['current_market_price'] ?>"
                                                            data-maturity="<?= esc($h['maturity_date']) ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#bondMaturityRedemptionModal"
                                                            title="Bond has reached maturity date. Click to record final principal redemption.">
                                                        <i class="bi bi-award me-1 text-warning"></i>Redeem
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split rounded-end-3" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <?php if ($isMatured && $h['active_quantity'] > 0): ?>
                                                        <li>
                                                            <button type="button" 
                                                                    class="dropdown-item small text-dark fw-semibold open-maturity-modal"
                                                                    data-id="<?= $h['id'] ?>"
                                                                    data-name="<?= esc($h['bond_name']) ?>"
                                                                    data-isin="<?= esc($h['isin']) ?>"
                                                                    data-cat="<?= esc($h['category']) ?>"
                                                                    data-qty="<?= $h['active_quantity'] ?>"
                                                                    data-face="<?= (float)$h['face_value'] ?>"
                                                                    data-cmp="<?= (float)$h['current_market_price'] ?>"
                                                                    data-maturity="<?= esc($h['maturity_date']) ?>"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#bondMaturityRedemptionModal">
                                                                <i class="bi bi-award-fill me-2 text-warning"></i>Redeem at Maturity
                                                            </button>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <button class="dropdown-item small open-edit-bond-modal"
                                                                data-id="<?= $h['id'] ?>"
                                                                data-name="<?= esc($h['bond_name']) ?>"
                                                                data-isin="<?= esc($h['isin']) ?>"
                                                                data-symbol="<?= esc($h['bond_symbol'] ?? '') ?>"
                                                                data-cat="<?= esc($h['category']) ?>"
                                                                data-issuer="<?= esc($h['issuer'] ?? '') ?>"
                                                                data-face="<?= (float)$h['face_value'] ?>"
                                                                data-rate="<?= (float)$h['coupon_rate'] ?>"
                                                                data-freq="<?= esc($h['interest_frequency']) ?>"
                                                                data-maturity="<?= esc($h['maturity_date']) ?>"
                                                                data-issue="<?= esc($h['issue_date'] ?? '') ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editBondModal">
                                                            <i class="bi bi-pencil me-2 text-primary"></i>Edit Bond Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('bonds?bond_id=' . $h['id'] . '&tab=transactions') ?>" onclick="showBondLedger(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-success" href="<?= base_url('bonds?bond_id=' . $h['id'] . '&tab=payouts') ?>" onclick="showBondPayouts(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-cash-coin me-2"></i>View Interest History
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                            href="<?= base_url('bonds/delete/' . $h['id']) ?>" 
                                                            onclick="return confirm('Delete this Bond and all related transaction/coupon history?');">
                                                            <i class="bi bi-trash me-2"></i>Delete Bond
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

            <!-- TAB 1B: PAST HOLDINGS (CLOSED / MATURED POSITIONS) -->
            <div class="tab-pane fade p-3" id="past-holdings" role="tabpanel">
                <?php if (empty($pastHoldings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-archive fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Past Bond Holdings Found</h5>
                        <p class="text-muted small">You don't have any fully redeemed or sold bond positions yet. When you sell or redeem 100% of a bond, it will appear here along with your lifetime realized capital gains and interest earned.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($pastHoldings) ?></strong> past / closed bond investments
                        </div>
                        <div class="text-muted small">
                            <span class="badge bg-secondary-subtle text-secondary border"><i class="bi bi-info-circle me-1"></i>0 Active Quantity &bull; Lifetime Matured &amp; Exited Positions</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="bondPastHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Bond Name / ISIN</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end">Realized Capital P&L</th>
                                    <th class="text-end">Interest Earned (₹)</th>
                                    <th class="text-end">Net Return (₹)</th>
                                    <th class="text-center" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pastHoldings as $ph): ?>
                                    <?php
                                    $catBadge = match($ph['category']) {
                                        'SGB'            => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                        'GOVT_SECURITY'  => 'bg-success-subtle text-success border-success-subtle',
                                        'CORPORATE_NCD'  => 'bg-primary-subtle text-primary border-primary-subtle',
                                        'TAX_FREE'       => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                        default          => 'bg-light text-secondary border',
                                    };
                                    ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0"><?= esc($ph['bond_name']) ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <span class="badge border <?= $catBadge ?> px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= esc($ph['category']) ?>
                                                    </span>
                                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.65rem;">
                                                        <?= esc($ph['isin']) ?>
                                                    </span>
                                                    <?php if (!empty($ph['bond_symbol'])): ?>
                                                        <?= stock_badge($ph['bond_symbol']) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                <i class="bi bi-check2-all me-1"></i>Closed
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <?= format_inr($ph['current_market_price']) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($ph['realized_pnl']) ?>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                STCG: <?= format_inr($ph['stcg']) ?> &bull; LTCG: <?= format_inr($ph['ltcg'] + $ph['sgb_exempt']) ?>
                                            </div>
                                        </td>
                                        <td class="text-end text-success fw-medium">
                                            <?= ($ph['interest_earned'] ?? 0) > 0 ? format_inr($ph['interest_earned']) : '—' ?>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= format_pnl($ph['total_return']) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success rounded-start-3 px-2 open-bond-trans-modal" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#bondTransactionModal"
                                                        data-id="<?= $ph['id'] ?>"
                                                        data-name="<?= esc($ph['bond_name']) ?>"
                                                        data-isin="<?= esc($ph['isin']) ?>"
                                                        data-symbol="<?= esc($ph['bond_symbol'] ?? '') ?>"
                                                        data-cat="<?= esc($ph['category']) ?>"
                                                        data-qty="0"
                                                        data-cmp="<?= $ph['current_market_price'] ?>"
                                                        data-avg="0"
                                                        data-face="<?= $ph['face_value'] ?>"
                                                        data-rate="<?= $ph['coupon_rate'] ?>"
                                                        data-freq="<?= esc($ph['interest_frequency']) ?>">
                                                    <i class="bi bi-plus-circle me-1"></i>Buy Again
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split rounded-end-3" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <button class="dropdown-item small open-edit-bond-modal"
                                                                data-id="<?= $ph['id'] ?>"
                                                                data-name="<?= esc($ph['bond_name']) ?>"
                                                                data-isin="<?= esc($ph['isin']) ?>"
                                                                data-symbol="<?= esc($ph['bond_symbol'] ?? '') ?>"
                                                                data-cat="<?= esc($ph['category']) ?>"
                                                                data-issuer="<?= esc($ph['issuer'] ?? '') ?>"
                                                                data-face="<?= (float)$ph['face_value'] ?>"
                                                                data-rate="<?= (float)$ph['coupon_rate'] ?>"
                                                                data-freq="<?= esc($ph['interest_frequency']) ?>"
                                                                data-maturity="<?= esc($ph['maturity_date']) ?>"
                                                                data-issue="<?= esc($ph['issue_date'] ?? '') ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editBondModal">
                                                            <i class="bi bi-pencil me-2 text-primary"></i>Edit Bond Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('bonds?bond_id=' . $ph['id'] . '&tab=transactions') ?>" onclick="showBondLedger(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-success" href="<?= base_url('bonds?bond_id=' . $ph['id'] . '&tab=payouts') ?>" onclick="showBondPayouts(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-cash-coin me-2"></i>View Interest History
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-info-emphasis" href="<?= base_url('bonds?tax_bond_id=' . $ph['id'] . '&tab=capitalgains') ?>" onclick="showBondTaxLog(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-receipt-cutoff me-2"></i>View Tax &amp; Redemption Log
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                            href="<?= base_url('bonds/delete/' . $ph['id']) ?>" 
                                                            onclick="return confirm('Delete this Bond and all related transaction/coupon history?');">
                                                            <i class="bi bi-trash me-2"></i>Delete Bond
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

            <!-- TAB 2: INTEREST PAYOUTS JOURNAL -->
            <div class="tab-pane fade p-3" id="payouts" role="tabpanel">
                <?php if (empty($payouts)): ?>
                    <p class="text-muted text-center py-4">No coupon interest payouts recorded yet.</p>
                <?php else: ?>
                    <?php
                    $fyRanges             = get_fy_ranges();
                    $payoutBonds          = [];
                    $overallGrossInterest = 0.0;
                    $overallTdsDeducted   = 0.0;
                    $overallNetInterest   = 0.0;
                    $overallPayoutCount   = 0;

                    foreach ($payouts as $p) {
                        $bId = (int)$p['bond_id'];
                        if (!isset($payoutBonds[$bId])) {
                            $payoutBonds[$bId] = [
                                'id'    => $bId,
                                'name'  => $p['bond_name'],
                                'isin'  => $p['isin'],
                                'count' => 0,
                            ];
                        }
                        $payoutBonds[$bId]['count']++;

                        $gross = (float)$p['gross_interest'];
                        $tds   = (float)$p['tds_deducted'];
                        $net   = (float)$p['net_interest'];

                        $overallGrossInterest += $gross;
                        $overallTdsDeducted   += $tds;
                        $overallNetInterest   += $net;
                        $overallPayoutCount++;
                    }
                    uasort($payoutBonds, fn($a, $b) => strcmp($a['name'], $b['name']));
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
                                            <label class="form-label mb-0 fw-semibold text-dark small">Filter Interest</label>
                                            <div class="text-muted" style="font-size: 0.72rem;">Filter by bond &amp; financial year</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                        <div style="min-width: 220px; max-width: 300px;" class="flex-grow-1">
                                            <select id="payoutBondFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">-- All Bonds (<?= count($payouts) ?> Payouts) --</option>
                                                <?php foreach ($payoutBonds as $bId => $b): ?>
                                                    <option value="<?= $bId ?>" data-name="<?= esc($b['name']) ?>">
                                                        <?= esc($b['name']) ?> (<?= esc($b['isin']) ?>) (<?= $b['count'] ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div style="min-width: 170px; max-width: 200px;">
                                            <select id="payoutDateFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">All Dates (All Time)</option>
                                                <option value="CURRENT_FY">Current FY (<?= $fyRanges['current']['label'] ?>)</option>
                                                <option value="LAST_FY">Last FY (<?= $fyRanges['last']['label'] ?>)</option>
                                            </select>
                                        </div>
                                        <button type="button" id="btnResetPayoutFilter" class="btn btn-sm btn-outline-secondary d-none" title="Reset all filters">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                        </button>
                                        <span id="filteredPayoutBadge" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 small d-none">
                                            <i class="bi bi-check2-circle me-1"></i><span id="filteredPayoutName"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4 KPI Summary Metric Cards -->
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-primary">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Gross Coupon Interest</span>
                                            <h5 class="fw-bold text-primary mb-0 mt-1" id="kpiPayoutGross"><?= format_inr($overallGrossInterest) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Total coupon entitlement
                                            </div>
                                        </div>
                                        <div class="bg-primary-subtle text-primary rounded-3 p-2">
                                            <i class="bi bi-cash-coin fs-5"></i>
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
                                            <h5 class="fw-bold text-danger mb-0 mt-1" id="kpiPayoutTds"><?= format_inr($overallTdsDeducted) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Tax withheld at source
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
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-success">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Net Interest Received</span>
                                            <h5 class="fw-bold text-success mb-0 mt-1" id="kpiPayoutNet"><?= format_inr($overallNetInterest) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Bank credits received
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
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-info">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Interest Payouts</span>
                                            <h5 class="fw-bold text-dark mb-0 mt-1" id="kpiPayoutCount"><?= $overallPayoutCount ?> Credits</h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Payment transactions
                                            </div>
                                        </div>
                                        <div class="bg-info-subtle text-info rounded-3 p-2">
                                            <i class="bi bi-receipt-cutoff fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="bondPayoutsTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Payout Date</th>
                                    <th>Bond Name / ISIN</th>
                                    <th>Frequency</th>
                                    <th class="text-end">Coupon Rate</th>
                                    <th class="text-end">Gross Interest (₹)</th>
                                    <th class="text-end">TDS Deducted (₹)</th>
                                    <th class="text-end">Net Received (₹)</th>
                                    <th>Period / FY</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payouts as $p): ?>
                                    <tr class="bond-payout-row"
                                        data-bond-id="<?= $p['bond_id'] ?>"
                                        data-name="<?= esc($p['bond_name']) ?>"
                                        data-date="<?= $p['payout_date'] ?>"
                                        data-gross="<?= (float)$p['gross_interest'] ?>"
                                        data-tds="<?= (float)$p['tds_deducted'] ?>"
                                        data-net="<?= (float)$p['net_interest'] ?>">
                                        <td><?= date('d-M-Y', strtotime($p['payout_date'])) ?></td>
                                        <td>
                                            <strong><?= esc($p['bond_name']) ?></strong>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($p['isin']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                <?= match($p['interest_frequency']) {
                                                    'MONTHLY'     => 'Monthly',
                                                    'QUARTERLY'   => 'Quarterly',
                                                    'SEMI_ANNUAL' => 'Semi-Annual',
                                                    'ANNUAL'      => 'Annual',
                                                    'CUMULATIVE'  => 'Cumulative',
                                                    default       => esc($p['interest_frequency']),
                                                } ?>
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold"><?= number_format($p['coupon_rate'], 2) ?>%</td>
                                        <td class="text-end"><?= format_inr($p['gross_interest']) ?></td>
                                        <td class="text-end text-danger"><?= format_inr($p['tds_deducted']) ?></td>
                                        <td class="text-end fw-bold text-success"><?= format_inr($p['net_interest']) ?></td>
                                        <td>
                                            <div><?= esc($p['period_description'] ?: '—') ?></div>
                                            <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">FY <?= esc($p['financial_year']) ?></span>
                                        </td>
                                        <td class="text-secondary"><?= esc($p['notes'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary py-0 px-1.5 open-edit-bond-interest"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editBondInterestModal"
                                                    data-id="<?= $p['id'] ?>"
                                                    data-name="<?= esc($p['bond_name']) ?>"
                                                    data-date="<?= esc($p['payout_date']) ?>"
                                                    data-rate="<?= (float)$p['coupon_rate'] ?>"
                                                    data-gross="<?= (float)$p['gross_interest'] ?>"
                                                    data-tds="<?= (float)$p['tds_deducted'] ?>"
                                                    data-desc="<?= esc($p['period_description'] ?? '') ?>"
                                                    data-notes="<?= esc($p['notes'] ?? '') ?>"
                                                    title="Edit Interest Payment">
                                                <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <a href="<?= base_url('bonds/delete-interest/' . $p['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger py-0 px-1.5 ms-1"
                                               onclick="return confirm('Delete this interest payout record?');"
                                               title="Delete Interest">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="payoutNoRows" class="d-none text-center py-4">
                                    <td colspan="10" class="text-muted py-4">
                                        <i class="bi bi-funnel text-secondary fs-4 d-block mb-1"></i>
                                        No interest payouts found matching the selected bond and date filter.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light fw-semibold border-top-2">
                                <tr>
                                    <td colspan="4">
                                        <span id="footerPayoutSummaryTitle">Total (<?= count($payouts) ?> Payouts)</span>
                                    </td>
                                    <td class="text-end" id="footerPayoutGross"><?= format_inr($overallGrossInterest) ?></td>
                                    <td class="text-end text-danger" id="footerPayoutTds"><?= format_inr($overallTdsDeducted) ?></td>
                                    <td class="text-end fw-bold text-success fs-6" id="footerPayoutNet"><?= format_inr($overallNetInterest) ?></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'bondPayout']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 3: FIFO TAX & REDEMPTION LOG -->
            <div class="tab-pane fade p-3" id="capitalgains" role="tabpanel">
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center rounded-3 small">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Indian Bond Taxation & FIFO Matching:</strong>
                        Secondary market sales before maturity are matched in First-In, First-Out order (Holding &gt; 365 days = LTCG, &le; 365 days = STCG).
                        <strong>SGB Redemption at RBI Maturity:</strong> Completely <strong>tax-exempt under Section 47(viic)</strong> of the Income Tax Act!
                    </div>
                </div>

                <?php if (empty($capitalGains)): ?>
                    <p class="text-muted text-center py-4">No realized sales or maturity redemptions recorded yet.</p>
                <?php else: ?>
                    <?php
                    $fyRanges          = get_fy_ranges();
                    $taxBonds          = [];
                    $totalMatchedUnits = 0.0;
                    $totalBuyCost      = 0.0;
                    $totalExitProceeds = 0.0;
                    $totalRealizedGain = 0.0;
                    $totalStcg         = 0.0;
                    $totalLtcg         = 0.0;
                    $totalExempt       = 0.0;

                    foreach ($capitalGains as $cg) {
                        $bId = (int)$cg['bond_id'];
                        if (!isset($taxBonds[$bId])) {
                            $taxBonds[$bId] = [
                                'id'    => $bId,
                                'name'  => $cg['bond_name'],
                                'isin'  => $cg['isin'],
                                'count' => 0,
                            ];
                        }
                        $taxBonds[$bId]['count']++;
                        $qty      = (float)$cg['quantity_matched'];
                        $cost     = $qty * (float)$cg['buy_price'];
                        $proceeds = $qty * (float)$cg['exit_price'];
                        $gain     = (float)$cg['realized_gain'];

                        $totalMatchedUnits += $qty;
                        $totalBuyCost      += $cost;
                        $totalExitProceeds += $proceeds;
                        $totalRealizedGain += $gain;
                        if ($cg['gain_type'] === 'EXEMPT_SGB_MATURITY') {
                            $totalExempt += $gain;
                        } elseif ($cg['gain_type'] === 'LTCG') {
                            $totalLtcg += $gain;
                        } else {
                            $totalStcg += $gain;
                        }
                    }
                    ?>

                    <!-- Filter Toolbar: Bond Selector + Gain Type + Date Range -->
                    <div class="bg-light p-3 rounded-4 border mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="taxLogBondFilter" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-filter me-1"></i>Filter by Bond
                                </label>
                                <select class="form-select form-select-sm shadow-sm" id="taxLogBondFilter">
                                    <option value="ALL">All Bonds (<?= count($capitalGains) ?> lots)</option>
                                    <?php foreach ($taxBonds as $tb): ?>
                                        <option value="<?= $tb['id'] ?>" data-name="<?= esc($tb['name']) ?>">
                                            <?= esc($tb['name']) ?> (<?= $tb['count'] ?> lots)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="taxLogTypeFilter" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-tags me-1"></i>Tax Category
                                </label>
                                <select class="form-select form-select-sm shadow-sm" id="taxLogTypeFilter">
                                    <option value="ALL">All Categories</option>
                                    <option value="EXEMPT_SGB_MATURITY">Sec 47(viic) Tax-Exempt (SGB)</option>
                                    <option value="LTCG">LTCG (&gt; 365 Days)</option>
                                    <option value="STCG">STCG (&le; 365 Days)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="taxLogDateFilter" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-calendar-range me-1"></i>Date Range (Exit Date)
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
                                <i class="bi bi-funnel-fill me-1"></i><span id="filteredTaxBondSpan">Active Filter</span>
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
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Exit Proceeds</div>
                                <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxProceeds"><?= format_inr($totalExitProceeds) ?></div>
                                <div class="text-muted small" style="font-size: 0.72rem;">Gross redemption / sale</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-<?= $totalRealizedGain >= 0 ? 'success' : 'danger' ?> border-4">
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Net Realized P&amp;L</div>
                                <div class="fs-5 fw-bold <?= $totalRealizedGain >= 0 ? 'text-success' : 'text-danger' ?> mb-0 mt-1" id="kpiTaxGain">
                                    <?= ($totalRealizedGain >= 0 ? '+' : '') . format_inr($totalRealizedGain) ?>
                                </div>
                                <div class="text-muted small" style="font-size: 0.72rem;" id="kpiTaxBreakdown">
                                    Exempt: <?= format_inr($totalExempt) ?> &bull; LTCG: <?= format_inr($totalLtcg) ?> &bull; STCG: <?= format_inr($totalStcg) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="bondTaxLogTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Bond Name</th>
                                    <th>Buy Date</th>
                                    <th>Exit Date</th>
                                    <th class="text-center">Days Held</th>
                                    <th class="text-center">Tax Category</th>
                                    <th class="text-end">Matched Units</th>
                                    <th class="text-end">Buy Price (₹)</th>
                                    <th class="text-end">Exit Price (₹)</th>
                                    <th class="text-end">Realized P&amp;L (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($capitalGains as $cg): ?>
                                    <?php
                                    $qty      = (float)$cg['quantity_matched'];
                                    $cost     = $qty * (float)$cg['buy_price'];
                                    $proceeds = $qty * (float)$cg['exit_price'];
                                    $gain     = (float)$cg['realized_gain'];
                                    ?>
                                    <tr class="bond-taxlog-row"
                                        data-bond-id="<?= $cg['bond_id'] ?>"
                                        data-bond-name="<?= esc($cg['bond_name']) ?>"
                                        data-gain-type="<?= esc($cg['gain_type']) ?>"
                                        data-date="<?= $cg['exit_date'] ?>"
                                        data-qty="<?= $qty ?>"
                                        data-cost="<?= $cost ?>"
                                        data-proceeds="<?= $proceeds ?>"
                                        data-gain="<?= $gain ?>">
                                        <td>
                                            <strong><?= esc($cg['bond_name']) ?></strong>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($cg['isin']) ?></div>
                                        </td>
                                        <td><?= date('d-M-Y', strtotime($cg['buy_date'])) ?></td>
                                        <td><?= date('d-M-Y', strtotime($cg['exit_date'])) ?></td>
                                        <td class="text-center fw-medium"><?= $cg['holding_days'] ?> days</td>
                                        <td class="text-center">
                                            <?php if ($cg['gain_type'] === 'EXEMPT_SGB_MATURITY'): ?>
                                                <span class="badge bg-success text-white px-2 py-1">
                                                    <i class="bi bi-shield-fill-check me-1"></i>Sec 47(viic) Tax-Exempt
                                                </span>
                                            <?php elseif ($cg['gain_type'] === 'LTCG'): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                    LTCG (&gt;365d)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">
                                                    STCG (&le;365d)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-semibold"><?= number_format($qty, 4) ?></td>
                                        <td class="text-end text-muted"><?= format_inr($cg['buy_price']) ?></td>
                                        <td class="text-end fw-medium"><?= format_inr($cg['exit_price']) ?></td>
                                        <td class="text-end">
                                            <?= format_pnl($gain) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-group-divider bg-light fw-semibold" id="bondTaxFooter">
                                <tr>
                                    <td colspan="5" class="py-3">
                                        <span class="fw-bold text-dark" id="footerTaxSummaryTitle">Total (<?= count($capitalGains) ?> Lots)</span>
                                        <span class="ms-2 badge bg-light text-secondary border fw-normal" id="footerTaxBreakdown">
                                            All FIFO lots
                                        </span>
                                    </td>
                                    <td class="text-end py-3 fw-bold" id="footerTaxQty"><?= number_format($totalMatchedUnits, 4) ?></td>
                                    <td class="text-end py-3 text-muted" id="footerTaxCost"><?= format_inr($totalBuyCost) ?></td>
                                    <td class="text-end py-3 text-muted" id="footerTaxProceeds"><?= format_inr($totalExitProceeds) ?></td>
                                    <td class="text-end py-3 fs-6 fw-bold <?= $totalRealizedGain >= 0 ? 'text-success' : 'text-danger' ?>" id="footerTaxGain">
                                        <?= ($totalRealizedGain >= 0 ? '+' : '') . format_inr($totalRealizedGain) ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'bondTax']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 4: TRADE LEDGER -->
            <div class="tab-pane fade p-3" id="transactions" role="tabpanel">
                <?php if (empty($transactions)): ?>
                    <p class="text-muted text-center py-4">No transactions recorded yet.</p>
                <?php else: ?>
                    <?php
                    $fyRanges          = get_fy_ranges();
                    $ledgerBonds       = [];
                    $overallBuyAmount  = 0.0;
                    $overallSellAmount = 0.0;
                    $overallBuyQty     = 0.0;
                    $overallSellQty    = 0.0;
                    $overallBuyCount   = 0;
                    $overallSellCount  = 0;
                    $overallCharges    = 0.0;

                    foreach ($transactions as $t) {
                        $bId = (int)$t['bond_id'];
                        if (!isset($ledgerBonds[$bId])) {
                            $ledgerBonds[$bId] = [
                                'id'    => $bId,
                                'name'  => $t['bond_name'],
                                'isin'  => $t['isin'],
                                'count' => 0,
                            ];
                        }
                        $ledgerBonds[$bId]['count']++;

                        $qty     = (float)$t['quantity'];
                        $amt     = (float)$t['total_amount'];
                        $charges = (float)$t['brokerage_charges'] + (float)$t['accrued_interest'];
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
                    uasort($ledgerBonds, fn($a, $b) => strcmp($a['name'], $b['name']));
                    $overallNetOutlay = $overallBuyAmount - $overallSellAmount;
                    ?>

                    <!-- Filter Toolbar -->
                    <div class="card border shadow-sm mb-3 bg-body-tertiary rounded-3">
                        <div class="card-body p-3">
                            <div class="row align-items-center g-2">
                                <div class="col-md-4 col-lg-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-danger-subtle text-danger rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                            <i class="bi bi-funnel-fill fs-6"></i>
                                        </div>
                                        <div>
                                            <label class="form-label mb-0 fw-semibold text-dark small">Filter Trades</label>
                                            <div class="text-muted" style="font-size: 0.72rem;">Filter by bond &amp; financial year</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                        <div style="min-width: 220px; max-width: 300px;" class="flex-grow-1">
                                            <select id="ledgerBondFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">-- All Bonds (<?= count($transactions) ?> Trades) --</option>
                                                <?php foreach ($ledgerBonds as $bId => $b): ?>
                                                    <option value="<?= $bId ?>" data-name="<?= esc($b['name']) ?>">
                                                        <?= esc($b['name']) ?> (<?= esc($b['isin']) ?>) (<?= $b['count'] ?>)
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
                                        <button type="button" id="btnResetBondFilter" class="btn btn-sm btn-outline-secondary d-none" title="Reset all filters">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                        </button>
                                        <span id="filteredBondBadge" class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1.5 small d-none">
                                            <i class="bi bi-check2-circle me-1"></i><span id="filteredBondName"></span>
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
                                                <?= $overallBuyCount ?> buys • <?= number_format($overallBuyQty, 2) ?> bonds
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Sales &amp; Redemptions</span>
                                            <h5 class="fw-bold text-danger mb-0 mt-1" id="kpiSellAmount"><?= format_inr($overallSellAmount) ?></h5>
                                            <div class="text-muted small mt-1" id="kpiSellDetails" style="font-size: 0.75rem;">
                                                <?= $overallSellCount ?> exits • <?= number_format($overallSellQty, 2) ?> bonds
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Charges &amp; Accrued Int</span>
                                            <h5 class="fw-bold text-dark mb-0 mt-1" id="kpiCharges"><?= format_inr($overallCharges) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Brokerage + Dirty Price Int
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
                        <table class="table table-hover align-middle mb-0 small" id="bondLedgerTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Date</th>
                                    <th>Bond Name / ISIN</th>
                                    <th>Type</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Clean Price (₹)</th>
                                    <th class="text-end">Charges (₹)</th>
                                    <th class="text-end">Accrued Interest (₹)</th>
                                    <th class="text-end">Net Total (₹)</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $t): ?>
                                    <tr class="bond-ledger-row"
                                        data-bond-id="<?= $t['bond_id'] ?>"
                                        data-name="<?= esc($t['bond_name']) ?>"
                                        data-date="<?= $t['transaction_date'] ?>"
                                        data-type="<?= $t['transaction_type'] ?>"
                                        data-qty="<?= (float)$t['quantity'] ?>"
                                        data-charges="<?= (float)$t['brokerage_charges'] + (float)$t['accrued_interest'] ?>"
                                        data-amount="<?= (float)$t['total_amount'] ?>">
                                        <td><?= date('d-M-Y', strtotime($t['transaction_date'])) ?></td>
                                        <td>
                                            <strong><?= esc($t['bond_name']) ?></strong>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($t['isin']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($t['transaction_type'] === 'BUY'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">BUY</span>
                                            <?php elseif ($t['transaction_type'] === 'SELL'): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">SELL</span>
                                            <?php else: ?>
                                                <span class="badge bg-dark-subtle text-dark border border-dark-subtle px-2 py-1">REDEMPTION</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-semibold"><?= number_format($t['quantity'], 0) ?></td>
                                        <td class="text-end"><?= format_inr($t['price']) ?></td>
                                        <td class="text-end text-muted"><?= format_inr($t['brokerage_charges']) ?></td>
                                        <td class="text-end text-muted"><?= format_inr($t['accrued_interest']) ?></td>
                                        <td class="text-end fw-bold text-dark"><?= format_inr($t['total_amount']) ?></td>
                                        <td class="text-secondary"><?= esc($t['notes'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary py-0 px-1.5 open-edit-bond-trans"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editBondTransModal"
                                                    data-id="<?= $t['id'] ?>"
                                                    data-name="<?= esc($t['bond_name']) ?>"
                                                    data-type="<?= esc($t['transaction_type']) ?>"
                                                    data-date="<?= esc($t['transaction_date']) ?>"
                                                    data-qty="<?= (float)$t['quantity'] ?>"
                                                    data-price="<?= (float)$t['price'] ?>"
                                                    data-charges="<?= (float)$t['brokerage_charges'] ?>"
                                                    data-accrued="<?= (float)$t['accrued_interest'] ?>"
                                                    data-notes="<?= esc($t['notes'] ?? '') ?>"
                                                    title="Edit Transaction">
                                                <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <a href="<?= base_url('bonds/delete-transaction/' . $t['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger py-0 px-1.5 ms-1"
                                               onclick="return confirm('Delete this <?= esc($t['transaction_type']) ?> transaction? FIFO lots and capital gains will be automatically recalculated.');"
                                               title="Delete Transaction">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="ledgerNoRows" class="d-none text-center py-4">
                                    <td colspan="10" class="text-muted py-4">
                                        <i class="bi bi-funnel text-secondary fs-4 d-block mb-1"></i>
                                        No transactions found matching the selected bond and date filter.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light fw-semibold border-top-2">
                                <tr>
                                    <td colspan="3">
                                        <span id="footerSummaryTitle">Total (<?= count($transactions) ?> Trades)</span>
                                        <div class="text-muted fw-normal" style="font-size: 0.72rem;" id="footerActionBreakdown">
                                            <span class="text-success"><?= $overallBuyCount ?> BUY</span> &bull; <span class="text-danger"><?= $overallSellCount ?> SELL/REDEEM</span>
                                        </div>
                                    </td>
                                    <td class="text-end" id="footerTotalQty"><?= number_format($overallBuyQty + $overallSellQty, 2) ?></td>
                                    <td class="text-end text-muted">—</td>
                                    <td colspan="2" class="text-end text-muted" id="footerTotalCharges"><?= format_inr($overallCharges) ?></td>
                                    <td class="text-end fw-bold fs-6" id="footerTotalAmount"><?= format_inr($overallNetOutlay) ?></td>
                                    <td class="text-secondary small" id="footerNote"><?= $overallNetOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized' ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'bondLedger']) ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MINIMAL BOND TRANSACTION POPUP MODAL (Buy / Sell / Interest / Redeem)     -->
<!-- ========================================================================= -->
<div class="modal fade" id="bondTransactionModal" tabindex="-1" aria-labelledby="bondTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="bondTransactionModalLabel">
                        Add Transaction: <span id="modalBondName" class="text-danger"></span>
                    </h5>
                    <div class="text-muted small" id="modalBondSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/transaction') ?>" method="POST" id="bondTransForm">
                <?= csrf_field() ?>
                <input type="hidden" name="bond_id" id="modalBondId" value="">

                <div class="modal-body p-4">
                    <!-- Segmented 4-way Action Switcher -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actBondBuy" value="BUY" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actBondBuy">
                            <i class="bi bi-cart-plus me-1"></i>Buy More
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actBondSell" value="SELL" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actBondSell">
                            <i class="bi bi-cart-dash me-1"></i>Sell (FIFO)
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actBondInterest" value="INTEREST" autocomplete="off">
                        <label class="btn btn-outline-primary fw-semibold" for="actBondInterest">
                            <i class="bi bi-cash-coin me-1"></i>Record Interest
                        </label>
                    </div>

                    <!-- SUB-PANEL 1: BUY MORE -->
                    <div id="panelBondBuy" class="bond-trans-panel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="buyBondDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity (Units / Grams) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="buyBondQty" min="0.0001" step="0.0001" placeholder="Quantity" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Buy Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="buyBondPrice" min="0.01" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Brokerage / Taxes (₹)</label>
                                <input type="number" class="form-control" name="brokerage_charges" id="buyBondCharges" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Accrued Interest (₹)</label>
                                <input type="number" class="form-control" name="accrued_interest" id="buyBondAccrued" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Secondary market tranche accumulation">
                            </div>
                        </div>

                        <!-- Buy Outlay Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Total Outlay:</span>
                                <span class="fw-bold text-success fs-5" id="buyBondTotalPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-PANEL 2: SELL (FIFO) -->
                    <div id="panelBondSell" class="bond-trans-panel d-none">
                        <div class="alert alert-warning py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Available Units to Sell:</span>
                            <strong id="sellBondAvailableBadge">0.0000</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Sale Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="sellBondDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity to Sell <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="sellBondQty" min="0.0001" step="0.0001" placeholder="Quantity" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Sell Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="sellBondPrice" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Brokerage / Charges (₹)</label>
                                <input type="number" class="form-control" name="brokerage_charges" id="sellBondCharges" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Secondary market exit" disabled>
                            </div>
                        </div>

                        <!-- Sell Proceeds Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Estimated Net Proceeds:</span>
                                <span class="fw-bold text-dark" id="sellBondProceedsPreview">₹ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Realized P&L:</span>
                                <span class="small fw-semibold" id="sellBondPnlPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-PANEL 3: RECORD INTEREST PAID -->
                    <div id="panelBondInterest" class="bond-trans-panel d-none">
                        <div class="alert alert-success py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Coupon Interest:</span>
                            <span id="interestBondCalcBadge">0.00%</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Payout Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="payout_date" id="payoutBondDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Gross Coupon Interest (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="gross_interest" id="interestGross" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                                <input type="number" class="form-control" name="tds_deducted" id="interestTds" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Net Interest Credited (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="net_interest" id="interestNet" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Coupon Period Description</label>
                                <input type="text" class="form-control" name="period_description" placeholder="e.g. Semi-Annual Coupon (Nov 2025 - May 2026)" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Direct RBI ECS credit to bank" disabled>
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="small text-muted">
                                <i class="bi bi-info-circle me-1"></i>Recording interest will update the bond's <strong>Last Interest Paid Date</strong> on the dashboard. Units held remain unchanged.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitBondTrans">
                        Confirm Purchase Lot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- DEDICATED BOND MATURITY REDEMPTION MODAL (Enabled Only Post-Maturity)     -->
<!-- ========================================================================= -->
<div class="modal fade" id="bondMaturityRedemptionModal" tabindex="-1" aria-labelledby="bondMaturityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-dark text-white rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="bondMaturityModalLabel">
                        <i class="bi bi-award-fill text-warning me-2"></i>Maturity Redemption: <span id="maturityModalBondName" class="text-warning"></span>
                    </h5>
                    <div class="text-white-50 small" id="maturityModalBondSubtitle"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/transaction') ?>" method="POST" id="bondMaturityForm">
                <?= csrf_field() ?>
                <input type="hidden" name="action_type" value="REDEMPTION">
                <input type="hidden" name="bond_id" id="maturityRedeemBondId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3 d-flex align-items-center" id="maturitySgbExemptAlert">
                        <i class="bi bi-shield-check fs-4 text-warning-emphasis me-2"></i>
                        <div>
                            <strong>Section 47(viic) Tax Exemption:</strong> For Sovereign Gold Bonds (SGB) redeemed upon maturity by RBI, capital gains are <strong>100% Tax-Exempt</strong>.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Redemption Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="redemption_date" id="maturityRedeemDate" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Redeemed Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" id="maturityRedeemQty" min="0.0001" step="0.0001" placeholder="Quantity" required>
                            <div class="form-text small" id="maturityMaxQtyHint"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Redemption Price per Unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="redemption_price" id="maturityRedeemPrice" min="0.01" step="0.01" placeholder="0.00" required>
                            <div class="form-text small">RBI gold price for SGB, Face Value for G-Sec/NCD</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                            <input type="text" class="form-control" name="notes" id="maturityRedeemNotes" placeholder="e.g. RBI SGB Final Maturity Redemption">
                        </div>
                    </div>

                    <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Total Principal Amount Received:</span>
                            <span class="fw-bold text-dark fs-5" id="maturityProceedsPreview">₹ 0.00</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark btn-sm px-4 fw-semibold" id="btnSubmitMaturityRedeem">
                        <i class="bi bi-check2-circle me-1"></i>Confirm Maturity Redemption
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- QUICK UPDATE CMP MODAL                                                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="bondPriceModal" tabindex="-1" aria-labelledby="bondPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h6 class="modal-title fw-bold text-dark" id="bondPriceModalLabel">Update Market Price</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/update-price') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="bond_id" id="priceModalBondId" value="">

                <div class="modal-body p-4">
                    <div class="mb-2 fw-semibold text-dark text-truncate" id="priceModalBondName"></div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Current Market Price (₹)</label>
                        <input type="number" class="form-control" name="current_market_price" id="priceModalPrice" min="0.01" step="0.01" required>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-secondary">Price Date</label>
                        <input type="date" class="form-control" name="cmp_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-semibold">Update Price</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT BOND METADATA MODAL                                                  -->
<!-- ========================================================================= -->
<div class="modal fade" id="editBondModal" tabindex="-1" aria-labelledby="editBondModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editBondModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Bond Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/update-bond') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="bond_id" id="editBondId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">Bond / Instrument Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="bond_name" id="editBondName" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" id="editBondCategory" required>
                                <option value="CORPORATE_NCD">Corporate Bond / NCD</option>
                                <option value="GOVT_SECURITY">Government Security (G-Sec / SDL)</option>
                                <option value="SGB">Sovereign Gold Bond (SGB)</option>
                                <option value="TAX_FREE">Tax-Free PSU Bond</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ISIN Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="isin" id="editBondIsin" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Trading Symbol / Ticker</label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="bond_symbol" id="editBondSymbol">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Issuer / Authority</label>
                            <input type="text" class="form-control" name="issuer" id="editBondIssuer" placeholder="e.g. Reserve Bank of India, NHAI">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Face Value (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="face_value" id="editBondFaceValue" min="1" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Coupon Rate (% p.a.) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="coupon_rate" id="editBondCouponRate" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Interest Frequency <span class="text-danger">*</span></label>
                            <select class="form-select" name="interest_frequency" id="editBondFrequency" required>
                                <option value="MONTHLY">MONTHLY</option>
                                <option value="QUARTERLY">QUARTERLY</option>
                                <option value="SEMI_ANNUAL">SEMI_ANNUAL</option>
                                <option value="ANNUAL">ANNUAL</option>
                                <option value="CUMULATIVE">CUMULATIVE</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Issue Date</label>
                            <input type="date" class="form-control" name="issue_date" id="editBondIssueDate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Maturity Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="maturity_date" id="editBondMaturityDate" required>
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
<!-- EDIT BOND TRANSACTION MODAL (WITH FIFO REBUILD)                           -->
<!-- ========================================================================= -->
<div class="modal fade" id="editBondTransModal" tabindex="-1" aria-labelledby="editBondTransModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editBondTransModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i><span id="editBondTransTitle">Edit Transaction</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editBondTransId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Saving changes will automatically <strong>rebalance FIFO lots and recalculate capital gains</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editBondTransDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quantity (Units / Grams) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="quantity" id="editBondTransQty" min="0.0001" step="0.0001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Price per Unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="editBondTransPrice" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Brokerage / Charges (₹)</label>
                            <input type="number" class="form-control" name="brokerage_charges" id="editBondTransCharges" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Accrued Interest Paid (₹)</label>
                            <input type="number" class="form-control" name="accrued_interest" id="editBondTransAccrued" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editBondTransNotes">
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
<!-- EDIT BOND INTEREST PAYOUT MODAL                                           -->
<!-- ========================================================================= -->
<div class="modal fade" id="editBondInterestModal" tabindex="-1" aria-labelledby="editBondInterestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editBondInterestModalLabel">
                    <i class="bi bi-cash-coin text-success me-2"></i><span id="editBondIntTitle">Edit Interest Payment</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/update-interest') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="interest_id" id="editBondIntId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Payout Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="payout_date" id="editBondIntDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Coupon Rate (% p.a.)</label>
                            <input type="number" class="form-control" name="coupon_rate" id="editBondIntRate" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Gross Interest (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="gross_interest" id="editBondIntGross" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                            <input type="number" class="form-control text-danger" name="tds_deducted" id="editBondIntTds" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Period / Description</label>
                            <input type="text" class="form-control" name="period_description" id="editBondIntDesc" placeholder="e.g. H1 FY26-27 Semi-Annual Coupon">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editBondIntNotes">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">Save Interest Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentBond = {};

    // Hook Edit Bond buttons
    document.querySelectorAll('.open-edit-bond-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editBondId').value = this.dataset.id;
            document.getElementById('editBondName').value = this.dataset.name;
            document.getElementById('editBondIsin').value = this.dataset.isin;
            document.getElementById('editBondSymbol').value = this.dataset.symbol || '';
            document.getElementById('editBondCategory').value = this.dataset.cat;
            document.getElementById('editBondIssuer').value = this.dataset.issuer || '';
            document.getElementById('editBondFaceValue').value = parseFloat(this.dataset.face).toFixed(2);
            document.getElementById('editBondCouponRate').value = parseFloat(this.dataset.rate).toFixed(2);
            document.getElementById('editBondFrequency').value = this.dataset.freq;
            document.getElementById('editBondMaturityDate').value = this.dataset.maturity;
            document.getElementById('editBondIssueDate').value = this.dataset.issue || '';
        });
    });

    // Hook Edit Transaction buttons
    document.querySelectorAll('.open-edit-bond-trans').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editBondTransId').value = this.dataset.id;
            document.getElementById('editBondTransTitle').textContent = 'Edit ' + this.dataset.type + ': ' + this.dataset.name;
            document.getElementById('editBondTransDate').value = this.dataset.date;
            document.getElementById('editBondTransQty').value = parseFloat(this.dataset.qty);
            document.getElementById('editBondTransPrice').value = parseFloat(this.dataset.price).toFixed(2);
            document.getElementById('editBondTransCharges').value = parseFloat(this.dataset.charges || 0).toFixed(2);
            document.getElementById('editBondTransAccrued').value = parseFloat(this.dataset.accrued || 0).toFixed(2);
            document.getElementById('editBondTransNotes').value = this.dataset.notes || '';
        });
    });

    // Hook Edit Interest buttons
    document.querySelectorAll('.open-edit-bond-interest').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editBondIntId').value = this.dataset.id;
            document.getElementById('editBondIntTitle').textContent = 'Edit Interest Payment: ' + this.dataset.name;
            document.getElementById('editBondIntDate').value = this.dataset.date;
            document.getElementById('editBondIntRate').value = parseFloat(this.dataset.rate || 0).toFixed(2);
            document.getElementById('editBondIntGross').value = parseFloat(this.dataset.gross).toFixed(2);
            document.getElementById('editBondIntTds').value = parseFloat(this.dataset.tds || 0).toFixed(2);
            document.getElementById('editBondIntDesc').value = this.dataset.desc || '';
            document.getElementById('editBondIntNotes').value = this.dataset.notes || '';
        });
    });

    // 1. Hook "Add Trans" buttons to populate modal
    document.querySelectorAll('.open-bond-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentBond = {
                id: this.dataset.id,
                name: this.dataset.name,
                isin: this.dataset.isin,
                cat: this.dataset.cat,
                qty: parseFloat(this.dataset.qty) || 0,
                cmp: parseFloat(this.dataset.cmp) || 0,
                avgPrice: parseFloat(this.dataset.avg) || 0,
                faceValue: parseFloat(this.dataset.face) || 1000,
                couponRate: parseFloat(this.dataset.rate) || 0,
                frequency: this.dataset.freq || 'SEMI_ANNUAL'
            };

            document.getElementById('modalBondId').value = currentBond.id;
            document.getElementById('modalBondName').textContent = currentBond.name;
            document.getElementById('modalBondSubtitle').textContent = 'ISIN: ' + currentBond.isin + ' | Available: ' + Math.round(currentBond.qty) + ' | CMP: ₹ ' + currentBond.cmp.toFixed(2);

            // Set defaults in Buy Panel
            document.getElementById('buyBondPrice').value = currentBond.cmp.toFixed(2);
            document.getElementById('buyBondQty').value = '';
            document.getElementById('buyBondCharges').value = '0.00';
            document.getElementById('buyBondAccrued').value = '0.00';
            document.getElementById('buyBondTotalPreview').textContent = '₹ 0.00';

            // Set defaults in Sell Panel
            document.getElementById('sellBondAvailableBadge').textContent = Math.round(currentBond.qty) + ' units/grams';
            document.getElementById('sellBondQty').max = currentBond.qty;
            document.getElementById('sellBondQty').value = '';
            document.getElementById('sellBondPrice').value = currentBond.cmp.toFixed(2);
            document.getElementById('sellBondCharges').value = '0.00';
            document.getElementById('sellBondProceedsPreview').textContent = '₹ 0.00';
            document.getElementById('sellBondPnlPreview').textContent = '₹ 0.00';

            // Set defaults in Interest Panel
            document.getElementById('interestBondCalcBadge').textContent = currentBond.couponRate.toFixed(2) + '% p.a. (' + currentBond.frequency + ')';
            // Pre-calculate approximate coupon
            let periodsPerYear = 1;
            if (currentBond.frequency === 'MONTHLY') periodsPerYear = 12;
            else if (currentBond.frequency === 'QUARTERLY') periodsPerYear = 4;
            else if (currentBond.frequency === 'SEMI_ANNUAL') periodsPerYear = 2;

            const estGross = (currentBond.qty * currentBond.faceValue * (currentBond.couponRate / 100)) / periodsPerYear;
            document.getElementById('interestGross').value = estGross > 0 ? estGross.toFixed(2) : '';
            document.getElementById('interestTds').value = '0.00';
            document.getElementById('interestNet').value = estGross > 0 ? estGross.toFixed(2) : '';

            // Reset to Buy panel
            document.getElementById('actBondBuy').checked = true;
            switchBondAction('BUY');
        });
    });

    // 2. Action Tab Switching (Buy / Sell / Interest)
    const actRadios = document.querySelectorAll('input[name="action_type"]');
    actRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            switchBondAction(this.value);
        });
    });

    function switchBondAction(type) {
        const pBuy = document.getElementById('panelBondBuy');
        const pSell = document.getElementById('panelBondSell');
        const pInterest = document.getElementById('panelBondInterest');
        const submitBtn = document.getElementById('btnSubmitBondTrans');

        // Hide all
        [pBuy, pSell, pInterest].forEach(p => {
            if (p) {
                p.classList.add('d-none');
                setInputsDisabled(p, true);
            }
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
            submitBtn.textContent = 'Execute FIFO Sell';
        } else if (type === 'INTEREST') {
            pInterest.classList.remove('d-none');
            setInputsDisabled(pInterest, false);
            submitBtn.className = 'btn btn-primary btn-sm px-4 fw-semibold';
            submitBtn.textContent = 'Record Interest Payout';
        }
    }

    function setInputsDisabled(container, disabled) {
        container.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = disabled;
        });
    }

    // 3. Live calculations in BUY modal
    const buyQty = document.getElementById('buyBondQty');
    const buyPrice = document.getElementById('buyBondPrice');
    const buyCharges = document.getElementById('buyBondCharges');
    const buyAccrued = document.getElementById('buyBondAccrued');
    const buyPreview = document.getElementById('buyBondTotalPreview');

    function calcBuy() {
        const q = parseFloat(buyQty.value) || 0;
        const p = parseFloat(buyPrice.value) || 0;
        const c = parseFloat(buyCharges.value) || 0;
        const a = parseFloat(buyAccrued.value) || 0;
        const total = (q * p) + c + a;
        buyPreview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    [buyQty, buyPrice, buyCharges, buyAccrued].forEach(el => el.addEventListener('input', calcBuy));

    // 4. Live calculations in SELL modal
    const sellQty = document.getElementById('sellBondQty');
    const sellPrice = document.getElementById('sellBondPrice');
    const sellCharges = document.getElementById('sellBondCharges');
    const sellProceeds = document.getElementById('sellBondProceedsPreview');
    const sellPnl = document.getElementById('sellBondPnlPreview');

    function calcSell() {
        const q = parseFloat(sellQty.value) || 0;
        const p = parseFloat(sellPrice.value) || 0;
        const c = parseFloat(sellCharges.value) || 0;
        const net = Math.max(0, (q * p) - c);
        const costBasis = q * (currentBond.avgPrice || 0);
        const estGain = net - costBasis;

        sellProceeds.textContent = '₹ ' + net.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const sign = estGain >= 0 ? '+' : '';
        sellPnl.textContent = sign + '₹ ' + estGain.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        sellPnl.className = 'small fw-semibold ' + (estGain >= 0 ? 'text-success' : 'text-danger');
    }
    [sellQty, sellPrice, sellCharges].forEach(el => el.addEventListener('input', calcSell));

    // 5. Live calculations in INTEREST modal
    const interestGross = document.getElementById('interestGross');
    const interestTds = document.getElementById('interestTds');
    const interestNet = document.getElementById('interestNet');

    function calcInterest() {
        const g = parseFloat(interestGross.value) || 0;
        const t = parseFloat(interestTds.value) || 0;
        const net = Math.max(0, g - t);
        interestNet.value = net.toFixed(2);
    }
    interestGross.addEventListener('input', calcInterest);
    interestTds.addEventListener('input', calcInterest);

    // 6. Dedicated Maturity Redemption Modal
    const matQty = document.getElementById('maturityRedeemQty');
    const matPrice = document.getElementById('maturityRedeemPrice');
    const matPreview = document.getElementById('maturityProceedsPreview');

    function calcMaturityProceeds() {
        if (!matQty || !matPrice || !matPreview) return;
        const q = parseFloat(matQty.value) || 0;
        const p = parseFloat(matPrice.value) || 0;
        const total = q * p;
        matPreview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    if (matQty) matQty.addEventListener('input', calcMaturityProceeds);
    if (matPrice) matPrice.addEventListener('input', calcMaturityProceeds);

    document.querySelectorAll('.open-maturity-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const isin = this.dataset.isin || '';
            const cat = this.dataset.cat || '';
            const qty = parseFloat(this.dataset.qty) || 0;
            const face = parseFloat(this.dataset.face) || 100;
            const cmp = parseFloat(this.dataset.cmp) || face;
            const maturity = this.dataset.maturity || '';

            document.getElementById('maturityRedeemBondId').value = id;
            document.getElementById('maturityModalBondName').textContent = name;
            document.getElementById('maturityModalBondSubtitle').textContent = `${cat} • ISIN: ${isin} • Matured on ${maturity}`;

            const defPrice = (cat === 'SGB' ? cmp : face);
            matQty.value = Math.round(qty);
            matQty.max = qty;
            matPrice.value = defPrice.toFixed(2);
            document.getElementById('maturityMaxQtyHint').textContent = `Max eligible units: ${Math.round(qty)} ${cat === 'SGB' ? 'grams' : 'units'}`;

            const sgbAlert = document.getElementById('maturitySgbExemptAlert');
            if (sgbAlert) sgbAlert.classList.toggle('d-none', cat !== 'SGB');

            calcMaturityProceeds();
        });
    });

    // 7. Quick CMP Modal population
    document.querySelectorAll('.open-price-modal').forEach(a => {
        a.addEventListener('click', function() {
            document.getElementById('priceModalBondId').value = this.dataset.id;
            document.getElementById('priceModalBondName').textContent = this.dataset.name;
            document.getElementById('priceModalPrice').value = parseFloat(this.dataset.price).toFixed(2);
        });
    });

    // =========================================================================
    // 8. BONDS: DUAL FILTER & TOTAL AMOUNTS (INTEREST PAYOUTS & TRADE LEDGER)
    // =========================================================================
    const FY_RANGES = <?= json_encode(get_fy_ranges()) ?>;

    function formatINR(val) {
        return '₹ ' + Number(val).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // --- A. INTEREST PAYOUTS FILTER ENGINE ---
    const payoutBondFilter = document.getElementById('payoutBondFilter');
    const payoutDateFilter = document.getElementById('payoutDateFilter');
    const payoutTable      = document.getElementById('bondPayoutsTable');
    const payoutResetBtn   = document.getElementById('btnResetPayoutFilter');
    const payoutBadge      = document.getElementById('filteredPayoutBadge');
    const payoutNameSpan   = document.getElementById('filteredPayoutName');
    const payoutNoRowsEl   = document.getElementById('payoutNoRows');

    const kpiPayoutGross = document.getElementById('kpiPayoutGross');
    const kpiPayoutTds   = document.getElementById('kpiPayoutTds');
    const kpiPayoutNet   = document.getElementById('kpiPayoutNet');
    const kpiPayoutCount = document.getElementById('kpiPayoutCount');

    const footerPayoutSummaryTitle = document.getElementById('footerPayoutSummaryTitle');
    const footerPayoutGross        = document.getElementById('footerPayoutGross');
    const footerPayoutTds          = document.getElementById('footerPayoutTds');
    const footerPayoutNet          = document.getElementById('footerPayoutNet');

    function applyPayoutFilters(updateUrl = true) {
        if (!payoutTable) return;

        const selectedBond   = payoutBondFilter ? payoutBondFilter.value : 'ALL';
        const selectedFy     = payoutDateFilter ? payoutDateFilter.value : 'ALL';
        const selectedOption = payoutBondFilter ? payoutBondFilter.options[payoutBondFilter.selectedIndex] : null;
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

        const rows = payoutTable.querySelectorAll('tbody tr.bond-payout-row');
        let visibleCount = 0;
        let totalGross   = 0.0;
        let totalTds     = 0.0;
        let totalNet     = 0.0;

        rows.forEach(row => {
            const rowBondId = row.dataset.bondId;
            const rowDate   = row.dataset.date;
            const rowGross  = parseFloat(row.dataset.gross) || 0.0;
            const rowTds    = parseFloat(row.dataset.tds) || 0.0;
            const rowNet    = parseFloat(row.dataset.net) || 0.0;

            const matchesBond = (selectedBond === 'ALL' || rowBondId === selectedBond);
            let matchesDate   = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesBond && matchesDate) {
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;
                totalGross += rowGross;
                totalTds   += rowTds;
                totalNet   += rowNet;
            } else {
                row.classList.add('d-none-filter');
                row.dataset.filtered = 'true';
            }
        });

        if (payoutNoRowsEl) {
            payoutNoRowsEl.classList.toggle('d-none', visibleCount > 0);
        }

        // Update KPI Cards
        if (kpiPayoutGross) kpiPayoutGross.textContent = formatINR(totalGross);
        if (kpiPayoutTds)   kpiPayoutTds.textContent   = formatINR(totalTds);
        if (kpiPayoutNet)   kpiPayoutNet.textContent   = formatINR(totalNet);
        if (kpiPayoutCount) kpiPayoutCount.textContent = `${visibleCount} Credits`;

        // Update Footer Totals
        if (footerPayoutSummaryTitle) {
            let label = selectedBond === 'ALL' ? 'Total' : selectedName;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerPayoutSummaryTitle.textContent = `${label} (${visibleCount} Payouts)`;
        }
        if (footerPayoutGross) footerPayoutGross.textContent = formatINR(totalGross);
        if (footerPayoutTds)   footerPayoutTds.textContent   = formatINR(totalTds);
        if (footerPayoutNet)   footerPayoutNet.textContent   = formatINR(totalNet);

        // Refresh pagination slice
        if (window.bondPayoutPager) {
            window.bondPayoutPager.refresh();
        }

        // Toggle reset button & filtered badge
        const hasActiveFilter = (selectedBond !== 'ALL' || selectedFy !== 'ALL');
        if (payoutResetBtn) payoutResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (payoutBadge) {
            payoutBadge.classList.toggle('d-none', !hasActiveFilter);
            if (payoutNameSpan) {
                let badgeText = selectedBond !== 'ALL' ? selectedName : 'All Bonds';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                payoutNameSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedBond === 'ALL') url.searchParams.delete('bond_id');
            else url.searchParams.set('bond_id', selectedBond);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (payoutBondFilter) payoutBondFilter.addEventListener('change', () => applyPayoutFilters(true));
    if (payoutDateFilter) payoutDateFilter.addEventListener('change', () => applyPayoutFilters(true));
    if (payoutResetBtn) {
        payoutResetBtn.addEventListener('click', () => {
            if (payoutBondFilter) payoutBondFilter.value = 'ALL';
            if (payoutDateFilter) payoutDateFilter.value = 'ALL';
            applyPayoutFilters(true);
        });
    }

    // --- B. TRADE LEDGER FILTER ENGINE ---
    const ledgerBondFilter = document.getElementById('ledgerBondFilter');
    const ledgerDateFilter = document.getElementById('ledgerDateFilter');
    const ledgerTable      = document.getElementById('bondLedgerTable');
    const ledgerResetBtn   = document.getElementById('btnResetBondFilter');
    const ledgerBadge      = document.getElementById('filteredBondBadge');
    const ledgerNameSpan   = document.getElementById('filteredBondName');
    const ledgerNoRowsEl   = document.getElementById('ledgerNoRows');

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

        const selectedBond   = ledgerBondFilter ? ledgerBondFilter.value : 'ALL';
        const selectedFy     = ledgerDateFilter ? ledgerDateFilter.value : 'ALL';
        const selectedOption = ledgerBondFilter ? ledgerBondFilter.options[ledgerBondFilter.selectedIndex] : null;
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

        const rows = ledgerTable.querySelectorAll('tbody tr.bond-ledger-row');
        let visibleCount = 0;
        let buyAmount    = 0.0;
        let sellAmount   = 0.0;
        let buyQty       = 0.0;
        let sellQty      = 0.0;
        let buyCount     = 0;
        let sellCount    = 0;
        let totalCharges = 0.0;

        rows.forEach(row => {
            const rowBondId = row.dataset.bondId;
            const rowDate   = row.dataset.date;
            const rowType   = row.dataset.type;
            const rowQty    = parseFloat(row.dataset.qty) || 0.0;
            const rowAmt    = parseFloat(row.dataset.amount) || 0.0;
            const rowChg    = parseFloat(row.dataset.charges) || 0.0;

            const matchesBond = (selectedBond === 'ALL' || rowBondId === selectedBond);
            let matchesDate   = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesBond && matchesDate) {
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
        if (kpiBuyDetails) kpiBuyDetails.textContent = `${buyCount} buys • ${buyQty.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} bonds`;

        if (kpiSellAmount) kpiSellAmount.textContent = formatINR(sellAmount);
        if (kpiSellDetails) kpiSellDetails.textContent = `${sellCount} exits • ${sellQty.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} bonds`;

        if (kpiNetOutlay) {
            kpiNetOutlay.textContent = formatINR(netOutlay);
            kpiNetOutlay.className = 'fw-bold mb-0 mt-1 ' + (netOutlay >= 0 ? 'text-primary' : 'text-success');
        }
        if (kpiNetDetails) {
            if (selectedBond === 'ALL') {
                kpiNetDetails.textContent = 'Net cash invested in period';
            } else {
                const heldDiff = buyQty - sellQty;
                kpiNetDetails.textContent = `Net held from trades: ${heldDiff.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} bonds`;
            }
        }

        if (kpiCharges) kpiCharges.textContent = formatINR(totalCharges);

        // Update Footer Totals
        if (footerSummaryTitle) {
            let label = selectedBond === 'ALL' ? 'Total' : selectedName;
            if (selectedFy !== 'ALL') {
                label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            }
            footerSummaryTitle.textContent = `${label} (${visibleCount} Trades)`;
        }

        if (footerActionBreakdown) {
            footerActionBreakdown.innerHTML = `<span class="text-success">${buyCount} BUY</span> &bull; <span class="text-danger">${sellCount} SELL/REDEEM</span>`;
        }

        if (footerTotalQty) footerTotalQty.textContent = (buyQty + sellQty).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (footerTotalCharges) footerTotalCharges.textContent = formatINR(totalCharges);
        if (footerTotalAmount) {
            footerTotalAmount.textContent = formatINR(netOutlay);
            footerTotalAmount.className = 'text-end fw-bold fs-6 ' + (netOutlay >= 0 ? 'text-dark' : 'text-success');
        }
        if (footerNote) footerNote.textContent = netOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized';

        // Refresh pagination slice
        if (window.bondLedgerPager) {
            window.bondLedgerPager.refresh();
        }

        // Toggle reset button & filtered badge
        const hasActiveFilter = (selectedBond !== 'ALL' || selectedFy !== 'ALL');
        if (ledgerResetBtn) ledgerResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (ledgerBadge) {
            ledgerBadge.classList.toggle('d-none', !hasActiveFilter);
            if (ledgerNameSpan) {
                let badgeText = selectedBond !== 'ALL' ? selectedName : 'All Bonds';
                if (selectedFy !== 'ALL') badgeText += ' • ' + (selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                ledgerNameSpan.textContent = badgeText;
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedBond === 'ALL') url.searchParams.delete('bond_id');
            else url.searchParams.set('bond_id', selectedBond);

            if (selectedFy === 'ALL') url.searchParams.delete('fy');
            else url.searchParams.set('fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (ledgerBondFilter) ledgerBondFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerDateFilter) ledgerDateFilter.addEventListener('change', () => applyLedgerFilters(true));
    if (ledgerResetBtn) {
        ledgerResetBtn.addEventListener('click', () => {
            if (ledgerBondFilter) ledgerBondFilter.value = 'ALL';
            if (ledgerDateFilter) ledgerDateFilter.value = 'ALL';
            applyLedgerFilters(true);
        });
    }

    // Shortcuts from Holdings dropdown
    window.showBondLedger = function(bondId) {
        const transTabBtn = document.getElementById('transactions-tab');
        if (transTabBtn) {
            bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        }
        if (ledgerBondFilter) {
            ledgerBondFilter.value = String(bondId);
            applyLedgerFilters(true);
        }
    };

    window.showBondPayouts = function(bondId) {
        const payoutsTabBtn = document.getElementById('payouts-tab');
        if (payoutsTabBtn) {
            bootstrap.Tab.getOrCreateInstance(payoutsTabBtn).show();
        }
        if (payoutBondFilter) {
            payoutBondFilter.value = String(bondId);
            applyPayoutFilters(true);
        }
    };

    window.showBondTaxLog = function(bondId) {
        const taxTabBtn = document.getElementById('capitalgains-tab');
        if (taxTabBtn) {
            bootstrap.Tab.getOrCreateInstance(taxTabBtn).show();
        }
        if (taxBondFilter) {
            taxBondFilter.value = String(bondId);
            applyBondTaxFilters(true);
        }
    };

    // ==========================================
    // BOND HOLDINGS SORTING
    // ==========================================
    const sortSelect = document.getElementById('sortBondsSelect');
    const holdingsTbody = document.getElementById('bondHoldingsTbody');
    const sortableHeaders = document.querySelectorAll('.sortable-bond-th');

    function sortBondsHoldings(sortBy) {
        if (!holdingsTbody) return;
        const rows = Array.from(holdingsTbody.querySelectorAll('tr.bond-holding-row'));
        if (rows.length === 0) return;

        const [field, dir] = sortBy.split('_');
        const isAsc = dir === 'asc';

        rows.sort((a, b) => {
            let valA, valB;
            if (field === 'name') {
                valA = (a.dataset.name || '').toLowerCase();
                valB = (b.dataset.name || '').toLowerCase();
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (field === 'maturity') {
                valA = a.dataset.maturity || '9999-99-99';
                valB = b.dataset.maturity || '9999-99-99';
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            } else if (field === 'interest') {
                valA = a.dataset.interest || '';
                valB = b.dataset.interest || '';
                if (!valA) return 1;
                if (!valB) return -1;
                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
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
            sortBondsHoldings(this.value);
        });
    }

    sortableHeaders.forEach(th => {
        th.addEventListener('click', function() {
            const col = this.dataset.col;
            const currentVal = sortSelect ? sortSelect.value : '';
            let newDir = 'asc';
            if (currentVal.startsWith(col)) {
                newDir = currentVal.endsWith('asc') ? 'desc' : 'asc';
            } else if (col === 'current' || col === 'interest') {
                newDir = 'desc';
            }
            const newVal = `${col}_${newDir}`;
            if (sortSelect) sortSelect.value = newVal;
            sortBondsHoldings(newVal);
        });
    });

    // ==========================================
    // FIFO TAX LOG FILTERING (Bond, Gain Type, Date Range)
    // ==========================================
    const taxBondFilter  = document.getElementById('taxLogBondFilter');
    const taxTypeFilter  = document.getElementById('taxLogTypeFilter');
    const taxDateFilter  = document.getElementById('taxLogDateFilter');
    const taxResetBtn    = document.getElementById('btnResetTaxFilter');
    const taxBadge       = document.getElementById('filteredTaxBadge');
    const taxBondSpan    = document.getElementById('filteredTaxBondSpan');
    const taxTable       = document.getElementById('bondTaxLogTable');

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
    const footerTaxQty          = document.getElementById('footerTaxQty');
    const footerTaxCost         = document.getElementById('footerTaxCost');
    const footerTaxProceeds     = document.getElementById('footerTaxProceeds');
    const footerTaxGain         = document.getElementById('footerTaxGain');

    function applyBondTaxFilters(updateUrl = true) {
        if (!taxTable) return;

        const selectedBond = taxBondFilter ? taxBondFilter.value : 'ALL';
        const selectedType = taxTypeFilter ? taxTypeFilter.value : 'ALL';
        const selectedFy   = taxDateFilter ? taxDateFilter.value : 'ALL';

        let fyStart = null;
        let fyEnd   = null;
        if (selectedFy === 'CURRENT_FY' && FY_RANGES.current) {
            fyStart = FY_RANGES.current.start;
            fyEnd   = FY_RANGES.current.end;
        } else if (selectedFy === 'LAST_FY' && FY_RANGES.last) {
            fyStart = FY_RANGES.last.start;
            fyEnd   = FY_RANGES.last.end;
        }

        const rows = taxTable.querySelectorAll('tbody tr.bond-taxlog-row');
        let visibleCount = 0;
        let totalMatchedUnits = 0;
        let totalBuyCost = 0;
        let totalExitProceeds = 0;
        let totalRealizedGain = 0;
        let totalStcg = 0;
        let totalLtcg = 0;
        let totalExempt = 0;
        let selectedBondName = '';

        if (selectedBond !== 'ALL' && taxBondFilter) {
            const opt = taxBondFilter.querySelector(`option[value="${selectedBond}"]`);
            if (opt) selectedBondName = opt.dataset.name || opt.textContent.split('(')[0].trim();
        }

        rows.forEach(row => {
            const rowBondId   = row.dataset.bondId;
            const rowGainType = row.dataset.gainType;
            const rowDate     = row.dataset.date;

            const matchesBond = (selectedBond === 'ALL' || rowBondId === selectedBond);
            const matchesType = (selectedType === 'ALL' || rowGainType === selectedType);
            let matchesDate   = true;
            if (fyStart && fyEnd) {
                matchesDate = (rowDate >= fyStart && rowDate <= fyEnd);
            }

            if (matchesBond && matchesType && matchesDate) {
                row.classList.remove('d-none');
                row.classList.remove('d-none-filter');
                row.dataset.filtered = 'false';
                visibleCount++;

                const qty      = parseFloat(row.dataset.qty) || 0;
                const cost     = parseFloat(row.dataset.cost) || 0;
                const proceeds = parseFloat(row.dataset.proceeds) || 0;
                const gain     = parseFloat(row.dataset.gain) || 0;

                totalMatchedUnits += qty;
                totalBuyCost      += cost;
                totalExitProceeds += proceeds;
                totalRealizedGain += gain;

                if (rowGainType === 'EXEMPT_SGB_MATURITY') {
                    totalExempt += gain;
                } else if (rowGainType === 'LTCG') {
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
        if (kpiTaxProceeds) kpiTaxProceeds.textContent = formatINR(totalExitProceeds);
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
            kpiTaxBreakdown.textContent = `Exempt: ${formatINR(totalExempt)} • LTCG: ${formatINR(totalLtcg)} • STCG: ${formatINR(totalStcg)}`;
        }

        // Update Dynamic Footer
        if (footerTaxSummaryTitle) {
            let label = selectedBond === 'ALL' ? 'Total' : selectedBondName;
            if (selectedType !== 'ALL') {
                label += ` [${selectedType === 'EXEMPT_SGB_MATURITY' ? 'Exempt SGB' : selectedType}]`;
            }
            if (selectedFy !== 'ALL') label += ` (${selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY'})`;
            footerTaxSummaryTitle.textContent = `${label} (${visibleCount} Lots)`;
        }
        if (footerTaxBreakdown) {
            footerTaxBreakdown.textContent = `Exempt: ${formatINR(totalExempt)} • LTCG: ${formatINR(totalLtcg)} • STCG: ${formatINR(totalStcg)}`;
        }
        if (footerTaxQty) footerTaxQty.textContent = totalMatchedUnits.toLocaleString('en-IN', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
        if (footerTaxCost) footerTaxCost.textContent = formatINR(totalBuyCost);
        if (footerTaxProceeds) footerTaxProceeds.textContent = formatINR(totalExitProceeds);
        if (footerTaxGain) {
            footerTaxGain.textContent = (totalRealizedGain >= 0 ? '+' : '') + formatINR(totalRealizedGain);
            footerTaxGain.className = 'text-end py-3 fs-6 fw-bold ' + (totalRealizedGain >= 0 ? 'text-success' : 'text-danger');
        }

        // Refresh pagination slice
        if (window.bondTaxPager) {
            window.bondTaxPager.refresh();
        }

        // Toggle reset button & active filter badge
        const hasActiveFilter = (selectedBond !== 'ALL' || selectedType !== 'ALL' || selectedFy !== 'ALL');
        if (taxResetBtn) taxResetBtn.classList.toggle('d-none', !hasActiveFilter);
        if (taxBadge) {
            taxBadge.classList.toggle('d-none', !hasActiveFilter);
            if (taxBondSpan) {
                let badgeParts = [];
                if (selectedBond !== 'ALL') badgeParts.push(selectedBondName);
                if (selectedType !== 'ALL') badgeParts.push(selectedType === 'EXEMPT_SGB_MATURITY' ? 'Tax-Exempt' : selectedType);
                if (selectedFy !== 'ALL') badgeParts.push(selectedFy === 'CURRENT_FY' ? 'Current FY' : 'Last FY');
                taxBondSpan.textContent = badgeParts.join(' • ');
            }
        }

        if (updateUrl) {
            const url = new URL(window.location);
            if (selectedBond === 'ALL') url.searchParams.delete('tax_bond_id');
            else url.searchParams.set('tax_bond_id', selectedBond);

            if (selectedType === 'ALL') url.searchParams.delete('tax_type');
            else url.searchParams.set('tax_type', selectedType);

            if (selectedFy === 'ALL') url.searchParams.delete('tax_fy');
            else url.searchParams.set('tax_fy', selectedFy);

            window.history.replaceState({}, '', url);
        }
    }

    if (taxBondFilter) taxBondFilter.addEventListener('change', () => applyBondTaxFilters(true));
    if (taxTypeFilter) taxTypeFilter.addEventListener('change', () => applyBondTaxFilters(true));
    if (taxDateFilter) taxDateFilter.addEventListener('change', () => applyBondTaxFilters(true));
    if (taxResetBtn) {
        taxResetBtn.addEventListener('click', () => {
            if (taxBondFilter) taxBondFilter.value = 'ALL';
            if (taxTypeFilter) taxTypeFilter.value = 'ALL';
            if (taxDateFilter) taxDateFilter.value = 'ALL';
            applyBondTaxFilters(true);
        });
    }

    // Deep link handling on page load
    const urlParams = new URLSearchParams(window.location.search);
    const initialBondId = urlParams.get('bond_id');
    const initialFy     = urlParams.get('fy');
    const initialTab    = urlParams.get('tab');
    const hash          = window.location.hash;

    if (initialBondId) {
        if (ledgerBondFilter) ledgerBondFilter.value = initialBondId;
        if (payoutBondFilter) payoutBondFilter.value = initialBondId;
    }
    if (initialFy) {
        if (ledgerDateFilter) ledgerDateFilter.value = initialFy;
        if (payoutDateFilter) payoutDateFilter.value = initialFy;
    }

    if ((initialBondId || initialFy || initialTab || hash) && (ledgerTable || payoutTable)) {
        if (ledgerTable) applyLedgerFilters(false);
        if (payoutTable) applyPayoutFilters(false);

        if (initialTab === 'transactions' || (initialBondId && !initialTab) || hash === '#transactions') {
            const transTabBtn = document.getElementById('transactions-tab');
            if (transTabBtn) bootstrap.Tab.getOrCreateInstance(transTabBtn).show();
        } else if (initialTab === 'payouts' || hash === '#payouts') {
            const payoutsTabBtn = document.getElementById('payouts-tab');
            if (payoutsTabBtn) bootstrap.Tab.getOrCreateInstance(payoutsTabBtn).show();
        }
    }

    if (initialTab === 'past-holdings' || hash === '#past-holdings') {
        const pastTabBtn = document.getElementById('past-holdings-tab');
        if (pastTabBtn) bootstrap.Tab.getOrCreateInstance(pastTabBtn).show();
    }

    const taxBondParam = urlParams.get('tax_bond_id');
    const taxTypeParam = urlParams.get('tax_type');
    const taxFyParam   = urlParams.get('tax_fy');
    if (taxBondParam || taxTypeParam || taxFyParam || initialTab === 'capitalgains' || hash === '#capitalgains') {
        const taxTabBtn = document.getElementById('capitalgains-tab');
        if (taxTabBtn && (initialTab === 'capitalgains' || hash === '#capitalgains')) {
            bootstrap.Tab.getOrCreateInstance(taxTabBtn).show();
        }
        if (taxBondParam && taxBondFilter) taxBondFilter.value = taxBondParam;
        if (taxTypeParam && taxTypeFilter) taxTypeFilter.value = taxTypeParam;
        if (taxFyParam && taxDateFilter) taxDateFilter.value = taxFyParam;
        applyBondTaxFilters(false);
    }

    // ==========================================
    // TABLE PAGINATION INITIALIZATION
    // ==========================================
    if (typeof window.initTablePagination === 'function') {
        window.bondLedgerPager = window.initTablePagination({
            tableId: 'bondLedgerTable',
            rowSelector: '.bond-ledger-row',
            infoId: 'bondLedgerPageInfo',
            sizeSelectId: 'bondLedgerPageSize',
            controlsId: 'bondLedgerPageControls'
        });

        window.bondTaxPager = window.initTablePagination({
            tableId: 'bondTaxLogTable',
            rowSelector: '.bond-taxlog-row',
            infoId: 'bondTaxPageInfo',
            sizeSelectId: 'bondTaxPageSize',
            controlsId: 'bondTaxPageControls'
        });

        window.bondPayoutPager = window.initTablePagination({
            tableId: 'bondPayoutTable',
            rowSelector: '.bond-payout-row',
            infoId: 'bondPayoutPageInfo',
            sizeSelectId: 'bondPayoutPageSize',
            controlsId: 'bondPayoutPageControls'
        });
    }
});
</script>

<?= $this->endSection() ?>

