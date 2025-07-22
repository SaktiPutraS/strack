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

    <!-- Clients List -->
    <div class="row">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                        <div>
                            <h4 class="fw-bold mb-1">
                                <i class="bi bi-person-lines-fill text-purple me-2"></i>Daftar Klien
                            </h4>
                        </div>

                        <!-- Search Form -->
                        <form method="GET" class="d-flex gap-2" style="min-width: 300px;">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, telepon, atau email..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                            @if (request('search'))
                                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </form>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($clients as $client)
                                            <tr class="client-row clickable-row" data-url="{{ route('clients.show', $client) }}" style="cursor: pointer;">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-person-circle text-purple fs-4"></i>
                                                        </div>
                                                        <div>
                                                            <strong class="text-dark">{{ $client->name }}</strong>
                                                            @if ($client->has_testimonial)
                                                                <span
                                                                    class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill ms-2">
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-block d-md-none p-3">
                            @foreach ($clients as $client)
                                <div class="card luxury-card mb-3 client-card clickable-card" data-url="{{ route('clients.show', $client) }}"
                                    style="cursor: pointer;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person-circle text-purple fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-1">{{ $client->name }}</h6>
                                                @if ($client->has_testimonial)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill">
                                                        <i class="bi bi-star-fill me-1"></i>Testimoni
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <i class="bi bi-eye text-primary" title="Klik untuk detail"></i>
                                            </div>
                                        </div>

                                        <div class="row g-2 text-sm">
                                            <div class="col-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bi bi-telephone text-purple me-2"></i>
                                                    <small class="text-muted">{{ $client->phone }}</small>
                                                </div>
                                                @if ($client->email)
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

                                @if ($clients->hasPages())
                                    <nav>
                                        <ul class="pagination pagination-sm justify-content-center mb-0">
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
                                @if (request('search'))
                                    <i class="bi bi-search text-muted" style="font-size: 2.5rem;"></i>
                                @else
                                    <i class="bi bi-person-x text-muted" style="font-size: 2.5rem;"></i>
                                @endif
                            </div>
                            <h5 class="fw-bold mb-2">
                                @if (request('search'))
                                    Tidak ada hasil pencarian
                                @else
                                    Belum ada klien
                                @endif
                            </h5>
                            <p class="text-muted mb-4">
                                @if (request('search'))
                                    Coba kata kunci lain atau reset pencarian
                                @else
                                    Mulai dengan menambahkan klien pertama Anda
                                @endif
                            </p>
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                @if (request('search'))
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
            // SweetAlert untuk session messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#8B5CF6',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#8B5CF6'
                });
            @endif

            // Clickable rows dan cards
            document.querySelectorAll('.clickable-row, .clickable-card').forEach(element => {
                element.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    if (url) {
                        window.location.href = url;
                    }
                });

                // Hover effects
                element.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(139, 92, 246, 0.05)';
                    this.style.transition = 'all 0.2s ease';
                });

                element.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });

                // Touch feedback
                element.addEventListener('touchstart', function() {
                    this.style.backgroundColor = 'rgba(139, 92, 246, 0.1)';
                }, {
                    passive: true
                });

                element.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.style.backgroundColor = '';
                    }, 200);
                }, {
                    passive: true
                });
            });

            // Add animation to cards
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

        .stat-card-purple::before {
            background: linear-gradient(90deg, #8B5CF6, #A855F7);
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10B981, #059669);
        }

        .stat-card-info::before {
            background: linear-gradient(90deg, #3B82F6, #2563EB);
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #F59E0B, #D97706);
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

        .text-purple {
            color: #8B5CF6 !important;
        }

        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.1) !important;
        }

        .clickable-row,
        .clickable-card {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(139, 92, 246, 0.05) !important;
        }

        .client-card {
            position: relative;
            overflow: hidden;
        }

        .client-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #8B5CF6, #A855F7);
            transition: all 0.3s ease;
        }

        .client-card:hover::before {
            width: 6px;
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

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }
        }
    </style>
@endpush
