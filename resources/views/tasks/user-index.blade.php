@extends('layouts.app')
@section('title', 'Tugas Hari Ini')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-list-task me-2"></i>Tugas Hari Ini
                    </h1>
                    <p class="text-muted mb-0">Daftar tugas yang perlu dikerjakan hari ini</p>
                </div>
                <div class="d-flex align-items-center">
                    <div class="luxury-icon me-2">
                        <i class="bi bi-calendar-day text-primary"></i>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary fs-6">
                        {{ $today->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if ($assignments->count() > 0)
        <!-- Task Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                    <i class="bi bi-bar-chart me-2 text-purple"></i>Progress Hari Ini
                </h5>
            </div>
            <div class="col-6 col-xl-3 col-lg-4 col-md-6">
                <div class="card luxury-card stat-card stat-card-purple h-100">
                    <div class="card-body text-center p-3">
                        <div class="luxury-icon mx-auto mb-2">
                            <i class="bi bi-list-task text-purple fs-4"></i>
                        </div>
                        <h3 class="fw-bold text-purple mb-1">{{ $assignments->count() }}</h3>
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
                        <h3 class="fw-bold text-warning mb-1">{{ $assignments->where('status', 'pending')->count() }}</h3>
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
                        <h3 class="fw-bold text-info mb-1">{{ $assignments->where('status', 'dikerjakan')->count() }}</h3>
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
                        <h3 class="fw-bold text-success mb-1">{{ $assignments->where('status', 'valid')->count() }}</h3>
                        <small class="text-muted fw-semibold">Selesai</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        <div class="row g-4">
            @foreach ($assignments as $assignment)
                <div class="col-lg-6 col-xl-4">
                    <div class="card luxury-card task-card h-100 {{ $assignment->isSubmitted() ? 'completed' : 'pending' }}">
                        <div class="card-header bg-white border-0 p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $assignment->task->name }}</h6>
                                    <small class="text-muted">
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            {{ $assignment->task->schedule_text }}
                                        </span>
                                        @if ($assignment->task->schedule === 'once' && $assignment->task->target_date)
                                            <div class="mt-1">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                Target: {{ $assignment->task->target_date->format('d M Y') }}
                                            </div>
                                        @endif
                                    </small>
                                </div>
                                <span
                                    class="badge bg-{{ $assignment->status_color }} bg-opacity-10 text-{{ $assignment->status_color }} border border-{{ $assignment->status_color }}">
                                    {{ $assignment->status_text }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <p class="text-muted mb-3">{{ $assignment->task->description }}</p>

                            @if ($assignment->isSubmitted())
                                <div class="mt-3">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="luxury-icon me-2" style="width: 24px; height: 24px;">
                                            <i class="bi bi-chat-text text-info" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <small class="fw-semibold text-muted d-block">Keterangan:</small>
                                            <p class="small mb-0">{{ Str::limit($assignment->remarks, 100) }}</p>
                                        </div>
                                    </div>

                                    @if ($assignment->attachment)
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="luxury-icon me-2" style="width: 24px; height: 24px;">
                                                <i class="bi bi-paperclip text-primary" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <a href="{{ route('tasks.download-attachment', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download me-1"></i>Download
                                            </a>
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-center text-muted">
                                        <div class="luxury-icon me-2" style="width: 24px; height: 24px;">
                                            <i class="bi bi-clock text-muted" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <small>Dikerjakan: {{ $assignment->submitted_at->format('d M Y H:i') }}</small>
                                    </div>

                                    @if ($assignment->isValidated())
                                        <div class="d-flex align-items-center text-success mt-1">
                                            <div class="luxury-icon me-2" style="width: 24px; height: 24px;">
                                                <i class="bi bi-check-circle text-success" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <small>Divalidasi: {{ $assignment->validated_at->format('d M Y H:i') }}</small>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-white border-0 p-4">
                            @if (!$assignment->isSubmitted())
                                <a href="{{ route('tasks.user.show', $assignment) }}" class="btn btn-primary w-100">
                                    <i class="bi bi-play-circle me-2"></i>Kerjakan Tugas
                                </a>
                            @else
                                @if ($assignment->isValidated())
                                    <button class="btn btn-success w-100" disabled>
                                        <i class="bi bi-check-circle me-2"></i>Tugas Selesai & Valid
                                    </button>
                                @else
                                    <button class="btn btn-warning w-100" disabled>
                                        <i class="bi bi-hourglass-split me-2"></i>Menunggu Validasi Admin
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State - No Tasks -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card luxury-card">
                    <div class="card-body text-center py-5">
                        <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Tidak Ada Tugas Hari Ini</h5>
                        <p class="text-muted mb-4">
                            Selamat! Tidak ada tugas yang perlu dikerjakan untuk hari ini.
                        </p>
                        <div class="d-flex justify-content-center">
                            <div class="luxury-icon" style="width: 60px; height: 60px;">
                                <i class="bi bi-emoji-smile text-success" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Schedule Information -->
    <div class="mt-5">
        <div class="card luxury-card border-0">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    <div class="luxury-icon me-3">
                        <i class="bi bi-info-circle text-info"></i>
                    </div>
                    Informasi Jadwal Tugas
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-calendar-day text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-primary mb-1">Senin-Jumat</h6>
                                <small class="text-muted">Tugas harian yang muncul setiap hari kerja</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-calendar-week text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-success mb-1">Seminggu Sekali</h6>
                                <small class="text-muted">Tugas mingguan yang muncul setiap hari Senin</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-calendar-month text-warning"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-warning mb-1">Sebulan Sekali</h6>
                                <small class="text-muted">Tugas bulanan yang muncul setiap tanggal 1</small>
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
            // SweetAlert untuk session messages
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

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#8B5CF6'
                });
            @endif

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

            // Animation for task cards
            const taskCards = document.querySelectorAll('.task-card');
            taskCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 200 + (index * 100));
            });

            // Progress calculation and display
            const totalTasks = {{ $assignments->count() }};
            const completedTasks = {{ $assignments->where('status', 'valid')->count() }};

            if (totalTasks > 0) {
                const progressPercentage = Math.round((completedTasks / totalTasks) * 100);

                // Add progress indicator to page header
                const headerDiv = document.querySelector('.row.mb-4 .col-12 > div');
                if (progressPercentage > 0) {
                    const progressDiv = document.createElement('div');
                    progressDiv.className = 'mt-2';
                    progressDiv.innerHTML = `
                        <div class="d-flex align-items-center">
                            <small class="text-muted me-2">Progress hari ini:</small>
                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: ${progressPercentage}%"
                                     aria-valuenow="${progressPercentage}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                            <small class="fw-bold text-success">${progressPercentage}%</small>
                        </div>
                    `;
                    headerDiv.appendChild(progressDiv);
                }
            }

            // Auto-refresh for real-time updates
            let lastUpdate = Date.now();
            setInterval(() => {
                const timeSinceUpdate = Math.floor((Date.now() - lastUpdate) / 1000);
                if (timeSinceUpdate > 600) { // 10 minutes
                    // Show refresh suggestion
                    const refreshAlert = document.createElement('div');
                    refreshAlert.className = 'position-fixed bottom-0 end-0 m-3';
                    refreshAlert.innerHTML = `
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            <small>Refresh halaman untuk update terbaru</small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    document.body.appendChild(refreshAlert);

                    setTimeout(() => {
                        if (refreshAlert.parentNode) {
                            refreshAlert.remove();
                        }
                    }, 5000);
                }
            }, 300000); // Check every 5 minutes
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

        .task-card {
            position: relative;
            overflow: hidden;
        }

        .task-card.pending::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #FFC107, #FF9800);
            transition: all 0.3s ease;
        }

        .task-card.completed::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #10B981, #059669);
            transition: all 0.3s ease;
        }

        .task-card:hover::before {
            width: 6px;
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

        .progress {
            border-radius: 10px;
            background-color: rgba(139, 92, 246, 0.1);
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }
        }

        /* Animation keyframes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .task-card {
            animation: fadeInUp 0.5s ease forwards;
        }
    </style>
@endpush
