@extends('layouts.app')
@section('title', 'Tarik Cash dari Bank')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-wallet2 me-2"></i>Tarik Cash dari Bank
                    </h1>
                    <p class="text-muted mb-0">Kelola penarikan cash dari Bank Octo</p>
                </div>
                <a href="{{ route('cash-withdrawals.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tarik Cash Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Balance Info -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card luxury-card border-0 bg-gradient-primary text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-bank text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-white-50">Saldo Bank Octo</h6>
                            <h3 class="mb-0 fw-bold text-white">{{ $formattedBankBalance }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card luxury-card border-0 bg-gradient-success text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-wallet2 text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-white-50">Saldo Cash</h6>
                            <h3 class="mb-0 fw-bold text-white">{{ $formattedCashBalance }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Statistik Penarikan
            </h5>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-arrow-down-circle text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1 fs-6">{{ number_format(($totalWithdrawals ?? 0) / 1000000, 1) }} Juta</h3>
                    <small class="text-muted fw-semibold">Total Penarikan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-info h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-calendar-month text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1 fs-6">{{ number_format(($monthlyWithdrawals ?? 0) / 1000000, 1) }} Juta</h3>
                    <small class="text-muted fw-semibold">Bulan Ini</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-primary h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-receipt text-primary fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">{{ $withdrawalCount ?? 0 }}</h3>
                    <small class="text-muted fw-semibold">Total Transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-graph-up text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1 fs-6">
                        {{ $withdrawalCount > 0 ? number_format(($totalWithdrawals ?? 0) / $withdrawalCount / 1000000, 1) : 0 }} Juta
                    </h3>
                    <small class="text-muted fw-semibold">Rata-rata</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawals List -->
    <div class="card luxury-card border-0">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-clock-history text-purple"></i>
                        </div>
                        Riwayat Penarikan Cash
                    </h5>
                </div>

                <!-- Search & Filter Form -->
                <form method="GET" class="d-flex flex-column flex-lg-row gap-2" style="min-width: 280px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari referensi, catatan..." value="{{ request('search') }}">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if (request()->hasAny(['search', 'date_from', 'date_to']))
                        <a href="{{ route('cash-withdrawals.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            @if (isset($withdrawals) && $withdrawals->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Tanggal</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark text-end">Jumlah</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Referensi</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Catatan</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdrawals as $withdrawal)
                                    <tr class="border-bottom withdrawal-row clickable-row" data-url="{{ route('cash-withdrawals.edit', $withdrawal) }}"
                                        style="cursor: pointer;">
                                        <td class="px-4 py-4">
                                            <div class="fw-medium">{{ $withdrawal->withdrawal_date->format('d M Y') }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-end">
                                            <strong class="text-warning fs-5">{{ $withdrawal->formatted_amount }}</strong>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-muted">{{ $withdrawal->reference_number ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-muted">{{ Str::limit($withdrawal->notes ?? '-', 50) }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="{{ route('cash-withdrawals.edit', $withdrawal) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('cash-withdrawals.destroy', $withdrawal) }}" method="POST"
                                                    style="display: inline;" onsubmit="return confirmDelete(event)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
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
                        @foreach ($withdrawals as $withdrawal)
                            <div class="card luxury-card mb-3 withdrawal-card clickable-card"
                                data-url="{{ route('cash-withdrawals.edit', $withdrawal) }}" style="cursor: pointer;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">Penarikan Cash</h6>
                                            @if ($withdrawal->reference_number)
                                                <small class="text-muted">Ref: {{ $withdrawal->reference_number }}</small>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-warning">{{ $withdrawal->formatted_amount }}</strong>
                                        </div>
                                    </div>

                                    @if ($withdrawal->notes)
                                        <p class="text-muted mb-2 small">{{ Str::limit($withdrawal->notes, 60) }}</p>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                                            <small class="text-muted">{{ $withdrawal->withdrawal_date->format('d M Y') }}</small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('cash-withdrawals.edit', $withdrawal) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('cash-withdrawals.destroy', $withdrawal) }}" method="POST"
                                                style="display: inline;" onsubmit="return confirmDelete(event)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if (method_exists($withdrawals, 'links'))
                    <div class="card-footer bg-light border-0 p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-muted mb-0">
                                    Menampilkan {{ $withdrawals->firstItem() }}-{{ $withdrawals->lastItem() }}
                                    dari {{ $withdrawals->total() }} penarikan
                                </p>
                            </div>
                            <div class="col-md-6">
                                @if ($withdrawals->hasPages())
                                    <nav class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                                        <ul class="pagination mb-0">
                                            @if ($withdrawals->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $withdrawals->previousPageUrl() }}">
                                                        <i class="bi bi-chevron-left"></i>
                                                    </a>
                                                </li>
                                            @endif

                                            <li class="page-item active">
                                                <span class="page-link">{{ $withdrawals->currentPage() }} / {{ $withdrawals->lastPage() }}</span>
                                            </li>

                                            @if ($withdrawals->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $withdrawals->nextPageUrl() }}">
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
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-wallet2 text-muted" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Belum ada penarikan cash</h5>
                    @if (request()->hasAny(['search', 'date_from', 'date_to']))
                        <p class="text-muted mb-4">Coba ubah kriteria pencarian</p>
                        <a href="{{ route('cash-withdrawals.index') }}" class="btn btn-outline-primary me-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset Filter
                        </a>
                    @else
                        <p class="text-muted mb-4">Mulai dengan penarikan cash pertama</p>
                    @endif
                    <a href="{{ route('cash-withdrawals.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tarik Cash Baru
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert messages
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

            // Clickable rows
            document.querySelectorAll('.clickable-row, .clickable-card').forEach(element => {
                element.addEventListener('click', function(e) {
                    // Don't trigger if clicking on action buttons
                    if (!e.target.closest('.btn')) {
                        window.location.href = this.getAttribute('data-url');
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
            });

            // Animation
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

        function confirmDelete(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Yakin ingin menghapus penarikan cash ini? Aksi ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });

            return false;
        }
    </script>

    <style>
        .stat-card {
            position: relative;
            overflow: hidden;
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

        .stat-card-primary::before {
            background: linear-gradient(90deg, #8B5CF6, #A855F7);
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #F59E0B, #D97706);
        }

        .stat-card-info::before {
            background: linear-gradient(90deg, #3B82F6, #2563EB);
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10B981, #059669);
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
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10B981, #059669) !important;
        }

        .withdrawal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #F59E0B, #D97706);
        }

        .table th {
            background-color: rgba(139, 92, 246, 0.05);
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        }

        .pagination .page-link {
            color: #8B5CF6;
            border-color: rgba(139, 92, 246, 0.2);
        }

        .pagination .page-item.active .page-link {
            background-color: #8B5CF6;
            border-color: #8B5CF6;
        }
    </style>
@endpush
