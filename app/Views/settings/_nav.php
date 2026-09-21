<?php
$currentUri = uri_string();
?>
<div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-gear me-2 text-primary"></i>Admin & Account Settings</h4>
        <p class="text-muted small mb-0">Manage your profile, display preferences, security, and equity sector taxonomy.</p>
    </div>
</div>

<ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3">
    <li class="nav-item">
        <a class="nav-link <?= ($currentUri === 'settings' || $currentUri === 'settings/index') ? 'active bg-primary text-white fw-semibold' : 'text-secondary border' ?>" href="<?= base_url('settings') ?>">
            <i class="bi bi-person-gear me-1"></i> Profile & Preferences
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= (strpos($currentUri, 'settings/password') !== false) ? 'active bg-primary text-white fw-semibold' : 'text-secondary border' ?>" href="<?= base_url('settings/password') ?>">
            <i class="bi bi-shield-lock me-1"></i> Change Password
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= (strpos($currentUri, 'settings/sectors') !== false) ? 'active bg-primary text-white fw-semibold' : 'text-secondary border' ?>" href="<?= base_url('settings/sectors') ?>">
            <i class="bi bi-pie-chart me-1"></i> Equities Sector Master
        </a>
    </li>
</ul>

