@extends('layouts.app')
@section('title', 'Status Testimoni')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-check-square"></i>Status Testimoni
                </h1>
            </div>
            <p class="text-muted">Penanda proyek yang sudah/belum memiliki testimoni</p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $projectsWithTestimonial ?? 0 }}</h3>
                    <p class="mb-0 text-muted">Sudah Ada Testimoni</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $projectsWithoutTestimonial ?? 0 }}</h3>
                    <p class="mb-0 text-muted">Belum Ada Testimoni</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $finishedProjectsReady ?? 0 }}</h3>
                    <p class="mb-0 text-muted">Siap Dibuat Testimoni</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
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
                            <select name="has_testimonial" class="form-select">
                                <option value="">Semua</option>
                                <option value="1" {{ request('has_testimonial') == '1' ? 'selected' : '' }}>Ada Testimoni</option>
                                <option value="0" {{ request('has_testimonial') == '0' ? 'selected' : '' }}>Belum Testimoni</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="FINISHED" {{ request('status') == 'FINISHED' ? 'selected' : '' }}>Selesai</option>
                                <option value="PROGRESS" {{ request('status') == 'PROGRESS' ? 'selected' : '' }}>Progress</option>
                                <option value="WAITING" {{ request('status') == 'WAITING' ? 'selected' : '' }}>Menunggu</option>
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
                        <i class="bi bi-list-check"></i>Status Testimoni Proyek ({{ $projects->total() ?? $projects->count() }} total)
                    </h5>

                    @if (isset($projects) && $projects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Proyek</th>
                                        <th>Klien</th>
                                        <th>Status Proyek</th>
                                        <th>Selesai</th>
                                        <th>Status Testimoni</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projects as $project)
                                        <tr>
                                            <td>
                                                <strong class="text-lilac">{{ $project->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $project->type }}</small>
                                            </td>
                                            <td>{{ $project->client->name }}</td>
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
                                                @if ($project->status == 'FINISHED')
                                                    {{ $project->updated_at->format('d M Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($project->has_testimonial)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Sudah Ada
                                                    </span>
                                                @else
                                                    @if ($project->status == 'FINISHED')
                                                        <span class="badge bg-warning">
                                                            <i class="bi bi-exclamation-triangle"></i> Perlu Dibuat
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="bi bi-minus-circle"></i> Belum Siap
                                                        </span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if ($project->has_testimonial)
                                                    @if ($project->testimonial)
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('testimonials.edit', $project->testimonial) }}"
                                                                class="btn btn-sm btn-outline-primary" title="Edit Testimoni">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                onclick="removeTestimonial({{ $project->testimonial->id }})" title="Hapus Testimoni">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                @else
                                                    @if ($project->status == 'FINISHED')
                                                        <a href="{{ route('testimonials.create', ['project' => $project->id]) }}"
                                                            class="btn btn-sm btn-success">
                                                            <i class="bi bi-plus"></i> Buat Testimoni
                                                        </a>
                                                    @else
                                                        <small class="text-muted">Selesaikan proyek dulu</small>
                                                    @endif
                                                @endif
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
                            <i class="bi bi-list-check text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada proyek ditemukan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function removeTestimonial(testimonialId) {
            if (confirm('Apakah Anda yakin ingin menghapus testimoni ini?\n\nProyek akan ditandai sebagai belum memiliki testimoni.')) {
                fetch(`{{ route('testimonials.destroy', ':id') }}`.replace(':id', testimonialId), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Gagal menghapus testimoni');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan');
                    });
            }
        }
    </script>
@endpush
