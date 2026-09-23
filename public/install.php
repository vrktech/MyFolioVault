<?php
/**
 * Standalone First-Time Web Setup Wizard
 * MyFolioVault - Indian Investment Portfolio Tracker
 *
 * 100% Offline Compatible - Native System Fonts
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('INSTALL_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('SCHEMA_FILE', INSTALL_ROOT . 'schema.sql');
define('SAMPLE_DATA_FILE', INSTALL_ROOT . 'sample_data.sql');
define('ENV_FILE', INSTALL_ROOT . '.env');
define('ENV_EXAMPLE', INSTALL_ROOT . 'env');

// Handle Self-Deletion Action
$action = $_GET['action'] ?? ($_POST['action'] ?? '');
if ($action === 'delete_self') {
    $selfFile = __FILE__;
    $success = @unlink($selfFile);

    // Resolve accurate app base URL from .env if available
    $targetLoginUrl = '';
    if (file_exists(ENV_FILE)) {
        $envLines = file(ENV_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($envLines as $line) {
            $line = trim($line);
            if (preg_match('/^app\.baseURL\s*=\s*(.*)$/', $line, $m)) {
                $targetLoginUrl = rtrim(trim($m[1], "'\" "), '/') . '/login';
                break;
            }
        }
    }
    if (empty($targetLoginUrl)) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
        $scriptDir = rtrim(str_replace('\\', '/', $scriptDir), '/');
        $det = $protocol . $host . $scriptDir . '/';
        if (str_ends_with($det, '/public/')) {
            $det = substr($det, 0, -7) . '/';
        }
        $targetLoginUrl = rtrim($det, '/') . '/login';
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Setup Installer Deleted - MyFolioVault</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/bootstrap-icons.min.css">
        <link rel="stylesheet" href="assets/css/app.css">
    </head>
    <body class="auth-page">
        <div class="auth-card p-4 p-md-5 text-center" style="max-width: 520px;">
            <div class="brand-badge mb-3 bg-success">
                <i class="bi bi-shield-check"></i>
            </div>
            <?php if ($success): ?>
                <h4 class="fw-bold text-dark mb-2">Installer Deleted Successfully</h4>
                <p class="text-secondary small mb-4">
                    <code>install.php</code> has been permanently removed from your server. Your portfolio installation is secure and ready for production use.
                </p>
                <a href="<?= htmlspecialchars($targetLoginUrl) ?>" class="btn btn-primary-gradient px-4 py-2 text-white text-decoration-none">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Proceed to Login
                </a>
            <?php else: ?>
                <h4 class="fw-bold text-danger mb-2">Manual Deletion Required</h4>
                <p class="text-secondary small mb-3">
                    PHP could not automatically delete <code>public/install.php</code> due to filesystem write permissions.
                </p>
                <div class="alert alert-warning text-start small">
                    Please manually delete this file using your file manager or terminal:<br>
                    <code><?= htmlspecialchars($selfFile) ?></code>
                </div>
                <a href="<?= htmlspecialchars($targetLoginUrl) ?>" class="btn btn-primary px-4 py-2 text-white text-decoration-none">
                    Proceed to Login Anyway
                </a>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Auto-detect base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$scriptDir = rtrim(str_replace('\\', '/', $scriptDir), '/');
$detectedBaseUrl = $protocol . $host . $scriptDir . '/';
// If scriptDir ends with /public, parent directory is typically the CI base url
if (str_ends_with($detectedBaseUrl, '/public/')) {
    $detectedBaseUrl = substr($detectedBaseUrl, 0, -7) . '/';
}

// Pre-fill existing DB credentials if .env exists
$cfgHost = 'localhost';
$cfgPort = 3306;
$cfgUser = 'root';
$cfgPass = '';
$cfgName = 'investment_portfolio';
$cfgUrl  = $detectedBaseUrl;

if (file_exists(ENV_FILE)) {
    $envLines = file(ENV_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '#')) continue;
        if (preg_match('/^app\.baseURL\s*=\s*(.*)$/', $line, $m)) $cfgUrl = trim($m[1], "'\" ");
        if (preg_match('/^database\.default\.hostname\s*=\s*(.*)$/', $line, $m)) $cfgHost = trim($m[1], "'\" ");
        if (preg_match('/^database\.default\.database\s*=\s*(.*)$/', $line, $m)) $cfgName = trim($m[1], "'\" ");
        if (preg_match('/^database\.default\.username\s*=\s*(.*)$/', $line, $m)) $cfgUser = trim($m[1], "'\" ");
        if (preg_match('/^database\.default\.password\s*=\s*(.*)$/', $line, $m)) $cfgPass = trim($m[1], "'\" ");
        if (preg_match('/^database\.default\.port\s*=\s*(.*)$/', $line, $m)) $cfgPort = (int)trim($m[1], "'\" ");
    }
}

// Check System Requirements
$requirements = [
    'PHP Version (>= 8.1.0)' => [
        'status' => version_compare(PHP_VERSION, '8.1.0', '>='),
        'current' => PHP_VERSION,
        'required' => '8.1.0+'
    ],
    'MySQLi Extension' => [
        'status' => extension_loaded('mysqli'),
        'current' => extension_loaded('mysqli') ? 'Loaded' : 'Missing',
        'required' => 'Required'
    ],
    'Intl Extension' => [
        'status' => extension_loaded('intl'),
        'current' => extension_loaded('intl') ? 'Loaded' : 'Missing',
        'required' => 'Required'
    ],
    'Mbstring Extension' => [
        'status' => extension_loaded('mbstring'),
        'current' => extension_loaded('mbstring') ? 'Loaded' : 'Missing',
        'required' => 'Required'
    ],
    'JSON Extension' => [
        'status' => extension_loaded('json'),
        'current' => extension_loaded('json') ? 'Loaded' : 'Missing',
        'required' => 'Required'
    ],
    'Schema DDL File (schema.sql)' => [
        'status' => file_exists(SCHEMA_FILE),
        'current' => file_exists(SCHEMA_FILE) ? 'Found (' . number_format(filesize(SCHEMA_FILE) / 1024, 1) . ' KB)' : 'Missing',
        'required' => 'schema.sql in root'
    ],
    'Demo Data File (sample_data.sql)' => [
        'status' => true,
        'current' => file_exists(SAMPLE_DATA_FILE) ? 'Available (' . number_format(filesize(SAMPLE_DATA_FILE) / 1024, 1) . ' KB)' : 'Not found',
        'required' => 'Optional'
    ],
    'Root Directory Writable' => [
        'status' => is_writable(INSTALL_ROOT),
        'current' => is_writable(INSTALL_ROOT) ? 'Writable' : 'Read-only',
        'required' => 'Writable (for .env)'
    ],
];

$allReqsPassed = true;
foreach ($requirements as $req) {
    if (!$req['status']) {
        $allReqsPassed = false;
        break;
    }
}

$installError = '';
$installSuccess = false;
$logs = [];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'install' || empty($action))) {
    $dbHost       = trim($_POST['db_host'] ?? 'localhost');
    $dbPort       = (int)($_POST['db_port'] ?? 3306);
    $dbUser       = trim($_POST['db_user'] ?? 'root');
    $dbPass       = $_POST['db_pass'] ?? '';
    $dbName       = trim($_POST['db_name'] ?? 'investment_portfolio');
    $createDb     = isset($_POST['create_db']);
    $appUrl       = rtrim(trim($_POST['app_url'] ?? $detectedBaseUrl), '/') . '/';
    $appEnv       = $_POST['app_env'] ?? 'production';

    $adminName    = trim($_POST['admin_name'] ?? 'Administrator');
    $adminEmail   = trim($_POST['admin_email'] ?? 'admin@portfolio.local');
    $adminPass    = $_POST['admin_pass'] ?? '';
    $adminPass2   = $_POST['admin_pass2'] ?? '';
    $fyStartMonth = (int)($_POST['fy_start_month'] ?? 4);
    $recordsPerPage = (int)($_POST['records_per_page'] ?? 20);
    $installDemoData = !empty($_POST['install_demo_data']);

    if (empty($dbHost) || empty($dbUser) || empty($dbName)) {
        $installError = 'Database Host, Username, and Database Name are required.';
    } elseif (empty($adminEmail) || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        $installError = 'Please enter a valid Administrator email address.';
    } elseif (strlen($adminPass) < 6) {
        $installError = 'Administrator password must be at least 6 characters long.';
    } elseif ($adminPass !== $adminPass2) {
        $installError = 'Administrator passwords do not match.';
    } elseif (!file_exists(SCHEMA_FILE)) {
        $installError = 'Database schema file (schema.sql) was not found in the project root.';
    } else {
        // Step 1: Connect to MySQL server
        $logs[] = "Connecting to MySQL server at {$dbHost}:{$dbPort}...";
        $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, '', $dbPort);
        if ($mysqli->connect_error) {
            $installError = "MySQL connection error: " . $mysqli->connect_error;
        } else {
            $mysqli->set_charset('utf8mb4');
            $logs[] = "✓ Connected to MySQL server (" . $mysqli->server_info . ")";

            // Step 2: Create DB if requested or select
            if ($createDb) {
                $logs[] = "Creating database `{$dbName}` if not exists...";
                if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
                    $installError = "Failed to create database `{$dbName}`: " . $mysqli->error;
                }
            }

            if (empty($installError)) {
                if (!$mysqli->select_db($dbName)) {
                    $installError = "Failed to select database `{$dbName}`: " . $mysqli->error;
                } else {
                    $logs[] = "✓ Selected database `{$dbName}`";

                    // Step 3: Execute schema.sql
                    $logs[] = "Importing tables, constraints and indexes from schema.sql...";
                    $schemaSql = file_get_contents(SCHEMA_FILE);

                    $mysqli->query("SET FOREIGN_KEY_CHECKS = 0;");
                    $mysqli->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");

                    // Execute multi-query
                    if ($mysqli->multi_query($schemaSql)) {
                        do {
                            if ($result = $mysqli->store_result()) {
                                $result->free();
                            }
                        } while ($mysqli->more_results() && $mysqli->next_result());
                    }

                    if ($mysqli->error) {
                        $logs[] = "! Note during DDL execution: " . $mysqli->error;
                    }

                    $mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");

                    // Verify tables
                    $tRes = $mysqli->query("SHOW TABLES FROM `{$dbName}`");
                    $tableCount = $tRes ? $tRes->num_rows : 0;
                    $logs[] = "✓ Created/verified {$tableCount} database tables";

                    // Step 4: Create Administrator account
                    $logs[] = "Creating administrator user account ({$adminEmail})...";
                    $passwordHash = password_hash($adminPass, PASSWORD_BCRYPT);
                    $now = date('Y-m-d H:i:s');

                    $stmt = $mysqli->prepare("
                        INSERT INTO `users` (`name`, `email`, `password_hash`, `fy_start_month`, `records_per_page`, `created_at`, `updated_at`)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                            `name` = VALUES(`name`),
                            `password_hash` = VALUES(`password_hash`),
                            `fy_start_month` = VALUES(`fy_start_month`),
                            `records_per_page` = VALUES(`records_per_page`),
                            `updated_at` = VALUES(`updated_at`)
                    ");

                    if ($stmt) {
                        $stmt->bind_param('sssiiss', $adminName, $adminEmail, $passwordHash, $fyStartMonth, $recordsPerPage, $now, $now);
                        if ($stmt->execute()) {
                            $logs[] = "✓ Administrator account configured successfully";
                        } else {
                            $installError = "Failed to create administrator account: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $installError = "User statement preparation failed: " . $mysqli->error;
                    }

                    // Step 4b: Optionally import demo portfolio data
                    if (empty($installError) && $installDemoData) {
                        if (file_exists(SAMPLE_DATA_FILE)) {
                            $logs[] = "Importing demo holdings and sample transactions (sample_data.sql)...";
                            $sampleSql = file_get_contents(SAMPLE_DATA_FILE);

                            // Find administrator user_id to ensure foreign key integrity
                            $adminUserId = 1;
                            $userQuery = $mysqli->query("SELECT id FROM `users` WHERE `email` = '" . $mysqli->real_escape_string($adminEmail) . "' LIMIT 1");
                            if ($userQuery && $row = $userQuery->fetch_assoc()) {
                                $adminUserId = (int)$row['id'];
                            }

                            $mysqli->query("SET FOREIGN_KEY_CHECKS = 0;");
                            $mysqli->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");

                            if ($mysqli->multi_query($sampleSql)) {
                                do {
                                    if ($result = $mysqli->store_result()) {
                                        $result->free();
                                    }
                                } while ($mysqli->more_results() && $mysqli->next_result());
                            }

                            if ($mysqli->error) {
                                $logs[] = "! Note during demo data import: " . $mysqli->error;
                            } else {
                                if ($adminUserId !== 1) {
                                    $sampleTables = [
                                        'bonds', 'bond_capital_gains', 'bond_interest_payouts', 'bond_transactions',
                                        'corporate_actions', 'equities', 'equity_capital_gains', 'equity_dividends',
                                        'equity_transactions', 'etfs', 'etf_capital_gains', 'etf_transactions',
                                        'expenses',
                                        'mutual_funds', 'mutual_fund_capital_gains', 'mutual_fund_transactions',
                                        'nps_accounts', 'nps_scheme_units', 'nps_transactions',
                                        'reits_invits', 'reit_invit_capital_gains', 'reit_invit_distributions', 'reit_invit_transactions'
                                    ];
                                    foreach ($sampleTables as $tbl) {
                                        $mysqli->query("UPDATE `{$tbl}` SET `user_id` = {$adminUserId} WHERE `user_id` = 1");
                                    }
                                }
                                $logs[] = "✓ Sample demo transactions, holdings, and payouts loaded successfully";
                            }
                            $mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");
                        } else {
                            $logs[] = "! Warning: sample_data.sql was not found in root; skipped demo data installation.";
                        }
                    } elseif (empty($installError)) {
                        $logs[] = "✓ Clean installation: no demo data loaded (empty portfolio)";
                    }

                    // Step 5: Update .env configuration file
                    if (empty($installError)) {
                        $logs[] = "Writing production settings to .env...";

                        $envSource = file_exists(ENV_FILE) ? ENV_FILE : (file_exists(ENV_EXAMPLE) ? ENV_EXAMPLE : '');
                        $envContent = $envSource ? file_get_contents($envSource) : '';

                        $replaceParams = [
                            'CI_ENVIRONMENT' => $appEnv,
                            'app.baseURL' => $appUrl,
                            'database.default.hostname' => $dbHost,
                            'database.default.database' => $dbName,
                            'database.default.username' => $dbUser,
                            'database.default.password' => $dbPass,
                            'database.default.DBDriver' => 'MySQLi',
                            'database.default.DBPrefix' => '',
                            'database.default.port'     => $dbPort,
                        ];

                        foreach ($replaceParams as $key => $val) {
                            $escapedKey = preg_quote($key, '/');
                            $quotedVal = is_string($val) && !is_numeric($val) && !in_array($val, ['production', 'development']) ? "'{$val}'" : $val;
                            if (preg_match("/^#?\s*{$escapedKey}\s*=/m", $envContent)) {
                                $envContent = preg_replace("/^#?\s*{$escapedKey}\s*=.*$/m", "{$key} = {$quotedVal}", $envContent);
                            } else {
                                $envContent .= "\n{$key} = {$quotedVal}";
                            }
                        }

                        // Ensure session.savePath = null is commented out to prevent session path errors
                        $envContent = preg_replace('/^app\.sessionSavePath\s*=\s*null/m', '# app.sessionSavePath = null', $envContent);

                        if (@file_put_contents(ENV_FILE, $envContent) !== false) {
                            $logs[] = "✓ Updated .env with database credentials and base URL";
                            $installSuccess = true;
                        } else {
                            $installError = "Could not write to .env. Please verify write permissions for " . ENV_FILE;
                        }
                    }
                }
            }
            $mysqli->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>First-Time Setup Wizard - MyFolioVault</title>
    <!-- 100% Offline Local Assets & Native Fonts -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        body.installer-page {
            background-color: #f1f5f9;
            min-height: 100vh;
            padding: 2.5rem 1rem;
        }
        .installer-container {
            max-width: 820px;
            margin: 0 auto;
        }
        .wizard-step-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #475569;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .wizard-step-badge.active {
            background: #2563eb;
            color: #fff;
        }
        .step-heading {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body class="installer-page">

<div class="installer-container">
    <!-- Header Brand -->
    <div class="text-center mb-4">
        <div class="brand-logo-icon mb-2" style="width: 48px; height: 48px; font-size: 1.5rem; display: inline-flex;">₹</div>
        <h3 class="fw-bold text-dark mb-1">MyFolioVault</h3>
        <p class="text-muted small mb-0">First-Time Web Installation &amp; Database Setup Wizard</p>
    </div>

    <?php if ($installSuccess): ?>
        <!-- SUCCESS COMPLETION SCREEN & SECURITY WARNING -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                    <i class="bi bi-check-lg"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">Installation Completed Successfully!</h3>
                <p class="text-muted small">Your database tables have been provisioned and your administrator account is ready.</p>
            </div>

            <!-- PROMINENT SECURITY ALERT (REQUIREMENT) -->
            <div class="alert alert-danger border-2 border-danger rounded-3 p-4 mb-4" role="alert">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-shield-slash-fill fs-2 text-danger"></i>
                    <div>
                        <h5 class="fw-bold text-danger mb-1">CRITICAL SECURITY WARNING: Delete install.php</h5>
                        <p class="small mb-3 text-dark">
                            Leaving <code>public/install.php</code> accessible allows anyone to re-run the setup wizard and overwrite your portfolio database. Click the button below to immediately delete this file from disk.
                        </p>
                        <form method="POST" action="install.php?action=delete_self" class="d-inline">
                            <input type="hidden" name="action" value="delete_self">
                            <button type="submit" class="btn btn-danger fw-semibold px-4 rounded-3">
                                <i class="bi bi-trash3-fill me-1"></i> Delete install.php Now (Recommended)
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Setup Details Summary -->
            <div class="bg-light p-3 rounded-3 border mb-4">
                <h6 class="fw-bold text-dark mb-2">Installation Summary</h6>
                <div class="row g-2 small">
                    <div class="col-sm-6">
                        <span class="text-muted">Application URL:</span> <strong class="text-dark"><?= htmlspecialchars($appUrl) ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Database:</span> <strong class="text-dark"><?= htmlspecialchars($dbName) ?></strong> (<?= htmlspecialchars($dbHost) ?>)
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Administrator Email:</span> <strong class="text-dark"><?= htmlspecialchars($adminEmail) ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Financial Year Starts:</span> <strong class="text-dark"><?= date('F', mktime(0, 0, 0, $fyStartMonth, 1)) ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted">Portfolio Data:</span> <strong class="text-dark"><?= !empty($installDemoData) ? 'Demo Sample Data Loaded' : 'Clean &amp; Empty Portfolio' ?></strong>
                    </div>
                </div>
            </div>

            <!-- Installation Activity Log -->
            <div class="mb-4">
                <label class="form-label small fw-semibold text-muted mb-1">Activity Log</label>
                <div class="bg-dark text-light p-3 rounded-3 font-monospace small" style="max-height: 160px; overflow-y: auto;">
                    <?php foreach ($logs as $log): ?>
                        <div><?= htmlspecialchars($log) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2">
                <span class="text-muted small">Indian Rupee (₹) Portfolio Edition</span>
                <a href="<?= htmlspecialchars(rtrim($appUrl, '/') . '/login') ?>" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Proceed to Login
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- INSTALLATION WIZARD FORM -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            
            <?php if (!empty($installError)): ?>
                <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-4 rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?= htmlspecialchars($installError) ?></div>
                </div>
            <?php endif; ?>

            <!-- System Pre-flight Checks -->
            <div class="mb-4">
                <div class="step-heading">
                    <span class="wizard-step-badge active">1</span>
                    <span>System Requirements &amp; Pre-flight Check</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-2">
                        <thead class="table-light small">
                            <tr>
                                <th>Component</th>
                                <th>Requirement</th>
                                <th>Current Value</th>
                                <th class="text-center" style="width: 100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php foreach ($requirements as $name => $item): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($name) ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($item['required']) ?></td>
                                    <td><?= htmlspecialchars($item['current']) ?></td>
                                    <td class="text-center">
                                        <?php if ($item['status']): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-lg"></i> Pass</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-x-lg"></i> Fail</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (!$allReqsPassed): ?>
                    <div class="alert alert-warning small py-2">
                        <i class="bi bi-info-circle me-1"></i> Some system checks did not pass. Please resolve them before submitting.
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" action="install.php">
                <input type="hidden" name="action" value="install">

                <!-- Step 2: Database Settings -->
                <div class="mb-4">
                    <div class="step-heading">
                        <span class="wizard-step-badge active">2</span>
                        <span>Database Connection &amp; Environment</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">Database Host</label>
                            <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars($_POST['db_host'] ?? $cfgHost) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Port</label>
                            <input type="number" name="db_port" class="form-control" value="<?= (int)($_POST['db_port'] ?? $cfgPort) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Database Username</label>
                            <input type="text" name="db_user" class="form-control" value="<?= htmlspecialchars($_POST['db_user'] ?? $cfgUser) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Database Password</label>
                            <input type="password" name="db_pass" class="form-control" value="<?= htmlspecialchars($_POST['db_pass'] ?? $cfgPass) ?>" placeholder="Leave blank if none">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">Database Name</label>
                            <input type="text" name="db_name" class="form-control" value="<?= htmlspecialchars($_POST['db_name'] ?? $cfgName) ?>" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="create_db" id="createDb" checked>
                                <label class="form-check-label small text-secondary" for="createDb">
                                    Create DB if missing
                                </label>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">Application Base URL</label>
                            <input type="text" name="app_url" class="form-control" value="<?= htmlspecialchars($_POST['app_url'] ?? $cfgUrl) ?>" required>
                            <div class="form-text small">Full URL including trailing slash (e.g. <code>http://localhost/Portfolio/</code>)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Environment</label>
                            <select name="app_env" class="form-select">
                                <option value="production" selected>Production</option>
                                <option value="development">Development</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Administrator Account Setup -->
                <div class="mb-4">
                    <div class="step-heading">
                        <span class="wizard-step-badge active">3</span>
                        <span>Administrator Account &amp; Financial Year Settings</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Administrator Name</label>
                            <input type="text" name="admin_name" class="form-control" value="<?= htmlspecialchars($_POST['admin_name'] ?? 'Administrator') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Administrator Email</label>
                            <input type="email" name="admin_email" class="form-control" value="<?= htmlspecialchars($_POST['admin_email'] ?? 'admin@portfolio.local') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Login Password</label>
                            <input type="password" name="admin_pass" class="form-control" placeholder="Minimum 6 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Confirm Password</label>
                            <input type="password" name="admin_pass2" class="form-control" placeholder="Confirm password" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Financial Year Start Month</label>
                            <select name="fy_start_month" class="form-select">
                                <option value="4" selected>April (Standard Indian FY: Apr - Mar)</option>
                                <option value="1">January (Calendar Year: Jan - Dec)</option>
                                <option value="7">July (Jul - Jun)</option>
                                <option value="10">October (Oct - Sep)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Default Rows Per Page</label>
                            <select name="records_per_page" class="form-select">
                                <option value="20" selected>20 records per page</option>
                                <option value="40">40 records per page</option>
                                <option value="50">50 records per page</option>
                                <option value="80">80 records per page</option>
                                <option value="100">100 records per page</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Demo / Sample Data Option -->
                <div class="mb-4">
                    <div class="step-heading">
                        <span class="wizard-step-badge active">4</span>
                        <span>Sample / Demo Portfolio Data</span>
                    </div>

                    <div class="card border border-primary-subtle bg-primary-subtle bg-opacity-10 rounded-3 p-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="install_demo_data" id="installDemoData" value="1" <?= !empty($_POST['install_demo_data']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-dark" for="installDemoData">
                                <i class="bi bi-database-fill-add text-primary me-1"></i> Install Demo Transactions &amp; Sample Portfolio Holdings
                            </label>
                            <div class="form-text small text-muted mt-1">
                                Pre-populates your portfolio with realistic test holdings across Equities, Mutual Funds, ETFs, Bonds, InvITs/REITs, and NPS. <strong>Leave unchecked (default: No) for a clean, blank portfolio database.</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Creates 25 tables, composite indexes &amp; administrator account</span>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">
                        <i class="bi bi-gear-fill me-1"></i> Install MyFolioVault
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="text-center mt-4 text-muted small">
        &copy; <?= date('Y') ?> MyFolioVault &bull; 100% Offline Compatible &bull; Indian Rupee (₹)
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

