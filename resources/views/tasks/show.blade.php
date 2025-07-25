@extends('layouts.app')
@section('title', 'Detail Tugas: ' . $task->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-list-task me-2"></i>Detail Tugas
                    </h1>
                    <p class="text-muted mb-0">{{ $task->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil-square me-2"></i>Edit Tugas
                    </a>
                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Information -->
    <div class="card luxury-card border-0 mb-4">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <div class="luxury-icon me-3">
                    <i class="bi bi-info-circle text-purple"></i>
                </div>
                Informasi Tugas
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Nama Tugas</label>
                        <div class="p-3 bg-light rounded">
                            <h6 class="mb-0 fw-bold">{{ $task->name }}</h6>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Jadwal</label>
                        <div class="p-3 bg-light rounded">
                            <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                {{ $task->schedule_text }}
                            </span>
                            @if ($task->schedule === 'once' && $task->target_date)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        Target: {{ $task->target_date->format('d M Y') }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Status</label>
                        <div class="p-3 bg-light rounded">
                            @if ($task->status === 'active')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                    <i class="bi bi-check-circle me-1"></i>{{ $task->status_text }}
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                    <i class="bi bi-pause-circle me-1"></i>{{ $task->status_text }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Dibuat</label>
                        <div class="p-3 bg-light rounded">
                            <small class="fw-medium">{{ $task->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-muted">Deskripsi</label>
                    <div class="p-3 bg-light rounded">
                        <p class="mb-0">{{ $task->description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Statistik Assignment
            </h5>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-purple h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clipboard-data text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $task->assignments->count() }}</h3>
                    <small class="text-muted fw-semibold">Total Assignment</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $task->assignments->where('status', 'pending')->count() }}</h3>
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
                    <h3 class="fw-bold text-info mb-1">{{ $task->assignments->where('status', 'dikerjakan')->count() }}</h3>
                    <small class="text-muted fw-semibold">Perlu Validasi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $task->assignments->where('status', 'valid')->count() }}</h3>
                    <small class="text-muted fw-semibold">Selesai & Valid</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment History -->
    <div class="card luxury-card border-0">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-clock-history text-purple"></i>
                        </div>
                        Riwayat Assignment
                    </h5>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($task->assignments->count() > 0)
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
                                            <i class="bi bi-person me-2 text-muted"></i>User
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-calendar me-2 text-muted"></i>Tanggal
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-flag me-2 text-muted"></i>Status
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-chat-text me-2 text-muted"></i>Keterangan
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-paperclip me-2 text-muted"></i>Lampiran
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-clock me-2 text-muted"></i>Submit
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-check-circle me-2 text-muted"></i>Validasi
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-gear me-2 text-muted"></i>Aksi
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($task->assignments as $index => $assignment)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-4">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="luxury-icon me-2" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-person text-secondary"></i>
                                                </div>
                                                <span class="fw-medium">{{ $assignment->user_id }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="fw-medium">{{ $assignment->assigned_date->format('d M Y') }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span
                                                class="badge bg-{{ $assignment->status_color }} bg-opacity-10 text-{{ $assignment->status_color }} border border-{{ $assignment->status_color }}">
                                                {{ $assignment->status_text }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($assignment->remarks)
                                                <div style="max-width: 200px;">
                                                    {{ Str::limit($assignment->remarks, 50) }}
                                                    @if (strlen($assignment->remarks) > 50)
                                                        <br>
                                                        <button type="button" class="btn btn-sm btn-outline-info mt-1" data-bs-toggle="modal"
                                                            data-bs-target="#remarksModal{{ $assignment->id }}">
                                                            <i class="bi bi-eye me-1"></i>Detail
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($assignment->attachment)
                                                <a href="{{ route('tasks.download-attachment', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($assignment->submitted_at)
                                                <small class="fw-medium">{{ $assignment->submitted_at->format('d M Y H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($assignment->validated_at)
                                                <small class="fw-medium">{{ $assignment->validated_at->format('d M Y H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($assignment->status === 'dikerjakan')
                                                <form action="{{ route('tasks.validate-assignment', $assignment) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin validasi tugas ini?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle me-1"></i>Validasi
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Modal untuk keterangan lengkap -->
                                    @if ($assignment->remarks && strlen($assignment->remarks) > 50)
                                        <div class="modal fade" id="remarksModal{{ $assignment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Keterangan Lengkap</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold text-muted">User</label>
                                                            <div class="p-2 bg-light rounded">{{ $assignment->user_id }}</div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold text-muted">Tanggal</label>
                                                            <div class="p-2 bg-light rounded">{{ $assignment->assigned_date->format('d M Y') }}</div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold text-muted">Status</label>
                                                            <div class="p-2 bg-light rounded">
                                                                <span
                                                                    class="badge bg-{{ $assignment->status_color }} bg-opacity-10 text-{{ $assignment->status_color }} border border-{{ $assignment->status_color }}">
                                                                    {{ $assignment->status_text }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold text-muted">Keterangan</label>
                                                            <div class="p-3 bg-light rounded">{{ $assignment->remarks }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    <div class="p-3">
                        @foreach ($task->assignments as $index => $assignment)
                            <div class="card luxury-card assignment-card mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="luxury-icon me-2" style="width: 24px; height: 24px;">
                                                    <i class="bi bi-person text-secondary" style="font-size: 0.8rem;"></i>
                                                </div>
                                                <h6 class="fw-bold mb-0">User {{ $assignment->user_id }}</h6>
                                            </div>
                                            <small class="text-muted">{{ $assignment->assigned_date->format('d M Y') }}</small>
                                        </div>
                                        <span
                                            class="badge bg-{{ $assignment->status_color }} bg-opacity-10 text-{{ $assignment->status_color }} border border-{{ $assignment->status_color }}">
                                            {{ $assignment->status_text }}
                                        </span>
                                    </div>

                                    @if ($assignment->remarks)
                                        <div class="mb-2">
                                            <small class="fw-semibold text-muted">Keterangan:</small>
                                            <p class="small mb-0">{{ Str::limit($assignment->remarks, 100) }}</p>
                                            @if (strlen($assignment->remarks) > 100)
                                                <button type="button" class="btn btn-sm btn-outline-info mt-1" data-bs-toggle="modal"
                                                    data-bs-target="#remarksModal{{ $assignment->id }}">
                                                    <i class="bi bi-eye me-1"></i>Lihat Lengkap
                                                </button>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex flex-column">
                                            @if ($assignment->submitted_at)
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>Submit: {{ $assignment->submitted_at->format('d M Y H:i') }}
                                                </small>
                                            @endif
                                            @if ($assignment->validated_at)
                                                <small class="text-success">
                                                    <i class="bi bi-check-circle me-1"></i>Valid: {{ $assignment->validated_at->format('d M Y H:i') }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="d-flex gap-1">
                                            @if ($assignment->attachment)
                                                <a href="{{ route('tasks.download-attachment', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @endif
                                            @if ($assignment->status === 'dikerjakan')
                                                <form action="{{ route('tasks.validate-assignment', $assignment) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin validasi tugas ini?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
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
                        <i class="bi bi-clipboard-x text-muted" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Belum ada assignment</h5>
                    <p class="text-muted mb-4">Assignment akan dibuat otomatis saat user mengakses tugas sesuai jadwal</p>
                </div>
            @endif
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

        .assignment-card {
            position: relative;
            overflow: hidden;
        }

        .assignment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #8B5CF6, #A855F7);
            transition: all 0.3s ease;
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

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }
        }
    </style>
@endpush
