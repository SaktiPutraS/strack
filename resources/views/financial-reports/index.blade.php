@extends('layouts.app')
@section('title', 'Laporan Keuangan Komprehensif')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-file-earmark-bar-graph"></i>Laporan Keuangan Komprehensif
                </h1>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Update Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- A. Laporan Laba Rugi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="section-title text-primary">
                        <i class="bi bi-graph-up"></i>A. Laporan Laba Rugi
                        <small class="text-muted">({{ Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                            {{ Carbon\Carbon::parse($endDate)->format('d M Y') }})</small>
                    </h4>

                    <!-- PENDAPATAN -->
                    <div class="mb-4">
                        <h5 class="text-success mb-3">
                            <i class="bi bi-plus-circle me-2"></i>PENDAPATAN (yang sudah masuk ke Bank Octo):
                        </h5>
                        <table class="table table-borderless">
                            <tr>
                                <td>+ Transfer dari Pembayaran Client</td>
                                <td class="text-end fw-bold">Rp
                                    {{ number_format($laporanLabaRugi['pendapatan']['transfer_dari_pembayaran'], 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>+ Hasil Penjualan Emas</td>
                                <td class="text-end fw-bold">Rp {{ number_format($laporanLabaRugi['pendapatan']['hasil_penjualan_emas'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>+ Pendapatan Lain-lain</td>
                                <td class="text-end fw-bold">Rp {{ number_format($laporanLabaRugi['pendapatan']['pendapatan_lain_lain'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="border-top border-success">
                                <td class="fw-bold text-success">= TOTAL PENDAPATAN BANK OCTO</td>
                                <td class="text-end fw-bold text-success fs-5">Rp
                                    {{ number_format($laporanLabaRugi['pendapatan']['total_pendapatan_bank_octo'], 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- PENGELUARAN -->
                    <div class="mb-4">
                        <h5 class="text-danger mb-3">
                            <i class="bi bi-dash-circle me-2"></i>PENGELUARAN:
                        </h5>
                        <table class="table table-borderless">
                            <tr>
                                <td>- Biaya Operasional</td>
                                <td class="text-end fw-bold">Rp {{ number_format($laporanLabaRugi['pengeluaran']['biaya_operasional'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>- Biaya Marketing</td>
                                <td class="text-end fw-bold">Rp {{ number_format($laporanLabaRugi['pengeluaran']['biaya_marketing'], 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>- Biaya Pengembangan</td>
                                <td class="text-end fw-bold">Rp {{ number_format($laporanLabaRugi['pengeluaran']['biaya_pengembangan'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>- Gaji & Freelance</td>
                                <td class="text-end fw-bold">Rp {{ number_format($laporanLabaRugi['pengeluaran']['gaji_freelance'], 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>- Entertainment</td>
                                <td class="text-end fw-bold">Rp {{ number_format($laporanLabaRugi['pengeluaran']['entertainment'], 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>- Pengeluaran Lain-lain</td>
                                <td class="text-end fw-bold">Rp
                                    {{ number_format($laporanLabaRugi['pengeluaran']['pengeluaran_lain_lain'], 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-top border-danger">
                                <td class="fw-bold text-danger">= TOTAL PENGELUARAN OPERASIONAL</td>
                                <td class="text-end fw-bold text-danger fs-5">Rp
                                    {{ number_format($laporanLabaRugi['pengeluaran']['total_pengeluaran_operasional'], 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- INVESTASI -->
                    <div class="mb-4">
                        <h5 class="text-warning mb-3">
                            <i class="bi bi-gem me-2"></i>INVESTASI:
                        </h5>
                        <table class="table table-borderless">
                            <tr>
                                <td>- Pembelian Emas</td>
                                <td class="text-end fw-bold">Rp {{ number_format($laporanLabaRugi['investasi']['pembelian_emas'], 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-top border-warning">
                                <td class="fw-bold text-warning">= TOTAL PENGELUARAN + INVESTASI</td>
                                <td class="text-end fw-bold text-warning fs-5">Rp
                                    {{ number_format($laporanLabaRugi['investasi']['total_pengeluaran_dan_investasi'], 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- HASIL -->
                    <div class="p-3 bg-light rounded">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="fw-bold fs-5">LABA/RUGI OPERASIONAL</td>
                                <td
                                    class="text-end fw-bold fs-4 {{ $laporanLabaRugi['hasil']['laba_rugi_operasional'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    Rp {{ number_format($laporanLabaRugi['hasil']['laba_rugi_operasional'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold fs-5">SALDO BANK OCTO AKHIR PERIODE</td>
                                <td class="text-end fw-bold fs-4 text-primary">Rp
                                    {{ number_format($laporanLabaRugi['hasil']['saldo_bank_octo_akhir'], 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- B. Neraca Sederhana -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="section-title text-info">
                        <i class="bi bi-balance-scale"></i>B. Neraca Sederhana
                    </h4>

                    <div class="row">
                        <!-- ASET -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-building me-2"></i>ASET:
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td>- Kas (Saldo Bank Octo)</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($neracaSederhana['aset']['kas_bank_octo'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>- Investasi Emas ({{ number_format($neracaSederhana['aset']['investasi_emas']['grams'], 3) }} gram @ rata² harga
                                        beli)</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($neracaSederhana['aset']['investasi_emas']['nilai'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-top border-primary">
                                    <td class="fw-bold text-primary">= TOTAL ASET</td>
                                    <td class="text-end fw-bold text-primary fs-5">Rp
                                        {{ number_format($neracaSederhana['aset']['total_aset'], 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- PIUTANG -->
                        <div class="col-md-6">
                            <h5 class="text-warning mb-3">
                                <i class="bi bi-clock me-2"></i>PIUTANG:
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td>- Pembayaran Client Belum Transfer</td>
                                    <td class="text-end fw-bold">Rp
                                        {{ number_format($neracaSederhana['piutang']['pembayaran_belum_transfer'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>- Sisa Tagihan Proyek</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($neracaSederhana['piutang']['sisa_tagihan_proyek'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-top border-warning">
                                    <td class="fw-bold text-warning">= TOTAL PIUTANG</td>
                                    <td class="text-end fw-bold text-warning fs-5">Rp
                                        {{ number_format($neracaSederhana['piutang']['total_piutang'], 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- NET WORTH -->
                    <div class="mt-4 p-3 bg-success bg-opacity-10 rounded">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td class="fw-bold fs-4 text-success">NET WORTH (Aset + Piutang)</td>
                                        <td class="text-end fw-bold fs-3 text-success">Rp
                                            {{ number_format($neracaSederhana['net_worth'], 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- C. Portfolio Emas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-body">
                    <h4 class="section-title text-warning">
                        <i class="bi bi-gem"></i>C. Portfolio Emas
                    </h4>

                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 bg-warning bg-opacity-10 rounded mb-3">
                                <h3 class="text-warning mb-1">{{ number_format($portfolioEmas['total_emas'], 3) }} gram</h3>
                                <p class="mb-0 text-muted">Total Emas</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-warning bg-opacity-10 rounded mb-3">
                                <h3 class="text-warning mb-1">Rp {{ number_format($portfolioEmas['rata_rata_harga_beli'], 0, ',', '.') }}</h3>
                                <p class="mb-0 text-muted">Rata² Harga Beli /gram</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-warning bg-opacity-10 rounded mb-3">
                                <h3 class="text-warning mb-1">Rp {{ number_format($portfolioEmas['total_investasi'], 0, ',', '.') }}</h3>
                                <p class="mb-0 text-muted">Total Investasi</p>
                            </div>
                        </div>
                    </div>

                    <!-- Portfolio Summary -->
                    <div class="row">
                        <div class="col-12">
                            <div class="p-4 bg-gradient-warning text-white rounded text-center">
                                <h5 class="mb-2">🥇 PORTFOLIO EMAS:</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Total Emas:</strong> {{ number_format($portfolioEmas['total_emas'], 1) }} gram
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Rata² Harga Beli:</strong> Rp
                                        {{ number_format($portfolioEmas['rata_rata_harga_beli'], 0, ',', '.') }}/gram
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total Investasi:</strong> Rp {{ number_format($portfolioEmas['total_investasi'], 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
    </style>
@endpush
