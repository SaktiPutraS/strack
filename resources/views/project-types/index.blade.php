@extends('layouts.app')
@section('title', 'Tipe Proyek')

@section('content')
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-tags me-2"></i>Manajemen Tipe Proyek
                    </h1>
                    <p class="text-muted mb-0">Kelola jenis-jenis proyek yang tersedia</p>
                </div>
                <a href="{{ route('project-types.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Tipe Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Project Types List -->
    <div class="card luxury-card border-0">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-list text-purple"></i>
                        </div>
                        Daftar Tipe Proyek
                    </h5>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($projectTypes->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-tag me-2 text-muted"></i>Nama Tipe
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-eye me-2 text-muted"></i>Nama Tampilan
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-sort-numeric-down me-2 text-muted"></i>Urutan
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-folder me-2 text-muted"></i>Proyek
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark d-flex align-items-center">
                                            <i class="bi bi-toggle-on me-2 text-muted"></i>Status
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($projectTypes as $projectType)
                                    <tr class="border-bottom project-type-row clickable-row" data-id="{{ $projectType->id }}" style="cursor: pointer;">
                                        <td class="px-4 py-4">
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark">{{ $projectType->name }}</h6>
                                                @if ($projectType->description)
                                                    <small class="text-muted">{{ Str::limit($projectType->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="fw-medium">{{ $projectType->display_name }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                                {{ $projectType->sort_order }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="luxury-icon me-2" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-folder text-info"></i>
                                                </div>
                                                <span class="fw-bold text-info">{{ $projectType->projects()->count() }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($projectType->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                    <i class="bi bi-check-circle me-1"></i>AKTIF
                                                </span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                                    <i class="bi bi-pause-circle me-1"></i>NONAKTIF
                                                </span>
                                            @endif
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
                        @foreach ($projectTypes as $projectType)
                            <div class="card luxury-card project-card mb-3 clickable-card" data-id="{{ $projectType->id }}" style="cursor: pointer;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $projectType->display_name }}</h6>
                                            <small class="text-muted">{{ $projectType->name }}</small>
                                        </div>
                                        @if ($projectType->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                <i class="bi bi-check-circle me-1"></i>AKTIF
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                                <i class="bi bi-pause-circle me-1"></i>NONAKTIF
                                            </span>
                                        @endif
                                    </div>

                                    @if ($projectType->description)
                                        <p class="text-muted mb-2 small">{{ Str::limit($projectType->description, 80) }}</p>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-folder me-1 text-info"></i>
                                            <small class="fw-medium">{{ $projectType->projects()->count() }} proyek</small>
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-sort-numeric-down me-1 text-muted"></i>
                                            <small class="text-muted">Urutan: {{ $projectType->sort_order }}</small>
                                        </div>
                                        <div>
                                            <i class="bi bi-pencil-square text-primary" title="Klik untuk edit"></i>
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
                        <i class="bi bi-tags text-muted" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Belum ada tipe proyek</h5>
                    <p class="text-muted mb-4">Tambahkan tipe proyek pertama untuk mulai mengelola jenis-jenis proyek</p>
                    <a href="{{ route('project-types.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Tipe Pertama
                    </a>
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

            // Clickable rows untuk edit
            document.querySelectorAll('.clickable-row, .clickable-card').forEach(row => {
                row.addEventListener('click', function() {
                    const projectTypeId = this.getAttribute('data-id');
                    window.location.href = `{{ url('project-types') }}/${projectTypeId}/edit`;
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

            // Remove old delete/toggle handlers since they're moved to edit page
            // Konfirmasi delete dengan SweetAlert - DIHAPUS
            // Konfirmasi toggle status - DIHAPUS

            // Add hover effects to table rows - DIPINDAH KE ATAS

            // Add animation to statistics cards
            const statCards = document.querySelectorAll('.luxury-card');
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

        .project-card {
            position: relative;
            overflow: hidden;
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
