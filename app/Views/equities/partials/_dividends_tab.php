            <div class="tab-pane fade p-3" id="dividends" role="tabpanel">
                <?php if (empty($dividends)): ?>
                    <p class="text-muted text-center py-4">No dividend credits recorded yet.</p>
                <?php else: ?>
                    <?php
                    $fyRanges           = get_fy_ranges();
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
                                        <td><?= stock_badge($d['symbol']) ?></td>
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

