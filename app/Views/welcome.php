<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Greeting & Consolidated Portfolio Summary -->
<div class="row mb-4">
    <div class="col-12">
        <div class="content-card border-0 shadow-sm p-4 p-md-5 position-relative overflow-hidden">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-primary bg-opacity-10 text-primary rounded-pill small fw-semibold mb-3">
                        <i class="bi bi-person-check-fill"></i>
                        <span>Active Portfolio Dashboard</span>
                    </div>
                    <h2 class="fw-bold text-dark mb-2">Welcome back, <?= esc($userName) ?>! 👋</h2>
                    <p class="text-secondary mb-4 fs-6">
                        Real-time overview of your wealth and active holdings across Indian asset classes in Indian Rupees (<strong>₹</strong>).
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark border px-3 py-2 fw-medium">
                            <i class="bi bi-person me-1 text-primary"></i> <?= esc($userEmail) ?>
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2 fw-medium">
                            <i class="bi bi-currency-rupee me-1 text-success"></i> INR (₹)
                        </span>
                        <a href="<?= base_url('reports') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-semibold">
                            <i class="bi bi-pie-chart-fill me-1"></i> Full Analytics & Reports
                        </a>
                        <a href="<?= base_url('guide') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 fw-semibold">
                            <i class="bi bi-book me-1 text-warning"></i> User Guide
                        </a>
                    </div>
                </div>

                <!-- Consolidated Portfolio KPIs -->
                <div class="col-lg-5 mt-4 mt-lg-0">
                    <div class="p-3 bg-light rounded-4 border">
                        <div class="text-muted small text-uppercase fw-semibold mb-1">Consolidated Portfolio Net Worth</div>
                        <div class="d-flex align-items-baseline gap-2 mb-3">
                            <h2 class="fw-bold text-dark mb-0"><?= format_inr($totalCurrent) ?></h2>
                            <span class="badge <?= $totalPnl >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> border fw-semibold">
                                <?= $totalPnl >= 0 ? '+' : '' ?><?= number_format($totalPnlPct, 2) ?>%
                            </span>
                        </div>

                        <div class="row g-2 pt-2 border-top">
                            <div class="col-6">
                                <div class="text-muted fs-7">Total Invested</div>
                                <div class="fw-bold text-dark"><?= format_inr($totalInvested) ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted fs-7">Total Unrealized P&L</div>
                                <div class="fw-bold <?= $totalPnl >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $totalPnl >= 0 ? '+' : '' ?><?= format_inr($totalPnl) ?>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="d-flex justify-content-between align-items-center text-muted fs-7">
                                    <span>Active Asset Positions:</span>
                                    <span class="badge bg-dark text-white rounded-pill px-2.5 py-1"><?= (int)$totalHoldings ?> Positions Held</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Title -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-1">
            <i class="bi bi-layers-half me-2 text-primary"></i>Current Holdings by Asset Class
        </h5>
        <div class="text-muted small">Summary of active positions, cost basis, valuation and returns per module</div>
    </div>
    <div>
        <a href="<?= base_url('reports') ?>" class="btn btn-outline-secondary btn-sm rounded-3">
            <i class="bi bi-bar-chart-line me-1"></i> Allocation Matrix
        </a>
    </div>
</div>

<!-- 6 Module Holdings Breakdown Cards (Standard Module Order) -->
<div class="row g-3 mb-5">
    <?php foreach ($modules as $modKey => $mod): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border rounded-4 shadow-sm hover-lift bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Header: Icon, Title & Holdings Count -->
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="module-card-icon bg-<?= esc($mod['color']) ?> bg-opacity-10 text-<?= esc($mod['color']) ?>">
                                    <i class="<?= esc($mod['icon']) ?>"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0"><?= esc($mod['title']) ?></h6>
                                    <div class="text-muted fs-7"><?= esc($mod['subtitle']) ?></div>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill fs-7 fw-semibold">
                                <?= (int)$mod['holdings_count'] ?> <?= esc($mod['unit_label']) ?>
                            </span>
                        </div>

                        <!-- Financial Metrics -->
                        <div class="p-3 bg-light rounded-3 mb-3 border border-subtle">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="text-muted fs-7">Invested Capital</div>
                                    <div class="fw-bold text-dark"><?= format_inr($mod['invested']) ?></div>
                                </div>
                                <div class="col-6 text-end">
                                    <div class="text-muted fs-7">Current Valuation</div>
                                    <div class="fw-bold text-dark"><?= format_inr($mod['current']) ?></div>
                                </div>
                                <div class="col-12 pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="text-muted fs-7">Unrealized P&L:</span>
                                    <span class="fw-semibold fs-7 <?= $mod['unrealized'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $mod['unrealized'] >= 0 ? '+' : '' ?><?= format_inr($mod['unrealized']) ?>
                                        (<?= $mod['unrealized'] >= 0 ? '+' : '' ?><?= number_format($mod['unrealized_pct'], 2) ?>%)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center gap-2 pt-2">
                        <a href="<?= esc($mod['url']) ?>" class="btn btn-outline-primary btn-sm flex-fill rounded-3 fw-semibold">
                            <i class="bi bi-arrow-up-right me-1"></i> View Holdings
                        </a>
                        <a href="<?= esc($mod['new_url']) ?>" class="btn btn-light btn-sm border rounded-3 text-secondary" title="Add New <?= esc($mod['title']) ?>">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Bottom Application Details & System Specs Card -->
<div class="row">
    <div class="col-12">
        <div class="card border rounded-4 shadow-sm bg-white p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="brand-logo-icon" style="width: 32px; height: 32px; font-size: 1rem;">₹</div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Investment Portfolio Tracker &bull; System Specifications</h6>
                        <small class="text-muted">Enterprise-grade self-hosted personal finance platform</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold">
                        <i class="bi bi-wifi-off me-1"></i> Offline Ready &bull; Zero External Dependencies
                    </span>
                </div>
            </div>

            <!-- Specs Grid -->
            <div class="row g-3">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="system-spec-badge w-100">
                        <i class="bi bi-code-slash text-primary"></i>
                        <div>
                            <div class="text-muted fs-8">Framework</div>
                            <div class="fw-bold text-dark">CodeIgniter <?= esc($systemInfo['ci_version']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="system-spec-badge w-100">
                        <i class="bi bi-filetype-php text-info"></i>
                        <div>
                            <div class="text-muted fs-8">PHP Runtime</div>
                            <div class="fw-bold text-dark">PHP <?= esc($systemInfo['php_version']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="system-spec-badge w-100">
                        <i class="bi bi-database text-warning"></i>
                        <div>
                            <div class="text-muted fs-8">Database Server</div>
                            <div class="fw-bold text-dark"><?= esc($systemInfo['db_driver']) ?> <?= esc(substr($systemInfo['db_version'], 0, 12)) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="system-spec-badge w-100">
                        <i class="bi bi-calendar-check text-success"></i>
                        <div>
                            <div class="text-muted fs-8">Last System Update</div>
                            <div class="fw-bold text-dark"><?= esc($systemInfo['last_update']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="system-spec-badge w-100">
                        <i class="bi bi-currency-rupee text-success"></i>
                        <div>
                            <div class="text-muted fs-8">Base Currency</div>
                            <div class="fw-bold text-dark"><?= esc($systemInfo['currency']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="system-spec-badge w-100">
                        <i class="bi bi-clock-history text-secondary"></i>
                        <div>
                            <div class="text-muted fs-8">Timezone</div>
                            <div class="fw-bold text-dark"><?= esc($systemInfo['timezone']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="system-spec-badge w-100">
                        <i class="bi bi-hdd-network text-primary"></i>
                        <div>
                            <div class="text-muted fs-8">Server Host OS</div>
                            <div class="fw-bold text-dark"><?= esc($systemInfo['server_os']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="system-spec-badge w-100">
                        <i class="bi bi-shield-check text-danger"></i>
                        <div>
                            <div class="text-muted fs-8">Environment</div>
                            <div class="fw-bold text-dark"><?= esc(ucfirst($systemInfo['environment'])) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
