<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-percent text-primary me-2"></i>Capital Gains &amp; Tax Audit Report
            </h3>
            <p class="text-secondary small mb-0">
                Audited Long-Term (LTCG) and Short-Term (STCG) capital gains realized across all investment modules under Indian Income Tax Act.
            </p>
        </div>
    </div>

    <!-- Multipage Sub-Navigation -->
    <?= $this->include('reports/_nav') ?>

    <!-- Date Range Filter Toolbar -->
    <?= $this->include('reports/_date_filter') ?>

    <!-- 4 Hero Tax KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Realized Capital Gains -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-<?= $totals['total_gain'] >= 0 ? 'success' : 'danger' ?> border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Realized Capital Gains</div>
                        <h4 class="fw-bold mb-0 mt-1 <?= $totals['total_gain'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= ($totals['total_gain'] >= 0 ? '+' : '') . format_inr($totals['total_gain']) ?>
                        </h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Net realized profits across all modules</div>
                    </div>
                    <div class="bg-<?= $totals['total_gain'] >= 0 ? 'success' : 'danger' ?>-subtle text-<?= $totals['total_gain'] >= 0 ? 'success' : 'danger' ?> rounded-3 p-2">
                        <i class="bi bi-trophy fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. STCG -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-warning border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Short-Term Gains (STCG)</div>
                        <h4 class="fw-bold text-dark mb-0 mt-1"><?= format_inr($totals['stcg']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Holding &le; 12 months &bull; Sec 111A / Slab</div>
                    </div>
                    <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-2">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. LTCG -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-primary border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Long-Term Gains (LTCG)</div>
                        <h4 class="fw-bold text-primary mb-0 mt-1"><?= format_inr($totals['ltcg']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Holding &gt; 12 months &bull; Sec 112A (12.5%)</div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2">
                        <i class="bi bi-calendar-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Tax-Exempt Gains -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-info border-4 p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">100% Tax-Exempt Gains</div>
                        <h4 class="fw-bold text-info-emphasis mb-0 mt-1"><?= format_inr($totals['exempt']) ?></h4>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">SGB Maturity u/s 47(viic) &bull; Zero Tax</div>
                    </div>
                    <div class="bg-info-subtle text-info-emphasis rounded-3 p-2">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module-wise Capital Gains Summary Table -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Module-wise Capital Gains (LTCG &amp; STCG)</h5>
                <small class="text-muted">FIFO matched tax audit across all asset classes for <?= esc($range['label']) ?></small>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill">
                <?= number_format($totals['lots']) ?> Matched Lots
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th class="ps-4">Asset Class / Module</th>
                        <th>Tax Treatment</th>
                        <th class="text-end">STCG (&le;1 yr)</th>
                        <th class="text-end">LTCG (&gt;1 yr)</th>
                        <th class="text-end">Exempt Gains</th>
                        <th class="text-end">Total Realized Gain</th>
                        <th class="text-end">Sales Value (₹)</th>
                        <th class="text-end">Cost Basis (₹)</th>
                        <th class="text-center">Lots</th>
                        <th class="text-center pe-4" style="width: 110px;">Tax Log</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $m): ?>
                        <tr>
                            <!-- Module -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="bg-<?= esc($m['color']) ?>-subtle text-<?= esc($m['color']) ?> rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="<?= esc($m['icon']) ?> fs-5"></i>
                                    </div>
                                    <div class="fw-bold text-dark fs-6"><?= esc($m['name']) ?></div>
                                </div>
                            </td>

                            <!-- Tax Rule Note -->
                            <td>
                                <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.68rem;">
                                    <?= esc($m['tax_rule']) ?>
                                </span>
                            </td>

                            <!-- STCG -->
                            <td class="text-end fw-semibold <?= $m['stcg'] > 0 ? 'text-dark' : 'text-muted' ?>">
                                <?= $m['stcg'] != 0 ? format_inr($m['stcg']) : '₹0.00' ?>
                            </td>

                            <!-- LTCG -->
                            <td class="text-end fw-semibold <?= $m['ltcg'] > 0 ? 'text-primary' : 'text-muted' ?>">
                                <?= $m['ltcg'] != 0 ? format_inr($m['ltcg']) : '₹0.00' ?>
                            </td>

                            <!-- Exempt Gains -->
                            <td class="text-end">
                                <?php if ($m['exempt'] > 0): ?>
                                    <span class="badge bg-info-subtle text-info-emphasis px-2 py-1 font-monospace">
                                        <?= format_inr($m['exempt']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- Total Realized Gain -->
                            <td class="text-end fw-bold <?= $m['total_gain'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= ($m['total_gain'] >= 0 ? '+' : '') . format_inr($m['total_gain']) ?>
                            </td>

                            <!-- Matched Sales Value -->
                            <td class="text-end text-secondary">
                                <?= format_inr($m['sales']) ?>
                            </td>

                            <!-- Matched Cost Basis -->
                            <td class="text-end text-secondary">
                                <?= format_inr($m['cost']) ?>
                            </td>

                            <!-- Lots Count -->
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">
                                    <?= number_format($m['lots']) ?>
                                </span>
                            </td>

                            <!-- Action / Link -->
                            <td class="text-center pe-4">
                                <?php if ($m['lots'] > 0): ?>
                                    <a href="<?= esc($m['log_url']) ?>" class="btn btn-light btn-sm border px-2.5 py-1 text-primary shadow-sm" title="View module FIFO audit log">
                                        <i class="bi bi-search me-1"></i>Audit
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-bold border-top-2" style="font-size: 0.85rem;">
                    <tr>
                        <td class="ps-4" colspan="2">CONSOLIDATED CAPITAL GAINS TOTAL</td>
                        <td class="text-end text-dark"><?= format_inr($totals['stcg']) ?></td>
                        <td class="text-end text-primary"><?= format_inr($totals['ltcg']) ?></td>
                        <td class="text-end text-info-emphasis"><?= format_inr($totals['exempt']) ?></td>
                        <td class="text-end <?= $totals['total_gain'] >= 0 ? 'text-success' : 'text-danger' ?> fs-6">
                            <?= ($totals['total_gain'] >= 0 ? '+' : '') . format_inr($totals['total_gain']) ?>
                        </td>
                        <td class="text-end text-secondary"><?= format_inr($totals['sales']) ?></td>
                        <td class="text-end text-secondary"><?= format_inr($totals['cost']) ?></td>
                        <td class="text-center"><?= number_format($totals['lots']) ?></td>
                        <td class="pe-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Estimated Indian Tax Computation Reference Card (Post Budget 2024) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-calculator text-primary me-2"></i>Estimated Capital Gains Tax Liability (Budget 2024 Provisions)
                </h6>
                <small class="text-muted">Provisional estimation under Section 111A (STCG 20%) &amp; Section 112A (LTCG 12.5% above ₹1.25L)</small>
            </div>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1">
                FY 2024-25 onwards
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Left: Tax Rules Description -->
                <div class="col-lg-6">
                    <h6 class="fw-bold text-dark mb-3">Statutory Indian Tax Provisions Applied:</h6>
                    <ul class="list-unstyled small text-secondary mb-0">
                        <li class="mb-2.5 d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-0.5"></i>
                            <div>
                                <strong>Sec 111A Short-Term Capital Gains (STCG):</strong>
                                Taxed at a flat rate of <strong>20%</strong> for listed equities, equity ETFs, and business trusts (REITs/InvITs) sold after 23-Jul-2024.
                            </div>
                        </li>
                        <li class="mb-2.5 d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-0.5"></i>
                            <div>
                                <strong>Sec 112A Long-Term Capital Gains (LTCG):</strong>
                                Eligible long-term gains across Equities, ETFs, and REITs enjoy an annual aggregate exemption of up to <strong>₹1,25,000</strong>. Gains exceeding this threshold are taxed at <strong>12.5%</strong> without indexation benefit.
                            </div>
                        </li>
                        <li class="mb-2.5 d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-0.5"></i>
                            <div>
                                <strong>Sec 47(viic) Sovereign Gold Bonds (SGB):</strong>
                                Capital gains arising on maturity redemption of SGBs by an individual are <strong>100% Tax-Free</strong>.
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-info mt-0.5"></i>
                            <div>
                                <strong>Sec 10(12A) National Pension System (NPS Tier 1):</strong>
                                Accumulated returns are tax-deferred; 60% of total corpus withdrawn at maturity is entirely tax-exempt.
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Right: Computed Estimated Tax Breakdown -->
                <div class="col-lg-6">
                    <div class="bg-light p-3.5 rounded-4 border">
                        <h6 class="fw-bold text-dark mb-3">Provisional Tax Liability Summary (<?= esc($range['shortLabel']) ?>):</h6>
                        
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom small">
                            <span class="text-secondary">Applicable STCG (Sec 111A)</span>
                            <span class="fw-semibold text-dark"><?= format_inr($estTax['equity_stcg']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom small">
                            <span class="text-secondary">Estimated STCG Tax @ 20%</span>
                            <span class="fw-bold text-danger"><?= format_inr($estTax['est_tax_stcg']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom small">
                            <span class="text-secondary">Applicable LTCG (Sec 112A)</span>
                            <span class="fw-semibold text-dark"><?= format_inr($estTax['equity_ltcg']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom small">
                            <span class="text-secondary">Less: Annual Exemption Limit (Sec 112A)</span>
                            <span class="text-success">&minus;<?= format_inr(min($estTax['equity_ltcg'], $estTax['exemption_limit'])) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom small">
                            <span class="text-secondary">Taxable LTCG (Subject to 12.5%)</span>
                            <span class="fw-semibold text-dark"><?= format_inr($estTax['taxable_ltcg']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom small">
                            <span class="text-secondary">Estimated LTCG Tax @ 12.5%</span>
                            <span class="fw-bold text-danger"><?= format_inr($estTax['est_tax_ltcg']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom small">
                            <span class="text-secondary">Health &amp; Education Cess @ 4%</span>
                            <span class="text-danger">+<?= format_inr($estTax['cess_4_percent']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 fw-bold text-dark fs-6">
                            <span>Total Estimated Tax Payable</span>
                            <span class="text-danger"><?= format_inr($estTax['final_tax_payble']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

