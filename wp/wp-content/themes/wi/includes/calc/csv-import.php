<?php

declare(strict_types=1);

/**
 * CSV import for calculator rates: validation, parse, batch insert, history.
 */

if (! defined('ABSPATH')) {
    return;
}

/**
 * Parse CSV file (separator ;). Skip empty lines.
 *
 * @param string $path Full filesystem path to CSV
 * @return array<int, array<int, string>> Rows as arrays of column values
 */
function wi_calc_parse_csv_file(string $path): array
{
    $rows = [];
    $h    = fopen($path, 'r');
    if (! $h) {
        return [];
    }
    while (($line = fgetcsv($h, 10000, ';')) !== false) {
        $rows[] = $line;
    }
    fclose($h);
    return $rows;
}

/**
 * Normalize numeric value from CSV (remove spaces, "zł", take int).
 *
 * @param string $value
 * @return int
 */
function wi_calc_normalize_int(string $value): int
{
    $value = str_replace(["\xC2\xA0", ' ', ' ', 'zł', 'z', 'ł'], '', $value);
    $value = preg_replace('/[^0-9-]/', '', $value);
    return (int) $value;
}

/**
 * Normalize percent from CSV (expect "0", "10", "20" possibly with %).
 *
 * @param string $value
 * @return int
 */
function wi_calc_normalize_percent(string $value): int
{
    $value = trim(str_replace('%', '', $value));
    return (int) $value;
}

/**
 * Validate a single data row. Columns: 0=car_id, 1=idv, 3=month, 4=km, 5=percent, 6=fee, 7=rate.
 *
 * @param array<int, string> $row
 * @param int $lineNum 1-based line number for error messages
 * @return array{valid: bool, data: array<string, mixed>|null, error: string|null}
 */
function wi_calc_validate_row(array $row, int $lineNum): array
{
    if (count($row) < 8) {
        return ['valid' => false, 'data' => null, 'error' => "Line {$lineNum}: insufficient columns (need at least 8)"];
    }

    $carId   = trim((string) $row[0]);
    $idv     = trim((string) $row[1]);
    $month   = wi_calc_normalize_int((string) $row[3]);
    $km      = wi_calc_normalize_int((string) $row[4]);
    $percent = wi_calc_normalize_percent((string) $row[5]);
    $fee     = wi_calc_normalize_int((string) $row[6]);
    $rate    = wi_calc_normalize_int((string) $row[7]);

    if ($carId === '' && $idv === '') {
        return ['valid' => false, 'data' => null, 'error' => null];
    }

    if ($carId === '' || ! ctype_digit($carId)) {
        return ['valid' => false, 'data' => null, 'error' => "Line {$lineNum}: car ID must be a non-empty number"];
    }
    if ($idv === '') {
        return ['valid' => false, 'data' => null, 'error' => "Line {$lineNum}: IDV is required"];
    }
    if (! in_array($month, [24, 36, 48], true)) {
        return ['valid' => false, 'data' => null, 'error' => "Line {$lineNum}: month must be 24, 36 or 48"];
    }
    if ($km <= 0) {
        return ['valid' => false, 'data' => null, 'error' => "Line {$lineNum}: km must be positive"];
    }
    if (! in_array($percent, [0, 10, 20], true)) {
        return ['valid' => false, 'data' => null, 'error' => "Line {$lineNum}: percent must be 0, 10 or 20"];
    }
    if ($fee < 0) {
        return ['valid' => false, 'data' => null, 'error' => "Line {$lineNum}: fee cannot be negative"];
    }
    if ($rate <= 0) {
        return ['valid' => false, 'data' => null, 'error' => "Line {$lineNum}: rate must be positive"];
    }

    return [
        'valid' => true,
        'data'  => [
            'car_id'  => $carId,
            'idv'     => $idv,
            'month'   => $month,
            'km'      => $km,
            'percent' => $percent,
            'fee'     => $fee,
            'rate'    => $rate,
        ],
        'error' => null,
    ];
}

/**
 * Validate all data rows. Skip header (first line) and empty rows.
 *
 * @param array<int, array<int, string>> $rows
 * @param int $previewRows Number of valid rows to include as preview
 * @return array{valid_rows: array<int, array<string, mixed>>, errors: array<int, string>, preview: array<int, array<string, mixed>>, total_data_rows: int}
 */
function wi_calc_validate_csv(array $rows, int $previewRows = 10): array
{
    $validRows = [];
    $errors    = [];
    $preview   = [];
    $lineNum   = 0;

    foreach ($rows as $row) {
        $lineNum++;
        if ($lineNum === 1) {
            continue;
        }
        $trimmed = array_map('trim', $row);
        if (implode('', $trimmed) === '') {
            continue;
        }

        $result = wi_calc_validate_row($row, $lineNum);
        if ($result['error'] !== null) {
            $errors[] = $result['error'];
            continue;
        }
        if (! $result['valid'] || $result['data'] === null) {
            continue;
        }

        $validRows[] = $result['data'];
        if (count($preview) < $previewRows) {
            $preview[] = $result['data'];
        }
    }

    return [
        'valid_rows'     => $validRows,
        'errors'         => $errors,
        'preview'        => $preview,
        'total_data_rows' => count($validRows) + count($errors),
    ];
}

/**
 * Import validated rows into wi_calc_rates. Replace all or append.
 *
 * @param array<int, array<string, mixed>> $validRows
 * @param string $mode 'replace' or 'append'
 * @param int $uploadedBy WordPress user ID
 * @param string $originalName Original filename for history
 * @return array{success: bool, rows_imported: int, cars_affected: int, status: string, error_message: string|null}
 */
function wi_calc_import_csv(array $validRows, string $mode, int $uploadedBy, string $originalName = ''): array
{
    if (! class_exists('CalcRate') || ! class_exists('CalcUpload')) {
        return [
            'success'        => false,
            'rows_imported'  => 0,
            'cars_affected'  => 0,
            'status'        => 'error',
            'error_message' => 'Models not loaded',
        ];
    }

    $rowsImported = 0;
    $carsAffected = 0;
    $status       = 'success';
    $errorMessage = null;

    try {
        if ($mode === 'replace') {
            CalcRate::query()->truncate();
        }

        $chunkSize = 500;
        $carIds    = [];
        foreach (array_chunk($validRows, $chunkSize) as $chunk) {
            foreach ($chunk as $row) {
                CalcRate::create($row);
                $rowsImported++;
                $carIds[$row['car_id']] = true;
            }
        }
        $carsAffected = count($carIds);

        CalcUpload::create([
            'filename'       => basename($originalName) ?: 'import.csv',
            'original_name'  => $originalName,
            'rows_imported'  => $rowsImported,
            'cars_affected'  => $carsAffected,
            'status'         => $status,
            'error_message'  => $errorMessage,
            'uploaded_by'    => $uploadedBy,
        ]);
    } catch (Throwable $e) {
        $status       = 'error';
        $errorMessage = $e->getMessage();
        CalcUpload::create([
            'filename'       => basename($originalName) ?: 'import.csv',
            'original_name'  => $originalName,
            'rows_imported'  => $rowsImported,
            'cars_affected'  => 0,
            'status'         => $status,
            'error_message'  => $errorMessage,
            'uploaded_by'    => $uploadedBy,
        ]);
        return [
            'success'        => false,
            'rows_imported'  => $rowsImported,
            'cars_affected'  => 0,
            'status'        => $status,
            'error_message' => $errorMessage,
        ];
    }

    wi_calc_update_cena_od_for_all_cars();

    return [
        'success'        => true,
        'rows_imported'  => $rowsImported,
        'cars_affected'  => $carsAffected,
        'status'        => $status,
        'error_message' => $errorMessage,
    ];
}

/**
 * Update ACF field cena_od on all offer posts from min rate in wi_calc_rates.
 */
function wi_calc_update_cena_od_for_all_cars(): void
{
    if (! function_exists('get_field') || ! function_exists('update_field') || ! function_exists('wpmlID')) {
        return;
    }

    $minRates = CalcRate::query()
        ->selectRaw('car_id, MIN(rate) as min_rate')
        ->groupBy('car_id')
        ->pluck('min_rate', 'car_id');

    $query = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    ]);

    foreach ($query->posts as $post) {
        $carId = get_field('id', $post->ID);
        if ($carId === null || $carId === '' || $carId === false) {
            continue;
        }
        $carId = (string) $carId;
        if (isset($minRates[$carId])) {
            update_field('cena_od', (int) $minRates[$carId], $post->ID);
        }
    }
}
