            <div class="tab-pane fade p-3" id="past-holdings" role="tabpanel">
                <?php if (empty($pastHoldings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-archive fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Past Bond Holdings Found</h5>
                        <p class="text-muted small">You don't have any fully redeemed or sold bond positions yet. When you sell or redeem 100% of a bond, it will appear here along with your lifetime realized capital gains and interest earned.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($pastHoldings) ?></strong> past / closed bond investments
                        </div>
                        <div class="text-muted small">
                            <span class="badge bg-secondary-subtle text-secondary border"><i class="bi bi-info-circle me-1"></i>0 Active Quantity &bull; Lifetime Matured &amp; Exited Positions</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="bondPastHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Bond Name / ISIN</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end">Realized Capital P&L</th>
                                    <th class="text-end">Interest Earned (₹)</th>
                                    <th class="text-end">Net Return (₹)</th>
                                    <th class="text-center" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pastHoldings as $ph): ?>
                                    <?php
                                    $catBadge = match($ph['category']) {
                                        'SGB'            => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                        'GOVT_SECURITY'  => 'bg-success-subtle text-success border-success-subtle',
                                        'CORPORATE_NCD'  => 'bg-primary-subtle text-primary border-primary-subtle',
                                        'TAX_FREE'       => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                        default          => 'bg-light text-secondary border',
                                    };
                                    ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0"><?= esc($ph['bond_name']) ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <span class="badge border <?= $catBadge ?> px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= esc($ph['category']) ?>
                                                    </span>
                                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.65rem;">
                                                        <?= esc($ph['isin']) ?>
                                                    </span>
                                                    <?php if (!empty($ph['bond_symbol'])): ?>
                                                        <?= stock_badge($ph['bond_symbol']) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                <i class="bi bi-check2-all me-1"></i>Closed
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <?= format_inr($ph['current_market_price']) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($ph['realized_pnl']) ?>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                STCG: <?= format_inr($ph['stcg']) ?> &bull; LTCG: <?= format_inr($ph['ltcg']) ?>
                                            </div>
                                        </td>
                                        <td class="text-end text-success fw-medium">
                                            <?= ($ph['interest_earned'] ?? 0) > 0 ? format_inr($ph['interest_earned']) : '—' ?>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= format_pnl($ph['total_return']) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success rounded-start-3 px-2 open-bond-trans-modal" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#bondTransactionModal"
                                                        data-id="<?= $ph['id'] ?>"
                                                        data-name="<?= esc($ph['bond_name']) ?>"
                                                        data-isin="<?= esc($ph['isin']) ?>"
                                                        data-symbol="<?= esc($ph['bond_symbol'] ?? '') ?>"
                                                        data-cat="<?= esc($ph['category']) ?>"
                                                        data-qty="0"
                                                        data-cmp="<?= $ph['current_market_price'] ?>"
                                                        data-avg="0"
                                                        data-face="<?= $ph['face_value'] ?>"
                                                        data-rate="<?= $ph['coupon_rate'] ?>"
                                                        data-freq="<?= esc($ph['interest_frequency']) ?>">
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
                                                        <button class="dropdown-item small open-edit-bond-modal"
                                                                data-id="<?= $ph['id'] ?>"
                                                                data-name="<?= esc($ph['bond_name']) ?>"
                                                                data-isin="<?= esc($ph['isin']) ?>"
                                                                data-symbol="<?= esc($ph['bond_symbol'] ?? '') ?>"
                                                                data-cat="<?= esc($ph['category']) ?>"
                                                                data-issuer="<?= esc($ph['issuer'] ?? '') ?>"
                                                                data-face="<?= (float)$ph['face_value'] ?>"
                                                                data-rate="<?= (float)$ph['coupon_rate'] ?>"
                                                                data-freq="<?= esc($ph['interest_frequency']) ?>"
                                                                data-maturity="<?= esc($ph['maturity_date']) ?>"
                                                                data-issue="<?= esc($ph['issue_date'] ?? '') ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editBondModal">
                                                            <i class="bi bi-pencil me-2 text-primary"></i>Edit Bond Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('bonds?bond_id=' . $ph['id'] . '&tab=transactions') ?>" onclick="showBondLedger(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-success" href="<?= base_url('bonds?bond_id=' . $ph['id'] . '&tab=payouts') ?>" onclick="showBondPayouts(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-cash-coin me-2"></i>View Interest History
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-info-emphasis" href="<?= base_url('bonds?tax_bond_id=' . $ph['id'] . '&tab=capitalgains') ?>" onclick="showBondTaxLog(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-receipt-cutoff me-2"></i>View Tax &amp; Redemption Log
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                            href="<?= base_url('bonds/delete/' . $ph['id']) ?>" 
                                                            onclick="return confirm('Delete this Bond and all related transaction/coupon history?');">
                                                            <i class="bi bi-trash me-2"></i>Delete Bond
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

