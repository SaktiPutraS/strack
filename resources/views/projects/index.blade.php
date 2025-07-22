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
                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Proyek Baru
                </a>
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
            <div class="card luxury-card stat-card stat-card-secondary h-100 clickable-card" data-filter="month=current">
                <div class="card-body text-center p-1">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-calendar-month text-secondary fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-secondary mb-1" id="totalNilaiBulanIni">
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
            <form method="GET" class="row g-3">
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

                <!-- Enhanced Pagination -->
                @if (method_exists($projects, 'links'))
                    <div class="card-footer bg-light border-0 p-4">
                        <div style="display: none;">
                            {{ $projects->links() }}
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-muted mb-0">
                                    Menampilkan <strong>{{ $projects->firstItem() }}-{{ $projects->lastItem() }}</strong>
                                    dari <strong>{{ $projects->total() }}</strong> proyek
                                </p>
                            </div>
                            <div class="col-md-6">
                                <nav class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                                    <ul class="pagination mb-0">
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

                                        <!-- Show current page info -->
                                        <li class="page-item active">
                                            <span class="page-link">{{ $projects->currentPage() }} / {{ $projects->lastPage() }}</span>
                                        </li>

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

            // PERBAIKAN: Search handling tanpa auto-submit bermasalah
            const searchInput = document.querySelector('input[name="search"]');
            const searchForm = document.querySelector('form');
            const searchBtn = searchForm.querySelector('button[type="submit"]');

            // DEBUGGING: Cek apakah ada masalah dengan form action atau method
            console.log('Form action:', searchForm.action);
            console.log('Form method:', searchForm.method);

            // Alternative: Debug Enter key
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    console.log('Enter pressed - akan submit form');
                    // Biarkan browser handle submit secara natural
                    // Jangan preventDefault atau manual submit
                }
            });

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
        });
    </script>

    <style>
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

        /* Pagination */
        .pagination .page-link {
            color: #8B5CF6;
            border: 1px solid rgba(139, 92, 246, 0.2);
        }

        .pagination .page-item.active .page-link {
            background-color: #8B5CF6;
            border-color: #8B5CF6;
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
        }

        /* Touch feedback */
        .clickable-card:active,
        .project-row:active,
        .project-card:active {
            transform: scale(0.98);
        }
    </style>
@endpush
