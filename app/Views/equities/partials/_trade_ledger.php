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
                                            <?= stock_badge($t['symbol']) ?>
                                            <span class="text-muted small ms-1">(<?= esc($t['exchange']) ?>)</span>
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

