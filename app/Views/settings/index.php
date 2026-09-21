<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <!-- Settings Sub-Navigation -->
    <?= $this->include('settings/_nav') ?>

    <div class="row g-4">
        <!-- Profile & System Preferences -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-3">
                            <i class="bi bi-person-gear fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Profile & Display Preferences</h5>
                            <small class="text-muted">Manage your personal information, fiscal year cycle, and table pagination</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('settings/update-profile') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="name" class="form-label small fw-semibold text-secondary">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 0.65rem 0 0 0.65rem;">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-start-0" 
                                       id="name" 
                                       name="name" 
                                       value="<?= old('name', $user['name'] ?? '') ?>" 
                                       required 
                                       style="border-radius: 0 0.65rem 0.65rem 0;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 0.65rem 0 0 0.65rem;">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control border-start-0" 
                                       id="email" 
                                       name="email" 
                                       value="<?= old('email', $user['email'] ?? '') ?>" 
                                       required 
                                       style="border-radius: 0 0.65rem 0.65rem 0;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label small fw-semibold text-secondary">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 0.65rem 0 0 0.65rem;">
                                    <i class="bi bi-telephone"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-start-0" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?= old('phone', $user['phone'] ?? '') ?>" 
                                       placeholder="+91 98765 43210" 
                                       style="border-radius: 0 0.65rem 0.65rem 0;">
                            </div>
                            <div class="form-text small">Used for portfolio notifications and alerts.</div>
                        </div>

                        <div class="mb-3">
                            <label for="fy_start_month" class="form-label small fw-semibold text-secondary">Financial Year Start</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 0.65rem 0 0 0.65rem;">
                                    <i class="bi bi-calendar3"></i>
                                </span>
                                <select class="form-select border-start-0" 
                                        id="fy_start_month" 
                                        name="fy_start_month" 
                                        style="border-radius: 0 0.65rem 0.65rem 0;">
                                    <option value="4" <?= (int) ($user['fy_start_month'] ?? 4) === 4 ? 'selected' : '' ?>>
                                        1st April (India — FY 1 Apr to 31 Mar) [Recommended]
                                    </option>
                                    <option value="1" <?= (int) ($user['fy_start_month'] ?? 4) === 1 ? 'selected' : '' ?>>
                                        1st January (Calendar Year — 1 Jan to 31 Dec)
                                    </option>
                                    <option value="7" <?= (int) ($user['fy_start_month'] ?? 4) === 7 ? 'selected' : '' ?>>
                                        1st July (1 Jul to 30 Jun)
                                    </option>
                                    <option value="10" <?= (int) ($user['fy_start_month'] ?? 4) === 10 ? 'selected' : '' ?>>
                                        1st October (1 Oct to 30 Sep)
                                    </option>
                                </select>
                            </div>
                            <div class="form-text small text-muted">
                                Sets the annual cut-off for Capital Gains tax calculations (STCG & LTCG) for year-end tax reporting.
                            </div>
                        </div>

                        <!-- Table Pagination Setting -->
                        <div class="mb-4">
                            <label for="records_per_page" class="form-label small fw-semibold text-secondary">Default Records Per Page (Pagination)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 0.65rem 0 0 0.65rem;">
                                    <i class="bi bi-list-ol"></i>
                                </span>
                                <select class="form-select border-start-0" 
                                        id="records_per_page" 
                                        name="records_per_page" 
                                        style="border-radius: 0 0.65rem 0.65rem 0;">
                                    <?php 
                                    $userPageSize = (int) ($user['records_per_page'] ?? 20);
                                    $allowedSizes = [20, 40, 50, 80, 100];
                                    foreach ($allowedSizes as $size): 
                                    ?>
                                        <option value="<?= $size ?>" <?= $userPageSize === $size ? 'selected' : '' ?>>
                                            <?= $size ?> records per page <?= $size === 20 ? '(Default)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-text small text-muted">
                                Controls the default row limit for trade ledgers, FIFO tax logs, dividends, and interest tables across all modules.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">
                            <i class="bi bi-check2-circle me-2"></i>Save Profile & Preferences
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Architecture & Long-term Scale Card -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-info bg-opacity-10 text-info rounded-3 me-3">
                            <i class="bi bi-database-check fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">System & Database Architecture</h5>
                            <small class="text-muted">Built for multi-year high-volume scalability</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Multi-Year Performance Optimized</h6>
                        <p class="small text-muted mb-2">
                            All transaction and audit tables feature composite indexes on <code>(user_id, security_id, transaction_date)</code>. 
                            Even after 10 to 20 years with tens of thousands of trades, database queries execute in single-digit milliseconds.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-layers-fill text-primary me-2"></i>Client-Side DOM Virtualization</h6>
                        <p class="small text-muted mb-2">
                            Table pagination limits active browser DOM nodes to your configured page size (<?= (int) ($user['records_per_page'] ?? 20) ?> rows), ensuring instantaneous scrolling, sorting, and filtering with zero browser lag.
                        </p>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-secondary">Active Session FY Start</span>
                            <span class="badge bg-primary-subtle text-primary">Month <?= (int) ($user['fy_start_month'] ?? 4) ?></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold text-secondary">Default Page Size</span>
                            <span class="badge bg-success-subtle text-success"><?= (int) ($user['records_per_page'] ?? 20) ?> entries/page</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
