<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Guide Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-book-half me-2 text-warning"></i>Application User &amp; Operations Guide
        </h4>
        <div class="text-muted small">
            Exhaustive step-by-step documentation, formulas, and "How It Works" architectural mechanics for every module and action
        </div>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
            <i class="bi bi-wifi-off me-1"></i> 100% Offline Compatible
        </span>
        <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
            <i class="bi bi-house-door me-1"></i> Welcome Dashboard
        </a>
    </div>
</div>

<!-- Guide Content Layout: Left Nav Tabs, Right Tab Content -->
<div class="row g-4">
    <!-- Left Navigation Menu -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white sticky-top" style="top: 80px;">
            <div class="nav flex-column nav-pills gap-1" id="guide-tabs" role="tablist" aria-orientation="vertical">
                <button class="nav-link active text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-intro-btn" data-bs-toggle="pill" data-bs-target="#tab-intro" type="button" role="tab">
                    <i class="bi bi-info-circle me-2 text-primary"></i> 1. Getting Started &amp; Config
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-equities-btn" data-bs-toggle="pill" data-bs-target="#tab-equities" type="button" role="tab">
                    <i class="bi bi-graph-up-arrow me-2 text-primary"></i> 2. Equities &amp; Corporate Actions
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-reits-btn" data-bs-toggle="pill" data-bs-target="#tab-reits" type="button" role="tab">
                    <i class="bi bi-buildings me-2 text-info"></i> 3. InvITs &amp; REITs (Distributions)
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-etfs-btn" data-bs-toggle="pill" data-bs-target="#tab-etfs" type="button" role="tab">
                    <i class="bi bi-pie-chart me-2 text-success"></i> 4. Exchange Traded Funds (ETFs)
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-bonds-btn" data-bs-toggle="pill" data-bs-target="#tab-bonds" type="button" role="tab">
                    <i class="bi bi-receipt me-2 text-danger"></i> 5. Bonds &amp; SGBs
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-mfs-btn" data-bs-toggle="pill" data-bs-target="#tab-mfs" type="button" role="tab">
                    <i class="bi bi-briefcase me-2 text-warning"></i> 6. Mutual Funds (SIP &amp; FIFO)
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-nps-btn" data-bs-toggle="pill" data-bs-target="#tab-nps" type="button" role="tab">
                    <i class="bi bi-shield-check me-2 text-secondary"></i> 7. NPS Tier 1 (4-Scheme E/C/G/A)
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-expenses-btn" data-bs-toggle="pill" data-bs-target="#tab-expenses" type="button" role="tab">
                    <i class="bi bi-wallet2 me-2 text-danger"></i> 8. Brokerage &amp; Expenses Ledger
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-reports-btn" data-bs-toggle="pill" data-bs-target="#tab-reports" type="button" role="tab">
                    <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i> 9. Tax Reports &amp; Capital Gains
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold" id="tab-ops-btn" data-bs-toggle="pill" data-bs-target="#tab-ops" type="button" role="tab">
                    <i class="bi bi-hdd-network me-2 text-dark"></i> 10. Backup, Cleanup &amp; Production
                </button>
                <button class="nav-link text-start py-2 px-3 rounded-3 small fw-semibold text-danger" id="tab-support-btn" data-bs-toggle="pill" data-bs-target="#tab-support" type="button" role="tab">
                    <i class="bi bi-heart-fill me-2 text-danger"></i> 11. Support the Project
                </button>
            </div>
        </div>
    </div>

    <!-- Right Content Tabs -->
    <div class="col-lg-9">
        <div class="tab-content bg-white p-4 p-md-5 rounded-4 shadow-sm border" id="guide-tabContent">
            <!-- 1. GETTING STARTED & CONFIG -->
            <?= $this->include('guide/partials/_tab_intro') ?>

            <!-- 2. EQUITIES & CORPORATE ACTIONS -->
            <?= $this->include('guide/partials/_tab_equities') ?>

            <!-- 3. INvITs & REITs (DISTRIBUTIONS) -->
            <?= $this->include('guide/partials/_tab_reits') ?>

            <!-- 4. EXCHANGE TRADED FUNDS (ETFs) -->
            <?= $this->include('guide/partials/_tab_etfs') ?>

            <!-- 5. BONDS & FIXED INCOME -->
            <?= $this->include('guide/partials/_tab_bonds') ?>

            <!-- 6. MUTUAL FUNDS -->
            <?= $this->include('guide/partials/_tab_mfs') ?>

            <!-- 7. NPS TIER 1 -->
            <?= $this->include('guide/partials/_tab_nps') ?>

            <!-- 8. BROKERAGE & EXPENSES LEDGER -->
            <?= $this->include('guide/partials/_tab_expenses') ?>

            <!-- 9. REPORTS & CAPITAL GAINS TAX -->
            <?= $this->include('guide/partials/_tab_reports') ?>

            <!-- 10. OPERATIONS & BACKUP -->
            <?= $this->include('guide/partials/_tab_ops') ?>

            <!-- 11. SUPPORT THE PROJECT -->
            <?= $this->include('guide/partials/_tab_support') ?>
        </div>
    </div>

    <!-- DISCLAIMER & LIMITATION OF LIABILITY -->
    <?= $this->include('guide/partials/_disclaimer') ?>
</div>

<?= $this->endSection() ?>
