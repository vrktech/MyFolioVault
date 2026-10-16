            <div class="tab-pane fade show active p-3" id="holdings" role="tabpanel">
                <?php if (empty($activeHoldings ?? $holdings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-briefcase fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Active Mutual Fund Schemes Found</h5>
                        <p class="text-muted small">You don't have any active mutual fund schemes currently. Click below to add your first scheme!</p>
                        <a href="<?= base_url('mutual-funds/new') ?>" class="btn btn-primary btn-sm rounded-3">
                            <i class="bi bi-plus-lg me-1"></i>Add New Scheme
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Scheme / AMFI Code</th>
                                    <th class="text-end">Units Held</th>
                                    <th class="text-end">Avg NAV (₹)</th>
                                    <th class="text-end">Latest NAV (₹)</th>
                                    <th class="text-end">Invested (₹)</th>
                                    <th class="text-end">Current (₹)</th>
                                    <th class="text-end">Unrealized P&L</th>
                                    <th class="text-center" style="min-width: 160px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (($activeHoldings ?? $holdings) as $h): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <div class="brand-badge-sm me-2 bg-success bg-opacity-10 text-success rounded px-2 py-1 small fw-bold">
                                                    <?= esc($h['amfi_code']) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark mb-0"><?= esc($h['scheme_name']) ?></div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                            <?= esc($h['category']) ?>
                                                        </span>
                                                        <?php if (!empty($h['folio_number'])): ?>
                                                            <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                                Folio: <?= esc($h['folio_number']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($h['nav_date'])): ?>
                                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                                NAV as of <?= date('d-M-Y', strtotime($h['nav_date'])) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?= number_format($h['active_units'], 4) ?>
                                        </td>
                                        <td class="text-end text-muted small">
                                            <?= format_inr($h['avg_purchase_nav']) ?>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <?= format_inr($h['current_nav']) ?>
                                        </td>
                                        <td class="text-end small text-muted">
                                            <?= format_inr($h['invested_value']) ?>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            <?= format_inr($h['current_value']) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($h['unrealized_pnl'], $h['unrealized_pnl_percent'], false, true) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success rounded-start-3 px-2 py-1 open-mf-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#mfTransactionModal"
                                                        data-id="<?= $h['id'] ?>"
                                                        data-code="<?= esc($h['amfi_code']) ?>"
                                                        data-name="<?= esc($h['scheme_name']) ?>"
                                                        data-folio="<?= esc($h['folio_number']) ?>"
                                                        data-units="<?= $h['active_units'] ?>"
                                                        data-nav="<?= $h['current_nav'] ?>"
                                                        data-avg="<?= $h['avg_purchase_nav'] ?>">
                                                    <i class="bi bi-lightning-charge-fill me-1"></i>Add Trans
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split rounded-end-3" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <button class="dropdown-item small open-edit-mf-modal"
                                                                data-id="<?= $h['id'] ?>"
                                                                data-name="<?= esc($h['scheme_name']) ?>"
                                                                data-category="<?= esc($h['category']) ?>"
                                                                data-folio="<?= esc($h['folio_number'] ?? '') ?>"
                                                                data-amc="<?= esc($h['fund_house'] ?? '') ?>"
                                                                data-amfi="<?= esc($h['amfi_code'] ?? '') ?>"
                                                                data-isin="<?= esc($h['isin'] ?? '') ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editMfModal">
                                                            <i class="bi bi-pencil me-2 text-primary"></i>Edit Scheme Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('mutual-funds?fund_id=' . $h['id'] . '&tab=transactions') ?>" onclick="showMfLedger(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                            href="<?= base_url('mutual-funds/delete/' . $h['id']) ?>" 
                                                            onclick="return confirm('Delete this Mutual Fund scheme and all its transaction records?');">
                                                            <i class="bi bi-trash me-2"></i>Delete Scheme
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

