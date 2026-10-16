<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header, Action Buttons, Executive KPIs & Asset Allocation Bar -->
<?= $this->include('nps/partials/_header_kpis') ?>

<!-- Main Tabs Section -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-3 px-4 rounded-top-4">
        <ul class="nav nav-tabs card-header-tabs border-0" id="npsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="schemes-tab" data-bs-toggle="tab" data-bs-target="#schemes" type="button" role="tab">
                    <i class="bi bi-pie-chart me-1 text-primary"></i>Scheme Portfolios (E, C, G, A)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="contributions-tab" data-bs-toggle="tab" data-bs-target="#contributions" type="button" role="tab">
                    <i class="bi bi-clock-history me-1"></i>Contributions Ledger (<?= count($contributions) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="deductions-tab" data-bs-toggle="tab" data-bs-target="#deductions" type="button" role="tab">
                    <i class="bi bi-receipt-cutoff me-1 text-danger"></i>Quarterly Fee Deductions (<?= count($deductions) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="npsTabContent">
            <!-- TAB 1: SCHEME PORTFOLIOS -->
            <?= $this->include('nps/partials/_schemes_tab') ?>

            <!-- TAB 2: CONTRIBUTIONS LEDGER -->
            <?= $this->include('nps/partials/_contributions_tab') ?>

            <!-- TAB 3: QUARTERLY FEE DEDUCTIONS -->
            <?= $this->include('nps/partials/_deductions_tab') ?>
        </div>
    </div>
</div>

<!-- Modals -->
<?= $this->include('nps/partials/_modals') ?>

<script>
    window.NPS_CONFIG = {
        allocations: {
            equity: <?= (float)($account['alloc_equity'] ?? 0) ?>,
            corporateDebt: <?= (float)($account['alloc_corporate_debt'] ?? 0) ?>,
            govtBonds: <?= (float)($account['alloc_govt_bonds'] ?? 0) ?>,
            alternative: <?= (float)($account['alloc_alternative'] ?? 0) ?>
        }
    };
</script>
<script src="<?= base_url('assets/js/nps.js') ?>"></script>

<?= $this->endSection() ?>
