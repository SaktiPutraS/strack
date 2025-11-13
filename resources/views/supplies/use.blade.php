@extends('layouts.app')
@section('title', 'Catat Penggunaan Perlengkapan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-arrow-down-circle me-2"></i>Catat Penggunaan
                    </h1>
                    <p class="text-muted mb-0">{{ $supply->name }}</p>
                </div>
                <a href="{{ route('supplies.show', $supply) }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Stock Info -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card luxury-card border-0 bg-gradient-info text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-boxes text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-white-50">Stok Tersedia</h6>
                            <h3 class="mb-0 fw-bold text-white" id="currentStock">{{ $supply->qty }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card luxury-card border-0 bg-gradient-warning text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-exclamation-triangle text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-white-50">Minimum Stok</h6>
                            <h3 class="mb-0 fw-bold text-white">{{ $supply->minimum_stock }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <form action="{{ route('supplies.record-usage', $supply) }}" method="POST" id="usage-form">
                @csrf

                <!-- Usage Form Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-clipboard-check text-purple"></i>
                            </div>
                            Form Penggunaan Barang
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Usage Date -->
                            <div class="col-md-6">
                                <label for="usage_date" class="form-label fw-semibold">
                                    Tanggal Penggunaan <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control form-control-lg @error('usage_date') is-invalid @enderror" id="usage_date"
                                    name="usage_date" value="{{ old('usage_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                @error('usage_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Qty Used -->
                            <div class="col-md-6">
                                <label for="qty_used" class="form-label fw-semibold">
                                    Jumlah Digunakan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-danger bg-opacity-10 text-danger fw-bold">
                                        <i class="bi bi-arrow-down-circle me-1"></i>
                                    </span>
                                    <input type="number" class="form-control @error('qty_used') is-invalid @enderror" id="qty_used" name="qty_used"
                                        value="{{ old('qty_used') }}" min="1" max="{{ $supply->qty }}" placeholder="0" required>
                                </div>
                                @error('qty_used')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="maxQtyInfo">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Maksimal: <strong>{{ $supply->qty }}</strong> unit
                                </small>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    Keterangan
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                                    placeholder="Keterangan penggunaan (opsional)...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="bi bi-eye me-2 text-purple"></i>Preview Penggunaan
                        </h6>
                        <div class="row text-sm">
                            <div class="col-6">
                                <strong>Stok Sebelum:</strong><br>
                                <span id="previewBefore">{{ $supply->qty }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Jumlah Digunakan:</strong><br>
                                <span class="text-danger" id="previewUsed">0</span>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="row text-sm">
                            <div class="col-6">
                                <strong>Stok Sesudah:</strong><br>
                                <span id="previewAfter" class="fs-5 fw-bold">{{ $supply->qty }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Status:</strong><br>
                                <span id="previewStatus" class="badge bg-{{ $supply->stock_status_color }}">
                                    {{ $supply->stock_status_text }}
                                </span>
                            </div>
                        </div>
                        <div id="lowStockWarning" style="display: none;" class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Peringatan:</strong> Stok akan berada di bawah minimum setelah penggunaan ini!
                        </div>
                        <div id="outOfStockWarning" style="display: none;" class="alert alert-danger mt-3 mb-0">
                            <i class="bi bi-x-circle me-2"></i>
                            <strong>Perhatian:</strong> Stok akan habis setelah penggunaan ini!
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <a href="{{ route('supplies.show', $supply) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Catat Penggunaan
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
            const qtyUsedInput = document.getElementById('qty_used');
            const currentStock = {{ $supply->qty }};
            const minimumStock = {{ $supply->minimum_stock }};
            const previewBefore = document.getElementById('previewBefore');
            const previewUsed = document.getElementById('previewUsed');
            const previewAfter = document.getElementById('previewAfter');
            const previewStatus = document.getElementById('previewStatus');
            const lowStockWarning = document.getElementById('lowStockWarning');
            const outOfStockWarning = document.getElementById('outOfStockWarning');
            const submitBtn = document.getElementById('submitBtn');

            function updatePreview() {
                const qtyUsed = parseInt(qtyUsedInput.value) || 0;
                const afterStock = currentStock - qtyUsed;

                previewUsed.textContent = qtyUsed;
                previewAfter.textContent = afterStock;

                // Reset warnings
                lowStockWarning.style.display = 'none';
                outOfStockWarning.style.display = 'none';

                // Update status badge
                if (afterStock === 0) {
                    previewStatus.className = 'badge bg-danger';
                    previewStatus.textContent = 'Habis';
                    previewAfter.className = 'fs-5 fw-bold text-danger';
                    outOfStockWarning.style.display = 'block';
                } else if (afterStock < minimumStock) {
                    previewStatus.className = 'badge bg-warning';
                    previewStatus.textContent = 'Stok Rendah';
                    previewAfter.className = 'fs-5 fw-bold text-warning';
                    lowStockWarning.style.display = 'block';
                } else {
                    previewStatus.className = 'badge bg-success';
                    previewStatus.textContent = 'Normal';
                    previewAfter.className = 'fs-5 fw-bold text-success';
                }

                // Validate
                if (qtyUsed > currentStock) {
                    qtyUsedInput.setCustomValidity('Jumlah melebihi stok tersedia');
                    submitBtn.disabled = true;
                } else if (qtyUsed <= 0) {
                    qtyUsedInput.setCustomValidity('Jumlah harus lebih dari 0');
                    submitBtn.disabled = true;
                } else {
                    qtyUsedInput.setCustomValidity('');
                    submitBtn.disabled = false;
                }
            }

            qtyUsedInput.addEventListener('input', updatePreview);

            // Form validation
            document.getElementById('usage-form').addEventListener('submit', function(e) {
                const qtyUsed = parseInt(qtyUsedInput.value) || 0;

                if (qtyUsed <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Tidak Valid',
                        text: 'Masukkan jumlah penggunaan yang valid!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return;
                }

                if (qtyUsed > currentStock) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok Tidak Cukup',
                        text: 'Jumlah melebihi stok yang tersedia!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return;
                }

                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                submitBtn.disabled = true;
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

        .bg-gradient-info {
            background: linear-gradient(135deg, #06B6D4, #0891B2) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #F59E0B, #D97706) !important;
        }
    </style>
@endpush
