{{-- resources/views/expenses/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pencatatan Pengeluaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-credit-card"></i>Pencatatan Pengeluaran
                </h1>
                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Pengeluaran
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-danger">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h3>
                    <p class="mb-0 text-muted">Total Pengeluaran</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">Rp {{ number_format($monthlyExpenses, 0, ',', '.') }}</h3>
                    <p class="mb-0 text-muted">Pengeluaran Bulan Ini</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $expenses->total() ?? $expenses->count() }}</h3>
                    <p class="mb-0 text-muted">Total Transaksi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    @if ($expensesByCategory->isNotEmpty())
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="section-title">
                            <i class="bi bi-pie-chart"></i>Pengeluaran per Kategori (Bulan Ini)
                        </h5>
                        <div class="row g-2">
                            @foreach ($expensesByCategory as $category => $amount)
                                <div class="col-6 col-md-4 col-lg-2">
                                    <div class="text-center p-2 border rounded">
                                        <div class="fw-bold text-danger">Rp {{ number_format($amount, 0, ',', '.') }}</div>
                                        <small class="text-muted">{{ $category }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari deskripsi atau subcategory..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach (\App\Models\Expense::CATEGORIES as $key => $label)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
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

    <!-- Expenses List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-list-ul"></i>Daftar Pengeluaran ({{ $expenses->total() ?? $expenses->count() }} total)
                    </h5>

                    @if ($expenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $expense->category_label }}</span>
                                                @if ($expense->subcategory)
                                                    <br><small class="text-muted">{{ $expense->subcategory_label }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $expense->description }}</strong>
                                            </td>
                                            <td>
                                                <strong class="text-danger">{{ $expense->formatted_amount }}</strong>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-primary"
                                                        title="Lihat">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-secondary"
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

                        @if (method_exists($expenses, 'links'))
                            <div class="mt-4">
                                <div style="display: none;">{{ $expenses->links() }}</div>
                                <div class="pagination-info-alt">
                                    Menampilkan {{ $expenses->firstItem() }}-{{ $expenses->lastItem() }}
                                    dari {{ $expenses->total() }} pengeluaran
                                </div>
                                <nav class="bootstrap-pagination">
                                    <ul class="pagination">
                                        @if ($expenses->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $expenses->previousPageUrl() }}">
                                                    <i class="bi bi-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        @for ($i = 1; $i <= $expenses->lastPage(); $i++)
                                            @if ($i == $expenses->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $i }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $expenses->url($i) }}">{{ $i }}</a>
                                                </li>
                                            @endif
                                        @endfor

                                        @if ($expenses->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $expenses->nextPageUrl() }}">
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
                            <i class="bi bi-wallet2 text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada pengeluaran ditemukan</p>
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Pengeluaran Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


