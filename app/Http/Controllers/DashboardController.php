<?php
// app/Http/Controllers/DashboardController.php - SIMPLIFIED VERSION

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

class DashboardController extends Controller
{
    /**
     * Display dashboard with simplified data summary
     */
    public function index()
    {
        // 1. Proyek Menunggu
        $proyekMenunggu = Project::where('status', 'WAITING')->count();

        // 2. Proyek Progress
        $proyekProgress = Project::where('status', 'PROGRESS')->count();

        // 3. Total Pendapatan (dari semua payment)
        $totalPendapatan = Payment::sum('amount');

        // 4. Total Piutang (sisa pembayaran dari proyek aktif)
        $totalPiutang = Project::whereIn('status', ['WAITING', 'PROGRESS'])
            ->sum(DB::raw('total_value - paid_amount'));

        // 5. Total Pengeluaran
        $totalPengeluaran = Expense::sum('amount');

        // 6. Saldo Bank Octo
        $saldoOcto = BankBalance::getCurrentBalance();

        // 7. Saldo Emas (dalam rupiah berdasarkan harga beli rata-rata)
        $totalBeliEmas = GoldTransaction::buy()->sum('grams');
        $totalJualEmas = GoldTransaction::sell()->sum('grams');
        $sisaEmas = $totalBeliEmas - $totalJualEmas;

        $totalInvestasiEmas = GoldTransaction::buy()->sum('total_price');
        $hargaRataRataEmas = $totalBeliEmas > 0 ? $totalInvestasiEmas / $totalBeliEmas : 0;
        $saldoEmas = $sisaEmas * $hargaRataRataEmas;

        // List: Proyek dengan deadline terdekat (max 3)
        $proyekDeadlineTermedekat = Project::with('client')
            ->whereIn('status', ['WAITING', 'PROGRESS'])
            ->where('deadline', '>=', Carbon::now())
            ->orderBy('deadline')
            ->limit(3)
            ->get();

        return view('dashboard.index', compact(
            'proyekMenunggu',
            'proyekProgress',
            'totalPendapatan',
            'totalPiutang',
            'totalPengeluaran',
            'saldoOcto',
            'saldoEmas',
            'proyekDeadlineTermedekat'
        ));
    }
}
