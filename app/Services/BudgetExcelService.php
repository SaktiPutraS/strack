<?php

namespace App\Services;

use App\Models\BudgetItem;
use App\Support\BudgetPeriod;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

/**
 * BudgetExcelService
 *
 * Format kolom (9 kolom, 1 sheet):
 *   A = ID         (kosong = item baru)
 *   B = Tahun      (4 digit)
 *   C = Bulan      (1–12)
 *   D = Kategori   (string, opsional)
 *   E = Nama Item  (string, wajib)
 *   F = Nominal    (numerik)
 *   G = Catatan    (string, opsional)
 *   H = Status     (Y / N)
 *   I = Tgl Selesai (YYYY-MM-DD HH:MM, opsional)
 *
 * Row layout:
 *   1 = Judul (merged A1:I1)
 *   2 = Keterangan / progress (merged A2:I2)
 *   3 = Petunjuk (merged A3:I3)
 *   5 = Header kolom
 *   6+ = Data
 */
class BudgetExcelService
{
    private const DATA_START_ROW = 6;
    private const COLS = ['A','B','C','D','E','F','G','H','I'];

    // ─── EXPORT SINGLE ────────────────────────────────────────────────────────

    /**
     * Export satu periode budget ke Excel (single sheet, semua kolom termasuk Tahun & Bulan).
     */
    public function export(BudgetPeriod $budget): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Budget');

        $this->applyColumnWidths($sheet);

        // ── Row 1: Judul ──
        $sheet->setCellValue('A1', 'BUDGET ' . strtoupper($budget->period));
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Row 2: Info ──
        $pct = $budget->progress_percentage;
        $sheet->setCellValue('A2', "Total: {$budget->formatted_budget}  |  Selesai: {$budget->completed_items_count}/{$budget->total_items_count} item  |  Progress: {$pct}%");
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFont()->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('555555'));

        // ── Row 3: Petunjuk ──
        $sheet->setCellValue('A3', 'Petunjuk: Kolom A (ID) jangan diubah — kosongkan untuk item baru. Kolom B (Tahun) dan C (Bulan) wajib diisi. Status: Y = selesai, N = belum.');
        $sheet->mergeCells('A3:I3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('A3')->getFont()->getColor()->setRGB('888888');

        // ── Row 5: Header kolom ──
        $this->writeColumnHeaders($sheet);

        // ── Row 6+: Data ──
        $row   = self::DATA_START_ROW;
        $items = $budget->items->sortBy([['category', 'asc'], ['id', 'asc']]);

        foreach ($items as $item) {
            $this->writeItemRow($sheet, $row, $item, $budget->year, $budget->month);
            $row++;
        }

        // Baris kosong untuk item baru
        for ($i = 0; $i < 5; $i++) {
            $sheet->setCellValue('B' . $row, $budget->year);
            $sheet->setCellValue('C' . $row, $budget->month);
            $sheet->setCellValue('H' . $row, 'N');
            $row++;
        }

        $this->applyDataBorders($sheet, $row - 1);

        return $this->saveTemp($spreadsheet, 'budget_' . $budget->year . '_' . str_pad($budget->month, 2, '0', STR_PAD_LEFT));
    }

    // ─── EXPORT ALL ───────────────────────────────────────────────────────────

    /**
     * Export semua periode ke 1 sheet Excel (format identik, dengan Tahun & Bulan per baris).
     */
    public function exportAll(): string
    {
        $allItems = BudgetItem::orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('category')
            ->orderBy('id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Semua Budget');

        $this->applyColumnWidths($sheet);

        // ── Row 1: Judul ──
        $sheet->setCellValue('A1', 'SEMUA BUDGET — Export: ' . date('d/m/Y'));
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Row 2: Info ──
        $totalItems = $allItems->count();
        $doneItems  = $allItems->where('is_completed', true)->count();
        $periods    = $allItems->groupBy(fn($i) => $i->year . '-' . $i->month)->count();
        $sheet->setCellValue('A2', "Total {$periods} periode  |  {$totalItems} item  |  {$doneItems} item selesai");
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Row 3: Petunjuk ──
        $sheet->setCellValue('A3', 'Petunjuk: Kolom A (ID) jangan diubah — kosongkan untuk item baru. Kolom B (Tahun) dan C (Bulan) wajib diisi. Status: Y = selesai, N = belum.');
        $sheet->mergeCells('A3:I3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('A3')->getFont()->getColor()->setRGB('888888');

        // ── Row 5: Header kolom ──
        $this->writeColumnHeaders($sheet);

        // ── Row 6+: Data — diberi warna zebra per periode ──
        $row         = self::DATA_START_ROW;
        $prevPeriod  = null;
        $colorToggle = false;

        foreach ($allItems as $item) {
            $currentPeriod = $item->year . '-' . $item->month;
            if ($currentPeriod !== $prevPeriod) {
                $colorToggle = !$colorToggle;
                $prevPeriod  = $currentPeriod;
            }

            $this->writeItemRow($sheet, $row, $item, $item->year, $item->month, $colorToggle);
            $row++;
        }

        // 3 baris kosong di akhir untuk item baru
        for ($i = 0; $i < 3; $i++) {
            $sheet->setCellValue('H' . $row, 'N');
            $row++;
        }

        $this->applyDataBorders($sheet, $row - 1);

        return $this->saveTemp($spreadsheet, 'all_budgets');
    }

    // ─── IMPORT SINGLE ────────────────────────────────────────────────────────

    /**
     * Import item dari Excel ke periode tertentu.
     * Baris dengan Tahun/Bulan berbeda dari yang diharapkan akan dilewati.
     */
    public function import(int $month, int $year, string $filepath): array
    {
        $spreadsheet = IOFactory::load($filepath);
        $sheet       = $spreadsheet->getActiveSheet();

        $results = [
            'success' => true,
            'updated' => 0,
            'created' => 0,
            'skipped' => 0,
            'errors'  => [],
        ];

        DB::beginTransaction();
        try {
            $highestRow = $sheet->getHighestRow();

            for ($row = self::DATA_START_ROW; $row <= $highestRow; $row++) {
                $id        = $sheet->getCell('A' . $row)->getValue();
                $fileYear  = (int) $sheet->getCell('B' . $row)->getValue();
                $fileMonth = (int) $sheet->getCell('C' . $row)->getValue();
                $itemName  = trim($sheet->getCell('E' . $row)->getValue() ?? '');

                // Skip baris kosong
                if (empty($itemName)) {
                    continue;
                }

                // Verifikasi periode — jika baris punya Tahun/Bulan berbeda, lewati
                if ($fileYear > 0 && $fileMonth > 0 && ($fileYear !== $year || $fileMonth !== $month)) {
                    $results['skipped']++;
                    $results['errors'][] = "Baris {$row} dilewati: periode {$fileYear}-{$fileMonth} berbeda dari yang diharapkan ({$year}-{$month})";
                    continue;
                }

                // Jika Tahun/Bulan kosong, gunakan periode yang diberikan
                $useYear  = ($fileYear > 0)  ? $fileYear  : $year;
                $useMonth = ($fileMonth > 0) ? $fileMonth : $month;

                $result = $this->processRow($sheet, $row, $useMonth, $useYear, $id);

                if ($result === 'updated')      $results['updated']++;
                elseif ($result === 'created')  $results['created']++;
                elseif (is_string($result))     $results['errors'][] = "Baris {$row}: {$result}";
            }

            DB::commit();

            $results['message'] = "Import berhasil! {$results['updated']} diupdate, {$results['created']} baru ditambahkan"
                . ($results['skipped'] > 0 ? ", {$results['skipped']} dilewati." : '.');

        } catch (\Exception $e) {
            DB::rollBack();
            $results['success'] = false;
            $results['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $results;
    }

    // ─── IMPORT ALL ───────────────────────────────────────────────────────────

    /**
     * Import semua budget dari 1 sheet Excel.
     * Setiap baris harus memiliki Tahun (kolom B) dan Bulan (kolom C).
     */
    public function importAll(string $filepath): array
    {
        $spreadsheet = IOFactory::load($filepath);
        $sheet       = $spreadsheet->getActiveSheet();

        $results = [
            'success'           => true,
            'updated'           => 0,
            'created'           => 0,
            'skipped'           => 0,
            'budgets_processed' => 0,
            'errors'            => [],
        ];

        $processedPeriods = [];

        DB::beginTransaction();
        try {
            $highestRow = $sheet->getHighestRow();

            for ($row = self::DATA_START_ROW; $row <= $highestRow; $row++) {
                $id        = $sheet->getCell('A' . $row)->getValue();
                $fileYear  = (int) $sheet->getCell('B' . $row)->getValue();
                $fileMonth = (int) $sheet->getCell('C' . $row)->getValue();
                $itemName  = trim($sheet->getCell('E' . $row)->getValue() ?? '');

                // Skip baris kosong
                if (empty($itemName)) {
                    continue;
                }

                // Tahun dan Bulan wajib ada
                if ($fileYear < 2000 || $fileYear > 2100 || $fileMonth < 1 || $fileMonth > 12) {
                    $results['skipped']++;
                    $results['errors'][] = "Baris {$row} dilewati: Tahun ({$fileYear}) atau Bulan ({$fileMonth}) tidak valid";
                    continue;
                }

                $result = $this->processRow($sheet, $row, $fileMonth, $fileYear, $id);

                if ($result === 'updated') {
                    $results['updated']++;
                } elseif ($result === 'created') {
                    $results['created']++;
                    $key = "{$fileYear}-{$fileMonth}";
                    if (!isset($processedPeriods[$key])) {
                        $processedPeriods[$key] = true;
                        $results['budgets_processed']++;
                    }
                } elseif (is_string($result)) {
                    $results['errors'][] = "Baris {$row}: {$result}";
                }
            }

            DB::commit();

            $results['message'] = "Import berhasil! {$results['updated']} item diupdate, {$results['created']} item baru"
                . ($results['budgets_processed'] > 0 ? " ({$results['budgets_processed']} periode baru)" : '')
                . ($results['skipped'] > 0 ? ", {$results['skipped']} baris dilewati." : '.');

        } catch (\Exception $e) {
            DB::rollBack();
            $results['success'] = false;
            $results['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $results;
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    /**
     * Proses satu baris data: update item lama atau buat item baru.
     * Return: 'updated' | 'created' | string (pesan error)
     */
    private function processRow($sheet, int $row, int $month, int $year, mixed $id): string
    {
        $category    = trim($sheet->getCell('D' . $row)->getValue() ?? '');
        $itemName    = trim($sheet->getCell('E' . $row)->getValue() ?? '');
        $amount      = $sheet->getCell('F' . $row)->getValue();
        $notes       = trim($sheet->getCell('G' . $row)->getValue() ?? '');
        $status      = strtoupper(trim($sheet->getCell('H' . $row)->getValue() ?? 'N'));
        $completedAt = $sheet->getCell('I' . $row)->getValue();

        if (empty($itemName)) {
            return 'Nama item kosong';
        }

        $amount = is_numeric($amount) ? floatval($amount) : 0;
        if ($amount < 0) {
            return 'Nominal tidak boleh negatif';
        }

        $isCompleted     = in_array($status, ['Y', 'YES', '1', 'TRUE', 'SELESAI']);
        $completedAtDate = $this->parseCompletedAt($completedAt, $isCompleted);

        $data = [
            'month'            => $month,
            'year'             => $year,
            'category'         => $category ?: null,
            'item_name'        => $itemName,
            'estimated_amount' => $amount,
            'notes'            => $notes ?: null,
            'is_completed'     => $isCompleted,
            'completed_at'     => $completedAtDate,
        ];

        if ($id && is_numeric($id)) {
            $item = BudgetItem::where('id', $id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($item) {
                $item->update($data);
                return 'updated';
            }

            return "Item ID {$id} tidak ditemukan di periode {$year}-{$month}";
        }

        BudgetItem::create($data);
        return 'created';
    }

    /**
     * Set lebar kolom A–I.
     */
    private function applyColumnWidths($sheet): void
    {
        $widths = [
            'A' => 8,   // ID
            'B' => 8,   // Tahun
            'C' => 7,   // Bulan
            'D' => 24,  // Kategori
            'E' => 38,  // Nama Item
            'F' => 16,  // Nominal
            'G' => 28,  // Catatan
            'H' => 12,  // Status
            'I' => 18,  // Tgl Selesai
        ];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
    }

    /**
     * Tulis baris header kolom (baris 5).
     */
    private function writeColumnHeaders($sheet): void
    {
        $headers = ['ID', 'Tahun', 'Bulan', 'Kategori', 'Nama Item', 'Nominal', 'Catatan', 'Status (Y/N)', 'Tanggal Selesai'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '5', $h);
            $col++;
        }
        $sheet->getStyle('A5:I5')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8B5CF6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    /**
     * Tulis satu baris data item.
     * $zebra: jika true, beri warna latar belakang abu-abu muda (untuk exportAll).
     */
    private function writeItemRow($sheet, int $row, BudgetItem $item, int $year, int $month, bool $zebra = false): void
    {
        $sheet->setCellValue('A' . $row, $item->id);
        $sheet->setCellValue('B' . $row, $year);
        $sheet->setCellValue('C' . $row, $month);
        $sheet->setCellValue('D' . $row, $item->category ?? '');
        $sheet->setCellValue('E' . $row, $item->item_name);
        $sheet->setCellValue('F' . $row, $item->estimated_amount);
        $sheet->setCellValue('G' . $row, $item->notes ?? '');
        $sheet->setCellValue('H' . $row, $item->is_completed ? 'Y' : 'N');
        $sheet->setCellValue('I' . $row, $item->completed_at ? $item->completed_at->format('Y-m-d H:i') : '');

        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($item->is_completed) {
            $sheet->getStyle('A' . $row . ':I' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D1FAE5');
        } elseif ($zebra) {
            $sheet->getStyle('A' . $row . ':I' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F5F3FF');
        }
    }

    /**
     * Terapkan border tipis untuk area data.
     */
    private function applyDataBorders($sheet, int $lastRow): void
    {
        if ($lastRow >= 5) {
            $sheet->getStyle('A5:I' . $lastRow)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }
    }

    /**
     * Simpan Spreadsheet ke file temp dan return filepath-nya.
     */
    private function saveTemp(Spreadsheet $spreadsheet, string $prefix): string
    {
        $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filename = $prefix . '_' . date('YmdHis') . '.xlsx';
        $filepath = $tempDir . DIRECTORY_SEPARATOR . $filename;

        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        return $filepath;
    }

    /**
     * Parse nilai completed_at dari cell Excel.
     */
    private function parseCompletedAt(mixed $value, bool $isCompleted): ?\DateTime
    {
        if (!$isCompleted) return null;

        if ($value) {
            try {
                if (is_numeric($value)) {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                }
                return new \DateTime($value);
            } catch (\Exception $e) {
                return now()->toDateTime();
            }
        }

        return now()->toDateTime();
    }
}
