@extends('layouts.app')
@section('title', $type === 'BUY' ? 'Beli Emas' : 'Jual Emas')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        @if ($type === 'BUY')
                            <i class="bi bi-plus-circle me-2"></i>Beli Emas
                        @else
                            <i class="bi bi-dash-circle me-2"></i>Jual Emas
                        @endif
                    </h1>
                    <p class="text-muted mb-0">{{ $type === 'BUY' ? 'Investasi emas baru' : 'Jual investasi emas' }}</p>
                </div>
                <a href="{{ route('gold.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Current Status Info -->
    @if ($type === 'BUY')
        <div class="row mb-4">
            <div class="col-12">
                <div class="card luxury-card border-0 bg-gradient-success text-white">
                    <div class="card-body text-center py-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="luxury-icon me-3 bg-white bg-opacity-25">
                                <i class="bi bi-bank text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-white-50">Saldo Bank Octo Saat Ini</h6>
                                <h3 class="mb-0 fw-bold text-white">Rp {{ number_format($currentBalance ?? 0, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Portfolio Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0 bg-gradient-warning text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-gem text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-white-50">Stok Emas Saat Ini</h6>
                            <h3 class="mb-0 fw-bold text-white" id="current-gold-stock">Loading...</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-{{ $type === 'BUY' ? 'plus' : 'dash' }}-circle text-{{ $type === 'BUY' ? 'success' : 'warning' }}"></i>
                        </div>
                        Form {{ $type === 'BUY' ? 'Pembelian' : 'Penjualan' }} Emas
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('gold.store') }}" method="POST" id="gold-form">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">

                        <div class="row g-4">
                            <!-- Transaction Date -->
                            <div class="col-md-6">
                                <label for="transaction_date" class="form-label fw-semibold">
                                    <i class="bi bi-calendar3 text-purple me-2"></i>
                                    Tanggal {{ $type === 'BUY' ? 'Beli' : 'Jual' }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" id="transaction_date"
                                    name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                @error('transaction_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Grams -->
                            <div class="col-md-6">
                                <label for="grams" class="form-label fw-semibold">
                                    <i class="bi bi-gem text-purple me-2"></i>
                                    Gram Emas <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-gem text-purple"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 @error('grams') is-invalid @enderror" id="grams"
                                        name="grams" value="{{ old('grams') }}" placeholder="0.000" required>
                                    <span class="input-group-text bg-light">gram</span>
                                </div>
                                @error('grams')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted" id="grams-help">
                                    @if ($type === 'SELL')
                                        Maksimal sesuai stok yang tersedia
                                    @else
                                        Minimal 0.001 gram, gunakan titik (.) untuk desimal
                                    @endif
                                </div>
                            </div>

                            <!-- Total Price -->
                            <div class="col-12">
                                <label for="total_price" class="form-label fw-semibold">
                                    <i class="bi bi-cash text-purple me-2"></i>
                                    Harga Total <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-currency-dollar text-purple"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 @error('total_price') is-invalid @enderror" id="total_price"
                                        name="total_price" value="{{ old('total_price') }}" placeholder="0" required>
                                </div>
                                @error('total_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted" id="price-help">
                                    @if ($type === 'BUY')
                                        Jumlah yang akan dikeluarkan dari Bank Octo (hanya angka, tanpa titik/koma)
                                    @else
                                        Jumlah yang akan diterima ke Bank Octo (hanya angka, tanpa titik/koma)
                                    @endif
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    <i class="bi bi-journal-text text-purple me-2"></i>
                                    Catatan
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                    placeholder="Contoh: Emas Antam via Tokopedia, Jual untuk dana project, dll">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Calculation Preview -->
                        <div class="mt-4" id="calculation-preview" style="display: none;">
                            <div class="card luxury-card border-0 bg-light">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-3 d-flex align-items-center">
                                        <div class="luxury-icon me-2">
                                            <i class="bi bi-calculator text-primary"></i>
                                        </div>
                                        Preview Perhitungan
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Harga per Gram</small>
                                                <div class="fw-bold text-primary fs-5" id="price-per-gram">Rp 0</div>
                                            </div>
                                        </div>
                                        @if ($type === 'BUY')
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <small class="text-muted d-block">Saldo Setelah Beli</small>
                                                    <div class="fw-bold text-success fs-5" id="balance-after">Rp 0</div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <small
                                                    class="text-muted d-block">{{ $type === 'BUY' ? 'Total Emas Setelah Beli' : 'Sisa Emas Setelah Jual' }}</small>
                                                <div class="fw-bold text-warning fs-5" id="gold-after">0 gram</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mt-4 pt-4 border-top">
                            <a href="{{ route('gold.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-warning" onclick="resetCalculation()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-{{ $type === 'BUY' ? 'success' : 'warning' }}">
                                    <i class="bi bi-{{ $type === 'BUY' ? 'plus' : 'dash' }}-circle me-2"></i>
                                    {{ $type === 'BUY' ? 'Beli' : 'Jual' }} Emas
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const transactionType = '{{ $type }}';
        const currentBalance = {{ $currentBalance ?? 0 }};
        let currentGoldStock = 0;

        document.addEventListener('DOMContentLoaded', function() {
            loadCurrentGoldStock();

            const gramsInput = document.getElementById('grams');
            const priceInput = document.getElementById('total_price');

            // Format input untuk gram (allow decimal dengan titik)
            gramsInput.addEventListener('input', function(e) {
                let value = e.target.value;
                value = value.replace(/[^0-9.]/g, '');
                const parts = value.split('.');
                if (parts.length > 2) {
                    value = parts[0] + '.' + parts.slice(1).join('');
                }
                e.target.value = value;
                updateCalculation();
            });

            // Format input untuk total price (hanya angka)
            priceInput.addEventListener('input', function(e) {
                let value = e.target.value;
                value = value.replace(/[^0-9]/g, '');
                e.target.value = value;
                updateCalculation();
            });

            // Animation
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

            // Form submission dengan konversi data
            document.getElementById('gold-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const gramsValue = document.getElementById('grams').value;
                const priceValue = document.getElementById('total_price').value;

                if (!gramsValue || !priceValue) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        text: 'Semua field wajib diisi!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return false;
                }

                const grams = parseFloat(gramsValue.replace(',', '.')) || 0;
                const totalPrice = parseInt(priceValue) || 0;

                if (grams <= 0 || totalPrice <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Nilai Invalid',
                        text: 'Gram dan harga harus lebih dari 0!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return false;
                }

                if (transactionType === 'BUY') {
                    if (totalPrice > currentBalance) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Saldo Tidak Cukup',
                            text: 'Saldo Bank Octo tidak mencukupi!',
                            confirmButtonColor: '#8B5CF6'
                        });
                        return false;
                    }
                } else {
                    if (grams > currentGoldStock) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Stok Tidak Cukup',
                            text: 'Stok emas tidak mencukupi!',
                            confirmButtonColor: '#8B5CF6'
                        });
                        return false;
                    }
                }

                // Confirmation
                const action = transactionType === 'BUY' ? 'membeli' : 'menjual';
                const pricePerGram = totalPrice / grams;

                let html = `
                    <div class="text-start">
                        <p><strong>Gram:</strong> ${grams.toFixed(3)} gram</p>
                        <p><strong>Harga Total:</strong> Rp ${new Intl.NumberFormat('id-ID').format(totalPrice)}</p>
                        <p><strong>Harga per Gram:</strong> Rp ${new Intl.NumberFormat('id-ID').format(pricePerGram)}</p>
                        ${transactionType === 'BUY' ? `<p class="text-muted mt-3">Saldo setelah beli: <strong>Rp ${new Intl.NumberFormat('id-ID').format(currentBalance - totalPrice)}</strong></p>` : ''}
                    </div>
                `;

                Swal.fire({
                    title: `Konfirmasi ${transactionType === 'BUY' ? 'Pembelian' : 'Penjualan'}`,
                    html: html,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: transactionType === 'BUY' ? '#10B981' : '#F59E0B',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: `Ya, ${action.charAt(0).toUpperCase() + action.slice(1)}`,
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses Transaksi...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Set nilai yang sudah diformat
                        document.getElementById('grams').value = grams;
                        document.getElementById('total_price').value = totalPrice;

                        // Submit form
                        this.submit();
                    }
                });
            });
        });

        function loadCurrentGoldStock() {
            // Mock data for now
            currentGoldStock = 0;
            document.getElementById('current-gold-stock').textContent = '0.000 gram';
        }

        function updateCalculation() {
            const gramsValue = document.getElementById('grams').value;
            const priceValue = document.getElementById('total_price').value;

            const grams = parseFloat(gramsValue.replace(',', '.')) || 0;
            const totalPrice = parseInt(priceValue) || 0;

            if (grams > 0 && totalPrice > 0) {
                const pricePerGram = totalPrice / grams;

                document.getElementById('price-per-gram').textContent =
                    'Rp ' + new Intl.NumberFormat('id-ID').format(pricePerGram);

                if (transactionType === 'BUY') {
                    const balanceAfter = currentBalance - totalPrice;
                    document.getElementById('balance-after').textContent =
                        'Rp ' + new Intl.NumberFormat('id-ID').format(balanceAfter);
                    document.getElementById('balance-after').className =
                        balanceAfter >= 0 ? 'fw-bold text-success fs-5' : 'fw-bold text-danger fs-5';

                    const goldAfter = currentGoldStock + grams;
                    document.getElementById('gold-after').textContent =
                        goldAfter.toFixed(3) + ' gram';
                } else {
                    const goldAfter = currentGoldStock - grams;
                    document.getElementById('gold-after').textContent =
                        goldAfter.toFixed(3) + ' gram';
                    document.getElementById('gold-after').className =
                        goldAfter >= 0 ? 'fw-bold text-warning fs-5' : 'fw-bold text-danger fs-5';
                }

                // Show calculation preview with animation
                const calculationPreview = document.getElementById('calculation-preview');
                calculationPreview.style.display = 'block';
                setTimeout(() => {
                    calculationPreview.style.opacity = '1';
                    calculationPreview.style.transform = 'translateY(0)';
                }, 50);
            } else {
                document.getElementById('calculation-preview').style.display = 'none';
            }
        }

        function resetCalculation() {
            document.getElementById('calculation-preview').style.display = 'none';
        }
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

        .form-control:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        .input-group-text {
            background: rgba(139, 92, 246, 0.05);
            border-color: rgba(139, 92, 246, 0.2);
        }

        #calculation-preview {
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }
    </style>
@endpush
