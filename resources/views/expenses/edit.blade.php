@extends('layouts.app')
@section('title', 'Edit Pengeluaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-pencil-square me-2"></i>Edit Pengeluaran
                    </h1>
                    <p class="text-muted mb-0">{{ $expense->formatted_amount }} - {{ $expense->category_label }}</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Lihat Detail
                    </a>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <form action="{{ route('expenses.update', $expense) }}" method="POST" id="expense-form">
                @csrf
                @method('PUT')

                <!-- Expense Details Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-credit-card text-purple"></i>
                            </div>
                            Update Data Pengeluaran
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Expense Date -->
                            <div class="col-md-6">
                                <label for="expense_date" class="form-label fw-semibold">
                                    Tanggal Pengeluaran <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control form-control-lg @error('expense_date') is-invalid @enderror" id="expense_date"
                                    name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                                    max="{{ date('Y-m-d') }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <label for="amount" class="form-label fw-semibold">
                                    Jumlah Pengeluaran <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-danger bg-opacity-10 text-danger fw-bold">Rp</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount"
                                        value="{{ old('amount', $expense->amount) }}" min="1000" placeholder="50000" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimal Rp 1.000</div>
                            </div>

                            <!-- Category -->
                            <div class="col-12">
                                <label for="category" class="form-label fw-semibold">
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select name="category" id="category" class="form-select form-select-lg @error('category') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach (\App\Models\Expense::CATEGORIES as $key => $label)
                                        <option value="{{ $key }}" {{ old('category', $expense->category) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">
                                    Deskripsi <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4"
                                    placeholder="Contoh: Hosting bulanan untuk website client, Kopi meeting dengan klien, dll" required>{{ old('description', $expense->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Data Info -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-info-circle text-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-info mb-2">Data Saat Ini</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <small class="text-muted fw-semibold">Tanggal Awal:</small>
                                        <div class="fw-bold">{{ $expense->expense_date->format('d M Y') }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted fw-semibold">Jumlah Awal:</small>
                                        <div class="fw-bold text-danger">{{ $expense->formatted_amount }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted fw-semibold">Kategori Awal:</small>
                                        <div class="fw-bold">{{ $expense->category_label }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted fw-semibold">Dibuat:</small>
                                        <div class="fw-bold">{{ $expense->created_at->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <button type="button" class="btn btn-outline-danger btn-lg" onclick="confirmDelete()">
                                    <i class="bi bi-trash me-2"></i>Hapus Pengeluaran
                                </button>
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-info btn-lg">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </a>
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Update Pengeluaran
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Delete Form -->
            <form id="delete-form" action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
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

            // Form validation
            document.getElementById('expense-form').addEventListener('submit', function(e) {
                const amount = parseFloat(document.getElementById('amount').value) || 0;

                if (amount < 1000) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Tidak Valid',
                        text: 'Jumlah pengeluaran minimal Rp 1.000',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return false;
                }

                const submitBtn = e.target.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengupdate...';
                submitBtn.disabled = true;
            });

            // Format amount display
            document.getElementById('amount').addEventListener('input', function() {
                const value = this.value;
                if (value) {
                    this.title = 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                }
            });
        });
    </script>

    <style>
        .form-control:focus,
        .form-select:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
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

        @media (max-width: 768px) {
            .card-header h5 {
                font-size: 1.1rem;
            }
        }
    </style>
@endpush
