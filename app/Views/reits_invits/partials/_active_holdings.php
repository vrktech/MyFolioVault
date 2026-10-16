            <div class="tab-pane fade show active p-3" id="holdings" role="tabpanel">
                <?php if (empty($activeHoldings ?? $holdings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-buildings fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Active REIT or InvIT Holdings Found</h5>
                        <p class="text-muted small">You don't have any active REIT or InvIT holdings currently. Click below to add your first holding!</p>
                        <a href="<?= base_url('reits-invits/new') ?>" class="btn btn-primary btn-sm rounded-3">
                            <i class="bi bi-plus-lg me-1"></i>Add New Trust
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Active Holdings Sorting Toolbar -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($activeHoldings ?? $holdings) ?></strong> active trusts
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="sortReitsSelect" class="small text-muted mb-0 fw-semibold text-nowrap"><i class="bi bi-sort-down me-1"></i>Sort By:</label>
                            <select id="sortReitsSelect" class="form-select form-select-sm shadow-none border-secondary-subtle" style="width: auto;">
                                <option value="invested_desc" selected>Invested (High → Low)</option>
                                <option value="invested_asc">Invested (Low → High)</option>
                                <option value="current_desc">Current (High → Low)</option>
                                <option value="current_asc">Current (Low → High)</option>
                                <option value="security_asc">Security (A → Z)</option>
                                <option value="security_desc">Security (Z → A)</option>
                                <option value="type_asc">Type (InvIT First)</option>
                                <option value="type_desc">Type (REIT First)</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="reitHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="cursor-pointer sortable-reit-th" data-col="security" role="button" title="Click to sort by Security">
                                        Security / Trust <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="cursor-pointer sortable-reit-th" data-col="type" role="button" title="Click to sort by Type">
                                        Type <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Units</th>
                                    <th class="text-end">Avg Cost (₹)</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end cursor-pointer sortable-reit-th" data-col="invested" role="button" title="Click to sort by Invested Value">
                                        Invested (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end cursor-pointer sortable-reit-th" data-col="current" role="button" title="Click to sort by Current Value">
                                        Current Value (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Unrealized P&L</th>
                                    <th class="text-end">Distributions (₹)</th>
                                    <th class="text-end">Total Return (₹)</th>
                                    <th class="text-center" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reitHoldingsTbody">
                                <?php foreach (($activeHoldings ?? $holdings) as $h): ?>
                                    <tr class="reit-holding-row"
                                        data-security="<?= esc(strtolower($h['symbol'] . ' ' . $h['trust_name'])) ?>"
                                        data-type="<?= esc(strtolower($h['trust_type'] ?? '')) ?>"
                                        data-invested="<?= (float)($h['invested_value'] ?? 0) ?>"
                                        data-current="<?= (float)($h['current_value'] ?? 0) ?>">
                                        <!-- Security / Trust -->
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0"><?= esc($h['trust_name']) ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <span class="badge bg-secondary-subtle text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= esc($h['exchange']) ?>
                                                    </span>
                                                    <?= stock_badge($h['symbol']) ?>
                                                </div>
                                                <?php if (!empty($h['sponsor'])): ?>
                                                    <div class="text-muted" style="font-size: 0.7rem;">
                                                        Sponsor: <?= esc($h['sponsor']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Type -->
                                        <td>
                                            <?php if ($h['trust_type'] === 'REIT'): ?>
                                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-building me-1"></i>REIT
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-broadcast-pin me-1"></i>InvIT
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Units -->
                                        <td class="text-end fw-semibold">
                                            <?= number_format($h['active_units'], 0) ?>
                                        </td>

                                        <!-- Avg Cost -->
                                        <td class="text-end">
                                            <?= format_inr($h['avg_buy_price']) ?>
                                        </td>

                                        <!-- CMP -->
                                        <td class="text-end">
                                            <div class="fw-bold text-dark"><?= format_inr($h['current_price']) ?></div>
                                            <?php if (!empty($h['last_price_update'])): ?>
                                                <small class="text-muted" style="font-size: 0.68rem;" title="Last price update">
                                                    <?= date('d M H:i', strtotime($h['last_price_update'])) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Invested -->
                                        <td class="text-end small text-muted">
                                            <div class="fw-semibold text-dark"><?= format_inr($h['invested_value']) ?></div>
                                            <?php $invPct = ($summary['total_invested'] ?? 0) > 0 ? (($h['invested_value'] / $summary['total_invested']) * 100) : 0; ?>
                                            <div class="text-secondary" style="font-size: 0.72rem;"><?= number_format($invPct, 2) ?>% of total</div>
                                        </td>

                                        <!-- Current Value -->
                                        <td class="text-end fw-bold text-dark">
                                            <div><?= format_inr($h['current_value']) ?></div>
                                            <?php $currPct = ($summary['total_current_value'] ?? 0) > 0 ? (($h['current_value'] / $summary['total_current_value']) * 100) : 0; ?>
                                            <div class="text-secondary fw-normal" style="font-size: 0.72rem;"><?= number_format($currPct, 2) ?>% of total</div>
                                        </td>

                                        <!-- Unrealized P&L -->
                                        <td class="text-end">
                                            <?= format_pnl($h['unrealized_pnl'], $h['unrealized_pnl_percent'], false, true) ?>
                                        </td>

                                        <!-- Distributions Earned -->
                                        <td class="text-end text-success fw-semibold">
                                            <?= format_inr($h['distributions_earned']) ?>
                                            <?php if (!empty($h['last_distribution_date'])): ?>
                                                <div class="text-muted" style="font-size: 0.68rem;">
                                                    Last: <?= date('d-M-Y', strtotime($h['last_distribution_date'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Total Return -->
                                        <td class="text-end fw-bold">
                                            <?= format_pnl($h['total_return']) ?>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary rounded-start-3 px-2 open-reit-modal" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#reitTransactionModal"
                                                        data-id="<?= $h['id'] ?>"
                                                        data-symbol="<?= esc($h['symbol']) ?>"
                                                        data-name="<?= esc($h['trust_name']) ?>"
                                                        data-type="<?= esc($h['trust_type']) ?>"
                                                        data-exchange="<?= esc($h['exchange']) ?>"
                                                        data-qty="<?= $h['active_units'] ?>"
                                                        data-cmp="<?= $h['current_price'] ?>"
                                                        data-avg="<?= $h['avg_buy_price'] ?>">
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
                                                        <button class="dropdown-item small open-edit-trust-modal"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editTrustModal"
                                                                data-id="<?= $h['id'] ?>"
                                                                data-name="<?= esc($h['trust_name']) ?>"
                                                                data-symbol="<?= esc($h['symbol']) ?>"
                                                                data-isin="<?= esc($h['isin'] ?? '') ?>"
                                                                data-type="<?= esc($h['trust_type']) ?>"
                                                                data-exchange="<?= esc($h['exchange']) ?>"
                                                                data-sponsor="<?= esc($h['sponsor'] ?? '') ?>"
                                                                data-price="<?= esc($h['current_price'] ?? '') ?>">
                                                            <i class="bi bi-pencil me-2 text-warning"></i>Edit Trust Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('reits-invits?trust_id=' . $h['id'] . '&tab=transactions') ?>" onclick="showTrustLedger(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-success" href="<?= base_url('reits-invits?trust_id=' . $h['id'] . '&tab=distributions') ?>" onclick="showTrustDistributions(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-cash-coin me-2"></i>View Distributions
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                            href="<?= base_url('reits-invits/delete/' . $h['id']) ?>" 
                                                            onclick="return confirm('Delete this trust and all related transaction, payout, and tax records?');">
                                                            <i class="bi bi-trash me-2"></i>Delete Trust
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

