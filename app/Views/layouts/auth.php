<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sign In - Portfolio Tracker') ?></title>
    <!-- Bootstrap 5.3 CSS (Local Offline) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons (Local Offline) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>">
    <!-- Custom Application CSS (Local Offline, Native System Fonts) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="auth-page">

    <div class="auth-card p-4 p-md-5">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap 5.3 JS Bundle (Local Offline) -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- App UI Scripts (Local Offline) -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
