@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-house-door me-2"></i>Dashboard
                    </h1>
                    <p class="text-muted mb-0">{{ now()->format('d F Y') }} • Selamat datang kembali</p>
                </div>
                <div class="d-flex align-items-center">
                    <div class="luxury-icon me-2">
                        <i class="bi bi-calendar-day text-primary"></i>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary fs-6">
                        {{ now()->format('l') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Ringkasan Tugas Hari Ini
            </h5>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-purple h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-list-task text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $todayStats['total'] }}</h3>
                    <small class="text-muted fw-semibold">Total Tugas</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $todayStats['pending'] }}</h3>
                    <small class="text-muted fw-semibold">Belum Dikerjakan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-info h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-hourglass-split text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $todayStats['submitted'] }}</h3>
                    <small class="text-muted fw-semibold">Menunggu Validasi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $todayStats['completed'] }}</h3>
                    <small class="text-muted fw-semibold">Selesai</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    @if ($todayStats['total'] > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card luxury-card border-0">
                    <div class="card-header bg-white border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1 d-flex align-items-center">
                                    <div class="luxury-icon me-3">
                                        <i class="bi bi-graph-up text-purple"></i>
                                    </div>
                                    Progress Hari Ini
                                </h5>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success fs-6">
                                {{ $progressPercentage }}% Selesai
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="progress mb-3" style="height: 12px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: {{ $progressPercentage }}%" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-3">
                                <small class="text-muted d-block">Pending</small>
                                <span class="fw-bold text-warning">{{ $todayStats['pending'] }}</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">Dikerjakan</small>
                                <span class="fw-bold text-info">{{ $todayStats['submitted'] }}</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">Selesai</small>
                                <span class="fw-bold text-success">{{ $todayStats['completed'] }}</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">Total</small>
                                <span class="fw-bold text-purple">{{ $todayStats['total'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-lightning text-warning"></i>
                        </div>
                        Aksi Cepat
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('tasks.user.index') }}" class="btn btn-outline-primary w-100 p-3">
                                <div class="d-flex align-items-center">
                                    <div class="luxury-icon me-3">
                                        <i class="bi bi-list-task text-primary"></i>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-0">Lihat Tugas Hari Ini</h6>
                                        <small class="text-muted">Daftar semua tugas yang perlu dikerjakan</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            @if ($todayStats['pending'] > 0)
                                <a href="{{ $nextTask ? route('tasks.user.show', $nextTask) : route('tasks.user.index') }}"
                                    class="btn btn-primary w-100 p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="luxury-icon me-3" style="background: rgba(255,255,255,0.2);">
                                            <i class="bi bi-play-circle text-white"></i>
                                        </div>
                                        <div class="text-start text-white">
                                            <h6 class="mb-0">Kerjakan Tugas</h6>
                                            <small class="opacity-75">Mulai mengerjakan tugas berikutnya</small>
                                        </div>
                                    </div>
                                </a>
                            @else
                                <div class="btn btn-success w-100 p-3" style="cursor: default;">
                                    <div class="d-flex align-items-center">
                                        <div class="luxury-icon me-3" style="background: rgba(255,255,255,0.2);">
                                            <i class="bi bi-check-circle text-white"></i>
                                        </div>
                                        <div class="text-start text-white">
                                            <h6 class="mb-0">Semua Tugas Selesai</h6>
                                            <small class="opacity-75">Bagus! Tidak ada tugas yang tertunda</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Tasks Overview -->
    @if ($todayTasks->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card luxury-card border-0">
                    <div class="card-header bg-white border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1 d-flex align-items-center">
                                    <div class="luxury-icon me-3">
                                        <i class="bi bi-calendar-day text-info"></i>
                                    </div>
                                    Tugas Hari Ini
                                </h5>
                            </div>
                            <a href="{{ route('tasks.user.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-3">
                            @foreach ($todayTasks->take(5) as $assignment)
                                <div
                                    class="d-flex align-items-center p-3 rounded mb-2 task-item {{ $assignment->isSubmitted() ? 'completed' : 'pending' }}">
                                    <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                        @if ($assignment->isValidated())
                                            <i class="bi bi-check-circle text-success"></i>
                                        @elseif ($assignment->isSubmitted())
                                            <i class="bi bi-hourglass-split text-info"></i>
                                        @else
                                            <i class="bi bi-circle text-warning"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $assignment->task->name }}</h6>
                                        <small class="text-muted">{{ $assignment->task->schedule_text }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge bg-{{ $assignment->status_color }} bg-opacity-10 text-{{ $assignment->status_color }} border border-{{ $assignment->status_color }}">
                                            {{ $assignment->status_text }}
                                        </span>
                                        @if (!$assignment->isSubmitted())
                                            <div class="mt-1">
                                                <a href="{{ route('tasks.user.show', $assignment) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-play-circle me-1"></i>Kerjakan
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Tasks Today -->
        <div class="row">
            <div class="col-12">
                <div class="card luxury-card border-0">
                    <div class="card-body text-center py-5">
                        <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-emoji-smile text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Tidak Ada Tugas Hari Ini</h5>
                        <p class="text-muted mb-4">Selamat! Tidak ada tugas yang perlu dikerjakan untuk hari ini.</p>
                        <div class="d-flex justify-content-center">
                            <div class="luxury-icon" style="width: 60px; height: 60px;">
                                <i class="bi bi-cup-hot text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation for statistics cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Progress bar animation
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.width = '{{ $progressPercentage }}%';
                }, 500);
            }

            // Task item hover effects
            document.querySelectorAll('.task-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(139, 92, 246, 0.05)';
                    this.style.transition = 'background-color 0.2s ease';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });

            // Auto-refresh every 5 minutes
            setInterval(() => {
                const now = new Date();
                if (now.getMinutes() % 5 === 0 && now.getSeconds() === 0) {
                    location.reload();
                }
            }, 1000);
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
            transition: all 0.3s ease;
        }

        .stat-card-purple::before {
            background: linear-gradient(90deg, #8B5CF6, #A855F7);
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10B981, #059669);
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #FFC107, #FF9800);
        }

        .stat-card-info::before {
            background: linear-gradient(90deg, #06B6D4, #0891B2);
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

        .task-item {
            border: 1px solid rgba(139, 92, 246, 0.1);
            transition: all 0.3s ease;
        }

        .task-item.completed {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .task-item.pending {
            background: rgba(255, 193, 7, 0.05);
            border-color: rgba(255, 193, 7, 0.2);
        }

        .progress {
            border-radius: 10px;
            background-color: rgba(139, 92, 246, 0.1);
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 1s ease-in-out;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }
        }
    </style>
@endpush
