<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header & Executive KPI Summary Cards -->
<?= $this->include('etfs/partials/_header_kpis') ?>

<!-- Main Tabs Section -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-3 px-4 rounded-top-4">
        <ul class="nav nav-tabs card-header-tabs border-0" id="etfsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="holdings-tab" data-bs-toggle="tab" data-bs-target="#holdings" type="button" role="tab">
                    <i class="bi bi-pie-chart me-1 text-info"></i>Active Holdings (<?= count($activeHoldings ?? $holdings) ?>)
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
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="corporate-actions-tab" data-bs-toggle="tab" data-bs-target="#corporate-actions" type="button" role="tab">
                    <i class="bi bi-diagram-3 me-1 text-warning"></i>Splits &amp; Consolidation (<?= count($corporateActions ?? []) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="etfsTabContent">
            <!-- TAB 1: ACTIVE HOLDINGS -->
            <?= $this->include('etfs/partials/_active_holdings') ?>

            <!-- TAB 2: PAST HOLDINGS -->
            <?= $this->include('etfs/partials/_past_holdings') ?>

            <!-- TAB 3: TRANSACTIONS (TRADE LEDGER) -->
            <?= $this->include('etfs/partials/_trade_ledger') ?>

            <!-- TAB 4: FIFO TAX AUDIT LOG (CAPITAL GAINS) -->
            <?= $this->include('etfs/partials/_capital_gains_tab') ?>

            <!-- TAB 5: CORPORATE ACTIONS (SPLITS) -->
            <?= $this->include('etfs/partials/_corporate_actions_tab') ?>
        </div>
    </div>
</div>

<!-- Modals -->
<?= $this->include('etfs/partials/_modals') ?>

<script>
    window.ETFS_CONFIG = {
        fyRanges: <?= json_encode(get_fy_ranges()) ?>,
        checkSplitUrl: '<?= base_url('etfs/check-split-eligibility') ?>'
    };
</script>
<script src="<?= base_url('assets/js/etfs.js') ?>"></script>

<?= $this->endSection() ?>
