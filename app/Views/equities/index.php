<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header & Top Action Buttons -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Equities</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">Equities Portfolio (Stocks)</h3>
        <small class="text-muted">Direct stock holdings across NSE & BSE tracked using FIFO tax lots</small>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#corporateActionModal">
            <i class="bi bi-diagram-3 me-1 text-primary"></i>Corporate Action
        </button>
        <a href="<?= base_url('equities/refresh-prices') ?>" class="btn btn-light border shadow-sm btn-sm rounded-3 px-3">
            <i class="bi bi-arrow-clockwise me-1 text-primary"></i>Refresh Live Prices
        </a>
        <a href="<?= base_url('equities/new') ?>" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i>Add New Stock
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
                <i class="bi bi-check-circle-fill text-success me-1"></i><?= $summary['active_holdings_count'] ?> Active Stocks
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
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Open Positions</div>
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

    <!-- Total Dividends -->
    <div class="col-sm-6 col-lg-4 col-xl-2dot4" style="flex: 0 0 auto; width: 20%;">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem;">Dividends Received</div>
            <div class="fs-5 fw-bold text-success"><?= format_inr($summary['total_dividends']) ?></div>
            <div class="text-muted small mt-1" style="font-size: 0.75rem;">Cashflows Earned</div>
        </div>
    </div>
</div>

<!-- Main Tabs Section -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-3 px-4 rounded-top-4">
        <ul class="nav nav-tabs card-header-tabs border-0" id="equitiesTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="holdings-tab" data-bs-toggle="tab" data-bs-target="#holdings" type="button" role="tab">
                    <i class="bi bi-briefcase me-1 text-primary"></i>Active Holdings (<?= count($activeHoldings ?? $holdings) ?>)
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
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="dividends-tab" data-bs-toggle="tab" data-bs-target="#dividends" type="button" role="tab">
                    <i class="bi bi-cash-coin me-1 text-success"></i>Dividends (<?= count($dividends) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="corporate-actions-tab" data-bs-toggle="tab" data-bs-target="#corporate-actions" type="button" role="tab">
                    <i class="bi bi-diagram-3 me-1 text-info"></i>Corporate Actions (<?= count($corporateActions ?? []) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="equitiesTabContent">
            
            <!-- TAB 1: ACTIVE HOLDINGS -->
            <div class="tab-pane fade show active p-3" id="holdings" role="tabpanel">
                <?php if (empty($activeHoldings ?? $holdings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-graph-up-arrow fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Active Stock Holdings Found</h5>
                        <p class="text-muted small">You don't have any active stock holdings currently. Click below to add your first stock!</p>
                        <a href="<?= base_url('equities/new') ?>" class="btn btn-primary btn-sm rounded-3">
                            <i class="bi bi-plus-lg me-1"></i>Add New Stock
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Active Holdings Sorting Toolbar -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($activeHoldings ?? $holdings) ?></strong> active stocks
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="sortEquitiesSelect" class="small text-muted mb-0 fw-semibold text-nowrap"><i class="bi bi-sort-down me-1"></i>Sort By:</label>
                            <select id="sortEquitiesSelect" class="form-select form-select-sm shadow-none border-secondary-subtle" style="width: auto;">
                                <option value="invested_desc" selected>Invested (High → Low)</option>
                                <option value="invested_asc">Invested (Low → High)</option>
                                <option value="current_desc">Current (High → Low)</option>
                                <option value="current_asc">Current (Low → High)</option>
                                <option value="name_asc">Name (A → Z)</option>
                                <option value="name_desc">Name (Z → A)</option>
                                <option value="sector_asc">Sector (A → Z)</option>
                                <option value="sector_desc">Sector (Z → A)</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="equitiesHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="cursor-pointer sortable-equity-th" data-col="name" role="button" title="Click to sort by Name">
                                        Stock <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="cursor-pointer sortable-equity-th" data-col="sector" role="button" title="Click to sort by Sector">
                                        Sector <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Qty Held</th>
                                    <th class="text-end">Avg Buy (₹)</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end cursor-pointer sortable-equity-th" data-col="invested" role="button" title="Click to sort by Invested Value">
                                        Invested (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end cursor-pointer sortable-equity-th" data-col="current" role="button" title="Click to sort by Current Value">
                                        Current (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Unrealized P&L</th>
                                    <th class="text-end">Dividends</th>
                                    <th class="text-center" style="min-width: 170px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="equitiesHoldingsTbody">
                                <?php foreach ($holdings as $h): ?>
                                    <tr class="equity-holding-row"
                                        data-name="<?= esc(strtolower($h['symbol'] . ' ' . $h['company_name'])) ?>"
                                        data-symbol="<?= esc($h['symbol']) ?>"
                                        data-sector="<?= esc(strtolower($h['sector'] ?? '')) ?>"
                                        data-invested="<?= (float)$h['invested_value'] ?>"
                                        data-current="<?= (float)$h['current_value'] ?>">
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0"><?= esc($h['company_name']) ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <span class="badge bg-secondary-subtle text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= esc($h['exchange']) ?>
                                                    </span>
                                                    <span class="text-muted small font-monospace fw-semibold" style="font-size: 0.75rem;"><?= esc($h['symbol']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-secondary small"><?= esc($h['sector'] ?: '—') ?></span>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?= number_format($h['active_quantity']) ?>
                                        </td>
                                        <td class="text-end text-muted small">
                                            <?= format_inr($h['avg_buy_price']) ?>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <div><?= format_inr($h['current_price']) ?></div>
                                            <?php $priceUpdate = $h['price_updated_at'] ?? $h['updated_at'] ?? null; ?>
                                            <?php if (!empty($priceUpdate)): ?>
                                                <div class="text-muted fw-normal" style="font-size: 0.68rem;" title="Last price update">
                                                    <?= date('d M H:i', strtotime($priceUpdate)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end small text-muted">
                                            <div class="fw-semibold text-dark"><?= format_inr($h['invested_value']) ?></div>
                                            <?php $invPct = ($summary['total_invested'] ?? 0) > 0 ? (($h['invested_value'] / $summary['total_invested']) * 100) : 0; ?>
                                            <div class="text-secondary" style="font-size: 0.72rem;"><?= number_format($invPct, 2) ?>% of total</div>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            <div><?= format_inr($h['current_value']) ?></div>
                                            <?php $currPct = ($summary['total_current_value'] ?? 0) > 0 ? (($h['current_value'] / $summary['total_current_value']) * 100) : 0; ?>
                                            <div class="text-secondary fw-normal" style="font-size: 0.72rem;"><?= number_format($currPct, 2) ?>% of total</div>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($h['unrealized_pnl'], $h['unrealized_pnl_percent'], false, true) ?>
                                        </td>
                                        <td class="text-end small text-success fw-medium">
                                            <?= $h['total_dividends'] > 0 ? format_inr($h['total_dividends']) : '—' ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary rounded-start-3 px-2 py-1 open-trans-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#transactionModal"
                                                        data-id="<?= $h['id'] ?>"
                                                        data-symbol="<?= esc($h['symbol']) ?>"
                                                        data-name="<?= esc($h['company_name']) ?>"
                                                        data-qty="<?= $h['active_quantity'] ?>"
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
                                                        <a class="dropdown-item small view-stock-ledger" href="#transactions" data-stock-id="<?= $h['id'] ?>">
                                                            <i class="bi bi-clock-history me-2 text-primary"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" 
                                                                class="dropdown-item small open-edit-stock-modal"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editStockModal"
                                                                data-id="<?= $h['id'] ?>"
                                                                data-symbol="<?= esc($h['symbol']) ?>"
                                                                data-name="<?= esc($h['company_name']) ?>"
                                                                data-isin="<?= esc($h['isin'] ?? '') ?>"
                                                                data-sector="<?= esc($h['sector'] ?? '') ?>"
                                                                data-exchange="<?= esc($h['exchange']) ?>">
                                                            <i class="bi bi-pencil me-2 text-secondary"></i>Edit Stock Details
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                           href="<?= base_url('equities/delete/' . $h['id']) ?>" 
                                                           onclick="return confirm('Delete this stock and all its trade records?');">
                                                            <i class="bi bi-trash me-2"></i>Delete Stock
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

            <!-- TAB 1B: PAST HOLDINGS (CLOSED POSITIONS) -->
            <div class="tab-pane fade p-3" id="past-holdings" role="tabpanel">
                <?php if (empty($pastHoldings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-archive fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Past Holdings Found</h5>
                        <p class="text-muted small">You don't have any fully exited stock positions yet. When you sell 100% of your holdings in a stock, it will appear here along with your realized profit/loss and total dividends collected.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($pastHoldings) ?></strong> past / closed positions
                        </div>
                        <div class="text-muted small">
                            <span class="badge bg-secondary-subtle text-secondary border"><i class="bi bi-info-circle me-1"></i>0 Active Shares &bull; Lifetime Closed Trades</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="equitiesPastHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Stock</th>
                                    <th>Sector</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end">Realized P&L</th>
                                    <th class="text-end">Dividends Received</th>
                                    <th class="text-end">Net Gain</th>
                                    <th class="text-center" style="min-width: 170px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pastHoldings as $ph): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0"><?= esc($ph['company_name']) ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <span class="badge bg-secondary-subtle text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= esc($ph['exchange']) ?>
                                                    </span>
                                                    <span class="text-muted small font-monospace fw-semibold" style="font-size: 0.75rem;"><?= esc($ph['symbol']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-secondary small"><?= esc($ph['sector'] ?: '—') ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                <i class="bi bi-check2-all me-1"></i>Closed
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <div><?= format_inr($ph['current_price']) ?></div>
                                            <?php $priceUpdate = $ph['price_updated_at'] ?? $ph['updated_at'] ?? null; ?>
                                            <?php if (!empty($priceUpdate)): ?>
                                                <div class="text-muted fw-normal" style="font-size: 0.68rem;" title="Last price update">
                                                    <?= date('d M H:i', strtotime($priceUpdate)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($ph['realized_pnl']) ?>
                                        </td>
                                        <td class="text-end small text-success fw-medium">
                                            <?= $ph['total_dividends'] > 0 ? format_inr($ph['total_dividends']) : '—' ?>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= format_pnl($ph['total_gain']) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success rounded-start-3 px-2 py-1 open-trans-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#transactionModal"
                                                        data-id="<?= $ph['id'] ?>"
                                                        data-symbol="<?= esc($ph['symbol']) ?>"
                                                        data-name="<?= esc($ph['company_name']) ?>"
                                                        data-qty="0"
                                                        data-cmp="<?= $ph['current_price'] ?>"
                                                        data-avg="0">
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
                                                        <a class="dropdown-item small view-stock-ledger" href="#transactions" data-stock-id="<?= $ph['id'] ?>">
                                                            <i class="bi bi-clock-history me-2 text-primary"></i>Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small view-stock-tax" href="#capitalgains" data-stock-id="<?= $ph['id'] ?>">
                                                            <i class="bi bi-receipt-cutoff me-2 text-info"></i>FIFO Tax Lots
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" 
                                                                class="dropdown-item small open-edit-stock-modal"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editStockModal"
                                                                data-id="<?= $ph['id'] ?>"
                                                                data-symbol="<?= esc($ph['symbol']) ?>"
                                                                data-name="<?= esc($ph['company_name']) ?>"
                                                                data-isin="<?= esc($ph['isin'] ?? '') ?>"
                                                                data-sector="<?= esc($ph['sector'] ?? '') ?>"
                                                                data-exchange="<?= esc($ph['exchange']) ?>">
                                                            <i class="bi bi-pencil me-2 text-secondary"></i>Edit Stock Details
                                                        </button>
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
                    $fyRanges          = get_fy_ranges();
                    // Collect unique stocks from transactions and calculate initial aggregates
                    $ledgerStocks      = [];
                    $overallBuyAmount  = 0.0;
                    $overallSellAmount = 0.0;
                    $overallBuyQty     = 0;
                    $overallSellQty    = 0;
                    $overallBuyCount   = 0;
                    $overallSellCount  = 0;
                    $overallCharges    = 0.0;

                    foreach ($transactions as $t) {
                        $sId = (int)$t['equity_id'];
                        if (!isset($ledgerStocks[$sId])) {
                            $ledgerStocks[$sId] = [
                                'id'           => $sId,
                                'symbol'       => $t['symbol'],
                                'company_name' => $t['company_name'],
                                'exchange'     => $t['exchange'] ?? 'NSE',
                                'count'        => 0,
                                'buy_amount'   => 0.0,
                                'sell_amount'  => 0.0,
                            ];
                        }
                        $ledgerStocks[$sId]['count']++;

                        $qty     = (int)$t['quantity'];
                        $amt     = (float)$t['total_amount'];
                        $charges = (float)$t['brokerage'] + (float)$t['stt_taxes'];
                        $overallCharges += $charges;

                        if ($t['transaction_type'] === 'BUY') {
                            $overallBuyAmount += $amt;
                            $overallBuyQty    += $qty;
                            $overallBuyCount++;
                            $ledgerStocks[$sId]['buy_amount'] += $amt;
                        } else {
                            $overallSellAmount += $amt;
                            $overallSellQty    += $qty;
                            $overallSellCount++;
                            $ledgerStocks[$sId]['sell_amount'] += $amt;
                        }
                    }
                    uasort($ledgerStocks, fn($a, $b) => strcmp($a['symbol'], $b['symbol']));
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
                                            <div class="text-muted" style="font-size: 0.72rem;">Filter by stock &amp; financial year</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                        <div style="min-width: 200px; max-width: 270px;" class="flex-grow-1">
                                            <select id="ledgerStockFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">-- All Stocks (<?= count($transactions) ?> Trades) --</option>
                                                <?php foreach ($ledgerStocks as $sId => $s): ?>
                                                    <option value="<?= $sId ?>" data-symbol="<?= esc($s['symbol']) ?>">
                                                        <?= esc($s['symbol']) ?> — <?= esc($s['company_name']) ?> (<?= $s['count'] ?>)
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
                                        <button type="button" id="btnResetStockFilter" class="btn btn-sm btn-outline-secondary d-none" title="Reset all filters">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                        </button>
                                        <span id="filteredStockBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5 small d-none">
                                            <i class="bi bi-check2-circle me-1"></i><span id="filteredStockSymbol"></span>
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
                                                <?= $overallBuyCount ?> buys • <?= number_format($overallBuyQty) ?> shares
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
                                                <?= $overallSellCount ?> sells • <?= number_format($overallSellQty) ?> shares
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Net Outlay (Buys - Sells)</span>
                                            <h5 class="fw-bold text-primary mb-0 mt-1" id="kpiNetOutlay"><?= format_inr($overallNetOutlay) ?></h5>
                                            <div class="text-muted small mt-1" id="kpiNetDetails" style="font-size: 0.75rem;">
                                                Net cash invested
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Charges &amp; Taxes</span>
                                            <h5 class="fw-bold text-secondary mb-0 mt-1" id="kpiCharges"><?= format_inr($overallCharges) ?></h5>
                                            <div class="text-muted small mt-1" id="kpiChargesDetails" style="font-size: 0.75rem;">
                                                Brokerage + STT &amp; Taxes
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

                    <!-- Transactions Table with Dynamic Footer -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="ledgerTransactionsTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Date</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Price (₹)</th>
                                    <th class="text-end">Charges (₹)</th>
                                    <th class="text-end">Net Outlay / Proceeds</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ledgerTransactionsTbody">
                                <?php foreach ($transactions as $t): ?>
                                    <tr class="ledger-trans-row"
                                        data-equity-id="<?= $t['equity_id'] ?>"
                                        data-symbol="<?= esc($t['symbol']) ?>"
                                        data-date="<?= $t['transaction_date'] ?>"
                                        data-type="<?= $t['transaction_type'] ?>"
                                        data-qty="<?= (int)$t['quantity'] ?>"
                                        data-price="<?= (float)$t['price'] ?>"
                                        data-charges="<?= (float)$t['brokerage'] + (float)$t['stt_taxes'] ?>"
                                        data-amount="<?= (float)$t['total_amount'] ?>">
                                        <td><?= date('d-M-Y', strtotime($t['transaction_date'])) ?></td>
                                        <td>
                                            <strong><?= esc($t['symbol']) ?></strong>
                                            <span class="text-muted">(<?= esc($t['exchange']) ?>)</span>
                                        </td>
                                        <td>
                                            <?php if ($t['transaction_type'] === 'BUY'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">BUY</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">SELL</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-semibold"><?= number_format($t['quantity']) ?></td>
                                        <td class="text-end"><?= format_inr($t['price']) ?></td>
                                        <td class="text-end text-muted"><?= format_inr((float)$t['brokerage'] + (float)$t['stt_taxes']) ?></td>
                                        <td class="text-end fw-bold text-dark"><?= format_inr($t['total_amount']) ?></td>
                                        <td class="text-secondary"><?= esc($t['notes'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary py-0 px-1.5 open-edit-equity-trans"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editEquityTransModal"
                                                    data-id="<?= $t['id'] ?>"
                                                    data-symbol="<?= esc($t['symbol']) ?>"
                                                    data-type="<?= esc($t['transaction_type']) ?>"
                                                    data-date="<?= esc($t['transaction_date']) ?>"
                                                    data-qty="<?= (int)$t['quantity'] ?>"
                                                    data-price="<?= (float)$t['price'] ?>"
                                                    data-brokerage="<?= (float)$t['brokerage'] ?>"
                                                    data-stt="<?= (float)$t['stt_taxes'] ?>"
                                                    data-notes="<?= esc($t['notes'] ?? '') ?>"
                                                    title="Edit Transaction">
                                                <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <a href="<?= base_url('equities/delete-transaction/' . $t['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger py-0 px-1.5 ms-1"
                                               onclick="return confirm('Delete this <?= esc($t['transaction_type']) ?> transaction? FIFO lots and capital gains will be automatically recalculated.');"
                                               title="Delete Transaction">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="ledgerNoRows" class="d-none">
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-search me-1"></i> No transactions found for the selected stock.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-group-divider bg-light small fw-bold">
                                <tr>
                                    <td colspan="2">
                                        <span id="footerSummaryTitle">Total (All <?= count($transactions) ?> Trades)</span>
                                    </td>
                                    <td>
                                        <span id="footerActionBreakdown" class="badge bg-light text-dark border">
                                            <span class="text-success"><?= $overallBuyCount ?> BUY</span> &bull; <span class="text-danger"><?= $overallSellCount ?> SELL</span>
                                        </span>
                                    </td>
                                    <td class="text-end" id="footerTotalQty"><?= number_format($overallBuyQty + $overallSellQty) ?></td>
                                    <td class="text-end text-muted small">—</td>
                                    <td class="text-end text-muted" id="footerTotalCharges"><?= format_inr($overallCharges) ?></td>
                                    <td class="text-end text-dark fs-6" id="footerTotalAmount"><?= format_inr($overallNetOutlay) ?></td>
                                    <td colspan="2" class="text-muted small fw-normal" id="footerNote">
                                        Net Cash Outlay
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'equityLedger']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 3: FIFO TAX LOG (STCG vs LTCG) -->
            <div class="tab-pane fade p-3" id="capitalgains" role="tabpanel">
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center rounded-3 small">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Indian Income Tax FIFO Matching:</strong> Shares are liquidated in First-In, First-Out sequence.
                        Holding period <strong>&le; 365 days = STCG</strong>. Holding period <strong>&gt; 365 days = LTCG</strong>.
                    </div>
                </div>

                <?php if (empty($capitalGains)): ?>
                    <p class="text-muted text-center py-4">No realized sales recorded yet. When shares are sold, FIFO matched tax lots will appear here.</p>
                <?php else: ?>
                    <?php
                    $fyRanges          = get_fy_ranges();
                    $taxStocks         = [];
                    $totalMatchedQty   = 0;
                    $totalBuyCost      = 0.0;
                    $totalSellProceeds = 0.0;
                    $totalRealizedGain = 0.0;
                    $totalStcg         = 0.0;
                    $totalLtcg         = 0.0;

                    foreach ($capitalGains as $cg) {
                        $sId = (int)$cg['equity_id'];
                        if (!isset($taxStocks[$sId])) {
                            $taxStocks[$sId] = [
                                'id'     => $sId,
                                'symbol' => $cg['symbol'],
                                'name'   => $cg['company_name'],
                                'count'  => 0,
                            ];
                        }
                        $taxStocks[$sId]['count']++;
                        $qty      = (int)$cg['quantity_matched'];
                        $cost     = $qty * (float)$cg['buy_price'];
                        $proceeds = $qty * (float)$cg['sell_price'];
                        $gain     = (float)$cg['realized_gain'];

                        $totalMatchedQty   += $qty;
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

                    <!-- Filter Toolbar: Stock Selector + Gain Type + Date Range -->
                    <div class="bg-light p-3 rounded-4 border mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="taxLogStockFilter" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-filter me-1"></i>Filter by Stock
                                </label>
                                <select class="form-select form-select-sm shadow-sm" id="taxLogStockFilter">
                                    <option value="ALL">All Stocks (<?= count($capitalGains) ?> lots)</option>
                                    <?php foreach ($taxStocks as $st): ?>
                                        <option value="<?= $st['id'] ?>" data-symbol="<?= esc($st['symbol']) ?>">
                                            <?= esc($st['symbol']) ?> &mdash; <?= esc($st['name']) ?> (<?= $st['count'] ?> lots)
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
                                <i class="bi bi-funnel-fill me-1"></i><span id="filteredTaxSymbolSpan">Active Filter</span>
                            </span>
                        </div>
                    </div>

                    <!-- 4 Responsive KPI Metric Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-primary border-4">
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Matched Shares</div>
                                <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxQty"><?= number_format($totalMatchedQty) ?></div>
                                <div class="text-muted small" style="font-size: 0.72rem;" id="kpiTaxQtyCount"><?= count($capitalGains) ?> FIFO lots</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-secondary border-4">
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Buy Cost</div>
                                <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxCost"><?= format_inr($totalBuyCost) ?></div>
                                <div class="text-muted small" style="font-size: 0.72rem;">Original purchase cost</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-info border-4">
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Sale Proceeds</div>
                                <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxProceeds"><?= format_inr($totalSellProceeds) ?></div>
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
                        <table class="table table-hover align-middle mb-0 small" id="equitiesTaxLogTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Stock</th>
                                    <th>Buy Date</th>
                                    <th>Sell Date</th>
                                    <th class="text-center">Days Held</th>
                                    <th class="text-center">Tax Category</th>
                                    <th class="text-end">Matched Qty</th>
                                    <th class="text-end">Buy Price (₹)</th>
                                    <th class="text-end">Sell Price (₹)</th>
                                    <th class="text-end">Realized P&amp;L (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($capitalGains as $cg): ?>
                                    <?php
                                    $qty      = (int)$cg['quantity_matched'];
                                    $cost     = $qty * (float)$cg['buy_price'];
                                    $proceeds = $qty * (float)$cg['sell_price'];
                                    $gain     = (float)$cg['realized_gain'];
                                    ?>
                                    <tr class="equity-taxlog-row"
                                        data-stock-id="<?= $cg['equity_id'] ?>"
                                        data-symbol="<?= esc($cg['symbol']) ?>"
                                        data-gain-type="<?= esc($cg['gain_type']) ?>"
                                        data-date="<?= $cg['sell_date'] ?>"
                                        data-qty="<?= $qty ?>"
                                        data-cost="<?= $cost ?>"
                                        data-proceeds="<?= $proceeds ?>"
                                        data-gain="<?= $gain ?>">
                                        <td><strong><?= esc($cg['symbol']) ?></strong> (<?= esc($cg['company_name']) ?>)</td>
                                        <td><?= date('d-M-Y', strtotime($cg['buy_date'])) ?></td>
                                        <td><?= date('d-M-Y', strtotime($cg['sell_date'])) ?></td>
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
                                        <td class="text-end fw-semibold"><?= number_format($qty) ?></td>
                                        <td class="text-end text-muted"><?= format_inr($cg['buy_price']) ?></td>
                                        <td class="text-end fw-medium"><?= format_inr($cg['sell_price']) ?></td>
                                        <td class="text-end">
                                            <?= format_pnl($gain) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-group-divider bg-light fw-semibold" id="equitiesTaxFooter">
                                <tr>
                                    <td colspan="5" class="py-3">
                                        <span class="fw-bold text-dark" id="footerTaxSummaryTitle">Total (<?= count($capitalGains) ?> Lots)</span>
                                        <span class="ms-2 badge bg-light text-secondary border fw-normal" id="footerTaxBreakdown">
                                            All FIFO lots
                                        </span>
                                    </td>
                                    <td class="text-end py-3 fw-bold" id="footerTaxQty"><?= number_format($totalMatchedQty) ?></td>
                                    <td class="text-end py-3 text-muted" id="footerTaxCost"><?= format_inr($totalBuyCost) ?></td>
                                    <td class="text-end py-3 text-muted" id="footerTaxProceeds"><?= format_inr($totalSellProceeds) ?></td>
                                    <td class="text-end py-3 fs-6 fw-bold <?= $totalRealizedGain >= 0 ? 'text-success' : 'text-danger' ?>" id="footerTaxGain">
                                        <?= ($totalRealizedGain >= 0 ? '+' : '') . format_inr($totalRealizedGain) ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'equityTax']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 4: DIVIDENDS -->
            <div class="tab-pane fade p-3" id="dividends" role="tabpanel">
                <?php if (empty($dividends)): ?>
                    <p class="text-muted text-center py-4">No dividend credits recorded yet.</p>
                <?php else: ?>
                    <?php
                    $divStocks          = [];
                    $totalGrossDiv      = 0.0;
                    $totalTdsDiv        = 0.0;
                    $totalNetDiv        = 0.0;
                    $totalSharesHeldDiv = 0;
                    $divCount           = count($dividends);

                    foreach ($dividends as $d) {
                        $sId = (int)$d['equity_id'];
                        if (!isset($divStocks[$sId])) {
                            $divStocks[$sId] = [
                                'id'     => $sId,
                                'symbol' => $d['symbol'],
                                'count'  => 0,
                            ];
                        }
                        $divStocks[$sId]['count']++;
                        $gross = (float)$d['total_amount'];
                        $tds   = (float)$d['tds_deducted'];
                        $totalGrossDiv      += $gross;
                        $totalTdsDiv        += $tds;
                        $totalNetDiv        += ($gross - $tds);
                        $totalSharesHeldDiv += (int)($d['shares_held'] ?? 0);
                    }
                    uasort($divStocks, fn($a, $b) => strcmp($a['symbol'], $b['symbol']));
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
                                            <label class="form-label mb-0 fw-semibold text-dark small">Filter Dividends</label>
                                            <div class="text-muted" style="font-size: 0.72rem;">Filter by stock &amp; financial year</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                        <div style="min-width: 200px; max-width: 270px;" class="flex-grow-1">
                                            <select id="divStockFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">-- All Stocks (<?= count($dividends) ?> Dividends) --</option>
                                                <?php foreach ($divStocks as $sId => $s): ?>
                                                    <option value="<?= $sId ?>" data-symbol="<?= esc($s['symbol']) ?>">
                                                        <?= esc($s['symbol']) ?> (<?= $s['count'] ?> payouts)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div style="min-width: 170px; max-width: 200px;">
                                            <select id="divDateFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">All Dates (All Time)</option>
                                                <option value="CURRENT_FY">Current FY (<?= $fyRanges['current']['label'] ?>)</option>
                                                <option value="LAST_FY">Last FY (<?= $fyRanges['last']['label'] ?>)</option>
                                            </select>
                                        </div>
                                        <button type="button" id="btnResetDivFilter" class="btn btn-sm btn-outline-secondary d-none" title="Reset all filters">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                        </button>
                                        <span id="filteredDivBadge" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 small d-none">
                                            <i class="bi bi-check2-circle me-1"></i><span id="filteredDivSymbol"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Amounts KPI Summary Cards -->
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100 border-start border-4 border-success">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Gross Dividends</span>
                                            <h5 class="fw-bold text-success mb-0 mt-1" id="kpiDivGross"><?= format_inr($totalGrossDiv) ?></h5>
                                            <div class="text-muted small mt-1" id="kpiDivGrossSubtitle" style="font-size: 0.75rem;">
                                                <?= $divCount ?> payouts recorded
                                            </div>
                                        </div>
                                        <div class="bg-success-subtle text-success rounded-3 p-2">
                                            <i class="bi bi-cash-stack fs-5"></i>
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">TDS Deducted (10%)</span>
                                            <h5 class="fw-bold text-danger mb-0 mt-1" id="kpiDivTds"><?= format_inr($totalTdsDiv) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Claimable in Form 26AS
                                            </div>
                                        </div>
                                        <div class="bg-danger-subtle text-danger rounded-3 p-2">
                                            <i class="bi bi-shield-minus fs-5"></i>
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Net Received in Bank</span>
                                            <h5 class="fw-bold text-primary mb-0 mt-1" id="kpiDivNet"><?= format_inr($totalNetDiv) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Gross minus TDS
                                            </div>
                                        </div>
                                        <div class="bg-primary-subtle text-primary rounded-3 p-2">
                                            <i class="bi bi-bank fs-5"></i>
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Payouts</span>
                                            <h5 class="fw-bold text-dark mb-0 mt-1" id="kpiDivCount"><?= $divCount ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Dividend credits
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

                    <!-- Dividends Table with Dynamic Footer -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="dividendsTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Credit Date</th>
                                    <th>Stock</th>
                                    <th>Type</th>
                                    <th class="text-end">Shares Held</th>
                                    <th class="text-end">Per Share (₹)</th>
                                    <th class="text-end">TDS Deducted (₹)</th>
                                    <th class="text-end">Gross Amount</th>
                                    <th class="text-end">Net Received</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="dividendsTbody">
                                <?php foreach ($dividends as $d): ?>
                                    <?php 
                                    $grossAmt = (float)$d['total_amount'];
                                    $tdsAmt   = (float)$d['tds_deducted'];
                                    $netAmt   = $grossAmt - $tdsAmt;
                                    ?>
                                    <tr class="div-trans-row"
                                        data-equity-id="<?= $d['equity_id'] ?>"
                                        data-symbol="<?= esc($d['symbol']) ?>"
                                        data-date="<?= $d['dividend_date'] ?>"
                                        data-shares="<?= (int)($d['shares_held'] ?? 0) ?>"
                                        data-gross="<?= $grossAmt ?>"
                                        data-tds="<?= $tdsAmt ?>"
                                        data-net="<?= $netAmt ?>">
                                        <td><?= date('d-M-Y', strtotime($d['dividend_date'])) ?></td>
                                        <td><strong><?= esc($d['symbol']) ?></strong></td>
                                        <td>
                                            <span class="badge bg-light text-secondary border px-2 py-1"><?= esc($d['dividend_type']) ?></span>
                                        </td>
                                        <td class="text-end"><?= $d['shares_held'] ? number_format($d['shares_held']) : '—' ?></td>
                                        <td class="text-end text-muted"><?= $d['amount_per_share'] ? format_inr($d['amount_per_share']) : '—' ?></td>
                                        <td class="text-end text-danger"><?= $tdsAmt > 0 ? '-' . format_inr($tdsAmt) : '₹ 0.00' ?></td>
                                        <td class="text-end fw-semibold text-dark"><?= format_inr($grossAmt) ?></td>
                                        <td class="text-end fw-bold text-success"><?= format_inr($netAmt) ?></td>
                                        <td class="text-secondary"><?= esc($d['notes'] ?: '—') ?></td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary py-0 px-1.5 open-edit-dividend"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editEquityDividendModal"
                                                    data-id="<?= $d['id'] ?>"
                                                    data-symbol="<?= esc($d['symbol']) ?>"
                                                    data-date="<?= esc($d['dividend_date']) ?>"
                                                    data-type="<?= esc($d['dividend_type']) ?>"
                                                    data-shares="<?= (int)($d['shares_held'] ?? 0) ?>"
                                                    data-per-share="<?= (float)($d['amount_per_share'] ?? 0) ?>"
                                                    data-tds="<?= $tdsAmt ?>"
                                                    data-total="<?= $grossAmt ?>"
                                                    data-notes="<?= esc($d['notes'] ?? '') ?>"
                                                    title="Edit Dividend">
                                                <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <a href="<?= base_url('equities/delete-dividend/' . $d['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger py-0 px-1.5 ms-1"
                                               onclick="return confirm('Delete this dividend record?');"
                                               title="Delete Dividend">
                                                <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="divNoRows" class="d-none">
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="bi bi-search me-1"></i> No dividends found for the selected criteria.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-group-divider bg-light small fw-bold">
                                <tr>
                                    <td colspan="3">
                                        <span id="footerDivSummaryTitle">Total (All <?= $divCount ?> Payouts)</span>
                                    </td>
                                    <td class="text-end" id="footerDivShares">—</td>
                                    <td class="text-end text-muted small">—</td>
                                    <td class="text-end text-danger" id="footerDivTds"><?= format_inr($totalTdsDiv) ?></td>
                                    <td class="text-end text-dark" id="footerDivGross"><?= format_inr($totalGrossDiv) ?></td>
                                    <td class="text-end text-success fs-6" id="footerDivNet"><?= format_inr($totalNetDiv) ?></td>
                                    <td colspan="2" class="text-muted small fw-normal">Net Bank Credits</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'equityDiv']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 5: CORPORATE ACTIONS LOG -->
            <div class="tab-pane fade p-3" id="corporate-actions" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0"><i class="bi bi-diagram-3 text-primary me-2"></i>Corporate Actions Audit Log</h6>
                        <small class="text-muted">History of stock splits, bonus allotments, rights issues, mergers, and demergers</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#corporateActionModal">
                            <i class="bi bi-plus-circle me-1"></i>Record Corporate Action
                        </button>
                    </div>
                </div>

                <?php if (empty($corporateActions)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-diagram-3 fs-1 d-block mb-3 text-secondary"></i>
                        <h6>No Corporate Actions Recorded Yet</h6>
                        <p class="small text-muted mb-3">When a stock announces a split, bonus shares, rights issue, or merger/demerger, record it here.</p>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#corporateActionModal">
                            <i class="bi bi-plus-lg me-1"></i>Record Corporate Action
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="equityCorporateActionsTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Record Date</th>
                                    <th>Stock</th>
                                    <th>Action Type</th>
                                    <th class="text-center">Ratio / Details</th>
                                    <th class="text-end">Shares Before</th>
                                    <th class="text-end">Shares After</th>
                                    <th>Target / Apportionment</th>
                                    <th>Notes</th>
                                    <th>Recorded At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($corporateActions as $ca): ?>
                                    <tr class="ca-row">
                                        <td class="fw-semibold text-dark"><?= date('d-M-Y', strtotime($ca['record_date'])) ?></td>
                                        <td>
                                            <strong class="text-primary"><?= esc($ca['security_symbol']) ?></strong>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($ca['security_name']) ?></div>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = match($ca['action_type']) {
                                                'SPLIT' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                                'BONUS' => 'bg-success-subtle text-success border border-success-subtle',
                                                'RIGHTS' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                                'MERGER' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                'DEMERGER' => 'bg-secondary-subtle text-dark border',
                                                default => 'bg-light text-dark border'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?> px-2 py-1"><?= esc($ca['action_type']) ?></span>
                                        </td>
                                        <td class="text-center fw-semibold">
                                            <?php if ($ca['action_type'] === 'RIGHTS'): ?>
                                                Price: <?= format_inr($ca['offer_price'] ?? 0) ?>
                                            <?php else: ?>
                                                <?= (float)$ca['ratio_new'] ?> : <?= (float)$ca['ratio_old'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-semibold text-muted"><?= number_format($ca['shares_before'], 2) ?></td>
                                        <td class="text-end fw-bold text-dark"><?= number_format($ca['shares_after'], 2) ?></td>
                                        <td>
                                            <?php if (!empty($ca['target_security_symbol'])): ?>
                                                <span class="badge bg-light text-dark border">
                                                    &rarr; <?= esc($ca['target_security_symbol']) ?>
                                                    <?= !empty($ca['cost_apportionment_ratio']) ? ' (' . (float)$ca['cost_apportionment_ratio'] . '%)' : '' ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-secondary"><?= esc($ca['notes'] ?: '—') ?></td>
                                        <td class="text-muted small"><?= date('d-M-Y H:i', strtotime($ca['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'equityCA']) ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MINIMAL TRANSACTION POPUP MODAL (Buy More / Sell / Record Dividend)       -->
<!-- ========================================================================= -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="transactionModalLabel">
                        Add Transaction: <span id="modalStockSymbol" class="text-primary"></span>
                    </h5>
                    <div class="text-muted small" id="modalStockSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('equities/transaction') ?>" method="POST" id="transForm">
                <?= csrf_field() ?>
                <input type="hidden" name="equity_id" id="modalEquityId" value="">

                <div class="modal-body p-4">
                    <!-- Segmented Action Tabs -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actBuy" value="BUY" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actBuy">
                            <i class="bi bi-cart-plus me-1"></i>Buy More
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actSell" value="SELL" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actSell">
                            <i class="bi bi-cart-dash me-1"></i>Sell (FIFO)
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actDividend" value="DIVIDEND" autocomplete="off">
                        <label class="btn btn-outline-primary fw-semibold" for="actDividend">
                            <i class="bi bi-cash-coin me-1"></i>Dividend
                        </label>
                    </div>

                    <!-- SUB-FORM 1: BUY MORE -->
                    <div id="panelBuy" class="trans-panel">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="buyDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="buyQty" min="1" step="1" placeholder="Shares" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Buy Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="buyPrice" min="0.01" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Brokerage + Taxes (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="buyCharges" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Added on correction">
                            </div>
                        </div>

                        <!-- Buy Outlay Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Total Outlay:</span>
                                <span class="fw-bold text-success" id="buyTotalPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-FORM 2: SELL -->
                    <div id="panelSell" class="trans-panel d-none">
                        <div class="alert alert-warning py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Available to Sell:</span>
                            <strong id="sellAvailableQtyBadge">0 shares</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Sale Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="sellDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity to Sell <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="sellQty" min="1" step="1" placeholder="Shares" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Sell Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="sellPrice" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Brokerage + STT (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="sellCharges" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Booking profits" disabled>
                            </div>
                        </div>

                        <!-- Sell Proceeds Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Estimated Net Proceeds:</span>
                                <span class="fw-bold text-dark" id="sellTotalPreview">₹ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Realized P&L:</span>
                                <span class="small fw-semibold" id="sellPnlPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-FORM 3: RECORD DIVIDEND -->
                    <div id="panelDividend" class="trans-panel d-none">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Credit Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="dividend_date" id="divDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="dividend_type" disabled>
                                    <option value="Interim">Interim</option>
                                    <option value="Final">Final</option>
                                    <option value="Special">Special</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Total Net Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="total_amount" id="divAmount" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                                <input type="number" class="form-control" name="tds_deducted" id="divTds" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Dividend Per Share (₹)</label>
                                <input type="number" class="form-control" name="amount_per_share" id="divPerShare" min="0" step="0.01" placeholder="Optional" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Shares Held on Record Date</label>
                                <input type="number" class="form-control" name="shares_held" id="divSharesHeld" min="0" step="1" placeholder="Optional" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Q3 Interim Dividend" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnSubmitTrans">
                        Confirm Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT STOCK METADATA MODAL                                                 -->
<!-- ========================================================================= -->
<div class="modal fade" id="editStockModal" tabindex="-1" aria-labelledby="editStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editStockModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Stock Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('equities/update-stock') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="equity_id" id="editStockId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Trading Symbol <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold text-uppercase" name="symbol" id="editStockSymbol" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Exchange <span class="text-danger">*</span></label>
                            <select class="form-select" name="exchange" id="editStockExchange" required>
                                <option value="NSE">NSE</option>
                                <option value="BSE">BSE</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="company_name" id="editStockName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ISIN Code</label>
                            <input type="text" class="form-control text-uppercase" name="isin" id="editStockIsin" placeholder="e.g. INE002A01018">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Industry Sector</label>
                            <select class="form-select" name="sector" id="editStockSector">
                                <option value="">-- Select Sector / Industry --</option>
                                <?php if (!empty($sectors)): ?>
                                    <?php foreach ($sectors as $sec): ?>
                                        <option value="<?= esc($sec['name']) ?>"><?= esc($sec['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
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
<!-- MODAL: RECORD CORPORATE ACTION (SPLIT, BONUS, RIGHTS, MERGER, DEMERGER)   -->
<!-- ========================================================================= -->
<div class="modal fade" id="corporateActionModal" tabindex="-1" aria-labelledby="corporateActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= base_url('equities/corporate-action') ?>" method="POST" id="corporateActionForm">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark" id="corporateActionModalLabel">
                        <i class="bi bi-diagram-3 text-primary me-2"></i>Record Corporate Action
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Step 1: Select Stock -->
                    <div class="mb-3">
                        <label for="ca_equity_id" class="form-label small fw-semibold text-secondary">Select Stock <span class="text-danger">*</span></label>
                        <select class="form-select" id="ca_equity_id" name="equity_id" required>
                            <option value="">-- Choose Stock --</option>
                            <?php foreach ($holdings as $h): ?>
                                <option value="<?= $h['id'] ?>" data-symbol="<?= esc($h['symbol']) ?>">
                                    <?= esc($h['symbol']) ?> — <?= esc($h['company_name']) ?> (<?= number_format((float)($h['active_quantity'] ?? $h['total_quantity'] ?? 0)) ?> shares)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Step 2: Action Type -->
                    <div class="mb-3">
                        <label for="ca_action_type" class="form-label small fw-semibold text-secondary">Action Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="ca_action_type" name="action_type" required>
                            <option value="SPLIT">Stock Split (e.g. 1:2, 1:5, 1:10)</option>
                            <option value="BONUS">Bonus Issue (e.g. 1:1, 2:1, 1:4)</option>
                            <option value="RIGHTS">Rights Issue (Subsidized buy)</option>
                            <option value="MERGER">Merger / Amalgamation (Swap into Target Stock)</option>
                            <option value="DEMERGER">Demerger / Spin-off (Apportion cost to new Stock)</option>
                        </select>
                    </div>

                    <!-- Step 3: Record Date -->
                    <div class="mb-3">
                        <label for="ca_record_date" class="form-label small fw-semibold text-secondary">Record Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="ca_record_date" name="record_date" value="<?= date('Y-m-d') ?>" required>
                        <div class="mt-1 d-flex align-items-center gap-2" id="ca_eligibility_box">
                            <span class="badge bg-light text-secondary border small" id="ca_eligible_badge">
                                <i class="bi bi-info-circle me-1"></i>Select stock to check eligible shares on record date
                            </span>
                        </div>
                    </div>

                    <!-- Conditional Section: Ratio (Split, Bonus, Merger, Demerger) -->
                    <div class="row g-2 mb-3" id="ca_ratio_group">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary" id="ca_ratio_new_label">New Shares <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="ca_ratio_new" name="ratio_new" value="2">
                            <div class="form-text small" id="ca_ratio_new_help">Shares received</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary" id="ca_ratio_old_label">For Existing Shares <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="ca_ratio_old" name="ratio_old" value="1">
                            <div class="form-text small" id="ca_ratio_old_help">Shares held on record date</div>
                        </div>
                    </div>

                    <!-- Conditional Section: Rights Issue Fields -->
                    <div class="row g-2 mb-3 d-none" id="ca_rights_group">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Subscribed Shares <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="ca_subscribed_shares" name="subscribed_shares">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Offer Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="ca_offer_price" name="offer_price">
                        </div>
                    </div>

                    <!-- Conditional Section: Target Security (Merger / Demerger) -->
                    <div class="mb-3 d-none" id="ca_target_group">
                        <label for="ca_target_security_id" class="form-label small fw-semibold text-secondary" id="ca_target_label">Destination / Resulting Stock <span class="text-danger">*</span></label>
                        <select class="form-select" id="ca_target_security_id" name="target_security_id">
                            <option value="">-- Choose Stock --</option>
                            <?php foreach ($holdings as $h): ?>
                                <option value="<?= $h['id'] ?>"><?= esc($h['symbol']) ?> — <?= esc($h['company_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small text-muted" id="ca_target_help">
                            If the resulting entity is not yet in your portfolio, add it via "Add New Stock" first.
                        </div>
                    </div>

                    <!-- Conditional Section: Demerger Cost Apportionment % -->
                    <div class="mb-3 d-none" id="ca_demerger_group">
                        <label for="ca_cost_apportionment" class="form-label small fw-semibold text-secondary">Cost Apportionment % to Spun-off Entity <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" max="99.99" class="form-control" id="ca_cost_apportionment" name="cost_apportionment_ratio" placeholder="e.g. 33.5">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text small text-muted">
                            As per circular (e.g. 33.5% cost basis apportioned to new entity, 66.5% retained by parent).
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-2">
                        <label for="ca_notes" class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                        <textarea class="form-control" id="ca_notes" name="notes" rows="2" placeholder="e.g. Approved in AGM / Board Meeting"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-semibold" id="ca_submit_btn">
                        <i class="bi bi-check2-circle me-1"></i>Apply Corporate Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT TRANSACTION MODAL (WITH FIFO REBUILD)                                -->
<!-- ========================================================================= -->
<div class="modal fade" id="editEquityTransModal" tabindex="-1" aria-labelledby="editEquityTransModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editEquityTransModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i><span id="editEquityTransTitle">Edit Transaction</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('equities/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editEquityTransId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Saving changes will automatically <strong>rebalance FIFO lots and recalculate capital gains</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editEquityTransDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quantity (Shares) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="quantity" id="editEquityTransQty" min="1" step="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Price per Share (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="editEquityTransPrice" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                            <input type="number" class="form-control" name="brokerage" id="editEquityTransBrokerage" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STT / Taxes (₹)</label>
                            <input type="number" class="form-control" name="stt_taxes" id="editEquityTransStt" min="0" step="0.01">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editEquityTransNotes">
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
<!-- EDIT DIVIDEND MODAL                                                       -->
<!-- ========================================================================= -->
<div class="modal fade" id="editEquityDividendModal" tabindex="-1" aria-labelledby="editEquityDividendModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editEquityDividendModalLabel">
                    <i class="bi bi-cash-coin text-success me-2"></i><span id="editEquityDivTitle">Edit Dividend</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('equities/update-dividend') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="dividend_id" id="editEquityDivId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Credit Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="dividend_date" id="editEquityDivDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Dividend Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="dividend_type" id="editEquityDivType" required>
                                <option value="Interim">Interim</option>
                                <option value="Final">Final</option>
                                <option value="Special">Special</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Shares Held</label>
                            <input type="number" class="form-control" name="shares_held" id="editEquityDivShares" min="0" step="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Dividend per Share (₹)</label>
                            <input type="number" class="form-control" name="amount_per_share" id="editEquityDivPerShare" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Total Gross Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="total_amount" id="editEquityDivTotal" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                            <input type="number" class="form-control text-danger" name="tds_deducted" id="editEquityDivTds" min="0" step="0.01">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editEquityDivNotes">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">Save Dividend</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    <?php $fyRangesData = get_fy_ranges(); ?>
    window.EQUITIES_CONFIG = {
        currentFyStart: '<?= $fyRangesData['current']['start'] ?>',
        currentFyEnd:   '<?= $fyRangesData['current']['end'] ?>',
        lastFyStart:    '<?= $fyRangesData['last']['start'] ?>',
        lastFyEnd:      '<?= $fyRangesData['last']['end'] ?>',
        checkEligibilityUrl: '<?= base_url('equities/check-corporate-action-eligibility') ?>'
    };
</script>
<script src="<?= base_url('assets/js/equities.js') ?>"></script>

<?= $this->endSection() ?>

