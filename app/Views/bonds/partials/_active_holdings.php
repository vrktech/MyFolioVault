            <div class="tab-pane fade show active p-3" id="holdings" role="tabpanel">
                <?php if (empty($activeHoldings ?? $holdings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-receipt fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Active Bond Holdings Found</h5>
                        <p class="text-muted small">You don't have any active bond holdings. Click below to add your first bond security!</p>
                        <a href="<?= base_url('bonds/new') ?>" class="btn btn-primary btn-sm rounded-3">
                            <i class="bi bi-plus-lg me-1"></i>Add New Bond
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Active Holdings Sorting Toolbar -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($activeHoldings ?? $holdings) ?></strong> active bond holdings
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="sortBondsSelect" class="small text-muted mb-0 fw-semibold text-nowrap"><i class="bi bi-sort-down me-1"></i>Sort By:</label>
                            <select id="sortBondsSelect" class="form-select form-select-sm shadow-none border-secondary-subtle" style="width: auto;">
                                <option value="current_desc" selected>Current Value (High → Low)</option>
                                <option value="current_asc">Current Value (Low → High)</option>
                                <option value="name_asc">Bond Name (A → Z)</option>
                                <option value="name_desc">Bond Name (Z → A)</option>
                                <option value="interest_desc">Last Interest Date (Recent First)</option>
                                <option value="interest_asc">Last Interest Date (Oldest First)</option>
                                <option value="maturity_asc">Maturity Date (Nearest First)</option>
                                <option value="maturity_desc">Maturity Date (Furthest First)</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="bondHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="cursor-pointer sortable-bond-th" data-col="name" role="button" title="Click to sort by Bond Name">
                                        Bond Name / ISIN <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Units</th>
                                    <th>Coupon</th>
                                    <th class="cursor-pointer sortable-bond-th" data-col="maturity" role="button" title="Click to sort by Maturity Date">
                                        Maturity Date <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Avg Cost (₹)</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end cursor-pointer sortable-bond-th" data-col="current" role="button" title="Click to sort by Current Value">
                                        Current (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Capital P&L</th>
                                    <th class="cursor-pointer sortable-bond-th" data-col="interest" role="button" title="Click to sort by Last Payout">
                                        Last Payout <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-center" style="min-width: 160px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="bondHoldingsTbody">
                                <?php foreach (($activeHoldings ?? $holdings) as $h): ?>
                                    <?php $isMatured = ($h['days_to_maturity'] <= 0 || date('Y-m-d') >= $h['maturity_date']); ?>
                                    <tr class="bond-holding-row"
                                        data-name="<?= esc(strtolower($h['bond_name'])) ?>"
                                        data-maturity="<?= esc($h['maturity_date']) ?>"
                                        data-current="<?= (float)$h['current_value'] ?>"
                                        data-interest="<?= esc($h['last_interest_paid_date'] ?? '') ?>">
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0"><?= esc($h['bond_name']) ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <?php
                                                    $catBadge = match($h['category']) {
                                                        'SGB'            => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                                        'GOVT_SECURITY'  => 'bg-success-subtle text-success border-success-subtle',
                                                        'CORPORATE_NCD'  => 'bg-primary-subtle text-primary border-primary-subtle',
                                                        'TAX_FREE'       => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                                        default          => 'bg-light text-secondary border',
                                                    };
                                                    $catLabel = match($h['category']) {
                                                        'SGB'            => 'SGB Gold',
                                                        'GOVT_SECURITY'  => 'G-Sec',
                                                        'CORPORATE_NCD'  => 'Corp NCD',
                                                        'TAX_FREE'       => 'Tax-Free',
                                                        default          => esc($h['category']),
                                                    };
                                                    ?>
                                                    <span class="badge <?= $catBadge ?> border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= $catLabel ?>
                                                    </span>
                                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.65rem;">
                                                        <?= esc($h['isin']) ?>
                                                    </span>
                                                    <?php if (!empty($h['bond_symbol'])): ?>
                                                        <?= stock_badge($h['bond_symbol']) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?= number_format($h['active_quantity'], 0) ?>
                                            <div class="text-muted small" style="font-size: 0.7rem;">
                                                <?= $h['category'] === 'SGB' ? 'grams' : 'units' ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= number_format($h['coupon_rate'], 2) ?>% p.a.</div>
                                            <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                <?= match($h['interest_frequency']) {
                                                    'MONTHLY'     => 'Monthly',
                                                    'QUARTERLY'   => 'Quarterly',
                                                    'SEMI_ANNUAL' => 'Semi-Annual',
                                                    'ANNUAL'      => 'Annual',
                                                    'CUMULATIVE'  => 'Cumulative',
                                                    default       => esc($h['interest_frequency']),
                                                } ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-dark small"><?= date('d-M-Y', strtotime($h['maturity_date'])) ?></div>
                                            <small class="text-muted" style="font-size: 0.72rem;">
                                                <?php if ($h['days_to_maturity'] > 0): ?>
                                                    <?= $h['days_to_maturity'] ?> days left
                                                <?php else: ?>
                                                    <span class="text-danger fw-bold">Matured</span>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td class="text-end text-muted small">
                                            <?= format_inr($h['avg_buy_price']) ?>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <a href="#" class="text-decoration-none text-dark open-price-modal" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#bondPriceModal"
                                               data-id="<?= $h['id'] ?>"
                                               data-name="<?= esc($h['bond_name']) ?>"
                                               data-price="<?= $h['current_market_price'] ?>"
                                               title="Click to update market price">
                                                <?= format_inr($h['current_market_price']) ?>
                                                <i class="bi bi-pencil-fill text-muted ms-1" style="font-size: 0.65rem;"></i>
                                            </a>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            <?= format_inr($h['current_value']) ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($h['unrealized_pnl'], $h['unrealized_pnl_percent'], false, true) ?>
                                        </td>
                                        <td>
                                            <!-- LAST INTEREST PAID DATE (Moved to last before Action) -->
                                            <?php if (!empty($h['last_interest_paid_date'])): ?>
                                                <div class="fw-semibold text-success">
                                                    <i class="bi bi-calendar-check me-1"></i><?= date('d-M-Y', strtotime($h['last_interest_paid_date'])) ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.72rem;">
                                                    <?= format_inr($h['interest_earned']) ?> earned
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted small">— Not yet paid</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger rounded-start-3 px-2 py-1 open-bond-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#bondTransactionModal"
                                                        data-id="<?= $h['id'] ?>"
                                                        data-name="<?= esc($h['bond_name']) ?>"
                                                        data-isin="<?= esc($h['isin']) ?>"
                                                        data-cat="<?= esc($h['category']) ?>"
                                                        data-qty="<?= $h['active_quantity'] ?>"
                                                        data-cmp="<?= $h['current_market_price'] ?>"
                                                        data-avg="<?= $h['avg_buy_price'] ?>"
                                                        data-face="<?= $h['face_value'] ?>"
                                                        data-rate="<?= $h['coupon_rate'] ?>"
                                                        data-freq="<?= esc($h['interest_frequency']) ?>">
                                                    <i class="bi bi-lightning-charge-fill me-1"></i>Add Trans
                                                </button>
                                                <?php if ($isMatured && $h['active_quantity'] > 0): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-dark px-2 py-1 open-maturity-modal"
                                                            data-id="<?= $h['id'] ?>"
                                                            data-name="<?= esc($h['bond_name']) ?>"
                                                            data-isin="<?= esc($h['isin']) ?>"
                                                            data-cat="<?= esc($h['category']) ?>"
                                                            data-qty="<?= $h['active_quantity'] ?>"
                                                            data-face="<?= (float)$h['face_value'] ?>"
                                                            data-cmp="<?= (float)$h['current_market_price'] ?>"
                                                            data-maturity="<?= esc($h['maturity_date']) ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#bondMaturityRedemptionModal"
                                                            title="Bond has reached maturity date. Click to record final principal redemption.">
                                                        <i class="bi bi-award me-1 text-warning"></i>Redeem
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split rounded-end-3" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <?php if ($isMatured && $h['active_quantity'] > 0): ?>
                                                        <li>
                                                            <button type="button" 
                                                                    class="dropdown-item small text-dark fw-semibold open-maturity-modal"
                                                                    data-id="<?= $h['id'] ?>"
                                                                    data-name="<?= esc($h['bond_name']) ?>"
                                                                    data-isin="<?= esc($h['isin']) ?>"
                                                                    data-cat="<?= esc($h['category']) ?>"
                                                                    data-qty="<?= $h['active_quantity'] ?>"
                                                                    data-face="<?= (float)$h['face_value'] ?>"
                                                                    data-cmp="<?= (float)$h['current_market_price'] ?>"
                                                                    data-maturity="<?= esc($h['maturity_date']) ?>"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#bondMaturityRedemptionModal">
                                                                <i class="bi bi-award-fill me-2 text-warning"></i>Redeem at Maturity
                                                            </button>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <button class="dropdown-item small open-edit-bond-modal"
                                                                data-id="<?= $h['id'] ?>"
                                                                data-name="<?= esc($h['bond_name']) ?>"
                                                                data-isin="<?= esc($h['isin']) ?>"
                                                                data-symbol="<?= esc($h['bond_symbol'] ?? '') ?>"
                                                                data-cat="<?= esc($h['category']) ?>"
                                                                data-issuer="<?= esc($h['issuer'] ?? '') ?>"
                                                                data-face="<?= (float)$h['face_value'] ?>"
                                                                data-rate="<?= (float)$h['coupon_rate'] ?>"
                                                                data-freq="<?= esc($h['interest_frequency']) ?>"
                                                                data-maturity="<?= esc($h['maturity_date']) ?>"
                                                                data-issue="<?= esc($h['issue_date'] ?? '') ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editBondModal">
                                                            <i class="bi bi-pencil me-2 text-primary"></i>Edit Bond Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('bonds?bond_id=' . $h['id'] . '&tab=transactions') ?>" onclick="showBondLedger(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-success" href="<?= base_url('bonds?bond_id=' . $h['id'] . '&tab=payouts') ?>" onclick="showBondPayouts(<?= $h['id'] ?>); return false;">
                                                            <i class="bi bi-cash-coin me-2"></i>View Interest History
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                            href="<?= base_url('bonds/delete/' . $h['id']) ?>" 
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

