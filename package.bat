@echo off
set VERSION=%1
if "%VERSION%"=="" set VERSION=v1.0.0

echo Building RupeeFolio package for %VERSION%...
echo.

if exist "D:\xampp\php\php.exe" (
    "D:\xampp\php\php.exe" package.php %VERSION%
) else if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" package.php %VERSION%
) else (
    php package.php %VERSION%
)

echo.
pause

