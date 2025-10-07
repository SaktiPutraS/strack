<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\BankTransfer;
use App\Models\GoldTransaction;
use App\Models\BankBalance;
use App\Models\CashBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use App\Models\Task;
use App\Models\TaskAssignment;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        if (!session('role') || session('role') !== 'admin') {
            return redirect()->route('dashboard.user');
        }

        $agent = new Agent();
        $isMobile = $agent->isMobile();

        // Hitung proyek
        $proyekMenunggu = Project::where('status', 'WAITING')->count();
        $proyekProgress = Project::where('status', 'PROGRESS')->count();

        // Hitung keuangan bulan ini
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $totalPendapatan = Payment::whereMonth('payment_date', $bulanIni)
            ->whereYear('payment_date', $tahunIni)
            ->sum('amount');

        $totalPengeluaran = Expense::whereMonth('expense_date', $bulanIni)
            ->whereYear('expense_date', $tahunIni)
            ->sum('amount');

        $totalPiutang = Project::whereIn('status', ['WAITING', 'PROGRESS'])
            ->sum(DB::raw('total_value - paid_amount'));

        // Hitung saldo bank dan cash
        $saldoBank = BankBalance::getCurrentBalance();
        $saldoCash = CashBalance::getCurrentBalance();
        $totalKas = $saldoBank + $saldoCash;

        $totalBeliEmas = GoldTransaction::buy()->sum('grams');
        $totalJualEmas = GoldTransaction::sell()->sum('grams');
        $sisaEmas = $totalBeliEmas - $totalJualEmas;

        $totalInvestasiEmas = GoldTransaction::buy()->sum('total_price');
        $hargaRataRataEmas = $totalBeliEmas > 0 ? $totalInvestasiEmas / $totalBeliEmas : 0;
        $saldoEmas = $sisaEmas * $hargaRataRataEmas;

        // Format nilai untuk tampilan mobile
        $formatCurrency = function ($amount) {
            if ($amount >= 1000000000) {
                return 'Rp ' . number_format($amount / 1000000000, 1, ',', '.') . 'M';
            } elseif ($amount >= 1000000) {
                return 'Rp ' . number_format($amount / 1000000, 1, ',', '.') . 'Jt';
            }
            return 'Rp ' . number_format($amount, 0, ',', '.');
        };

        // Data untuk line chart (per minggu dalam setahun ini, di mulai dari juli)
        $startOfYear = Carbon::now()->startOfYear()->addMonths(6);
        if ($startOfYear->month < 7) {
            $startOfYear->subYear();
        }
        $endOfYear = Carbon::now()->endOfYear()->addMonths(6);
        if ($endOfYear->month < 7) {
            $endOfYear->subYear();
        }

        $currentDate = Carbon::now();

        $weeklyData = [];
        $currentWeekStart = $startOfYear->copy();
        $weekNumber = 1;

        while ($currentWeekStart < $currentDate) {
            $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();
            if ($currentWeekEnd > $currentDate) {
                $currentWeekEnd = $currentDate;
            }

            $income = Payment::whereBetween('payment_date', [$currentWeekStart, $currentWeekEnd])
                ->sum('amount');

            $expense = Expense::whereBetween('expense_date', [$currentWeekStart, $currentWeekEnd])
                ->sum('amount');

            $weeklyData[] = [
                'week' => 'Minggu ' . $weekNumber,
                'start_date' => $currentWeekStart->format('d M'),
                'end_date' => $currentWeekEnd->format('d M'),
                'income' => $income,
                'expense' => $expense
            ];

            $currentWeekStart = $currentWeekEnd->copy()->addDay();
            $weekNumber++;
        }

        // Data untuk grafik pendapatan per bulan (total nilai proyek)
        $monthlyRevenueData = [];
        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ags',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];

        for ($month = 1; $month <= 12; $month++) {
            $projectValue = Project::whereMonth('created_at', $month)
                ->whereYear('created_at', $tahunIni)
                ->sum('total_value');

            $monthlyRevenueData[] = [
                'month' => $monthNames[$month],
                'month_number' => $month,
                'project_value' => $projectValue,
                'formatted_value' => $formatCurrency($projectValue)
            ];
        }

        // Data untuk pie chart (komposisi aset) dengan breakdown kas
        $pieData = [
            'labels' => [
                'Piutang: ' . $formatCurrency($totalPiutang),
                'Bank: ' . $formatCurrency($saldoBank),
                'Cash: ' . $formatCurrency($saldoCash),
                'Emas: ' . $formatCurrency($saldoEmas)
            ],
            'data' => [$totalPiutang, $saldoBank, $saldoCash, $saldoEmas],
            'colors' => ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B'],
            'total' => $totalPiutang + $totalKas + $saldoEmas
        ];

        // Data kalender catatan untuk bulan ini
        $currentMonth = Carbon::now();
        $calendarData = [
            'currentMonth' => $currentMonth->format('F Y'),
            'currentYear' => $currentMonth->year,
            'currentMonthNumber' => $currentMonth->month,
            'firstDayOfMonth' => $currentMonth->copy()->startOfMonth(),
            'lastDayOfMonth' => $currentMonth->copy()->endOfMonth(),
            'today' => Carbon::today(),
        ];

        // Ambil catatan untuk bulan ini
        $userId = session('role');
        $calendarNotes = \App\Models\CalendarNote::getNotesForMonth(
            $userId,
            $calendarData['currentYear'],
            $calendarData['currentMonthNumber']
        );

        // NEW: Ambil project deadlines untuk bulan ini
        $projectDeadlines = Project::with('client')
            ->whereYear('deadline', $calendarData['currentYear'])
            ->whereMonth('deadline', $calendarData['currentMonthNumber'])
            ->whereIn('status', ['WAITING', 'PROGRESS'])
            ->get()
            ->groupBy(function ($project) {
                return $project->deadline->day;
            });

        return view('dashboard.index', [
            'proyekMenunggu' => $proyekMenunggu,
            'proyekProgress' => $proyekProgress,
            'totalPendapatan' => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalPiutang' => $totalPiutang,
            'saldoBank' => $saldoBank,
            'saldoCash' => $saldoCash,
            'totalKas' => $totalKas,
            'saldoEmas' => $saldoEmas,
            'formatCurrency' => $formatCurrency,
            'isMobile' => $isMobile,
            'weeklyData' => $weeklyData,
            'monthlyRevenueData' => $monthlyRevenueData,
            'pieData' => $pieData,
            'calendarData' => $calendarData,
            'calendarNotes' => $calendarNotes,
            'projectDeadlines' => $projectDeadlines, // NEW
        ]);
    }

    public function userIndex(Request $request)
    {
        $agent = new Agent();
        $isMobile = $agent->isMobile();
        $today = Carbon::today();
        $userId = session('role');

        // Get today's tasks for current user
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

        // Calculate statistics
        $todayStats = [
            'total' => $assignments->count(),
            'pending' => $assignments->where('status', 'pending')->count(),
            'submitted' => $assignments->where('status', 'dikerjakan')->count(),
            'completed' => $assignments->where('status', 'valid')->count(),
        ];

        // Calculate progress percentage
        $progressPercentage = $todayStats['total'] > 0
            ? round(($todayStats['completed'] / $todayStats['total']) * 100)
            : 0;

        // Get next pending task
        $nextTask = $assignments->where('status', 'pending')->first();

        // NEW: Data kalender catatan untuk bulan ini
        $currentMonth = Carbon::now();
        $calendarData = [
            'currentMonth' => $currentMonth->format('F Y'),
            'currentYear' => $currentMonth->year,
            'currentMonthNumber' => $currentMonth->month,
            'firstDayOfMonth' => $currentMonth->copy()->startOfMonth(),
            'lastDayOfMonth' => $currentMonth->copy()->endOfMonth(),
            'today' => Carbon::today(),
        ];

        // Ambil catatan untuk bulan ini
        $calendarNotes = \App\Models\CalendarNote::getNotesForMonth(
            $userId,
            $calendarData['currentYear'],
            $calendarData['currentMonthNumber']
        );

        // NEW: Ambil project deadlines untuk bulan ini (semua proyek, tidak hanya milik user)
        $projectDeadlines = Project::with('client')
            ->whereYear('deadline', $calendarData['currentYear'])
            ->whereMonth('deadline', $calendarData['currentMonthNumber'])
            ->whereIn('status', ['WAITING', 'PROGRESS'])
            ->get()
            ->groupBy(function ($project) {
                return $project->deadline->day;
            });

        return view('dashboard.index-user', [
            'isMobile' => $isMobile,
            'todayStats' => $todayStats,
            'progressPercentage' => $progressPercentage,
            'todayTasks' => $assignments,
            'nextTask' => $nextTask,
            'calendarData' => $calendarData, // NEW
            'calendarNotes' => $calendarNotes, // NEW
            'projectDeadlines' => $projectDeadlines, // NEW
        ]);
    }
}
