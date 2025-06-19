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

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $projects->where('status', 'WAITING')->count() }}</h3>
                    <p class="mb-0 text-muted">Menunggu</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $projects->where('status', 'PROGRESS')->count() }}</h3>
                    <p class="mb-0 text-muted">Progress</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $projects->where('status', 'FINISHED')->count() }}</h3>
                    <p class="mb-0 text-muted">Selesai</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-muted">{{ $projects->where('has_testimonial', true)->count() }}</h3>
                    <p class="mb-0 text-muted">Ada Testimoni</p>
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
                            <select name="has_testimonial" class="form-select">
                                <option value="">Semua Testimoni</option>
                                <option value="1" {{ request('has_testimonial') == '1' ? 'selected' : '' }}>Ada Testimoni</option>
                                <option value="0" {{ request('has_testimonial') == '0' ? 'selected' : '' }}>Belum Testimoni</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="client_id" class="form-select">
                                <option value="">Semua Klien</option>
                                @if (isset($clients))
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
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
                                        <th>Testimoni</th>
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
                                                @if ($project->has_testimonial)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Ada
                                                    </span>
                                                @else
                                                    @if ($project->status == 'FINISHED')
                                                        <a href="{{ route('testimonials.create', ['project' => $project->id]) }}"
                                                            class="badge bg-warning text-decoration-none">
                                                            <i class="bi bi-plus"></i> Buat
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="bi bi-minus"></i> N/A
                                                        </span>
                                                    @endif
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
                            <div class="d-flex justify-content-center mt-4">
                                {{ $projects->links() }}
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
