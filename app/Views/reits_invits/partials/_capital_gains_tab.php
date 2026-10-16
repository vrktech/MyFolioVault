            <div class="tab-pane fade p-3" id="capitalgains" role="tabpanel">
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center rounded-3 small">
                    <i class="bi bi-shield-check me-2 fs-5"></i>
                    <div>
                        <strong>Capital Gains on Business Trusts (REITs &amp; InvITs):</strong>
                        Units sold on stock exchange with STT paid are matched via First-In, First-Out (FIFO).
                        Holding &gt; 12 months = <strong>LTCG</strong>;
                        Holding &le; 12 months = <strong>STCG</strong>.
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
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Matched Units</div>
                                        <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxUnits"><?= number_format($totalMatchedUnits, 0) ?></div>
                                        <div class="text-muted small" style="font-size: 0.72rem;" id="kpiTaxQtyCount"><?= count($capitalGains) ?> FIFO lots</div>
                                    </div>
                                    <div class="bg-primary-subtle text-primary rounded-3 p-2 flex-shrink-0">
                                        <i class="bi bi-layers fs-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-secondary border-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Cost Basis</div>
                                        <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxCost"><?= format_inr($totalCostBasis) ?></div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">Original purchase cost</div>
                                    </div>
                                    <div class="bg-secondary-subtle text-secondary rounded-3 p-2 flex-shrink-0">
                                        <i class="bi bi-cash-coin fs-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-info border-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Net Proceeds</div>
                                        <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxProceeds"><?= format_inr($totalNetProceeds) ?></div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">Gross exit value</div>
                                    </div>
                                    <div class="bg-info-subtle text-info-emphasis rounded-3 p-2 flex-shrink-0">
                                        <i class="bi bi-box-arrow-up-right fs-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-<?= $totalRealizedGain >= 0 ? 'success' : 'danger' ?> border-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="me-2">
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Net Realized P&amp;L</div>
                                        <div class="fs-5 fw-bold <?= $totalRealizedGain >= 0 ? 'text-success' : 'text-danger' ?> mb-0 mt-1" id="kpiTaxGain">
                                            <?= ($totalRealizedGain >= 0 ? '+' : '') . format_inr($totalRealizedGain) ?>
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.72rem;" id="kpiTaxBreakdown">
                                            STCG: <?= format_inr($totalStcg) ?> &bull; LTCG: <?= format_inr($totalLtcg) ?>
                                        </div>
                                    </div>
                                    <div class="bg-<?= $totalRealizedGain >= 0 ? 'success' : 'danger' ?>-subtle text-<?= $totalRealizedGain >= 0 ? 'success' : 'danger' ?> rounded-3 p-2 flex-shrink-0">
                                        <i class="bi bi-trophy fs-5"></i>
                                    </div>
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
                                            <?= stock_badge($cg['symbol']) ?>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($cg['trust_name']) ?></div>
                                        </td>
                                        <td class="text-end fw-semibold"><?= number_format($qMatched, 0) ?></td>
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

