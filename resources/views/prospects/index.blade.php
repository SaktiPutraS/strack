@extends('layouts.app')
@section('title', 'Manajemen Prospek')

@section('content')
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-people me-2"></i>Manajemen Prospek
                    </h1>
                    <p class="text-muted mb-0">Kelola data calon customer dan tracking status penawaran</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('prospects.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Prospek
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Statistik Prospek
            </h5>
        </div>
        <div class="col-6 col-xl-2 col-lg-4 col-md-4">
            <div class="card luxury-card stat-card stat-card-secondary h-100 clickable-card" data-filter="status=BELUM_DIHUBUNGI">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clock-history text-secondary fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-secondary mb-1">{{ $stats['belum_dihubungi'] }}</h3>
                    <small class="text-muted fw-semibold">Belum Dihubungi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2 col-lg-4 col-md-4">
            <div class="card luxury-card stat-card stat-card-info h-100 clickable-card" data-filter="status=PENGECEKAN_KEAKTIFAN">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-search text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $stats['pengecekan'] }}</h3>
                    <small class="text-muted fw-semibold">Pengecekan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2 col-lg-4 col-md-4">
            <div class="card luxury-card stat-card stat-card-warning h-100 clickable-card" data-filter="status=PENAWARAN">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-envelope-paper text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $stats['penawaran'] }}</h3>
                    <small class="text-muted fw-semibold">Penawaran</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2 col-lg-4 col-md-4">
            <div class="card luxury-card stat-card stat-card-purple h-100 clickable-card" data-filter="status=FOLLOW_UP">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-telephone-forward text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $stats['follow_up'] }}</h3>
                    <small class="text-muted fw-semibold">Follow Up</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2 col-lg-4 col-md-4">
            <div class="card luxury-card stat-card stat-card-danger h-100 clickable-card" data-filter="status=TOLAK">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-x-circle text-danger fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-danger mb-1">{{ $stats['tolak'] }}</h3>
                    <small class="text-muted fw-semibold">Ditolak</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2 col-lg-4 col-md-4">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-people-fill text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $stats['total'] }}</h3>
                    <small class="text-muted fw-semibold">Total Prospek</small>
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
                            <i class="bi bi-list-ul text-purple"></i>
                        </div>
                        Daftar Prospek
                    </h5>
                </div>
            </div>
        </div>
        <div class="card-body p-4 border-bottom">
            <form method="GET" class="row g-3" id="searchForm">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control form-control-lg" value="{{ request('search') }}"
                        placeholder="Cari nama, WhatsApp, alamat, atau keterangan...">
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                        <a href="{{ route('prospects.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>

                <!-- Hidden inputs -->
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="order" value="{{ request('order') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
            </form>
        </div>

        <div class="card-body p-0">
            @if ($prospects->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => request('sort') == 'name' && request('order') == 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-decoration-none text-dark d-flex align-items-center fw-semibold">
                                            <i class="bi bi-person me-2 text-muted"></i>Nama
                                            @if (request('sort') == 'name')
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
                                        <div class="text-dark d-flex align-items-center fw-semibold">
                                            <i class="bi bi-whatsapp me-2 text-muted"></i>WhatsApp
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <div class="text-dark d-flex align-items-center fw-semibold">
                                            <i class="bi bi-geo-alt me-2 text-muted"></i>Alamat
                                        </div>
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
                                    <th class="px-4 py-3 border-0 text-center">
                                        <div class="text-dark d-flex align-items-center justify-content-center fw-semibold">
                                            <i class="bi bi-gear me-2 text-muted"></i>Aksi
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prospects as $prospect)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-4">
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark">{{ $prospect->name }}</h6>
                                                @if ($prospect->social_media)
                                                    <small class="text-muted">
                                                        <i class="bi bi-link-45deg me-1"></i>
                                                        <a href="{{ $prospect->social_media }}" target="_blank" class="text-decoration-none">
                                                            Link Sosmed
                                                        </a>
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <a href="{{ $prospect->whatsapp_link }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-whatsapp me-1"></i>{{ $prospect->whatsapp }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-4">
                                            <small class="text-muted">{{ $prospect->address ?? '-' }}</small>
                                        </td>
                                        <td class="px-4 py-4">
                                            @php
                                                $colorMap = [
                                                    'secondary' => 'secondary',
                                                    'info' => 'info',
                                                    'warning' => 'warning',
                                                    'purple' => 'purple',
                                                    'danger' => 'danger',
                                                ];
                                                $color = $colorMap[$prospect->status_color] ?? 'secondary';
                                            @endphp
                                            <span
                                                class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }}">
                                                {{ $prospect->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="{{ route('prospects.show', $prospect) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('prospects.edit', $prospect) }}" class="btn btn-sm btn-outline-warning"
                                                    title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('prospects.destroy', $prospect) }}" method="POST"
                                                    class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
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
                        @foreach ($prospects as $prospect)
                            <div class="card luxury-card mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold mb-1 flex-grow-1">{{ $prospect->name }}</h6>
                                        @php
                                            $colorMap = [
                                                'secondary' => 'secondary',
                                                'info' => 'info',
                                                'warning' => 'warning',
                                                'purple' => 'purple',
                                                'danger' => 'danger',
                                            ];
                                            $color = $colorMap[$prospect->status_color] ?? 'secondary';
                                        @endphp
                                        <span
                                            class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }}">
                                            {{ $prospect->status_label }}
                                        </span>
                                    </div>

                                    <div class="mb-2">
                                        <a href="{{ $prospect->whatsapp_link }}" target="_blank" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-whatsapp me-1"></i>{{ $prospect->whatsapp }}
                                        </a>
                                    </div>

                                    @if ($prospect->address)
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $prospect->address }}
                                            </small>
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2 mt-3">
                                        <a href="{{ route('prospects.show', $prospect) }}" class="btn btn-sm btn-outline-info flex-fill">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </a>
                                        <a href="{{ route('prospects.edit', $prospect) }}" class="btn btn-sm btn-outline-warning flex-fill">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </a>
                                        <form action="{{ route('prospects.destroy', $prospect) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if (method_exists($prospects, 'links'))
                    <div class="card-footer bg-light border-0 p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-muted mb-0">
                                    Menampilkan <strong>{{ $prospects->firstItem() }}-{{ $prospects->lastItem() }}</strong>
                                    dari <strong>{{ $prospects->total() }}</strong> prospek
                                </p>
                            </div>
                            <div class="col-md-6">
                                <nav class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                                    <ul class="pagination mb-0">
                                        @if ($prospects->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $prospects->previousPageUrl() }}">
                                                    <i class="bi bi-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        <li class="page-item active">
                                            <span class="page-link">{{ $prospects->currentPage() }} / {{ $prospects->lastPage() }}</span>
                                        </li>

                                        @if ($prospects->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $prospects->nextPageUrl() }}">
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
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-people text-muted" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Belum ada prospek</h5>
                        @if (request('search') || request('status'))
                            <p class="text-muted mb-4">Coba ubah kriteria pencarian atau filter Anda</p>
                            <a href="{{ route('prospects.index') }}" class="btn btn-outline-primary me-2">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset Filter
                            </a>
                        @else
                            <p class="text-muted mb-4">Mulai dengan menambahkan prospek pertama Anda</p>
                        @endif
                        <a href="{{ route('prospects.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Prospek
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

                card.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    if (filter) {
                        this.style.opacity = '0.7';
                        setTimeout(() => {
                            window.location.href = `{{ route('prospects.index') }}?${filter}`;
                        }, 100);
                    }
                });

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

            // Delete confirmation
            const deleteForms = document.querySelectorAll('.delete-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Prospek?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#DC3545',
                        cancelButtonColor: '#6C757D',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });

            // Search form submit button loading state
            const searchForm = document.getElementById('searchForm');
            const searchBtn = searchForm.querySelector('button[type="submit"]');

            searchForm.addEventListener('submit', function() {
                searchBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Mencari...';
                searchBtn.disabled = true;
            });

            // Animation for statistics cards
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

        .stat-card-secondary::before {
            background: linear-gradient(90deg, #6C757D, #5A6268);
        }

        .stat-card-info::before {
            background: linear-gradient(90deg, #06B6D4, #0891B2);
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #FFC107, #FF9800);
        }

        .stat-card-purple::before {
            background: linear-gradient(90deg, #8B5CF6, #A855F7);
        }

        .stat-card-danger::before {
            background: linear-gradient(90deg, #DC3545, #C82333);
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10B981, #059669);
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

        .bg-purple {
            background-color: #8B5CF6 !important;
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .border-purple {
            border-color: rgba(139, 92, 246, 0.3) !important;
        }

        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.1) !important;
        }

        .clickable-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }

            .clickable-card:hover {
                transform: none !important;
            }
        }
    </style>
@endpush
