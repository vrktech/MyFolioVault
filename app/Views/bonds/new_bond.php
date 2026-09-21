<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('bonds') ?>" class="text-decoration-none">Bonds</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Bond Entry</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">Add New Bond Security</h3>
        <p class="text-secondary small mb-0">Use this form <strong>only once</strong> when acquiring a new bond instrument for the first time.</p>
    </div>
    <a href="<?= base_url('bonds') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Holdings
    </a>
</div>

<div class="row">
    <div class="col-lg-9 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                <div class="d-flex align-items-center">
                    <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-3 me-3">
                        <i class="bi bi-receipt fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Bond Security & Initial Purchase Details</h5>
                        <small class="text-muted">Subsequent purchases, secondary sales, coupon payouts, and redemptions will use the dashboard popup modal</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('bonds/new') ?>" method="POST" id="newBondForm">
                    <?= csrf_field() ?>

                    <!-- Section 1: Bond Identification -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-shield-check me-1 text-primary"></i> 1. Security Identity & Issuer
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label for="bond_name" class="form-label small fw-semibold text-secondary">Bond Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="bond_name" 
                                   name="bond_name" 
                                   placeholder="e.g. SGB 2021-22 Series VIII or 7.18% GS 2033" 
                                   value="<?= old('bond_name') ?>" 
                                   required 
                                   autofocus>
                        </div>

                        <div class="col-md-5">
                            <label for="isin" class="form-label small fw-semibold text-secondary">ISIN Code <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control font-monospace text-uppercase" 
                                   id="isin" 
                                   name="isin" 
                                   placeholder="e.g. IN0020210194" 
                                   maxlength="12" 
                                   value="<?= old('isin') ?>" 
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label for="category" class="form-label small fw-semibold text-secondary">Bond Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="SGB" <?= old('category') === 'SGB' ? 'selected' : '' ?>>Sovereign Gold Bond (SGB)</option>
                                <option value="GOVT_SECURITY" <?= old('category') === 'GOVT_SECURITY' ? 'selected' : '' ?>>Government Security (G-Sec / SDL)</option>
                                <option value="CORPORATE_NCD" <?= old('category') === 'CORPORATE_NCD' ? 'selected' : '' ?>>Corporate NCD / Debenture</option>
                                <option value="TAX_FREE" <?= old('category') === 'TAX_FREE' ? 'selected' : '' ?>>Tax-Free PSU Bond</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="bond_symbol" class="form-label small fw-semibold text-secondary">Trading Symbol / Ticker</label>
                            <input type="text" 
                                   class="form-control text-uppercase" 
                                   id="bond_symbol" 
                                   name="bond_symbol" 
                                   placeholder="e.g. SGBNOV29, 718GS2033" 
                                   value="<?= old('bond_symbol') ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="issuer" class="form-label small fw-semibold text-secondary">Issuer / Authority <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="issuer" 
                                   name="issuer" 
                                   placeholder="e.g. RBI, Tata Capital Ltd, NHAI" 
                                   value="<?= old('issuer') ?>" 
                                   required>
                        </div>
                    </div>

                    <!-- Section 2: Interest Terms & Maturity -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-calendar-check me-1 text-primary"></i> 2. Interest Coupon & Maturity Terms
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="face_value" class="form-label small fw-semibold text-secondary">Face Value (₹) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="face_value" 
                                   name="face_value" 
                                   min="1" 
                                   step="0.01" 
                                   value="<?= old('face_value', '1000.00') ?>" 
                                   required>
                            <div class="form-text small">Issue price for SGB, ₹100 for G-Sec, ₹1000 for NCD</div>
                        </div>

                        <div class="col-md-3">
                            <label for="coupon_rate" class="form-label small fw-semibold text-secondary">Coupon Rate (% p.a.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="coupon_rate" 
                                       name="coupon_rate" 
                                       min="0" 
                                       step="0.01" 
                                       placeholder="e.g. 7.18 or 2.50" 
                                       value="<?= old('coupon_rate') ?>" 
                                       required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="interest_frequency" class="form-label small fw-semibold text-secondary">Interest Frequency <span class="text-danger">*</span></label>
                            <select class="form-select" id="interest_frequency" name="interest_frequency" required>
                                <option value="MONTHLY" <?= old('interest_frequency') === 'MONTHLY' ? 'selected' : '' ?>>Monthly</option>
                                <option value="QUARTERLY" <?= old('interest_frequency') === 'QUARTERLY' ? 'selected' : '' ?>>Quarterly</option>
                                <option value="SEMI_ANNUAL" <?= old('interest_frequency', 'SEMI_ANNUAL') === 'SEMI_ANNUAL' ? 'selected' : '' ?>>Semi-Annual (Half-Yearly)</option>
                                <option value="ANNUAL" <?= old('interest_frequency') === 'ANNUAL' ? 'selected' : '' ?>>Annual (Yearly)</option>
                                <option value="CUMULATIVE" <?= old('interest_frequency') === 'CUMULATIVE' ? 'selected' : '' ?>>Cumulative (At Maturity)</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="maturity_date" class="form-label small fw-semibold text-secondary">Maturity Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control" 
                                   id="maturity_date" 
                                   name="maturity_date" 
                                   value="<?= old('maturity_date') ?>" 
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label for="issue_date" class="form-label small fw-semibold text-secondary">Issue Date (Optional)</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="issue_date" 
                                   name="issue_date" 
                                   value="<?= old('issue_date') ?>">
                        </div>
                    </div>

                    <!-- Section 3: Initial Purchase Order -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-cart-check me-1 text-primary"></i> 3. Initial Purchase Order (Lot 1)
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="transaction_date" class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control" 
                                   id="transaction_date" 
                                   name="transaction_date" 
                                   value="<?= old('transaction_date', date('Y-m-d')) ?>" 
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label for="quantity" class="form-label small fw-semibold text-secondary">Quantity (Units / Grams) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="quantity" 
                                   name="quantity" 
                                   min="0.0001" 
                                   step="0.0001" 
                                   placeholder="Units / Grams" 
                                   value="<?= old('quantity') ?>" 
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label for="price" class="form-label small fw-semibold text-secondary">Purchase Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="price" 
                                   name="price" 
                                   min="0.01" 
                                   step="0.01" 
                                   placeholder="0.00" 
                                   value="<?= old('price') ?>" 
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label for="brokerage_charges" class="form-label small fw-semibold text-secondary">Brokerage / Taxes (₹)</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="brokerage_charges" 
                                   name="brokerage_charges" 
                                   min="0" 
                                   step="0.01" 
                                   value="<?= old('brokerage_charges', '0.00') ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="accrued_interest" class="form-label small fw-semibold text-secondary">Accrued Interest Paid (₹)</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="accrued_interest" 
                                   name="accrued_interest" 
                                   min="0" 
                                   step="0.01" 
                                   value="<?= old('accrued_interest', '0.00') ?>">
                            <div class="form-text small">If bought in secondary market with dirty price</div>
                        </div>

                        <div class="col-md-8">
                            <label for="notes" class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="notes" 
                                   name="notes" 
                                   placeholder="e.g. Primary RBI Retail Direct tranche allotment" 
                                   value="<?= old('notes') ?>">
                        </div>
                    </div>

                    <!-- Total Outlay Summary Card -->
                    <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Estimated Total Outlay:</span>
                                <div class="text-secondary small" style="font-size: 0.75rem;">(Qty &times; Price) + Charges + Accrued Interest</div>
                            </div>
                            <span class="fs-4 fw-bold text-success" id="totalOutlayPreview">₹ 0.00</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('bonds') ?>" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold" id="btnSubmitBond">
                            <i class="bi bi-check-lg me-1"></i>Save & Setup Bond Security
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('quantity');
    const priceInput = document.getElementById('price');
    const chargesInput = document.getElementById('brokerage_charges');
    const accruedInput = document.getElementById('accrued_interest');
    const preview = document.getElementById('totalOutlayPreview');

    function calcTotal() {
        const q = parseFloat(qtyInput.value) || 0;
        const p = parseFloat(priceInput.value) || 0;
        const c = parseFloat(chargesInput.value) || 0;
        const a = parseFloat(accruedInput.value) || 0;
        const total = (q * p) + c + a;
        preview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    [qtyInput, priceInput, chargesInput, accruedInput].forEach(el => {
        el.addEventListener('input', calcTotal);
    });
});
</script>

<?= $this->endSection() ?>

