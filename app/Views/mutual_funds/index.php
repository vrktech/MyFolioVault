<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header & Executive KPI Summary Cards -->
<?= $this->include('mutual_funds/partials/_header_kpis') ?>

<!-- Main Tabs Section -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-3 px-4 rounded-top-4">
        <ul class="nav nav-tabs card-header-tabs border-0" id="mfTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="holdings-tab" data-bs-toggle="tab" data-bs-target="#holdings" type="button" role="tab">
                    <i class="bi bi-briefcase me-1 text-success"></i>Active Holdings (<?= count($activeHoldings ?? $holdings) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="past-holdings-tab" data-bs-toggle="tab" data-bs-target="#past-holdings" type="button" role="tab">
                    <i class="bi bi-archive me-1 text-secondary"></i>Past Holdings (<?= count($pastHoldings ?? []) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
                    <i class="bi bi-clock-history me-1"></i>Trade Ledger (<?= count($transactions) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="capitalgains-tab" data-bs-toggle="tab" data-bs-target="#capitalgains" type="button" role="tab">
                    <i class="bi bi-receipt-cutoff me-1 text-primary"></i>FIFO Tax Log (STCG / LTCG) (<?= count($capitalGains) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="mfTabContent">
            <!-- TAB 1: ACTIVE HOLDINGS -->
            <?= $this->include('mutual_funds/partials/_active_holdings') ?>

            <!-- TAB 2: PAST HOLDINGS -->
            <?= $this->include('mutual_funds/partials/_past_holdings') ?>

            <!-- TAB 3: TRANSACTIONS (TRADE LEDGER) -->
            <?= $this->include('mutual_funds/partials/_trade_ledger') ?>

            <!-- TAB 4: FIFO CAPITAL GAINS TAX LOG -->
            <?= $this->include('mutual_funds/partials/_capital_gains_tab') ?>
        </div>
    </div>
</div>

<!-- Modals -->
<?= $this->include('mutual_funds/partials/_modals') ?>

<script>
    window.MUTUAL_FUNDS_CONFIG = {
        fyRanges: <?= json_encode(get_fy_ranges()) ?>
    };
</script>
<script src="<?= base_url('assets/js/mutual_funds.js') ?>"></script>

<?= $this->endSection() ?>
