<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header & Executive KPI Summary Cards -->
<?= $this->include('bonds/partials/_header_kpis') ?>

<!-- Main Tabs Section -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom pt-3 px-4 rounded-top-4">
        <ul class="nav nav-tabs card-header-tabs border-0" id="bondsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="holdings-tab" data-bs-toggle="tab" data-bs-target="#holdings" type="button" role="tab">
                    <i class="bi bi-receipt me-1 text-danger"></i>Active Holdings (<?= count($activeHoldings ?? $holdings) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="past-holdings-tab" data-bs-toggle="tab" data-bs-target="#past-holdings" type="button" role="tab">
                    <i class="bi bi-archive me-1 text-secondary"></i>Past Holdings (<?= count($pastHoldings ?? []) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="payouts-tab" data-bs-toggle="tab" data-bs-target="#payouts" type="button" role="tab">
                    <i class="bi bi-cash-coin me-1 text-success"></i>Interest Payouts Journal (<?= count($payouts) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="capitalgains-tab" data-bs-toggle="tab" data-bs-target="#capitalgains" type="button" role="tab">
                    <i class="bi bi-clock-history me-1 text-primary"></i>FIFO Tax &amp; Redemption Log (<?= count($capitalGains) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
                    <i class="bi bi-card-checklist me-1 text-dark"></i>Trade Ledger (<?= count($transactions) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content" id="bondsTabContent">
            <!-- TAB 1: ACTIVE HOLDINGS -->
            <?= $this->include('bonds/partials/_active_holdings') ?>

            <!-- TAB 2: PAST HOLDINGS -->
            <?= $this->include('bonds/partials/_past_holdings') ?>

            <!-- TAB 3: INTEREST PAYOUTS JOURNAL -->
            <?= $this->include('bonds/partials/_interest_journal') ?>

            <!-- TAB 4: FIFO TAX AUDIT LOG & REDEMPTIONS -->
            <?= $this->include('bonds/partials/_tax_log') ?>

            <!-- TAB 5: TRANSACTIONS (TRADE LEDGER) -->
            <?= $this->include('bonds/partials/_trade_ledger') ?>
        </div>
    </div>
</div>

<!-- Modals -->
<?= $this->include('bonds/partials/_modals') ?>

<script>
    window.BONDS_CONFIG = {
        fyRanges: <?= json_encode(get_fy_ranges()) ?>
    };
</script>
<script src="<?= base_url('assets/js/bonds.js') ?>"></script>

<?= $this->endSection() ?>
