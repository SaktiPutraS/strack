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

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = Task::with('assignments')->orderBy('created_at', 'desc')->get();
        return view('tasks.index', compact('tasks'));
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
