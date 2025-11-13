@extends('layouts.app')
@section('title', 'Tambah Perlengkapan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Perlengkapan
                    </h1>
                    <p class="text-muted mb-0">Tambahkan item perlengkapan baru ke inventaris</p>
                </div>
                <a href="{{ route('supplies.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <form action="{{ route('supplies.store') }}" method="POST">
                @csrf

                <!-- Basic Information Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-info-circle text-purple"></i>
                            </div>
                            Informasi Perlengkapan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Name -->
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold">
                                    Nama Barang <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" placeholder="Contoh: Kertas A4, Tinta Printer, dll" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Qty -->
                            <div class="col-md-6">
                                <label for="qty" class="form-label fw-semibold">
                                    Jumlah/Stok Awal <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-success bg-opacity-10 text-success fw-bold">
                                        <i class="bi bi-boxes me-1"></i>
                                    </span>
                                    <input type="number" class="form-control @error('qty') is-invalid @enderror" id="qty" name="qty"
                                        value="{{ old('qty', 0) }}" min="0" placeholder="0" required>
                                </div>
                                @error('qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Jumlah stok saat ini</small>
                            </div>

                            <!-- Minimum Stock -->
                            <div class="col-md-6">
                                <label for="minimum_stock" class="form-label fw-semibold">
                                    Minimum Stok <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-warning bg-opacity-10 text-warning fw-bold">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                    </span>
                                    <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" id="minimum_stock"
                                        name="minimum_stock" value="{{ old('minimum_stock', 0) }}" min="0" placeholder="0" required>
                                </div>
                                @error('minimum_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Batas minimum stok untuk peringatan</small>
                            </div>

                            <!-- Order Link -->
                            <div class="col-12">
                                <label for="order_link" class="form-label fw-semibold">
                                    Link Order/Pembelian
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-link-45deg"></i>
                                    </span>
                                    <input type="url" class="form-control @error('order_link') is-invalid @enderror" id="order_link" name="order_link"
                                        value="{{ old('order_link') }}" placeholder="https://...">
                                </div>
                                @error('order_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Link untuk memesan/membeli ulang barang ini</small>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    Keterangan
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                                    placeholder="Catatan tambahan tentang barang ini...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <a href="{{ route('supplies.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Simpan Perlengkapan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qtyInput = document.getElementById('qty');
            const minStockInput = document.getElementById('minimum_stock');

            // Validasi minimum stock tidak boleh lebih besar dari qty
            minStockInput.addEventListener('input', function() {
                const qty = parseInt(qtyInput.value) || 0;
                const minStock = parseInt(this.value) || 0;

                if (minStock > qty) {
                    this.setCustomValidity('Minimum stok tidak boleh lebih besar dari jumlah stok');
                } else {
                    this.setCustomValidity('');
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
    </style>
@endpush
