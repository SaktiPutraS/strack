<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display dashboard
     */
    public function index()
    {
        // Get basic statistics
        $stats = $this->getBasicStats();

        // Get active projects
        $activeProjects = Project::with('client')
            ->active()
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        // Get upcoming deadlines
        $upcomingDeadlines = Project::with('client')
            ->upcomingDeadlines(15)
            ->get();

        // Get recent payments
        $recentPayments = Payment::with(['project.client'])
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'activeProjects',
            'upcomingDeadlines',
            'recentPayments'
        ));
    }

    /**
     * Get basic statistics for dashboard
     */
    private function getBasicStats(): array
    {
        // Project statistics
        $totalProjects = Project::count();
        $activeProjects = Project::active()->count();
        $finishedProjects = Project::finished()->count();

        // Financial statistics
        $totalValue = Project::sum('total_value');
        $totalPaid = Project::sum('paid_amount');
        $totalRemaining = $totalValue - $totalPaid;

        // Client statistics
        $totalClients = Client::count();

        // Status breakdown
        $statusBreakdown = [
            'waiting' => Project::where('status', 'WAITING')->count(),
            'progress' => Project::where('status', 'PROGRESS')->count(),
            'finished' => Project::where('status', 'FINISHED')->count(),
            'cancelled' => Project::where('status', 'CANCELLED')->count(),
        ];

        // Monthly income (last 6 months)
        $monthlyIncome = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $income = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
            $monthlyIncome[] = [
                'month' => $date->format('M Y'),
                'amount' => $income
            ];
        }

        return [
            'projects' => [
                'total' => $totalProjects,
                'active' => $activeProjects,
                'finished' => $finishedProjects,
                'waiting' => $statusBreakdown['waiting'],
                'progress' => $statusBreakdown['progress'],
                'cancelled' => $statusBreakdown['cancelled'],
            ],
            'financial' => [
                'total_value' => $totalValue,
                'total_paid' => $totalPaid,
                'total_remaining' => $totalRemaining,
                'completion_percentage' => $totalValue > 0 ? round(($totalPaid / $totalValue) * 100, 1) : 0,
            ],
            'clients' => [
                'total' => $totalClients,
            ],
            'monthly_income' => $monthlyIncome,
            'status_breakdown' => $statusBreakdown,
        ];
    }

    /**
     * Get stats for API/AJAX requests
     */
    public function getStats(): JsonResponse
    {
        return response()->json($this->getBasicStats());
    }

    /**
     * Get chart data for financial overview
     */
    public function getFinancialChart(): JsonResponse
    {
        // Monthly income vs target
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $income = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'income' => $income,
                'target' => 5000000, // Target 5 juta per bulan
            ];
        }

        // Project type distribution
        $projectTypes = Project::selectRaw('type, COUNT(*) as count, SUM(total_value) as total_value')
            ->groupBy('type')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->type,
                    'count' => $item->count,
                    'value' => $item->total_value,
                ];
            });

        return response()->json([
            'monthly_income' => $monthlyData,
            'project_types' => $projectTypes,
        ]);
    }

    /**
     * Get overdue projects
     */
    public function getOverdueProjects(): JsonResponse
    {
        $overdueProjects = Project::with('client')
            ->overdue()
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'client' => $project->client->name,
                    'deadline' => $project->deadline->format('d M Y'),
                    'days_overdue' => abs($project->days_until_deadline),
                    'status' => $project->status,
                    'remaining_amount' => $project->remaining_amount,
                ];
            });

        return response()->json($overdueProjects);
    }

    /**
     * Get quick actions data
     */
    public function getQuickActions(): JsonResponse
    {
        return response()->json([
            'recent_activities' => [
                'latest_payment' => Payment::with(['project.client'])
                    ->latest('payment_date')
                    ->first(),
                'latest_project' => Project::with('client')
                    ->latest()
                    ->first(),
            ],
            'urgent_tasks' => [
                'overdue_count' => Project::overdue()->count(),
                'deadline_this_week' => Project::upcomingDeadlines(7)->count(),
            ]
        ]);
    }
}
