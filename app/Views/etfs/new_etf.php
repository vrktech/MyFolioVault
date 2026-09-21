<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('etfs') ?>" class="text-decoration-none">ETFs</a></li>
                <li class="breadcrumb-item active" aria-current="page">New ETF Entry</li>
            </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0">Add New ETF Purchase</h3>
        <p class="text-secondary small mb-0">Use this form <strong>only once</strong> when acquiring an ETF for the first time.</p>
    </div>
    <a href="<?= base_url('etfs') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Holdings
    </a>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                <div class="d-flex align-items-center">
                    <div class="p-2 bg-info bg-opacity-10 text-info rounded-3 me-3">
                        <i class="bi bi-pie-chart-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">ETF & Initial Purchase Information</h5>
                        <small class="text-muted">Subsequent buys and sells will use the quick popup modal from the dashboard</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('etfs/new') ?>" method="POST" id="newEtfForm">
                    <?= csrf_field() ?>

                    <!-- Section 1: ETF Details -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-shield-shaded me-1 text-info"></i> 1. ETF Identity
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="symbol" class="form-label small fw-semibold text-secondary">Trading Symbol <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control text-uppercase" 
                                   id="symbol" 
                                   name="symbol" 
                                   placeholder="e.g. NIFTYBEES" 
                                   value="<?= old('symbol') ?>" 
                                   required 
                                   autofocus>
                            <div class="form-text small">NSE/BSE Exchange Ticker</div>
                        </div>

                        <div class="col-md-5">
                            <label for="etf_name" class="form-label small fw-semibold text-secondary">Full ETF Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="etf_name" 
                                   name="etf_name" 
                                   placeholder="e.g. Nippon India ETF Nifty 50 BeES" 
                                   value="<?= old('etf_name') ?>" 
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label for="exchange" class="form-label small fw-semibold text-secondary">Exchange <span class="text-danger">*</span></label>
                            <select class="form-select" id="exchange" name="exchange" required>
                                <option value="NSE" <?= old('exchange', 'NSE') === 'NSE' ? 'selected' : '' ?>>NSE</option>
                                <option value="BSE" <?= old('exchange') === 'BSE' ? 'selected' : '' ?>>BSE</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="category" class="form-label small fw-semibold text-secondary">ETF Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="Index" <?= old('category', 'Index') === 'Index' ? 'selected' : '' ?>>Index (Nifty 50, Next 50, Midcap)</option>
                                <option value="Commodity - Gold" <?= old('category') === 'Commodity - Gold' ? 'selected' : '' ?>>Commodity - Gold (GOLDBEES, etc.)</option>
                                <option value="Commodity - Silver" <?= old('category') === 'Commodity - Silver' ? 'selected' : '' ?>>Commodity - Silver (SILVERBEES, etc.)</option>
                                <option value="Sectoral" <?= old('category') === 'Sectoral' ? 'selected' : '' ?>>Sectoral (Banking, IT, Infra, Auto)</option>
                                <option value="Global" <?= old('category') === 'Global' ? 'selected' : '' ?>>Global / International (MON100, S&P 500)</option>
                                <option value="Debt" <?= old('category') === 'Debt' ? 'selected' : '' ?>>Debt / Liquid (LIQUIDBEES, Bharat Bond)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="amc_name" class="form-label small fw-semibold text-secondary">Fund House / AMC</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="amc_name" 
                                   name="amc_name" 
                                   placeholder="e.g. Nippon India Mutual Fund" 
                                   value="<?= old('amc_name') ?>">
                        </div>
                    </div>

                    <!-- Section 2: Initial Purchase Details (FIFO Lot 1) -->
                    <h6 class="fw-bold text-dark text-uppercase small tracking-wide mb-3 pb-2 border-bottom">
                        <i class="bi bi-cart-check me-1 text-success"></i> 2. Initial Purchase Order (First FIFO Lot)
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="transaction_date" class="form-label small fw-semibold text-secondary">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control" 
                                   id="transaction_date" 
                                   name="transaction_date" 
                                   value="<?= old('transaction_date', date('Y-m-d')) ?>" 
                                   max="<?= date('Y-m-d') ?>" 
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label for="quantity" class="form-label small fw-semibold text-secondary">Units (Quantity) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="quantity" 
                                   name="quantity" 
                                   min="1" 
                                   step="1" 
                                   placeholder="e.g. 100" 
                                   value="<?= old('quantity') ?>" 
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label for="price" class="form-label small fw-semibold text-secondary">Buy Price per Unit (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">₹</span>
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
                        </div>

                        <div class="col-md-6">
                            <label for="brokerage" class="form-label small fw-semibold text-secondary">Brokerage Charges (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="brokerage" 
                                       name="brokerage" 
                                       min="0" 
                                       step="0.01" 
                                       placeholder="0.00" 
                                       value="<?= old('brokerage', '0.00') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="stt_taxes" class="form-label small fw-semibold text-secondary">STT, Stamp Duty & Taxes (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="stt_taxes" 
                                       name="stt_taxes" 
                                       min="0" 
                                       step="0.01" 
                                       placeholder="0.00" 
                                       value="<?= old('stt_taxes', '0.00') ?>">
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label small fw-semibold text-secondary">Notes (Optional)</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="notes" 
                                   name="notes" 
                                   placeholder="e.g. Core asset allocation / Gold hedge" 
                                   value="<?= old('notes') ?>">
                        </div>
                    </div>

                    <!-- Live Calculation Preview Card -->
                    <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Estimated Total Outlay:</span>
                            <span class="fs-5 fw-bold text-info" id="previewTotal">₹ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Effective Cost Per Unit:</span>
                            <span class="small fw-semibold text-dark" id="previewEffectivePrice">₹ 0.00</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i>Save ETF & Add Initial Lot
                        </button>
                        <a href="<?= base_url('etfs') ?>" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                            Cancel
                        </a>
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
        const taxesInput = document.getElementById('stt_taxes');
        const totalElem = document.getElementById('previewTotal');
        const effectiveElem = document.getElementById('previewEffectivePrice');

        function updateOutlay() {
            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const brokerage = parseFloat(brokerageInput.value) || 0;
            const taxes = parseFloat(taxesInput.value) || 0;

            const baseCost = qty * price;
            const totalOutlay = baseCost + brokerage + taxes;
            const effectivePrice = qty > 0 ? (totalOutlay / qty) : 0;

            totalElem.textContent = '₹ ' + totalOutlay.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            effectiveElem.textContent = '₹ ' + effectivePrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        [qtyInput, priceInput, brokerageInput, taxesInput].forEach(inp => {
            inp.addEventListener('input', updateOutlay);
        });
    });
</script>

<?= $this->endSection() ?>

