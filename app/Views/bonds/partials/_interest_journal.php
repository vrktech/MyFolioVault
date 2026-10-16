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

