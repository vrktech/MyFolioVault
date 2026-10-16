<!-- ========================================================================= -->
<!-- MINIMAL ETF TRANSACTION POPUP MODAL (Buy More / Sell FIFO)                -->
<!-- ========================================================================= -->
<div class="modal fade" id="etfTransactionModal" tabindex="-1" aria-labelledby="etfTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="etfTransactionModalLabel">
                        Add Transaction: <span id="modalEtfSymbol" class="text-info"></span>
                    </h5>
                    <div class="text-muted small" id="modalEtfSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('etfs/transaction') ?>" method="POST" id="etfTransForm">
                <?= csrf_field() ?>
                <input type="hidden" name="etf_id" id="modalEtfId" value="">

                <div class="modal-body p-4">
                    <!-- Segmented Action Tabs -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actEtfBuy" value="BUY" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actEtfBuy">
                            <i class="bi bi-cart-plus me-1"></i>Buy More
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actEtfSell" value="SELL" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actEtfSell">
                            <i class="bi bi-cart-dash me-1"></i>Sell (FIFO)
                        </label>
                    </div>

                    <!-- SUB-FORM 1: BUY MORE -->
                    <div id="panelEtfBuy" class="etf-trans-panel">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="buyEtfDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Units <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="buyEtfQty" min="1" step="1" placeholder="Units" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Buy Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="buyEtfPrice" min="0.01" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Brokerage + Taxes (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="buyEtfCharges" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Monthly SIP accumulation">
                            </div>
                        </div>

                        <!-- Buy Outlay Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Total Outlay:</span>
                                <span class="fw-bold text-success" id="buyEtfTotalPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-FORM 2: SELL (FIFO) -->
                    <div id="panelEtfSell" class="etf-trans-panel d-none">
                        <div class="alert alert-warning py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Available to Sell:</span>
                            <strong id="sellEtfAvailableQtyBadge">0 units</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Sale Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="sellEtfDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Units to Sell <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="sellEtfQty" min="1" step="1" placeholder="Units" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Sell Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="sellEtfPrice" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Brokerage + STT (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="sellEtfCharges" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Asset rebalancing" disabled>
                            </div>
                        </div>

                        <!-- Sell Proceeds Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Estimated Net Proceeds:</span>
                                <span class="fw-bold text-dark" id="sellEtfTotalPreview">₹ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Realized P&L:</span>
                                <span class="small fw-semibold" id="sellEtfPnlPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitEtfTrans">
                        Confirm Purchase Lot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT ETF METADATA MODAL                                                   -->
<!-- ========================================================================= -->
<div class="modal fade" id="editEtfModal" tabindex="-1" aria-labelledby="editEtfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editEtfModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit ETF Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('etfs/update-etf') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="etf_id" id="editEtfId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Trading Symbol <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-bold text-uppercase" name="symbol" id="editEtfSymbol" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Exchange <span class="text-danger">*</span></label>
                            <select class="form-select" name="exchange" id="editEtfExchange" required>
                                <option value="NSE">NSE</option>
                                <option value="BSE">BSE</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">ETF Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="etf_name" id="editEtfName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Asset Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" id="editEtfCategory" required>
                                <option value="Index">Index</option>
                                <option value="Commodity - Gold">Commodity - Gold</option>
                                <option value="Commodity - Silver">Commodity - Silver</option>
                                <option value="Sectoral">Sectoral</option>
                                <option value="Global">Global</option>
                                <option value="Debt">Debt</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">AMC / Issuer</label>
                            <input type="text" class="form-control" name="amc_name" id="editEtfAmc" placeholder="e.g. Nippon India, SBI Mutual Fund">
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
<!-- MODAL: RECORD ETF UNIT SPLIT                                              -->
<!-- ========================================================================= -->
<div class="modal fade" id="etfSplitModal" tabindex="-1" aria-labelledby="etfSplitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= base_url('etfs/split') ?>" method="POST" id="etfSplitForm">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark" id="etfSplitModalLabel">
                        <i class="bi bi-pie-chart-fill text-info me-2"></i>Record ETF Unit Split
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="split_etf_id" class="form-label small fw-semibold text-secondary">Select ETF <span class="text-danger">*</span></label>
                        <select class="form-select" id="split_etf_id" name="etf_id" required>
                            <option value="">-- Choose ETF --</option>
                            <?php foreach ($holdings as $h): ?>
                                <option value="<?= $h['id'] ?>" data-symbol="<?= esc($h['symbol']) ?>">
                                    <?= esc($h['symbol']) ?> — <?= esc($h['etf_name'] ?? $h['name'] ?? '') ?> (<?= number_format((float)($h['active_quantity'] ?? $h['total_quantity'] ?? 0)) ?> units)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="split_record_date" class="form-label small fw-semibold text-secondary">Record Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="split_record_date" name="record_date" value="<?= date('Y-m-d') ?>" required>
                        <div class="mt-1 d-flex align-items-center gap-2">
                            <span class="badge bg-light text-secondary border small" id="split_eligible_badge">
                                <i class="bi bi-info-circle me-1"></i>Select ETF to check eligible units on record date
                            </span>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">New Units <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="split_ratio_new" name="ratio_new" value="2" required>
                            <div class="form-text small">Units received (e.g. 2, 5, 10)</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">For Existing Units <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" id="split_ratio_old" name="ratio_old" value="1" required>
                            <div class="form-text small">Units held (e.g. 1)</div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="split_notes" class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                        <textarea class="form-control" id="split_notes" name="notes" rows="2" placeholder="e.g. 1:5 Split announced by AMC"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i>Execute ETF Split
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT ETF TRANSACTION MODAL (WITH FIFO REBUILD)                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="editEtfTransModal" tabindex="-1" aria-labelledby="editEtfTransModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editEtfTransModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i><span id="editEtfTransTitle">Edit Transaction</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('etfs/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editEtfTransId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Saving changes will automatically <strong>rebalance FIFO lots and recalculate capital gains</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editEtfTransDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quantity (Units) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="quantity" id="editEtfTransQty" min="1" step="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Price per Unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="editEtfTransPrice" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                            <input type="number" class="form-control" name="brokerage" id="editEtfTransBrokerage" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">STT / Taxes (₹)</label>
                            <input type="number" class="form-control" name="stt_taxes" id="editEtfTransStt" min="0" step="0.01">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editEtfTransNotes">
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
