<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = Task::with('assignments')->orderBy('created_at', 'desc')->get();

        // Hitung jumlah tugas yang perlu validasi
        $needValidationCount = TaskAssignment::needValidation()->count();

        return view('tasks.index', compact('tasks', 'needValidationCount'));
    }

    public function create(): View
    {
        return view('tasks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'schedule' => 'required|in:daily,weekly,monthly,once',
            'target_date' => 'nullable|date|after_or_equal:today',
        ]);

        $validated['admin_id'] = 1;
        $validated['status'] = 'active';

        $task = Task::create($validated);

        $this->autoAssignTask($task);

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil dibuat!');
    }

    public function show(Task $task): View
    {
        $task->load(['assignments' => function ($query) {
            $query->orderBy('assigned_date', 'desc')->orderBy('created_at', 'desc');
        }]);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'schedule' => 'required|in:daily,weekly,monthly,once',
            'status' => 'required|in:active,inactive',
            'target_date' => 'nullable|date|after_or_equal:today',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil diperbarui!');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tugas berhasil dihapus!');
    }

    public function validation(): View
    {
        $assignments = TaskAssignment::with(['task'])
            ->needValidation()
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('tasks.validation', compact('assignments'));
    }

    public function validateAssignment(TaskAssignment $assignment): RedirectResponse
    {
        if ($assignment->status !== 'dikerjakan') {
            return back()->with('error', 'Tugas ini tidak dapat divalidasi!');
        }

        $assignment->validateTask();

        return back()->with('success', 'Tugas berhasil divalidasi!');
    }

    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Task Management System')
            ->setLastModifiedBy('Admin')
            ->setTitle('Laporan Performa User - Task Assignment')
            ->setSubject('Export Data Assignment')
            ->setDescription('Laporan performa pengerjaan tugas harian per user');

        // Header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '8B5CF6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Data styling
        $dataStyle = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        // Status color styling
        $statusStyles = [
            'pending' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3CD'],
                ],
            ],
            'dikerjakan' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D1ECF1'],
                ],
            ],
            'valid' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D4EDDA'],
                ],
            ],
        ];

        // Set headers
        $headers = [
            'A1' => 'No',
            'B1' => 'User ID',
            'C1' => 'Nama Tugas',
            'D1' => 'Deskripsi Tugas',
            'E1' => 'Tanggal Assignment',
            'F1' => 'Status',
            'G1' => 'Keterangan',
            'H1' => 'Attachment',
            'I1' => 'Tanggal Submit',
            'J1' => 'Tanggal Validasi',
            'K1' => 'Lama Pengerjaan (Hari)',
            'L1' => 'Performance'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Apply header styling
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(18);
        $sheet->getColumnDimension('J')->setWidth(18);
        $sheet->getColumnDimension('K')->setWidth(18);
        $sheet->getColumnDimension('L')->setWidth(15);

        // Get data - semua assignment dengan task info
        $assignments = TaskAssignment::with(['task'])
            ->orderBy('assigned_date', 'desc')
            ->orderBy('user_id', 'asc')
            ->get();

        $row = 2;
        foreach ($assignments as $index => $assignment) {
            // Hitung lama pengerjaan
            $lamaPengerjaan = '-';
            $performance = 'Belum Selesai';

            if ($assignment->submitted_at) {
                $diffDays = $assignment->assigned_date->diffInDays($assignment->submitted_at->toDateString());
                $lamaPengerjaan = $diffDays . ' hari';

                if ($assignment->isValidated()) {
                    $performance = $diffDays <= 1 ? 'Excellent' : ($diffDays <= 3 ? 'Good' : 'Average');
                } else {
                    $performance = 'Menunggu Validasi';
                }
            }

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $assignment->user_id);
            $sheet->setCellValue('C' . $row, $assignment->task->name);
            $sheet->setCellValue('D' . $row, $assignment->task->description);
            $sheet->setCellValue('E' . $row, $assignment->assigned_date->format('d/m/Y'));
            $sheet->setCellValue('F' . $row, $assignment->status_text);
            $sheet->setCellValue('G' . $row, $assignment->remarks ?: '-');
            $sheet->setCellValue('H' . $row, $assignment->attachment ? 'Ada' : 'Tidak ada');
            $sheet->setCellValue('I' . $row, $assignment->submitted_at ? $assignment->submitted_at->format('d/m/Y H:i') : '-');
            $sheet->setCellValue('J' . $row, $assignment->validated_at ? $assignment->validated_at->format('d/m/Y H:i') : '-');
            $sheet->setCellValue('K' . $row, $lamaPengerjaan);
            $sheet->setCellValue('L' . $row, $performance);

            // Apply status color
            if (isset($statusStyles[$assignment->status])) {
                $sheet->getStyle('F' . $row)->applyFromArray($statusStyles[$assignment->status]);
            }

            $row++;
        }

        // Apply data styling
        $lastRow = $row - 1;
        $sheet->getStyle('A2:L' . $lastRow)->applyFromArray($dataStyle);

        // Set row height for better readability
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(25);
        }

        // Create filename with current date
        $filename = 'Laporan_Performa_User_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Create writer and download
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function userIndex(): View
    {
        $today = Carbon::today();
        $userId = session('role');

        $todayTasks = Task::getTasksForDate($today);

        $assignments = collect();

        foreach ($todayTasks as $task) {
            $assignment = $task->getAssignmentForUserAndDate($userId, $today);

            if (!$assignment) {
                $assignment = TaskAssignment::create([
                    'task_id' => $task->id,
                    'user_id' => $userId,
                    'assigned_date' => $today,
                    'status' => 'pending'
                ]);
                $assignment->load('task');
            }

            $assignments->push($assignment);
        }

        return view('tasks.user-index', compact('assignments', 'today'));
    }

    public function userShow(TaskAssignment $assignment): View
    {
        if ($assignment->user_id !== session('role')) {
            abort(403, 'Tidak ada akses ke tugas ini');
        }

        $assignment->load('task');

        return view('tasks.user-show', compact('assignment'));
    }

    public function userSubmit(Request $request, TaskAssignment $assignment): RedirectResponse
    {
        if ($assignment->user_id !== session('role')) {
            abort(403, 'Tidak ada akses ke tugas ini');
        }
        if ($assignment->isSubmitted()) {
            return back()->with('error', 'Tugas ini sudah dikerjakan sebelumnya!');
        }

        $validated = $request->validate([
            'remarks' => 'required|string',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx'
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('task-attachments', $filename, 'public');
        }

        $assignment->submitTask($validated['remarks'], $attachmentPath);

        return redirect()->route('tasks.user.index')
            ->with('success', 'Tugas berhasil dikerjakan dan dikirim untuk validasi!');
    }

    private function autoAssignTask(Task $task)
    {
        // Logic ini bisa disesuaikan dengan kebutuhan
        // Misalnya assign ke semua user yang aktif
        // Untuk saat ini, kita biarkan kosong karena assignment dibuat saat user mengakses
    }

    public function downloadAttachment(TaskAssignment $assignment)
    {
        if (!$assignment->attachment) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = storage_path('app/public/' . $assignment->attachment);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath, $assignment->attachment_name);
    }
}
