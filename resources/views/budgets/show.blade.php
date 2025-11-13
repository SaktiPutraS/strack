@extends('layouts.app')
@section('title', 'Detail Budget')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-calendar-check me-2"></i>Budget {{ $budget->period }}
                    </h1>
                    <p class="text-muted mb-0">
                        <span class="badge bg-{{ $budget->status_color }} me-2">{{ $budget->status_text }}</span>
                        {{ $budget->total_items_count }} item pengeluaran
                    </p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Budget
                    </a>
                    <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Budget Items -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-list-check text-purple"></i>
                            </div>
                            Daftar Item Pengeluaran
                        </h5>
                        <div class="text-end">
                            <small class="text-muted d-block">Progress</small>
                            <strong class="text-{{ $budget->status_color }}">
                                {{ $budget->completed_items_count }}/{{ $budget->total_items_count }}
                            </strong>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 10px;">
                        <div class="progress-bar bg-{{ $budget->status_color }}" role="progressbar" style="width: {{ $budget->progress_percentage }}%">
                        </div>
                    </div>
                    <small class="text-muted">{{ $budget->progress_percentage }}% selesai</small>
                </div>
                <div class="card-body p-0">
                    @if ($budget->items->count() > 0)
                        <div class="p-4">
                            @foreach ($budget->items as $item)
                                <div class="budget-item {{ $item->is_completed ? 'completed' : '' }} mb-3 p-3 border rounded"
                                    data-item-id="{{ $item->id }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3">
                                                    @if ($item->is_completed)
                                                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                                    @else
                                                        <i class="bi bi-circle text-warning fs-3"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6
                                                        class="mb-1 fw-bold item-name {{ $item->is_completed ? 'text-decoration-line-through text-muted' : 'text-dark' }}">
                                                        {{ $item->item_name }}
                                                    </h6>
                                                    @if ($item->notes)
                                                        <small class="text-muted">→ {{ $item->notes }}</small>
                                                    @endif
                                                    @if ($item->is_completed && $item->completed_at)
                                                        <div class="mt-1">
                                                            <small class="text-success">
                                                                <i class="bi bi-calendar-check me-1"></i>
                                                                Selesai pada: {{ $item->completed_date }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center my-2 my-md-0">
                                            <span
                                                class="badge bg-{{ $item->is_completed ? 'success' : 'warning' }} bg-opacity-10
                                                text-{{ $item->is_completed ? 'success' : 'warning' }}
                                                border border-{{ $item->is_completed ? 'success' : 'warning' }} fs-6">
                                                {{ $item->formatted_amount }}
                                            </span>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            @if ($item->is_completed)
                                                <button type="button" class="btn btn-sm btn-outline-secondary toggle-complete-btn"
                                                    data-item-id="{{ $item->id }}">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Batalkan
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-success toggle-complete-btn"
                                                    data-item-id="{{ $item->id }}">
                                                    <i class="bi bi-check-circle me-1"></i>Tandai Selesai
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">Tidak ada item</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if ($budget->notes)
                <div class="card luxury-card border-0">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-journal-text text-purple"></i>
                            </div>
                            Catatan Budget
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-0 text-muted">{{ $budget->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Summary -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-calculator text-purple"></i>
                        </div>
                        Ringkasan Budget
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <small class="text-muted d-block mb-1">Total Budget</small>
                        <h3 class="fw-bold text-purple mb-0">{{ $budget->formatted_budget }}</h3>
                    </div>

                    <hr>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Item:</span>
                                <strong>{{ $budget->total_items_count }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-success">Selesai:</span>
                                <strong class="text-success">
                                    {{ $budget->completed_items_count }}
                                    ({{ number_format($budget->completed_amount, 0, ',', '.') }})
                                </strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-warning">Belum Selesai:</span>
                                <strong class="text-warning">
                                    {{ $budget->total_items_count - $budget->completed_items_count }}
                                    ({{ number_format($budget->remaining_amount, 0, ',', '.') }})
                                </strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <small class="text-muted d-block mb-2">Progress Keseluruhan</small>
                        <div class="position-relative d-inline-block">
                            <svg width="120" height="120">
                                <circle cx="60" cy="60" r="50" fill="none" stroke="#E5E7EB" stroke-width="10" />
                                <circle cx="60" cy="60" r="50" fill="none"
                                    stroke="{{ $budget->status == 'completed' ? '#10B981' : ($budget->status == 'progress' ? '#F59E0B' : '#6B7280') }}"
                                    stroke-width="10" stroke-dasharray="{{ 2 * 3.14159 * 50 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 50 * (1 - $budget->progress_percentage / 100) }}" transform="rotate(-90 60 60)"
                                    stroke-linecap="round" />
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <h4 class="fw-bold mb-0 text-{{ $budget->status_color }}">{{ $budget->progress_percentage }}%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-lightning text-purple"></i>
                        </div>
                        Aksi Cepat
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Budget
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash me-2"></i>Hapus Budget
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="card luxury-card border-0">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Periode</small>
                            <strong>{{ $budget->period }}</strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-{{ $budget->status_color }}">{{ $budget->status_text }}</span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Dibuat</small>
                            <strong>{{ $budget->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Terakhir Update</small>
                            <strong>{{ $budget->updated_at->format('d M Y H:i') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" action="{{ route('budgets.destroy', $budget) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
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
                    document.getElementById('delete-form').submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Toggle complete
            document.querySelectorAll('.toggle-complete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                    const btn = this;

                    // Disable button
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Loading...';

                    fetch(`/budget-items/${itemId}/toggle-complete`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Reload page to update UI
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat mengubah status',
                                confirmButtonColor: '#8B5CF6'
                            });
                            btn.disabled = false;
                            // Restore button text
                            const isCompleted = itemElement.classList.contains('completed');
                            btn.innerHTML = isCompleted ?
                                '<i class="bi bi-arrow-counterclockwise me-1"></i>Batalkan' :
                                '<i class="bi bi-check-circle me-1"></i>Tandai Selesai';
                        });
                });
            });

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
        .budget-item {
            transition: all 0.3s ease;
            background: white;
            border-color: rgba(139, 92, 246, 0.1) !important;
        }

        .budget-item:hover {
            background: rgba(139, 92, 246, 0.02);
            border-color: rgba(139, 92, 246, 0.2) !important;
        }

        .budget-item.completed {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.2) !important;
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
