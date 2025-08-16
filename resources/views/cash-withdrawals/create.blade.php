@extends('layouts.app')
@section('title', 'Tarik Cash Baru')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-wallet2 me-2"></i>Tarik Cash Baru
                    </h1>
                    <p class="text-muted mb-0">Penarikan cash dari Bank Octo</p>
                </div>
                <a href="{{ route('cash-withdrawals.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
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
                            <h3 class="mb-0 fw-bold text-white" id="bankBalance">Rp {{ number_format($currentBankBalance, 0, ',', '.') }}</h3>
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
                            <h6 class="mb-0 text-white-50">Saldo Cash Saat Ini</h6>
                            <h3 class="mb-0 fw-bold text-white">Rp {{ number_format($currentCashBalance, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card luxury-card border-0">
                <div class="card-header bg-gradient-warning text-white border-0 p-4">
                    <div class="d-flex align-items-center">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-arrow-down-circle text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-white">Form Penarikan Cash</h5>
                            <p class="mb-0 text-white-50">Isi detail penarikan cash dari bank</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('cash-withdrawals.store') }}" method="POST" id="withdrawalForm">
                        @csrf

                        <div class="row g-4">
                            <!-- Tanggal Penarikan -->
                            <div class="col-md-6">
                                <label for="withdrawal_date" class="form-label fw-semibold">
                                    <i class="bi bi-calendar3 text-purple me-2"></i>
                                    Tanggal Penarikan <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="withdrawal_date" id="withdrawal_date"
                                    class="form-control @error('withdrawal_date') is-invalid @enderror"
                                    value="{{ old('withdrawal_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                @error('withdrawal_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jumlah -->
                            <div class="col-md-6">
                                <label for="amount" class="form-label fw-semibold">
                                    <i class="bi bi-cash text-purple me-2"></i>
                                    Jumlah Penarikan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror"
                                        value="{{ old('amount') }}" placeholder="0" min="1" max="{{ $currentBankBalance }}" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Maksimal: Rp {{ number_format($currentBankBalance, 0, ',', '.') }}
                                </small>
                            </div>

                            <!-- Nomor Referensi -->
                            <div class="col-12">
                                <label for="reference_number" class="form-label fw-semibold">
                                    <i class="bi bi-hash text-purple me-2"></i>
                                    Nomor Referensi
                                </label>
                                <input type="text" name="reference_number" id="reference_number"
                                    class="form-control @error('reference_number') is-invalid @enderror" value="{{ old('reference_number') }}"
                                    placeholder="Nomor ATM, transfer, atau referensi lainnya">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Catatan -->
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    <i class="bi bi-journal-text text-purple me-2"></i>
                                    Catatan/Keperluan
                                </label>
                                <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Keperluan penarikan cash ini...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="mt-4">
                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-2 text-dark">
                                        <i class="bi bi-eye me-2 text-purple"></i>Preview Transaksi
                                    </h6>
                                    <div class="row text-sm">
                                        <div class="col-6">
                                            <strong>Saldo Bank Sebelum:</strong><br>
                                            <span class="text-primary">Rp {{ number_format($currentBankBalance, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Saldo Cash Sebelum:</strong><br>
                                            <span class="text-success">Rp {{ number_format($currentCashBalance, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row text-sm" id="previewAfter" style="display: none;">
                                        <div class="col-6">
                                            <strong>Saldo Bank Sesudah:</strong><br>
                                            <span class="text-primary" id="bankAfter">-</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Saldo Cash Sesudah:</strong><br>
                                            <span class="text-success" id="cashAfter">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex flex-column flex-md-row gap-3 justify-content-end mt-4">
                            <a href="{{ route('cash-withdrawals.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-warning" id="submitBtn">
                                <i class="bi bi-arrow-down-circle me-2"></i>Tarik Cash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            const previewAfter = document.getElementById('previewAfter');
            const bankAfter = document.getElementById('bankAfter');
            const cashAfter = document.getElementById('cashAfter');
            const submitBtn = document.getElementById('submitBtn');

            const currentBankBalance = {{ $currentBankBalance }};
            const currentCashBalance = {{ $currentCashBalance }};

            // Format currency
            function formatCurrency(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            // Update preview on amount change
            amountInput.addEventListener('input', function() {
                const amount = parseFloat(this.value) || 0;

                if (amount > 0) {
                    const newBankBalance = currentBankBalance - amount;
                    const newCashBalance = currentCashBalance + amount;

                    bankAfter.textContent = formatCurrency(newBankBalance);
                    cashAfter.textContent = formatCurrency(newCashBalance);

                    previewAfter.style.display = 'block';

                    // Validation
                    if (amount > currentBankBalance) {
                        this.classList.add('is-invalid');
                        bankAfter.style.color = '#EF4444';
                        submitBtn.disabled = true;
                    } else {
                        this.classList.remove('is-invalid');
                        bankAfter.style.color = '#3B82F6';
                        submitBtn.disabled = false;
                    }
                } else {
                    previewAfter.style.display = 'none';
                    this.classList.remove('is-invalid');
                    submitBtn.disabled = false;
                }
            });

            // Form validation
            document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
                const amount = parseFloat(amountInput.value) || 0;

                if (amount <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Tidak Valid',
                        text: 'Masukkan jumlah penarikan yang valid!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return;
                }

                if (amount > currentBankBalance) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Saldo Tidak Cukup',
                        text: 'Saldo Bank Octo tidak mencukupi untuk penarikan ini!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return;
                }

                // Confirmation
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Penarikan',
                    html: `
                        <div class="text-start">
                            <p><strong>Jumlah:</strong> ${formatCurrency(amount)}</p>
                            <p><strong>Saldo Bank setelah:</strong> ${formatCurrency(currentBankBalance - amount)}</p>
                            <p><strong>Saldo Cash setelah:</strong> ${formatCurrency(currentCashBalance + amount)}</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#F59E0B',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Tarik Cash',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
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

        .bg-gradient-primary {
            background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
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
            background-color: rgba(139, 92, 246, 0.1);
            border-color: rgba(139, 92, 246, 0.2);
            color: #8B5CF6;
            font-weight: 600;
        }
    </style>
@endpush
