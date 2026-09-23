@echo off
setlocal
set PHP_BIN=php
if exist "D:\xampp\php\php.exe" set PHP_BIN="D:\xampp\php\php.exe"
if exist "C:\xampp\php\php.exe" set PHP_BIN="C:\xampp\php\php.exe"

%PHP_BIN% "%~dp0package.php" %*

