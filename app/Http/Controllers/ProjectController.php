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
}
