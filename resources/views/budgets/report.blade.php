@extends('layouts.app')
@section('title', 'Laporan Budget')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Budget {{ $selectedYear }}
                    </h1>
                    <p class="text-muted mb-0">Analisis dan ringkasan budget tahunan</p>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select" onchange="window.location.href='{{ route('budgets.report') }}/' + this.value">
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('budgets.index', ['year' => $selectedYear]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card luxury-card stat-card stat-card-purple h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="width: 40px; height: 40px;">
                        <i class="bi bi-calendar-check text-purple"></i>
                    </div>
                    <h4 class="fw-bold text-purple mb-1">{{ $stats['total_budgets'] }}</h4>
                    <small class="text-muted fw-semibold">Budget</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.1)">
                        <i class="bi bi-cash-stack text-success"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-1 fs-6">{{ number_format($stats['total_budget_year'], 0, ',', '.') }}</h5>
                    <small class="text-muted fw-semibold">Total Tahun</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card luxury-card stat-card stat-card-info h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="width: 40px; height: 40px; background: rgba(6, 182, 212, 0.1)">
                        <i class="bi bi-calculator text-info"></i>
                    </div>
                    <h5 class="fw-bold text-info mb-1 fs-6">{{ number_format($stats['avg_budget_month'], 0, ',', '.') }}</h5>
                    <small class="text-muted fw-semibold">Rata-rata/Bulan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="width: 40px; height: 40px; background: rgba(245, 158, 11, 0.1)">
                        <i class="bi bi-graph-up text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-warning mb-1 fs-6">{{ number_format($stats['highest_budget'], 0, ',', '.') }}</h5>
                    <small class="text-muted fw-semibold">Tertinggi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card luxury-card stat-card stat-card-danger h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="width: 40px; height: 40px; background: rgba(239, 68, 68, 0.1)">
                        <i class="bi bi-graph-down text-danger"></i>
                    </div>
                    <h5 class="fw-bold text-danger mb-1 fs-6">{{ number_format($stats['lowest_budget'], 0, ',', '.') }}</h5>
                    <small class="text-muted fw-semibold">Terendah</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card luxury-card stat-card stat-card-secondary h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="width: 40px; height: 40px; background: rgba(107, 114, 128, 0.1)">
                        <i class="bi bi-percent text-secondary"></i>
                    </div>
                    <h4 class="fw-bold text-secondary mb-1">{{ $stats['completion_rate'] }}%</h4>
                    <small class="text-muted fw-semibold">Progress</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Monthly Trend Chart -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-graph-up text-purple"></i>
                        </div>
                        Tren Budget Bulanan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="monthlyTrendChart" height="80"></canvas>
                </div>
            </div>

            <!-- Monthly Comparison - Total Tiap Bulan -->
            @if (count($monthlyComparison) > 0)
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-table text-purple"></i>
                            </div>
                            Data Perbandingan Antar Bulan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th class="text-end">Total Budget</th>
                                        <th class="text-center">Jumlah Item</th>
                                        <th class="text-center">Selesai</th>
                                        <th class="text-center">Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monthlyComparison as $comp)
                                        <tr>
                                            <td class="fw-semibold">{{ $comp['month_name'] }}</td>
                                            <td class="text-end fw-bold text-purple">
                                                Rp {{ number_format($comp['total'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">{{ $comp['items_count'] }}</td>
                                            <td class="text-center text-success">{{ $comp['completed_count'] }}</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 8px; max-width: 80px;">
                                                        <div class="progress-bar bg-{{ $comp['progress'] == 100 ? 'success' : ($comp['progress'] > 0 ? 'warning' : 'secondary') }}"
                                                            style="width: {{ $comp['progress'] }}%"></div>
                                                    </div>
                                                    <span class="badge bg-{{ $comp['progress'] == 100 ? 'success' : ($comp['progress'] > 0 ? 'warning' : 'secondary') }}">
                                                        {{ $comp['progress'] }}%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td>TOTAL</td>
                                        <td class="text-end text-purple">Rp {{ number_format($stats['total_budget_year'], 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $stats['total_items'] }}</td>
                                        <td class="text-center text-success">{{ $stats['completed_items'] }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $stats['completion_rate'] == 100 ? 'success' : ($stats['completion_rate'] > 0 ? 'warning' : 'secondary') }}">
                                                {{ $stats['completion_rate'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Pengeluaran Rutin Tahunan (dipindah ke sini) -->
            @if ($itemGroups->count() > 0)
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-arrow-repeat text-purple"></i>
                            </div>
                            Pengeluaran Rutin Tahunan
                        </h5>
                        <p class="text-muted small mb-0 mt-2">Item yang muncul di beberapa bulan sepanjang tahun {{ $selectedYear }}</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Item</th>
                                        <th class="text-center">Frekuensi</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Rata-rata</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($itemGroups->take(15) as $group)
                                        @php
                                            $completionRate = $group['count'] > 0 ? round(($group['completed'] / $group['count']) * 100) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $group['name'] }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-purple-soft text-purple">{{ $group['count'] }}x</span>
                                            </td>
                                            <td class="text-end fw-bold text-purple">
                                                Rp {{ number_format($group['total'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-end text-muted">
                                                Rp {{ number_format($group['avg'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($completionRate == 100)
                                                    <span class="badge bg-success">{{ $group['completed'] }}/{{ $group['count'] }}</span>
                                                @elseif ($completionRate > 0)
                                                    <span class="badge bg-warning">{{ $group['completed'] }}/{{ $group['count'] }}</span>
                                                @else
                                                    <span class="badge bg-secondary">0/{{ $group['count'] }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Category Breakdown -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-pie-chart text-purple"></i>
                        </div>
                        Kategori Pengeluaran
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($categorizedData->count() > 0)
                        <canvas id="categoryChart" height="200"></canvas>
                        <div class="mt-4">
                            @foreach ($categorizedData as $category => $data)
                                @php
                                    $percentage = $stats['total_budget_year'] > 0
                                        ? round(($data['total'] / $stats['total_budget_year']) * 100, 1)
                                        : 0;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <div class="fw-bold">{{ $category }}</div>
                                        <small class="text-muted">
                                            {{ $data['count'] }} item
                                            @if(isset($data['completed']) && $data['completed'] > 0)
                                                · {{ $data['completed'] }} selesai
                                            @endif
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-purple">Rp {{ number_format($data['total'], 0, ',', '.') }}</div>
                                        <small class="text-muted">{{ $percentage }}%</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-folder2-open text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data kategori</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Completion Progress -->
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-check2-circle text-purple"></i>
                        </div>
                        Progress Keseluruhan
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <svg width="150" height="150">
                            <circle cx="75" cy="75" r="65" fill="none" stroke="#E5E7EB" stroke-width="12" />
                            <circle cx="75" cy="75" r="65" fill="none" stroke="#8B5CF6" stroke-width="12"
                                stroke-dasharray="{{ 2 * 3.14159 * 65 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 65 * (1 - $stats['completion_rate'] / 100) }}" transform="rotate(-90 75 75)"
                                stroke-linecap="round" />
                        </svg>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <h2 class="fw-bold mb-0 text-purple">{{ $stats['completion_rate'] }}%</h2>
                            <small class="text-muted">Complete</small>
                        </div>
                    </div>
                    <div class="row g-3 text-start">
                        <div class="col-6">
                            <small class="text-muted d-block">Total Item</small>
                            <strong>{{ $stats['total_items'] }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-success d-block">Selesai</small>
                            <strong class="text-success">{{ $stats['completed_items'] }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-warning d-block">Belum</small>
                            <strong class="text-warning">{{ $stats['total_items'] - $stats['completed_items'] }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Rata-rata/Budget</small>
                            <strong>{{ round($stats['total_items'] / max($stats['total_budgets'], 1), 1) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Trend Chart
            const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
            const monthlyData = @json($monthlyData);

            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(d => d.month_name),
                    datasets: [{
                        label: 'Budget (Rp)',
                        data: monthlyData.map(d => d.total),
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                }
                            }
                        }
                    }
                }
            });

            // Category Chart
            const categoryChartEl = document.getElementById('categoryChart');
            if (categoryChartEl) {
                const categoryCtx = categoryChartEl.getContext('2d');
                const categorizedData = @json($categorizedData);

                const categoryLabels = Object.keys(categorizedData);
                const categoryValues = Object.values(categorizedData).map(d => d.total);
                const categoryColors = [
                    '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#06B6D4',
                    '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#84CC16'
                ];

                if (categoryLabels.length > 0) {
                    new Chart(categoryCtx, {
                        type: 'doughnut',
                        data: {
                            labels: categoryLabels,
                            datasets: [{
                                data: categoryValues,
                                backgroundColor: categoryColors.slice(0, categoryLabels.length),
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.parsed;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            return context.label + ': Rp ' + value.toLocaleString('id-ID') + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>

    <style>
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .stat-card-purple::before {
            background: linear-gradient(90deg, #8B5CF6, #A855F7);
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10B981, #059669);
        }

        .stat-card-info::before {
            background: linear-gradient(90deg, #06B6D4, #0891B2);
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #F59E0B, #D97706);
        }

        .stat-card-danger::before {
            background: linear-gradient(90deg, #EF4444, #DC2626);
        }

        .stat-card-secondary::before {
            background: linear-gradient(90deg, #6B7280, #4B5563);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
        }

        .luxury-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(139, 92, 246, 0.08);
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
            border-radius: 16px;
        }

        .luxury-icon {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.15));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .table tbody tr:hover {
            background-color: rgba(139, 92, 246, 0.03);
        }

        .bg-purple-soft {
            background-color: rgba(139, 92, 246, 0.15) !important;
        }

        .progress {
            border-radius: 10px;
            background-color: rgba(139, 92, 246, 0.1);
        }

        .progress-bar {
            border-radius: 10px;
        }
    </style>
@endpush
