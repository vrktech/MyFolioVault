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
                                            <?= stock_badge($d['symbol']) ?>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($d['trust_name']) ?></div>
                                        </td>
                                        <td>
                                            <div><?= esc($d['quarter_description'] ?: '—') ?></div>
                                            <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">FY <?= esc($d['financial_year']) ?></span>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?= !empty($d['eligible_units']) && (float)$d['eligible_units'] > 0 ? number_format($d['eligible_units'], 0) : '—' ?>
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

