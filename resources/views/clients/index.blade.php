@extends('layouts.app')
@section('title', 'Daftar Klien')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-people me-2"></i>Daftar Klien
                    </h1>
                    <p class="text-muted mb-0">Kelola semua informasi klien Anda</p>
                </div>
                <div>
                    <a href="{{ route('clients.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Klien Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="search" class="form-label text-muted fw-semibold">
                                <i class="bi bi-search me-1"></i>Pencarian
                            </label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Cari berdasarkan nama, telepon, atau email..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i>Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Statistik Klien
            </h5>
        </div>

        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card stat-card-purple h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-people text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $clients->total() ?? 0 }}</h3>
                    <small class="text-muted fw-semibold">Total Klien</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-star-fill text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $clients->where('has_testimonial', true)->count() ?? 0 }}</h3>
                    <small class="text-muted fw-semibold">Testimoni</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card stat-card-info h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-list-task text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $clients->sum(function($c) { return $c->projects->count(); }) ?? 0 }}</h3>
                    <small class="text-muted fw-semibold">Total Proyek</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-cash-coin text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1 fs-6">
                        {{ number_format($clients->sum('total_project_value') / 1000000, 1) }}M
                    </h3>
                    <small class="text-muted fw-semibold">Nilai Total</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients List -->
    <div class="row">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1">
                                <i class="bi bi-person-lines-fill text-purple me-2"></i>Daftar Klien
                            </h4>
                            <p class="text-muted mb-0">{{ $clients->total() ?? 0 }} klien terdaftar</p>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body p-0">
                    @if (isset($clients) && $clients->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 text-muted fw-semibold">Klien</th>
                                            <th class="border-0 text-muted fw-semibold">Kontak</th>
                                            <th class="border-0 text-muted fw-semibold text-center">Proyek</th>
                                            <th class="border-0 text-muted fw-semibold text-end">Nilai Total</th>
                                            <th class="border-0 text-muted fw-semibold text-end">Sudah Dibayar</th>
                                            <th class="border-0 text-muted fw-semibold text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($clients as $client)
                                            <tr class="client-row">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-person-circle text-purple fs-4"></i>
                                                        </div>
                                                        <div>
                                                            <strong class="text-dark">{{ $client->name }}</strong>
                                                            @if ($client->has_testimonial)
                                                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill ms-2">
                                                                    <i class="bi bi-star-fill me-1"></i>Testimoni
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="fw-medium">{{ $client->phone }}</div>
                                                        @if ($client->email)
                                                            <small class="text-muted">{{ $client->email }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-purple-light text-purple rounded-pill">
                                                        {{ $client->projects->count() ?? 0 }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-success">
                                                        Rp {{ number_format($client->total_project_value ?? 0, 0, ',', '.') }}
                                                    </strong>
                                                </td>
                                                <td class="text-end">
                                                    <span class="text-muted">
                                                        Rp {{ number_format($client->total_paid ?? 0, 0, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ $client->whatsapp_link }}" target="_blank"
                                                            class="btn btn-sm btn-success" title="WhatsApp">
                                                            <i class="bi bi-whatsapp"></i>
                                                        </a>
                                                        <a href="{{ route('clients.show', $client) }}"
                                                            class="btn btn-sm btn-outline-primary" title="Lihat">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('clients.edit', $client) }}"
                                                            class="btn btn-sm btn-outline-secondary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-block d-md-none p-3">
                            @foreach ($clients as $client)
                                <div class="card luxury-card mb-3 client-card" data-url="{{ route('clients.show', $client) }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person-circle text-purple fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">{{ $client->name }}</h6>
                                                    @if ($client->has_testimonial)
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill">
                                                            <i class="bi bi-star-fill me-1"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ $client->whatsapp_link }}" target="_blank">
                                                            <i class="bi bi-whatsapp text-success me-2"></i>WhatsApp
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('clients.show', $client) }}">
                                                            <i class="bi bi-eye text-primary me-2"></i>Lihat Detail
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('clients.edit', $client) }}">
                                                            <i class="bi bi-pencil text-secondary me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="row g-2 text-sm">
                                            <div class="col-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bi bi-telephone text-purple me-2"></i>
                                                    <small class="text-muted">{{ $client->phone }}</small>
                                                </div>
                                                @if($client->email)
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-envelope text-purple me-2"></i>
                                                        <small class="text-muted">{{ $client->email }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-6 text-end">
                                                <div class="mb-1">
                                                    <small class="text-muted">Proyek:</small>
                                                    <strong class="text-purple">{{ $client->projects->count() ?? 0 }}</strong>
                                                </div>
                                                <div>
                                                    <small class="text-muted">Total Nilai:</small><br>
                                                    <strong class="text-success fs-7">
                                                        Rp {{ number_format($client->total_project_value ?? 0, 0, ',', '.') }}
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Custom Pagination -->
                        @if (method_exists($clients, 'links'))
                            <div class="p-4 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="text-muted">
                                        Menampilkan {{ $clients->firstItem() }}-{{ $clients->lastItem() }}
                                        dari {{ $clients->total() }} klien
                                    </div>
                                </div>

                                @if($clients->hasPages())
                                    <nav>
                                        <ul class="pagination pagination-sm justify-content-center mb-0">
                                            {{-- Previous Page Link --}}
                                            @if ($clients->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $clients->previousPageUrl() }}">
                                                        <i class="bi bi-chevron-left"></i>
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach ($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
                                                @if ($page == $clients->currentPage())
                                                    <li class="page-item active">
                                                        <span class="page-link">{{ $page }}</span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach

                                            {{-- Next Page Link --}}
                                            @if ($clients->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $clients->nextPageUrl() }}">
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
                                @endif
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                                @if(request('search'))
                                    <i class="bi bi-search text-muted" style="font-size: 2.5rem;"></i>
                                @else
                                    <i class="bi bi-person-x text-muted" style="font-size: 2.5rem;"></i>
                                @endif
                            </div>
                            <h5 class="fw-bold mb-2">
                                @if(request('search'))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada klien
                                @endif
                            </h5>
                            <p class="text-muted mb-4">
                                @if(request('search'))
                                    Coba kata kunci lain atau reset pencarian
                                @else
                                    Mulai dengan menambahkan klien pertama Anda
                                @endif
                            </p>
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                @if(request('search'))
                                    <a href="{{ route('clients.index') }}" class="btn btn-outline-primary">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Reset Pencarian
                                    </a>
                                @endif
                                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Klien
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile client card click handler
            const clientCards = document.querySelectorAll('.client-card');

            clientCards.forEach(card => {
                card.style.cursor = 'pointer';

                // Touch feedback
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                    this.style.transition = 'transform 0.1s ease';
                });

                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                    this.style.transition = 'transform 0.2s ease';
                });

                // Click handler (avoid dropdown clicks)
                card.addEventListener('click', function(e) {
                    if (!e.target.closest('.dropdown')) {
                        window.location.href = this.dataset.url;
                    }
                });
            });

            // Desktop row hover effect
            const clientRows = document.querySelectorAll('.client-row');

            clientRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(139, 92, 246, 0.05)';
                    this.style.transform = 'translateX(4px)';
                    this.style.transition = 'all 0.2s ease';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                    this.style.transform = 'translateX(0)';
                });
            });

            // Search input enhancement
            const searchInput = document.getElementById('search');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    if (this.value.length > 0) {
                        this.style.borderColor = '#8B5CF6';
                        this.style.boxShadow = '0 0 0 0.2rem rgba(139, 92, 246, 0.25)';
                    } else {
                        this.style.borderColor = '';
                        this.style.boxShadow = '';
                    }
                });
            }

            // Add custom styles
            const style = document.createElement('style');
            style.textContent = `
                .stat-card-success::before {
                    background: linear-gradient(90deg, #10B981, #059669);
                }

                .stat-card-info::before {
                    background: linear-gradient(90deg, #3B82F6, #2563EB);
                }

                .client-card {
                    transition: all 0.2s ease;
                    cursor: pointer;
                }

                .client-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.15);
                }

                .client-row {
                    transition: all 0.2s ease;
                    cursor: pointer;
                }

                .table tbody tr:hover {
                    background-color: rgba(139, 92, 246, 0.05) !important;
                }

                .pagination .page-link {
                    color: #8B5CF6;
                    border-color: rgba(139, 92, 246, 0.2);
                }

                .pagination .page-item.active .page-link {
                    background-color: #8B5CF6;
                    border-color: #8B5CF6;
                }

                .pagination .page-link:hover {
                    background-color: rgba(139, 92, 246, 0.1);
                    border-color: #8B5CF6;
                    color: #8B5CF6;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
@endpush
