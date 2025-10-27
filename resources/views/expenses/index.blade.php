@extends('layouts.app')
@section('title', 'Pencatatan Pengeluaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-credit-card me-2"></i>Pencatatan Pengeluaran
                    </h1>
                    <p class="text-muted mb-0">Kelola semua pengeluaran Bank & Cash</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <!-- Analysis Button -->
                    <a href="{{ route('expenses.analysis') }}" class="btn btn-outline-purple">
                        <i class="bi bi-graph-up-arrow me-2"></i>Analisis Pengeluaran
                    </a>
                    <!-- Export Button -->
                    <button type="button" class="btn btn-outline-success" id="exportExcelBtn">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </button>
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Pengeluaran
                    </a>
                </div>
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

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <!-- Monthly Chart -->
        <div class="col-12 col-lg-6">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-bar-chart text-purple"></i>
                        </div>
                        Pengeluaran Bulan Ini
                    </h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="monthlyChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Yearly Chart -->
        <div class="col-12 col-lg-6">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-bar-chart text-purple"></i>
                        </div>
                        Pengeluaran Tahun Ini
                    </h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="yearlyChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses List -->
    <div class="card luxury-card border-0">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-list-ul text-purple"></i>
                        </div>
                        Daftar Pengeluaran
                    </h5>
                    <p class="text-muted mb-0">{{ $expenses->total() ?? $expenses->count() }} transaksi</p>
                </div>

                <!-- Search & Filter -->
                <form method="GET" class="d-flex flex-column flex-lg-row gap-2" id="searchForm" style="min-width: 350px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari deskripsi..." value="{{ request('search') }}">
                    <select name="source" class="form-select">
                        <option value="">Semua Sumber</option>
                        @foreach (\App\Models\Expense::SOURCES as $key => $label)
                            <option value="{{ $key }}" {{ request('source') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach (\App\Models\Expense::CATEGORIES as $key => $label)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Dari tanggal">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Sampai tanggal">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if (request()->hasAny(['search', 'source', 'category', 'date_from', 'date_to']))
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($expenses->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Tanggal</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Sumber</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Kategori</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark">Deskripsi</th>
                                    <th class="px-4 py-3 border-0 fw-semibold text-dark text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $expense)
                                    <tr class="border-bottom expense-row clickable-row" data-url="{{ route('expenses.edit', $expense) }}"
                                        style="cursor: pointer;">
                                        <td class="px-4 py-4">
                                            <div class="fw-medium">{{ $expense->expense_date->format('d M Y') }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="badge bg-{{ $expense->source_color }} border border-{{ $expense->source_color }}">
                                                <i class="bi bi-{{ $expense->source_icon }} me-1"></i>
                                                {{ $expense->source_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="badge bg-secondary">{{ $expense->category_label }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <h6 class="mb-0 fw-bold text-dark">{{ Str::limit($expense->description, 50) }}</h6>
                                        </td>
                                        <td class="px-4 py-4 text-end">
                                            <strong class="text-danger fs-5">{{ $expense->formatted_amount }}</strong>
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
                        @foreach ($expenses as $expense)
                            <div class="card luxury-card mb-3 expense-card clickable-card" data-url="{{ route('expenses.edit', $expense) }}"
                                style="cursor: pointer;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ Str::limit($expense->description, 40) }}</h6>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-{{ $expense->source_color }} border border-{{ $expense->source_color }}">
                                                    <i class="bi bi-{{ $expense->source_icon }} me-1"></i>
                                                    {{ $expense->source_label }}
                                                </span>
                                                <span class="badge bg-secondary">{{ $expense->category_label }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-danger">{{ $expense->formatted_amount }}</strong>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                                            <small class="text-muted">{{ $expense->expense_date->format('d M Y') }}</small>
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
                @if (method_exists($expenses, 'links'))
                    <div class="card-footer bg-light border-0 p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-muted mb-0">
                                    Menampilkan {{ $expenses->firstItem() }}-{{ $expenses->lastItem() }}
                                    dari {{ $expenses->total() }} pengeluaran
                                </p>
                            </div>
                            <div class="col-md-6">
                                @if ($expenses->hasPages())
                                    <nav class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                                        <ul class="pagination mb-0">
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

                                            <li class="page-item active">
                                                <span class="page-link">{{ $expenses->currentPage() }} / {{ $expenses->lastPage() }}</span>
                                            </li>

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
                    <h5 class="fw-bold mb-2">Tidak ada pengeluaran ditemukan</h5>
                    @if (request()->hasAny(['search', 'source', 'category']))
                        <p class="text-muted mb-4">Coba ubah kriteria pencarian atau filter</p>
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-primary me-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset Filter
                        </a>
                    @else
                        <p class="text-muted mb-4">Mulai dengan menambah pengeluaran pertama</p>
                    @endif
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Pengeluaran
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Export Excel functionality
            const exportExcelBtn = document.getElementById('exportExcelBtn');

            exportExcelBtn.addEventListener('click', function() {
                // Show loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengunduh...';
                this.disabled = true;

                // Get current filter parameters
                const searchForm = document.getElementById('searchForm');
                const formData = new FormData(searchForm);
                const params = new URLSearchParams();

                // Add all form parameters to export URL
                for (let [key, value] of formData.entries()) {
                    if (value) {
                        params.append(key, value);
                    }
                }

                // Create export URL
                const exportUrl = '{{ route('expenses.export.excel') }}?' + params.toString();

                // Create temporary link to download
                const link = document.createElement('a');
                link.href = exportUrl;
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Reset button state after delay
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            });

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

            // Chart data
            const monthlyData = @json($monthlyExpensesByCategory ?? collect());
            const yearlyData = @json($yearlyExpensesByCategory ?? collect());

            // Shorten numbers (100k, 1.000k)
            const shortenNumber = (num) => {
                if (num >= 1_000_000) {
                    return (num / 1_000_000).toFixed(0) + 'Jt';
                } else if (num >= 1000) {
                    return (num / 1000).toFixed(0) + 'Rb';
                }
                return num;
            };

            // Chart colors
            const chartColors = [
                '#8B5CF6', '#06B6D4', '#10B981', '#F59E0B',
                '#EF4444', '#8B5A2B', '#84CC16', '#6366F1'
            ];

            // Monthly Chart by Category
            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                const sortedMonthlyData = Object.entries(monthlyData)
                    .filter(([key, value]) => key !== "Saldo Awal")
                    .sort((a, b) => b[1] - a[1]);

                new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: sortedMonthlyData.map(entry => entry[0]),
                        datasets: [{
                            label: 'Jumlah (Rp)',
                            data: sortedMonthlyData.map(entry => entry[1]),
                            backgroundColor: chartColors,
                            borderColor: chartColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + shortenNumber(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Yearly Chart by Category
            const yearlyCtx = document.getElementById('yearlyChart');
            if (yearlyCtx) {
                const sortedYearlyData = Object.entries(yearlyData)
                    .filter(([key, value]) => key !== "Saldo Awal")
                    .sort((a, b) => b[1] - a[1]);

                new Chart(yearlyCtx, {
                    type: 'bar',
                    data: {
                        labels: sortedYearlyData.map(entry => entry[0]),
                        datasets: [{
                            label: 'Jumlah (Rp)',
                            data: sortedYearlyData.map(entry => entry[1]),
                            backgroundColor: chartColors,
                            borderColor: chartColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + shortenNumber(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Clickable rows
            document.querySelectorAll('.clickable-row, .clickable-card').forEach(element => {
                element.addEventListener('click', function() {
                    window.location.href = this.getAttribute('data-url');
                });

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
        .btn-outline-purple {
            border: 1.5px solid #8B5CF6;
            color: #8B5CF6;
            background: rgba(255, 255, 255, 0.9);
        }

        .btn-outline-purple:hover {
            background: linear-gradient(135deg, #8B5CF6, #A855F7);
            color: white;
            border-color: #8B5CF6;
        }

        /* Export button styling */
        .btn-outline-success {
            border-color: #10B981;
            color: #10B981;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .btn-outline-success:hover {
            background-color: #10B981;
            border-color: #10B981;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.25);
        }

        .btn-outline-success:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }

        .luxury-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(139, 92, 246, 0.08);
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
            border-radius: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
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

        .expense-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #EF4444, #DC2626);
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

        .bg-gradient-primary {
            background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10B981, #059669) !important;
        }

        .clickable-row,
        .clickable-card {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .clickable-row:hover,
        .clickable-card:hover {
            background-color: rgba(139, 92, 246, 0.05) !important;
        }

        /* Date input styling */
        input[type="date"] {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 8px 12px;
        }

        input[type="date"]:focus {
            border-color: #8B5CF6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
    </style>
@endpush
