<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <!-- Settings Sub-Navigation -->
    <?= $this->include('settings/_nav') ?>

    <div class="row g-4">
        <div class="col-lg-6 col-xl-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3 me-3">
                            <i class="bi bi-shield-lock fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Reset Password</h5>
                            <small class="text-muted">Ensure your account uses a strong, unique password</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('settings/change-password') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="current_password" class="form-label small fw-semibold text-secondary">Current Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="current_password" 
                                   name="current_password" 
                                   placeholder="Enter current password" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label small fw-semibold text-secondary">New Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password" 
                                   name="new_password" 
                                   placeholder="Minimum 6 characters" 
                                   minlength="6" 
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label small fw-semibold text-secondary">Confirm New Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Re-enter new password" 
                                   required>
                        </div>

                        <button type="submit" class="btn btn-warning text-dark px-4 py-2 rounded-3 fw-semibold w-100">
                            <i class="bi bi-key me-2"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-success bg-opacity-10 text-success rounded-3 me-3">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Security Best Practices</h5>
                            <small class="text-muted">Guidelines for securing your financial portfolio</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div>
                                <strong>Length & Complexity:</strong> Use at least 8 characters with a blend of uppercase letters, numbers, and symbols.
                            </div>
                        </li>
                        <li class="d-flex mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div>
                                <strong>Unique Password:</strong> Do not reuse passwords across email providers, banks, and portfolio tools.
                            </div>
                        </li>
                        <li class="d-flex mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div>
                                <strong>Bcrypt Cryptographic Hashing:</strong> Passwords are protected using salted Bcrypt hashes and are never stored in plaintext.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

