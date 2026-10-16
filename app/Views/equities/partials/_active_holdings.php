            <div class="tab-pane fade show active p-3" id="holdings" role="tabpanel">
                <?php if (empty($activeHoldings ?? $holdings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-graph-up-arrow fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Active Stock Holdings Found</h5>
                        <p class="text-muted small">You don't have any active stock holdings currently. Click below to add your first stock!</p>
                        <a href="<?= base_url('equities/new') ?>" class="btn btn-primary btn-sm rounded-3">
                            <i class="bi bi-plus-lg me-1"></i>Add New Stock
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Active Holdings Sorting Toolbar -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($activeHoldings ?? $holdings) ?></strong> active stocks
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="sortEquitiesSelect" class="small text-muted mb-0 fw-semibold text-nowrap"><i class="bi bi-sort-down me-1"></i>Sort By:</label>
                            <select id="sortEquitiesSelect" class="form-select form-select-sm shadow-none border-secondary-subtle" style="width: auto;">
                                <option value="invested_desc" selected>Invested (High → Low)</option>
                                <option value="invested_asc">Invested (Low → High)</option>
                                <option value="current_desc">Current (High → Low)</option>
                                <option value="current_asc">Current (Low → High)</option>
                                <option value="name_asc">Name (A → Z)</option>
                                <option value="name_desc">Name (Z → A)</option>
                                <option value="sector_asc">Sector (A → Z)</option>
                                <option value="sector_desc">Sector (Z → A)</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="equitiesHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="cursor-pointer sortable-equity-th" data-col="name" role="button" title="Click to sort by Name">
                                        Stock <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="cursor-pointer sortable-equity-th" data-col="sector" role="button" title="Click to sort by Sector">
                                        Sector <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Qty Held</th>
                                    <th class="text-end">Avg Buy (₹)</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end cursor-pointer sortable-equity-th" data-col="invested" role="button" title="Click to sort by Invested Value">
                                        Invested (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end cursor-pointer sortable-equity-th" data-col="current" role="button" title="Click to sort by Current Value">
                                        Current (₹) <i class="bi bi-arrow-down-up text-muted small ms-1"></i>
                                    </th>
                                    <th class="text-end">Unrealized P&L</th>
                                    <th class="text-end">Dividends</th>
                                    <th class="text-center" style="min-width: 170px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="equitiesHoldingsTbody">
                                <?php foreach ($holdings as $h): ?>
                                    <tr class="equity-holding-row"
                                        data-name="<?= esc(strtolower($h['symbol'] . ' ' . $h['company_name'])) ?>"
                                        data-symbol="<?= esc($h['symbol']) ?>"
                                        data-sector="<?= esc(strtolower($h['sector'] ?? '')) ?>"
                                        data-invested="<?= (float)$h['invested_value'] ?>"
                                        data-current="<?= (float)$h['current_value'] ?>">
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0"><?= esc($h['company_name']) ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <span class="badge bg-secondary-subtle text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= esc($h['exchange']) ?>
                                                    </span>
                                                    <?= stock_badge($h['symbol']) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-secondary small"><?= esc($h['sector'] ?: '—') ?></span>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?= number_format($h['active_quantity']) ?>
                                        </td>
                                        <td class="text-end text-muted small">
                                            <?= format_inr($h['avg_buy_price']) ?>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <div><?= format_inr($h['current_price']) ?></div>
                                            <?php $priceUpdate = $h['price_updated_at'] ?? $h['updated_at'] ?? null; ?>
                                            <?php if (!empty($priceUpdate)): ?>
                                                <div class="text-muted fw-normal" style="font-size: 0.68rem;" title="Last price update">
                                                    <?= date('d M H:i', strtotime($priceUpdate)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end small text-muted">
                                            <div class="fw-semibold text-dark"><?= format_inr($h['invested_value']) ?></div>
                                            <?php $invPct = ($summary['total_invested'] ?? 0) > 0 ? (($h['invested_value'] / $summary['total_invested']) * 100) : 0; ?>
                                            <div class="text-secondary" style="font-size: 0.72rem;"><?= number_format($invPct, 2) ?>% of total</div>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            <div><?= format_inr($h['current_value']) ?></div>
                                            <?php $currPct = ($summary['total_current_value'] ?? 0) > 0 ? (($h['current_value'] / $summary['total_current_value']) * 100) : 0; ?>
                                            <div class="text-secondary fw-normal" style="font-size: 0.72rem;"><?= number_format($currPct, 2) ?>% of total</div>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($h['unrealized_pnl'], $h['unrealized_pnl_percent'], false, true) ?>
                                        </td>
                                        <td class="text-end small text-success fw-medium">
                                            <?= $h['total_dividends'] > 0 ? format_inr($h['total_dividends']) : '—' ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary rounded-start-3 px-2 py-1 open-trans-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#transactionModal"
                                                        data-id="<?= $h['id'] ?>"
                                                        data-symbol="<?= esc($h['symbol']) ?>"
                                                        data-name="<?= esc($h['company_name']) ?>"
                                                        data-qty="<?= $h['active_quantity'] ?>"
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
                                                        <a class="dropdown-item small view-stock-ledger" href="#transactions" data-stock-id="<?= $h['id'] ?>">
                                                            <i class="bi bi-clock-history me-2 text-primary"></i>View in Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" 
                                                                class="dropdown-item small open-edit-stock-modal"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editStockModal"
                                                                data-id="<?= $h['id'] ?>"
                                                                data-symbol="<?= esc($h['symbol']) ?>"
                                                                data-name="<?= esc($h['company_name']) ?>"
                                                                data-isin="<?= esc($h['isin'] ?? '') ?>"
                                                                data-sector="<?= esc($h['sector'] ?? '') ?>"
                                                                data-exchange="<?= esc($h['exchange']) ?>">
                                                            <i class="bi bi-pencil me-2 text-secondary"></i>Edit Stock Details
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger small" 
                                                           href="<?= base_url('equities/delete/' . $h['id']) ?>" 
                                                           onclick="return confirm('Delete this stock and all its trade records?');">
                                                            <i class="bi bi-trash me-2"></i>Delete Stock
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

