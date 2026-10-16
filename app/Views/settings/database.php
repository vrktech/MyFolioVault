<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <!-- Settings Sub-Navigation -->
    <?= $this->include('settings/_nav') ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div>
                    <h6 class="mb-0 fw-bold">Success!</h6>
                    <small><?= session()->getFlashdata('success') ?></small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                <div>
                    <h6 class="mb-0 fw-bold">Action Failed</h6>
                    <small><?= session()->getFlashdata('error') ?></small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Database Status Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 me-3">
                        <i class="bi bi-table fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium text-uppercase">Portfolio Tables</span>
                        <h4 class="mb-0 fw-bold text-dark"><?= number_format($stats['total_tables'] ?? 0) ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 me-3">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium text-uppercase">Stored Records</span>
                        <h4 class="mb-0 fw-bold text-dark"><?= number_format($stats['total_records'] ?? 0) ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 bg-info bg-opacity-10 text-info p-3 me-3">
                        <i class="bi bi-hdd-network fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium text-uppercase">Storage Size</span>
                        <h4 class="mb-0 fw-bold text-dark"><?= number_format($stats['database_size'] ?? 0, 2) ?> MB</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3 me-3">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium text-uppercase">Protected Data</span>
                        <h6 class="mb-0 fw-bold text-dark">Users & Migrations</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup & Restore Operations -->
    <div class="row g-4">
        <!-- Card 1: Backup Database -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-3">
                            <i class="bi bi-cloud-arrow-down fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Export Database Backup</h5>
                            <small class="text-muted">Download a compressed SQL archive of your portfolio</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-secondary small mb-3">
                            Generate a clean, self-contained zipped SQL dump of your portfolio data. You can keep this backup archive safe on your local drive or cloud storage.
                        </p>

                        <div class="p-3 bg-light rounded-3 border mb-4">
                            <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-info-circle text-primary me-1"></i> What is included in this backup:</h6>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>All <strong>Equities, Mutual Funds, ETFs, Bonds, REITs/InvITs & NPS</strong> assets</li>
                                <li>All trade transaction histories, buy/sell FIFO lots, and realized capital gains</li>
                                <li>All dividend payments, bond interest payouts, and quarterly trust distributions</li>
                                <li>Personal & investment expense records and custom sector masters</li>
                                <li class="text-success fw-semibold"><i class="bi bi-shield-lock me-1"></i>Administrator login and credentials (<code>users</code> table) are safely preserved</li>
                                <li class="text-success fw-semibold"><i class="bi bi-database-check me-1"></i>Database migration state (<code>migrations</code> table) is kept intact</li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <a href="<?= base_url('settings/database/backup') ?>" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold w-100 shadow-sm">
                            <i class="bi bi-file-earmark-zip me-2"></i>Download Database Backup (.zip)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Restore Database -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-3 me-3">
                            <i class="bi bi-cloud-arrow-up fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Restore Database from Backup</h5>
                            <small class="text-muted">Revert or migrate your portfolio using a previous backup ZIP</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="alert alert-warning border-warning border-opacity-25 bg-warning bg-opacity-10 rounded-3 p-3 mb-3 small">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill text-warning fs-5 me-2 flex-shrink-0"></i>
                                <div>
                                    <strong class="text-dark">Caution: Overwrite Warning</strong><br>
                                    Restoring from a backup clears all current portfolio data (holdings, trades, dividends, expenses) and replaces them with the records in the uploaded backup file. Your user login and password will remain unchanged.
                                </div>
                            </div>
                        </div>

                        <form id="restoreForm" action="<?= base_url('settings/database/restore') ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="backup_zip" class="form-label small fw-semibold text-secondary">
                                    Select Backup ZIP Archive
                                </label>
                                <input type="file" 
                                       class="form-control" 
                                       id="backup_zip" 
                                       name="backup_zip" 
                                       accept=".zip" 
                                       required>
                                <div class="form-text small text-muted">
                                    Only <code>.zip</code> backup files created by MyFolioVault are accepted (Max 50 MB).
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="mt-3">
                        <button type="button" 
                                id="btnTriggerRestore" 
                                class="btn btn-outline-danger px-4 py-2 rounded-3 fw-semibold w-100" 
                                disabled>
                            <i class="bi bi-arrow-repeat me-2"></i>Upload & Restore Database
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Confirm Database Restore -->
<div class="modal fade" id="confirmRestoreModal" tabindex="-1" aria-labelledby="confirmRestoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4 py-3 px-4">
                <h5 class="modal-title fw-bold" id="confirmRestoreModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Database Restore
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-dark mb-3">
                    Are you sure you want to proceed with restoring the database from:
                    <strong id="selectedFileName" class="text-primary d-block mt-1"></strong>
                </p>

                <div class="p-3 bg-light rounded-3 border mb-3 small text-danger">
                    <i class="bi bi-shield-slash me-1"></i>
                    <strong>This action cannot be undone.</strong> Current transactions and portfolio records will be permanently replaced with data from the uploaded backup file. An automated safety snapshot will be created before applying changes.
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmCheckbox">
                    <label class="form-check-label small fw-semibold text-dark" for="confirmCheckbox">
                        I understand that existing portfolio data will be overwritten
                    </label>
                </div>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4 py-3 px-4">
                <button type="button" class="btn btn-secondary rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btnConfirmSubmit" class="btn btn-danger rounded-3 px-4 fw-semibold" disabled>
                    <span id="btnRestoreSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                    <span id="btnRestoreText">Yes, Restore Database</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/settings_database.js') ?>"></script>

<?= $this->endSection() ?>
