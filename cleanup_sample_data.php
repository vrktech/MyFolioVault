<?php
/**
 * Standalone Sample Data Cleanup Script
 * Investment Portfolio Tracker
 *
 * Usage: php cleanup_sample_data.php
 */

define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);

// 1. Read .env if present
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'investment_portfolio';
$dbPort = 3306;

if (file_exists(ROOTPATH . '.env')) {
    $envLines = file(ROOTPATH . '.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '#')) continue;
        if (preg_match('/^database\.default\.hostname\s*=\s*(.*)$/', $line, $m)) $dbHost = trim($m[1], "'\" ");
        if (preg_match('/^database\.default\.database\s*=\s*(.*)$/', $line, $m)) $dbName = trim($m[1], "'\" ");
        if (preg_match('/^database\.default\.username\s*=\s*(.*)$/', $line, $m)) $dbUser = trim($m[1], "'\" ");
        if (preg_match('/^database\.default\.password\s*=\s*(.*)$/', $line, $m)) $dbPass = trim($m[1], "'\" ");
        if (preg_match('/^database\.default\.port\s*=\s*(.*)$/', $line, $m)) $dbPort = (int)trim($m[1], "'\" ");
    }
}

echo "========================================================\n";
echo "Investment Portfolio Tracker - Sample Data Cleanup\n";
echo "========================================================\n";
echo "Connecting to MySQL at {$dbHost}:{$dbPort} for database '{$dbName}' as user '{$dbUser}'...\n";

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($mysqli->connect_error) {
    die("ERROR: Failed to connect to MySQL: " . $mysqli->connect_error . "\n");
}
$mysqli->set_charset('utf8mb4');

// 2. Ensure backup file exists
$backupFile = ROOTPATH . 'backup_sample_data.sql';
if (!file_exists($backupFile) || filesize($backupFile) === 0) {
    echo "Creating backup of current portfolio data to 'backup_sample_data.sql'...\n";
    $mysqldumpPath = 'mysqldump';
    if (file_exists('D:\\xampp\\mysql\\bin\\mysqldump.exe')) {
        $mysqldumpPath = 'D:\\xampp\\mysql\\bin\\mysqldump.exe';
    }
    $cmd = "\"{$mysqldumpPath}\" -h {$dbHost} -P {$dbPort} -u {$dbUser} " . (!empty($dbPass) ? "-p{$dbPass} " : "") . "{$dbName} > \"{$backupFile}\"";
    exec($cmd, $output, $ret);
    if ($ret === 0 && file_exists($backupFile)) {
        echo "✓ Successfully backed up data to {$backupFile} (" . filesize($backupFile) . " bytes)\n";
    } else {
        echo "! Notice: mysqldump command returned code {$ret}. Proceeding with cleanup...\n";
    }
} else {
    echo "✓ Found existing backup file 'backup_sample_data.sql' (" . filesize($backupFile) . " bytes)\n";
}

// 3. Truncate sample transaction & holding tables in safe dependency order
$tablesToTruncate = [
    // Bonds
    'bond_capital_gains',
    'bond_interest_payouts',
    'bond_transactions',
    'bonds',

    // Equities
    'corporate_actions',
    'equity_capital_gains',
    'equity_dividends',
    'equity_transactions',
    'equities',

    // InvITs & REITs
    'reit_invit_capital_gains',
    'reit_invit_distributions',
    'reit_invit_transactions',
    'reits_invits',

    // ETFs
    'etf_capital_gains',
    'etf_transactions',
    'etfs',

    // Mutual Funds
    'mutual_fund_capital_gains',
    'mutual_fund_transactions',
    'mutual_funds',

    // NPS
    'nps_scheme_units',
    'nps_transactions',
    'nps_accounts',
];

echo "\nTruncating portfolio mock data across 22 tables...\n";
$mysqli->query("SET FOREIGN_KEY_CHECKS = 0");

$truncatedCount = 0;
foreach ($tablesToTruncate as $tbl) {
    // Check if table exists
    $res = $mysqli->query("SHOW TABLES LIKE '{$tbl}'");
    if ($res && $res->num_rows > 0) {
        $mysqli->query("TRUNCATE TABLE `{$tbl}`");
        echo "  - Truncated `{$tbl}`\n";
        $truncatedCount++;
    } else {
        echo "  - Skipped `{$tbl}` (table does not exist)\n";
    }
}

$mysqli->query("SET FOREIGN_KEY_CHECKS = 1");

echo "\n✓ Cleanup complete! {$truncatedCount} tables were reset.\n";
echo "✓ Master data preserved: `equity_sectors` & `users`.\n";
echo "✓ The application database is now 100% clean and ready for live personal investments.\n";
echo "========================================================\n";
