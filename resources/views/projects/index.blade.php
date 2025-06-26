@extends('layouts.app')
@section('title', 'Proyek')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-list-task"></i>Proyek
                </h1>
                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Proyek Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats - ✅ FIXED: Now shows total from ALL projects -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $projectStats['waiting'] }}</h3>
                    <p class="mb-0 text-muted">Menunggu</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $projectStats['progress'] }}</h3>
                    <p class="mb-0 text-muted">Progress</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $projectStats['finished'] }}</h3>
                    <p class="mb-0 text-muted">Selesai</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-danger">{{ $projectStats['cancelled'] }}</h3>
                    <p class="mb-0 text-muted">Dibatalkan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Simplified Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Cari proyek atau klien..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="WAITING" {{ request('status') == 'WAITING' ? 'selected' : '' }}>Menunggu</option>
                                <option value="PROGRESS" {{ request('status') == 'PROGRESS' ? 'selected' : '' }}>Progress</option>
                                <option value="FINISHED" {{ request('status') == 'FINISHED' ? 'selected' : '' }}>Selesai</option>
                                <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Cari
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
                        <i class="bi bi-folder2-open"></i>Daftar Proyek ({{ $projects->total() ?? $projects->count() }} total)
                    </h5>

                    @if ($projects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Proyek</th>
                                        <th>Klien</th>
                                        <th>Tipe</th>
                                        <th>Nilai</th>
                                        <th>Progress</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projects as $project)
                                        <tr>
                                            <td>
                                                <strong class="text-lilac">{{ $project->title }}</strong>
                                            </td>
                                            <td>{{ $project->client->name }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $project->type }}</span>
                                            </td>
                                            <td>{{ $project->formatted_total_value }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 60px; height: 6px;">
                                                        <div class="progress-bar bg-lilac" style="width: {{ $project->progress_percentage }}%"></div>
                                                    </div>
                                                    <small>{{ $project->progress_percentage }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $project->deadline->format('d M Y') }}
                                                @if ($project->is_overdue)
                                                    <br><small class="text-danger">Terlambat</small>
                                                @elseif($project->is_deadline_near)
                                                    <br><small class="text-warning">Deadline Dekat</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($project->status == 'WAITING')
                                                    <span class="badge bg-warning">MENUNGGU</span>
                                                @elseif($project->status == 'PROGRESS')
                                                    <span class="badge bg-primary">PROGRESS</span>
                                                @elseif($project->status == 'FINISHED')
                                                    <span class="badge bg-success">SELESAI</span>
                                                @else
                                                    <span class="badge bg-danger">DIBATALKAN</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary"
                                                        title="Lihat">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary"
                                                        title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if (method_exists($projects, 'links'))
                            <div class="mt-4">
                                <div style="display: none;">
                                    {{ $projects->links() }}
                                </div>

                                <div class="pagination-info-alt">
                                    Menampilkan {{ $projects->firstItem() }}-{{ $projects->lastItem() }}
                                    dari {{ $projects->total() }} proyek
                                </div>

                                <nav class="bootstrap-pagination">
                                    <ul class="pagination">
                                        @if ($projects->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $projects->previousPageUrl() }}">
                                                    <i class="bi bi-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        @for ($i = 1; $i <= $projects->lastPage(); $i++)
                                            @if ($i == $projects->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $i }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $projects->url($i) }}">{{ $i }}</a>
                                                </li>
                                            @endif
                                        @endfor

                                        @if ($projects->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $projects->nextPageUrl() }}">
                                                    <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-folder-x text-muted" style="font-size: 3rem;"></i>
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
