@extends('layouts.app')
@section('title', 'Budgeting')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-calculator me-2"></i>Budgeting
                    </h1>
                    <p class="text-muted mb-0">Perencanaan dan tracking pengeluaran bulanan</p>
                </div>
                <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Buat Budget Baru
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
                        <i class="bi bi-calendar-check text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $stats['total_budgets'] }}</h3>
                    <small class="text-muted fw-semibold">Budget Tahun Ini</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="background: rgba(16, 185, 129, 0.1)">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $stats['completed_budgets'] }}</h3>
                    <small class="text-muted fw-semibold">Selesai Sempurna</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-info h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="background: rgba(6, 182, 212, 0.1)">
                        <i class="bi bi-calculator text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1 fs-6">{{ number_format($stats['avg_budget'], 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Rata-rata/Bulan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2" style="background: rgba(245, 158, 11, 0.1)">
                        <i class="bi bi-cash-stack text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1 fs-6">{{ number_format($stats['total_year'], 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Total Tahun Ini</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Budgets List -->
    <div class="card luxury-card border-0">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-list-ul text-purple"></i>
                        </div>
                        Daftar Budget
                    </h5>
                    <p class="text-muted mb-0">{{ $budgets->total() }} budget</p>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center">
                    <!-- Button Laporan - TAMBAH INI -->
                    <a href="{{ route('budgets.report', $selectedYear) }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Lihat Laporan
                    </a>

                    <!-- Filter -->
                    <form method="GET" class="d-flex gap-2">
                        <select name="year" class="form-select" onchange="this.form.submit()">
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Baru</option>
                            <option value="progress" {{ request('status') == 'progress' ? 'selected' : '' }}>Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        @if (request()->hasAny(['status']))
                            <a href="{{ route('budgets.index', ['year' => $selectedYear]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($budgets->count() > 0)
                <!-- Budget Cards Grid -->
                <div class="p-4">
                    <div class="row g-3">
                        @foreach ($budgets as $budget)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card luxury-card border-0 h-100 budget-card">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="fw-bold mb-1">{{ $budget->period }}</h5>
                                                <span class="badge bg-{{ $budget->status_color }}">
                                                    {{ $budget->status_text }}
                                                </span>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('budgets.show', $budget) }}">
                                                            <i class="bi bi-eye me-2"></i>Detail
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('budgets.edit', $budget) }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" onclick="confirmDelete({{ $budget->id }})">
                                                            <i class="bi bi-trash me-2"></i>Hapus
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted small">Total Budget</span>
                                                <span class="fw-bold text-purple">{{ $budget->formatted_budget }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted small">{{ $budget->total_items_count }} Item</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted small">Progress</span>
                                                <span class="fw-bold text-{{ $budget->status_color }}">
                                                    {{ $budget->completed_items_count }}/{{ $budget->total_items_count }}
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $budget->status_color }}" role="progressbar"
                                                    style="width: {{ $budget->progress_percentage }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $budget->progress_percentage }}%</small>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <a href="{{ route('budgets.show', $budget) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i>Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Form -->
                            <form id="delete-form-{{ $budget->id }}" action="{{ route('budgets.destroy', $budget) }}" method="POST"
                                style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if ($budgets->hasPages())
                    <div class="card-footer bg-light border-0 p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-muted mb-0">
                                    Menampilkan {{ $budgets->firstItem() }}-{{ $budgets->lastItem() }}
                                    dari {{ $budgets->total() }} budget
                                </p>
                            </div>
                            <div class="col-md-6">
                                <nav class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                                    {{ $budgets->links() }}
                                </nav>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-calculator text-muted" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Belum ada budget</h5>
                    <p class="text-muted mb-4">Mulai dengan membuat budget bulanan pertama</p>
                    <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Buat Budget Baru
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(budgetId) {
            Swal.fire({
                title: 'Yakin menghapus budget?',
                text: "Semua item budget akan ikut terhapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + budgetId).submit();
                }
            });
        }

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

        .stat-card-info::before {
            background: linear-gradient(90deg, #06B6D4, #0891B2);
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #F59E0B, #D97706);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
        }

        .budget-card {
            transition: all 0.3s ease;
        }

        .budget-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 48px rgba(139, 92, 246, 0.15);
        }

        .luxury-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(139, 92, 246, 0.08);
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
            border-radius: 16px;
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

        .progress {
            border-radius: 10px;
            background-color: rgba(139, 92, 246, 0.1);
        }

        .progress-bar {
            border-radius: 10px;
        }
    </style>
@endpush
