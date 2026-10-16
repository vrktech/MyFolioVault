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
