<?php
/**
 * MyFolioVault - Data Export Helpers
 *
 * Provides standardized CSV and data download streaming with UTF-8 BOM
 * support for Microsoft Excel and third-party accounting compatibility.
 */

if (!function_exists('csv_download')) {
    /**
     * Stream a formatted CSV file directly to the client browser.
     *
     * @param string $filename  Output filename (e.g. 'capital_gains_fy26.csv')
     * @param array  $headers   Table column headers (e.g. ['Symbol', 'Buy Date', 'LTCG'])
     * @param array  $rows      Array of data rows (each row is an array of values)
     * @param array  $metadata  Optional report title / metadata lines rendered above table
     * @return never
     */
    function csv_download(string $filename, array $headers, array $rows, array $metadata = []): void
    {
        // Clean all existing output buffers to prevent corruption
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Ensure .csv extension
        if (!str_ends_with(strtolower($filename), '.csv')) {
            $filename .= '.csv';
        }

        // Set download headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . rawurlencode($filename) . '"; filename*=UTF-8\'\'' . rawurlencode($filename));
        header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        // Write UTF-8 Byte Order Mark (BOM) so Excel respects UTF-8 encoding (₹ symbols, accents)
        fwrite($out, "\xEF\xBB\xBF");

        // Write metadata header lines if provided
        if (!empty($metadata)) {
            foreach ($metadata as $metaLine) {
                if (is_array($metaLine)) {
                    fputcsv($out, $metaLine);
                } elseif ($metaLine === '') {
                    fputcsv($out, []);
                } else {
                    fputcsv($out, [(string) $metaLine]);
                }
            }
            fputcsv($out, []); // Blank separator row
        }

        // Write column headers
        if (!empty($headers)) {
            fputcsv($out, $headers);
        }

        // Write table rows
        foreach ($rows as $row) {
            if (is_array($row)) {
                $formattedRow = array_map(function ($val) {
                    if (is_float($val)) {
                        return number_format($val, 2, '.', '');
                    }
                    if ($val === null) {
                        return '';
                    }
                    return (string) $val;
                }, array_values($row));

                fputcsv($out, $formattedRow);
            }
        }

        fclose($out);
        exit;
    }
}

