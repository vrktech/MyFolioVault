<!-- ========================================================================= -->
<!-- MINIMAL TRANSACTION POPUP MODAL (Buy More / Sell / Record Dividend)       -->
<!-- ========================================================================= -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="transactionModalLabel">
                        Add Transaction: <span id="modalStockSymbol" class="text-primary"></span>
                    </h5>
                    <div class="text-muted small" id="modalStockSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('equities/transaction') ?>" method="POST" id="transForm">
                <?= csrf_field() ?>
                <input type="hidden" name="equity_id" id="modalEquityId" value="">

                <div class="modal-body p-4">
                    <!-- Segmented Action Tabs -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actBuy" value="BUY" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actBuy">
                            <i class="bi bi-cart-plus me-1"></i>Buy More
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actSell" value="SELL" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actSell">
                            <i class="bi bi-cart-dash me-1"></i>Sell (FIFO)
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actDividend" value="DIVIDEND" autocomplete="off">
                        <label class="btn btn-outline-primary fw-semibold" for="actDividend">
                            <i class="bi bi-cash-coin me-1"></i>Dividend
                        </label>
                    </div>

                    <!-- SUB-FORM 1: BUY MORE -->
                    <div id="panelBuy" class="trans-panel">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="buyDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="buyQty" min="1" step="1" placeholder="Shares" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Buy Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="buyPrice" min="0.01" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Brokerage + Taxes (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="buyCharges" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Added on correction">
                            </div>
                        </div>

                        <!-- Buy Outlay Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Total Outlay:</span>
                                <span class="fw-bold text-success" id="buyTotalPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-FORM 2: SELL -->
                    <div id="panelSell" class="trans-panel d-none">
                        <div class="alert alert-warning py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Available to Sell:</span>
                            <strong id="sellAvailableQtyBadge">0 shares</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Sale Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="sellDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity to Sell <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="sellQty" min="1" step="1" placeholder="Shares" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Sell Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="sellPrice" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Brokerage + STT (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="sellCharges" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Booking profits" disabled>
                            </div>
                        </div>

                        <!-- Sell Proceeds Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Estimated Net Proceeds:</span>
                                <span class="fw-bold text-dark" id="sellTotalPreview">₹ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Realized P&L:</span>
                                <span class="small fw-semibold" id="sellPnlPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-FORM 3: RECORD DIVIDEND -->
                    <div id="panelDividend" class="trans-panel d-none">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Credit Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="dividend_date" id="divDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="dividend_type" disabled>
                                    <option value="Interim">Interim</option>
                                    <option value="Final">Final</option>
                                    <option value="Special">Special</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Total Net Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="total_amount" id="divAmount" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                                <input type="number" class="form-control" name="tds_deducted" id="divTds" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Dividend Per Share (₹)</label>
                                <input type="number" class="form-control" name="amount_per_share" id="divPerShare" min="0" step="0.01" placeholder="Optional" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Shares Held on Record Date</label>
                                <input type="number" class="form-control" name="shares_held" id="divSharesHeld" min="0" step="1" placeholder="Optional" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Q3 Interim Dividend" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnSubmitTrans">
                        Confirm Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT STOCK METADATA MODAL                                                 -->
<!-- ========================================================================= -->
<div class="modal fade" id="editStockModal" tabindex="-1" aria-labelledby="editStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editStockModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Stock Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('equities/update-stock') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="equity_id" id="editStockId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Trading Symbol <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold text-uppercase" name="symbol" id="editStockSymbol" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Exchange <span class="text-danger">*</span></label>
                            <select class="form-select" name="exchange" id="editStockExchange" required>
                                <option value="NSE">NSE</option>
                                <option value="BSE">BSE</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="company_name" id="editStockName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ISIN Code</label>
                            <input type="text" class="form-control text-uppercase" name="isin" id="editStockIsin" placeholder="e.g. INE002A01018">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Industry Sector</label>
                            <select class="form-select" name="sector" id="editStockSector">
                                <option value="">-- Select Sector / Industry --</option>
                                <?php if (!empty($sectors)): ?>
                                    <?php foreach ($sectors as $sec): ?>
                                        <option value="<?= esc($sec['name']) ?>"><?= esc($sec['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: RECORD CORPORATE ACTION (SPLIT, BONUS, RIGHTS, MERGER, DEMERGER)   -->
<!-- ========================================================================= -->
<div class="modal fade" id="corporateActionModal" tabindex="-1" aria-labelledby="corporateActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= base_url('equities/corporate-action') ?>" method="POST" id="corporateActionForm">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark" id="corporateActionModalLabel">
                        <i class="bi bi-diagram-3 text-primary me-2"></i>Record Corporate Action
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Step 1: Select Stock -->
                    <div class="mb-3">
                        <label for="ca_equity_id" class="form-label small fw-semibold text-secondary">Select Stock <span class="text-danger">*</span></label>
                        <select class="form-select" id="ca_equity_id" name="equity_id" required>
                            <option value="">-- Choose Stock --</option>
                            <?php foreach ($holdings as $h): ?>
                                <option value="<?= $h['id'] ?>" data-symbol="<?= esc($h['symbol']) ?>">
                                    <?= esc($h['symbol']) ?> — <?= esc($h['company_name']) ?> (<?= number_format((float)($h['active_quantity'] ?? $h['total_quantity'] ?? 0)) ?> shares)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Step 2: Action Type -->
                    <div class="mb-3">
                        <label for="ca_action_type" class="form-label small fw-semibold text-secondary">Action Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="ca_action_type" name="action_type" required>
                            <option value="SPLIT">Stock Split (e.g. 1:2, 1:5, 1:10)</option>
                            <option value="BONUS">Bonus Issue (e.g. 1:1, 2:1, 1:4)</option>
                            <option value="RIGHTS">Rights Issue (Subsidized buy)</option>
                            <option value="MERGER">Merger / Amalgamation (Swap into Target Stock)</option>
                            <option value="DEMERGER">Demerger / Spin-off (Apportion cost to new Stock)</option>
                        </select>
                    </div>

                    <!-- Step 3: Record Date -->
                    <div class="mb-3">
                        <label for="ca_record_date" class="form-label small fw-semibold text-secondary">Record Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="ca_record_date" name="record_date" value="<?= date('Y-m-d') ?>" required>
                        <div class="mt-1 d-flex align-items-center gap-2" id="ca_eligibility_box">
                            <span class="badge bg-light text-secondary border small" id="ca_eligible_badge">
                                <i class="bi bi-info-circle me-1"></i>Select stock to check eligible shares on record date
                            </span>
                        </div>
                    </div>

                    <!-- Conditional Section: Ratio (Split, Bonus, Merger, Demerger) -->
                    <div class="row g-2 mb-3" id="ca_ratio_group">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary" id="ca_ratio_new_label">New Shares <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="ca_ratio_new" name="ratio_new" value="2">
                            <div class="form-text small" id="ca_ratio_new_help">Shares received</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary" id="ca_ratio_old_label">For Existing Shares <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="ca_ratio_old" name="ratio_old" value="1">
                            <div class="form-text small" id="ca_ratio_old_help">Shares held on record date</div>
                        </div>
                    </div>

                    <!-- Conditional Section: Rights Issue Fields -->
                    <div class="row g-2 mb-3 d-none" id="ca_rights_group">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Subscribed Shares <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="ca_subscribed_shares" name="subscribed_shares">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Offer Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="ca_offer_price" name="offer_price">
                        </div>
                    </div>

                    <!-- Conditional Section: Target Security (Merger / Demerger) -->
                    <div class="mb-3 d-none" id="ca_target_group">
                        <label for="ca_target_security_id" class="form-label small fw-semibold text-secondary" id="ca_target_label">Destination / Resulting Stock <span class="text-danger">*</span></label>
                        <select class="form-select" id="ca_target_security_id" name="target_security_id">
                            <option value="">-- Choose Stock --</option>
                            <?php foreach ($holdings as $h): ?>
                                <option value="<?= $h['id'] ?>"><?= esc($h['symbol']) ?> — <?= esc($h['company_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small text-muted" id="ca_target_help">
                            If the resulting entity is not yet in your portfolio, add it via "Add New Stock" first.
                        </div>
                    </div>

                    <!-- Conditional Section: Demerger Cost Apportionment % -->
                    <div class="mb-3 d-none" id="ca_demerger_group">
                        <label for="ca_cost_apportionment" class="form-label small fw-semibold text-secondary">Cost Apportionment % to Spun-off Entity <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" max="99.99" class="form-control" id="ca_cost_apportionment" name="cost_apportionment_ratio" placeholder="e.g. 33.5">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text small text-muted">
                            As per circular (e.g. 33.5% cost basis apportioned to new entity, 66.5% retained by parent).
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-2">
                        <label for="ca_notes" class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                        <textarea class="form-control" id="ca_notes" name="notes" rows="2" placeholder="e.g. Approved in AGM / Board Meeting"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-semibold" id="ca_submit_btn">
                        <i class="bi bi-check2-circle me-1"></i>Apply Corporate Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT TRANSACTION MODAL (WITH FIFO REBUILD)                                -->
<!-- ========================================================================= -->
<div class="modal fade" id="editEquityTransModal" tabindex="-1" aria-labelledby="editEquityTransModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editEquityTransModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i><span id="editEquityTransTitle">Edit Transaction</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('equities/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editEquityTransId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Saving changes will automatically <strong>rebalance FIFO lots and recalculate capital gains</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editEquityTransDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quantity (Shares) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="quantity" id="editEquityTransQty" min="1" step="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Price per Share (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="editEquityTransPrice" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                            <input type="number" class="form-control" name="brokerage" id="editEquityTransBrokerage" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STT / Taxes (₹)</label>
                            <input type="number" class="form-control" name="stt_taxes" id="editEquityTransStt" min="0" step="0.01">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editEquityTransNotes">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">Update &amp; Rebalance FIFO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT DIVIDEND MODAL                                                       -->
<!-- ========================================================================= -->
<div class="modal fade" id="editEquityDividendModal" tabindex="-1" aria-labelledby="editEquityDividendModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editEquityDividendModalLabel">
                    <i class="bi bi-cash-coin text-success me-2"></i><span id="editEquityDivTitle">Edit Dividend</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('equities/update-dividend') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="dividend_id" id="editEquityDivId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Credit Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="dividend_date" id="editEquityDivDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Dividend Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="dividend_type" id="editEquityDivType" required>
                                <option value="Interim">Interim</option>
                                <option value="Final">Final</option>
                                <option value="Special">Special</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Shares Held</label>
                            <input type="number" class="form-control" name="shares_held" id="editEquityDivShares" min="0" step="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Dividend per Share (₹)</label>
                            <input type="number" class="form-control" name="amount_per_share" id="editEquityDivPerShare" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Total Gross Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="total_amount" id="editEquityDivTotal" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                            <input type="number" class="form-control text-danger" name="tds_deducted" id="editEquityDivTds" min="0" step="0.01">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editEquityDivNotes">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">Save Dividend</button>
                </div>
            </form>
        </div>
    </div>
</div>
