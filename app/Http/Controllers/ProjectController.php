<?php
// app/Http/Controllers/ProjectController.php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectController extends Controller
{

    public function index(Request $request): View
    {
        $agent = new Agent();
        $isMobile = $agent->isMobile();
        $query = Project::with('client');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('testimoni')) {
            if ($request->testimoni === 'true') {
                $query->withTestimoni();
            } elseif ($request->testimoni === 'false') {
                $query->withoutTestimoni();
            }
        }

        if ($request->filled('piutang') && $request->piutang == 'true') {
            $query->whereRaw('total_value > paid_amount')
                ->whereIn('status', ['WAITING', 'PROGRESS']);
        }

        if ($request->filled('month') && $request->month == 'current') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = in_array(strtolower($request->get('order', 'desc')), ['asc', 'desc'])
            ? strtolower($request->get('order', 'desc'))
            : 'desc';

        switch ($sortBy) {
            case 'client_id':
                $query->join('clients', 'projects.client_id', '=', 'clients.id')
                    ->orderBy('clients.name', $sortOrder)
                    ->select('projects.*');
                break;

            case 'title':
                $query->orderBy('title', $sortOrder);
                break;

            case 'deadline':
                $query->orderBy('deadline', $sortOrder);
                break;

            case 'status':
                $query->orderBy('status', $sortOrder);
                break;

            case 'testimoni':
                $query->orderBy('testimoni', $sortOrder);
                break;

            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }

        $projects = $query->paginate(5)->withQueryString();

        $projectStats = [
            'waiting' => Project::where('status', 'WAITING')->count(),
            'progress' => Project::where('status', 'PROGRESS')->count(),
            'finished' => Project::where('status', 'FINISHED')->count(),
            'cancelled' => Project::where('status', 'CANCELLED')->count(),
        ];

        $testimoniStats = [
            'with_testimoni' => Project::withTestimoni()->count(),
            'without_testimoni' => Project::withoutTestimoni()->count(),
            'finished_with_testimoni' => Project::finished()->withTestimoni()->count(),
            'finished_without_testimoni' => Project::finished()->withoutTestimoni()->count(),
        ];

        $totalPiutang = Project::whereIn('status', ['WAITING', 'PROGRESS'])
            ->sum(DB::raw('total_value - paid_amount'));

        $totalNilaiBulanIni = Project::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_value');

        $clients = Client::orderBy('name')->get();
        $projectTypes = Project::distinct()->pluck('type')->filter();
        $statuses = ['WAITING', 'PROGRESS', 'FINISHED', 'CANCELLED'];

        $formatCurrency = function ($amount) {
            if ($amount >= 1000000000) {
                return 'Rp ' . number_format($amount / 1000000000, 1, ',', '.') . 'M';
            } elseif ($amount >= 1000000) {
                return 'Rp ' . number_format($amount / 1000000, 1, ',', '.') . 'Jt';
            }
            return 'Rp ' . number_format($amount, 0, ',', '.');
        };

        return view('projects.index', compact(
            'projects',
            'projectStats',
            'testimoniStats',
            'totalPiutang',
            'totalNilaiBulanIni',
            'clients',
            'projectTypes',
            'statuses',
            'formatCurrency',
            'isMobile'
        ));
    }

    /**
     * Export projects to Excel
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        // Get filtered data based on request parameters
        $query = Project::with('client');

        // Apply same filters as index method
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('testimoni')) {
            if ($request->testimoni === 'true') {
                $query->withTestimoni();
            } elseif ($request->testimoni === 'false') {
                $query->withoutTestimoni();
            }
        }

        if ($request->filled('piutang') && $request->piutang == 'true') {
            $query->whereRaw('total_value > paid_amount')
                ->whereIn('status', ['WAITING', 'PROGRESS']);
        }

        if ($request->filled('month') && $request->month == 'current') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = in_array(strtolower($request->get('order', 'desc')), ['asc', 'desc'])
            ? strtolower($request->get('order', 'desc'))
            : 'desc';

        switch ($sortBy) {
            case 'client_id':
                $query->join('clients', 'projects.client_id', '=', 'clients.id')
                    ->orderBy('clients.name', $sortOrder)
                    ->select('projects.*');
                break;
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            case 'deadline':
                $query->orderBy('deadline', $sortOrder);
                break;
            case 'status':
                $query->orderBy('status', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }

        $projects = $query->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Proyek');

        // Set headers
        $headers = [
            'A1' => 'No',
            'B1' => 'Nama Proyek',
            'C1' => 'Klien',
            'D1' => 'Tipe',
            'E1' => 'Status',
            'F1' => 'Total Nilai',
            'G1' => 'DP',
            'H1' => 'Terbayar',
            'I1' => 'Sisa',
            'J1' => 'Progress (%)',
            'K1' => 'Deadline',
            'L1' => 'Testimoni',
            'M1' => 'Tanggal Dibuat',
            'N1' => 'Deskripsi',
            'O1' => 'Catatan'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '8B5CF6']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];

        $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);

        // Auto-size columns
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add data
        $row = 2;
        foreach ($projects as $index => $project) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $project->title);
            $sheet->setCellValue('C' . $row, $project->client->name);
            $sheet->setCellValue('D' . $row, $project->type);
            $sheet->setCellValue('E' . $row, $project->status);
            $sheet->setCellValue('F' . $row, $project->total_value);
            $sheet->setCellValue('G' . $row, $project->dp_amount);
            $sheet->setCellValue('H' . $row, $project->paid_amount);
            $sheet->setCellValue('I' . $row, $project->remaining_amount);
            $sheet->setCellValue('J' . $row, $project->progress_percentage);
            $sheet->setCellValue('K' . $row, $project->deadline->format('d/m/Y'));
            $sheet->setCellValue('L' . $row, $project->testimoni ? 'Sudah' : 'Belum');
            $sheet->setCellValue('M' . $row, $project->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('N' . $row, $project->description);
            $sheet->setCellValue('O' . $row, $project->notes);

            // Format currency columns
            $sheet->getStyle('F' . $row . ':I' . $row)->getNumberFormat()
                ->setFormatCode('#,##0');

            // Color code status
            $statusColor = match ($project->status) {
                'WAITING' => 'FFF3CD',
                'PROGRESS' => 'CCE5FF',
                'FINISHED' => 'D4EDDA',
                'CANCELLED' => 'F8D7DA',
                default => 'FFFFFF'
            };

            $sheet->getStyle('E' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($statusColor);

            // Color code deadline
            if ($project->is_overdue) {
                $sheet->getStyle('K' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8D7DA');
            } elseif ($project->is_deadline_near) {
                $sheet->getStyle('K' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFF3CD');
            }

            // Apply borders to data rows
            $sheet->getStyle('A' . $row . ':O' . $row)->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $row++;
        }

        // Add summary at the bottom
        $summaryRow = $row + 2;
        $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN:');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Proyek:');
        $sheet->setCellValue('B' . $summaryRow, $projects->count());

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Menunggu:');
        $sheet->setCellValue('B' . $summaryRow, $projects->where('status', 'WAITING')->count());

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Progress:');
        $sheet->setCellValue('B' . $summaryRow, $projects->where('status', 'PROGRESS')->count());

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Selesai:');
        $sheet->setCellValue('B' . $summaryRow, $projects->where('status', 'FINISHED')->count());

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Dibatalkan:');
        $sheet->setCellValue('B' . $summaryRow, $projects->where('status', 'CANCELLED')->count());

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Nilai:');
        $sheet->setCellValue('B' . $summaryRow, $projects->sum('total_value'));
        $sheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Terbayar:');
        $sheet->setCellValue('B' . $summaryRow, $projects->sum('paid_amount'));
        $sheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Piutang:');
        $sheet->setCellValue('B' . $summaryRow, $projects->sum('remaining_amount'));
        $sheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');

        // Generate filename with current date and filters
        $filename = 'Data_Proyek_' . date('Y-m-d');
        if ($request->filled('status')) {
            $filename .= '_Status_' . $request->status;
        }
        if ($request->filled('search')) {
            $filename .= '_Search_' . substr($request->search, 0, 20);
        }
        $filename .= '.xlsx';

        // Return as download
        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function create(): View
    {
        $clients = Client::orderBy('name')->get();
        $projectTypes = ProjectType::active()->ordered()->get();

        if ($projectTypes->isEmpty()) {
            $defaultTypes = [
                ['name' => 'HTML/PHP', 'display_name' => 'HTML/PHP'],
                ['name' => 'LARAVEL', 'display_name' => 'Laravel Framework'],
                ['name' => 'FIGMA', 'display_name' => 'Figma'],
                ['name' => 'DELPHI', 'display_name' => 'Delphi'],
                ['name' => 'LAPORAN', 'display_name' => 'Laporan'],
                ['name' => 'HOSTING/DOMAIN', 'display_name' => 'Hosting/Domain'],
                ['name' => 'OTHER', 'display_name' => 'Other'],
            ];

            foreach ($defaultTypes as $index => $typeData) {
                ProjectType::create([
                    'name' => $typeData['name'],
                    'display_name' => $typeData['display_name'],
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                ]);
            }

            $projectTypes = ProjectType::active()->ordered()->get();
        }

        return view('projects.create', compact('clients', 'projectTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validTypes = ProjectType::active()->pluck('name')->toArray();

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', $validTypes),
            'total_value' => 'required|numeric|min:0',
            'dp_amount' => 'nullable|numeric|min:0',
            'deadline' => 'required|date',
            'notes' => 'nullable|string',
            'testimoni' => 'nullable|boolean',
        ]);

        $validated['dp_amount'] = $validated['dp_amount'] ?? 0;
        $validated['paid_amount'] = 0;
        $validated['status'] = 'WAITING';
        $validated['testimoni'] = $validated['testimoni'] ?? false;

        $project = Project::create($validated);

        if ($validated['dp_amount'] > 0) {
            $project->payments()->create([
                'amount' => $validated['dp_amount'],
                'payment_type' => 'DP',
                'payment_date' => now(),
                'notes' => 'Down Payment otomatis saat pembuatan proyek',
                'payment_method' => 'Transfer'
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dibuat!');
    }

    public function show(Project $project): View
    {
        $project->load(['client', 'payments']);

        $totalPayments = $project->payments()->count();
        $lastPayment = $project->payments()->latest('payment_date')->first();
        $paymentHistory = $project->payments()->orderBy('payment_date', 'desc')->get();

        return view('projects.show', compact(
            'project',
            'totalPayments',
            'lastPayment',
            'paymentHistory'
        ));
    }

    public function edit(Project $project): View
    {
        $clients = Client::orderBy('name')->get();
        $projectTypes = ProjectType::active()->ordered()->get();

        return view('projects.edit', compact('project', 'clients', 'projectTypes'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validTypes = ProjectType::active()->pluck('name')->toArray();

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', $validTypes),
            'total_value' => 'required|numeric|min:0',
            'deadline' => 'required|date',
            'status' => 'required|in:WAITING,PROGRESS,FINISHED,CANCELLED',
            'notes' => 'nullable|string',
            'testimoni' => 'nullable|boolean',
        ]);

        $validated['testimoni'] = $validated['testimoni'] ?? false;

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyek berhasil diperbarui!');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->payments()->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'Tidak dapat menghapus proyek yang sudah memiliki pembayaran!');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus!');
    }

    public function updateStatus(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:WAITING,PROGRESS,FINISHED,CANCELLED'
        ]);

        $oldStatus = $project->status;
        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Status proyek berhasil diubah dari {$oldStatus} ke {$project->status}",
            'project' => $project->load('client')
        ]);
    }

    public function updateTestimoni(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'testimoni' => 'required|boolean'
        ]);

        $oldTestimoni = $project->testimoni;
        $project->update($validated);

        $statusText = $project->testimoni ? 'sudah dibuat' : 'belum dibuat';
        $oldStatusText = $oldTestimoni ? 'sudah dibuat' : 'belum dibuat';

        return response()->json([
            'success' => true,
            'message' => "Status testimoni berhasil diubah dari {$oldStatusText} ke {$statusText}",
            'project' => $project->load('client')
        ]);
    }

    public function getActiveProjects(): JsonResponse
    {
        $projects = Project::with('client')
            ->active()
            ->orderBy('deadline')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'client_name' => $project->client->name,
                    'type' => $project->type,
                    'status' => $project->status,
                    'deadline' => $project->deadline->format('Y-m-d'),
                    'deadline_formatted' => $project->deadline->format('d M Y'),
                    'days_until_deadline' => $project->days_until_deadline,
                    'is_deadline_near' => $project->is_deadline_near,
                    'is_overdue' => $project->is_overdue,
                    'total_value' => $project->total_value,
                    'paid_amount' => $project->paid_amount,
                    'remaining_amount' => $project->remaining_amount,
                    'progress_percentage' => $project->progress_percentage,
                    'formatted_total_value' => $project->formatted_total_value,
                    'formatted_remaining_amount' => $project->formatted_remaining_amount,
                    'status_color' => $project->status_color,
                    'status_icon' => $project->status_icon,
                    'testimoni' => $project->testimoni,
                    'testimoni_status_text' => $project->testimoni_status_text,
                    'testimoni_color' => $project->testimoni_color,
                    'testimoni_icon' => $project->testimoni_icon,
                ];
            });

        return response()->json($projects);
    }

    public function getUpcomingDeadlines(): JsonResponse
    {
        $projects = Project::with('client')
            ->upcomingDeadlines(30)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'client_name' => $project->client->name,
                    'deadline' => $project->deadline->format('Y-m-d'),
                    'deadline_formatted' => $project->deadline->format('d M Y'),
                    'days_until_deadline' => $project->days_until_deadline,
                    'is_deadline_near' => $project->is_deadline_near,
                    'is_overdue' => $project->is_overdue,
                    'status' => $project->status,
                    'status_color' => $project->status_color,
                    'testimoni' => $project->testimoni,
                    'testimoni_status_text' => $project->testimoni_status_text,
                ];
            });

        return response()->json($projects);
    }

    public function getProjectPayments(Project $project): JsonResponse
    {
        $payments = $project->payments()
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'formatted_amount' => $payment->formatted_amount,
                    'payment_type' => $payment->payment_type,
                    'payment_date' => $payment->payment_date->format('Y-m-d'),
                    'payment_date_formatted' => $payment->payment_date->format('d M Y'),
                    'payment_method' => $payment->payment_method,
                    'notes' => $payment->notes,
                ];
            });

        return response()->json([
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'total_value' => $project->total_value,
                'paid_amount' => $project->paid_amount,
                'remaining_amount' => $project->remaining_amount,
                'formatted_total_value' => $project->formatted_total_value,
                'formatted_paid_amount' => $project->formatted_paid_amount,
                'formatted_remaining_amount' => $project->formatted_remaining_amount,
                'progress_percentage' => $project->progress_percentage,
                'testimoni' => $project->testimoni,
                'testimoni_status_text' => $project->testimoni_status_text,
            ],
            'payments' => $payments
        ]);
    }

    /**
     * Print Invoice untuk proyek Btools
     */
    public function printInvoice(Project $project): View
    {
        // Pastikan hanya tipe Btools yang bisa print invoice
        if ($project->type !== 'BTOOLS') {
            abort(403, 'Invoice hanya tersedia untuk proyek Btools');
        }

        // Generate nomor faktur
        $invoiceNumber = $this->generateInvoiceNumber($project);

        // Format terbilang
        $terbilang = $this->numberToWords($project->total_value);

        return view('projects.invoice', compact('project', 'invoiceNumber', 'terbilang'));
    }

    /**
     * Generate nomor faktur berdasarkan tanggal dan urutan
     */
    private function generateInvoiceNumber(Project $project): string
    {
        $date = $project->deadline->format('ymd'); // Format: 250819

        // Hitung berapa banyak project Btools yang sudah dibuat di tanggal yang sama
        $count = Project::where('type', 'BTOOLS')
            ->whereDate('deadline', $project->deadline->format('Y-m-d'))
            ->where('id', '<=', $project->id)
            ->count();

        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "INV-{$date}{$sequence}";
    }

    /**
     * Convert number to words (Bahasa Indonesia)
     */
    private function numberToWords($number): string
    {
        $number = (int) $number;

        if ($number == 0) return 'nol rupiah';

        $units = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
        $teens = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];
        $tens = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];
        $thousands = ['', 'ribu', 'juta', 'miliar', 'triliun'];

        function convertGroup($num, $units, $teens, $tens)
        {
            $result = '';

            if ($num >= 100) {
                if (intval($num / 100) == 1) {
                    $result .= 'seratus ';
                } else {
                    $result .= $units[intval($num / 100)] . ' ratus ';
                }
                $num %= 100;
            }

            if ($num >= 20) {
                $result .= $tens[intval($num / 10)] . ' ';
                $num %= 10;
            } elseif ($num >= 10) {
                $result .= $teens[$num - 10] . ' ';
                return $result;
            }

            if ($num > 0) {
                if ($num == 1 && strpos($result, 'belas') === false) {
                    $result .= 'satu ';
                } else {
                    $result .= $units[$num] . ' ';
                }
            }

            return $result;
        }

        $result = '';
        $groupIndex = 0;

        while ($number > 0) {
            $group = $number % 1000;

            if ($group > 0) {
                $groupWord = convertGroup($group, $units, $teens, $tens);

                if ($groupIndex == 1 && $group == 1) {
                    $groupWord = 'seribu ';
                } else {
                    $groupWord .= $thousands[$groupIndex] . ' ';
                }

                $result = $groupWord . $result;
            }

            $number = intval($number / 1000);
            $groupIndex++;
        }

        return trim($result) . ' rupiah';
    }

    /**
     * Get project deadlines for a specific month (untuk kalender) - FIXED VERSION
     */
    public function getMonthDeadlines($year, $month): JsonResponse
    {
        try {
            // Validasi input
            if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter tahun atau bulan tidak valid'
                ], 400);
            }

            // FIXED: Pastikan filter tahun dan bulan benar-benar spesifik
            $projects = Project::with('client')
                ->whereYear('deadline', $year)
                ->whereMonth('deadline', $month)
                ->whereIn('status', ['WAITING', 'PROGRESS'])
                ->orderBy('deadline')
                ->get();

            // Debug: Log data yang ditemukan
            \Log::info("Getting deadlines for {$year}-{$month}", [
                'count' => $projects->count(),
                'projects' => $projects->pluck('title', 'deadline')->toArray()
            ]);

            // Group by day dengan validasi yang lebih ketat
            $groupedProjects = $projects->groupBy(function ($project) {
                return $project->deadline->day;
            })->map(function ($dayProjects) {
                return $dayProjects->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'title' => $project->title,
                        'client_name' => $project->client->name,
                        'type' => $project->type,
                        'status' => $project->status,
                        'deadline' => $project->deadline->format('Y-m-d'),
                        'deadline_formatted' => $project->deadline->format('d M Y'),
                        'is_overdue' => $project->is_overdue,
                        'is_deadline_near' => $project->is_deadline_near,
                        'days_until_deadline' => $project->days_until_deadline,
                        'status_color' => $project->status_color,
                        'remaining_amount' => $project->remaining_amount,
                        'formatted_remaining_amount' => $project->formatted_remaining_amount,
                        'url' => route('projects.show', $project)
                    ];
                });
            });

            return response()->json([
                'success' => true,
                'deadlines' => $groupedProjects,
                'debug' => [
                    'requested_year' => $year,
                    'requested_month' => $month,
                    'total_projects' => $projects->count()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getMonthDeadlines', [
                'year' => $year,
                'month' => $month,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data deadline proyek: ' . $e->getMessage()
            ], 500);
        }
    }
}
