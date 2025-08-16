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
    <div class="row g-3 mb-4">
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
            <div class="card luxury-card stat-card stat-card-primary h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-bank text-primary fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1 fs-6">{{ number_format($saldoBank, 0, ',', '.') }}</h3>
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
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Statistik Keuangan
            </h5>
        </div>

        <!-- Line Chart -->
        <div class="col-12 col-lg-8">
            <div class="card luxury-card border-0 h-100">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-graph-up me-2 text-purple"></i>Pendapatan & Pengeluaran
                    </h5>
                    <p class="text-muted mb-0">Akumulasi per minggu tahun ini (dimulai Juli 2025)</p>
                </div>
                <div class="card-body">
                    <canvas id="lineChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-12 col-lg-4">
            <div class="card luxury-card border-0 h-100">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-0">Asset</h5>
                    <p class="text-muted mb-0">Total: <strong>Rp. {{ number_format($pieData['total']) }}</strong></p>
                </div>
                <div class="card-body position-relative">
                    <canvas id="pieChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Deadlines -->
    <div class="row">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <div>
                        <h4 class="fw-bold mb-1">
                            <i class="bi bi-calendar-event text-purple me-2"></i>Deadline Terdekat
                        </h4>
                        <p class="text-muted mb-0">Proyek yang segera jatuh tempo</p>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if ($proyekDeadlineTermedekat->count() > 0)
                        <div class="row g-3 p-3">
                            @foreach ($proyekDeadlineTermedekat as $project)
                                <div class="col-12">
                                    <div class="card luxury-card project-card border-0" data-url="{{ route('projects.show', $project) }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold mb-1">{{ $project->title }}</h6>
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="bi bi-person-fill text-purple me-1 fs-7"></i>
                                                        <small class="text-muted">{{ $project->client->name }}</small>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-calendar-date text-purple me-1 fs-7"></i>
                                                        <small class="fw-medium">{{ $project->deadline->format('d M Y') }}</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    @if ($project->status == 'WAITING')
                                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill">
                                                            MENUNGGU
                                                        </span>
                                                    @else
                                                        <span class="badge bg-purple-light text-purple border border-purple rounded-pill">
                                                            PROGRESS
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-calendar-check text-purple" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Tidak ada deadline mendekat</h5>
                            <p class="text-muted mb-4">Semua proyek dalam kondisi aman</p>
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Proyek Baru
                                </a>
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-list-task me-2"></i>Lihat Proyek
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clickable cards navigation
            const clickableCards = document.querySelectorAll('.clickable-card');

            clickableCards.forEach(card => {
                card.style.cursor = 'pointer';

                // Touch feedback for mobile
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                    this.style.transition = 'transform 0.1s ease';
                }, {
                    passive: true
                });

                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                    this.style.transition = 'transform 0.2s ease';
                }, {
                    passive: true
                });

                // Click handler
                card.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    if (filter) {
                        // Add loading state
                        this.style.opacity = '0.7';

                        // Navigate
                        setTimeout(() => {
                            window.location.href = `{{ route('projects.index') }}?${filter}`;
                        }, 100);
                    }
                });

                // Hover effect for desktop
                card.addEventListener('mouseenter', function() {
                    if (window.innerWidth > 768) {
                        this.style.transform = 'translateY(-4px)';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    if (window.innerWidth > 768) {
                        this.style.transform = 'translateY(0)';
                    }
                });
            });

            // Smooth animations on scroll (desktop only)
            if (window.innerWidth > 768 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                const cards = document.querySelectorAll('.luxury-card');

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry, index) => {
                        if (entry.isIntersecting) {
                            setTimeout(() => {
                                entry.target.style.opacity = '1';
                                entry.target.style.transform = 'translateY(0)';
                            }, index * 100);
                        }
                    });
                }, {
                    threshold: 0.1
                });

                cards.forEach(card => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    observer.observe(card);
                });
            }

            // Add ripple animation keyframes
            const style = document.createElement('style');
            style.textContent = `
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

                @media (max-width: 768px) {
                    .luxury-card:hover {
                        transform: none !important;
                    }
                }

                .bg-purple {
                    background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
                }

                /* Stat card styling dengan border atas berwarna */
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

                /* Border colors untuk setiap card */
                .stat-card-warning::before {
                    background: linear-gradient(90deg, #FFC107, #FF9800);
                }

                .stat-card-purple::before {
                    background: linear-gradient(90deg, #8B5CF6, #A855F7);
                }

                .stat-card-primary::before {
                    background: linear-gradient(90deg, #3B82F6, #2563EB);
                }

                .stat-card-success::before {
                    background: linear-gradient(90deg, #10B981, #059669);
                }

                /* Hover effect */
                .stat-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
                }

                .stat-card:hover::before {
                    height: 6px;
                }

                /* Project card dengan border kiri */
                .project-card {
                    position: relative;
                    overflow: hidden;
                    cursor: pointer;
                }

                .project-card::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    bottom: 0;
                    width: 4px;
                    background: linear-gradient(180deg, #8B5CF6, #A855F7);
                    transition: all 0.3s ease;
                }

                .project-card:hover::before {
                    width: 6px;
                }
            `;
            document.head.appendChild(style);

            // Project cards click handler
            const projectCards = document.querySelectorAll('.project-card');
            projectCards.forEach(card => {
                card.addEventListener('click', function() {
                    window.location.href = this.dataset.url;
                });

                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });

                card.addEventListener('touchend', function() {
                    this.style.transform = '';
                });
            });

            // Line Chart
            const lineCtx = document.getElementById('lineChart').getContext('2d');
            const lineChart = new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: @json(collect($weeklyData)->pluck('week')),
                    datasets: [{
                        label: 'Pendapatan',
                        data: @json(collect($weeklyData)->pluck('income')),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }, {
                        label: 'Pengeluaran',
                        data: @json(collect($weeklyData)->pluck('expense')),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    const index = tooltipItems[0].dataIndex;
                                    const weekData = @json($weeklyData)[index];
                                    return weekData.start_date + ' - ' + weekData.end_date;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });

            // Pie Chart
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            const pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: @json($pieData['labels']),
                    datasets: [{
                        data: @json($pieData['data']),
                        backgroundColor: @json($pieData['colors']),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label.split(':')[0] || '';
                                    const value = context.raw || 0;
                                    return `${label}: Rp ${value.toLocaleString('id-ID')}`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
