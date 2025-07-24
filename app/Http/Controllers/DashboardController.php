<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\BankTransfer;
use App\Models\GoldTransaction;
use App\Models\BankBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
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

        // Hitung saldo bank dan emas
        $saldoOcto = BankBalance::getCurrentBalance();

        $totalBeliEmas = GoldTransaction::buy()->sum('grams');
        $totalJualEmas = GoldTransaction::sell()->sum('grams');
        $sisaEmas = $totalBeliEmas - $totalJualEmas;

        $totalInvestasiEmas = GoldTransaction::buy()->sum('total_price');
        $hargaRataRataEmas = $totalBeliEmas > 0 ? $totalInvestasiEmas / $totalBeliEmas : 0;
        $saldoEmas = $sisaEmas * $hargaRataRataEmas;

        // Deadline proyek terdekat
        $proyekDeadlineTermedekat = Project::with('client')
            ->whereIn('status', ['WAITING', 'PROGRESS'])
            ->where('deadline', '>=', Carbon::now())
            ->orderBy('deadline')
            ->limit($isMobile ? 3 : 5)
            ->get();

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

            // Pindah ke minggu berikutnya
            $currentWeekStart = $currentWeekEnd->copy()->addDay();
            $weekNumber++;
        }

        // Data untuk pie chart (komposisi aset) dengan nilai
        $pieData = [
            'labels' => [
                'Piutang: ' . $formatCurrency($totalPiutang),
                'Bank: ' . $formatCurrency($saldoOcto),
                'Emas: ' . $formatCurrency($saldoEmas)
            ],
            'data' => [$totalPiutang, $saldoOcto, $saldoEmas],
            'colors' => ['#3B82F6', '#10B981', '#F59E0B'],
            'total' => $totalPiutang + $saldoOcto + $saldoEmas
        ];

        return view('dashboard.index', [
            'proyekMenunggu' => $proyekMenunggu,
            'proyekProgress' => $proyekProgress,
            'totalPendapatan' => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalPiutang' => $totalPiutang,
            'saldoOcto' => $saldoOcto,
            'saldoEmas' => $saldoEmas,
            'proyekDeadlineTermedekat' => $proyekDeadlineTermedekat,
            'formatCurrency' => $formatCurrency,
            'isMobile' => $isMobile,
            'weeklyData' => $weeklyData,
            'pieData' => $pieData,
        ]);
    }

    public function userIndex(Request $request)
    {
        //
    }
}
