@extends('layouts.app')
@section('title', 'Manajemen Tugas Harian')

@section('content')
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-list-task me-2"></i>Manajemen Tugas Harian
                    </h1>
                    <p class="text-muted mb-0">Kelola tugas harian yang akan dikerjakan oleh tim</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('tasks.export-excel') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </a>
                    <a href="{{ route('tasks.validation') }}" class="btn btn-outline-warning position-relative">
                        <i class="bi bi-check-circle me-2"></i>Validasi Tugas
                        @if ($needValidationCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $needValidationCount }}
                                <span class="visually-hidden">tugas perlu validasi</span>
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Tugas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Statistik Tugas
            </h5>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $tasks->where('status', 'active')->count() }}</h3>
                    <small class="text-muted fw-semibold">Aktif</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-pause-circle text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $tasks->where('status', 'inactive')->count() }}</h3>
                    <small class="text-muted fw-semibold">Nonaktif</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-info h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clipboard-check text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $tasks->sum(function ($task) {return $task->assignments->count();}) }}</h3>
                    <small class="text-muted fw-semibold">Total Assignment</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-danger h-100">
                <div class="card-body text-center p-3 position-relative">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-exclamation-circle text-danger fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-danger mb-1">{{ $needValidationCount }}</h3>
                    <small class="text-muted fw-semibold">Perlu Validasi</small>
                    @if ($needValidationCount > 0)
                        <div class="pulse-dot"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="card luxury-card border-0">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-list text-purple"></i>
                        </div>
                        Daftar Tugas Harian
                    </h5>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($tasks->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-hash me-2 text-muted"></i>No
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-task me-2 text-muted"></i>Nama Tugas
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-calendar me-2 text-muted"></i>Jadwal
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-toggle-on me-2 text-muted"></i>Status
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-clipboard-data me-2 text-muted"></i>Assignment
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $index => $task)
                                    <tr class="border-bottom task-row clickable-row" data-id="{{ $task->id }}" style="cursor: pointer;">
                                        <td class="px-4 py-4">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark">{{ $task->name }}</h6>
                                                <small class="text-muted">{{ Str::limit($task->description, 60) }}</small>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                                {{ $task->schedule_text }}
                                            </span>
                                            @if ($task->schedule === 'once' && $task->target_date)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ $task->target_date->format('d M Y') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($task->status === 'active')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                    <i class="bi bi-check-circle me-1"></i>AKTIF
                                                </span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                                    <i class="bi bi-pause-circle me-1"></i>NONAKTIF
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="luxury-icon me-2" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-clipboard-data text-purple"></i>
                                                </div>
                                                <span class="fw-bold text-purple">{{ $task->assignments->count() }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    <div class="p-3">
                        @foreach ($tasks as $index => $task)
                            <div class="card luxury-card task-card mb-3 clickable-card" data-id="{{ $task->id }}" style="cursor: pointer;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $task->name }}</h6>
                                            <small class="text-muted">{{ Str::limit($task->description, 60) }}</small>
                                        </div>
                                        @if ($task->status === 'active')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                <i class="bi bi-check-circle me-1"></i>AKTIF
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                                <i class="bi bi-pause-circle me-1"></i>NONAKTIF
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mb-2">
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            {{ $task->schedule_text }}
                                        </span>
                                        @if ($task->schedule === 'once' && $task->target_date)
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    Target: {{ $task->target_date->format('d M Y') }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clipboard-data me-1 text-purple"></i>
                                            <small class="fw-medium">{{ $task->assignments->count() }} assignment</small>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary"
                                                onclick="event.stopPropagation();">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-warning"
                                                onclick="event.stopPropagation();">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="event.stopPropagation(); confirmDelete({{ $task->id }});">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-list-task text-muted" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Belum ada tugas harian</h5>
                    <p class="text-muted mb-4">Tambahkan tugas harian pertama untuk mulai mengelola aktivitas tim</p>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Tugas Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Hidden Delete Forms -->
    @foreach ($tasks as $task)
        <form id="delete-form-{{ $task->id }}" action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection

@push('scripts')
    <script>
        function confirmDelete(taskId) {
            Swal.fire({
                title: 'Yakin menghapus tugas ini?',
                text: "Semua assignment yang terkait akan ikut terhapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + taskId).submit();
                }
            });
        }

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

            // Clickable rows untuk detail
            document.querySelectorAll('.clickable-row, .clickable-card').forEach(row => {
                row.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-id');
                    window.location.href = `{{ url('tasks') }}/${taskId}`;
                });

                // Hover effect
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(139, 92, 246, 0.05)';
                    this.style.transition = 'background-color 0.2s ease';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });

                // Touch feedback for mobile
                row.addEventListener('touchstart', function() {
                    this.style.backgroundColor = 'rgba(139, 92, 246, 0.1)';
                }, {
                    passive: true
                });

                row.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.style.backgroundColor = '';
                    }, 200);
                }, {
                    passive: true
                });
            });

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

        .stat-card-danger::before {
            background: linear-gradient(90deg, #EF4444, #DC2626);
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

        .task-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #8B5CF6, #A855F7);
            transition: all 0.3s ease;
        }

        .task-card:hover::before {
            width: 6px;
        }

        .table th {
            background-color: rgba(139, 92, 246, 0.05);
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
            padding: 1rem;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(139, 92, 246, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(139, 92, 246, 0.03);
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* Pulse animation for validation card */
        .pulse-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 12px;
            height: 12px;
            background: #EF4444;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }
        }
    </style>
@endpush
