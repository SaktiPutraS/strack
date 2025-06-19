@extends('layouts.app')
@section('title', 'Daftar Pembayaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-credit-card"></i>Daftar Pembayaran
                </h1>
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Pembayaran
                </a>
            </div>
        </div>
    </div>

    <!-- Payment Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-cash-stack stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($totalPayments ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Pembayaran</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-piggy-bank stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($totalSavings ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Tabungan 10%</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-calendar-month stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($monthlyIncome ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Pendapatan Bulan Ini</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-list-ol stat-icon"></i>
                    <div class="stat-value">{{ $paymentCount ?? 0 }}</div>
                    <div class="stat-label">Total Transaksi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari proyek, klien, atau catatan..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="payment_type" class="form-select">
                                <option value="">Semua Tipe</option>
                                <option value="DP" {{ request('payment_type') == 'DP' ? 'selected' : '' }}>DP</option>
                                <option value="INSTALLMENT" {{ request('payment_type') == 'INSTALLMENT' ? 'selected' : '' }}>Cicilan</option>
                                <option value="FULL" {{ request('payment_type') == 'FULL' ? 'selected' : '' }}>Lunas</option>
                                <option value="FINAL" {{ request('payment_type') == 'FINAL' ? 'selected' : '' }}>Final</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Dari Tanggal">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Sampai Tanggal">
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

    <!-- Payments List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-receipt"></i>Riwayat Pembayaran ({{ $payments->total() ?? $payments->count() }} total)
                    </h5>

                    @if (isset($payments) && $payments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($payments as $payment)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-start">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 50px; height: 50px; background: var(--lilac-light); color: var(--lilac-primary);">
                                            <i class="bi bi-credit-card"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1 text-lilac">{{ $payment->project->title }}</h6>
                                                    <small class="text-muted">{{ $payment->project->client->name }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold text-success fs-5">{{ $payment->formatted_amount }}</div>
                                                    @if ($payment->payment_type == 'DP')
                                                        <span class="badge" style="background: var(--lilac-secondary); color: white;">DP</span>
                                                    @elseif($payment->payment_type == 'INSTALLMENT')
                                                        <span class="badge badge-warning">CICILAN</span>
                                                    @elseif($payment->payment_type == 'FULL')
                                                        <span class="badge badge-success">LUNAS</span>
                                                    @else
                                                        <span class="badge badge-success">FINAL</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-2">
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-calendar3 text-muted me-2"></i>
                                                        <small class="text-muted">{{ $payment->payment_date->format('d M Y') }}</small>
                                                    </div>
                                                </div>
                                                @if ($payment->payment_method)
                                                    <div class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-credit-card text-muted me-2"></i>
                                                            <small class="text-muted">{{ $payment->payment_method }}</small>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-piggy-bank text-muted me-2"></i>
                                                        <small class="text-success">Tabungan: {{ $payment->formatted_saving_amount }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-graph-up text-muted me-2"></i>
                                                        <small class="text-muted">Progress: {{ $payment->project->progress_percentage }}%</small>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($payment->notes)
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-journal-text me-1"></i>
                                                        {{ $payment->notes }}
                                                    </small>
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="progress flex-grow-1 me-3" style="height: 6px;">
                                                    <div class="progress-bar"
                                                        style="width: {{ $payment->project->progress_percentage }}%; background: var(--lilac-primary);"
                                                        role="progressbar"></div>
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('projects.show', $payment->project) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-folder2-open"></i>
                                                    </a>
                                                    <a href="{{ route('payments.edit', $payment) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if (method_exists($payments, 'links'))
                            <div class="d-flex justify-content-center mt-4">
                                {{ $payments->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-credit-card-2-front text-lilac-secondary" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada pembayaran ditemukan</p>
                            <a href="{{ route('payments.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Pembayaran Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
