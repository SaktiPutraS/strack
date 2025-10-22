@extends('layouts.app')
@section('title', 'Proyek')

@section('content')
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-list-task me-2"></i>Manajemen Proyek
                    </h1>
                    <p class="text-muted mb-0">Kelola semua proyek dan tracking progressnya</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <!-- Export Button -->
                    <button type="button" class="btn btn-outline-success" id="exportExcelBtn">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </button>
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Proyek Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Project Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Statistik Proyek
            </h5>
        </div>
        <div class="col-6 col-xl-2 col-lg-3 col-md-4">
            <div class="card luxury-card stat-card stat-card-warning h-100 clickable-card" data-filter="status=WAITING">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $projectStats['waiting'] }}</h3>
                    <small class="text-muted fw-semibold">Menunggu</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2 col-lg-3 col-md-4">
            <div class="card luxury-card stat-card stat-card-purple h-100 clickable-card" data-filter="status=PROGRESS">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-play-circle-fill text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $projectStats['progress'] }}</h3>
                    <small class="text-muted fw-semibold">Progress</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 col-md-4">
            <div class="card luxury-card stat-card stat-card-success h-100 clickable-card" data-filter="month=current">
                <div class="card-body text-center p-1">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-calendar-month text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1" id="totalNilaiBulanIni">
                        {{ $formatCurrency($totalNilaiBulanIni) }}
                    </h3>
                    <small class="text-muted fw-semibold">Nilai Bulan Ini</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-4">
            <div class="card luxury-card stat-card stat-card-danger h-100 clickable-card" data-filter="piutang=true">
                <div class="card-body text-center p-1">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-cash-coin text-danger fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-danger mb-1" id="totalPiutang">
                        {{ $formatCurrency($totalPiutang) }}
                    </h3>
                    <small class="text-muted fw-semibold">Total Piutang</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-2 col-lg-3 col-md-4">
            <div class="card luxury-card stat-card stat-card-info h-100 clickable-card" data-filter="testimoni=false">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-chat-quote-fill text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $testimoniStats['without_testimoni'] }}</h3>
                    <small class="text-muted fw-semibold">Belum Testimoni</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Search -->
    <div class="card luxury-card border-0 mb-0">
        <div class="card-header bg-white border-0 p-4 pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-folder2-open text-purple"></i>
                        </div>
                        Daftar Proyek
                    </h5>
                </div>
            </div>
        </div>
        <div class="card-body p-4 border-bottom">
            <form method="GET" class="row g-3" id="searchForm">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control form-control-lg" value="{{ request('search') }}"
                        placeholder="Cari proyek, klien, atau deskripsi...">
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>

                <!-- Hidden inputs to maintain sort parameters -->
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="order" value="{{ request('order') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="piutang" value="{{ request('piutang') }}">
                <input type="hidden" name="testimoni" value="{{ request('testimoni') }}">
                <input type="hidden" name="month" value="{{ request('month') }}">
            </form>
        </div>

        <div class="card-body p-0">
            @if ($projects->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'order' => request('sort') == 'title' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-decoration-none text-dark d-flex align-items-center fw-semibold">
                                            <i class="bi bi-folder2-open me-2 text-muted"></i>Proyek
                                            @if (request('sort') == 'title')
                                                @if (request('order') == 'asc')
                                                    <i class="bi bi-arrow-up ms-1 text-purple"></i>
                                                @else
                                                    <i class="bi bi-arrow-down ms-1 text-purple"></i>
                                                @endif
                                            @else
                                                <i class="bi bi-arrow-down-up ms-1 opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'client_id', 'order' => request('sort') == 'client_id' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-decoration-none text-dark d-flex align-items-center fw-semibold">
                                            <i class="bi bi-person me-2 text-muted"></i>Klien
                                            @if (request('sort') == 'client_id')
                                                @if (request('order') == 'asc')
                                                    <i class="bi bi-arrow-up ms-1 text-purple"></i>
                                                @else
                                                    <i class="bi bi-arrow-down ms-1 text-purple"></i>
                                                @endif
                                            @else
                                                <i class="bi bi-arrow-down-up ms-1 opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'deadline', 'order' => request('sort') == 'deadline' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-decoration-none text-dark d-flex align-items-center fw-semibold">
                                            <i class="bi bi-calendar3 me-2 text-muted"></i>Deadline
                                            @if (request('sort') == 'deadline')
                                                @if (request('order') == 'asc')
                                                    <i class="bi bi-arrow-up ms-1 text-purple"></i>
                                                @else
                                                    <i class="bi bi-arrow-down ms-1 text-purple"></i>
                                                @endif
                                            @else
                                                <i class="bi bi-arrow-down-up ms-1 opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'order' => request('sort') == 'status' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-decoration-none text-dark d-flex align-items-center fw-semibold">
                                            <i class="bi bi-flag me-2 text-muted"></i>Status
                                            @if (request('sort') == 'status')
                                                @if (request('order') == 'asc')
                                                    <i class="bi bi-arrow-up ms-1 text-purple"></i>
                                                @else
                                                    <i class="bi bi-arrow-down ms-1 text-purple"></i>
                                                @endif
                                            @else
                                                <i class="bi bi-arrow-down-up ms-1 opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($projects as $project)
                                    <tr class="border-bottom project-row" data-project-id="{{ $project->id }}" style="cursor: pointer;">
                                        <td class="px-4 py-4">
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark">{{ $project->title }}</h6>
                                                <small class="text-muted">{{ $project->type }}</small>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="bi bi-person-fill text-purple"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $project->client->name }}</div>
                                                    @if ($project->remaining_amount > 0)
                                                        <small class="text-info fw-medium">
                                                            <i class="bi bi-cash me-1"></i>Piutang: {{ $project->formatted_remaining_amount }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">Lunas</small>
                                                    @endif

                                                    @if (!$project->testimoni)
                                                        <small class="text-primary fw-medium">
                                                            <i class="bi bi-clock-history me-1"></i>Belum Testimoni
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-date me-2 text-muted"></i>
                                                <div>
                                                    <div class="fw-medium">{{ $project->deadline->format('d M Y') }}</div>
                                                    @if ($project->is_overdue)
                                                        <small class="text-danger fw-medium">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>Terlambat
                                                        </small>
                                                    @elseif($project->is_deadline_near)
                                                        <small class="text-warning fw-medium">
                                                            <i class="bi bi-clock me-1"></i>Deadline Dekat
                                                        </small>
                                                    @else
                                                        <small class="text-muted">Normal</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($project->status == 'WAITING')
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                                    <i class="bi bi-clock me-1"></i>MENUNGGU
                                                </span>
                                            @elseif($project->status == 'PROGRESS')
                                                <span class="badge bg-purple-light text-purple border border-purple">
                                                    <i class="bi bi-play-fill me-1"></i>PROGRESS
                                                </span>
                                            @elseif($project->status == 'FINISHED')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                    <i class="bi bi-check-circle-fill me-1"></i>SELESAI
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                    <i class="bi bi-x-circle-fill me-1"></i>DIBATALKAN
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
                        @foreach ($projects as $project)
                            <div class="card luxury-card project-card mb-3 project-card" data-project-id="{{ $project->id }}"
                                style="cursor: pointer;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold mb-1 flex-grow-1">{{ $project->title }}</h6>
                                        @if ($project->status == 'WAITING')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                                <i class="bi bi-clock me-1"></i>MENUNGGU
                                            </span>
                                        @elseif($project->status == 'PROGRESS')
                                            <span class="badge bg-purple-light text-purple border border-purple">
                                                <i class="bi bi-play-fill me-1"></i>PROGRESS
                                            </span>
                                        @elseif($project->status == 'FINISHED')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                <i class="bi bi-check-circle-fill me-1"></i>SELESAI
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                <i class="bi bi-x-circle-fill me-1"></i>DIBATALKAN
                                            </span>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-purple"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $project->client->name }}</div>
                                            @if ($project->remaining_amount > 0)
                                                <small class="text-info fw-medium">
                                                    <i class="bi bi-cash me-1"></i>Piutang: {{ $project->formatted_remaining_amount }}
                                                </small>
                                            @else
                                                <small class="text-muted">Lunas</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-date me-2 text-muted"></i>
                                            <small class="fw-medium">{{ $project->deadline->format('d M Y') }}</small>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            @if ($project->is_overdue)
                                                <small class="text-danger fw-medium">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Terlambat
                                                </small>
                                            @elseif($project->is_deadline_near)
                                                <small class="text-warning fw-medium">
                                                    <i class="bi bi-clock me-1"></i>Deadline Dekat
                                                </small>
                                            @endif
                                            @if (!$project->testimoni)
                                                <small class="text-primary fw-medium mt-1">
                                                    <i class="bi bi-clock-history me-1"></i>Belum Testimoni
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Enhanced Pagination dengan Page Selector -->
                @if (method_exists($projects, 'links'))
                    <div class="card-footer bg-light border-0 p-4">
                        <div style="display: none;">
                            {{ $projects->links() }}
                        </div>

                        <div class="row align-items-center g-3">
                            <!-- Info Text -->
                            <div class="col-12 col-md-4">
                                <p class="text-muted mb-0 text-center text-md-start">
                                    Menampilkan <strong>{{ $projects->firstItem() }}-{{ $projects->lastItem() }}</strong>
                                    dari <strong>{{ $projects->total() }}</strong> proyek
                                </p>
                            </div>

                            <!-- Page Selector (Mobile & Desktop) -->
                            <div class="col-12 col-md-4">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <label class="text-muted mb-0 small">Halaman:</label>
                                    <select class="form-select form-select-sm" id="pageSelector" style="width: auto; min-width: 80px;">
                                        @for ($i = 1; $i <= $projects->lastPage(); $i++)
                                            <option value="{{ $i }}" {{ $projects->currentPage() == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    <span class="text-muted small">dari {{ $projects->lastPage() }}</span>
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="col-12 col-md-4">
                                <nav class="d-flex justify-content-center justify-content-md-end">
                                    <ul class="pagination mb-0">
                                        <!-- First Page -->
                                        @if ($projects->currentPage() > 1)
                                            <li class="page-item d-none d-md-inline-block">
                                                <a class="page-link" href="{{ $projects->url(1) }}" title="Halaman Pertama">
                                                    <i class="bi bi-chevron-double-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        <!-- Previous Page -->
                                        @if ($projects->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $projects->previousPageUrl() }}" title="Halaman Sebelumnya">
                                                    <i class="bi bi-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        <!-- Current Page Info (Mobile Only) -->
                                        <li class="page-item active d-md-none">
                                            <span class="page-link">{{ $projects->currentPage() }} / {{ $projects->lastPage() }}</span>
                                        </li>

                                        <!-- Page Numbers (Desktop Only) -->
                                        <div class="d-none d-md-flex">
                                            @php
                                                $start = max(1, $projects->currentPage() - 2);
                                                $end = min($projects->lastPage(), $projects->currentPage() + 2);
                                            @endphp

                                            @if ($start > 1)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif

                                            @for ($i = $start; $i <= $end; $i++)
                                                <li class="page-item {{ $projects->currentPage() == $i ? 'active' : '' }}">
                                                    <a class="page-link" href="{{ $projects->url($i) }}">{{ $i }}</a>
                                                </li>
                                            @endfor

                                            @if ($end < $projects->lastPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        </div>

                                        <!-- Next Page -->
                                        @if ($projects->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $projects->nextPageUrl() }}" title="Halaman Selanjutnya">
                                                    <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                                            </li>
                                        @endif

                                        <!-- Last Page -->
                                        @if ($projects->currentPage() < $projects->lastPage())
                                            <li class="page-item d-none d-md-inline-block">
                                                <a class="page-link" href="{{ $projects->url($projects->lastPage()) }}" title="Halaman Terakhir">
                                                    <i class="bi bi-chevron-double-right"></i>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Modern Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-folder-x text-muted" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Tidak ada proyek ditemukan</h5>
                        @if (request('search') || request('status') || request('piutang') || request('testimoni'))
                            <p class="text-muted mb-4">Coba ubah kriteria pencarian atau filter Anda</p>
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-primary me-2">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset Filter
                            </a>
                        @else
                            <p class="text-muted mb-4">Mulai dengan membuat proyek pertama Anda</p>
                        @endif
                        <a href="{{ route('projects.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Buat Proyek Pertama
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Export Excel functionality
            const exportExcelBtn = document.getElementById('exportExcelBtn');

            exportExcelBtn.addEventListener('click', function() {
                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengunduh...';
                this.disabled = true;

                // Get current filter parameters
                const searchForm = document.getElementById('searchForm');
                const formData = new FormData(searchForm);
                const params = new URLSearchParams();

                // Add all form parameters to export URL
                for (let [key, value] of formData.entries()) {
                    if (value) {
                        params.append(key, value);
                    }
                }

                // Create export URL
                const exportUrl = '{{ route('projects.export.excel') }}?' + params.toString();

                // Create temporary link to download
                const link = document.createElement('a');
                link.href = exportUrl;
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Reset button state after delay
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            });

            // Clickable statistics cards
            const clickableCards = document.querySelectorAll('.clickable-card');

            clickableCards.forEach(card => {
                card.style.cursor = 'pointer';

                // Touch feedback for mobile
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                    this.style.transition = 'transform 0.1s ease';
                }, {
                    passive: true
                });

                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                    this.style.transition = 'transform 0.2s ease';
                }, {
                    passive: true
                });

                // Click handler
                card.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    if (filter) {
                        // Add loading state
                        this.style.opacity = '0.7';

                        // Navigate
                        setTimeout(() => {
                            window.location.href = `{{ route('projects.index') }}?${filter}`;
                        }, 100);
                    }
                });

                // Hover effect for desktop
                card.addEventListener('mouseenter', function() {
                    if (window.innerWidth > 768) {
                        this.style.transform = 'translateY(-4px)';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    if (window.innerWidth > 768) {
                        this.style.transform = 'translateY(0)';
                    }
                });
            });

            // Single click to navigate to project detail
            const projectRows = document.querySelectorAll('[data-project-id]');

            projectRows.forEach(row => {
                row.addEventListener('click', function() {
                    const projectId = this.getAttribute('data-project-id');
                    window.location.href = `{{ url('projects') }}/${projectId}`;
                });

                // Hover effects
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

            // Search handling
            const searchInput = document.querySelector('input[name="search"]');
            const searchForm = document.querySelector('form');
            const searchBtn = searchForm.querySelector('button[type="submit"]');

            // Add loading state to search form
            searchForm.addEventListener('submit', function() {
                searchBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Mencari...';
                searchBtn.disabled = true;
            });

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

            // Format currency for total piutang
            const totalPiutangElement = document.getElementById('totalPiutang');
            if (totalPiutangElement) {
                const formatCurrency = function(amount) {
                    if (amount >= 1000000000) {
                        return 'Rp ' + (amount / 1000000000).toFixed(1).replace('.', ',') + 'M';
                    } else if (amount >= 1000000) {
                        return 'Rp ' + (amount / 1000000).toFixed(1).replace('.', ',') + 'Jt';
                    }
                    return 'Rp ' + amount.toLocaleString('id-ID');
                };

                // Apply formatting if needed
                const originalText = totalPiutangElement.textContent.trim();
                if (!originalText.includes('Rp')) {
                    const amount = parseInt(originalText.replace(/[^\d]/g, ''));
                    if (!isNaN(amount)) {
                        totalPiutangElement.textContent = formatCurrency(amount);
                    }
                }
            }

            // ===== ENHANCED PAGINATION FUNCTIONALITY =====

            // Page Selector Dropdown
            const pageSelector = document.getElementById('pageSelector');
            if (pageSelector) {
                pageSelector.addEventListener('change', function() {
                    const selectedPage = this.value;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('page', selectedPage);

                    // Smooth scroll to top
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                    // Navigate after a short delay
                    setTimeout(() => {
                        window.location.href = currentUrl.toString();
                    }, 200);
                });
            }

            // Jump to Page functionality
            const jumpToPageBtn = document.getElementById('jumpToPageBtn');
            const jumpToPageInput = document.getElementById('jumpToPage');

            if (jumpToPageBtn && jumpToPageInput) {
                jumpToPageBtn.addEventListener('click', function() {
                    const pageNumber = parseInt(jumpToPageInput.value);
                    const maxPage = parseInt(jumpToPageInput.getAttribute('max'));
                    const minPage = parseInt(jumpToPageInput.getAttribute('min'));

                    if (pageNumber >= minPage && pageNumber <= maxPage) {
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('page', pageNumber);

                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                        setTimeout(() => {
                            window.location.href = currentUrl.toString();
                        }, 200);
                    } else {
                        alert(`Masukkan nomor halaman antara ${minPage} dan ${maxPage}`);
                    }
                });

                // Allow Enter key to jump
                jumpToPageInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        jumpToPageBtn.click();
                    }
                });
            }
        });
    </script>

    <style>
        /* Export button styling */
        .btn-outline-success {
            border-color: #10B981;
            color: #10B981;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .btn-outline-success:hover {
            background-color: #10B981;
            border-color: #10B981;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.25);
        }

        .btn-outline-success:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }

        /* Stat card styling dengan border atas berwarna */
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
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

        /* Border colors untuk setiap card */
        .stat-card-warning::before {
            background: linear-gradient(90deg, #FFC107, #FF9800);
        }

        .stat-card-purple::before {
            background: linear-gradient(90deg, #8B5CF6, #A855F7);
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10B981, #059669);
        }

        .stat-card-info::before {
            background: linear-gradient(90deg, #06B6D4, #0891B2);
        }

        .stat-card-secondary::before {
            background: linear-gradient(90deg, #6C757D, #5A6268);
        }

        .stat-card-danger::before {
            background: linear-gradient(90deg, #DC3545, #C82333);
        }

        /* Hover effect */
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

        /* Enhanced table styling */
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

        /* Badge */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Enhanced Pagination Styles */
        #pageSelector {
            border: 1px solid rgba(139, 92, 246, 0.3);
            color: #8B5CF6;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        #pageSelector:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        #pageSelector:hover {
            border-color: #8B5CF6;
        }

        /* Page number buttons */
        .pagination .page-link {
            color: #8B5CF6;
            border: 1px solid rgba(139, 92, 246, 0.2);
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 2px;
        }

        .pagination .page-link:hover {
            background-color: rgba(139, 92, 246, 0.1);
            border-color: #8B5CF6;
            color: #8B5CF6;
        }

        .pagination .page-item.active .page-link {
            background-color: #8B5CF6;
            border-color: #8B5CF6;
            color: white;
            font-weight: 600;
        }

        .pagination .page-item.disabled .page-link {
            color: #9CA3AF;
            border-color: #E5E7EB;
        }

        /* Jump to page input */
        #jumpToPage {
            border: 1px solid rgba(139, 92, 246, 0.3);
            text-align: center;
            border-radius: 8px;
        }

        #jumpToPage:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        .btn-outline-purple {
            border-color: #8B5CF6;
            color: #8B5CF6;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-purple:hover {
            background-color: #8B5CF6;
            border-color: #8B5CF6;
            color: white;
        }

        /* Button */
        .btn-outline-primary {
            border-color: #8B5CF6;
            color: #8B5CF6;
        }

        .btn-outline-primary:hover {
            background-color: #8B5CF6;
            color: white;
        }

        /* Clickable cards cursor */
        .clickable-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .clickable-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.15);
        }

        /* Project rows */
        .project-row,
        .project-card {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .project-row:hover,
        .project-card:hover {
            background-color: rgba(139, 92, 246, 0.05) !important;
        }

        /* Mobile card styling dengan border kiri */
        .project-card {
            border: 1px solid rgba(139, 92, 246, 0.1);
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

        .project-card:hover {
            border-color: rgba(139, 92, 246, 0.2);
            transform: translateY(-2px);
        }

        .project-card:hover::before {
            width: 6px;
        }

        /* Badge color for purple theme */
        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.1) !important;
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .border-purple {
            border-color: rgba(139, 92, 246, 0.3) !important;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }

            .clickable-card:hover {
                transform: none !important;
            }

            .project-card:hover {
                transform: none !important;
            }

            .pagination .page-link {
                padding: 0.4rem 0.6rem;
                font-size: 0.9rem;
            }

            #pageSelector {
                font-size: 0.9rem;
            }
        }

        /* Touch feedback */
        .clickable-card:active,
        .project-row:active,
        .project-card:active {
            transform: scale(0.98);
        }

        /* Animation for page change */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-footer {
            animation: fadeIn 0.3s ease;
        }
    </style>
@endpush
