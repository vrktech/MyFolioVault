            <div class="tab-pane fade p-3" id="corporate-actions" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0"><i class="bi bi-diagram-3 text-primary me-2"></i>Corporate Actions Audit Log</h6>
                        <small class="text-muted">History of stock splits, bonus allotments, rights issues, mergers, and demergers</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#corporateActionModal">
                            <i class="bi bi-plus-circle me-1"></i>Record Corporate Action
                        </button>
                    </div>
                </div>

                <?php if (empty($corporateActions)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-diagram-3 fs-1 d-block mb-3 text-secondary"></i>
                        <h6>No Corporate Actions Recorded Yet</h6>
                        <p class="small text-muted mb-3">When a stock announces a split, bonus shares, rights issue, or merger/demerger, record it here.</p>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#corporateActionModal">
                            <i class="bi bi-plus-lg me-1"></i>Record Corporate Action
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="equityCorporateActionsTable">
                            <thead class="table-light text-muted text-uppercase" style="font-size: 0.72rem;">
                                <tr>
                                    <th>Record Date</th>
                                    <th>Stock</th>
                                    <th>Action Type</th>
                                    <th class="text-center">Ratio / Details</th>
                                    <th class="text-end">Shares Before</th>
                                    <th class="text-end">Shares After</th>
                                    <th>Target / Apportionment</th>
                                    <th>Notes</th>
                                    <th>Recorded At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($corporateActions as $ca): ?>
                                    <tr class="ca-row">
                                        <td class="fw-semibold text-dark"><?= date('d-M-Y', strtotime($ca['record_date'])) ?></td>
                                        <td>
                                            <strong class="text-primary"><?= esc($ca['security_symbol']) ?></strong>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($ca['security_name']) ?></div>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = match($ca['action_type']) {
                                                'SPLIT' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                                'BONUS' => 'bg-success-subtle text-success border border-success-subtle',
                                                'RIGHTS' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                                'MERGER' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                'DEMERGER' => 'bg-secondary-subtle text-dark border',
                                                default => 'bg-light text-dark border'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?> px-2 py-1"><?= esc($ca['action_type']) ?></span>
                                        </td>
                                        <td class="text-center fw-semibold">
                                            <?php if ($ca['action_type'] === 'RIGHTS'): ?>
                                                Price: <?= format_inr($ca['offer_price'] ?? 0) ?>
                                            <?php else: ?>
                                                <?= (float)$ca['ratio_new'] ?> : <?= (float)$ca['ratio_old'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-semibold text-muted"><?= number_format($ca['shares_before'], 2) ?></td>
                                        <td class="text-end fw-bold text-dark"><?= number_format($ca['shares_after'], 2) ?></td>
                                        <td>
                                            <?php if (!empty($ca['target_security_symbol'])): ?>
                                                <span class="badge bg-light text-dark border">
                                                    &rarr; <?= esc($ca['target_security_symbol']) ?>
                                                    <?= !empty($ca['cost_apportionment_ratio']) ? ' (' . (float)$ca['cost_apportionment_ratio'] . '%)' : '' ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-secondary"><?= esc($ca['notes'] ?: '—') ?></td>
                                        <td class="text-muted small"><?= date('d-M-Y H:i', strtotime($ca['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= view('partials/pagination_bar', ['idPrefix' => 'equityCA']) ?>
                <?php endif; ?>
            </div>
