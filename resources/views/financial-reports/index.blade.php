@extends('layouts.app')
@section('title', 'Laporan Keuangan Komprehensif')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Keuangan Komprehensif
                    </h1>
                    <p class="text-muted mb-0">Analisis keuangan lengkap dan portfolio</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Current Bank Balance -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0 bg-gradient-primary text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-bank text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-white-50">Saldo Bank Octo Saat Ini</h6>
                            <h3 class="mb-0 fw-bold text-white">{{ $formattedBankBalance }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-calendar-range text-purple"></i>
                        </div>
                        Filter Periode Laporan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label fw-semibold">
                                <i class="bi bi-calendar3 text-purple me-2"></i>Tanggal Mulai
                            </label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label fw-semibold">
                                <i class="bi bi-calendar-check text-purple me-2"></i>Tanggal Akhir
                            </label>
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

    <!-- Tab Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-body p-0">
                    <ul class="nav nav-pills luxury-nav-pills" id="reportTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="laba-rugi-tab" data-bs-toggle="pill" data-bs-target="#laba-rugi" type="button"
                                role="tab">
                                <i class="bi bi-graph-up me-2"></i>Laba Rugi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="neraca-portfolio-tab" data-bs-toggle="pill" data-bs-target="#neraca-portfolio" type="button"
                                role="tab">
                                <i class="bi bi-balance-scale me-2"></i>Neraca & Portfolio
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="arus-kas-tab" data-bs-toggle="pill" data-bs-target="#arus-kas" type="button" role="tab">
                                <i class="bi bi-cash-stack me-2"></i>Arus Kas Bank
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="reportTabsContent">
        <!-- Tab 1: Laporan Laba Rugi -->
        <div class="tab-pane fade show active" id="laba-rugi" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card luxury-card border-0">
                        <div class="card-header bg-gradient-success text-white border-0 p-4">
                            <div class="d-flex align-items-center">
                                <div class="luxury-icon me-3 bg-white bg-opacity-25">
                                    <i class="bi bi-graph-up text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-white">Laporan Laba Rugi</h5>
                                    <p class="mb-0 text-white-50">{{ Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                                        {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- PENDAPATAN -->
                            <div class="mb-4">
                                <h5 class="text-success mb-3 d-flex align-items-center">
                                    <div class="luxury-icon me-3">
                                        <i class="bi bi-plus-circle text-success"></i>
                                    </div>
                                    PENDAPATAN (masuk ke Bank Octo)
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="ps-4">+ Transfer dari Pembayaran Client</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pendapatan']['transfer_dari_pembayaran'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">+ Hasil Penjualan Emas</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pendapatan']['hasil_penjualan_emas'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">+ Pendapatan Lain-lain</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pendapatan']['pendapatan_lain_lain'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="border-top border-success">
                                            <td class="fw-bold text-success fs-5">= TOTAL PENDAPATAN</td>
                                            <td class="text-end fw-bold text-success fs-4">Rp
                                                {{ number_format($laporanLabaRugi['pendapatan']['total_pendapatan_bank_octo'], 0, ',', '.') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- PENGELUARAN -->
                            <div class="mb-4">
                                <h5 class="text-danger mb-3 d-flex align-items-center">
                                    <div class="luxury-icon me-3">
                                        <i class="bi bi-dash-circle text-danger"></i>
                                    </div>
                                    PENGELUARAN OPERASIONAL
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="ps-4">- AI</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pengeluaran']['biaya_ai'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">- Admin Bank</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pengeluaran']['biaya_admin_bank'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">- Buku</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pengeluaran']['biaya_buku'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">- Domain/Hosting</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pengeluaran']['biaya_domain_hosting'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">- Entertain/Jajan/Traktir</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pengeluaran']['biaya_entertain'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">- Gaji/Bonus</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pengeluaran']['biaya_gaji_bonus'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">- Kopi/Susu</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pengeluaran']['biaya_kopi_susu'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">- Lainnya</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['pengeluaran']['biaya_lainnya'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="border-top border-danger">
                                            <td class="fw-bold text-danger fs-5">= TOTAL PENGELUARAN</td>
                                            <td class="text-end fw-bold text-danger fs-4">Rp
                                                {{ number_format($laporanLabaRugi['pengeluaran']['total_pengeluaran_operasional'], 0, ',', '.') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- INVESTASI -->
                            <div class="mb-4">
                                <h5 class="text-warning mb-3 d-flex align-items-center">
                                    <div class="luxury-icon me-3">
                                        <i class="bi bi-gem text-warning"></i>
                                    </div>
                                    INVESTASI
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="ps-4">- Pembelian Emas</td>
                                            <td class="text-end fw-bold">Rp
                                                {{ number_format($laporanLabaRugi['investasi']['pembelian_emas'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="border-top border-warning">
                                            <td class="fw-bold text-warning fs-5">= TOTAL PENGELUARAN + INVESTASI</td>
                                            <td class="text-end fw-bold text-warning fs-4">Rp
                                                {{ number_format($laporanLabaRugi['investasi']['total_pengeluaran_dan_investasi'], 0, ',', '.') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- HASIL -->
                            <div class="card luxury-card bg-light border-0">
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0">
                                            <tr>
                                                <td class="fw-bold fs-4">LABA/RUGI OPERASIONAL</td>
                                                <td
                                                    class="text-end fw-bold fs-3 {{ $laporanLabaRugi['hasil']['laba_rugi_operasional'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rp {{ number_format($laporanLabaRugi['hasil']['laba_rugi_operasional'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold fs-4">SALDO BANK OCTO AKHIR</td>
                                                <td class="text-end fw-bold fs-3 text-primary">
                                                    Rp {{ number_format($laporanLabaRugi['hasil']['saldo_bank_octo_akhir'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Neraca & Portfolio -->
        <div class="tab-pane fade" id="neraca-portfolio" role="tabpanel">
            <div class="row g-4">
                <!-- Neraca Sederhana -->
                <div class="col-12">
                    <div class="card luxury-card border-0">
                        <div class="card-header bg-gradient-info text-white border-0 p-4">
                            <div class="d-flex align-items-center">
                                <div class="luxury-icon me-3 bg-white bg-opacity-25">
                                    <i class="bi bi-balance-scale text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-white">Neraca Sederhana</h5>
                                    <p class="mb-0 text-white-50">Posisi keuangan saat ini</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <!-- ASET -->
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3 d-flex align-items-center">
                                        <div class="luxury-icon me-3">
                                            <i class="bi bi-building text-primary"></i>
                                        </div>
                                        ASET
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="ps-4">- Kas (Bank Octo)</td>
                                                <td class="text-end fw-bold">Rp
                                                    {{ number_format($neracaSederhana['aset']['kas_bank_octo'], 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4">- Investasi Emas<br>
                                                    <small
                                                        class="text-muted">({{ number_format($neracaSederhana['aset']['investasi_emas']['grams'], 3) }}
                                                        gram @ rata-rata harga beli)</small>
                                                </td>
                                                <td class="text-end fw-bold">Rp
                                                    {{ number_format($neracaSederhana['aset']['investasi_emas']['nilai'], 0, ',', '.') }}</td>
                                            </tr>
                                            <tr class="border-top border-primary">
                                                <td class="fw-bold text-primary fs-5">= TOTAL ASET</td>
                                                <td class="text-end fw-bold text-primary fs-4">Rp
                                                    {{ number_format($neracaSederhana['aset']['total_aset'], 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- PIUTANG -->
                                <div class="col-md-6">
                                    <h5 class="text-warning mb-3 d-flex align-items-center">
                                        <div class="luxury-icon me-3">
                                            <i class="bi bi-clock text-warning"></i>
                                        </div>
                                        PIUTANG
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="ps-4">- Pembayaran Belum Transfer</td>
                                                <td class="text-end fw-bold">Rp
                                                    {{ number_format($neracaSederhana['piutang']['pembayaran_belum_transfer'], 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4">- Sisa Tagihan Proyek</td>
                                                <td class="text-end fw-bold">Rp
                                                    {{ number_format($neracaSederhana['piutang']['sisa_tagihan_proyek'], 0, ',', '.') }}</td>
                                            </tr>
                                            <tr class="border-top border-warning">
                                                <td class="fw-bold text-warning fs-5">= TOTAL PIUTANG</td>
                                                <td class="text-end fw-bold text-warning fs-4">Rp
                                                    {{ number_format($neracaSederhana['piutang']['total_piutang'], 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- NET WORTH -->
                            <div class="card luxury-card bg-gradient-success text-white border-0 mt-4">
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <h3 class="fw-bold text-white mb-1">NET WORTH</h3>
                                        <h2 class="fw-bold text-white">Rp {{ number_format($neracaSederhana['net_worth'], 0, ',', '.') }}</h2>
                                        <p class="text-white-50 mb-0">Total Aset + Piutang</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Portfolio Emas -->
                <div class="col-12">
                    <div class="card luxury-card border-0">
                        <div class="card-header bg-gradient-warning text-white border-0 p-4">
                            <div class="d-flex align-items-center">
                                <div class="luxury-icon me-3 bg-white bg-opacity-25">
                                    <i class="bi bi-gem text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-white">Portfolio Emas</h5>
                                    <p class="mb-0 text-white-50">Investasi emas saat ini</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                        <h3 class="text-warning mb-1">{{ number_format($portfolioEmas['total_emas'], 3) }} gram</h3>
                                        <p class="mb-0 text-muted">Total Emas</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                        <h3 class="text-warning mb-1">Rp {{ number_format($portfolioEmas['rata_rata_harga_beli'], 0, ',', '.') }}</h3>
                                        <p class="mb-0 text-muted">Rata-rata Harga Beli/gram</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                        <h3 class="text-warning mb-1">Rp {{ number_format($portfolioEmas['total_investasi'], 0, ',', '.') }}</h3>
                                        <p class="mb-0 text-muted">Total Investasi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Arus Kas Bank Octo -->
        <div class="tab-pane fade" id="arus-kas" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card luxury-card border-0">
                        <div class="card-header bg-gradient-primary text-white border-0 p-4">
                            <div class="d-flex align-items-center">
                                <div class="luxury-icon me-3 bg-white bg-opacity-25">
                                    <i class="bi bi-cash-stack text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-white">Arus Kas Bank Octo</h5>
                                    <p class="mb-0 text-white-50">{{ Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                                        {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- Ringkasan Arus Kas -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="card luxury-card bg-light border-0">
                                        <div class="card-body text-center p-3">
                                            <h5 class="text-muted mb-1">Saldo Awal (Est.)</h5>
                                            <h4 class="text-primary mb-0">Rp
                                                {{ number_format($arusKasBank['ringkasan']['saldo_awal_estimasi'], 0, ',', '.') }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card luxury-card bg-light border-0">
                                        <div class="card-body text-center p-3">
                                            <h5 class="text-success mb-1">Total Masuk</h5>
                                            <h4 class="text-success mb-0">Rp
                                                {{ number_format($arusKasBank['ringkasan']['total_pemasukan'], 0, ',', '.') }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card luxury-card bg-light border-0">
                                        <div class="card-body text-center p-3">
                                            <h5 class="text-danger mb-1">Total Keluar</h5>
                                            <h4 class="text-danger mb-0">Rp
                                                {{ number_format($arusKasBank['ringkasan']['total_pengeluaran'], 0, ',', '.') }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card luxury-card bg-light border-0">
                                        <div class="card-body text-center p-3">
                                            <h5 class="text-primary mb-1">Saldo Akhir</h5>
                                            <h4 class="text-primary mb-0">Rp {{ number_format($arusKasBank['ringkasan']['saldo_akhir'], 0, ',', '.') }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- PEMASUKAN -->
                                <div class="col-md-6">
                                    <h5 class="text-success mb-3 d-flex align-items-center">
                                        <div class="luxury-icon me-3">
                                            <i class="bi bi-arrow-down-circle text-success"></i>
                                        </div>
                                        PEMASUKAN
                                    </h5>

                                    <!-- Transfer Masuk -->
                                    @if ($arusKasBank['pemasukan']['transfer_masuk']->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-success">Transfer dari Client</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Client</th>
                                                            <th class="text-end">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($arusKasBank['pemasukan']['transfer_masuk'] as $transfer)
                                                            <tr>
                                                                <td>{{ $transfer->transfer_date->format('d M') }}</td>
                                                                <td>{{ $transfer->payment->project->client->name }}</td>
                                                                <td class="text-end fw-bold text-success">{{ $transfer->formatted_transfer_amount }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Penjualan Emas -->
                                    @if ($arusKasBank['pemasukan']['penjualan_emas']->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-success">Penjualan Emas</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Gram</th>
                                                            <th class="text-end">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($arusKasBank['pemasukan']['penjualan_emas'] as $gold)
                                                            <tr>
                                                                <td>{{ $gold->transaction_date->format('d M') }}</td>
                                                                <td>{{ $gold->grams }} gram</td>
                                                                <td class="text-end fw-bold text-success">{{ $gold->formatted_total_price }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($arusKasBank['pemasukan']['transfer_masuk']->count() == 0 && $arusKasBank['pemasukan']['penjualan_emas']->count() == 0)
                                        <div class="text-center py-3">
                                            <i class="bi bi-info-circle text-muted"></i>
                                            <p class="text-muted mb-0">Tidak ada pemasukan dalam periode ini</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- PENGELUARAN -->
                                <div class="col-md-6">
                                    <h5 class="text-danger mb-3 d-flex align-items-center">
                                        <div class="luxury-icon me-3">
                                            <i class="bi bi-arrow-up-circle text-danger"></i>
                                        </div>
                                        PENGELUARAN
                                    </h5>

                                    <!-- Pengeluaran Operasional -->
                                    @if ($arusKasBank['pengeluaran']['pengeluaran_detail']->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-danger">Pengeluaran Operasional</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Kategori</th>
                                                            <th class="text-end">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($arusKasBank['pengeluaran']['pengeluaran_detail'] as $expense)
                                                            <tr>
                                                                <td>{{ $expense->expense_date->format('d M') }}</td>
                                                                <td>{{ $expense->category_label }}</td>
                                                                <td class="text-end fw-bold text-danger">{{ $expense->formatted_amount }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Pembelian Emas -->
                                    @if ($arusKasBank['pengeluaran']['pembelian_emas']->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-danger">Pembelian Emas</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Gram</th>
                                                            <th class="text-end">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($arusKasBank['pengeluaran']['pembelian_emas'] as $gold)
                                                            <tr>
                                                                <td>{{ $gold->transaction_date->format('d M') }}</td>
                                                                <td>{{ $gold->grams }} gram</td>
                                                                <td class="text-end fw-bold text-danger">{{ $gold->formatted_total_price }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($arusKasBank['pengeluaran']['pengeluaran_detail']->count() == 0 && $arusKasBank['pengeluaran']['pembelian_emas']->count() == 0)
                                        <div class="text-center py-3">
                                            <i class="bi bi-info-circle text-muted"></i>
                                            <p class="text-muted mb-0">Tidak ada pengeluaran dalam periode ini</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Net Cash Flow -->
                            <div
                                class="card luxury-card border-0 mt-4 {{ $arusKasBank['ringkasan']['net_cash_flow'] >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10">
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <h5 class="mb-1">NET CASH FLOW</h5>
                                        <h3 class="fw-bold {{ $arusKasBank['ringkasan']['net_cash_flow'] >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                            Rp {{ number_format($arusKasBank['ringkasan']['net_cash_flow'], 0, ',', '.') }}
                                        </h3>
                                        <p class="text-muted mb-0">Selisih pemasukan dan pengeluaran</p>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#8B5CF6',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Animation for cards
            const cards = document.querySelectorAll('.luxury-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Tab animation
            const tabButtons = document.querySelectorAll('[data-bs-toggle="pill"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function() {
                    const targetPane = document.querySelector(this.getAttribute('data-bs-target'));
                    const cards = targetPane.querySelectorAll('.luxury-card');

                    cards.forEach((card, index) => {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.transition = 'all 0.5s ease';
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, index * 100);
                    });
                });
            });
        });
    </script>

    <style>
        .luxury-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(139, 92, 246, 0.08);
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .luxury-card:hover {
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
            transform: translateY(-2px);
        }

        .luxury-icon {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.15));
            border-radius: 12px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10B981, #059669) !important;
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #06B6D4, #0891B2) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #F59E0B, #D97706) !important;
        }

        .luxury-nav-pills {
            padding: 1rem;
            background: rgba(139, 92, 246, 0.05);
            border-radius: 16px;
        }

        .luxury-nav-pills .nav-link {
            border-radius: 12px;
            color: #6B7280;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
        }

        .luxury-nav-pills .nav-link:hover {
            background: rgba(139, 92, 246, 0.1);
            color: #8B5CF6;
        }

        .luxury-nav-pills .nav-link.active {
            background: linear-gradient(135deg, #8B5CF6, #A855F7);
            color: white;
            box-shadow: 0 4px 16px rgba(139, 92, 246, 0.25);
        }

        .form-control:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        .table th {
            background-color: rgba(139, 92, 246, 0.05);
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .luxury-nav-pills {
                padding: 0.5rem;
            }

            .luxury-nav-pills .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
        }
    </style>
@endpush
