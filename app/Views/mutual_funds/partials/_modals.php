<!-- ========================================================================= -->
<!-- MINIMAL MUTUAL FUND TRANSACTION POPUP MODAL (Buy More / Redeem FIFO)      -->
<!-- ========================================================================= -->
<div class="modal fade" id="mfTransactionModal" tabindex="-1" aria-labelledby="mfTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="mfTransactionModalLabel">
                        Add Transaction: <span id="modalMfCode" class="text-success"></span>
                    </h5>
                    <div class="text-muted small" id="modalMfSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('mutual-funds/transaction') ?>" method="POST" id="mfTransForm">
                <?= csrf_field() ?>
                <input type="hidden" name="mutual_fund_id" id="modalMfId" value="">

                <div class="modal-body p-4">
                    <!-- Segmented Action Tabs -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actMfBuy" value="BUY" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actMfBuy">
                            <i class="bi bi-cart-plus me-1"></i>Buy (SIP / Lumpsum)
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actMfRedeem" value="REDEEM" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actMfRedeem">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Redeem (FIFO)
                        </label>
                    </div>

                    <!-- SUB-FORM 1: BUY (SIP / LUMPSUM) -->
                    <div id="panelMfBuy" class="mf-trans-panel">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Purchase Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="buy_type" id="buyMfType" required>
                                    <option value="BUY_LUMPSUM" selected>Lumpsum Purchase</option>
                                    <option value="BUY_SIP">SIP Installment</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="buyMfDate" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Invested Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount" id="buyMfAmount" min="1" step="0.01" placeholder="e.g. 5000" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Allotted Units <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="units" id="buyMfUnits" min="0.0001" step="0.0001" placeholder="0.0000" required>
                            </div>

                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Allotment NAV (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="nav" id="buyMfNav" min="0.0001" step="0.0001" placeholder="0.0000" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Stamp Duty (0.005%) (₹)</label>
                                <input type="number" class="form-control" name="charges" id="buyMfCharges" min="0" step="0.01" value="0.00">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Monthly automated SIP allotment">
                            </div>
                        </div>

                        <!-- Buy Summary Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Total Cash Outflow:</span>
                                <span class="fw-bold text-success" id="buyMfTotalPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-FORM 2: REDEEM (FIFO) -->
                    <div id="panelMfRedeem" class="mf-trans-panel d-none">
                        <div class="alert alert-warning py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Available Units in Folio:</span>
                            <strong id="redeemMfAvailableUnitsBadge">0.0000 units</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Redemption Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="redeemMfDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Units to Redeem <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="units" id="redeemMfUnits" min="0.0001" step="0.0001" placeholder="Units" disabled>
                            </div>

                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Redemption NAV (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="nav" id="redeemMfNav" min="0.0001" step="0.0001" placeholder="0.0000" disabled>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-secondary">Exit Load / Charges (₹)</label>
                                <input type="number" class="form-control" name="charges" id="redeemMfCharges" min="0" step="0.01" value="0.00" disabled>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Goal attainment or rebalancing" disabled>
                            </div>
                        </div>

                        <!-- Redeem Proceeds Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Estimated Net Proceeds:</span>
                                <span class="fw-bold text-dark" id="redeemMfTotalPreview">₹ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Realized P&L:</span>
                                <span class="small fw-semibold" id="redeemMfPnlPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitMfTrans">
                        Confirm Purchase Lot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT MUTUAL FUND SCHEME METADATA MODAL                                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="editMfModal" tabindex="-1" aria-labelledby="editMfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editMfModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Scheme Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('mutual-funds/update-fund') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="fund_id" id="editMfId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Scheme Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="scheme_name" id="editMfSchemeName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" id="editMfCategory" required>
                                <option value="Equity - Large Cap">Equity - Large Cap</option>
                                <option value="Equity - Mid Cap">Equity - Mid Cap</option>
                                <option value="Equity - Small Cap">Equity - Small Cap</option>
                                <option value="Equity - Flexi Cap">Equity - Flexi Cap</option>
                                <option value="Equity - ELSS">Equity - ELSS</option>
                                <option value="Hybrid - Aggressive">Hybrid - Aggressive</option>
                                <option value="Hybrid - Balanced Advantage">Hybrid - Balanced Advantage</option>
                                <option value="Debt - Liquid">Debt - Liquid</option>
                                <option value="Debt - Short Term">Debt - Short Term</option>
                                <option value="Index Fund">Index Fund</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Folio Number</label>
                            <input type="text" class="form-control" name="folio_number" id="editMfFolio">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Fund House (AMC)</label>
                            <input type="text" class="form-control" name="fund_house" id="editMfFundHouse" placeholder="e.g. Parag Parikh, HDFC">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">AMFI Scheme Code</label>
                            <input type="text" class="form-control font-monospace" name="amfi_code" id="editMfAmfiCode" placeholder="e.g. 122639">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ISIN Code</label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="isin" id="editMfIsin" placeholder="e.g. INF846K01164">
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
<!-- EDIT MUTUAL FUND TRANSACTION MODAL (WITH FIFO REBUILD)                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="editMfTransModal" tabindex="-1" aria-labelledby="editMfTransModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editMfTransModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i><span id="editMfTransTitle">Edit Transaction</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('mutual-funds/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editMfTransId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Saving changes will automatically <strong>rebalance FIFO lots and recalculate capital gains</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editMfTransDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Units <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="units" id="editMfTransUnits" min="0.0001" step="0.0001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NAV (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="nav" id="editMfTransNav" min="0.0001" step="0.0001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Stamp Duty / Charges (₹)</label>
                            <input type="number" class="form-control" name="charges" id="editMfTransCharges" min="0" step="0.01">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editMfTransNotes">
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
