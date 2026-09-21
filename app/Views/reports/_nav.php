<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-2 p-md-3">
        <ul class="nav nav-pills nav-fill flex-column flex-sm-row gap-2" id="reportsNavTab">
            <li class="nav-item">
                <a class="nav-link rounded-3 py-2.5 px-3 fw-semibold text-start text-sm-center d-flex align-items-center justify-content-sm-center gap-2 <?= ($activeTab ?? '') === 'cashflow' ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' ?>" 
                   href="<?= base_url('reports/cashflow' . (isset($range['key']) ? '?fy=' . $range['key'] : '')) ?>">
                    <i class="bi bi-wallet2 fs-5"></i>
                    <span>Cash Flow &amp; Activity</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-3 py-2.5 px-3 fw-semibold text-start text-sm-center d-flex align-items-center justify-content-sm-center gap-2 <?= ($activeTab ?? '') === 'tax' ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' ?>" 
                   href="<?= base_url('reports/tax' . (isset($range['key']) ? '?fy=' . $range['key'] : '')) ?>">
                    <i class="bi bi-percent fs-5"></i>
                    <span>Capital Gains &amp; Tax (LTCG / STCG)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-3 py-2.5 px-3 fw-semibold text-start text-sm-center d-flex align-items-center justify-content-sm-center gap-2 <?= ($activeTab ?? '') === 'income' ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' ?>" 
                   href="<?= base_url('reports/income' . (isset($range['key']) ? '?fy=' . $range['key'] : '')) ?>">
                    <i class="bi bi-cash-coin fs-5"></i>
                    <span>Passive Income &amp; Yield</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-3 py-2.5 px-3 fw-semibold text-start text-sm-center d-flex align-items-center justify-content-sm-center gap-2 <?= ($activeTab ?? '') === 'expenses' ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' ?>" 
                   href="<?= base_url('reports/expenses' . (isset($range['key']) ? '?fy=' . $range['key'] : '')) ?>">
                    <i class="bi bi-receipt-cutoff fs-5"></i>
                    <span>Expenses &amp; Friction</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-3 py-2.5 px-3 fw-semibold text-start text-sm-center d-flex align-items-center justify-content-sm-center gap-2 <?= ($activeTab ?? '') === 'allocation' ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' ?>" 
                   href="<?= base_url('reports/allocation') ?>">
                    <i class="bi bi-pie-chart-fill fs-5"></i>
                    <span>Asset Allocation</span>
                </a>
            </li>
        </ul>
    </div>
</div>

