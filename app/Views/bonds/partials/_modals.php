<!-- ========================================================================= -->
<!-- MINIMAL BOND TRANSACTION POPUP MODAL (Buy / Sell / Interest / Redeem)     -->
<!-- ========================================================================= -->
<div class="modal fade" id="bondTransactionModal" tabindex="-1" aria-labelledby="bondTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="bondTransactionModalLabel">
                        Add Transaction: <span id="modalBondName" class="text-danger"></span>
                    </h5>
                    <div class="text-muted small" id="modalBondSubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/transaction') ?>" method="POST" id="bondTransForm">
                <?= csrf_field() ?>
                <input type="hidden" name="bond_id" id="modalBondId" value="">

                <div class="modal-body p-4">
                    <!-- Segmented 4-way Action Switcher -->
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" name="action_type" id="actBondBuy" value="BUY" checked autocomplete="off">
                        <label class="btn btn-outline-success fw-semibold" for="actBondBuy">
                            <i class="bi bi-cart-plus me-1"></i>Buy More
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actBondSell" value="SELL" autocomplete="off">
                        <label class="btn btn-outline-danger fw-semibold" for="actBondSell">
                            <i class="bi bi-cart-dash me-1"></i>Sell (FIFO)
                        </label>

                        <input type="radio" class="btn-check" name="action_type" id="actBondInterest" value="INTEREST" autocomplete="off">
                        <label class="btn btn-outline-primary fw-semibold" for="actBondInterest">
                            <i class="bi bi-cash-coin me-1"></i>Record Interest
                        </label>
                    </div>

                    <!-- SUB-PANEL 1: BUY MORE -->
                    <div id="panelBondBuy" class="bond-trans-panel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="buyBondDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity (Units / Grams) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="buyBondQty" min="0.0001" step="0.0001" placeholder="Quantity" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Buy Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="buyBondPrice" min="0.01" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Brokerage / Taxes (₹)</label>
                                <input type="number" class="form-control" name="brokerage_charges" id="buyBondCharges" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Accrued Interest (₹)</label>
                                <input type="number" class="form-control" name="accrued_interest" id="buyBondAccrued" min="0" step="0.01" value="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Secondary market tranche accumulation">
                            </div>
                        </div>

                        <!-- Buy Outlay Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Total Outlay:</span>
                                <span class="fw-bold text-success fs-5" id="buyBondTotalPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-PANEL 2: SELL (FIFO) -->
                    <div id="panelBondSell" class="bond-trans-panel d-none">
                        <div class="alert alert-warning py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Available Units to Sell:</span>
                            <strong id="sellBondAvailableBadge">0.0000</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Sale Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date" id="sellBondDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Quantity to Sell <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="sellBondQty" min="0.0001" step="0.0001" placeholder="Quantity" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Sell Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="price" id="sellBondPrice" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Brokerage / Charges (₹)</label>
                                <input type="number" class="form-control" name="brokerage_charges" id="sellBondCharges" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Secondary market exit" disabled>
                            </div>
                        </div>

                        <!-- Sell Proceeds Preview -->
                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted">Estimated Net Proceeds:</span>
                                <span class="fw-bold text-dark" id="sellBondProceedsPreview">₹ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Estimated Realized P&L:</span>
                                <span class="small fw-semibold" id="sellBondPnlPreview">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-PANEL 3: RECORD INTEREST PAID -->
                    <div id="panelBondInterest" class="bond-trans-panel d-none">
                        <div class="alert alert-success py-1.5 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                            <span>Coupon Interest:</span>
                            <span id="interestBondCalcBadge">0.00%</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Payout Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="payout_date" id="payoutBondDate" value="<?= date('Y-m-d') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Gross Coupon Interest (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="gross_interest" id="interestGross" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                                <input type="number" class="form-control" name="tds_deducted" id="interestTds" min="0" step="0.01" value="0.00" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Net Interest Credited (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="net_interest" id="interestNet" min="0.01" step="0.01" placeholder="0.00" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Coupon Period Description</label>
                                <input type="text" class="form-control" name="period_description" placeholder="e.g. Semi-Annual Coupon (Nov 2025 - May 2026)" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                                <input type="text" class="form-control" name="notes" placeholder="e.g. Direct RBI ECS credit to bank" disabled>
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                            <div class="small text-muted">
                                <i class="bi bi-info-circle me-1"></i>Recording interest will update the bond's <strong>Last Interest Paid Date</strong> on the dashboard. Units held remain unchanged.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold" id="btnSubmitBondTrans">
                        Confirm Purchase Lot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- DEDICATED BOND MATURITY REDEMPTION MODAL (Enabled Only Post-Maturity)     -->
<!-- ========================================================================= -->
<div class="modal fade" id="bondMaturityRedemptionModal" tabindex="-1" aria-labelledby="bondMaturityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-dark text-white rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="bondMaturityModalLabel">
                        <i class="bi bi-award-fill text-warning me-2"></i>Maturity Redemption: <span id="maturityModalBondName" class="text-warning"></span>
                    </h5>
                    <div class="text-white-50 small" id="maturityModalBondSubtitle"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/transaction') ?>" method="POST" id="bondMaturityForm">
                <?= csrf_field() ?>
                <input type="hidden" name="action_type" value="REDEMPTION">
                <input type="hidden" name="bond_id" id="maturityRedeemBondId" value="">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Redemption Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="redemption_date" id="maturityRedeemDate" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Redeemed Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" id="maturityRedeemQty" min="0.0001" step="0.0001" placeholder="Quantity" required>
                            <div class="form-text small" id="maturityMaxQtyHint"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Redemption Price per Unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="redemption_price" id="maturityRedeemPrice" min="0.01" step="0.01" placeholder="0.00" required>
                            <div class="form-text small">RBI gold price for SGB, Face Value for G-Sec/NCD</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                            <input type="text" class="form-control" name="notes" id="maturityRedeemNotes" placeholder="e.g. RBI SGB Final Maturity Redemption">
                        </div>
                    </div>

                    <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Total Principal Amount Received:</span>
                            <span class="fw-bold text-dark fs-5" id="maturityProceedsPreview">₹ 0.00</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark btn-sm px-4 fw-semibold" id="btnSubmitMaturityRedeem">
                        <i class="bi bi-check2-circle me-1"></i>Confirm Maturity Redemption
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- QUICK UPDATE CMP MODAL                                                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="bondPriceModal" tabindex="-1" aria-labelledby="bondPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h6 class="modal-title fw-bold text-dark" id="bondPriceModalLabel">Update Market Price</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/update-price') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="bond_id" id="priceModalBondId" value="">

                <div class="modal-body p-4">
                    <div class="mb-2 fw-semibold text-dark text-truncate" id="priceModalBondName"></div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Current Market Price (₹)</label>
                        <input type="number" class="form-control" name="current_market_price" id="priceModalPrice" min="0.01" step="0.01" required>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-secondary">Price Date</label>
                        <input type="date" class="form-control" name="cmp_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-semibold">Update Price</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- EDIT BOND METADATA MODAL                                                  -->
<!-- ========================================================================= -->
<div class="modal fade" id="editBondModal" tabindex="-1" aria-labelledby="editBondModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editBondModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Bond Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/update-bond') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="bond_id" id="editBondId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">Bond / Instrument Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="bond_name" id="editBondName" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" id="editBondCategory" required>
                                <option value="CORPORATE_NCD">Corporate Bond / NCD</option>
                                <option value="GOVT_SECURITY">Government Security (G-Sec / SDL)</option>
                                <option value="SGB">Sovereign Gold Bond (SGB)</option>
                                <option value="TAX_FREE">Tax-Free PSU Bond</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">ISIN Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="isin" id="editBondIsin" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Trading Symbol / Ticker</label>
                            <input type="text" class="form-control font-monospace text-uppercase" name="bond_symbol" id="editBondSymbol">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Issuer / Authority</label>
                            <input type="text" class="form-control" name="issuer" id="editBondIssuer" placeholder="e.g. Reserve Bank of India, NHAI">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Face Value (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="face_value" id="editBondFaceValue" min="1" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Coupon Rate (% p.a.) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="coupon_rate" id="editBondCouponRate" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Interest Frequency <span class="text-danger">*</span></label>
                            <select class="form-select" name="interest_frequency" id="editBondFrequency" required>
                                <option value="MONTHLY">MONTHLY</option>
                                <option value="QUARTERLY">QUARTERLY</option>
                                <option value="SEMI_ANNUAL">SEMI_ANNUAL</option>
                                <option value="ANNUAL">ANNUAL</option>
                                <option value="CUMULATIVE">CUMULATIVE</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Issue Date</label>
                            <input type="date" class="form-control" name="issue_date" id="editBondIssueDate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Maturity Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="maturity_date" id="editBondMaturityDate" required>
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
<!-- EDIT BOND TRANSACTION MODAL (WITH FIFO REBUILD)                           -->
<!-- ========================================================================= -->
<div class="modal fade" id="editBondTransModal" tabindex="-1" aria-labelledby="editBondTransModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editBondTransModalLabel">
                    <i class="bi bi-pencil-square text-primary me-2"></i><span id="editBondTransTitle">Edit Transaction</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/update-transaction') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="editBondTransId" value="">

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Saving changes will automatically <strong>rebalance FIFO lots and recalculate capital gains</strong>.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="transaction_date" id="editBondTransDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Quantity (Units / Grams) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="quantity" id="editBondTransQty" min="0.0001" step="0.0001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Price per Unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="editBondTransPrice" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Brokerage / Charges (₹)</label>
                            <input type="number" class="form-control" name="brokerage_charges" id="editBondTransCharges" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Accrued Interest Paid (₹)</label>
                            <input type="number" class="form-control" name="accrued_interest" id="editBondTransAccrued" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editBondTransNotes">
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
<!-- EDIT BOND INTEREST PAYOUT MODAL                                           -->
<!-- ========================================================================= -->
<div class="modal fade" id="editBondInterestModal" tabindex="-1" aria-labelledby="editBondInterestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="editBondInterestModalLabel">
                    <i class="bi bi-cash-coin text-success me-2"></i><span id="editBondIntTitle">Edit Interest Payment</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= base_url('bonds/update-interest') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="interest_id" id="editBondIntId" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Payout Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="payout_date" id="editBondIntDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Coupon Rate (% p.a.)</label>
                            <input type="number" class="form-control" name="coupon_rate" id="editBondIntRate" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Gross Interest (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fw-semibold" name="gross_interest" id="editBondIntGross" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TDS Deducted (₹)</label>
                            <input type="number" class="form-control text-danger" name="tds_deducted" id="editBondIntTds" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Period / Description</label>
                            <input type="text" class="form-control" name="period_description" id="editBondIntDesc" placeholder="e.g. H1 FY26-27 Semi-Annual Coupon">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Notes</label>
                            <input type="text" class="form-control" name="notes" id="editBondIntNotes">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">Save Interest Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
