@extends('layouts.app')
@section('title', 'Dashboard Keuangan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="section-title">
                <i class="bi bi-graph-up-arrow"></i>Dashboard Keuangan - {{ now()->format('F Y') }}
            </h1>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-bank stat-icon"></i>
                    <div class="stat-value" data-metric="bank-balance">Rp {{ number_format($bankBalance, 0, ',', '.') }}</div>
                    <div class="stat-label">Saldo Bank Octo</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-coin stat-icon"></i>
                    <div class="stat-value">{{ number_format($goldPortfolio['summary']['total_grams'], 3) }} gram</div>
                    <div class="stat-label">Emas (Rp {{ number_format($goldPortfolio['summary']['current_value'], 0, ',', '.') }})</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-trophy stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($netWorth, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Net Worth</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Performance -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-graph-up stat-icon text-success"></i>
                    <div class="stat-value">Rp {{ number_format($monthlyProfit['result']['operational_profit'], 0, ',', '.') }}</div>
                    <div class="stat-label">Laba Operasional Bulan Ini</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-exclamation-triangle stat-icon text-warning"></i>
                    <div class="stat-value">Rp {{ number_format($untransferredAmount, 0, ',', '.') }}</div>
                    <div class="stat-label">Pembayaran Belum Transfer</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information Sections -->
    <div class="row">
        <!-- Monthly Expenses Breakdown -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-pie-chart"></i>Pengeluaran Bulan Ini
                    </h5>

                    @if ($monthlyExpensesByCategory->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach ($monthlyExpensesByCategory as $category => $amount)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $category }}</span>
                                    <span class="fw-bold text-danger">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total Pengeluaran:</strong>
                                <strong class="text-danger">Rp {{ number_format($monthlyExpensesByCategory->sum(), 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-wallet2 text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Belum ada pengeluaran bulan ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Gold Portfolio Summary -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-coin"></i>Portfolio Emas
                        </h5>
                    </div>

                    @if ($goldPortfolio['summary']['total_grams'] > 0)
                        <div class="row g-3 text-center">
                            <div class="col-12">
                                <div class="p-3 bg-warning bg-opacity-10 rounded mb-3">
                                    <h4 class="text-warning mb-1">{{ number_format($goldPortfolio['summary']['total_grams'], 3) }} gram</h4>
                                    <small class="text-muted">Total Kepemilikan Emas</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-success">Rp
                                        {{ number_format($goldPortfolio['summary']['average_buy_price'] ?? 0, 0, ',', '.') }}</div>
                                    <small class="text-muted">Rata-rata Harga Beli</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-primary">Rp {{ number_format($goldPortfolio['summary']['current_value'], 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">Nilai Investasi</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-coin text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Belum ada investasi emas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Project Status Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-list-task"></i>Status Proyek
                    </h5>
                    <div class="row g-3 text-center">
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <h3 class="text-warning mb-1">{{ $projectStats['waiting'] ?? 0 }}</h3>
                                <p class="mb-0 text-muted">Menunggu</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <h3 class="text-primary mb-1">{{ $projectStats['progress'] ?? 0 }}</h3>
                                <p class="mb-0 text-muted">Progress</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <h3 class="text-success mb-1">{{ $projectStats['finished'] ?? 0 }}</h3>
                                <p class="mb-0 text-muted">Selesai</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-danger bg-opacity-10 rounded">
                                <h3 class="text-danger mb-1">{{ $projectStats['cancelled'] ?? 0 }}</h3>
                                <p class="mb-0 text-muted">Dibatalkan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-currency-dollar"></i>Ringkasan Keuangan
                    </h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="text-muted">Total Nilai Proyek:</span>
                                <strong class="text-primary">Rp {{ number_format($financialSummary['total_project_value'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="text-muted">Total Pendapatan:</span>
                                <strong class="text-success">Rp {{ number_format($financialSummary['total_income'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="text-muted">Total Pengeluaran:</span>
                                <strong class="text-danger">Rp {{ number_format($financialSummary['total_expenses'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <span class="text-muted">Sisa Piutang:</span>
                                <strong class="text-warning">Rp {{ number_format($financialSummary['total_receivables'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Balance Details -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-bank2"></i>Detail Saldo Bank Octo
                    </h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="p-3 bg-primary bg-opacity-10 rounded text-center mb-3">
                                <h3 class="text-primary mb-1">Rp {{ number_format($bankBalance, 0, ',', '.') }}</h3>
                                <small class="text-muted">Saldo Saat Ini</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fw-bold text-success">Rp {{ number_format($bankMovement['income'] ?? 0, 0, ',', '.') }}</div>
                                <small class="text-muted">Transfer Masuk</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fw-bold text-danger">Rp {{ number_format($bankMovement['expenses'] ?? 0, 0, ',', '.') }}</div>
                                <small class="text-muted">Pengeluaran</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fw-bold text-warning">Rp {{ number_format($bankMovement['gold_investment'] ?? 0, 0, ',', '.') }}</div>
                                <small class="text-muted">Investasi Emas</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fw-bold text-info">{{ date('d M Y') }}</div>
                                <small class="text-muted">Update Terakhir</small>
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
        // Auto-refresh data setiap 5 menit untuk dashboard
        setInterval(function() {
            updateFinancialMetrics();
        }, 300000); // 5 minutes

        function updateFinancialMetrics() {
            fetch('/api/financial/reports/summary')
                .then(response => response.json())
                .then(data => {
                    // Update bank balance jika ada
                    const bankBalanceEl = document.querySelector('[data-metric="bank-balance"]');
                    if (bankBalanceEl && data.balance_sheet) {
                        bankBalanceEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(
                            data.balance_sheet.assets.bank_octo_balance
                        );
                    }
                })
                .catch(error => console.error('Failed to update metrics:', error));
        }

        // Show last update time
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard loaded at:', new Date().toLocaleString('id-ID'));
        });
    </script>
@endpush
