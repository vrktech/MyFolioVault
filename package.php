<?php
/**
 * RupeeFolio - Local Standalone Release Packager
 * 
 * Usage:
 *   php package.php
 *   php package.php v1.0.0
 * 
 * Generates:
 *   dist/rupeefolio-v{VERSION}-standalone.zip
 *   dist/rupeefolio-v{VERSION}-standalone.zip.sha256
 */

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

$rootPath    = __DIR__;
$versionFile = $rootPath . DIRECTORY_SEPARATOR . 'VERSION';

// 1. Read existing version from VERSION file
$currentVersion = '1.0.0';
if (file_exists($versionFile)) {
    $trimmed = trim(file_get_contents($versionFile));
    if ($trimmed !== '') {
        $currentVersion = ltrim($trimmed, 'vV');
    }
}

// Parse semantic version triplets (MAJOR.MINOR.PATCH)
if (!preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $currentVersion, $matches)) {
    $currentVersion = '1.0.0';
    $matches = [0, 1, 0, 0];
}

$major = (int) $matches[1];
$minor = (int) $matches[2];
$patch = (int) $matches[3];

$arg = isset($argv[1]) ? strtolower(trim($argv[1])) : '';
$versionChanged = false;
$newVersion = $currentVersion;

if ($arg === '-patch' || $arg === '--patch' || $arg === 'patch') {
    $patch++;
    $newVersion = "{$major}.{$minor}.{$patch}";
    $versionChanged = true;
} elseif ($arg === '-minor' || $arg === '--minor' || $arg === 'minor') {
    $minor++;
    $patch = 0;
    $newVersion = "{$major}.{$minor}.{$patch}";
    $versionChanged = true;
} elseif ($arg === '-major' || $arg === '--major' || $arg === 'major') {
    $major++;
    $minor = 0;
    $patch = 0;
    $newVersion = "{$major}.{$minor}.{$patch}";
    $versionChanged = true;
} elseif ($arg !== '' && preg_match('/^v?(\d+)\.(\d+)\.(\d+)$/i', $arg, $customMatches)) {
    $newVersion = "{$customMatches[1]}.{$customMatches[2]}.{$customMatches[3]}";
    $versionChanged = ($newVersion !== $currentVersion);
} elseif ($arg !== '') {
    echo "Error: Unknown argument '{$argv[1]}'.\n\n";
    echo "Usage:\n";
    echo "  pack -patch    Increment patch version (e.g. 1.0.0 -> 1.0.1) for bug fixes\n";
    echo "  pack -minor    Increment minor version (e.g. 1.0.1 -> 1.1.0) for feature updates\n";
    echo "  pack -major    Increment major version (e.g. 1.1.0 -> 2.0.0) for major releases\n";
    echo "  pack           Package current version without incrementing ({$currentVersion})\n";
    echo "  pack 1.2.3     Explicitly set release version\n";
    exit(1);
}

// Persist updated version to VERSION file
if ($versionChanged || !file_exists($versionFile)) {
    file_put_contents($versionFile, $newVersion . "\n");
}

$versionTag = 'v' . $newVersion;
$distDir  = $rootPath . DIRECTORY_SEPARATOR . 'dist';
$pkgName  = 'rupeefolio-' . $versionTag . '-standalone';
$zipFile  = $distDir . DIRECTORY_SEPARATOR . $pkgName . '.zip';
$shaFile  = $zipFile . '.sha256';

echo "=====================================================\n";
echo " RupeeFolio - Standalone Production Packager\n";
if ($versionChanged) {
    echo " Version: v{$currentVersion} -> {$versionTag}\n";
} else {
    echo " Version: {$versionTag} (current)\n";
}
echo " Target:  {$zipFile}\n";
echo "=====================================================\n\n";

if (!extension_loaded('zip')) {
    die("Error: PHP zip extension is required to package release archives.\n");
}

if (!is_dir($distDir)) {
    mkdir($distDir, 0755, true);
}

if (file_exists($zipFile)) {
    @unlink($zipFile);
}
if (file_exists($shaFile)) {
    @unlink($shaFile);
}

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Error: Cannot create zip archive at {$zipFile}\n");
}

// 1. Files & Directories to include
$includeItems = [
    'app'             => true,
    'public'          => true,
    'system'          => true,
    '.htaccess'       => false,
    'index.php'       => false,
    'VERSION'         => false,
    'schema.sql'      => false,
    'sample_data.sql' => false,
    'README.md'       => false,
    'USER_GUIDE.md'   => false,
    'LICENSE'         => false,
];

// If .env.example exists, include it as 'env'
if (file_exists($rootPath . DIRECTORY_SEPARATOR . '.env.example')) {
    $zip->addFile($rootPath . DIRECTORY_SEPARATOR . '.env.example', 'env');
    echo "[+] Added: env (from .env.example)\n";
}

$excludePatterns = [
    '/\.git/',
    '/\.env$/',
    '/tests/',
    '/phpunit/',
    '/spark$/',
    '/preload\.php$/',
    '/\.log$/',
    '/dist/',
    '/writable[\\\\\/]debugbar/',
    '/writable[\\\\\/]cache[\\\\\/].+/',
    '/writable[\\\\\/]logs[\\\\\/].+/',
    '/writable[\\\\\/]session[\\\\\/].+/',
    '/writable[\\\\\/]uploads[\\\\\/].+/',
    '/backup_sample_data\.sql/',
    '/composer\.phar/',
    '/package\.php$/',
];

$fileCount = 0;

foreach ($includeItems as $item => $isDir) {
    $srcPath = $rootPath . DIRECTORY_SEPARATOR . $item;
    if (!file_exists($srcPath)) {
        if ($item === 'system') {
            echo "[!] Warning: 'system/' directory not found. If this is a git checkout, run composer install or download CodeIgniter core.\n";
        }
        continue;
    }

    if (!$isDir) {
        $zip->addFile($srcPath, $item);
        $fileCount++;
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        $filePath = $file->getRealPath();
        $relPath  = substr($filePath, strlen($rootPath) + 1);

        // Check exclusions
        $skip = false;
        foreach ($excludePatterns as $pattern) {
            if (preg_match($pattern, $relPath)) {
                $skip = true;
                break;
            }
        }
        if ($skip) continue;

        $zipEntry = str_replace('\\', '/', $relPath);

        if ($file->isDir()) {
            $zip->addEmptyDir($zipEntry);
        } else {
            // In standalone package release, remove demo credentials helper and prefilled inputs on login screen
            if ($zipEntry === 'app/Views/auth/login.php') {
                $loginContent = file_get_contents($filePath);

                // 1. Remove pre-configured demo account hint box
                $loginContent = preg_replace('/<!--\s*DEMO_CREDENTIALS_START\s*-->.*?<!--\s*DEMO_CREDENTIALS_END\s*-->/s', '', $loginContent);

                // 2. Clear pre-filled demo email
                $loginContent = str_replace("value=\"<?= old('email', 'admin@portfolio.local') ?>\"", "value=\"<?= old('email') ?>\"", $loginContent);

                // 3. Clear pre-filled demo password
                $loginContent = str_replace('value="password123"', 'value=""', $loginContent);

                $zip->addFromString($zipEntry, $loginContent);
                $fileCount++;
                echo "[*] Production Filter applied to: {$zipEntry} (removed demo credentials box & prefilled values)\n";
            } else {
                $zip->addFile($filePath, $zipEntry);
                $fileCount++;
            }
        }
    }
}

// 2. Ensure writable directories structure with silence index.html
$writableDirs = ['cache', 'logs', 'session', 'uploads'];
$silenceHtml = "<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>\n";

foreach ($writableDirs as $dir) {
    $relDir = 'writable/' . $dir;
    $zip->addEmptyDir($relDir);
    $zip->addFromString($relDir . '/index.html', $silenceHtml);
    $fileCount++;
}

$zip->close();

// 3. Compute SHA-256 Checksum
$sha256 = hash_file('sha256', $zipFile);
file_put_contents($shaFile, "{$sha256}  " . basename($zipFile) . "\n");

$sizeMb = round(filesize($zipFile) / (1024 * 1024), 2);

echo "\n=====================================================\n";
echo " Packaging Completed Successfully!\n";
echo " Total Files Packaged: {$fileCount}\n";
echo " Archive Size:         {$sizeMb} MB\n";
echo " Checksum (SHA-256):   {$sha256}\n";
echo " Output ZIP:           {$zipFile}\n";
echo " Output Checksum:      {$shaFile}\n";
echo "=====================================================\n";
echo "Ready to upload to GitHub Releases!\n";

