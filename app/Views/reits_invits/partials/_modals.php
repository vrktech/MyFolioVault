<!-- ========================================================================= -->
<!-- MINIMAL POPUP TRANSACTION MODAL (BUY MORE / SELL FIFO / DISTRIBUTION)     -->
<!-- ========================================================================= -->
<div class="modal fade" id="reitTransactionModal" tabindex="-1" aria-labelledby="reitTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="reitTransactionModalLabel">
                        <i class="bi bi-buildings text-info me-2"></i><span id="modalReitTitle">Manage Holding</span>
                    </h5>
                    <div class="text-muted small" id="modalReitSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('reits-invits/transaction') ?>" method="POST" id="reitTransForm">
                <?= csrf_field() ?>
                <input type="hidden" name="trust_id" id="modalTrustId" value="">

                <div class="modal-body p-4">
                    <!-- Segmented 3-way Action Switcher -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actReitBuy" value="BUY" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actReitBuy">
                            <i class="bi bi-cart-plus me-1"></i>Buy More
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actReitSell" value="SELL" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actReitSell">
                            <i class="bi bi-cart-dash me-1"></i>Sell (FIFO)
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actReitDist" value="DISTRIBUTION" autocomplete="off">
                        <label class="btn btn-outline-primary fw-semibold" for="actReitDist">
                            <i class="bi bi-cash-coin me-1"></i>Record Distribution
                        </label>
                    </div>

                    <!-- SUB-PANEL 1: BUY MORE -->
                    <div id="panelReitBuy" class="reit-trans-panel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="buyReitDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity (Units) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="buyReitQty" min="1" step="1" placeholder="e.g. 50" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Buy Price per Unit (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="buyReitPrice" min="0.01" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="buyReitBrokerage" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">STT / Taxes (₹)</label>
                                <input type="number" class="form-control" name="stt_taxes" id="buyReitStt" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Dip accumulation / SIP lot">
                            </div>
                        </div>

                        <!-- Buy Outlay Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Total Outlay:</span>
                                <span class="fw-bold text-success fs-5" id="buyReitTotalPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-PANEL 2: SELL (FIFO) -->
                    <div id="panelReitSell" class="reit-trans-panel d-none">
                        <div class="alert alert-warning py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Available Units to Sell:</span>
                            <strong id="sellReitAvailableBadge">0 units</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Sale Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="sellReitDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity to Sell <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="sellReitQty" min="1" step="1" placeholder="Quantity" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Sell Price per Unit (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="sellReitPrice" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                                <input type="number" class="form-control" name="brokerage" id="sellReitBrokerage" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">STT / Taxes (₹)</label>
                                <input type="number" class="form-control" name="stt_taxes" id="sellReitStt" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Profit booking" disabled>
                            </div>
                        </div>

                        <!-- Sell Proceeds Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Estimated Net Proceeds:</span>
                                <span class="fw-bold text-dark" id="sellReitProceedsPreview">₹ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Realized P&L:</span>
                                <span class="small fw-semibold" id="sellReitPnlPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-PANEL 3: RECORD DISTRIBUTION -->
                    <div id="panelReitDist" class="reit-trans-panel d-none">
                        <div class="alert alert-info py-2 px-3 mb-3 small rounded-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Record Date Eligibility:</strong> Distributions are paid only on units held on the <strong>Record Date</strong>. If you acquired additional units between record date and payout date, or are logging past payouts, adjust <strong>Eligible Held Quantity</strong> to match your distribution statement.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Payout Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="payout_date" id="distPayoutDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Record Date (Optional)</label>
                                <input type="date" class="form-control" name="record_date" id="distRecordDate" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Eligible Held Quantity (Units) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control fw-semibold" name="eligible_units" id="distEligibleUnits" min="1" step="1" placeholder="Units held on Record Date" required disabled>
                                    <span class="input-group-text small text-muted">units</span>
                                </div>
                                <div class="form-text small" style="font-size: 0.72rem;">Units held on Record Date (edit if you bought more later)</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Distribution Per Unit - DPU (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text small text-muted">₹</span>
                                    <input type="number" class="form-control" name="dpu" id="distDpu" min="0" step="0.0001" placeholder="e.g. 5.20" disabled>
                                    <span class="input-group-text small text-muted">/ unit</span>
                                </div>
                                <div class="form-text small" style="font-size: 0.72rem;">Auto-calculates with Gross Amount &amp; Eligible Units</div>
                            </div>

                            <!-- 4 Component Breakdown -->
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Interest (₹)</label>
                                <input type="number" class="form-control dist-component" name="interest_component" id="distInterest" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Dividend (₹)</label>
                                <input type="number" class="form-control dist-component" name="dividend_component" id="distDividend" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Return of Capital (₹)</label>
                                <input type="number" class="form-control dist-component" name="return_of_capital" id="distRoc" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Other Income (₹)</label>
                                <input type="number" class="form-control dist-component" name="other_income" id="distOther" min="0" step="0.01" value="0.00" disabled>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Total Gross Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control fw-semibold" name="total_amount" id="distTotal" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                                <input type="number" class="form-control text-danger" name="tds_deducted" id="distTds" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Net Received in Bank (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control fw-bold text-success" name="net_received" id="distNet" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quarter / Period Description</label>
                                <input type="text" class="form-control" name="quarter_description" placeholder="e.g. Q3 FY 2025-26" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Quarterly distribution credited via NEFT" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitReitTrans">
                        Confirm Purchase Lot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT TRUST METADATA MODAL                                                 -->
<!-- ========================================================================= -->
<div class="modal fade" id="editTrustModal" tabindex="-1" aria-labelledby="editTrustModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editTrustModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Trust Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('reits-invits/update-trust') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="trust_id" id="editTrustId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">Trust / Enterprise Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="trust_name" id="editTrustName" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Trust Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="trust_type" id="editTrustType" required>
                                <option value="REIT">REIT (Real Estate)</option>
                                <option value="INVIT">InvIT (Infrastructure)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NSE / BSE Symbol <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="symbol" id="editTrustSymbol" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ISIN Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="isin" id="editTrustIsin" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Exchange <span class="text-danger">*</span></label>
                            <select class="form-select" name="exchange" id="editTrustExchange" required>
                                <option value="NSE">NSE</option>
                                <option value="BSE">BSE</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Sponsor / Manager</label>
                            <input type="text" class="form-control" name="sponsor" id="editTrustSponsor">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Current Market Price (₹)</label>
                            <input type="number" class="form-control" name="current_price" id="editTrustPrice" min="0.01" step="0.01">
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
<!-- EDIT REIT/INVIT TRANSACTION MODAL (WITH FIFO REBUILD)                     -->
<!-- ========================================================================= -->
<div class="modal fade" id="editReitTransModal" tabindex="-1" aria-labelledby="editReitTransModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editReitTransModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i><span id="editReitTransTitle">Edit Transaction</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('reits-invits/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editReitTransId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Saving changes will automatically <strong>rebalance FIFO lots and recalculate capital gains</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editReitTransDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quantity (Units) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="quantity" id="editReitTransQty" min="1" step="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Price per Unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="editReitTransPrice" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                            <input type="number" class="form-control" name="brokerage" id="editReitTransBrokerage" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">STT / Taxes (₹)</label>
                            <input type="number" class="form-control" name="stt_taxes" id="editReitTransStt" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editReitTransNotes">
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
<!-- EDIT DISTRIBUTION ENTRY MODAL                                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="editReitDistModal" tabindex="-1" aria-labelledby="editReitDistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="editReitDistModalLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i>Edit Distribution Payout
                    </h5>
                    <div class="text-muted small" id="editReitDistSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('reits-invits/update-distribution') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="distribution_id" id="editReitDistId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Payout Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="payout_date" id="editDistPayoutDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Record Date</label>
                            <input type="date" class="form-control" name="record_date" id="editDistRecordDate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Eligible Units Held</label>
                            <input type="number" class="form-control" name="eligible_units" id="editDistEligibleUnits" min="0" step="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">DPU - Distribution Per Unit (₹)</label>
                            <input type="number" class="form-control" name="dpu" id="editDistDpu" min="0" step="0.0001">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Interest Component (₹)</label>
                            <input type="number" class="form-control" name="interest_component" id="editDistInterest" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Dividend Component (₹)</label>
                            <input type="number" class="form-control" name="dividend_component" id="editDistDividend" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Return of Capital (₹)</label>
                            <input type="number" class="form-control" name="return_of_capital" id="editDistRoc" min="0" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Other Income (₹)</label>
                            <input type="number" class="form-control" name="other_income" id="editDistOther" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Total Gross Distribution (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-bold" name="total_amount" id="editDistTotal" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                            <input type="number" class="form-control text-danger" name="tds_deducted" id="editDistTds" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Net Received (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-bold text-success" name="net_received" id="editDistNet" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quarter / Description</label>
                            <input type="text" class="form-control" name="quarter_description" id="editDistQuarter" placeholder="e.g. Q3 FY25 Interim Payout">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editDistNotes">
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
