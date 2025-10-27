@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-house-door me-2"></i>Dashboard
                    </h1>
                    @if (!$isMobile)
                        <p class="text-muted mb-0">{{ now()->format('F Y') }} • Selamat datang kembali</p>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Proyek Baru
                    </a>
                    <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-chart-bar me-1"></i>Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Status & Cash Balance Cards -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-kanban me-2 text-purple"></i>Status Proyek & Saldo Kas
            </h5>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-warning h-100 clickable-card" data-filter="status=WAITING">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $proyekMenunggu }}</h3>
                    <small class="text-muted fw-semibold">Menunggu</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-purple h-100 clickable-card" data-filter="status=PROGRESS">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-play-circle-fill text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $proyekProgress }}</h3>
                    <small class="text-muted fw-semibold">Progress</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-danger h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-bank text-danger fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-danger mb-1 fs-6">{{ number_format($saldoBank, 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Bank Octo</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-cash-coin text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1 fs-6">{{ number_format($saldoCash, 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Cash</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Statistik Keuangan
            </h5>
        </div>

        <!-- Asset Overview Card -->
        <div class="col-12 col-lg-6">
            <div class="card luxury-card border-0 h-100">
                <div class="card-header bg-white border-0 p-3 p-md-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-wallet2 me-2 text-purple"></i>Total Asset
                    </h5>
                    <h3 class="mb-0 text-purple fw-bold">{{ number_format($pieData['total'], 0, ',', '.') }}</h3>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-2 g-md-3">
                        <!-- Bank Octo -->
                        <div class="col-6">
                            <div class="asset-detail-card p-2 p-md-3 border rounded-3 h-100"
                                style="background: linear-gradient(135deg, rgba(220, 38, 38, 0.05), rgba(220, 38, 38, 0.1)); border-color: rgba(220, 38, 38, 0.2) !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="asset-icon rounded-circle p-1 p-md-2" style="background: rgba(220, 38, 38, 0.15);">
                                        <i class="bi bi-bank text-danger"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-1 asset-label">Bank Octo</p>
                                <h6 class="fw-bold mb-1 text-danger asset-value">{{ number_format($saldoBank, 0, ',', '.') }}</h6>
                                <small class="text-muted asset-percent">{{ round(($saldoBank / $pieData['total']) * 100, 1) }}%</small>
                            </div>
                        </div>

                        <!-- Cash -->
                        <div class="col-6">
                            <div class="asset-detail-card p-2 p-md-3 border rounded-3 h-100"
                                style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.1)); border-color: rgba(16, 185, 129, 0.2) !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="asset-icon rounded-circle p-1 p-md-2" style="background: rgba(16, 185, 129, 0.15);">
                                        <i class="bi bi-cash-coin text-success"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-1 asset-label">Cash</p>
                                <h6 class="fw-bold mb-1 text-success asset-value">{{ number_format($saldoCash, 0, ',', '.') }}</h6>
                                <small class="text-muted asset-percent">{{ round(($saldoCash / $pieData['total']) * 100, 1) }}%</small>
                            </div>
                        </div>

                        <!-- Piutang -->
                        <div class="col-6">
                            <div class="asset-detail-card p-2 p-md-3 border rounded-3 h-100"
                                style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), rgba(139, 92, 246, 0.1)); border-color: rgba(139, 92, 246, 0.2) !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="asset-icon rounded-circle p-1 p-md-2" style="background: rgba(139, 92, 246, 0.15);">
                                        <i class="bi bi-receipt text-purple"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-1 asset-label">Piutang</p>
                                <h6 class="fw-bold mb-1 text-purple asset-value">{{ number_format($totalPiutang, 0, ',', '.') }}</h6>
                                <small class="text-muted asset-percent">{{ round(($totalPiutang / $pieData['total']) * 100, 1) }}%</small>
                            </div>
                        </div>

                        <!-- Emas -->
                        <div class="col-6">
                            <div class="asset-detail-card p-2 p-md-3 border rounded-3 h-100"
                                style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(245, 158, 11, 0.1)); border-color: rgba(245, 158, 11, 0.2) !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="asset-icon rounded-circle p-1 p-md-2" style="background: rgba(245, 158, 11, 0.15);">
                                        <i class="bi bi-gem text-warning"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-1 asset-label">Emas</p>
                                <h6 class="fw-bold mb-1 text-warning asset-value">{{ number_format($saldoEmas, 0, ',', '.') }}</h6>
                                <small class="text-muted asset-percent">{{ round(($saldoEmas / $pieData['total']) * 100, 1) }}%</small>
                            </div>
                        </div>
                    </div>

                    <!-- Pie Chart (Hidden on mobile) -->
                    <div class="mt-3 mt-md-4 pt-3 border-top d-none d-lg-block">
                        <canvas id="pieChart" height="160"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="col-12 col-lg-6">
            <div class="card luxury-card border-0 h-100">
                <div class="card-header bg-white border-0 p-3 p-md-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-graph-up-arrow me-2 text-purple"></i>Pendapatan per Bulan
                    </h5>
                    <p class="text-muted mb-0">Total nilai proyek tahun {{ now()->year }}</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <canvas id="monthlyRevenueChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Income vs Expense Chart -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-3 p-md-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-graph-up me-2 text-purple"></i>Pendapatan & Pengeluaran Mingguan
                    </h5>
                    <p class="text-muted mb-0">Akumulasi per minggu tahun ini (dimulai Juli 2025)</p>
                </div>
                <div class="card-body p-3 p-md-4">
                    <canvas id="lineChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Notes & Project Deadlines Section -->
    <div class="row">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-3 p-md-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                        <div>
                            <h4 class="fw-bold mb-1">
                                <i class="bi bi-calendar3 text-purple me-2"></i>Kalender & Deadline Proyek
                            </h4>
                            <p class="text-muted mb-0">Catatan pribadi dan deadline proyek dalam satu tampilan</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <button class="btn btn-sm btn-outline-primary" id="prevMonth">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <h5 class="mb-0 fw-bold text-purple" id="calendarTitle">
                                {{ $calendarData['currentMonth'] }}
                            </h5>
                            <button class="btn btn-sm btn-outline-primary" id="nextMonth">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Legend -->
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <div class="d-flex align-items-center">
                            <div class="legend-dot bg-success me-2"></div>
                            <small class="text-muted">Catatan Pribadi</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-dot bg-primary me-2"></div>
                            <small class="text-muted">Deadline Normal</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-dot bg-warning me-2"></div>
                            <small class="text-muted">Deadline Mendekat</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-dot bg-danger me-2"></div>
                            <small class="text-muted">Deadline Terlewat</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="calendar-wrapper">
                        <div class="calendar-container">
                            <div class="calendar-header">
                                <div class="calendar-day-header">Min</div>
                                <div class="calendar-day-header">Sen</div>
                                <div class="calendar-day-header">Sel</div>
                                <div class="calendar-day-header">Rab</div>
                                <div class="calendar-day-header">Kam</div>
                                <div class="calendar-day-header">Jum</div>
                                <div class="calendar-day-header">Sab</div>
                            </div>
                            <div class="calendar-body" id="calendarBody">
                                <!-- Calendar days will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Modal for Notes and Project Details -->
    <div class="modal fade" id="calendarModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content luxury-card border-0">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-purple" id="modalTitle">
                        <i class="bi bi-calendar3 me-2"></i><span id="modalDate"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-pills mb-4" id="modalTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="notes-tab" data-bs-toggle="pill" data-bs-target="#notes-content" type="button"
                                role="tab">
                                <i class="bi bi-journal-text me-1"></i>Catatan Pribadi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="projects-tab" data-bs-toggle="pill" data-bs-target="#projects-content" type="button"
                                role="tab">
                                <i class="bi bi-folder me-1"></i>Deadline Proyek
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="modalTabContent">
                        <!-- Notes Tab -->
                        <div class="tab-pane fade show active" id="notes-content" role="tabpanel">
                            <div id="notesList">
                                <!-- Notes will be loaded here -->
                            </div>

                            <!-- Add/Edit Note Form -->
                            <div class="card border-0 bg-light mt-3">
                                <div class="card-body">
                                    <form id="noteForm">
                                        <input type="hidden" id="noteId" name="noteId">
                                        <input type="hidden" id="noteDate" name="date">
                                        <div class="mb-3">
                                            <label for="noteTitle" class="form-label fw-semibold">Judul Catatan</label>
                                            <input type="text" class="form-control" id="noteTitle" name="title" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="noteDescription" class="form-label fw-semibold">Deskripsi</label>
                                            <textarea class="form-control" id="noteDescription" name="description" rows="3"></textarea>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save me-1"></i><span id="submitBtnText">Simpan Catatan</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="cancelEditBtn" style="display: none;">
                                                <i class="bi bi-x-circle me-1"></i>Batal Edit
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Projects Tab -->
                        <div class="tab-pane fade" id="projects-content" role="tabpanel">
                            <div id="projectsList">
                                <!-- Projects will be loaded here -->
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
            // ===== Pie Chart - Komposisi Asset =====
            const pieCtx = document.getElementById('pieChart')?.getContext('2d');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($pieData['labels']) !!},
                        datasets: [{
                            data: {!! json_encode($pieData['data']) !!},
                            backgroundColor: {!! json_encode($pieData['colors']) !!},
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 11,
                                        family: '-apple-system, BlinkMacSystemFont, "SF Pro Display", sans-serif'
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    color: '#6B7280'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                cornerRadius: 8,
                                displayColors: true
                            }
                        }
                    }
                });
            }

            // ===== Monthly Revenue Chart =====
            const monthlyRevenueData = @json($monthlyRevenueData);
            const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart')?.getContext('2d');

            if (monthlyRevenueCtx) {
                const gradient = monthlyRevenueCtx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(139, 92, 246, 0.4)');
                gradient.addColorStop(1, 'rgba(139, 92, 246, 0.05)');

                new Chart(monthlyRevenueCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyRevenueData.map(d => d.month),
                        datasets: [{
                            label: 'Nilai Proyek',
                            data: monthlyRevenueData.map(d => d.project_value),
                            borderColor: '#8B5CF6',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#8B5CF6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                cornerRadius: 8,
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
                                grid: {
                                    color: 'rgba(139, 92, 246, 0.08)',
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                        }
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    },
                                    color: '#6B7280',
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6B7280',
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ===== Line Chart - Weekly Income vs Expense =====
            const weeklyData = @json($weeklyData);
            const lineCtx = document.getElementById('lineChart')?.getContext('2d');

            if (lineCtx) {
                const incomeGradient = lineCtx.createLinearGradient(0, 0, 0, 300);
                incomeGradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
                incomeGradient.addColorStop(1, 'rgba(16, 185, 129, 0.05)');

                const expenseGradient = lineCtx.createLinearGradient(0, 0, 0, 300);
                expenseGradient.addColorStop(0, 'rgba(239, 68, 68, 0.3)');
                expenseGradient.addColorStop(1, 'rgba(239, 68, 68, 0.05)');

                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: weeklyData.map(d => d.week),
                        datasets: [{
                                label: 'Pendapatan',
                                data: weeklyData.map(d => d.income),
                                borderColor: '#10B981',
                                backgroundColor: incomeGradient,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#10B981',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            },
                            {
                                label: 'Pengeluaran',
                                data: weeklyData.map(d => d.expense),
                                borderColor: '#EF4444',
                                backgroundColor: expenseGradient,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#EF4444',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        family: '-apple-system, BlinkMacSystemFont, "SF Pro Display", sans-serif'
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    color: '#6B7280'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' + context.parsed.y
                                            .toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(139, 92, 246, 0.08)',
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                        }
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    },
                                    color: '#6B7280',
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6B7280',
                                    font: {
                                        size: 10
                                    },
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            }

            // Calendar state
            let currentDate = new Date({{ $calendarData['currentYear'] }}, {{ $calendarData['currentMonthNumber'] }} - 1, 1);

            // Parse calendar data from Laravel
            let calendarNotesRaw = @json($calendarNotes);
            let projectDeadlinesRaw = @json($projectDeadlines);

            // Convert to proper format
            let calendarNotes = [];
            if (calendarNotesRaw) {
                if (Array.isArray(calendarNotesRaw)) {
                    calendarNotes = calendarNotesRaw;
                } else if (typeof calendarNotesRaw === 'object') {
                    calendarNotes = Object.values(calendarNotesRaw);
                }
            }

            let projectDeadlines = {};
            if (projectDeadlinesRaw && typeof projectDeadlinesRaw === 'object') {
                projectDeadlines = projectDeadlinesRaw;
            }

            const calendarBody = document.getElementById('calendarBody');
            const calendarTitle = document.getElementById('calendarTitle');
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');
            const calendarModal = new bootstrap.Modal(document.getElementById('calendarModal'));

            // Month names in Indonesian
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            // Render calendar function with project deadline handling
            function renderCalendar() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();

                calendarTitle.textContent = `${monthNames[month]} ${year}`;

                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const startingDayOfWeek = firstDay.getDay();
                const monthLength = lastDay.getDate();

                calendarBody.innerHTML = '';

                // Add empty cells for days before month starts
                for (let i = 0; i < startingDayOfWeek; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'calendar-day other-month';
                    calendarBody.appendChild(emptyDay);
                }

                // Add days of month
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                for (let day = 1; day <= monthLength; day++) {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';

                    const currentDay = new Date(year, month, day);
                    currentDay.setHours(0, 0, 0, 0);

                    if (currentDay.getTime() === today.getTime()) {
                        dayElement.classList.add('today');
                    }

                    // Check for notes
                    const dayNotes = calendarNotes.filter(note => {
                        const noteDate = new Date(note.date);
                        noteDate.setHours(0, 0, 0, 0);
                        return noteDate.getTime() === currentDay.getTime();
                    });

                    // Check for project deadlines
                    const dayProjects = projectDeadlines[day] || [];

                    // Determine if there are overdue projects
                    const hasOverdueProjects = dayProjects.some(project => {
                        const deadline = new Date(project.deadline);
                        deadline.setHours(0, 0, 0, 0);
                        return deadline.getTime() < today.getTime();
                    });

                    // Determine if there are upcoming projects (within 3 days)
                    const hasUpcomingProjects = dayProjects.some(project => {
                        const deadline = new Date(project.deadline);
                        deadline.setHours(0, 0, 0, 0);
                        const daysUntil = Math.ceil((deadline.getTime() - today.getTime()) / (1000 * 60 *
                            60 * 24));
                        return daysUntil >= 0 && daysUntil <= 3;
                    });

                    const hasNotes = dayNotes.length > 0;
                    const hasProjects = dayProjects.length > 0;

                    if (hasNotes || hasProjects) {
                        dayElement.classList.add('has-event');
                    }

                    dayElement.innerHTML = `
                <span class="day-number">${day}</span>
                ${hasNotes ? `<div class="note-preview bg-success"><i class="bi bi-journal-text"></i> ${dayNotes.length}</div>` : ''}
                ${hasOverdueProjects ? `<div class="deadline-preview bg-danger"><i class="bi bi-exclamation-triangle"></i> ${dayProjects.length}</div>` : ''}
                ${!hasOverdueProjects && hasUpcomingProjects ? `<div class="deadline-preview bg-warning"><i class="bi bi-calendar-event"></i> ${dayProjects.length}</div>` : ''}
                ${!hasOverdueProjects && !hasUpcomingProjects && hasProjects ? `<div class="deadline-preview bg-primary"><i class="bi bi-calendar-event"></i> ${dayProjects.length}</div>` : ''}
            `;

                    dayElement.addEventListener('click', () => openDayModal(year, month, day));
                    calendarBody.appendChild(dayElement);
                }
            }

            // Open day modal function with project handling
            function openDayModal(year, month, day) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const displayDate = `${day} ${monthNames[month]} ${year}`;

                document.getElementById('modalDate').textContent = displayDate;
                document.getElementById('noteDate').value = dateStr;

                // Filter notes and projects for this day
                const dayNotes = calendarNotes.filter(note => note.date === dateStr);
                const dayProjects = projectDeadlines[day] || [];

                // Populate notes list
                const notesList = document.getElementById('notesList');
                if (dayNotes.length === 0) {
                    notesList.innerHTML =
                        '<p class="text-muted text-center py-3"><i class="bi bi-journal-x me-2"></i>Belum ada catatan</p>';
                } else {
                    notesList.innerHTML = dayNotes.map(note => `
                <div class="card mb-2 border-0 bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">${note.title}</h6>
                                ${note.description ? `<p class="text-muted mb-0 small">${note.description}</p>` : ''}
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm" onclick="editNote(${note.id}, '${note.title}', '${note.description || ''}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteNote(${note.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
                }

                // Populate projects list
                const projectsList = document.getElementById('projectsList');
                if (dayProjects.length === 0) {
                    projectsList.innerHTML =
                        '<p class="text-muted text-center py-3"><i class="bi bi-folder-x me-2"></i>Tidak ada deadline proyek</p>';
                } else {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    projectsList.innerHTML = dayProjects.map(project => {
                        const deadline = new Date(project.deadline);
                        deadline.setHours(0, 0, 0, 0);
                        const daysUntil = Math.ceil((deadline.getTime() - today.getTime()) / (1000 * 60 *
                            60 * 24));

                        let badgeClass = 'bg-primary';
                        let badgeText = 'Normal';
                        let statusIcon = 'calendar-event';

                        if (daysUntil < 0) {
                            badgeClass = 'bg-danger';
                            badgeText = 'Terlewat';
                            statusIcon = 'exclamation-triangle';
                        } else if (daysUntil <= 3) {
                            badgeClass = 'bg-warning';
                            badgeText = 'Mendekat';
                            statusIcon = 'clock';
                        }

                        return `
                    <div class="card mb-2 border-0 bg-light">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-${statusIcon} text-${badgeClass.replace('bg-', '')}"></i>
                                        <h6 class="mb-0 fw-bold">${project.project_name}</h6>
                                    </div>
                                    <p class="text-muted mb-1 small">
                                        <i class="bi bi-building me-1"></i>${project.client.company_name}
                                    </p>
                                </div>
                                <span class="badge ${badgeClass}">${badgeText}</span>
                            </div>
                            <div class="d-flex gap-2 small text-muted">
                                <span><i class="bi bi-calendar3 me-1"></i>${new Date(project.deadline).toLocaleDateString('id-ID')}</span>
                                <span><i class="bi bi-tag me-1"></i>Rp ${parseInt(project.total_value).toLocaleString('id-ID')}</span>
                            </div>
                        </div>
                    </div>
                `;
                    }).join('');
                }

                // Reset form
                resetNoteForm();

                // Show modal
                calendarModal.show();
            }

            // Note form handling
            const noteForm = document.getElementById('noteForm');
            const noteIdInput = document.getElementById('noteId');
            const noteTitleInput = document.getElementById('noteTitle');
            const noteDescriptionInput = document.getElementById('noteDescription');
            const submitBtnText = document.getElementById('submitBtnText');
            const cancelEditBtn = document.getElementById('cancelEditBtn');

            noteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const noteId = noteIdInput.value;
                const date = document.getElementById('noteDate').value;
                const title = noteTitleInput.value;
                const description = noteDescriptionInput.value;

                const url = noteId ? `/calendar-notes/${noteId}` : '/calendar-notes';
                const method = noteId ? 'PUT' : 'POST';

                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            date,
                            title,
                            description
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Refresh calendar data
                            loadCalendarData();
                            resetNoteForm();
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan catatan'
                        });
                    });
            });

            window.editNote = function(id, title, description) {
                noteIdInput.value = id;
                noteTitleInput.value = title;
                noteDescriptionInput.value = description || '';
                submitBtnText.textContent = 'Update Catatan';
                cancelEditBtn.style.display = 'inline-block';

                // Switch to notes tab
                const notesTab = new bootstrap.Tab(document.getElementById('notes-tab'));
                notesTab.show();
            };

            window.deleteNote = function(id) {
                Swal.fire({
                    title: 'Hapus Catatan?',
                    text: 'Catatan yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/calendar-notes/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: data.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    // Refresh calendar data
                                    loadCalendarData();
                                }
                            });
                    }
                });
            };

            function resetNoteForm() {
                noteForm.reset();
                noteIdInput.value = '';
                submitBtnText.textContent = 'Simpan Catatan';
                cancelEditBtn.style.display = 'none';
            }

            cancelEditBtn.addEventListener('click', resetNoteForm);

            // Load calendar data function with project deadlines
            function loadCalendarData() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth() + 1;

                // Load notes
                fetch(`/calendar-notes/month/${year}/${month}`)
                    .then(response => response.json())
                    .then(data => {
                        calendarNotes = data.notes || [];
                        renderCalendar();

                        // After rendering calendar with notes, reload the current modal if open
                        const modalElement = document.getElementById('calendarModal');
                        if (modalElement.classList.contains('show')) {
                            const dateInput = document.getElementById('noteDate');
                            if (dateInput.value) {
                                const dateParts = dateInput.value.split('-');
                                openDayModal(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(
                                    dateParts[2]));
                            }
                        }
                    });

                // Load project deadlines
                fetch(`/projects/deadlines/month/${year}/${month}`)
                    .then(response => response.json())
                    .then(data => {
                        projectDeadlines = data.deadlines || {};
                        renderCalendar();
                    });
            }

            // Navigation buttons
            prevMonthBtn.addEventListener('click', function() {
                currentDate.setMonth(currentDate.getMonth() - 1);
                loadCalendarData();
            });

            nextMonthBtn.addEventListener('click', function() {
                currentDate.setMonth(currentDate.getMonth() + 1);
                loadCalendarData();
            });

            // Initial render
            renderCalendar();

            // Clickable project cards - redirect to projects page with filter
            const clickableCards = document.querySelectorAll('.clickable-card');
            clickableCards.forEach(card => {
                card.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    if (filter) {
                        window.location.href = '/projects?' + filter;
                    }
                });
            });

            // Add custom styles
            const style = document.createElement('style');
            style.textContent = `
                /* Prevent horizontal overflow */
                body {
                    overflow-x: hidden;
                }

                .container-fluid {
                    overflow-x: hidden;
                    max-width: 100vw;
                }

                .row {
                    margin-left: 0;
                    margin-right: 0;
                }

                /* Mobile specific fixes */
                @media (max-width: 768px) {
                    .container-fluid {
                        padding-left: 0.75rem;
                        padding-right: 0.75rem;
                    }

                    .card {
                        margin-bottom: 0.5rem;
                    }

                    /* Reduce card padding on mobile */
                    .stat-card .card-body {
                        padding: 0.75rem !important;
                    }

                    .luxury-card {
                        margin-bottom: 0;
                    }
                }

                /* Asset Detail Cards - Responsive */
                .asset-detail-card {
                    transition: all 0.3s ease;
                    min-height: 120px;
                }

                .asset-detail-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
                }

                .asset-icon {
                    width: 36px;
                    height: 36px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .asset-icon i {
                    font-size: 1.1rem;
                }

                .asset-label {
                    font-size: 0.8rem;
                    font-weight: 600;
                }

                .asset-value {
                    font-size: 0.95rem;
                }

                .asset-percent {
                    font-size: 0.7rem;
                }

                /* Mobile Responsive */
                @media (max-width: 768px) {
                    .asset-detail-card {
                        min-height: 95px;
                    }

                    .asset-icon {
                        width: 28px;
                        height: 28px;
                    }

                    .asset-icon i {
                        font-size: 0.9rem;
                    }

                    .asset-label {
                        font-size: 0.7rem;
                    }

                    .asset-value {
                        font-size: 0.8rem;
                    }

                    .asset-percent {
                        font-size: 0.65rem;
                    }

                    .luxury-card:hover {
                        transform: none !important;
                    }

                    .asset-detail-card:hover {
                        transform: none;
                    }
                }

                @media (max-width: 480px) {
                    .asset-detail-card {
                        min-height: 85px;
                    }

                    .asset-value {
                        font-size: 0.75rem;
                    }
                }

                /* Calendar Styles */
                .calendar-wrapper {
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    scrollbar-width: thin;
                    scrollbar-color: rgba(139, 92, 246, 0.3) transparent;
                }

                .calendar-wrapper::-webkit-scrollbar {
                    height: 6px;
                }

                .calendar-wrapper::-webkit-scrollbar-track {
                    background: rgba(139, 92, 246, 0.05);
                    border-radius: 10px;
                }

                .calendar-wrapper::-webkit-scrollbar-thumb {
                    background: rgba(139, 92, 246, 0.3);
                    border-radius: 10px;
                }

                .calendar-wrapper::-webkit-scrollbar-thumb:hover {
                    background: rgba(139, 92, 246, 0.5);
                }

                .calendar-container {
                    width: 100%;
                    min-width: 100%;
                }

                @media (max-width: 768px) {
                    .calendar-wrapper {
                        overflow-x: auto;
                        margin: 0 -0.75rem;
                        padding: 0 0.75rem;
                        position: relative;
                    }

                    .calendar-container {
                        min-width: 650px;
                    }

                    /* Scroll indicator */
                    .calendar-wrapper::before {
                        content: '← Scroll →';
                        position: absolute;
                        top: 10px;
                        right: 20px;
                        background: linear-gradient(135deg, #8B5CF6, #A855F7);
                        color: white;
                        padding: 6px 14px;
                        border-radius: 20px;
                        font-size: 0.65rem;
                        font-weight: 600;
                        z-index: 10;
                        animation: fadeOutScroll 4s forwards;
                        pointer-events: none;
                        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
                    }

                    @keyframes fadeOutScroll {
                        0%, 60% { opacity: 1; }
                        100% { opacity: 0; display: none; }
                    }
                }

                @media (max-width: 480px) {
                    .calendar-container {
                        min-width: 550px;
                    }
                }

                .calendar-container {
                    width: 100%;
                }

                .calendar-header {
                    display: grid;
                    grid-template-columns: repeat(7, 1fr);
                    gap: 8px;
                    margin-bottom: 12px;
                }

                .calendar-day-header {
                    text-align: center;
                    font-weight: 600;
                    color: #8B5CF6;
                    padding: 12px 8px;
                    font-size: 0.85rem;
                    letter-spacing: 0.5px;
                }

                .calendar-body {
                    display: grid;
                    grid-template-columns: repeat(7, 1fr);
                    gap: 8px;
                }

                .calendar-day {
                    aspect-ratio: 1;
                    border: 1px solid rgba(139, 92, 246, 0.1);
                    border-radius: 12px;
                    padding: 8px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    background: rgba(255, 255, 255, 0.95);
                    display: flex;
                    flex-direction: column;
                    position: relative;
                    min-height: 80px;
                }

                .calendar-day:hover {
                    background: rgba(139, 92, 246, 0.05);
                    border-color: rgba(139, 92, 246, 0.3);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
                }

                .calendar-day.today {
                    background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(168, 85, 247, 0.1));
                    border: 2px solid #8B5CF6;
                    font-weight: bold;
                }

                .calendar-day.other-month {
                    background: rgba(0, 0, 0, 0.02);
                    cursor: default;
                }

                .calendar-day.has-event {
                    border-color: rgba(139, 92, 246, 0.3);
                }

                .day-number {
                    font-size: 0.9rem;
                    font-weight: 600;
                    color: #374151;
                    margin-bottom: 4px;
                }

                .note-preview, .deadline-preview {
                    font-size: 0.7rem;
                    padding: 3px 6px;
                    border-radius: 6px;
                    color: white;
                    margin-top: 2px;
                    display: flex;
                    align-items: center;
                    gap: 4px;
                    font-weight: 500;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 100%;
                }

                .legend-dot {
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    display: inline-block;
                }

                /* Modal Tabs */
                .nav-pills .nav-link {
                    border-radius: 10px;
                    padding: 10px 20px;
                    color: #6B7280;
                    font-weight: 500;
                }

                .nav-pills .nav-link.active {
                    background: linear-gradient(135deg, #8B5CF6, #A855F7);
                }

                /* Responsive Calendar */
                @media (max-width: 768px) {
                    .calendar-day {
                        min-height: 70px;
                        padding: 6px;
                    }

                    .calendar-day-header {
                        padding: 8px 4px;
                        font-size: 0.75rem;
                    }

                    .day-number {
                        font-size: 0.75rem;
                    }

                    .deadline-preview, .note-preview {
                        font-size: 0.65rem;
                    }

                    .modal-lg {
                        max-width: 95%;
                    }
                }

                @media (max-width: 480px) {
                    .calendar-day {
                        min-height: 60px;
                        padding: 4px;
                    }

                    .calendar-day-header {
                        padding: 6px 2px;
                        font-size: 0.7rem;
                    }

                    .legend-dot {
                        width: 8px;
                        height: 8px;
                    }
                }

                /* Existing dashboard styles */
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }

                .luxury-card {
                    transition: all 0.3s ease;
                }

                .clickable-card:hover {
                    cursor: pointer;
                }

                .bg-purple {
                    background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
                }

                .stat-card {
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(20px);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    border-radius: 16px;
                    padding: 1.5rem;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                    height: 100%;
                    box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
                }

                .stat-card::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 4px;
                    transition: all 0.3s ease;
                }

                .stat-card-warning::before {
                    background: linear-gradient(90deg, #FFC107, #FF9800);
                }

                .stat-card-purple::before {
                    background: linear-gradient(90deg, #8B5CF6, #A855F7);
                }

                .stat-card-danger::before {
                    background: linear-gradient(90deg, #DC2626, #B91C1C);
                }

                .stat-card-success::before {
                    background: linear-gradient(90deg, #10B981, #059669);
                }

                .stat-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
                }

                .stat-card:hover::before {
                    height: 6px;
                }

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

                .bg-purple-light {
                    background-color: rgba(139, 92, 246, 0.1) !important;
                }

                .border-purple {
                    border-color: rgba(139, 92, 246, 0.3) !important;
                }

                .card-body canvas {
                    max-height: 300px;
                }

                #monthlyRevenueChart {
                    padding: 10px;
                }

                @media (max-width: 768px) {
                    .card-body canvas {
                        max-height: 200px;
                    }

                    .luxury-card .card-header h5 {
                        font-size: 1rem;
                    }

                    .luxury-card .card-header p {
                        font-size: 0.85rem;
                    }
                }

                .badge {
                    padding: 0.5em 0.75em;
                    font-weight: 600;
                    letter-spacing: 0.5px;
                    border-radius: 8px;
                }

                .fs-7 {
                    font-size: 0.8rem;
                }

                .clickable-card:active {
                    transform: scale(0.98);
                }

                @media (hover: none) and (pointer: coarse) {
                    .stat-card:hover {
                        transform: none !important;
                        box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08) !important;
                    }

                    .luxury-card:hover {
                        transform: none !important;
                        box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08) !important;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
@endpush
