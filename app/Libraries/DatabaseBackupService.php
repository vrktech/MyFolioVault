<?php

namespace App\Libraries;

use Exception;
use mysqli;
use ZipArchive;

class DatabaseBackupService
{
    /**
     * Tables excluded from backup and restore to protect credentials and system migration state.
     */
    protected const EXCLUDED_TABLES = ['users', 'migrations'];

    /**
     * Obtain a native MySQLi connection based on CodeIgniter's database configuration.
     *
     * @return mysqli
     * @throws Exception
     */
    protected function getConnection(): mysqli
    {
        $db = \Config\Database::connect();

        $dbHost = $db->hostname ?: 'localhost';
        $dbUser = $db->username ?: 'root';
        $dbPass = $db->password ?: '';
        $dbName = $db->database ?: '';
        $dbPort = (int)($db->port ?: 3306);

        if (empty($dbName)) {
            throw new Exception("Database configuration error: Database name is not specified.");
        }

        mysqli_report(MYSQLI_REPORT_OFF);
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
        if ($mysqli->connect_errno) {
            throw new Exception("Database connection failed: " . $mysqli->connect_error);
        }

        $mysqli->set_charset('utf8mb4');
        return $mysqli;
    }

    /**
     * Get list of database tables eligible for backup/restore (excluding users and migrations).
     *
     * @param mysqli $mysqli
     * @return array
     */
    public function getEligibleTables(mysqli $mysqli): array
    {
        $tables = [];
        $result = $mysqli->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                $tableName = $row[0];
                if (!in_array($tableName, self::EXCLUDED_TABLES, true)) {
                    $tables[] = $tableName;
                }
            }
            $result->free();
        }
        sort($tables);
        return $tables;
    }

    /**
     * Get overview statistics of the database for the settings UI.
     *
     * @return array
     */
    public function getDatabaseStats(): array
    {
        try {
            $mysqli = $this->getConnection();
            $tables = $this->getEligibleTables($mysqli);

            $db     = \Config\Database::connect();
            $dbName = $db->database ?: '';

            $totalRecords = 0;
            $tableStats   = [];

            foreach ($tables as $t) {
                $cntRes = $mysqli->query("SELECT COUNT(*) AS c FROM `{$t}`");
                $count  = 0;
                if ($cntRes) {
                    $row = $cntRes->fetch_assoc();
                    $count = (int)($row['c'] ?? 0);
                    $cntRes->free();
                }
                $totalRecords += $count;
                $tableStats[] = [
                    'name' => $t,
                    'rows' => $count,
                ];
            }

            // Estimate total size in MB from information_schema
            $sizeMb = 0.0;
            $sizeRes = $mysqli->query("SELECT ROUND(SUM(data_length + index_length) / (1024 * 1024), 2) AS size_mb 
                                       FROM information_schema.TABLES 
                                       WHERE table_schema = '" . $mysqli->real_escape_string($dbName) . "'");
            if ($sizeRes) {
                $row = $sizeRes->fetch_assoc();
                $sizeMb = (float)($row['size_mb'] ?? 0.0);
                $sizeRes->free();
            }

            $mysqli->close();

            return [
                'success'        => true,
                'total_tables'   => count($tables),
                'total_records'  => $totalRecords,
                'database_size'  => $sizeMb,
                'tables'         => $tableStats,
                'excluded_info'  => 'users, migrations',
            ];
        } catch (Exception $e) {
            return [
                'success'       => false,
                'error'         => $e->getMessage(),
                'total_tables'  => 0,
                'total_records' => 0,
                'database_size' => 0.0,
                'tables'        => [],
                'excluded_info' => 'users, migrations',
            ];
        }
    }

    /**
     * Generate a full zipped SQL backup of portfolio data (excluding users and migrations).
     *
     * @return array Information about the generated backup file.
     * @throws Exception
     */
    public function generateBackupZip(): array
    {
        if (!extension_loaded('zip')) {
            throw new Exception("The PHP Zip extension is required to generate backup archives.");
        }

        $mysqli = $this->getConnection();
        $tables = $this->getEligibleTables($mysqli);

        $writePath = defined('WRITEPATH') ? rtrim(WRITEPATH, '\\/') : realpath(APPPATH . '../writable');
        $backupDir = $writePath . DIRECTORY_SEPARATOR . 'backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_His');
        $sqlFile   = $backupDir . DIRECTORY_SEPARATOR . "backup_{$timestamp}.sql";
        $zipFile   = $backupDir . DIRECTORY_SEPARATOR . "myfoliovault_backup_{$timestamp}.zip";

        $handle = fopen($sqlFile, 'w');
        if (!$handle) {
            $mysqli->close();
            throw new Exception("Unable to write temporary SQL backup file at: {$sqlFile}");
        }

        // Header and environment setup
        $header = "-- =========================================================\n"
                . "-- MyFolioVault Database Backup\n"
                . "-- Generated At: " . date('Y-m-d H:i:s') . "\n"
                . "-- Excluded Tables: users, migrations\n"
                . "-- =========================================================\n\n"
                . "SET FOREIGN_KEY_CHECKS = 0;\n"
                . "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n"
                . "SET NAMES utf8mb4;\n\n";
        fwrite($handle, $header);

        $totalRecords = 0;
        $tableCounts  = [];

        foreach ($tables as $table) {
            // Write table header and truncate statement
            fwrite($handle, "--\n-- Data dump for table `{$table}`\n--\n");
            fwrite($handle, "TRUNCATE TABLE `{$table}`;\n");

            // Fetch columns to guarantee correct column mapping
            $colRes = $mysqli->query("SHOW COLUMNS FROM `{$table}`");
            $columns = [];
            if ($colRes) {
                while ($c = $colRes->fetch_assoc()) {
                    $columns[] = '`' . $c['Field'] . '`';
                }
                $colRes->free();
            }
            $colList = implode(', ', $columns);

            $rowsRes = $mysqli->query("SELECT * FROM `{$table}`");
            $count = 0;
            if ($rowsRes) {
                $batchRows = [];
                while ($row = $rowsRes->fetch_assoc()) {
                    $count++;
                    $values = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . $mysqli->real_escape_string((string)$val) . "'";
                        }
                    }
                    $batchRows[] = '(' . implode(', ', $values) . ')';

                    // Flush in chunks of 100 rows
                    if (count($batchRows) >= 100) {
                        $sql = "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $batchRows) . ";\n";
                        fwrite($handle, $sql);
                        $batchRows = [];
                    }
                }
                if (!empty($batchRows)) {
                    $sql = "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $batchRows) . ";\n";
                    fwrite($handle, $sql);
                }
                $rowsRes->free();
            }

            fwrite($handle, "\n");
            $totalRecords += $count;
            $tableCounts[$table] = $count;
        }

        // Re-enable foreign key checks at end of dump
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($handle);
        $mysqli->close();

        // 2. Prepare manifest.json
        $manifest = [
            'app'           => 'MyFolioVault',
            'version'       => defined('APP_VERSION') ? APP_VERSION : '1.0.5',
            'created_at'    => date('Y-m-d H:i:s'),
            'tables_count'  => count($tables),
            'total_records' => $totalRecords,
            'tables'        => $tableCounts,
            'excluded'      => self::EXCLUDED_TABLES,
        ];
        $manifestJson = json_encode($manifest, JSON_PRETTY_PRINT);

        // 3. Package into ZIP
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($sqlFile);
            throw new Exception("Unable to create zip archive at: {$zipFile}");
        }

        $zip->addFile($sqlFile, "backup_{$timestamp}.sql");
        $zip->addFromString('manifest.json', $manifestJson);
        $zip->close();

        // Remove uncompressed SQL file
        @unlink($sqlFile);

        return [
            'file_path'     => $zipFile,
            'filename'      => basename($zipFile),
            'tables_count'  => count($tables),
            'total_records' => $totalRecords,
            'file_size'     => filesize($zipFile),
        ];
    }

    /**
     * Restore database data from an uploaded backup ZIP file.
     *
     * @param string $zipFilePath Absolute path to the uploaded ZIP file.
     * @return array Execution statistics.
     * @throws Exception
     */
    public function restoreFromZip(string $zipFilePath): array
    {
        if (!extension_loaded('zip')) {
            throw new Exception("The PHP Zip extension is required to extract backup archives.");
        }

        if (!file_exists($zipFilePath)) {
            throw new Exception("Uploaded backup file could not be found on server.");
        }

        $writePath = defined('WRITEPATH') ? rtrim(WRITEPATH, '\\/') : realpath(APPPATH . '../writable');
        $extractDir = $writePath . DIRECTORY_SEPARATOR . 'restore_temp_' . uniqid('', true);
        if (!mkdir($extractDir, 0755, true) && !is_dir($extractDir)) {
            throw new Exception("Unable to create temporary extraction directory.");
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) !== true) {
            $this->cleanupDir($extractDir);
            throw new Exception("Invalid or corrupt ZIP archive. Please select a valid MyFolioVault backup file.");
        }

        $zip->extractTo($extractDir);
        $zip->close();

        // Locate .sql file inside extracted archive
        $sqlFile = null;
        $files = scandir($extractDir);
        foreach ($files as $f) {
            if ($f !== '.' && $f !== '..' && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'sql') {
                $sqlFile = $extractDir . DIRECTORY_SEPARATOR . $f;
                break;
            }
        }

        if (!$sqlFile || !file_exists($sqlFile)) {
            $this->cleanupDir($extractDir);
            throw new Exception("Invalid backup archive: No .sql file found inside the ZIP package.");
        }

        $sqlContent = file_get_contents($sqlFile);
        if (trim($sqlContent) === '') {
            $this->cleanupDir($extractDir);
            throw new Exception("The backup SQL file inside the archive is empty.");
        }

        // Safety Pre-Restore Snapshot: save current tables in case rollback is needed
        try {
            $backupDir = $writePath . DIRECTORY_SEPARATOR . 'backups';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            $this->createSafetySnapshot($backupDir . DIRECTORY_SEPARATOR . 'safety_pre_restore.sql');
        } catch (Exception $e) {
            log_message('error', 'Pre-restore safety snapshot warning: ' . $e->getMessage());
        }

        // Execute restore queries via MySQLi multi-query
        $mysqli = $this->getConnection();
        $mysqli->query("SET FOREIGN_KEY_CHECKS = 0;");
        $mysqli->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");

        // Execute the entire SQL script
        if ($mysqli->multi_query($sqlContent)) {
            do {
                if ($res = $mysqli->store_result()) {
                    $res->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());
        }

        if ($mysqli->errno) {
            $errMsg = $mysqli->error;
            $mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");
            $mysqli->close();
            $this->cleanupDir($extractDir);
            throw new Exception("Database restore failed while executing SQL: " . $errMsg);
        }

        $mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");
        $mysqli->close();

        // Cleanup temporary directory
        $this->cleanupDir($extractDir);

        return [
            'status' => 'success',
            'restored_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Create an uncompressed safety snapshot of all current data before performing a restore.
     *
     * @param string $destPath
     */
    protected function createSafetySnapshot(string $destPath): void
    {
        $mysqli = $this->getConnection();
        $tables = $this->getEligibleTables($mysqli);

        $h = fopen($destPath, 'w');
        if (!$h) {
            $mysqli->close();
            return;
        }

        fwrite($h, "SET FOREIGN_KEY_CHECKS = 0;\nSET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n");

        foreach ($tables as $table) {
            fwrite($h, "TRUNCATE TABLE `{$table}`;\n");
            $res = $mysqli->query("SELECT * FROM `{$table}`");
            if ($res) {
                $batch = [];
                while ($row = $res->fetch_assoc()) {
                    $vals = [];
                    foreach ($row as $v) {
                        $vals[] = ($v === null) ? 'NULL' : "'" . $mysqli->real_escape_string((string)$v) . "'";
                    }
                    $batch[] = '(' . implode(', ', $vals) . ')';
                    if (count($batch) >= 100) {
                        fwrite($h, "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $batch) . ";\n");
                        $batch = [];
                    }
                }
                if (!empty($batch)) {
                    fwrite($h, "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $batch) . ";\n");
                }
                $res->free();
            }
        }

        fwrite($h, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($h);
        $mysqli->close();
    }

    /**
     * Recursively delete a directory and its contents.
     *
     * @param string $dir
     */
    protected function cleanupDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->cleanupDir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}
