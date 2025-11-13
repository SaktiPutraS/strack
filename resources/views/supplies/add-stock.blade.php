@extends('layouts.app')
@section('title', 'Tambah Stok Perlengkapan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Stok
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
        <div class="col-12 col-md-4">
            <div class="card luxury-card border-0 bg-gradient-{{ $supply->stock_status_color }} text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-boxes text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-white-50">Stok Saat Ini</h6>
                            <h3 class="mb-0 fw-bold text-white" id="currentStock">{{ $supply->qty }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
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
        <div class="col-12 col-md-4">
            <div class="card luxury-card border-0 bg-gradient-success text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-arrow-up-circle text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-white-50">Stok Setelah Ditambah</h6>
                            <h3 class="mb-0 fw-bold text-white" id="afterStock">{{ $supply->qty }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <form action="{{ route('supplies.add-stock', $supply) }}" method="POST" id="add-stock-form">
                @csrf

                <!-- Add Stock Form Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-box-arrow-in-down text-purple"></i>
                            </div>
                            Form Tambah Stok
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Qty Added -->
                            <div class="col-12">
                                <label for="qty_added" class="form-label fw-semibold">
                                    Jumlah Ditambahkan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-success bg-opacity-10 text-success fw-bold">
                                        <i class="bi bi-plus-circle me-1"></i>
                                    </span>
                                    <input type="number" class="form-control @error('qty_added') is-invalid @enderror" id="qty_added" name="qty_added"
                                        value="{{ old('qty_added') }}" min="1" placeholder="Masukkan jumlah stok yang ditambahkan" required>
                                </div>
                                @error('qty_added')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Masukkan jumlah stok baru yang ingin ditambahkan ke inventaris
                                </small>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    Keterangan
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                                    placeholder="Catatan penambahan stok (opsional)...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Contoh: "Pembelian dari Toko ABC", "Restok bulanan", dll
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="bi bi-eye me-2 text-purple"></i>Preview Penambahan Stok
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted">Stok Saat Ini:</td>
                                        <td class="text-end">
                                            <strong id="previewCurrent">{{ $supply->qty }}</strong> unit
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jumlah Ditambahkan:</td>
                                        <td class="text-end">
                                            <strong class="text-success" id="previewAdded">+0</strong> unit
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="fw-bold">Stok Setelah Penambahan:</td>
                                        <td class="text-end">
                                            <span id="previewTotal" class="fs-4 fw-bold text-success">{{ $supply->qty }}</span> unit
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status:</td>
                                        <td class="text-end">
                                            <span id="previewStatus" class="badge bg-{{ $supply->stock_status_color }}">
                                                {{ $supply->stock_status_text }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="normalStockInfo" style="display: none;" class="alert alert-success mt-3 mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Bagus!</strong> Stok akan kembali ke level normal setelah penambahan ini.
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
                                <i class="bi bi-check-circle me-2"></i>Tambah Stok
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
            const qtyAddedInput = document.getElementById('qty_added');
            const currentStock = {{ $supply->qty }};
            const minimumStock = {{ $supply->minimum_stock }};
            const afterStockDisplay = document.getElementById('afterStock');
            const previewAdded = document.getElementById('previewAdded');
            const previewTotal = document.getElementById('previewTotal');
            const previewStatus = document.getElementById('previewStatus');
            const normalStockInfo = document.getElementById('normalStockInfo');
            const submitBtn = document.getElementById('submitBtn');

            function updatePreview() {
                const qtyAdded = parseInt(qtyAddedInput.value) || 0;
                const totalStock = currentStock + qtyAdded;

                afterStockDisplay.textContent = totalStock;
                previewAdded.textContent = '+' + qtyAdded;
                previewTotal.textContent = totalStock;

                // Reset info
                normalStockInfo.style.display = 'none';

                // Update status badge
                if (totalStock === 0) {
                    previewStatus.className = 'badge bg-danger';
                    previewStatus.textContent = 'Habis';
                    previewTotal.className = 'fs-4 fw-bold text-danger';
                } else if (totalStock < minimumStock) {
                    previewStatus.className = 'badge bg-warning';
                    previewStatus.textContent = 'Stok Rendah';
                    previewTotal.className = 'fs-4 fw-bold text-warning';
                } else {
                    previewStatus.className = 'badge bg-success';
                    previewStatus.textContent = 'Normal';
                    previewTotal.className = 'fs-4 fw-bold text-success';

                    // Show success info if stock was previously low
                    if (currentStock < minimumStock) {
                        normalStockInfo.style.display = 'block';
                    }
                }

                // Validate
                if (qtyAdded <= 0) {
                    qtyAddedInput.setCustomValidity('Jumlah harus lebih dari 0');
                    submitBtn.disabled = true;
                } else {
                    qtyAddedInput.setCustomValidity('');
                    submitBtn.disabled = false;
                }
            }

            qtyAddedInput.addEventListener('input', updatePreview);

            // Form validation
            document.getElementById('add-stock-form').addEventListener('submit', function(e) {
                const qtyAdded = parseInt(qtyAddedInput.value) || 0;

                if (qtyAdded <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Tidak Valid',
                        text: 'Masukkan jumlah penambahan stok yang valid!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return;
                }

                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menambahkan...';
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

        .bg-gradient-success {
            background: linear-gradient(135deg, #10B981, #059669) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #F59E0B, #D97706) !important;
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #EF4444, #DC2626) !important;
        }
    </style>
@endpush
