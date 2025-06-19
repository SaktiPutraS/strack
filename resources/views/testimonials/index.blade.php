@extends('layouts.app')
@section('title', 'Daftar Testimoni')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-star"></i>Daftar Testimoni
                </h1>
                <a href="{{ route('testimonials.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Testimoni
                </a>
            </div>
        </div>
    </div>

    <!-- Testimonial Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-star-fill stat-icon"></i>
                    <div class="stat-value">{{ $totalTestimonials ?? 0 }}</div>
                    <div class="stat-label">Total Testimoni</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-graph-up stat-icon"></i>
                    <div class="stat-value">{{ number_format($averageRating ?? 0, 1) }}</div>
                    <div class="stat-label">Rating Rata-rata</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-clock stat-icon"></i>
                    <div class="stat-value">{{ $draftTestimonials ?? 0 }}</div>
                    <div class="stat-label">Draft</div>
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
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Cari klien atau proyek..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="rating" class="form-select">
                                <option value="">Semua Rating</option>
                                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Bintang</option>
                                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Bintang</option>
                                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Bintang</option>
                                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Bintang</option>
                                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Bintang</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="is_published" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('is_published') == '1' ? 'selected' : '' }}>Dipublikasikan</option>
                                <option value="0" {{ request('is_published') == '0' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="project_type" class="form-select">
                                <option value="">Semua Tipe Proyek</option>
                                @if (isset($projectTypes))
                                    @foreach ($projectTypes as $type)
                                        <option value="{{ $type }}" {{ request('project_type') == $type ? 'selected' : '' }}>
                                            {{ $type }}</option>
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

    <!-- Testimonials List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-chat-quote"></i>Testimoni Klien ({{ $testimonials->total() ?? $testimonials->count() }} total)
                    </h5>

                    @if (isset($testimonials) && $testimonials->count() > 0)
                        <div class="row g-4">
                            @foreach ($testimonials as $testimonial)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 {{ $testimonial->is_published ? 'border-success' : 'border-warning' }}">
                                        <div class="card-body d-flex flex-column">
                                            <!-- Header -->
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title text-lilac mb-1">{{ $testimonial->project->client->name }}</h6>
                                                    <small class="text-muted">{{ $testimonial->project->title }}</small>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('testimonials.edit', $testimonial) }}">
                                                                <i class="bi bi-pencil me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item" onclick="togglePublish({{ $testimonial->id }})">
                                                                <i class="bi bi-{{ $testimonial->is_published ? 'eye-slash' : 'eye' }} me-2"></i>
                                                                {{ $testimonial->is_published ? 'Sembunyikan' : 'Publikasikan' }}
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item text-danger"
                                                                onclick="deleteTestimonial({{ $testimonial->id }})">
                                                                <i class="bi bi-trash me-2"></i>Hapus
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Rating -->
                                            <div class="mb-3">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= $testimonial->rating ? '-fill text-warning' : ' text-muted' }}"></i>
                                                @endfor
                                                <span class="ms-2 text-muted small">({{ $testimonial->rating }}/5)</span>
                                            </div>

                                            <!-- Content -->
                                            <blockquote class="blockquote flex-grow-1">
                                                <p class="mb-0 small">"{{ $testimonial->content }}"</p>
                                            </blockquote>

                                            <!-- Footer -->
                                            <div class="mt-3 pt-3 border-top">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge bg-secondary">{{ $testimonial->project->type }}</span>
                                                        @if ($testimonial->is_published)
                                                            <span class="badge badge-success">
                                                                <i class="bi bi-eye me-1"></i>Publik
                                                            </span>
                                                        @else
                                                            <span class="badge badge-warning">
                                                                <i class="bi bi-clock me-1"></i>Draft
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">{{ $testimonial->created_at->format('d M Y') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if (method_exists($testimonials, 'links'))
                            <div class="d-flex justify-content-center mt-4">
                                {{ $testimonials->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-chat-quote text-lilac-secondary" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada testimoni ditemukan</p>
                            <a href="{{ route('testimonials.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Testimoni Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Published Testimonials Preview -->
    @if (isset($publishedTestimonialsPreview) && $publishedTestimonialsPreview->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="section-title">
                            <i class="bi bi-globe"></i>Preview Testimoni Publik
                        </h5>
                        <p class="text-muted mb-4">Tampilan testimoni yang akan dilihat oleh calon klien</p>

                        <div class="row g-3">
                            @foreach ($publishedTestimonialsPreview as $testimonial)
                                <div class="col-md-6">
                                    <div class="card bg-lilac-soft border-0">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="bi bi-person-fill text-lilac"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $testimonial->project->client->name }}</h6>
                                                    <small class="text-muted">{{ $testimonial->project->type }}</small>
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= $testimonial->rating ? '-fill text-warning' : ' text-muted' }}"></i>
                                                @endfor
                                            </div>

                                            <p class="mb-0 small">"{{ Str::limit($testimonial->content, 150) }}"</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function togglePublish(testimonialId) {
            fetch(`{{ route('testimonials.publish', ':id') }}`.replace(':id', testimonialId), {
                    method: 'PATCH',
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
                        alert('Gagal mengubah status publikasi');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        }

        function deleteTestimonial(testimonialId) {
            if (confirm('Apakah Anda yakin ingin menghapus testimoni ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
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

        // Auto-refresh published testimonials preview
        setInterval(function() {
            // Optional: Auto-refresh functionality
        }, 30000); // 30 seconds
    </script>
@endpush
