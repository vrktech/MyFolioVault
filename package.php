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

$version = $argv[1] ?? 'v1.0.0';
if (strpos($version, 'v') !== 0) {
    $version = 'v' . $version;
}

$rootPath = __DIR__;
$distDir  = $rootPath . DIRECTORY_SEPARATOR . 'dist';
$pkgName  = 'rupeefolio-' . $version . '-standalone';
$zipFile  = $distDir . DIRECTORY_SEPARATOR . $pkgName . '.zip';
$shaFile  = $zipFile . '.sha256';

echo "=====================================================\n";
echo " RupeeFolio - Standalone Production Packager\n";
echo " Version: {$version}\n";
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
    'app'          => true,
    'public'       => true,
    'system'       => true,
    '.htaccess'    => false,
    'index.php'    => false,
    'schema.sql'   => false,
    'README.md'    => false,
    'USER_GUIDE.md'=> false,
    'LICENSE'      => false,
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

        if ($file->isDir()) {
            $zip->addEmptyDir($relPath);
        } else {
            $zip->addFile($filePath, $relPath);
            $fileCount++;
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

