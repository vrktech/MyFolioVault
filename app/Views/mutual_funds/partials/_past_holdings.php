            <div class="tab-pane fade p-3" id="past-holdings" role="tabpanel">
                <?php if (empty($pastHoldings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-archive fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Past Mutual Fund Holdings Found</h5>
                        <p class="text-muted small">You don't have any fully redeemed mutual fund schemes yet. When you redeem 100% of your units in a scheme, it will appear here along with your lifetime realized profit/loss.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($pastHoldings) ?></strong> past / closed schemes
                        </div>
                        <div class="text-muted small">
                            <span class="badge bg-secondary-subtle text-secondary border"><i class="bi bi-info-circle me-1"></i>0 Active Units &bull; Lifetime Closed Trades</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="mfPastHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Scheme / AMFI Code</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Latest NAV (₹)</th>
                                    <th class="text-end">Realized P&L</th>
                                    <th class="text-center" style="min-width: 160px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pastHoldings as $ph): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <div class="brand-badge-sm me-2 bg-secondary bg-opacity-10 text-secondary rounded px-2 py-1 small fw-bold">
                                                    <?= esc($ph['amfi_code']) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark mb-0"><?= esc($ph['scheme_name']) ?></div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                                        <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                            <?= esc($ph['category']) ?>
                                                        </span>
                                                        <?php if (!empty($ph['folio_number'])): ?>
                                                            <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                                Folio: <?= esc($ph['folio_number']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($ph['nav_date'])): ?>
                                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                                NAV as of <?= date('d-M-Y', strtotime($ph['nav_date'])) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                <i class="bi bi-check2-all me-1"></i>Closed
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <?= format_inr($ph['current_nav']) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($ph['realized_pnl']) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success rounded-start-3 px-2 py-1 open-mf-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#mfTransactionModal"
                                                        data-id="<?= $ph['id'] ?>"
                                                        data-code="<?= esc($ph['amfi_code']) ?>"
                                                        data-name="<?= esc($ph['scheme_name']) ?>"
                                                        data-folio="<?= esc($ph['folio_number']) ?>"
                                                        data-units="0"
                                                        data-nav="<?= $ph['current_nav'] ?>"
                                                        data-avg="0">
                                                    <i class="bi bi-plus-circle me-1"></i>Invest Again
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split rounded-end-3" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <button class="dropdown-item small open-edit-fund-modal"
                                                                data-id="<?= $ph['id'] ?>"
                                                                data-name="<?= esc($ph['scheme_name']) ?>"
                                                                data-amfi="<?= esc($ph['amfi_code']) ?>"
                                                                data-folio="<?= esc($ph['folio_number']) ?>"
                                                                data-category="<?= esc($ph['category']) ?>"
                                                                data-amc="<?= esc($ph['amc_name'] ?? '') ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editFundModal">
                                                            <i class="bi bi-pencil me-2 text-primary"></i>Edit Scheme Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('mutual-funds?mf_id=' . $ph['id'] . '&tab=transactions') ?>" onclick="showMfLedger(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-info" href="<?= base_url('mutual-funds?tax_mf_id=' . $ph['id'] . '&tab=capitalgains') ?>">
                                                            <i class="bi bi-receipt-cutoff me-2"></i>FIFO Tax Lots
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

