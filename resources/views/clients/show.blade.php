@extends('layouts.app')
@section('title', 'Detail Klien: ' . $client->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-person-circle me-2"></i>{{ $client->name }}
                    </h1>
                    <p class="text-muted mb-0">Detail informasi klien dan riwayat proyek</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ $client->whatsapp_link }}" target="_blank" class="btn btn-success">
                        <i class="bi bi-whatsapp me-2"></i>WhatsApp
                    </a>
                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card luxury-card stat-card stat-card-purple h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-folder2-open text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $totalProjects }}</h3>
                    <small class="text-muted fw-semibold">Total Proyek</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-graph-up-arrow text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1 fs-6">
                        Rp {{ number_format($client->total_project_value / 1000000, 1) }}M
                    </h3>
                    <small class="text-muted fw-semibold">Total Nilai</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card luxury-card stat-card stat-card-info h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-wallet2 text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1 fs-6">
                        Rp {{ number_format($client->total_paid / 1000000, 1) }}M
                    </h3>
                    <small class="text-muted fw-semibold">Sudah Dibayar</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1 fs-6">
                        Rp {{ number_format($client->total_remaining / 1000000, 1) }}M
                    </h3>
                    <small class="text-muted fw-semibold">Sisa Pembayaran</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Client Information -->
        <div class="col-md-8">
            <!-- Contact Information -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-info-circle text-purple"></i>
                        </div>
                        Informasi Kontak
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Nama Lengkap</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person text-purple me-2"></i>
                                <strong>{{ $client->name }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Nomor Telepon</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-telephone text-purple me-2"></i>
                                <strong>{{ $client->phone }}</strong>
                                <a href="{{ $client->whatsapp_link }}" target="_blank" class="btn btn-sm btn-success ms-2">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                        @if ($client->email)
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-semibold">Email</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-envelope text-purple me-2"></i>
                                    <a href="mailto:{{ $client->email }}" class="text-decoration-none">{{ $client->email }}</a>
                                </div>
                            </div>
                        @endif
                        @if ($client->address)
                            <div class="col-12">
                                <label class="form-label text-muted fw-semibold">Alamat</label>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-geo-alt text-purple me-2 mt-1"></i>
                                    <p class="mb-0">{{ $client->address }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Project Status Breakdown -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-pie-chart text-purple"></i>
                        </div>
                        Status Proyek
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="text-center">
                                <div class="fw-bold text-warning display-6">{{ $projectsByStatus['waiting'] }}</div>
                                <small class="text-muted">Menunggu</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-center">
                                <div class="fw-bold text-primary display-6">{{ $projectsByStatus['progress'] }}</div>
                                <small class="text-muted">Progress</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-center">
                                <div class="fw-bold text-success display-6">{{ $projectsByStatus['finished'] }}</div>
                                <small class="text-muted">Selesai</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-center">
                                <div class="fw-bold text-danger display-6">{{ $projectsByStatus['cancelled'] }}</div>
                                <small class="text-muted">Dibatalkan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Projects -->
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-folder2-open text-purple"></i>
                            </div>
                            Proyek Terbaru
                        </h5>
                        <a href="{{ route('projects.create') }}?client={{ $client->id }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Proyek Baru
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($recentProjects->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($recentProjects as $project)
                                <div class="list-group-item clickable-row" data-url="{{ route('projects.show', $project) }}" style="cursor: pointer;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <strong class="text-purple">{{ $project->title }}</strong>
                                                @if ($project->status == 'WAITING')
                                                    <span class="badge bg-warning ms-2">MENUNGGU</span>
                                                @elseif($project->status == 'PROGRESS')
                                                    <span class="badge bg-primary ms-2">PROGRESS</span>
                                                @elseif($project->status == 'FINISHED')
                                                    <span class="badge bg-success ms-2">SELESAI</span>
                                                @else
                                                    <span class="badge bg-danger ms-2">DIBATALKAN</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="bi bi-tag me-1"></i>{{ $project->type }}
                                                <span class="mx-2">•</span>
                                                <i class="bi bi-calendar3 me-1"></i>{{ $project->deadline->format('d M Y') }}
                                                <span class="mx-2">•</span>
                                                <i class="bi bi-currency-dollar me-1"></i>{{ $project->formatted_total_value }}
                                            </div>
                                            <div class="mt-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 100px; height: 6px;">
                                                        <div class="progress-bar bg-purple" style="width: {{ $project->progress_percentage }}%;">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">{{ $project->progress_percentage }}%</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <i class="bi bi-eye text-primary" title="Klik untuk detail"></i>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($client->projects->count() > 5)
                            <div class="text-center p-3">
                                <a href="{{ route('projects.index') }}?client_id={{ $client->id }}" class="btn btn-outline-primary">
                                    <i class="bi bi-folder2-open me-2"></i>Lihat Semua Proyek ({{ $client->projects->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <div class="luxury-icon mx-auto mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-folder-x text-muted" style="font-size: 1.5rem;"></i>
                            </div>
                            <p class="text-muted mt-2">Belum ada proyek</p>
                            <a href="{{ route('projects.create') }}?client={{ $client->id }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Buat Proyek Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-lightning text-purple"></i>
                        </div>
                        Aksi Cepat
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ $client->whatsapp_link }}" target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp me-2"></i>Chat WhatsApp
                        </a>
                        @if ($client->email)
                            <a href="mailto:{{ $client->email }}" class="btn btn-outline-primary">
                                <i class="bi bi-envelope me-2"></i>Kirim Email
                            </a>
                        @endif
                        <a href="{{ route('projects.create') }}?client={{ $client->id }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Proyek Baru
                        </a>
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil me-2"></i>Edit Klien
                        </a>
                    </div>
                </div>
            </div>

            <!-- Client Summary -->
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-graph-up text-purple"></i>
                        </div>
                        Ringkasan Kinerja
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Proyek Aktif:</span>
                                <strong>{{ $activeProjects }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Proyek Selesai:</span>
                                <strong>{{ $finishedProjects }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Proyek:</span>
                                <strong>{{ $totalProjects }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Rata-rata Nilai:</span>
                                <strong>
                                    @if ($totalProjects > 0)
                                        Rp {{ number_format($client->total_project_value / $totalProjects, 0, ',', '.') }}
                                    @else
                                        Rp 0
                                    @endif
                                </strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Progress Pembayaran:</span>
                                <strong>
                                    @if ($client->total_project_value > 0)
                                        {{ number_format(($client->total_paid / $client->total_project_value) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Progress Bar -->
                    <div class="mt-3">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-purple"
                                style="width: {{ $client->total_project_value > 0 ? ($client->total_paid / $client->total_project_value) * 100 : 0 }}%;">
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

            // Clickable project rows
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    if (url) {
                        window.location.href = url;
                    }
                });

                // Hover effects
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(139, 92, 246, 0.05)';
                    this.style.transition = 'all 0.2s ease';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });

                // Touch feedback
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

            // Add animation to cards
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

        .stat-card-info::before {
            background: linear-gradient(90deg, #3B82F6, #2563EB);
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #F59E0B, #D97706);
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

        .bg-purple {
            background-color: #8B5CF6 !important;
        }

        .clickable-row {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .progress-bar.bg-purple {
            background: linear-gradient(90deg, #8B5CF6, #A855F7) !important;
        }

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }
        }
    </style>
@endpush
