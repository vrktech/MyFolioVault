<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="text-center mb-4">
    <div class="brand-badge mb-3">
        <span>₹</span>
    </div>
    <h3 class="fw-bold text-dark mb-1">RupeeFolio</h3>
    <p class="text-muted small">Sign in to manage your Equities, MFs, ETFs, NPS & Bonds</p>
</div>

<!-- Flash Alerts -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-3 rounded-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div class="small"><?= esc(session()->getFlashdata('error')) ?></div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-3 rounded-3" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div class="small"><?= esc(session()->getFlashdata('success')) ?></div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger py-2 px-3 mb-3 rounded-3 small">
        <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Login Form -->
<form action="<?= base_url('login') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 0.65rem 0 0 0.65rem; border: 1.5px solid #e2e8f0;">
                <i class="bi bi-envelope"></i>
            </span>
            <input type="email" 
                   class="form-control border-start-0" 
                   id="email" 
                   name="email" 
                   value="<?= old('email', 'admin@portfolio.local') ?>" 
                   placeholder="name@example.com" 
                   style="border-radius: 0 0.65rem 0.65rem 0;"
                   required 
                   autofocus>
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label for="password" class="form-label small fw-semibold text-secondary mb-0">Password</label>
        </div>
        <div class="input-group mt-2">
            <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 0.65rem 0 0 0.65rem; border: 1.5px solid #e2e8f0;">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" 
                   class="form-control border-start-0 border-end-0" 
                   id="password" 
                   name="password" 
                   value="password123"
                   placeholder="Enter your password" 
                   required>
            <button class="btn btn-outline-secondary border-start-0 text-muted" 
                    type="button" 
                    id="togglePassword" 
                    style="border-radius: 0 0.65rem 0.65rem 0; border: 1.5px solid #e2e8f0;">
                <i class="bi bi-eye" id="togglePasswordIcon"></i>
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe" checked>
            <label class="form-check-label small text-secondary" for="rememberMe">
                Remember me
            </label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary-gradient w-100 mb-3 d-flex align-items-center justify-content-center">
        <span>Sign In to Dashboard</span>
        <i class="bi bi-arrow-right ms-2"></i>
    </button>
</form>

<!-- DEMO_CREDENTIALS_START -->
<!-- Demo Credentials Helper -->
<div class="credentials-pill text-center">
    <div class="text-secondary fw-semibold mb-1 small"><i class="bi bi-key-fill text-warning me-1"></i> Pre-configured Demo Account</div>
    <div class="small text-muted">
        Email: <span class="font-monospace text-dark fw-medium">admin@portfolio.local</span><br>
        Password: <span class="font-monospace text-dark fw-medium">password123</span>
    </div>
</div>
<!-- DEMO_CREDENTIALS_END -->

<?= $this->endSection() ?>

