            <div class="tab-pane fade show active p-3" id="schemes" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th>Scheme Name</th>
                                <th class="text-center">Target Split</th>
                                <th class="text-end">Units Held (Net)</th>
                                <th class="text-end">Avg Cost NAV (₹)</th>
                                <th class="text-end">Latest NAV (₹)</th>
                                <th class="text-end">Invested (₹)</th>
                                <th class="text-end">Valuation (₹)</th>
                                <th class="text-end">Return / Gain</th>
                                <th class="text-end">Fee Units Deducted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schemes as $s): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge <?= $s['badge'] ?> border px-2 py-1 me-2 small">
                                                <?= esc($s['code']) ?>
                                            </span>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0">
                                                    <?= esc($s['name']) ?>
                                                    <?php if (!empty($s['scheme_code'])): ?>
                                                        <span class="badge bg-light text-secondary border font-monospace ms-1" style="font-size: 0.68rem;" title="npsnav.in Scheme Code">
                                                            <i class="bi bi-upc-scan me-1 text-primary"></i><?= esc($s['scheme_code']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($s['nav_date'])): ?>
                                                    <small class="text-muted" style="font-size: 0.72rem;">
                                                        NAV date: <?= date('d-M-Y', strtotime($s['nav_date'])) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                            <?= $s['target_pct'] ?>%
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">
                                        <?= number_format($s['active_units'], 4) ?>
                                    </td>
                                    <td class="text-end text-muted small">
                                        <?= format_inr($s['avg_nav']) ?>
                                    </td>
                                    <td class="text-end fw-semibold text-dark">
                                        <?= format_inr($s['current_nav']) ?>
                                    </td>
                                    <td class="text-end small text-muted">
                                        <?= format_inr($s['invested_amount']) ?>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        <?= format_inr($s['current_value']) ?>
                                    </td>
                                    <td class="text-end">
                                        <?= format_pnl($s['unrealized_pnl'], $s['return_pct']) ?>
                                    </td>
                                    <td class="text-end text-secondary small">
                                        <?php if ($s['deducted_units'] > 0): ?>
                                            <span class="text-danger">-<?= number_format($s['deducted_units'], 4) ?></span>
                                            <div class="text-muted" style="font-size: 0.7rem;">&approx; <?= format_inr($s['deducted_value']) ?></div>
                                        <?php else: ?>
                                            0.0000
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

