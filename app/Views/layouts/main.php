<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'RupeeFolio') ?></title>
    <!-- Bootstrap 5.3 CSS (Local Offline) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons (Local Offline) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>">
    <!-- Custom Application CSS (Local Offline, Native System Fonts) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>

    <!-- Sidebar Navigation -->
    <nav id="sidebar">
        <div class="brand-area d-flex align-items-center">
            <div class="brand-logo-icon me-3">₹</div>
            <div>
                <h5 class="text-white fw-bold mb-1">RupeeFolio</h5>
                <small class="text-white" style="font-size: 0.72rem; color: #ffffff !important; display: block; line-height: 1.2;">Portfolio Manager</small>
            </div>
        </div>

        <div class="sidebar-nav py-3">
            <div class="nav-section-title">Home</div>
            <a href="<?= base_url('/') ?>" class="nav-link <?= uri_string() === '' || uri_string() === 'welcome' ? 'active' : '' ?>">
                <i class="bi bi-house-door"></i>
                <span>Welcome</span>
            </a>

            <div class="nav-section-title">Asset Modules</div>
            
            <a href="<?= base_url('equities') ?>" class="nav-link <?= strpos(uri_string(), 'equities') !== false ? 'active' : '' ?>">
                <i class="bi bi-graph-up-arrow text-primary"></i>
                <span>Equities</span>
            </a>

            <a href="<?= base_url('reits-invits') ?>" class="nav-link <?= strpos(uri_string(), 'reits-invits') !== false ? 'active' : '' ?>">
                <i class="bi bi-buildings text-info"></i>
                <span>InvITs / REITs</span>
            </a>

            <a href="<?= base_url('etfs') ?>" class="nav-link <?= strpos(uri_string(), 'etfs') !== false ? 'active' : '' ?>">
                <i class="bi bi-pie-chart text-info"></i>
                <span>ETFs</span>
            </a>

            <a href="<?= base_url('bonds') ?>" class="nav-link <?= strpos(uri_string(), 'bonds') !== false ? 'active' : '' ?>">
                <i class="bi bi-receipt text-danger"></i>
                <span>Bonds</span>
            </a>

            <a href="<?= base_url('mutual-funds') ?>" class="nav-link <?= strpos(uri_string(), 'mutual-funds') !== false ? 'active' : '' ?>">
                <i class="bi bi-briefcase text-success"></i>
                <span>Mutual Funds</span>
            </a>

            <a href="<?= base_url('nps') ?>" class="nav-link <?= strpos(uri_string(), 'nps') !== false ? 'active' : '' ?>">
                <i class="bi bi-shield-check text-warning"></i>
                <span>NPS (Tier 1)</span>
            </a>

            <div class="nav-section-title">Analytics & Reports</div>
            <a href="<?= base_url('reports') ?>" class="nav-link <?= strpos(uri_string(), 'reports') === 0 ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-bar-graph text-primary"></i>
                <span>Reports</span>
            </a>

            <div class="nav-section-title">Account & Config</div>
            <a href="<?= base_url('settings') ?>" class="nav-link <?= strpos(uri_string(), 'settings') === 0 ? 'active' : '' ?>">
                <i class="bi bi-gear text-secondary"></i>
                <span>Admin Settings</span>
            </a>

            <div class="nav-section-title">Help & Documentation</div>
            <a href="<?= base_url('guide') ?>" class="nav-link <?= strpos(uri_string(), 'guide') === 0 ? 'active' : '' ?>">
                <i class="bi bi-book text-warning"></i>
                <span>User Guide</span>
            </a>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <div id="content-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-light d-lg-none me-2" id="sidebarToggle" type="button">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <span class="currency-badge">
                        <i class="bi bi-currency-rupee me-1"></i> Base Currency: INR (₹)
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0 bg-transparent" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <div class="user-avatar">
                            <?= strtoupper(substr(session()->get('userName') ?? 'I', 0, 1)) ?>
                        </div>
                        <div class="d-none d-sm-block text-start">
                            <div class="fw-semibold small text-dark"><?= esc(session()->get('userName') ?? 'Investor') ?></div>
                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc(session()->get('userEmail') ?? '') ?></div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                        <li>
                            <div class="dropdown-header">Signed in as <br><strong><?= esc(session()->get('userEmail') ?? '') ?></strong></div>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?= base_url('settings') ?>">
                                <i class="bi bi-gear"></i>
                                <span>Settings & Profile</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger d-flex align-items-center gap-2" href="<?= base_url('logout') ?>">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Alerts -->
        <div class="container-fluid px-4 pt-3 pb-0">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2 px-3 rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div><?= esc(session()->getFlashdata('success')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2 px-3 rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?= esc(session()->getFlashdata('error')) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3 rounded-3 small" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Page Content -->
        <main class="main-container">
            <?= $this->renderSection('content') ?>
        </main>

        <!-- Footer -->
        <footer class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                &copy; <?= date('Y') ?> <strong>RupeeFolio</strong> &bull; Indian Financial Market Portfolio Tracker
            </div>
            <div class="text-muted small">
                Developed by <a href="https://vrktech.com" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-semibold text-primary">VRK Tech</a> &bull; Built with CodeIgniter 4 &amp; Bootstrap 5
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5.3 JS Bundle (Local Offline) -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- App UI & Pagination Scripts (Local Offline) -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>

