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

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index(Request $request): View
{
    $query = Project::with('client');
    
    // Search functionality
    if ($request->filled('search')) {
        $query->search($request->search);
    }
    
    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    // Filter by type
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }
    
    // Filter by client
    if ($request->filled('client_id')) {
        $query->where('client_id', $request->client_id);
    }
    
    // Enhanced Sort functionality
    $sortBy = $request->get('sort', 'created_at');
    $sortOrder = $request->get('order', 'desc');
    
    // Handle different sorting cases
    switch ($sortBy) {
        case 'client_id':
            // Sort by client name instead of client_id
            $query->join('clients', 'projects.client_id', '=', 'clients.id')
                  ->orderBy('clients.name', $sortOrder)
                  ->select('projects.*');
            break;
            
        case 'title':
            $query->orderBy('title', $sortOrder);
            break;
            
        case 'type':
            $query->orderBy('type', $sortOrder);
            break;
            
        case 'total_value':
            $query->orderBy('total_value', $sortOrder);
            break;
            
        case 'progress_percentage':
            $query->orderBy('progress_percentage', $sortOrder);
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
    
    $projects = $query->paginate(15)->withQueryString();
    
    // ✅ FIX: Get statistics from ALL projects, not just filtered results
    $projectStats = [
        'waiting' => Project::where('status', 'WAITING')->count(),
        'progress' => Project::where('status', 'PROGRESS')->count(),
        'finished' => Project::where('status', 'FINISHED')->count(),
        'cancelled' => Project::where('status', 'CANCELLED')->count(),
    ];
    
    // Get filter options
    $clients = Client::orderBy('name')->get();
    $projectTypes = Project::distinct()->pluck('type')->filter();
    $statuses = ['WAITING', 'PROGRESS', 'FINISHED', 'CANCELLED'];
    
    return view('projects.index', compact(
        'projects',
        'projectStats',
        'clients',
        'projectTypes',
        'statuses'
    ));
}

    /**
     * Show the form for creating a new project
     */
    public function create(): View
    {
        $clients = Client::orderBy('name')->get();

        // Load project types from database instead of hardcoded array
        $projectTypes = ProjectType::active()->ordered()->get();

        // Fallback to default types if none exist in database
        if ($projectTypes->isEmpty()) {
            $defaultTypes = [
                ['name' => 'HTML/PHP', 'display_name' => 'HTML/PHP'],
                ['name' => 'LARAVEL', 'display_name' => 'Laravel Framework'],
                ['name' => 'WORDPRESS', 'display_name' => 'WordPress'],
                ['name' => 'REACT', 'display_name' => 'React.js'],
                ['name' => 'VUE', 'display_name' => 'Vue.js'],
                ['name' => 'FLUTTER', 'display_name' => 'Flutter'],
                ['name' => 'MOBILE', 'display_name' => 'Mobile App'],
                ['name' => 'OTHER', 'display_name' => 'Other'],
            ];

            // Create default types if database is empty
            foreach ($defaultTypes as $index => $typeData) {
                ProjectType::create([
                    'name' => $typeData['name'],
                    'display_name' => $typeData['display_name'],
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                ]);
            }

            // Reload project types
            $projectTypes = ProjectType::active()->ordered()->get();
        }

        return view('projects.create', compact('clients', 'projectTypes'));
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:HTML/PHP,LARAVEL,WORDPRESS,REACT,VUE,FLUTTER,MOBILE,OTHER',
            'total_value' => 'required|numeric|min:0',
            'dp_amount' => 'nullable|numeric|min:0',
            'deadline' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        // Set default DP amount if not provided
        $validated['dp_amount'] = $validated['dp_amount'] ?? 0;
        $validated['paid_amount'] = 0;
        $validated['status'] = 'WAITING';

        $project = Project::create($validated);

        // Create DP payment if DP amount > 0
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

    /**
     * Display the specified project
     */
    public function show(Project $project): View
    {
        $project->load(['client', 'payments']);

        // Calculate additional metrics
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

    /**
     * Show the form for editing the project
     */
    public function edit(Project $project): View
    {
        $clients = Client::orderBy('name')->get();
        $projectTypes = ['HTML/PHP', 'LARAVEL', 'WORDPRESS', 'REACT', 'VUE', 'FLUTTER', 'MOBILE', 'OTHER'];

        return view('projects.edit', compact('project', 'clients', 'projectTypes'));
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:HTML/PHP,LARAVEL,WORDPRESS,REACT,VUE,FLUTTER,MOBILE,OTHER',
            'total_value' => 'required|numeric|min:0',
            'deadline' => 'required|date',
            'status' => 'required|in:WAITING,PROGRESS,FINISHED,CANCELLED',
            'notes' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyek berhasil diperbarui!');
    }

    /**
     * Remove the specified project
     */
    public function destroy(Project $project): RedirectResponse
    {
        // Check if project has payments
        if ($project->payments()->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'Tidak dapat menghapus proyek yang sudah memiliki pembayaran!');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus!');
    }

    /**
     * Update project status
     */
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

    /**
     * Get active projects for API
     */
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
                ];
            });

        return response()->json($projects);
    }

    /**
     * Get upcoming deadlines for API
     */
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
                ];
            });

        return response()->json($projects);
    }

    /**
     * Get project payments for API
     */
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
            ],
            'payments' => $payments
        ]);
    }
}
