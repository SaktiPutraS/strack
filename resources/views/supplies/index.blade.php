@extends('layouts.app')
@section('title', 'Perlengkapan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-box-seam me-2"></i>Perlengkapan
                    </h1>
                    <p class="text-muted mb-0">Kelola stok perlengkapan kantor dan operasional</p>
                </div>
                <a href="{{ route('supplies.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Perlengkapan
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-purple h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-boxes text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $stats['total'] }}</h3>
                    <small class="text-muted fw-semibold">Total Item</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="background: rgba(16, 185, 129, 0.1)">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $stats['normal'] }}</h3>
                    <small class="text-muted fw-semibold">Stok Normal</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="background: rgba(245, 158, 11, 0.1)">
                        <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $stats['low_stock'] }}</h3>
                    <small class="text-muted fw-semibold">Stok Rendah</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-danger h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="background: rgba(239, 68, 68, 0.1)">
                        <i class="bi bi-x-circle text-danger fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-danger mb-1">{{ $stats['out_of_stock'] }}</h3>
                    <small class="text-muted fw-semibold">Habis</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplies List -->
    <div class="card luxury-card border-0">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-list-ul text-purple"></i>
                        </div>
                        Daftar Perlengkapan
                    </h5>
                    <p class="text-muted mb-0">{{ $supplies->total() }} item</p>
                </div>

                <!-- Search & Filter -->
                <form method="GET" class="d-flex flex-column flex-lg-row gap-2" style="min-width: 350px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama barang..." value="{{ request('search') }}">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Stok Rendah</option>
                        <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Habis</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if (request()->hasAny(['search', 'status']))
                        <a href="{{ route('supplies.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($supplies->count() > 0)
                <!-- Desktop Table -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0 fw-semibold">Nama Barang</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-center">Stok</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-center">Min. Stok</th>
                                    <th class="px-4 py-3 border-0 fw-semibold">Status</th>
                                    <th class="px-4 py-3 border-0 fw-semibold">Link Order</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supplies as $supply)
                                    <tr class="border-bottom">
                                        <td class="px-4 py-4">
                                            <div class="fw-bold">{{ $supply->name }}</div>
                                            @if ($supply->notes)
                                                <small class="text-muted">{{ Str::limit($supply->notes, 50) }}</small>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span
                                                class="badge bg-{{ $supply->stock_status_color }} bg-opacity-10 text-{{ $supply->stock_status_color }} border border-{{ $supply->stock_status_color }} fs-6">
                                                {{ $supply->qty }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-muted">{{ $supply->minimum_stock }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="badge bg-{{ $supply->stock_status_color }}">
                                                {{ $supply->stock_status_text }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($supply->order_link)
                                                <a href="{{ $supply->order_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-link-45deg me-1"></i>Buka Link
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('supplies.show', $supply) }}" class="btn btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('supplies.use-form', $supply) }}" class="btn btn-outline-success" title="Gunakan">
                                                    <i class="bi bi-arrow-down-circle"></i>
                                                </a>
                                                <a href="{{ route('supplies.edit', $supply) }}" class="btn btn-outline-primary" title="Edit">
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
                <div class="d-lg-none">
                    <div class="p-3">
                        @foreach ($supplies as $supply)
                            <div class="card luxury-card mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $supply->name }}</h6>
                                            <div class="d-flex gap-2 mb-2">
                                                <span class="badge bg-{{ $supply->stock_status_color }}">
                                                    {{ $supply->stock_status_text }}
                                                </span>
                                                <span class="badge bg-secondary">
                                                    Stok: {{ $supply->qty }}
                                                </span>
                                            </div>
                                            @if ($supply->notes)
                                                <small class="text-muted">{{ Str::limit($supply->notes, 60) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('supplies.show', $supply) }}" class="btn btn-sm btn-outline-info flex-grow-1">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </a>
                                        <a href="{{ route('supplies.use-form', $supply) }}" class="btn btn-sm btn-outline-success flex-grow-1">
                                            <i class="bi bi-arrow-down-circle me-1"></i>Gunakan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if ($supplies->hasPages())
                    <div class="card-footer bg-light border-0 p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-muted mb-0">
                                    Menampilkan {{ $supplies->firstItem() }}-{{ $supplies->lastItem() }}
                                    dari {{ $supplies->total() }} item
                                </p>
                            </div>
                            <div class="col-md-6">
                                <nav class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                                    <ul class="pagination mb-0">
                                        @if ($supplies->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $supplies->previousPageUrl() }}">
                                                    <i class="bi bi-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        <li class="page-item active">
                                            <span class="page-link">{{ $supplies->currentPage() }} / {{ $supplies->lastPage() }}</span>
                                        </li>

                                        @if ($supplies->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $supplies->nextPageUrl() }}">
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
                    <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-box-seam text-muted" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Belum ada perlengkapan</h5>
                    <p class="text-muted mb-4">Mulai dengan menambahkan perlengkapan pertama</p>
                    <a href="{{ route('supplies.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Perlengkapan
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#8B5CF6'
                });
            @endif
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

        .stat-card-warning::before {
            background: linear-gradient(90deg, #F59E0B, #D97706);
        }

        .stat-card-danger::before {
            background: linear-gradient(90deg, #EF4444, #DC2626);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
        }

        /* Pagination Styles */
        .pagination {
            gap: 0.5rem;
        }

        .pagination .page-link {
            color: #8B5CF6;
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
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
        }

        .pagination .page-item.disabled .page-link {
            color: #9CA3AF;
            border-color: rgba(156, 163, 175, 0.2);
            background-color: #F9FAFB;
        }

        /* Luxury Card Styles */
        .luxury-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(139, 92, 246, 0.08);
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
            border-radius: 16px;
            transition: all 0.3s ease;
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

        .bg-purple {
            background-color: #8B5CF6 !important;
        }

        /* Table Styles */
        .table th {
            background-color: rgba(139, 92, 246, 0.05);
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        }

        .table tbody tr:hover {
            background-color: rgba(139, 92, 246, 0.03);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .pagination {
                flex-wrap: nowrap;
            }

            .pagination .page-link {
                padding: 0.4rem 0.8rem;
                font-size: 0.875rem;
            }
        }
    </style>
@endpush
