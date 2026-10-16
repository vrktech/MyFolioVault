            <div class="tab-pane fade p-3" id="past-holdings" role="tabpanel">
                <?php if (empty($pastHoldings)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-archive fs-1 text-muted mb-3 d-block"></i>
                        <h5>No Past ETF Holdings Found</h5>
                        <p class="text-muted small">You don't have any fully exited ETF positions yet. When you sell 100% of your units in an ETF, it will appear here along with your lifetime realized profit/loss.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">
                            Showing <strong><?= count($pastHoldings) ?></strong> past / closed ETF positions
                        </div>
                        <div class="text-muted small">
                            <span class="badge bg-secondary-subtle text-secondary border"><i class="bi bi-info-circle me-1"></i>0 Active Units &bull; Lifetime Closed Trades</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="etfPastHoldingsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>ETF Symbol / Name</th>
                                    <th>Category</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">CMP (₹)</th>
                                    <th class="text-end">Realized P&L</th>
                                    <th class="text-center" style="min-width: 170px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pastHoldings as $ph): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-semibold text-dark mb-0"><?= esc($ph['etf_name']) ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-0.5">
                                                    <span class="badge bg-secondary-subtle text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;">
                                                        <?= esc($ph['exchange']) ?>
                                                    </span>
                                                    <?= stock_badge($ph['symbol']) ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                                <?= esc($ph['category']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                <i class="bi bi-check2-all me-1"></i>Closed
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            <div><?= format_inr($ph['current_price']) ?></div>
                                            <?php $priceUpdate = $ph['price_updated_at'] ?? $ph['updated_at'] ?? null; ?>
                                            <?php if (!empty($priceUpdate)): ?>
                                                <div class="text-muted fw-normal" style="font-size: 0.68rem;" title="Last price update">
                                                    <?= date('d M H:i', strtotime($priceUpdate)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?= format_pnl($ph['realized_pnl']) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success rounded-start-3 px-2 py-1 open-etf-trans-modal"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#etfTransactionModal"
                                                        data-id="<?= $ph['id'] ?>"
                                                        data-symbol="<?= esc($ph['symbol']) ?>"
                                                        data-name="<?= esc($ph['etf_name']) ?>"
                                                        data-units="0"
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
                                                        <button class="dropdown-item small open-edit-etf-modal"
                                                                data-id="<?= $ph['id'] ?>"
                                                                data-symbol="<?= esc($ph['symbol']) ?>"
                                                                data-name="<?= esc($ph['etf_name']) ?>"
                                                                data-category="<?= esc($ph['category']) ?>"
                                                                data-amc="<?= esc($ph['amc_name'] ?? '') ?>"
                                                                data-exchange="<?= esc($ph['exchange']) ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editEtfModal">
                                                            <i class="bi bi-pencil me-2 text-primary"></i>Edit ETF Details
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-primary" href="<?= base_url('etfs?etf_id=' . $ph['id'] . '&tab=transactions') ?>" onclick="showEtfLedger(<?= $ph['id'] ?>); return false;">
                                                            <i class="bi bi-clock-history me-2"></i>Trade Ledger
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item small text-info" href="<?= base_url('etfs?tax_etf_id=' . $ph['id'] . '&tab=capitalgains') ?>">
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

