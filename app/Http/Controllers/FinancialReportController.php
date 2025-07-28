<?php

namespace App\Http\Controllers;

use App\Models\BankTransfer;
use App\Models\Expense;
use App\Models\GoldTransaction;
use App\Models\BankBalance;
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

        // A. Laporan Laba Rugi
        $laporanLabaRugi = $this->generateLaporanLabaRugi($startDate, $endDate);

        // B. Neraca Sederhana
        $neracaSederhana = $this->generateNeracaSederhana();

        // C. Portfolio Emas
        $portfolioEmas = $this->generatePortfolioEmas();

        // D. Arus Kas Bank Octo (NEW)
        $arusKasBank = $this->generateArusKasBank($startDate, $endDate);

        // Current Bank Balance
        $currentBankBalance = BankBalance::getCurrentBalance();
        $formattedBankBalance = 'Rp ' . number_format($currentBankBalance, 0, ',', '.');

        return view('financial-reports.index', compact(
            'laporanLabaRugi',
            'neracaSederhana',
            'portfolioEmas',
            'arusKasBank',
            'currentBankBalance',
            'formattedBankBalance',
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

        $pendapatanLainLain = 0; // Placeholder untuk pendapatan lain

        $totalPendapatanBankOcto = $transferDariPembayaran + $hasilPenjualanEmas + $pendapatanLainLain;

        // PENGELUARAN by Category - Updated dengan kategori yang ada di Expense model
        $pengeluaranByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        // Mapping sesuai dengan CATEGORIES di Expense model
        $biayaAI = $pengeluaranByCategory->get('AI')->total ?? 0;
        $biayaAdminBank = $pengeluaranByCategory->get('ADMIN_BANK')->total ?? 0;
        $biayaBuku = $pengeluaranByCategory->get('BUKU')->total ?? 0;
        $biayaDomainHosting = $pengeluaranByCategory->get('DOMAIN_HOSTING')->total ?? 0;
        $biayaEntertain = $pengeluaranByCategory->get('ENTERTAIN')->total ?? 0;
        $biayaGajiBonus = $pengeluaranByCategory->get('GAJI_BONUS')->total ?? 0;
        $biayaKopiSusu = $pengeluaranByCategory->get('KOPI_SUSU')->total ?? 0;
        $biayaLainnya = $pengeluaranByCategory->get('LAINNYA')->total ?? 0;

        $totalPengeluaranOperasional = $biayaAI + $biayaAdminBank + $biayaBuku + $biayaDomainHosting +
            $biayaEntertain + $biayaGajiBonus + $biayaKopiSusu + $biayaLainnya;

        // INVESTASI
        $pembelianEmas = GoldTransaction::buy()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_price');

        $totalPengeluaranDanInvestasi = $totalPengeluaranOperasional + $pembelianEmas;

        // LABA/RUGI & SALDO
        $labaRugiOperasional = $totalPendapatanBankOcto - $totalPengeluaranDanInvestasi;
        $saldoBankOctoAkhir = BankBalance::getCurrentBalance();

        return [
            'pendapatan' => [
                'transfer_dari_pembayaran' => $transferDariPembayaran,
                'hasil_penjualan_emas' => $hasilPenjualanEmas,
                'pendapatan_lain_lain' => $pendapatanLainLain,
                'total_pendapatan_bank_octo' => $totalPendapatanBankOcto,
            ],
            'pengeluaran' => [
                'biaya_ai' => $biayaAI,
                'biaya_admin_bank' => $biayaAdminBank,
                'biaya_buku' => $biayaBuku,
                'biaya_domain_hosting' => $biayaDomainHosting,
                'biaya_entertain' => $biayaEntertain,
                'biaya_gaji_bonus' => $biayaGajiBonus,
                'biaya_kopi_susu' => $biayaKopiSusu,
                'biaya_lainnya' => $biayaLainnya,
                'total_pengeluaran_operasional' => $totalPengeluaranOperasional,
            ],
            'investasi' => [
                'pembelian_emas' => $pembelianEmas,
                'total_pengeluaran_dan_investasi' => $totalPengeluaranDanInvestasi,
            ],
            'hasil' => [
                'laba_rugi_operasional' => $labaRugiOperasional,
                'saldo_bank_octo_akhir' => $saldoBankOctoAkhir,
            ]
        ];
    }

    private function generateNeracaSederhana(): array
    {
        // ASET
        $kasBankOcto = BankBalance::getCurrentBalance();

        // Investasi Emas
        $totalBeliEmas = GoldTransaction::buy()->sum('grams');
        $totalJualEmas = GoldTransaction::sell()->sum('grams');
        $sisaEmas = $totalBeliEmas - $totalJualEmas;

        $totalInvestasiEmas = GoldTransaction::buy()->sum('total_price');
        $rataRataHargaBeli = $totalBeliEmas > 0 ? $totalInvestasiEmas / $totalBeliEmas : 0;
        $nilaiInvestasiEmas = $sisaEmas * $rataRataHargaBeli;

        $totalAset = $kasBankOcto + $nilaiInvestasiEmas;

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

    private function generateArusKasBank(string $startDate, string $endDate): array
    {
        // PEMASUKAN
        $transferMasuk = BankTransfer::with('payment.project.client')
            ->whereBetween('transfer_date', [$startDate, $endDate])
            ->orderBy('transfer_date', 'desc')
            ->get();

        $penjualanEmas = GoldTransaction::sell()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $totalPemasukan = $transferMasuk->sum('transfer_amount') + $penjualanEmas->sum('total_price');

        // PENGELUARAN
        $pengeluaranDetail = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();

        $pembelianEmas = GoldTransaction::buy()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $totalPengeluaran = $pengeluaranDetail->sum('amount') + $pembelianEmas->sum('total_price');

        // RINGKASAN
        $netCashFlow = $totalPemasukan - $totalPengeluaran;
        $saldoAwal = BankBalance::getCurrentBalance() - $netCashFlow; // Estimasi saldo awal
        $saldoAkhir = BankBalance::getCurrentBalance();

        return [
            'pemasukan' => [
                'transfer_masuk' => $transferMasuk,
                'penjualan_emas' => $penjualanEmas,
                'total_pemasukan' => $totalPemasukan,
            ],
            'pengeluaran' => [
                'pengeluaran_detail' => $pengeluaranDetail,
                'pembelian_emas' => $pembelianEmas,
                'total_pengeluaran' => $totalPengeluaran,
            ],
            'ringkasan' => [
                'saldo_awal_estimasi' => $saldoAwal,
                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'net_cash_flow' => $netCashFlow,
                'saldo_akhir' => $saldoAkhir,
            ]
        ];
    }
}
