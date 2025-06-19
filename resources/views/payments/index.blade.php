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
                <div class="card-body text-center">
                    <h3 class="text-primary">Rp {{ number_format($totalPayments ?? 0, 0, ',', '.') }}</h3>
                    <p class="mb-0 text-muted">Total Pembayaran</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">Rp {{ number_format($totalSavings ?? 0, 0, ',', '.') }}</h3>
                    <p class="mb-0 text-muted">Total Tabungan 10%</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">Rp {{ number_format($monthlyIncome ?? 0, 0, ',', '.') }}</h3>
                    <p class="mb-0 text-muted">Pendapatan Bulan Ini</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $paymentCount ?? 0 }}</h3>
                    <p class="mb-0 text-muted">Total Transaksi</p>
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
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Proyek</th>
                                        <th>Klien</th>
                                        <th>Jumlah</th>
                                        <th>Tipe</th>
                                        <th>Metode</th>
                                        <th>Tabungan 10%</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                            <td>
                                                <strong class="text-lilac">{{ $payment->project->title }}</strong>
                                                @if ($payment->notes)
                                                    <br><small class="text-muted">{{ Str::limit($payment->notes, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $payment->project->client->name }}</td>
                                            <td>
                                                <strong class="text-success">{{ $payment->formatted_amount }}</strong>
                                            </td>
                                            <td>
                                                @if ($payment->payment_type == 'DP')
                                                    <span class="badge" style="background: var(--lilac-secondary); color: white;">DP</span>
                                                @elseif($payment->payment_type == 'INSTALLMENT')
                                                    <span class="badge bg-warning">CICILAN</span>
                                                @elseif($payment->payment_type == 'FULL')
                                                    <span class="badge bg-success">LUNAS</span>
                                                @else
                                                    <span class="badge bg-success">FINAL</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->payment_method ?? '-' }}</td>
                                            <td>
                                                <span class="text-success">{{ $payment->formatted_saving_amount }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('projects.show', $payment->project) }}" class="btn btn-sm btn-outline-primary"
                                                        title="Lihat Proyek">
                                                        <i class="bi bi-folder2-open"></i>
                                                    </a>
                                                    <a href="{{ route('payments.edit', $payment) }}" class="btn btn-sm btn-outline-secondary"
                                                        title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if (method_exists($payments, 'links'))
                            <div class="d-flex justify-content-center mt-4">
                                {{ $payments->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-credit-card-2-front text-muted" style="font-size: 3rem;"></i>
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
