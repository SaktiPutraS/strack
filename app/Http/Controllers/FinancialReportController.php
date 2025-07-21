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

        return view('financial-reports.index', compact(
            'laporanLabaRugi',
            'neracaSederhana',
            'portfolioEmas',
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

        // PENGELUARAN by Category
        $pengeluaranByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        $biayaOperasional = $pengeluaranByCategory->get('OPERASIONAL')->total ?? 0;
        $biayaMarketing = $pengeluaranByCategory->get('MARKETING')->total ?? 0;
        $biayaPengembangan = $pengeluaranByCategory->get('PENGEMBANGAN')->total ?? 0;
        $gajiFreelance = $pengeluaranByCategory->get('GAJI_FREELANCE')->total ?? 0;
        $entertainment = $pengeluaranByCategory->get('ENTERTAINMENT')->total ?? 0;
        $pengeluaranLainLain = $pengeluaranByCategory->get('LAIN_LAIN')->total ?? 0;

        $totalPengeluaranOperasional = $biayaOperasional + $biayaMarketing + $biayaPengembangan +
            $gajiFreelance + $entertainment + $pengeluaranLainLain;

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
                'biaya_operasional' => $biayaOperasional,
                'biaya_marketing' => $biayaMarketing,
                'biaya_pengembangan' => $biayaPengembangan,
                'gaji_freelance' => $gajiFreelance,
                'entertainment' => $entertainment,
                'pengeluaran_lain_lain' => $pengeluaranLainLain,
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
}
