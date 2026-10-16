<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header & Executive KPI Summary Cards -->
<?= $this->include('reits_invits/partials/_header_kpis') ?>

<!-- Main Tabs Section -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-3 px-4 rounded-top-4">
        <ul class="nav nav-tabs card-header-tabs border-0" id="reitsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="holdings-tab" data-bs-toggle="tab" data-bs-target="#holdings" type="button" role="tab">
                    <i class="bi bi-buildings me-1 text-info"></i>Active Holdings (<?= count($activeHoldings ?? $holdings) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="past-holdings-tab" data-bs-toggle="tab" data-bs-target="#past-holdings" type="button" role="tab">
                    <i class="bi bi-archive me-1 text-secondary"></i>Past Holdings (<?= count($pastHoldings ?? []) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="distributions-tab" data-bs-toggle="tab" data-bs-target="#distributions" type="button" role="tab">
                    <i class="bi bi-cash-coin me-1 text-success"></i>Distribution Journal (<?= count($distributions) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="capitalgains-tab" data-bs-toggle="tab" data-bs-target="#capitalgains" type="button" role="tab">
                    <i class="bi bi-clock-history me-1 text-primary"></i>FIFO Tax Log (<?= count($capitalGains) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
                    <i class="bi bi-receipt me-1 text-secondary"></i>Transaction History (<?= count($transactions) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="reitsTabContent">
            <!-- TAB 1: ACTIVE HOLDINGS -->
            <?= $this->include('reits_invits/partials/_active_holdings') ?>

            <!-- TAB 2: PAST HOLDINGS -->
            <?= $this->include('reits_invits/partials/_past_holdings') ?>

            <!-- TAB 3: DISTRIBUTION JOURNAL -->
            <?= $this->include('reits_invits/partials/_distributions_tab') ?>

            <!-- TAB 4: CAPITAL GAINS (FIFO AUDIT LOG) -->
            <?= $this->include('reits_invits/partials/_capital_gains_tab') ?>

            <!-- TAB 5: TRANSACTION HISTORY -->
            <?= $this->include('reits_invits/partials/_trade_ledger') ?>
        </div>
    </div>
</div>

<!-- Modals -->
<?= $this->include('reits_invits/partials/_modals') ?>

<script>
    window.REITS_CONFIG = {
        fyRanges: <?= json_encode(get_fy_ranges()) ?>
    };
</script>
<script src="<?= base_url('assets/js/reits_invits.js') ?>"></script>

<?= $this->endSection() ?>
