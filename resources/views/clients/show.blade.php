@extends('layouts.app')
@section('title', 'Detail Klien: ' . $client->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-person-circle"></i>{{ $client->name }}
                </h1>
                <div class="btn-group">
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
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-folder2-open stat-icon"></i>
                    <div class="stat-value">{{ $totalProjects }}</div>
                    <div class="stat-label">Total Proyek</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-graph-up-arrow stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($client->total_project_value, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Nilai Proyek</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-wallet2 stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($client->total_paid, 0, ',', '.') }}</div>
                    <div class="stat-label">Sudah Dibayar</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-clock-history stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($client->total_remaining, 0, ',', '.') }}</div>
                    <div class="stat-label">Sisa Pembayaran</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Client Information -->
        <div class="col-md-8">
            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-info-circle"></i>Informasi Kontak
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Nama Lengkap</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person text-lilac me-2"></i>
                                <strong>{{ $client->name }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Nomor Telepon</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-telephone text-lilac me-2"></i>
                                <strong>{{ $client->phone }}</strong>
                                <a href="{{ $client->whatsapp_link }}" target="_blank" class="btn btn-sm btn-success ms-2">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                        @if ($client->email)
                            <div class="col-md-6">
                                <label class="form-label text-muted">Email</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-envelope text-lilac me-2"></i>
                                    <a href="mailto:{{ $client->email }}" class="text-decoration-none">{{ $client->email }}</a>
                                </div>
                            </div>
                        @endif
                        @if ($client->address)
                            <div class="col-12">
                                <label class="form-label text-muted">Alamat</label>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-geo-alt text-lilac me-2 mt-1"></i>
                                    <p class="mb-0">{{ $client->address }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Project Status Breakdown -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-pie-chart"></i>Status Proyek
                    </h5>

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
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-folder2-open"></i>Proyek Terbaru
                        </h5>
                        <a href="{{ route('projects.create') }}?client={{ $client->id }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Proyek Baru
                        </a>
                    </div>

                    @if ($recentProjects->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($recentProjects as $project)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <strong class="text-lilac">{{ $project->title }}</strong>
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
                                            <!-- Progress Bar -->
                                            <div class="mt-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 100px; height: 6px;">
                                                        <div class="progress-bar"
                                                            style="width: {{ $project->progress_percentage }}%; background: var(--lilac-primary);">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">{{ $project->progress_percentage }}%</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($client->projects->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('projects.index') }}?client_id={{ $client->id }}" class="btn btn-outline-primary">
                                    <i class="bi bi-folder2-open me-2"></i>Lihat Semua Proyek ({{ $client->projects->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-folder-x text-lilac-secondary" style="font-size: 2rem;"></i>
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
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-lightning"></i>Aksi Cepat
                    </h6>

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
            <div class="card">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-graph-up"></i>Ringkasan Kinerja
                    </h6>

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
                            <div class="progress-bar"
                                style="width: {{ $client->total_project_value > 0 ? ($client->total_paid / $client->total_project_value) * 100 : 0 }}%; background: var(--lilac-primary);">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
