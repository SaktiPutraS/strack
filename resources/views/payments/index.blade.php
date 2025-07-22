@extends('layouts.app')
@section('title', 'Daftar Pemasukan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-credit-card me-2"></i>Daftar Pemasukan
                    </h1>
                    <p class="text-muted mb-0">Kelola semua pemasukan dari proyek</p>
                </div>
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Pemasukan
                </a>
            </div>
        </div>
    </div>

    <!-- Payment Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Statistik Pemasukan
            </h5>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-primary h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-cash-coin text-primary fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1 fs-6">{{ number_format(($totalPayments ?? 0) / 1000000, 1) }} Juta</h3>
                    <small class="text-muted fw-semibold">Total Pemasukan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-calendar-month text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1 fs-6">{{ number_format(($monthlyIncome ?? 0) / 1000000, 1) }} Juta</h3>
                    <small class="text-muted fw-semibold">Bulan Ini</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-info h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-receipt text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $paymentCount ?? 0 }}</h3>
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
                        {{ $paymentCount > 0 ? number_format(($totalPayments ?? 0) / $paymentCount / 1000000, 1) : 0 }} Juta
                    </h3>
                    <small class="text-muted fw-semibold">Rata-rata</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments List -->
    <div class="card luxury-card border-0">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-receipt text-purple"></i>
                        </div>
                        Riwayat Pemasukan
                    </h5>
                </div>

                <!-- Search & Filter Form -->
                <form method="GET" class="d-flex flex-column flex-lg-row gap-2" style="min-width: 280px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari proyek, klien..." value="{{ request('search') }}">
                    <select name="payment_type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="DP" {{ request('payment_type') == 'DP' ? 'selected' : '' }}>DP</option>
                        <option value="INSTALLMENT" {{ request('payment_type') == 'INSTALLMENT' ? 'selected' : '' }}>Cicilan</option>
                        <option value="FULL" {{ request('payment_type') == 'FULL' ? 'selected' : '' }}>Lunas</option>
                        <option value="FINAL" {{ request('payment_type') == 'FINAL' ? 'selected' : '' }}>Final</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if (request()->hasAny(['search', 'payment_type', 'date_from', 'date_to']))
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            @if (isset($payments) && $payments->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Tanggal</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Proyek</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Klien</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark text-end">Jumlah</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark text-center">Tipe</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Metode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr class="border-bottom payment-row clickable-row" data-url="{{ route('payments.edit', $payment) }}"
                                        style="cursor: pointer;">
                                        <td class="px-4 py-4">
                                            <div class="fw-medium">{{ $payment->payment_date->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $payment->payment_date->format('H:i') }}</small>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark">{{ $payment->project->title }}</h6>
                                                @if ($payment->notes)
                                                    <small class="text-muted">{{ Str::limit($payment->notes, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="luxury-icon me-2" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-person-circle text-purple"></i>
                                                </div>
                                                <span class="fw-medium">{{ $payment->project->client->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-end">
                                            <strong class="text-success fs-5">{{ $payment->formatted_amount }}</strong>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @if ($payment->payment_type == 'DP')
                                                <span class="badge bg-purple-light text-purple border border-purple">DP</span>
                                            @elseif($payment->payment_type == 'INSTALLMENT')
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">CICILAN</span>
                                            @elseif($payment->payment_type == 'FULL')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success">LUNAS</span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success">FINAL</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="fw-medium">{{ $payment->payment_method ?? '-' }}</span>
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
                        @foreach ($payments as $payment)
                            <div class="card luxury-card mb-3 payment-card clickable-card" data-url="{{ route('payments.edit', $payment) }}"
                                style="cursor: pointer;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $payment->project->title }}</h6>
                                            <small class="text-muted">{{ $payment->project->client->name }}</small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-success">{{ $payment->formatted_amount }}</strong>
                                        </div>
                                    </div>

                                    @if ($payment->notes)
                                        <p class="text-muted mb-2 small">{{ Str::limit($payment->notes, 60) }}</p>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                                            <small class="text-muted">{{ $payment->payment_date->format('d M Y') }}</small>
                                            <span class="mx-2">•</span>
                                            @if ($payment->payment_type == 'DP')
                                                <span class="badge bg-purple-light text-purple">DP</span>
                                            @elseif($payment->payment_type == 'INSTALLMENT')
                                                <span class="badge bg-warning">CICILAN</span>
                                            @elseif($payment->payment_type == 'FULL')
                                                <span class="badge bg-success">LUNAS</span>
                                            @else
                                                <span class="badge bg-success">FINAL</span>
                                            @endif
                                        </div>
                                        <div>
                                            <i class="bi bi-pencil text-primary" title="Klik untuk edit"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if (method_exists($payments, 'links'))
                    <div class="card-footer bg-light border-0 p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-muted mb-0">
                                    Menampilkan {{ $payments->firstItem() }}-{{ $payments->lastItem() }}
                                    dari {{ $payments->total() }} pemasukan
                                </p>
                            </div>
                            <div class="col-md-6">
                                @if ($payments->hasPages())
                                    <nav class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                                        <ul class="pagination mb-0">
                                            @if ($payments->onFirstPage())
                                                <li class="page-item disabled">
                                                    <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $payments->previousPageUrl() }}">
                                                        <i class="bi bi-chevron-left"></i>
                                                    </a>
                                                </li>
                                            @endif

                                            <li class="page-item active">
                                                <span class="page-link">{{ $payments->currentPage() }} / {{ $payments->lastPage() }}</span>
                                            </li>

                                            @if ($payments->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $payments->nextPageUrl() }}">
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
                        <i class="bi bi-credit-card-2-front text-muted" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Tidak ada pemasukan ditemukan</h5>
                    @if (request()->hasAny(['search', 'payment_type']))
                        <p class="text-muted mb-4">Coba ubah kriteria pencarian atau filter</p>
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-primary me-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset Filter
                        </a>
                    @else
                        <p class="text-muted mb-4">Mulai dengan menambah pemasukan pertama</p>
                    @endif
                    <a href="{{ route('payments.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Pemasukan
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
                element.addEventListener('click', function() {
                    window.location.href = this.getAttribute('data-url');
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

        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.1) !important;
        }

        .border-purple {
            border-color: rgba(139, 92, 246, 0.3) !important;
        }

        .payment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #8B5CF6, #A855F7);
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
