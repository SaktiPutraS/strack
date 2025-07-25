@extends('layouts.app')
@section('title', 'Validasi Tugas')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-check-circle me-2"></i>Validasi Tugas
                    </h1>
                    <p class="text-muted mb-0">Validasi tugas yang telah dikerjakan oleh tim</p>
                </div>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Tugas
                </a>
            </div>
        </div>
    </div>

    <!-- Validation List -->
    <div class="card luxury-card border-0">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-hourglass-split text-warning"></i>
                        </div>
                        Tugas Menunggu Validasi
                    </h5>
                    <small class="text-muted">{{ $assignments->count() }} tugas perlu divalidasi</small>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($assignments->count() > 0)
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
                                            <i class="bi bi-list-task me-2 text-muted"></i>Tugas
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
                                            <i class="bi bi-clock me-2 text-muted"></i>Waktu Submit
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
                                @foreach ($assignments as $index => $assignment)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-4">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark">{{ $assignment->task->name }}</h6>
                                                <small class="text-muted">
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                                        {{ $assignment->task->schedule_text }}
                                                    </span>
                                                </small>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="luxury-icon me-2" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-person text-info"></i>
                                                </div>
                                                <span class="fw-medium">{{ $assignment->user_id }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="fw-medium">{{ $assignment->assigned_date->format('d M Y') }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div style="max-width: 200px;">
                                                {{ Str::limit($assignment->remarks, 100) }}
                                                @if (strlen($assignment->remarks) > 100)
                                                    <br>
                                                    <button type="button" class="btn btn-sm btn-outline-info mt-1" data-bs-toggle="modal"
                                                        data-bs-target="#remarksModal{{ $assignment->id }}">
                                                        <i class="bi bi-eye me-1"></i>Lihat Selengkapnya
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($assignment->attachment)
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('tasks.download-attachment', $assignment) }}"
                                                        class="btn btn-sm btn-outline-primary mb-1">
                                                        <i class="bi bi-download me-1"></i>Download
                                                    </a>
                                                    <small class="text-muted">{{ $assignment->attachment_name }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
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
                                            <form action="{{ route('tasks.validate-assignment', $assignment) }}" method="POST" class="d-inline"
                                                onsubmit="return confirmValidation({{ $assignment->id }})">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle me-1"></i>Validasi
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Modal untuk keterangan lengkap -->
                                    @if (strlen($assignment->remarks) > 100)
                                        <div class="modal fade" id="remarksModal{{ $assignment->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Detail Pengerjaan Tugas</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold text-muted">Tugas</label>
                                                                <div class="p-2 bg-light rounded">{{ $assignment->task->name }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold text-muted">User</label>
                                                                <div class="p-2 bg-light rounded">{{ $assignment->user_id }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold text-muted">Tanggal</label>
                                                                <div class="p-2 bg-light rounded">{{ $assignment->assigned_date->format('d M Y') }}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold text-muted">Waktu Submit</label>
                                                                <div class="p-2 bg-light rounded">{{ $assignment->submitted_at->format('d M Y H:i') }}
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label fw-semibold text-muted">Keterangan Pengerjaan</label>
                                                                <div class="p-3 bg-light rounded">{{ $assignment->remarks }}</div>
                                                            </div>
                                                            @if ($assignment->attachment)
                                                                <div class="col-12">
                                                                    <label class="form-label fw-semibold text-muted">Lampiran</label>
                                                                    <div class="p-2 bg-light rounded">
                                                                        <a href="{{ route('tasks.download-attachment', $assignment) }}"
                                                                            class="btn btn-outline-primary">
                                                                            <i class="bi bi-download me-2"></i>Download
                                                                            {{ $assignment->attachment_name }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        <form action="{{ route('tasks.validate-assignment', $assignment) }}" method="POST"
                                                            class="d-inline" onsubmit="return confirmValidation({{ $assignment->id }})">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-check-circle me-2"></i>Validasi Tugas
                                                            </button>
                                                        </form>
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
                        @foreach ($assignments as $index => $assignment)
                            <div class="card luxury-card validation-card mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $assignment->task->name }}</h6>
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="luxury-icon me-2" style="width: 20px; height: 20px;">
                                                    <i class="bi bi-person text-info" style="font-size: 0.7rem;"></i>
                                                </div>
                                                <small class="fw-medium">{{ $assignment->user_id }}</small>
                                                <span class="mx-2">•</span>
                                                <small class="text-muted">{{ $assignment->assigned_date->format('d M Y') }}</small>
                                            </div>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                                {{ $assignment->task->schedule_text }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <small class="fw-semibold text-muted">Keterangan:</small>
                                        <p class="small mb-0">{{ Str::limit($assignment->remarks, 120) }}</p>
                                        @if (strlen($assignment->remarks) > 120)
                                            <button type="button" class="btn btn-sm btn-outline-info mt-1" data-bs-toggle="modal"
                                                data-bs-target="#remarksModal{{ $assignment->id }}">
                                                <i class="bi bi-eye me-1"></i>Lihat Lengkap
                                            </button>
                                        @endif
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>{{ $assignment->submitted_at->format('d M Y H:i') }}
                                            </small>
                                            @if ($assignment->attachment)
                                                <small class="text-primary">
                                                    <i class="bi bi-paperclip me-1"></i>Ada lampiran
                                                </small>
                                            @endif
                                        </div>
                                        <div class="d-flex gap-1">
                                            @if ($assignment->attachment)
                                                <a href="{{ route('tasks.download-attachment', $assignment) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @endif
                                            <form action="{{ route('tasks.validate-assignment', $assignment) }}" method="POST" class="d-inline"
                                                onsubmit="return confirmValidation({{ $assignment->id }})">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
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
                        <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Tidak ada tugas yang perlu divalidasi</h5>
                    <p class="text-muted mb-4">Semua tugas sudah divalidasi atau belum ada yang dikerjakan</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmValidation(assignmentId) {
            return new Promise((resolve) => {
                Swal.fire({
                    title: 'Validasi Tugas',
                    text: 'Yakin ingin memvalidasi tugas ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Validasi!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    resolve(result.isConfirmed);
                });
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

            // Enhanced form submission
            document.querySelectorAll('form[onsubmit*="confirmValidation"]').forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const assignmentId = this.querySelector('button[type="submit"]').closest('form').onsubmit.toString()
                        .match(/\d+/)[0];
                    const confirmed = await confirmValidation(assignmentId);

                    if (confirmed) {
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalContent = submitBtn.innerHTML;

                        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memvalidasi...';
                        submitBtn.disabled = true;

                        this.submit();
                    }
                });
            });

            // Auto refresh indicator
            let lastRefresh = Date.now();
            setInterval(() => {
                const timeSinceRefresh = Math.floor((Date.now() - lastRefresh) / 1000);
                if (timeSinceRefresh > 300) { // 5 minutes
                    const refreshIndicator = document.createElement('div');
                    refreshIndicator.className = 'position-fixed top-0 end-0 m-3';
                    refreshIndicator.innerHTML = `
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            Halaman terakhir dimuat ${Math.floor(timeSinceRefresh / 60)} menit yang lalu
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    document.body.appendChild(refreshIndicator);

                    setTimeout(() => {
                        if (refreshIndicator.parentNode) {
                            refreshIndicator.remove();
                        }
                    }, 5000);
                }
            }, 60000); // Check every minute
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

        .validation-card {
            position: relative;
            overflow: hidden;
        }

        .validation-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #FFC107, #FF9800);
            transition: all 0.3s ease;
        }

        .validation-card:hover::before {
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

        .btn-success {
            background: linear-gradient(135deg, #10B981, #059669);
            border: none;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.25);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669, #047857);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
        }

        /* Modal enhancements */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), rgba(168, 85, 247, 0.05));
        }

        .modal-footer {
            border-top: 1px solid rgba(139, 92, 246, 0.1);
        }

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }
        }

        /* Loading state */
        .btn:disabled {
            opacity: 0.7;
            transform: none !important;
        }

        /* Success animation */
        @keyframes successPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .btn-success.validated {
            animation: successPulse 2s;
        }
    </style>
@endpush
