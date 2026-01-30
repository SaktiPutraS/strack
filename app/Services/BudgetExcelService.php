<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetItem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class BudgetExcelService
{
    /**
     * Export budget items to Excel
     */
    public function export(Budget $budget): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Budget Items');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);   // ID
        $sheet->getColumnDimension('B')->setWidth(25);  // Kategori
        $sheet->getColumnDimension('C')->setWidth(40);  // Nama Item
        $sheet->getColumnDimension('D')->setWidth(18);  // Nominal
        $sheet->getColumnDimension('E')->setWidth(30);  // Catatan
        $sheet->getColumnDimension('F')->setWidth(12);  // Status
        $sheet->getColumnDimension('G')->setWidth(18);  // Tanggal Selesai

        // Header info
        $sheet->setCellValue('A1', 'BUDGET ' . strtoupper($budget->period));
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Total Budget: ' . $budget->formatted_budget);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Instructions
        $sheet->setCellValue('A3', 'Petunjuk: Edit data di kolom B-G. Kolom A (ID) jangan diubah. Untuk menambah item baru, kosongkan kolom ID.');
        $sheet->mergeCells('A3:G3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('A3')->getFont()->getColor()->setRGB('666666');

        // Column headers (row 5)
        $headers = ['ID', 'Kategori', 'Nama Item', 'Nominal', 'Catatan', 'Status (Y/N)', 'Tanggal Selesai'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '8B5CF6']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $sheet->getStyle('A5:G5')->applyFromArray($headerStyle);

        // Data rows
        $row = 6;
        $items = $budget->items()->orderBy('category')->orderBy('id')->get();

        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->category ?? '');
            $sheet->setCellValue('C' . $row, $item->item_name);
            $sheet->setCellValue('D' . $row, $item->estimated_amount);
            $sheet->setCellValue('E' . $row, $item->notes ?? '');
            $sheet->setCellValue('F' . $row, $item->is_completed ? 'Y' : 'N');
            $sheet->setCellValue('G' . $row, $item->completed_at ? $item->completed_at->format('Y-m-d H:i') : '');

            // Format nominal as number
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');

            // Color row based on status
            if ($item->is_completed) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D1FAE5');
            }

            $row++;
        }

        // Add empty rows for new items
        for ($i = 0; $i < 5; $i++) {
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('F' . $row, 'N');
            $row++;
        }

        // Border for data area
        $lastRow = $row - 1;
        $sheet->getStyle('A5:G' . $lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Hidden sheet for budget info
        $infoSheet = $spreadsheet->createSheet();
        $infoSheet->setTitle('_info');
        $infoSheet->setCellValue('A1', 'budget_id');
        $infoSheet->setCellValue('B1', $budget->id);
        $infoSheet->setCellValue('A2', 'period');
        $infoSheet->setCellValue('B2', $budget->period);
        $infoSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        $spreadsheet->setActiveSheetIndex(0);

        // Save to temp file
        $filename = 'budget_' . $budget->year . '_' . str_pad($budget->month, 2, '0', STR_PAD_LEFT) . '_' . date('YmdHis') . '.xlsx';
        $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
        $filepath = $tempDir . DIRECTORY_SEPARATOR . $filename;

        // Create temp directory if not exists
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        return $filepath;
    }

    /**
     * Import budget items from Excel
     */
    public function import(Budget $budget, string $filepath): array
    {
        $spreadsheet = IOFactory::load($filepath);

        // Verify budget ID from info sheet
        $infoSheet = null;
        try {
            $infoSheet = $spreadsheet->getSheetByName('_info');
        } catch (\Exception $e) {
            // Info sheet not found, continue anyway
        }

        if ($infoSheet) {
            $fileBudgetId = $infoSheet->getCell('B1')->getValue();
            if ($fileBudgetId && $fileBudgetId != $budget->id) {
                return [
                    'success' => false,
                    'message' => 'File Excel ini bukan untuk budget ' . $budget->period . '. File ini untuk budget ID ' . $fileBudgetId,
                ];
            }
        }

        $sheet = $spreadsheet->getSheetByName('Budget Items') ?? $spreadsheet->getActiveSheet();

        $results = [
            'success' => true,
            'updated' => 0,
            'created' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();
        try {
            // Find data start row (after header)
            $startRow = 6; // Data starts at row 6

            $highestRow = $sheet->getHighestRow();

            for ($row = $startRow; $row <= $highestRow; $row++) {
                $id = $sheet->getCell('A' . $row)->getValue();
                $category = trim($sheet->getCell('B' . $row)->getValue() ?? '');
                $itemName = trim($sheet->getCell('C' . $row)->getValue() ?? '');
                $amount = $sheet->getCell('D' . $row)->getValue();
                $notes = trim($sheet->getCell('E' . $row)->getValue() ?? '');
                $status = strtoupper(trim($sheet->getCell('F' . $row)->getValue() ?? 'N'));
                $completedAt = $sheet->getCell('G' . $row)->getValue();

                // Skip empty rows
                if (empty($itemName)) {
                    continue;
                }

                // Validate amount
                $amount = is_numeric($amount) ? floatval($amount) : 0;
                if ($amount < 0) {
                    $results['errors'][] = "Baris {$row}: Nominal tidak boleh negatif";
                    continue;
                }

                // Parse status
                $isCompleted = in_array($status, ['Y', 'YES', '1', 'TRUE', 'SELESAI']);

                // Parse completed_at
                $completedAtDate = null;
                if ($isCompleted) {
                    if ($completedAt) {
                        try {
                            if (is_numeric($completedAt)) {
                                // Excel date format
                                $completedAtDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($completedAt);
                            } else {
                                $completedAtDate = new \DateTime($completedAt);
                            }
                        } catch (\Exception $e) {
                            $completedAtDate = now();
                        }
                    } else {
                        $completedAtDate = now();
                    }
                }

                // Prepare data
                $data = [
                    'category' => $category ?: null,
                    'item_name' => $itemName,
                    'estimated_amount' => $amount,
                    'notes' => $notes ?: null,
                    'is_completed' => $isCompleted,
                    'completed_at' => $completedAtDate,
                ];

                if ($id && is_numeric($id)) {
                    // Update existing item
                    $item = BudgetItem::where('id', $id)
                        ->where('budget_id', $budget->id)
                        ->first();

                    if ($item) {
                        $item->update($data);
                        $results['updated']++;
                    } else {
                        $results['errors'][] = "Baris {$row}: Item dengan ID {$id} tidak ditemukan";
                    }
                } else {
                    // Create new item
                    $data['budget_id'] = $budget->id;
                    BudgetItem::create($data);
                    $results['created']++;
                }
            }

            // Update total budget
            $budget->updateTotalBudget();

            DB::commit();

            $results['message'] = "Import berhasil! {$results['updated']} item diupdate, {$results['created']} item baru ditambahkan.";

        } catch (\Exception $e) {
            DB::rollBack();
            $results['success'] = false;
            $results['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Export all budgets to Excel
     */
    public function exportAll(): string
    {
        $spreadsheet = new Spreadsheet();

        // Get all budgets ordered by year and month descending
        $budgets = Budget::with(['items' => function($query) {
            $query->orderBy('category')->orderBy('id');
        }])->orderByDesc('year')->orderByDesc('month')->get();

        $sheetIndex = 0;
        foreach ($budgets as $budget) {
            if ($sheetIndex === 0) {
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet();
            }

            // Set sheet name (max 31 chars)
            $sheetName = $budget->year . '-' . str_pad($budget->month, 2, '0', STR_PAD_LEFT);
            $sheet->setTitle($sheetName);

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(10);  // ID
            $sheet->getColumnDimension('B')->setWidth(12);  // Budget ID
            $sheet->getColumnDimension('C')->setWidth(25);  // Kategori
            $sheet->getColumnDimension('D')->setWidth(40);  // Nama Item
            $sheet->getColumnDimension('E')->setWidth(18);  // Nominal
            $sheet->getColumnDimension('F')->setWidth(30);  // Catatan
            $sheet->getColumnDimension('G')->setWidth(12);  // Status
            $sheet->getColumnDimension('H')->setWidth(18);  // Tanggal Selesai

            // Header info
            $sheet->setCellValue('A1', 'BUDGET ' . strtoupper($budget->period));
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Total Budget: ' . $budget->formatted_budget . ' | Progress: ' . $budget->progress_percentage . '%');
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Instructions
            $sheet->setCellValue('A3', 'Petunjuk: Edit data di kolom C-H. Kolom A (ID) dan B (Budget ID) jangan diubah. Untuk menambah item baru, kosongkan kolom A dan isi kolom B dengan ' . $budget->id);
            $sheet->mergeCells('A3:H3');
            $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);
            $sheet->getStyle('A3')->getFont()->getColor()->setRGB('666666');

            // Column headers (row 5)
            $headers = ['ID', 'Budget ID', 'Kategori', 'Nama Item', 'Nominal', 'Catatan', 'Status (Y/N)', 'Tanggal Selesai'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '5', $header);
                $col++;
            }

            // Style header row
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8B5CF6']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ];
            $sheet->getStyle('A5:H5')->applyFromArray($headerStyle);

            // Data rows
            $row = 6;
            foreach ($budget->items as $item) {
                $sheet->setCellValue('A' . $row, $item->id);
                $sheet->setCellValue('B' . $row, $budget->id);
                $sheet->setCellValue('C' . $row, $item->category ?? '');
                $sheet->setCellValue('D' . $row, $item->item_name);
                $sheet->setCellValue('E' . $row, $item->estimated_amount);
                $sheet->setCellValue('F' . $row, $item->notes ?? '');
                $sheet->setCellValue('G' . $row, $item->is_completed ? 'Y' : 'N');
                $sheet->setCellValue('H' . $row, $item->completed_at ? $item->completed_at->format('Y-m-d H:i') : '');

                // Format nominal as number
                $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');

                // Color row based on status
                if ($item->is_completed) {
                    $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('D1FAE5');
                }

                $row++;
            }

            // Add empty rows for new items
            for ($i = 0; $i < 3; $i++) {
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('B' . $row, $budget->id);
                $sheet->setCellValue('G' . $row, 'N');
                $row++;
            }

            // Border for data area
            $lastRow = $row - 1;
            if ($lastRow >= 5) {
                $sheet->getStyle('A5:H' . $lastRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }

            $sheetIndex++;
        }

        // Create summary sheet at the beginning
        $summarySheet = $spreadsheet->createSheet(0);
        $summarySheet->setTitle('RINGKASAN');

        $summarySheet->getColumnDimension('A')->setWidth(20);
        $summarySheet->getColumnDimension('B')->setWidth(18);
        $summarySheet->getColumnDimension('C')->setWidth(15);
        $summarySheet->getColumnDimension('D')->setWidth(15);
        $summarySheet->getColumnDimension('E')->setWidth(12);

        $summarySheet->setCellValue('A1', 'RINGKASAN SEMUA BUDGET');
        $summarySheet->mergeCells('A1:E1');
        $summarySheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $summarySheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $summarySheet->setCellValue('A3', 'Periode');
        $summarySheet->setCellValue('B3', 'Total Budget');
        $summarySheet->setCellValue('C3', 'Total Item');
        $summarySheet->setCellValue('D3', 'Selesai');
        $summarySheet->setCellValue('E3', 'Progress');

        $summarySheet->getStyle('A3:E3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8B5CF6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sRow = 4;
        $grandTotal = 0;
        foreach ($budgets as $budget) {
            $summarySheet->setCellValue('A' . $sRow, $budget->period);
            $summarySheet->setCellValue('B' . $sRow, $budget->total_budget);
            $summarySheet->setCellValue('C' . $sRow, $budget->total_items_count);
            $summarySheet->setCellValue('D' . $sRow, $budget->completed_items_count);
            $summarySheet->setCellValue('E' . $sRow, $budget->progress_percentage . '%');

            $summarySheet->getStyle('B' . $sRow)->getNumberFormat()->setFormatCode('#,##0');

            $grandTotal += $budget->total_budget;
            $sRow++;
        }

        // Grand total row
        $summarySheet->setCellValue('A' . $sRow, 'TOTAL');
        $summarySheet->setCellValue('B' . $sRow, $grandTotal);
        $summarySheet->getStyle('A' . $sRow . ':E' . $sRow)->getFont()->setBold(true);
        $summarySheet->getStyle('B' . $sRow)->getNumberFormat()->setFormatCode('#,##0');

        $summarySheet->getStyle('A3:E' . $sRow)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $spreadsheet->setActiveSheetIndex(0);

        // Save to temp file
        $filename = 'all_budgets_' . date('YmdHis') . '.xlsx';
        $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
        $filepath = $tempDir . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        return $filepath;
    }

    /**
     * Import all budgets from Excel (multi-sheet)
     * - Jika Budget ID ada dan valid: update budget yang ada
     * - Jika Budget ID kosong: buat budget baru berdasarkan nama sheet (format: YYYY-MM)
     */
    public function importAll(string $filepath): array
    {
        $spreadsheet = IOFactory::load($filepath);
        $sheetCount = $spreadsheet->getSheetCount();

        $results = [
            'success' => true,
            'updated' => 0,
            'created' => 0,
            'budgets_created' => 0,
            'budgets_processed' => 0,
            'errors' => [],
        ];

        // Cache untuk budget yang sudah dibuat dalam import ini
        $createdBudgets = [];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $sheetCount; $i++) {
                $sheet = $spreadsheet->getSheet($i);
                $sheetName = $sheet->getTitle();

                // Skip summary sheet
                if ($sheetName === 'RINGKASAN' || $sheetName === '_info') {
                    continue;
                }

                $startRow = 6;
                $highestRow = $sheet->getHighestRow();

                $budgetProcessed = false;
                $sheetBudget = null; // Budget untuk sheet ini (jika perlu buat baru)

                for ($row = $startRow; $row <= $highestRow; $row++) {
                    $id = $sheet->getCell('A' . $row)->getValue();
                    $budgetId = $sheet->getCell('B' . $row)->getValue();
                    $category = trim($sheet->getCell('C' . $row)->getValue() ?? '');
                    $itemName = trim($sheet->getCell('D' . $row)->getValue() ?? '');
                    $amount = $sheet->getCell('E' . $row)->getValue();
                    $notes = trim($sheet->getCell('F' . $row)->getValue() ?? '');
                    $status = strtoupper(trim($sheet->getCell('G' . $row)->getValue() ?? 'N'));
                    $completedAt = $sheet->getCell('H' . $row)->getValue();

                    // Skip empty rows
                    if (empty($itemName)) {
                        continue;
                    }

                    $budget = null;

                    // Jika Budget ID ada dan valid, gunakan budget yang ada
                    if ($budgetId && is_numeric($budgetId)) {
                        $budget = Budget::find($budgetId);
                        if (!$budget) {
                            $results['errors'][] = "Sheet {$sheetName} Baris {$row}: Budget dengan ID {$budgetId} tidak ditemukan";
                            continue;
                        }
                    } else {
                        // Budget ID kosong - buat budget baru berdasarkan nama sheet
                        // Parse nama sheet (format: YYYY-MM atau nama bulan)
                        $parsedDate = $this->parseSheetNameToDate($sheetName);

                        if (!$parsedDate) {
                            $results['errors'][] = "Sheet {$sheetName} Baris {$row}: Tidak dapat menentukan periode budget dari nama sheet. Gunakan format YYYY-MM (contoh: 2026-02)";
                            continue;
                        }

                        $year = $parsedDate['year'];
                        $month = $parsedDate['month'];

                        // Cek apakah budget sudah ada untuk periode ini
                        $cacheKey = "{$year}-{$month}";

                        if (isset($createdBudgets[$cacheKey])) {
                            // Gunakan budget yang sudah dibuat dalam import ini
                            $budget = $createdBudgets[$cacheKey];
                        } else {
                            // Cek di database
                            $budget = Budget::where('year', $year)->where('month', $month)->first();

                            if (!$budget) {
                                // Buat budget baru
                                $budget = Budget::create([
                                    'year' => $year,
                                    'month' => $month,
                                    'total_budget' => 0,
                                    'notes' => 'Dibuat dari import Excel',
                                ]);
                                $results['budgets_created']++;
                            }

                            $createdBudgets[$cacheKey] = $budget;
                        }
                    }

                    // Validate amount
                    $amount = is_numeric($amount) ? floatval($amount) : 0;
                    if ($amount < 0) {
                        $results['errors'][] = "Sheet {$sheetName} Baris {$row}: Nominal tidak boleh negatif";
                        continue;
                    }

                    // Parse status
                    $isCompleted = in_array($status, ['Y', 'YES', '1', 'TRUE', 'SELESAI']);

                    // Parse completed_at
                    $completedAtDate = null;
                    if ($isCompleted) {
                        if ($completedAt) {
                            try {
                                if (is_numeric($completedAt)) {
                                    $completedAtDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($completedAt);
                                } else {
                                    $completedAtDate = new \DateTime($completedAt);
                                }
                            } catch (\Exception $e) {
                                $completedAtDate = now();
                            }
                        } else {
                            $completedAtDate = now();
                        }
                    }

                    // Prepare data
                    $data = [
                        'category' => $category ?: null,
                        'item_name' => $itemName,
                        'estimated_amount' => $amount,
                        'notes' => $notes ?: null,
                        'is_completed' => $isCompleted,
                        'completed_at' => $completedAtDate,
                    ];

                    if ($id && is_numeric($id)) {
                        // Update existing item
                        $item = BudgetItem::where('id', $id)
                            ->where('budget_id', $budget->id)
                            ->first();

                        if ($item) {
                            $item->update($data);
                            $results['updated']++;
                            $budgetProcessed = true;
                        } else {
                            $results['errors'][] = "Sheet {$sheetName} Baris {$row}: Item dengan ID {$id} tidak ditemukan";
                        }
                    } else {
                        // Create new item
                        $data['budget_id'] = $budget->id;
                        BudgetItem::create($data);
                        $results['created']++;
                        $budgetProcessed = true;
                    }

                    // Update budget total
                    $budget->updateTotalBudget();
                }

                if ($budgetProcessed) {
                    $results['budgets_processed']++;
                }
            }

            DB::commit();

            $message = "Import berhasil! ";
            if ($results['budgets_created'] > 0) {
                $message .= "{$results['budgets_created']} budget baru dibuat, ";
            }
            $message .= "{$results['budgets_processed']} budget diproses, {$results['updated']} item diupdate, {$results['created']} item baru ditambahkan.";
            $results['message'] = $message;

        } catch (\Exception $e) {
            DB::rollBack();
            $results['success'] = false;
            $results['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Parse sheet name to year and month
     * Supports formats: YYYY-MM, YYYY-M, MM-YYYY, Januari 2026, dll
     */
    private function parseSheetNameToDate(string $sheetName): ?array
    {
        $sheetName = trim($sheetName);

        // Format: YYYY-MM atau YYYY-M (contoh: 2026-02 atau 2026-2)
        if (preg_match('/^(\d{4})-(\d{1,2})$/', $sheetName, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
            if ($month >= 1 && $month <= 12 && $year >= 2000 && $year <= 2100) {
                return ['year' => $year, 'month' => $month];
            }
        }

        // Format: MM-YYYY (contoh: 02-2026)
        if (preg_match('/^(\d{1,2})-(\d{4})$/', $sheetName, $matches)) {
            $month = (int) $matches[1];
            $year = (int) $matches[2];
            if ($month >= 1 && $month <= 12 && $year >= 2000 && $year <= 2100) {
                return ['year' => $year, 'month' => $month];
            }
        }

        // Format: NamaBulan YYYY (contoh: Januari 2026, Jan 2026)
        $monthNames = [
            'januari' => 1, 'jan' => 1,
            'februari' => 2, 'feb' => 2,
            'maret' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'jun' => 6,
            'juli' => 7, 'jul' => 7,
            'agustus' => 8, 'agu' => 8, 'aug' => 8,
            'september' => 9, 'sep' => 9,
            'oktober' => 10, 'okt' => 10, 'oct' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'des' => 12, 'dec' => 12,
        ];

        $lowerName = strtolower($sheetName);
        foreach ($monthNames as $name => $monthNum) {
            if (preg_match('/^' . $name . '\s+(\d{4})$/i', $lowerName, $matches)) {
                $year = (int) $matches[1];
                if ($year >= 2000 && $year <= 2100) {
                    return ['year' => $year, 'month' => $monthNum];
                }
            }
            // Format: YYYY NamaBulan
            if (preg_match('/^(\d{4})\s+' . $name . '$/i', $lowerName, $matches)) {
                $year = (int) $matches[1];
                if ($year >= 2000 && $year <= 2100) {
                    return ['year' => $year, 'month' => $monthNum];
                }
            }
        }

        return null;
    }
}
