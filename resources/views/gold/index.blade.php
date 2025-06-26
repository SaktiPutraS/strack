{{-- resources/views/gold/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Manajemen Investasi Emas')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-coin"></i>Manajemen Investasi Emas
                </h1>
                <div class="btn-group">
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
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-gem stat-icon text-warning"></i>
                    <div class="stat-value">{{ number_format($currentGrams, 3) }} gram</div>
                    <div class="stat-label">Total Emas</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-graph-up stat-icon text-success"></i>
                    <div class="stat-value">Rp {{ number_format($averageBuyPrice, 0, ',', '.') }}</div>
                    <div class="stat-label">Rata-rata Harga Beli</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-cash-stack stat-icon text-primary"></i>
                    <div class="stat-value">Rp {{ number_format($totalInvestment, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Investasi</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-trophy stat-icon text-warning"></i>
                    <div class="stat-value">Rp {{ number_format($currentValue, 0, ',', '.') }}</div>
                    <div class="stat-label">Nilai Saat Ini</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gold Portfolio Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="section-title text-warning">
                        <i class="bi bi-gem"></i>Portfolio Emas
                    </h5>
                    <div class="row g-3 text-center">
                        <div class="col-md-4">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <h4 class="text-warning">{{ number_format($currentGrams, 3) }} gram</h4>
                                <p class="mb-0 text-muted">Total Emas</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <h4 class="text-success">Rp {{ number_format($averageBuyPrice, 0, ',', '.') }}/gram</h4>
                                <p class="mb-0 text-muted">Rata-rata Harga Beli</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <h4 class="text-primary">Rp {{ number_format($totalInvestment, 0, ',', '.') }}</h4>
                                <p class="mb-0 text-muted">Total Investasi</p>
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
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-clock-history"></i>Riwayat Transaksi Emas
                    </h5>

                    @if ($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Gram</th>
                                        <th>Harga Total</th>
                                        <th>Harga/Gram</th>
                                        <th>Catatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                                            <td>
                                                @if ($transaction->type === 'BUY')
                                                    <span class="badge bg-success">BELI</span>
                                                @else
                                                    <span class="badge bg-warning">JUAL</span>
                                                @endif
                                            </td>
                                            <td><strong>{{ $transaction->grams }} gram</strong></td>
                                            <td>
                                                <strong class="{{ $transaction->type === 'BUY' ? 'text-danger' : 'text-success' }}">
                                                    {{ $transaction->formatted_total_price }}
                                                </strong>
                                            </td>
                                            <td>{{ $transaction->formatted_price_per_gram }}</td>
                                            <td>{{ $transaction->notes ?? '-' }}</td>
                                            <td>
                                                <form action="{{ route('gold.destroy', $transaction) }}" method="POST"
                                                    onsubmit="return confirm('Yakin hapus transaksi ini?')" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if (method_exists($transactions, 'links'))
                            <div class="mt-4">
                                <div style="display: none;">{{ $transactions->links() }}</div>
                                <div class="pagination-info-alt">
                                    Menampilkan {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }}
                                    dari {{ $transactions->total() }} transaksi
                                </div>
                                <nav class="bootstrap-pagination">
                                    <ul class="pagination">
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

                                        @for ($i = 1; $i <= $transactions->lastPage(); $i++)
                                            @if ($i == $transactions->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $i }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $transactions->url($i) }}">{{ $i }}</a>
                                                </li>
                                            @endif
                                        @endfor

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
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-coin text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Belum ada transaksi emas</p>
                            <a href="{{ route('gold.create', ['type' => 'BUY']) }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>Beli Emas Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
