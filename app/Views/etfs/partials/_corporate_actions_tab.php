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
