<?php

namespace App\Http\Controllers;

use App\Models\BankTransfer;
use App\Models\Expense;
use App\Models\CashWithdrawal;
use App\Models\GoldTransaction;
use App\Models\BankBalance;
use App\Models\CashBalance;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        // A. Laporan Laba Rugi (Enhanced with Cash)
        $laporanLabaRugi = $this->generateLaporanLabaRugi($startDate, $endDate);

        // B. Neraca Sederhana (Enhanced with Cash)
        $neracaSederhana = $this->generateNeracaSederhana();

        // C. Portfolio Emas
        $portfolioEmas = $this->generatePortfolioEmas();

        // D. Arus Kas Bank & Cash (Enhanced)
        $arusKas = $this->generateArusKas($startDate, $endDate);

        // E. Laporan Penjualan Project (NEW)
        $laporanPenjualan = $this->generateLaporanPenjualan($startDate, $endDate);

        // Current Balances
        $currentBankBalance = BankBalance::getCurrentBalance();
        $currentCashBalance = CashBalance::getCurrentBalance();
        $formattedBankBalance = 'Rp ' . number_format($currentBankBalance, 0, ',', '.');
        $formattedCashBalance = 'Rp ' . number_format($currentCashBalance, 0, ',', '.');

        return view('financial-reports.index', compact(
            'laporanLabaRugi',
            'neracaSederhana',
            'portfolioEmas',
            'arusKas',
            'laporanPenjualan',
            'currentBankBalance',
            'currentCashBalance',
            'formattedBankBalance',
            'formattedCashBalance',
            'startDate',
            'endDate'
        ));
    }

    private function generateLaporanLabaRugi(string $startDate, string $endDate): array
    {
        // PENDAPATAN (yang sudah masuk ke Bank Octo)
        $transferDariPembayaran = BankTransfer::whereBetween('transfer_date', [$startDate, $endDate])
            ->sum('transfer_amount');

        $hasilPenjualanEmas = GoldTransaction::sell()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_price');

        $pendapatanLainLain = 0;
        $totalPendapatanBankOcto = $transferDariPembayaran + $hasilPenjualanEmas + $pendapatanLainLain;

        // PENGELUARAN by Category & Source
        $pengeluaranBank = Expense::where('source', 'BANK')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        $pengeluaranCash = Expense::where('source', 'CASH')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        // Total pengeluaran operasional
        $totalPengeluaranBank = $pengeluaranBank->sum('total');
        $totalPengeluaranCash = $pengeluaranCash->sum('total');
        $totalPengeluaranOperasional = $totalPengeluaranBank + $totalPengeluaranCash;

        // INVESTASI
        $pembelianEmas = GoldTransaction::buy()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_price');

        // CASH WITHDRAWAL (mengurangi bank, tapi bukan expense)
        $cashWithdrawals = CashWithdrawal::whereBetween('withdrawal_date', [$startDate, $endDate])
            ->sum('amount');

        $totalPengeluaranDanInvestasi = $totalPengeluaranOperasional + $pembelianEmas;

        // LABA/RUGI & SALDO
        $labaRugiOperasional = $totalPendapatanBankOcto - $totalPengeluaranDanInvestasi;
        $saldoBankOctoAkhir = BankBalance::getCurrentBalance();
        $saldoCashAkhir = CashBalance::getCurrentBalance();

        return [
            'pendapatan' => [
                'transfer_dari_pembayaran' => $transferDariPembayaran,
                'hasil_penjualan_emas' => $hasilPenjualanEmas,
                'pendapatan_lain_lain' => $pendapatanLainLain,
                'total_pendapatan_bank_octo' => $totalPendapatanBankOcto,
            ],
            'pengeluaran' => [
                'bank' => $pengeluaranBank,
                'cash' => $pengeluaranCash,
                'total_bank' => $totalPengeluaranBank,
                'total_cash' => $totalPengeluaranCash,
                'total_operasional' => $totalPengeluaranOperasional,
            ],
            'investasi' => [
                'pembelian_emas' => $pembelianEmas,
                'total_pengeluaran_dan_investasi' => $totalPengeluaranDanInvestasi,
            ],
            'cash_management' => [
                'cash_withdrawals' => $cashWithdrawals,
            ],
            'hasil' => [
                'laba_rugi_operasional' => $labaRugiOperasional,
                'saldo_bank_octo_akhir' => $saldoBankOctoAkhir,
                'saldo_cash_akhir' => $saldoCashAkhir,
            ]
        ];
    }

    private function generateNeracaSederhana(): array
    {
        // ASET
        $kasBankOcto = BankBalance::getCurrentBalance();
        $kasCash = CashBalance::getCurrentBalance();

        // Investasi Emas
        $totalBeliEmas = GoldTransaction::buy()->sum('grams');
        $totalJualEmas = GoldTransaction::sell()->sum('grams');
        $sisaEmas = $totalBeliEmas - $totalJualEmas;

        $totalInvestasiEmas = GoldTransaction::buy()->sum('total_price');
        $rataRataHargaBeli = $totalBeliEmas > 0 ? $totalInvestasiEmas / $totalBeliEmas : 0;
        $nilaiInvestasiEmas = $sisaEmas * $rataRataHargaBeli;

        $totalAset = $kasBankOcto + $kasCash + $nilaiInvestasiEmas;

        // PIUTANG
        $pembayaranBelumTransfer = Payment::where('is_transferred', false)->sum('amount');
        $sisaTagihanProyek = Project::whereIn('status', ['WAITING', 'PROGRESS'])
            ->sum(DB::raw('total_value - paid_amount'));

        $totalPiutang = $pembayaranBelumTransfer + $sisaTagihanProyek;

        // NET WORTH
        $netWorth = $totalAset + $totalPiutang;

        return [
            'aset' => [
                'kas_bank_octo' => $kasBankOcto,
                'kas_cash' => $kasCash,
                'total_kas' => $kasBankOcto + $kasCash,
                'investasi_emas' => [
                    'grams' => $sisaEmas,
                    'rata_rata_harga' => $rataRataHargaBeli,
                    'nilai' => $nilaiInvestasiEmas,
                ],
                'total_aset' => $totalAset,
            ],
            'piutang' => [
                'pembayaran_belum_transfer' => $pembayaranBelumTransfer,
                'sisa_tagihan_proyek' => $sisaTagihanProyek,
                'total_piutang' => $totalPiutang,
            ],
            'net_worth' => $netWorth,
        ];
    }

    private function generatePortfolioEmas(): array
    {
        $totalBeliEmas = GoldTransaction::buy()->sum('grams');
        $totalJualEmas = GoldTransaction::sell()->sum('grams');
        $sisaEmas = $totalBeliEmas - $totalJualEmas;

        $totalInvestasiEmas = GoldTransaction::buy()->sum('total_price');
        $rataRataHargaBeli = $totalBeliEmas > 0 ? $totalInvestasiEmas / $totalBeliEmas : 0;

        return [
            'total_emas' => $sisaEmas,
            'rata_rata_harga_beli' => $rataRataHargaBeli,
            'total_investasi' => $totalInvestasiEmas,
        ];
    }

    private function generateArusKas(string $startDate, string $endDate): array
    {
        // PEMASUKAN BANK
        $transferMasuk = BankTransfer::with('payment.project.client')
            ->whereBetween('transfer_date', [$startDate, $endDate])
            ->orderBy('transfer_date', 'desc')
            ->get();

        $penjualanEmas = GoldTransaction::sell()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $totalPemasukanBank = $transferMasuk->sum('transfer_amount') + $penjualanEmas->sum('total_price');

        // PENGELUARAN BANK
        $pengeluaranBank = Expense::where('source', 'BANK')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();

        $pembelianEmas = GoldTransaction::buy()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $cashWithdrawals = CashWithdrawal::whereBetween('withdrawal_date', [$startDate, $endDate])
            ->orderBy('withdrawal_date', 'desc')
            ->get();

        $totalPengeluaranBank = $pengeluaranBank->sum('amount') + $pembelianEmas->sum('total_price') + $cashWithdrawals->sum('amount');

        // ARUS KAS CASH
        $pemasukanCash = $cashWithdrawals; // Cash masuk dari withdrawal bank
        $pengeluaranCash = Expense::where('source', 'CASH')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalPemasukanCash = $cashWithdrawals->sum('amount');
        $totalPengeluaranCash = $pengeluaranCash->sum('amount');

        // RINGKASAN
        $netCashFlowBank = $totalPemasukanBank - $totalPengeluaranBank;
        $netCashFlowCash = $totalPemasukanCash - $totalPengeluaranCash;
        $saldoBankAkhir = BankBalance::getCurrentBalance();
        $saldoCashAkhir = CashBalance::getCurrentBalance();

        return [
            'bank' => [
                'pemasukan' => [
                    'transfer_masuk' => $transferMasuk,
                    'penjualan_emas' => $penjualanEmas,
                    'total' => $totalPemasukanBank,
                ],
                'pengeluaran' => [
                    'expenses' => $pengeluaranBank,
                    'pembelian_emas' => $pembelianEmas,
                    'cash_withdrawals' => $cashWithdrawals,
                    'total' => $totalPengeluaranBank,
                ],
                'net_flow' => $netCashFlowBank,
                'saldo_akhir' => $saldoBankAkhir,
            ],
            'cash' => [
                'pemasukan' => [
                    'withdrawals' => $pemasukanCash,
                    'total' => $totalPemasukanCash,
                ],
                'pengeluaran' => [
                    'expenses' => $pengeluaranCash,
                    'total' => $totalPengeluaranCash,
                ],
                'net_flow' => $netCashFlowCash,
                'saldo_akhir' => $saldoCashAkhir,
            ]
        ];
    }

    private function generateLaporanPenjualan(string $startDate, string $endDate): array
    {
        // Project yang dibuat dalam periode
        $projectsInPeriod = Project::with(['client', 'payments'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Pembayaran yang diterima dalam periode (terlepas kapan project dibuat)
        $paymentsInPeriod = Payment::with(['project.client'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();

        // Statistik Project
        $totalProjects = $projectsInPeriod->count();
        $totalNilaiProject = $projectsInPeriod->sum('total_value');
        $totalDpProject = $projectsInPeriod->sum('dp_amount');

        // Statistik by Type
        $projectsByType = $projectsInPeriod->groupBy('type')->map(function ($projects, $type) {
            return [
                'count' => $projects->count(),
                'total_value' => $projects->sum('total_value'),
                'avg_value' => $projects->avg('total_value'),
            ];
        });

        // Statistik by Status
        $projectsByStatus = $projectsInPeriod->groupBy('status')->map(function ($projects) {
            return [
                'count' => $projects->count(),
                'total_value' => $projects->sum('total_value'),
            ];
        });

        // Statistik Pembayaran
        $totalPayments = $paymentsInPeriod->count();
        $totalNilaiPayments = $paymentsInPeriod->sum('amount');

        $paymentsByType = $paymentsInPeriod->groupBy('payment_type')->map(function ($payments) {
            return [
                'count' => $payments->count(),
                'total_amount' => $payments->sum('amount'),
                'avg_amount' => $payments->avg('amount'),
            ];
        });

        // Top Clients by Value
        $topClients = $projectsInPeriod->groupBy('client.name')->map(function ($projects, $clientName) {
            return [
                'client_name' => $clientName,
                'project_count' => $projects->count(),
                'total_value' => $projects->sum('total_value'),
                'avg_value' => $projects->avg('total_value'),
            ];
        })->sortByDesc('total_value')->take(5);

        return [
            'projects' => [
                'list' => $projectsInPeriod,
                'total_count' => $totalProjects,
                'total_value' => $totalNilaiProject,
                'total_dp' => $totalDpProject,
                'avg_value' => $totalProjects > 0 ? $totalNilaiProject / $totalProjects : 0,
                'by_type' => $projectsByType,
                'by_status' => $projectsByStatus,
            ],
            'payments' => [
                'list' => $paymentsInPeriod,
                'total_count' => $totalPayments,
                'total_amount' => $totalNilaiPayments,
                'avg_amount' => $totalPayments > 0 ? $totalNilaiPayments / $totalPayments : 0,
                'by_type' => $paymentsByType,
            ],
            'top_clients' => $topClients,
        ];
    }
}
