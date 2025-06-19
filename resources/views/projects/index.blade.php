@extends('layouts.app')
@section('title', 'Daftar Proyek')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-list-task"></i>Daftar Proyek
                </h1>
                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Proyek Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-clock stat-icon"></i>
                    <div class="stat-value">{{ $projects->where('status', 'WAITING')->count() }}</div>
                    <div class="stat-label">Menunggu</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-play-circle stat-icon"></i>
                    <div class="stat-value">{{ $projects->where('status', 'PROGRESS')->count() }}</div>
                    <div class="stat-label">Dalam Progress</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-check-circle stat-icon"></i>
                    <div class="stat-value">{{ $projects->where('status', 'FINISHED')->count() }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-x-circle stat-icon"></i>
                    <div class="stat-value">{{ $projects->where('status', 'CANCELLED')->count() }}</div>
                    <div class="stat-label">Dibatalkan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari proyek atau klien..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="WAITING" {{ request('status') == 'WAITING' ? 'selected' : '' }}>Menunggu</option>
                                <option value="PROGRESS" {{ request('status') == 'PROGRESS' ? 'selected' : '' }}>Progress</option>
                                <option value="FINISHED" {{ request('status') == 'FINISHED' ? 'selected' : '' }}>Selesai</option>
                                <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">Semua Tipe</option>
                                @if (isset($projectTypes))
                                    @foreach ($projectTypes as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="client_id" class="form-select">
                                <option value="">Semua Klien</option>
                                @if (isset($clients))
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-folder2-open"></i>Proyek ({{ $projects->total() ?? $projects->count() }} total)
                    </h5>

                    @if ($projects->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($projects as $project)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-folder2-open text-lilac me-3 mt-1" style="font-size: 1.5rem;"></i>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 text-lilac">{{ $project->title }}</h6>
                                                <div class="d-flex gap-2">
                                                    @if ($project->status == 'WAITING')
                                                        <span class="badge badge-warning">MENUNGGU</span>
                                                    @elseif($project->status == 'PROGRESS')
                                                        <span class="badge" style="background: var(--lilac-primary); color: white;">PROGRESS</span>
                                                    @elseif($project->status == 'FINISHED')
                                                        <span class="badge badge-success">SELESAI</span>
                                                    @else
                                                        <span class="badge badge-danger">DIBATALKAN</span>
                                                    @endif
                                                    <span class="badge bg-secondary">{{ $project->type }}</span>
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-person text-muted me-2"></i>
                                                        <small class="text-muted">{{ $project->client->name }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-calendar3 text-muted me-2"></i>
                                                        <small class="text-muted">{{ $project->deadline->format('d M Y') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-currency-dollar text-muted me-2"></i>
                                                        <small class="text-muted">{{ $project->formatted_total_value }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-graph-up text-muted me-2"></i>
                                                        <small class="text-muted">{{ $project->progress_percentage }}% lunas</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Progress Bar -->
                                            <div class="progress mb-2" style="height: 8px;">
                                                <div class="progress-bar"
                                                    style="width: {{ $project->progress_percentage }}%; background: var(--lilac-primary);"
                                                    role="progressbar" aria-valuenow="{{ $project->progress_percentage }}" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex gap-2">
                                                    @if ($project->is_overdue)
                                                        <span class="badge badge-danger">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>Terlambat
                                                        </span>
                                                    @elseif($project->is_deadline_near)
                                                        <span class="badge badge-warning">
                                                            <i class="bi bi-clock me-1"></i>Deadline Dekat
                                                        </span>
                                                    @endif

                                                    @if ($project->has_testimonial)
                                                        <span class="badge badge-success">
                                                            <i class="bi bi-star me-1"></i>Ada Testimoni
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if (method_exists($projects, 'links'))
                            <div class="d-flex justify-content-center mt-4">
                                {{ $projects->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-folder-x text-lilac-secondary" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada proyek ditemukan</p>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Buat Proyek Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
