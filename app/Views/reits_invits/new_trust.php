<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('reits-invits') ?>" class="text-decoration-none">InvITs & REITs</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Trust Entry</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">Add New REIT / InvIT Holding</h3>
        <p class="text-secondary small mb-0">Use this form <strong>only once</strong> when adding a new Real Estate or Infrastructure trust to your portfolio.</p>
    </div>
    <a href="<?= base_url('reits-invits') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Holdings
    </a>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                <div class="d-flex align-items-center">
                    <div class="p-2 bg-teal bg-opacity-10 text-teal rounded-3 me-3" style="color: #0d9488;">
                        <i class="bi bi-building fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Trust Identity & Initial Purchase Order</h5>
                        <small class="text-muted">Subsequent purchases, sales, and quarterly distributions will use the dashboard popup modal</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('reits-invits/new') ?>" method="POST" id="newTrustForm">
                    <?= csrf_field() ?>

                    <!-- Section 1: Trust Identity -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-shield-check me-1 text-primary"></i> 1. Security Identity & Exchange
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="symbol" class="form-label small fw-semibold text-secondary">Trading Symbol <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control text-uppercase fw-bold" 
                                   id="symbol" 
                                   name="symbol" 
                                   placeholder="e.g. EMBASSY or PGINVIT" 
                                   value="<?= old('symbol') ?>" 
                                   required 
                                   autofocus>
                            <div class="form-text small">NSE / BSE ticker symbol</div>
                        </div>

                        <div class="col-md-5">
                            <label for="trust_name" class="form-label small fw-semibold text-secondary">Trust Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="trust_name" 
                                   name="trust_name" 
                                   placeholder="e.g. Embassy Office Parks REIT" 
                                   value="<?= old('trust_name') ?>" 
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label for="trust_type" class="form-label small fw-semibold text-secondary">Trust Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="trust_type" name="trust_type" required>
                                <option value="REIT" <?= old('trust_type') === 'REIT' ? 'selected' : '' ?>>REIT (Real Estate)</option>
                                <option value="INVIT" <?= old('trust_type') === 'INVIT' ? 'selected' : '' ?>>InvIT (Infrastructure)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="isin" class="form-label small fw-semibold text-secondary">ISIN Code <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control font-monospace text-uppercase" 
                                   id="isin" 
                                   name="isin" 
                                   placeholder="e.g. INE041025011" 
                                   maxlength="12" 
                                   value="<?= old('isin') ?>" 
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label for="exchange" class="form-label small fw-semibold text-secondary">Exchange <span class="text-danger">*</span></label>
                            <select class="form-select" id="exchange" name="exchange" required>
                                <option value="NSE" <?= old('exchange') === 'NSE' ? 'selected' : '' ?>>NSE (National Stock Exchange)</option>
                                <option value="BSE" <?= old('exchange') === 'BSE' ? 'selected' : '' ?>>BSE (Bombay Stock Exchange)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="sponsor" class="form-label small fw-semibold text-secondary">Sponsor / Manager</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="sponsor" 
                                   name="sponsor" 
                                   placeholder="e.g. Blackstone, PowerGrid" 
                                   value="<?= old('sponsor') ?>">
                        </div>
                    </div>

                    <!-- Section 2: Initial Purchase Order -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-cart-check me-1 text-primary"></i> 2. Initial Purchase Order (Lot 1)
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="transaction_date" class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control" 
                                   id="transaction_date" 
                                   name="transaction_date" 
                                   value="<?= old('transaction_date', date('Y-m-d')) ?>" 
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label for="quantity" class="form-label small fw-semibold text-secondary">Number of Units <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="quantity" 
                                   name="quantity" 
                                   min="1" 
                                   step="1" 
                                   placeholder="e.g. 200" 
                                   value="<?= old('quantity') ?>" 
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label for="price" class="form-label small fw-semibold text-secondary">Purchase Price per Unit (₹) <span class="text-danger">*</span></label>
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

                        <div class="col-md-4">
                            <label for="brokerage" class="form-label small fw-semibold text-secondary">Brokerage (₹)</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="brokerage" 
                                   name="brokerage" 
                                   min="0" 
                                   step="0.01" 
                                   value="<?= old('brokerage', '0.00') ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="stt_taxes" class="form-label small fw-semibold text-secondary">STT & Other Taxes (₹)</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="stt_taxes" 
                                   name="stt_taxes" 
                                   min="0" 
                                   step="0.01" 
                                   value="<?= old('stt_taxes', '0.00') ?>">
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="notes" 
                                   name="notes" 
                                   placeholder="e.g. Initial REIT allocation" 
                                   value="<?= old('notes') ?>">
                        </div>
                    </div>

                    <!-- Total Outlay Summary Card -->
                    <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Estimated Total Outlay:</span>
                                <div class="text-secondary small" style="font-size: 0.75rem;">(Units &times; Price) + Brokerage + STT</div>
                            </div>
                            <span class="fs-4 fw-bold text-success" id="totalOutlayPreview">₹ 0.00</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('reits-invits') ?>" class="btn btn-outline-secondary rounded-3 px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold" id="btnSubmitTrust">
                            <i class="bi bi-check-lg me-1"></i>Save & Setup Trust Security
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
    const brokerageInput = document.getElementById('brokerage');
    const sttInput = document.getElementById('stt_taxes');
    const preview = document.getElementById('totalOutlayPreview');

    function calcTotal() {
        const q = parseFloat(qtyInput.value) || 0;
        const p = parseFloat(priceInput.value) || 0;
        const b = parseFloat(brokerageInput.value) || 0;
        const s = parseFloat(sttInput.value) || 0;
        const total = (q * p) + b + s;
        preview.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    [qtyInput, priceInput, brokerageInput, sttInput].forEach(el => {
        el.addEventListener('input', calcTotal);
    });
});
</script>

<?= $this->endSection() ?>

