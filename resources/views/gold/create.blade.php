{{-- resources/views/gold/create.blade.php - FIXED VERSION --}}
@extends('layouts.app')
@section('title', $type === 'BUY' ? 'Beli Emas' : 'Jual Emas')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    @if ($type === 'BUY')
                        <i class="bi bi-plus-circle text-success"></i>Beli Emas
                    @else
                        <i class="bi bi-dash-circle text-warning"></i>Jual Emas
                    @endif
                </h1>
                <div class="btn-group">
                    <a href="{{ route('gold.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Current Status Info -->
            <div class="card mb-4 border-{{ $type === 'BUY' ? 'success' : 'warning' }}">
                <div class="card-body">
                    <h6 class="text-{{ $type === 'BUY' ? 'success' : 'warning' }} mb-3">
                        <i class="bi bi-info-circle me-2"></i>Informasi Saat Ini
                    </h6>
                    <div class="row g-3">
                        @if ($type === 'BUY')
                            <div class="col-md-6">
                                <small class="text-muted">Saldo Bank Octo:</small>
                                <div class="fw-bold text-success">Rp {{ number_format($currentBalance, 0, ',', '.') }}</div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <small class="text-muted">Stok Emas:</small>
                            <div class="fw-bold text-warning" id="current-gold-stock">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Form -->
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-{{ $type === 'BUY' ? 'plus' : 'dash' }}-circle"></i>
                        Form {{ $type === 'BUY' ? 'Pembelian' : 'Penjualan' }} Emas
                    </h5>

                    <form action="{{ route('gold.store') }}" method="POST" id="gold-form">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">

                        <div class="row g-3">
                            <!-- Transaction Date -->
                            <div class="col-md-6">
                                <label for="transaction_date" class="form-label">
                                    <i class="bi bi-calendar3 text-lilac me-2"></i>
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
                                <label for="grams" class="form-label">
                                    <i class="bi bi-gem text-lilac me-2"></i>
                                    Gram Emas <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('grams') is-invalid @enderror" id="grams" name="grams"
                                        value="{{ old('grams') }}" placeholder="0.000" required pattern="[0-9]+([.,][0-9]+)?">
                                    <span class="input-group-text">gram</span>
                                </div>
                                @error('grams')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text" id="grams-help">
                                    @if ($type === 'SELL')
                                        Maksimal sesuai stok yang tersedia
                                    @else
                                        Minimal 0.001 gram, gunakan titik (.) untuk desimal
                                    @endif
                                </div>
                            </div>

                            <!-- Total Price -->
                            <div class="col-12">
                                <label for="total_price" class="form-label">
                                    <i class="bi bi-cash text-lilac me-2"></i>
                                    Harga Total <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control @error('total_price') is-invalid @enderror" id="total_price"
                                        name="total_price" value="{{ old('total_price') }}" placeholder="0" required>
                                </div>
                                @error('total_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text" id="price-help">
                                    @if ($type === 'BUY')
                                        Jumlah yang akan dikeluarkan dari Bank Octo (hanya angka, tanpa titik/koma)
                                    @else
                                        Jumlah yang akan diterima ke Bank Octo (hanya angka, tanpa titik/koma)
                                    @endif
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label">
                                    <i class="bi bi-journal-text text-lilac me-2"></i>
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
                        <div class="mt-4 p-3 bg-light rounded" id="calculation-preview" style="display: none;">
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-calculator text-primary me-2"></i>
                                Preview Perhitungan
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Harga per Gram:</small>
                                    <div class="fw-bold text-primary" id="price-per-gram">Rp 0</div>
                                </div>
                                @if ($type === 'BUY')
                                    <div class="col-md-4">
                                        <small class="text-muted">Saldo Setelah Beli:</small>
                                        <div class="fw-bold text-success" id="balance-after">Rp 0</div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <small class="text-muted">{{ $type === 'BUY' ? 'Total Emas Setelah Beli' : 'Sisa Emas Setelah Jual' }}:</small>
                                    <div class="fw-bold text-warning" id="gold-after">0 gram</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('gold.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2" onclick="resetCalculation()">
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
        const currentBalance = {{ $currentBalance }};
        let currentGoldStock = 0;

        document.addEventListener('DOMContentLoaded', function() {
            loadCurrentGoldStock();

            const gramsInput = document.getElementById('grams');
            const priceInput = document.getElementById('total_price');

            // Format input untuk gram (allow decimal dengan titik)
            gramsInput.addEventListener('input', function(e) {
                let value = e.target.value;
                // Hanya izinkan angka dan titik
                value = value.replace(/[^0-9.]/g, '');
                // Pastikan hanya ada satu titik
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
                // Hanya izinkan angka
                value = value.replace(/[^0-9]/g, '');
                e.target.value = value;
                updateCalculation();
            });

            // Form submission dengan konversi data
            document.getElementById('gold-form').addEventListener('submit', function(e) {
                // Konversi grams ke format yang benar untuk server
                const gramsValue = document.getElementById('grams').value;
                const priceValue = document.getElementById('total_price').value;

                if (!gramsValue || !priceValue) {
                    e.preventDefault();
                    alert('Semua field wajib diisi!');
                    return false;
                }

                const grams = parseFloat(gramsValue.replace(',', '.')) || 0;
                const totalPrice = parseInt(priceValue) || 0;

                if (grams <= 0 || totalPrice <= 0) {
                    e.preventDefault();
                    alert('Gram dan harga harus lebih dari 0!');
                    return false;
                }

                if (transactionType === 'BUY') {
                    if (totalPrice > currentBalance) {
                        e.preventDefault();
                        alert('Saldo Bank Octo tidak mencukupi!');
                        return false;
                    }
                } else {
                    if (grams > currentGoldStock) {
                        e.preventDefault();
                        alert('Stok emas tidak mencukupi!');
                        return false;
                    }
                }

                // Set nilai yang sudah diformat ke hidden inputs atau update nilai yang ada
                document.getElementById('grams').value = grams;
                document.getElementById('total_price').value = totalPrice;
            });
        });

        function loadCurrentGoldStock() {
            // Mock data untuk sementara jika API belum tersedia
            currentGoldStock = 0; // Default value
            document.getElementById('current-gold-stock').textContent = '0.000 gram';

            // Uncomment jika API sudah tersedia
            /*
            fetch('{{ route('api.gold.portfolio') }}')
                .then(response => response.json())
                .then(data => {
                    currentGoldStock = data.current_grams;
                    document.getElementById('current-gold-stock').textContent =
                        data.formatted_current_grams + ' gram';

                    if (transactionType === 'SELL') {
                        document.getElementById('grams-help').textContent =
                            `Maksimal ${data.formatted_current_grams} gram (stok tersedia)`;
                    }
                })
                .catch(error => {
                    console.error('Error loading gold portfolio:', error);
                    document.getElementById('current-gold-stock').textContent = 'Error loading';
                });
            */
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
                        balanceAfter >= 0 ? 'fw-bold text-success' : 'fw-bold text-danger';

                    const goldAfter = currentGoldStock + grams;
                    document.getElementById('gold-after').textContent =
                        goldAfter.toFixed(3) + ' gram';
                } else {
                    const goldAfter = currentGoldStock - grams;
                    document.getElementById('gold-after').textContent =
                        goldAfter.toFixed(3) + ' gram';
                    document.getElementById('gold-after').className =
                        goldAfter >= 0 ? 'fw-bold text-warning' : 'fw-bold text-danger';
                }

                document.getElementById('calculation-preview').style.display = 'block';
            } else {
                document.getElementById('calculation-preview').style.display = 'none';
            }
        }

        function resetCalculation() {
            document.getElementById('calculation-preview').style.display = 'none';
        }
    </script>
@endpush
