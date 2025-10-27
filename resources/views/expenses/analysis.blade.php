@extends('layouts.app')
@section('title', 'Analisis Pengeluaran')

@section('content')
    <div class="page-header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h1 class="page-title mb-2">
                    <i class="bi bi-graph-up-arrow me-2 text-purple"></i>Analisis Pengeluaran
                </h1>
                <p class="text-muted mb-0">Rekap dan tren biaya per kategori</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card luxury-card border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Tahun</label>
                        <select name="year" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            @foreach ($availableYears as $y)
                                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Kategori Biaya</label>
                        <select name="category" class="form-select" id="categorySelect">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $key => $label)
                                @if ($key != 'SALDO_AWAL')
                                    <option value="{{ $key }}" {{ $selectedCategory == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-semibold">Dari Tanggal</label>
                        <input type="date" name="period_start" class="form-control" value="{{ $periodStart }}">
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="period_end" class="form-control" value="{{ $periodEnd }}">
                    </div>
                    <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ route('expenses.analysis') }}?year={{ $year }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2 bg-purple-light">
                        <i class="bi bi-currency-dollar text-purple fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Total {{ $year }}</h6>
                    <h5 class="fw-bold text-purple mb-0">{{ number_format($stats['total_year'], 0, ',', '.') }}</h5>
                    @if ($selectedCategory)
                        <small class="text-muted">{{ $categories[$selectedCategory] ?? $selectedCategory }}</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="background: rgba(59, 130, 246, 0.1) !important;">
                        <i class="bi bi-calendar-month text-primary fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Rata-rata/Bulan</h6>
                    <h5 class="fw-bold text-primary mb-0">{{ number_format($stats['avg_monthly'], 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="background: rgba(16, 185, 129, 0.1) !important;">
                        <i class="bi bi-arrow-up-circle text-success fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Bulan Tertinggi</h6>
                    <h6 class="fw-bold text-success mb-0">{{ $stats['highest_month']['month_name'] ?? '-' }}</h6>
                    <small class="text-muted">{{ number_format($stats['highest_month']['total'] ?? 0, 0, ',', '.') }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="background: rgba(6, 182, 212, 0.1) !important;">
                        <i class="bi bi-arrow-down-circle text-info fs-4"></i>
                    </div>
                    <h6 class="text-muted fw-semibold mb-1">Bulan Terendah</h6>
                    <h6 class="fw-bold text-info mb-0">{{ $stats['lowest_month']['month_name'] ?? '-' }}</h6>
                    <small class="text-muted">{{ number_format($stats['lowest_month']['total'] ?? 0, 0, ',', '.') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-8">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-3 p-md-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-graph-up me-2 text-purple"></i>Pergerakan Biaya Bulanan {{ $year }}
                        @if ($selectedCategory)
                            <span class="badge bg-purple ms-2">{{ $categories[$selectedCategory] }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-3 p-md-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-list-ol me-2 text-purple"></i>Top 10 Kategori</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="categories-list">
                        @foreach ($topCategories as $i => $cat)
                            <a href="{{ route('expenses.analysis') }}?year={{ $year }}&category={{ $cat['category'] }}"
                                class="d-flex justify-content-between py-2 border-bottom text-decoration-none {{ $selectedCategory == $cat['category'] ? 'bg-purple-light' : '' }}"
                                style="border-radius: 8px; padding-left: 8px; padding-right: 8px;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-purple rounded-circle"
                                        style="width:24px;height:24px;display:flex;align-items:center;justify-content:center;">{{ $i + 1 }}</span>
                                    <span class="fw-semibold text-dark">{{ $cat['label'] }}</span>
                                </div>
                                <span class="text-purple fw-bold">{{ number_format($cat['total'], 0, ',', '.') }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Chart -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-3 p-md-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-bar-chart me-2 text-purple"></i>Perbandingan {{ $year }} vs {{ $compareYear }}
                        @if ($selectedCategory)
                            <span class="badge bg-purple ms-2">{{ $categories[$selectedCategory] }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <canvas id="comparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Transactions -->
    @if ($detailTransactions && $detailTransactions->count() > 0)
        <div class="row g-3">
            <div class="col-12">
                <div class="card luxury-card border-0">
                    <div class="card-header bg-white border-0 p-3 p-md-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-receipt me-2 text-purple"></i>Detail Transaksi: {{ $categories[$selectedCategory] }}
                            <span class="text-muted fw-normal ms-2">({{ \Carbon\Carbon::parse($periodStart)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($periodEnd)->format('d/m/Y') }})</span>
                        </h5>
                        <p class="text-muted mb-0 mt-2">Total: {{ $detailTransactions->count() }} transaksi • Rp
                            {{ number_format($detailTransactions->sum('amount'), 0, ',', '.') }}</p>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Deskripsi</th>
                                        <th>Sumber</th>
                                        <th class="text-end">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detailTransactions as $index => $expense)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                                            <td>{{ $expense->description }}</td>
                                            <td>
                                                <span class="badge bg-{{ $expense->source_color }}">
                                                    <i class="bi bi-{{ $expense->source_icon }} me-1"></i>{{ $expense->source_label }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold text-danger">{{ number_format($expense->amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        window.addEventListener('load', function() {
            const monthlyData = @json($monthlyExpenses);
            const comparisonData = @json($comparisonData);

            // Trend Chart dengan indikator naik/turun
            const trendCtx = document.getElementById('trendChart');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(d => d.month_name),
                    datasets: [{
                        label: 'Pengeluaran',
                        data: monthlyData.map(d => d.total),
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointBackgroundColor: monthlyData.map((d, i) => {
                            if (i === 0) return '#8B5CF6';
                            return monthlyData[i].total > monthlyData[i - 1].total ? '#EF4444' : '#10B981';
                        }),
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2.5,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y;
                                    const index = context.dataIndex;
                                    let trend = '';
                                    if (index > 0) {
                                        const prev = monthlyData[index - 1].total;
                                        const diff = value - prev;
                                        const percent = prev > 0 ? ((diff / prev) * 100).toFixed(1) : 0;
                                        trend = diff >= 0 ? ` ↑ ${percent}%` : ` ↓ ${Math.abs(percent)}%`;
                                    }
                                    return 'Rp ' + value.toLocaleString('id-ID') + trend;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (val) => val >= 1000000 ? 'Rp ' + (val / 1000000).toFixed(0) + 'Jt' : 'Rp ' + val
                                    .toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });

            // Comparison Chart
            new Chart(document.getElementById('comparisonChart'), {
                type: 'bar',
                data: {
                    labels: comparisonData.map(d => d.month_name),
                    datasets: [{
                            label: '{{ $year }}',
                            data: comparisonData.map(d => d.this_year),
                            backgroundColor: 'rgba(139, 92, 246, 0.8)'
                        },
                        {
                            label: '{{ $compareYear }}',
                            data: comparisonData.map(d => d.last_year),
                            backgroundColor: 'rgba(156, 163, 175, 0.6)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 3,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                footer: function(items) {
                                    if (items.length > 0) {
                                        const index = items[0].dataIndex;
                                        const diff = comparisonData[index].difference;
                                        const percent = comparisonData[index].percentage.toFixed(1);
                                        const symbol = diff >= 0 ? '↑' : '↓';
                                        return `${symbol} Rp ${Math.abs(diff).toLocaleString('id-ID')} (${Math.abs(percent)}%)`;
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (val) => val >= 1000000 ? 'Rp ' + (val / 1000000).toFixed(0) + 'Jt' : 'Rp ' + val
                                    .toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });
        });
    </script>

    <style>
        .bg-purple {
            background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
        }

        .bg-purple-light {
            background: rgba(139, 92, 246, 0.1) !important;
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .luxury-icon {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.15));
            border-radius: 12px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .categories-list {
            max-height: 450px;
            overflow-y: auto;
        }

        .categories-list a:hover {
            background: rgba(139, 92, 246, 0.05) !important;
        }

        @media (max-width: 768px) {
            .luxury-icon {
                width: 40px;
                height: 40px;
            }

            .table {
                font-size: 0.85rem;
            }
        }
    </style>
@endpush
