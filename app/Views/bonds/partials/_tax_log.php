            <div class="tab-pane fade p-3" id="capitalgains" role="tabpanel">
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center rounded-3 small">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Bond Capital Gains &amp; FIFO Matching:</strong>
                        Secondary market sales and maturity redemptions are matched in First-In, First-Out order (Holding &gt; 365 days = LTCG, &le; 365 days = STCG).
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
                        if ($cg['gain_type'] === 'LTCG' || $cg['gain_type'] === 'EXEMPT_SGB_MATURITY') {
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
                                    <i class="bi bi-tags me-1"></i>Gain Type
                                </label>
                                <select class="form-select form-select-sm shadow-sm" id="taxLogTypeFilter">
                                    <option value="ALL">All Categories</option>
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
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Matched Units</div>
                                        <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxUnits"><?= number_format($totalMatchedUnits, 4) ?></div>
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
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Purchase Cost</div>
                                        <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxCost"><?= format_inr($totalBuyCost) ?></div>
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
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Exit Proceeds</div>
                                        <div class="fs-5 fw-bold text-dark mb-0 mt-1" id="kpiTaxProceeds"><?= format_inr($totalExitProceeds) ?></div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">Gross redemption / sale</div>
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
                                            Exempt: <?= format_inr($totalExempt) ?> &bull; LTCG: <?= format_inr($totalLtcg) ?> &bull; STCG: <?= format_inr($totalStcg) ?>
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
                                            <?php if ($cg['gain_type'] === 'LTCG' || $cg['gain_type'] === 'EXEMPT_SGB_MATURITY'): ?>
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

