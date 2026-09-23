# MyFolioVault Packager - PowerShell Wrapper
$phpBin = "php"
if (Test-Path "D:\xampp\php\php.exe") {
    $phpBin = "D:\xampp\php\php.exe"
} elseif (Test-Path "C:\xampp\php\php.exe") {
    $phpBin = "C:\xampp\php\php.exe"
}

$scriptPath = Join-Path $PSScriptRoot "package.php"
& $phpBin $scriptPath @args

