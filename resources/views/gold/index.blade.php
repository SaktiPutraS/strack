@extends('layouts.app')
@section('title', 'Manajemen Investasi Emas')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-coin me-2"></i>Manajemen Investasi Emas
                    </h1>
                    <p class="text-muted mb-0">Kelola portfolio investasi emas</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('gold.create', ['type' => 'BUY']) }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Beli Emas
                    </a>
                    <a href="{{ route('gold.create', ['type' => 'SELL']) }}" class="btn btn-warning">
                        <i class="bi bi-dash-circle me-2"></i>Jual Emas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gold Portfolio Overview -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card luxury-card border-0">
                <div class="card-body text-center py-4">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-gem text-warning"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-warning">{{ number_format($currentGrams, 3) }}</h3>
                            <p class="mb-0 text-muted">Total Emas (gram)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card luxury-card border-0">
                <div class="card-body text-center py-4">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-graph-up text-success"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($averageBuyPrice, 0, ',', '.') }}</h3>
                            <p class="mb-0 text-muted">Harga Rata-rata/gram</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card luxury-card border-0">
                <div class="card-body text-center py-4">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-cash-stack text-primary"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($totalInvestment, 0, ',', '.') }}</h3>
                            <p class="mb-0 text-muted">Total Investasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card luxury-card border-0">
                <div class="card-body text-center py-4">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-trophy text-purple"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-purple">{{ number_format($currentValue, 0, ',', '.') }}</h3>
                            <p class="mb-0 text-muted">Nilai Saat Ini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gold Portfolio Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0 bg-gradient-warning text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-gem text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-white">Portfolio Emas</h5>
                            <p class="mb-0 text-white-50">Ringkasan investasi emas Anda</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-white bg-opacity-15 rounded">
                                <h4 class="text-black mb-1">{{ number_format($currentGrams, 3) }} gram</h4>
                                <small class="text-black-50">Total Emas</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-white bg-opacity-15 rounded">
                                <h4 class="text-black mb-1">Rp {{ number_format($averageBuyPrice, 0, ',', '.') }}/gram</h4>
                                <small class="text-black-50">Rata-rata Harga Beli</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-white bg-opacity-15 rounded">
                                <h4 class="text-black mb-1">Rp {{ number_format($totalInvestment, 0, ',', '.') }}</h4>
                                <small class="text-black-50">Total Investasi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="row">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-clock-history text-purple"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Riwayat Transaksi Emas</h5>
                            <p class="mb-0 text-muted">{{ $transactions->total() ?? $transactions->count() }} transaksi</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if ($transactions->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Tanggal</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Tipe</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Gram</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark text-end">Harga Total</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Harga/Gram</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Catatan</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $transaction)
                                            <tr class="border-bottom">
                                                <td class="px-4 py-4">
                                                    <div class="fw-medium">{{ $transaction->transaction_date->format('d M Y') }}</div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    @if ($transaction->type === 'BUY')
                                                        <span class="badge bg-success">BELI</span>
                                                    @else
                                                        <span class="badge bg-warning">JUAL</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-4">
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $transaction->grams }} gram</h6>
                                                </td>
                                                <td class="px-4 py-4 text-end">
                                                    <strong class="{{ $transaction->type === 'BUY' ? 'text-danger' : 'text-success' }} fs-5">
                                                        {{ $transaction->formatted_total_price }}
                                                    </strong>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <span class="text-muted">{{ $transaction->formatted_price_per_gram }}</span>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <span class="text-muted">{{ $transaction->notes ?? '-' }}</span>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <form action="{{ route('gold.destroy', $transaction) }}" method="POST" style="display: inline;"
                                                        onsubmit="return confirmDelete(event)">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
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
                                @foreach ($transactions as $transaction)
                                    <div class="card luxury-card mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        @if ($transaction->type === 'BUY')
                                                            <span class="badge bg-success me-2">BELI</span>
                                                        @else
                                                            <span class="badge bg-warning me-2">JUAL</span>
                                                        @endif
                                                        <h6 class="fw-bold mb-0">{{ $transaction->grams }} gram</h6>
                                                    </div>
                                                    @if ($transaction->notes)
                                                        <small class="text-muted">{{ $transaction->notes }}</small>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    <strong class="{{ $transaction->type === 'BUY' ? 'text-danger' : 'text-success' }}">
                                                        {{ $transaction->formatted_total_price }}
                                                    </strong>
                                                    <div class="small text-muted">{{ $transaction->formatted_price_per_gram }}</div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                                    <small class="text-muted">{{ $transaction->transaction_date->format('d M Y') }}</small>
                                                </div>
                                                <form action="{{ route('gold.destroy', $transaction) }}" method="POST" style="display: inline;"
                                                    onsubmit="return confirmDelete(event)">
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
                        @if (method_exists($transactions, 'links'))
                            <div class="card-footer bg-light border-0 p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-muted mb-0">
                                            Menampilkan {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }}
                                            dari {{ $transactions->total() }} transaksi
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        @if ($transactions->hasPages())
                                            <nav class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                                                <ul class="pagination mb-0">
                                                    @if ($transactions->onFirstPage())
                                                        <li class="page-item disabled">
                                                            <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                                        </li>
                                                    @else
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $transactions->previousPageUrl() }}">
                                                                <i class="bi bi-chevron-left"></i>
                                                            </a>
                                                        </li>
                                                    @endif

                                                    <li class="page-item active">
                                                        <span class="page-link">{{ $transactions->currentPage() }} /
                                                            {{ $transactions->lastPage() }}</span>
                                                    </li>

                                                    @if ($transactions->hasMorePages())
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $transactions->nextPageUrl() }}">
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
                                <i class="bi bi-coin text-muted" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Belum ada transaksi emas</h5>
                            <p class="text-muted mb-4">Mulai dengan melakukan investasi emas pertama</p>
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                <a href="{{ route('gold.create', ['type' => 'BUY']) }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-2"></i>Beli Emas Pertama
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

            // Animation
            const cards = document.querySelectorAll('.luxury-card');
            cards.forEach((card, index) => {
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
                text: 'Yakin ingin menghapus transaksi ini? Aksi ini tidak dapat dibatalkan.',
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

        .bg-gradient-warning {
            background: linear-gradient(135deg, #F59E0B, #D97706) !important;
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
