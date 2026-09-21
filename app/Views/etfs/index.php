<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header & Top Action Buttons -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">ETFs</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">Exchange Traded Funds (ETFs)</h3>
        <small class="text-muted">Passive index funds, gold, silver, and global ETFs tracked with FIFO tax lots</small>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#etfSplitModal">
            <i class="bi bi-pie-chart-fill me-1 text-info"></i>Record Split
        </button>
        <a href="<?= base_url('etfs/refresh-prices') ?>" class="btn btn-light border shadow-sm btn-sm rounded-3 px-3">
            <i class="bi bi-arrow-clockwise me-1 text-primary"></i>Refresh Live Prices
        </a>
        <a href="<?= base_url('etfs/new') ?>" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i>Add New ETF
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
                <i class="bi bi-check-circle-fill text-success me-1"></i><?= $summary['active_holdings_count'] ?> Active ETFs
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
        <ul class="nav nav-tabs card-header-tabs border-0" id="etfsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="holdings-tab" data-bs-toggle="tab" data-bs-target="#holdings" type="button" role="tab">
                    <i class="bi bi-pie-chart me-1 text-info"></i>Active Holdings (<?= count($holdings) ?>)
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
                <button class="nav-link fw-semibold" id="corporate-actions-tab" data-bs-toggle="tab" data-bs-target="#corporate-actions" type="button" role="tab">
                    <i class="bi bi-diagram-3 me-1 text-secondary"></i>Splits Log (<?= count($corporateActions ?? []) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="etfsTabContent">
            
            <!-- TAB 1: ACTIVE HOLDINGS -->
            <div class="tab-pane fade show active p-3" id="holdings" role="tabpanel">
                <?php if (empty($holdings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-pie-chart fs-1 text-muted mb-3 d-block"></i>
                        <h5>No ETF Holdings Found</h5>
                        <p class="text-muted small">You haven't added any ETFs yet. Click below to add your first ETF!</p>
                        <a href="<?= base_url('etfs/new') ?>" class="btn btn-primary btn-sm rounded-3">
                            <i class="bi bi-plus-lg me-1"></i>Add New ETF
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Active Holdings Sorting Toolbar -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($holdings) ?></strong> active ETFs
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="sortEtfsSelect" class="small text-muted mb-0 fw-semibold text-nowrap"><i class="bi bi-sort-down me-1"></i>Sort By:</label>
                            <select id="sortEtfsSelect" class="form-select form-select-sm shadow-none border-secondary-subtle" style="width: auto;">
                                <option value="invested_desc" selected>Invested (High → Low)</option>
                                <option value="invested_asc">Invested (Low → High)</option>
                                <option value="current_desc">Current (High → Low)</option>
                                <option value="current_asc">Current (Low → High)</option>
                                <option value="name_asc">Name (A → Z)</option>
                                <option value="name_desc">Name (Z → A)</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="etfHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="cursor-pointer sortable-etf-th" data-col="name" role="button" title="Click to sort by Name">
                                        ETF Symbol / Name <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th>Category</th>
                                    <th>AMC / Fund House</th>
                                    <th class="text-end">Units Held</th>
                                    <th class="text-end">Avg Cost (₹)</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end cursor-pointer sortable-etf-th" data-col="invested" role="button" title="Click to sort by Invested Value">
                                        Invested (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end cursor-pointer sortable-etf-th" data-col="current" role="button" title="Click to sort by Current Value">
                                        Current (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Unrealized P&L</th>
                                    <th class="text-center" style="min-width: 170px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="etfHoldingsTbody">
                                <?php foreach ($holdings as $h): ?>
                                    <tr class="etf-holding-row"
                                        data-name="<?= esc(strtolower($h['symbol'] . ' ' . $h['etf_name'])) ?>"
                                        data-invested="<?= (float)$h['invested_value'] ?>"
                                        data-current="<?= (float)$h['current_value'] ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="brand-badge-sm me-2 bg-info bg-opacity-10 text-info rounded px-2 py-1 small fw-bold">
                                                    <?= esc($h['symbol']) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark mb-0"><?= esc($h['etf_name']) ?></div>
                                                    <span class="badge bg-secondary-subtle text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= esc($h['exchange']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $catBadge = match($h['category']) {
                                                'Commodity - Gold'   => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                                'Commodity - Silver' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                                'Index'              => 'bg-primary-subtle text-primary border-primary-subtle',
                                                'Global'             => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                                'Sectoral'           => 'bg-dark-subtle text-dark border-dark-subtle',
                                                default              => 'bg-light text-secondary border',
                                            };
                                            ?>
                                            <span class="badge <?= $catBadge ?> border px-2 py-1 small">
                                                <?= esc($h['category']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-secondary small"><?= esc($h['amc_name'] ?: '—') ?></span>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?= number_format($h['active_quantity']) ?>
                                        </td>
                                        <td class="text-end text-muted small">
                                            <?= format_inr($h['avg_buy_price']) ?>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <?= format_inr($h['current_price']) ?>
                                        </td>
                                        <td class="text-end small text-muted">
                                            <?= format_inr($h['invested_value']) ?>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            <?= format_inr($h['current_value']) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($h['unrealized_pnl'], $h['unrealized_pnl_percent']) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info rounded-start-3 px-2 py-1 open-etf-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#etfTransactionModal"
                                                        data-id="<?= $h['id'] ?>"
                                                        data-symbol="<?= esc($h['symbol']) ?>"
                                                        data-name="<?= esc($h['etf_name']) ?>"
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
                                                        <button class="dropdown-item small open-edit-etf-modal"
                                                                data-id="<?= $h['id'] ?>"
                                                                data-symbol="<?= esc($h['symbol']) ?>"
                                                                data-name="<?= esc($h['etf_name']) ?>"
                                                                data-category="<?= esc($h['category']) ?>"
                                                                data-amc="<?= esc($h['amc_name'] ?? '') ?>"
                                                                data-exchange="<?= esc($h['exchange']) ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editEtfModal">
                                                            <i class="bi bi-pencil me-2 text-primary"></i>Edit ETF Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('etfs?etf_id=' . $h['id'] . '&tab=transactions') ?>" onclick="showEtfLedger(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                           href="<?= base_url('etfs/delete/' . $h['id']) ?>" 
                                                           onclick="return confirm('Delete this ETF and all its trade records?');">
                                                            <i class="bi bi-trash me-2"></i>Delete ETF
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
                    $fyRanges          = get_fy_ranges();
                    $ledgerEtfs        = [];
                    $overallBuyAmount  = 0.0;
                    $overallSellAmount = 0.0;
                    $overallBuyQty     = 0;
                    $overallSellQty    = 0;
                    $overallBuyCount   = 0;
                    $overallSellCount  = 0;
                    $overallCharges    = 0.0;

                    foreach ($transactions as $t) {
                        $eId = (int)$t['etf_id'];
                        if (!isset($ledgerEtfs[$eId])) {
                            $ledgerEtfs[$eId] = [
                                'id'       => $eId,
                                'symbol'   => $t['symbol'],
                                'name'     => $t['etf_name'],
                                'exchange' => $t['exchange'] ?? 'NSE',
                                'count'    => 0,
                            ];
                        }
                        $ledgerEtfs[$eId]['count']++;

                        $qty     = (int)$t['quantity'];
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
                    uasort($ledgerEtfs, fn($a, $b) => strcmp($a['symbol'], $b['symbol']));
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
                                            <div class="text-muted" style="font-size: 0.72rem;">Filter by ETF &amp; financial year</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                                        <div style="min-width: 200px; max-width: 270px;" class="flex-grow-1">
                                            <select id="ledgerEtfFilter" class="form-select form-select-sm shadow-none border-secondary-subtle">
                                                <option value="ALL">-- All ETFs (<?= count($transactions) ?> Trades) --</option>
                                                <?php foreach ($ledgerEtfs as $eId => $e): ?>
                                                    <option value="<?= $eId ?>" data-symbol="<?= esc($e['symbol']) ?>">
                                                        <?= esc($e['symbol']) ?> — <?= esc($e['name']) ?> (<?= $e['count'] ?>)
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
                                        <button type="button" id="btnResetEtfFilter" class="btn btn-sm btn-outline-secondary d-none" title="Reset all filters">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                        </button>
                                        <span id="filteredEtfBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5 small d-none">
                                            <i class="bi bi-check2-circle me-1"></i><span id="filteredEtfSymbol"></span>
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
                                                <?= $overallBuyCount ?> buys • <?= number_format($overallBuyQty) ?> units
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
                                                <?= $overallSellCount ?> sells • <?= number_format($overallSellQty) ?> units
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
                                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Charges &amp; Taxes</span>
                                            <h5 class="fw-bold text-dark mb-0 mt-1" id="kpiCharges"><?= format_inr($overallCharges) ?></h5>
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                Brokerage + STT
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
                        <table class="table table-hover align-middle mb-0 small" id="etfLedgerTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Date</th>
                                    <th>ETF Symbol</th>
                                    <th>Action</th>
                                    <th class="text-end">Units</th>
                                    <th class="text-end">Price (₹)</th>
                                    <th class="text-end">Charges (₹)</th>
                                    <th class="text-end">Net Outlay / Proceeds</th>
                                    <th>Notes</th>
                                    <th class="text-center" style="width: 85px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $t): ?>
                                    <tr class="etf-ledger-row"
                                        data-etf-id="<?= $t['etf_id'] ?>"
                                        data-symbol="<?= esc($t['symbol']) ?>"
                                        data-date="<?= $t['transaction_date'] ?>"
                                        data-type="<?= $t['transaction_type'] ?>"
                                        data-qty="<?= (int)$t['quantity'] ?>"
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
                                                    class="btn btn-sm btn-outline-secondary py-0 px-1.5 open-edit-etf-trans"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editEtfTransModal"
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
                                            <a href="<?= base_url('etfs/delete-transaction/' . $t['id']) ?>" 
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
                                        No transactions found matching the selected ETF and date filter.
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
                                    <td class="text-end" id="footerTotalQty"><?= number_format($overallBuyQty + $overallSellQty) ?></td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-end text-muted" id="footerTotalCharges"><?= format_inr($overallCharges) ?></td>
                                    <td class="text-end fw-bold fs-6" id="footerTotalAmount"><?= format_inr($overallNetOutlay) ?></td>
                                    <td class="text-secondary small" id="footerNote"><?= $overallNetOutlay >= 0 ? 'Net Cash Outlay' : 'Net Cash Realized' ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'etfLedger']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 3: FIFO TAX LOG (STCG vs LTCG) -->
            <div class="tab-pane fade p-3" id="capitalgains" role="tabpanel">
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center rounded-3 small">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Indian Income Tax FIFO Matching:</strong> ETF units are redeemed in First-In, First-Out sequence.
                        Holding period <strong>&le; 365 days = STCG</strong>. Holding period <strong>&gt; 365 days = LTCG</strong>.
                    </div>
                </div>

                <?php if (empty($capitalGains)): ?>
                    <p class="text-muted text-center py-4">No realized sales recorded yet. When ETF units are sold, FIFO matched tax lots will appear here.</p>
                <?php else: ?>
                    <?php
                    $fyRanges          = get_fy_ranges();
                    $taxEtfs           = [];
                    $totalMatchedUnits = 0;
                    $totalBuyCost      = 0.0;
                    $totalSellProceeds = 0.0;
                    $totalRealizedGain = 0.0;
                    $totalStcg         = 0.0;
                    $totalLtcg         = 0.0;

                    foreach ($capitalGains as $cg) {
                        $eId = (int)$cg['etf_id'];
                        if (!isset($taxEtfs[$eId])) {
                            $taxEtfs[$eId] = [
                                'id'     => $eId,
                                'symbol' => $cg['symbol'],
                                'name'   => $cg['etf_name'],
                                'count'  => 0,
                            ];
                        }
                        $taxEtfs[$eId]['count']++;
                        $qty      = (int)$cg['quantity_matched'];
                        $cost     = $qty * (float)$cg['buy_price'];
                        $proceeds = $qty * (float)$cg['sell_price'];
                        $gain     = (float)$cg['realized_gain'];

                        $totalMatchedUnits += $qty;
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

                    <!-- Filter Toolbar: ETF Selector + Gain Type + Date Range -->
                    <div class="bg-light p-3 rounded-4 border mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="taxLogEtfFilter" class="form-label small fw-semibold text-secondary mb-1">
                                    <i class="bi bi-filter me-1"></i>Filter by ETF
                                </label>
                                <select class="form-select form-select-sm shadow-sm" id="taxLogEtfFilter">
                                    <option value="ALL">All ETFs (<?= count($capitalGains) ?> lots)</option>
                                    <?php foreach ($taxEtfs as $et): ?>
                                        <option value="<?= $et['id'] ?>" data-symbol="<?= esc($et['symbol']) ?>">
                                            <?= esc($et['symbol']) ?> &mdash; <?= esc($et['name']) ?> (<?= $et['count'] ?> lots)
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
                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Matched Units</div>
                                <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxUnits"><?= number_format($totalMatchedUnits) ?></div>
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
                        <table class="table table-hover align-middle mb-0 small" id="etfTaxLogTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>ETF</th>
                                    <th>Buy Date</th>
                                    <th>Sell Date</th>
                                    <th class="text-center">Days Held</th>
                                    <th class="text-center">Tax Category</th>
                                    <th class="text-end">Matched Units</th>
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
                                    <tr class="etf-taxlog-row"
                                        data-etf-id="<?= $cg['etf_id'] ?>"
                                        data-symbol="<?= esc($cg['symbol']) ?>"
                                        data-gain-type="<?= esc($cg['gain_type']) ?>"
                                        data-date="<?= $cg['sell_date'] ?>"
                                        data-qty="<?= $qty ?>"
                                        data-cost="<?= $cost ?>"
                                        data-proceeds="<?= $proceeds ?>"
                                        data-gain="<?= $gain ?>">
                                        <td><strong><?= esc($cg['symbol']) ?></strong> (<?= esc($cg['etf_name']) ?>)</td>
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
                            <tfoot class="table-group-divider bg-light fw-semibold" id="etfTaxFooter">
                                <tr>
                                    <td colspan="5" class="py-3">
                                        <span class="fw-bold text-dark" id="footerTaxSummaryTitle">Total (<?= count($capitalGains) ?> Lots)</span>
                                        <span class="ms-2 badge bg-light text-secondary border fw-normal" id="footerTaxBreakdown">
                                            All FIFO lots
                                        </span>
                                    </td>
                                    <td class="text-end py-3 fw-bold" id="footerTaxQty"><?= number_format($totalMatchedUnits) ?></td>
                                    <td class="text-end py-3 text-muted" id="footerTaxCost"><?= format_inr($totalBuyCost) ?></td>
                                    <td class="text-end py-3 text-muted" id="footerTaxProceeds"><?= format_inr($totalSellProceeds) ?></td>
                                    <td class="text-end py-3 fs-6 fw-bold <?= $totalRealizedGain >= 0 ? 'text-success' : 'text-danger' ?>" id="footerTaxGain">
                                        <?= ($totalRealizedGain >= 0 ? '+' : '') . format_inr($totalRealizedGain) ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'etfTax']) ?>
                <?php endif; ?>
            </div>

            <!-- TAB 4: ETF SPLITS & CORPORATE ACTIONS LOG -->
            <div class="tab-pane fade p-3" id="corporate-actions" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart-fill text-info me-2"></i>ETF Unit Splits & Corporate Actions</h6>
                        <small class="text-muted">History of unit splits and lot ratio adjustments</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#etfSplitModal">
                            <i class="bi bi-plus-circle me-1"></i>Record ETF Split
                        </button>
                    </div>
                </div>

                <?php if (empty($corporateActions)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-pie-chart fs-1 d-block mb-3 text-secondary"></i>
                        <h6>No ETF Splits Recorded Yet</h6>
                        <p class="small text-muted mb-3">When an ETF executes a unit split, record it here to rebalance your holdings and tax lots.</p>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#etfSplitModal">
                            <i class="bi bi-plus-lg me-1"></i>Record ETF Split
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="etfCorporateActionsTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Record Date</th>
                                    <th>ETF</th>
                                    <th>Action Type</th>
                                    <th class="text-center">Split Ratio</th>
                                    <th class="text-end">Units Before</th>
                                    <th class="text-end">Units After</th>
                                    <th>Notes</th>
                                    <th>Recorded At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($corporateActions as $ca): ?>
                                    <tr class="ca-etf-row">
                                        <td class="fw-semibold text-dark"><?= date('d-M-Y', strtotime($ca['record_date'])) ?></td>
                                        <td>
                                            <strong class="text-info"><?= esc($ca['security_symbol']) ?></strong>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($ca['security_name']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                                <?= esc($ca['action_type']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center fw-semibold"><?= (float)$ca['ratio_new'] ?> : <?= (float)$ca['ratio_old'] ?></td>
                                        <td class="text-end fw-semibold text-muted"><?= number_format($ca['shares_before'], 2) ?></td>
                                        <td class="text-end fw-bold text-dark"><?= number_format($ca['shares_after'], 2) ?></td>
                                        <td class="text-secondary"><?= esc($ca['notes'] ?: '—') ?></td>
                                        <td class="text-muted small"><?= date('d-M-Y H:i', strtotime($ca['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'etfCA']) ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MINIMAL ETF TRANSACTION POPUP MODAL (Buy More / Sell FIFO)                -->
<!-- ========================================================================= -->
<div class="modal fade" id="etfTransactionModal" tabindex="-1" aria-labelledby="etfTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="etfTransactionModalLabel">
                        Add Transaction: <span id="modalEtfSymbol" class="text-info"></span>
                    </h5>
                    <div class="text-muted small" id="modalEtfSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('etfs/transaction') ?>" method="POST" id="etfTransForm">
                <?= csrf_field() ?>
                <input type="hidden" name="etf_id" id="modalEtfId" value="">

                <div class="modal-body p-4">
                    <!-- Segmented Action Tabs -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actEtfBuy" value="BUY" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actEtfBuy">
                            <i class="bi bi-cart-plus me-1"></i>Buy More
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actEtfSell" value="SELL" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actEtfSell">
                            <i class="bi bi-cart-dash me-1"></i>Sell (FIFO)
                        </label>
                    </div>

                    <!-- SUB-FORM 1: BUY MORE -->
                    <div id="panelEtfBuy" class="etf-trans-panel">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="buyEtfDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Units <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="buyEtfQty" min="1" step="1" placeholder="Units" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Buy Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="buyEtfPrice" min="0.01" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Brokerage + Taxes (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="buyEtfCharges" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Monthly SIP accumulation">
                            </div>
                        </div>

                        <!-- Buy Outlay Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Total Outlay:</span>
                                <span class="fw-bold text-success" id="buyEtfTotalPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-FORM 2: SELL (FIFO) -->
                    <div id="panelEtfSell" class="etf-trans-panel d-none">
                        <div class="alert alert-warning py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Available to Sell:</span>
                            <strong id="sellEtfAvailableQtyBadge">0 units</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Sale Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="sellEtfDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Units to Sell <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="sellEtfQty" min="1" step="1" placeholder="Units" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Sell Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="sellEtfPrice" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Brokerage + STT (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="sellEtfCharges" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Asset rebalancing" disabled>
                            </div>
                        </div>

                        <!-- Sell Proceeds Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Estimated Net Proceeds:</span>
                                <span class="fw-bold text-dark" id="sellEtfTotalPreview">₹ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Realized P&L:</span>
                                <span class="small fw-semibold" id="sellEtfPnlPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitEtfTrans">
                        Confirm Purchase Lot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT ETF METADATA MODAL                                                   -->
<!-- ========================================================================= -->
<div class="modal fade" id="editEtfModal" tabindex="-1" aria-labelledby="editEtfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editEtfModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit ETF Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('etfs/update-etf') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="etf_id" id="editEtfId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Trading Symbol <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold text-uppercase" name="symbol" id="editEtfSymbol" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Exchange <span class="text-danger">*</span></label>
                            <select class="form-select" name="exchange" id="editEtfExchange" required>
                                <option value="NSE">NSE</option>
                                <option value="BSE">BSE</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">ETF Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="etf_name" id="editEtfName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Asset Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" id="editEtfCategory" required>
                                <option value="Index">Index</option>
                                <option value="Commodity - Gold">Commodity - Gold</option>
                                <option value="Commodity - Silver">Commodity - Silver</option>
                                <option value="Sectoral">Sectoral</option>
                                <option value="Global">Global</option>
                                <option value="Debt">Debt</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">AMC / Issuer</label>
                            <input type="text" class="form-control" name="amc_name" id="editEtfAmc" placeholder="e.g. Nippon India, SBI Mutual Fund">
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
<!-- MODAL: RECORD ETF UNIT SPLIT                                              -->
<!-- ========================================================================= -->
<div class="modal fade" id="etfSplitModal" tabindex="-1" aria-labelledby="etfSplitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= base_url('etfs/split') ?>" method="POST" id="etfSplitForm">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark" id="etfSplitModalLabel">
                        <i class="bi bi-pie-chart-fill text-info me-2"></i>Record ETF Unit Split
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="split_etf_id" class="form-label small fw-semibold text-secondary">Select ETF <span class="text-danger">*</span></label>
                        <select class="form-select" id="split_etf_id" name="etf_id" required>
                            <option value="">-- Choose ETF --</option>
                            <?php foreach ($holdings as $h): ?>
                                <option value="<?= $h['id'] ?>" data-symbol="<?= esc($h['symbol']) ?>">
                                    <?= esc($h['symbol']) ?> — <?= esc($h['etf_name'] ?? $h['name'] ?? '') ?> (<?= number_format((float)($h['active_quantity'] ?? $h['total_quantity'] ?? 0)) ?> units)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="split_record_date" class="form-label small fw-semibold text-secondary">Record Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="split_record_date" name="record_date" value="<?= date('Y-m-d') ?>" required>
                        <div class="mt-1 d-flex align-items-center gap-2">
                            <span class="badge bg-light text-secondary border small" id="split_eligible_badge">
                                <i class="bi bi-info-circle me-1"></i>Select ETF to check eligible units on record date
                            </span>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">New Units <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="split_ratio_new" name="ratio_new" value="2" required>
                            <div class="form-text small">Units received (e.g. 2, 5, 10)</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">For Existing Units <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="split_ratio_old" name="ratio_old" value="1" required>
                            <div class="form-text small">Units held (e.g. 1)</div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="split_notes" class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                        <textarea class="form-control" id="split_notes" name="notes" rows="2" placeholder="e.g. 1:5 Split announced by AMC"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i>Execute ETF Split
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT ETF TRANSACTION MODAL (WITH FIFO REBUILD)                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="editEtfTransModal" tabindex="-1" aria-labelledby="editEtfTransModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editEtfTransModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i><span id="editEtfTransTitle">Edit Transaction</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('etfs/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editEtfTransId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Saving changes will automatically <strong>rebalance FIFO lots and recalculate capital gains</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editEtfTransDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quantity (Units) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="quantity" id="editEtfTransQty" min="1" step="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Price per Unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="editEtfTransPrice" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                            <input type="number" class="form-control" name="brokerage" id="editEtfTransBrokerage" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STT / Taxes (₹)</label>
                            <input type="number" class="form-control" name="stt_taxes" id="editEtfTransStt" min="0" step="0.01">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editEtfTransNotes">
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
    window.ETFS_CONFIG = {
        fyRanges: <?= json_encode(get_fy_ranges()) ?>,
        checkSplitUrl: '<?= base_url('etfs/check-split-eligibility') ?>'
    };
</script>
<script src="<?= base_url('assets/js/etfs.js') ?>"></script>

<?= $this->endSection() ?>

