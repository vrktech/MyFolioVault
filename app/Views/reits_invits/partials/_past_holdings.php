            <div class="tab-pane fade p-3" id="past-holdings" role="tabpanel">
                <?php if (empty($pastHoldings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-archive fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Past REIT / InvIT Holdings Found</h5>
                        <p class="text-muted small">You don't have any fully exited trust positions yet. When you sell 100% of your units in a REIT or InvIT, it will appear here along with your lifetime realized capital gains and distributions collected.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($pastHoldings) ?></strong> past / closed trusts
                        </div>
                        <div class="text-muted small">
                            <span class="badge bg-secondary-subtle text-secondary border"><i class="bi bi-info-circle me-1"></i>0 Active Units &bull; Lifetime Closed Trades</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="reitPastHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Security / Trust</th>
                                    <th>Type</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end">Realized P&L</th>
                                    <th class="text-end">Distributions (₹)</th>
                                    <th class="text-end">Total Return (₹)</th>
                                    <th class="text-center" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pastHoldings as $ph): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0"><?= esc($ph['trust_name']) ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <span class="badge bg-secondary-subtle text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= esc($ph['exchange']) ?>
                                                    </span>
                                                    <?= stock_badge($ph['symbol']) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                <?= esc($ph['trust_type']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                <i class="bi bi-check2-all me-1"></i>Closed
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <?= format_inr($ph['current_price']) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($ph['realized_pnl']) ?>
                                        </td>
                                        <td class="text-end text-success fw-medium">
                                            <?= ($ph['distributions_earned'] ?? 0) > 0 ? format_inr($ph['distributions_earned']) : '—' ?>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= format_pnl($ph['total_return']) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success rounded-start-3 px-2 open-reit-modal" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#reitTransactionModal"
                                                        data-id="<?= $ph['id'] ?>"
                                                        data-symbol="<?= esc($ph['symbol']) ?>"
                                                        data-name="<?= esc($ph['trust_name']) ?>"
                                                        data-type="<?= esc($ph['trust_type']) ?>"
                                                        data-exchange="<?= esc($ph['exchange']) ?>"
                                                        data-qty="0"
                                                        data-cmp="<?= $ph['current_price'] ?>"
                                                        data-avg="0">
                                                    <i class="bi bi-plus-circle me-1"></i>Buy Again
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split rounded-end-3" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li>
                                                        <button class="dropdown-item small open-edit-trust-modal"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editTrustModal"
                                                                data-id="<?= $ph['id'] ?>"
                                                                data-name="<?= esc($ph['trust_name']) ?>"
                                                                data-symbol="<?= esc($ph['symbol']) ?>"
                                                                data-isin="<?= esc($ph['isin'] ?? '') ?>"
                                                                data-type="<?= esc($ph['trust_type']) ?>"
                                                                data-exchange="<?= esc($ph['exchange']) ?>"
                                                                data-sponsor="<?= esc($ph['sponsor'] ?? '') ?>"
                                                                data-price="<?= esc($ph['current_price'] ?? '') ?>">
                                                            <i class="bi bi-pencil me-2 text-warning"></i>Edit Trust Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('reits-invits?trust_id=' . $ph['id'] . '&tab=transactions') ?>" onclick="showTrustLedger(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-success" href="<?= base_url('reits-invits?trust_id=' . $ph['id'] . '&tab=distributions') ?>" onclick="showTrustDistributions(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-cash-coin me-2"></i>View Distributions
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-info" href="<?= base_url('reits-invits?tax_reit_id=' . $ph['id'] . '&tab=capitalgains') ?>">
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

