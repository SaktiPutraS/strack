@extends('layouts.app')
@section('title', 'Detail Pengeluaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-receipt me-2"></i>Detail Pengeluaran
                    </h1>
                    <p class="text-muted mb-0">{{ $expense->description }}</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Expense Details -->
        <div class="col-md-8">
            <!-- Main Information -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-info-circle text-purple"></i>
                        </div>
                        Informasi Pengeluaran
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Jumlah Pengeluaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash text-purple me-2"></i>
                                <strong class="text-danger fs-3">{{ $expense->formatted_amount }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Sumber Dana</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $expense->source_icon }} text-purple me-2"></i>
                                <span
                                    class="badge bg-{{ $expense->source_color }} bg-opacity-10 text-{{ $expense->source_color }} border border-{{ $expense->source_color }} fs-6">
                                    <i class="bi bi-{{ $expense->source_icon }} me-1"></i>
                                    {{ $expense->source_label }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Tanggal Pengeluaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 text-purple me-2"></i>
                                <div>
                                    <strong>{{ $expense->expense_date->format('d M Y') }}</strong>
                                    <small class="text-muted ms-2">({{ $expense->expense_date->diffForHumans() }})</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Kategori</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tag text-purple me-2"></i>
                                <span class="badge bg-purple-light text-purple border border-purple fs-6">{{ $expense->category_label }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold">Deskripsi</label>
                            <div class="d-flex align-items-start">
                                <i class="bi bi-journal-text text-purple me-2 mt-1"></i>
                                <p class="mb-0 text-dark">{{ $expense->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-clock-history text-purple"></i>
                        </div>
                        Riwayat Transaksi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Dibuat pada</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-plus-circle text-purple me-2"></i>
                                <div>
                                    <strong>{{ $expense->created_at->format('d M Y') }}</strong>
                                    <small class="text-muted ms-2">{{ $expense->created_at->format('H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Terakhir diperbarui</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-pencil-square text-purple me-2"></i>
                                <div>
                                    <strong>{{ $expense->updated_at->format('d M Y') }}</strong>
                                    <small class="text-muted ms-2">{{ $expense->updated_at->format('H:i') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
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
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Pengeluaran
                        </a>
                        <a href="{{ route('expenses.create') }}" class="btn btn-outline-success">
                            <i class="bi bi-plus-circle me-2"></i>Pengeluaran Baru
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash me-2"></i>Hapus Pengeluaran
                        </button>
                    </div>
                </div>
            </div>

            <!-- Expense Summary -->
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-calculator text-purple"></i>
                        </div>
                        Ringkasan
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">ID Transaksi:</span>
                                <strong>#{{ str_pad($expense->id, 4, '0', STR_PAD_LEFT) }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Sumber Dana:</span>
                                <span
                                    class="badge bg-{{ $expense->source_color }} bg-opacity-10 text-{{ $expense->source_color }} border border-{{ $expense->source_color }}">
                                    <i class="bi bi-{{ $expense->source_icon }} me-1"></i>
                                    {{ $expense->source_label }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Bulan:</span>
                                <strong>{{ $expense->expense_date->format('M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Hari:</span>
                                <strong>{{ $expense->expense_date->format('l') }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Status:</span>
                                <span class="badge bg-success">Tercatat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Yakin menghapus pengeluaran?',
                text: "Tindakan ini tidak dapat dibatalkan!",
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

            // Add animation to cards
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
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.05) !important;
        }

        .border-purple {
            border-color: rgba(139, 92, 246, 0.3) !important;
        }

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }
        }
    </style>
@endpush
