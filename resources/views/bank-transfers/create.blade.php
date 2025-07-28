@extends('layouts.app')
@section('title', 'Transfer ke Bank Octo')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-bank me-2"></i>Transfer ke Bank Octo
                    </h1>
                    <p class="text-muted mb-0">Buat transfer pembayaran baru</p>
                </div>
                <a href="{{ route('bank-transfers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Current Bank Balance Info -->
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
                            <h3 class="mb-0 fw-bold text-white" id="current-balance-display">
                                {{ 'Rp ' . number_format($currentBalance ?? 0, 0, ',', '.') }}
                            </h3>
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
                            <i class="bi bi-plus-circle text-purple"></i>
                        </div>
                        Form Transfer Baru
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('bank-transfers.store') }}" method="POST" id="transfer-form">
                        @csrf

                        <div class="row g-4">
                            <!-- Payment Selection -->
                            <div class="col-12">
                                <label for="payment_id" class="form-label fw-semibold">
                                    <i class="bi bi-credit-card text-purple me-2"></i>
                                    Pembayaran yang Akan Ditransfer <span class="text-danger">*</span>
                                </label>
                                <select name="payment_id" id="payment_id" class="form-select @error('payment_id') is-invalid @enderror" required>
                                    <option value="">Pilih Pembayaran</option>
                                    @if (isset($untransferredPayments))
                                        @foreach ($untransferredPayments as $payment)
                                            <option value="{{ $payment->id }}" data-amount="{{ $payment->amount }}"
                                                data-project="{{ $payment->project->title }}" data-client="{{ $payment->project->client->name }}"
                                                data-date="{{ $payment->payment_date->format('d M Y') }}"
                                                {{ old('payment_id', request('payment_id')) == $payment->id ? 'selected' : '' }}>
                                                {{ $payment->formatted_amount }} - {{ $payment->project->title }}
                                                ({{ $payment->project->client->name }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('payment_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Info Card (Dynamic) -->
                            <div class="col-12" id="payment-info" style="display: none;">
                                <div class="card luxury-card border-0 bg-gradient-primary text-white">
                                    <div class="card-body p-3">
                                        <h6 class="text-white mb-3 d-flex align-items-center">
                                            <div class="luxury-icon me-2 bg-white bg-opacity-25">
                                                <i class="bi bi-info-circle text-white"></i>
                                            </div>
                                            Informasi Pembayaran
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <small class="text-white-50">Proyek:</small>
                                                <div class="fw-bold text-white" id="payment-project">-</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-white-50">Klien:</small>
                                                <div class="fw-bold text-white" id="payment-client">-</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-white-50">Tanggal Bayar:</small>
                                                <div class="fw-bold text-white" id="payment-date">-</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-white-50">Jumlah Asli:</small>
                                                <div class="fw-bold text-white" id="payment-amount">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer Date & Amount -->
                            <div class="col-md-6">
                                <label for="transfer_date" class="form-label fw-semibold">
                                    <i class="bi bi-calendar3 text-purple me-2"></i>
                                    Tanggal Transfer <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('transfer_date') is-invalid @enderror" id="transfer_date"
                                    name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                @error('transfer_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="transfer_amount" class="form-label fw-semibold">
                                    <i class="bi bi-cash text-purple me-2"></i>
                                    Jumlah Transfer <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-currency-dollar text-purple"></i>
                                    </span>
                                    <input type="number" class="form-control border-start-0 @error('transfer_amount') is-invalid @enderror"
                                        id="transfer_amount" name="transfer_amount" value="{{ old('transfer_amount') }}" min="1" placeholder="0"
                                        required>
                                </div>
                                @error('transfer_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <span id="amount-help">Bisa berbeda dari jumlah asli (dipotong fee transfer)</span>
                                </div>
                            </div>

                            <!-- Reference & Fee -->
                            <div class="col-md-6">
                                <label for="reference_number" class="form-label fw-semibold">
                                    <i class="bi bi-hash text-purple me-2"></i>
                                    Nomor Referensi
                                </label>
                                <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number"
                                    name="reference_number" value="{{ old('reference_number') }}" placeholder="Nomor transaksi bank">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">Opsional - nomor transaksi dari bank</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-info-circle text-purple me-2"></i>
                                    Fee Transfer
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-percent text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 bg-light" id="transfer-fee" readonly>
                                </div>
                                <div class="form-text text-muted">Otomatis terhitung dari selisih</div>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    <i class="bi bi-journal-text text-purple me-2"></i>
                                    Catatan
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                    placeholder="Catatan tambahan tentang transfer ini (opsional)">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Bank Balance Preview -->
                        <div class="mt-4" id="balance-preview" style="display: none;">
                            <div class="card luxury-card border-0 bg-light">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-3 d-flex align-items-center">
                                        <div class="luxury-icon me-2">
                                            <i class="bi bi-bank text-primary"></i>
                                        </div>
                                        Preview Saldo Bank Octo
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Saldo Saat Ini</small>
                                                <div class="fw-bold text-primary fs-5" id="current-balance">Loading...</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Transfer Masuk</small>
                                                <div class="fw-bold text-success fs-5" id="transfer-in">Rp 0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <small class="text-muted d-block">Saldo Setelah Transfer</small>
                                                <div class="fw-bold text-warning fs-5" id="balance-after">Rp 0</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mt-4 pt-4 border-top">
                            <a href="{{ route('bank-transfers.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-warning" onclick="resetForm()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-bank me-2"></i>Proses Transfer
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
        let currentBankBalance = {{ $currentBalance ?? 0 }};

        document.addEventListener('DOMContentLoaded', function() {
            const paymentSelect = document.getElementById('payment_id');
            const transferAmountInput = document.getElementById('transfer_amount');

            // Load current bank balance
            document.getElementById('current-balance').textContent = formatCurrency(currentBankBalance);

            // Event listeners
            paymentSelect.addEventListener('change', updatePaymentInfo);
            transferAmountInput.addEventListener('input', updateBalancePreview);

            // Auto-select if payment_id in URL
            if (paymentSelect.value) {
                updatePaymentInfo();
            }

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

        function updatePaymentInfo() {
            const paymentSelect = document.getElementById('payment_id');
            const selectedOption = paymentSelect.options[paymentSelect.selectedIndex];
            const paymentInfo = document.getElementById('payment-info');

            if (selectedOption.value) {
                const amount = parseFloat(selectedOption.dataset.amount) || 0;
                const project = selectedOption.dataset.project;
                const client = selectedOption.dataset.client;
                const date = selectedOption.dataset.date;

                // Update payment info display
                document.getElementById('payment-project').textContent = project;
                document.getElementById('payment-client').textContent = client;
                document.getElementById('payment-date').textContent = date;
                document.getElementById('payment-amount').textContent = formatCurrency(amount);

                // Auto-fill transfer amount with original amount
                document.getElementById('transfer_amount').value = amount;

                // Show payment info with animation
                paymentInfo.style.display = 'block';
                setTimeout(() => {
                    paymentInfo.style.opacity = '1';
                    paymentInfo.style.transform = 'translateY(0)';
                }, 50);

                // Update previews
                updateBalancePreview();
            } else {
                paymentInfo.style.display = 'none';
                document.getElementById('balance-preview').style.display = 'none';
            }
        }

        function updateBalancePreview() {
            const transferAmount = parseFloat(document.getElementById('transfer_amount').value) || 0;
            const paymentSelect = document.getElementById('payment_id');
            const selectedOption = paymentSelect.options[paymentSelect.selectedIndex];

            if (selectedOption.value && transferAmount > 0) {
                const originalAmount = parseFloat(selectedOption.dataset.amount) || 0;
                const fee = originalAmount - transferAmount;
                const balanceAfter = currentBankBalance + transferAmount;

                // Update transfer fee
                document.getElementById('transfer-fee').value = formatCurrency(fee);

                // Update balance preview
                document.getElementById('transfer-in').textContent = formatCurrency(transferAmount);
                document.getElementById('balance-after').textContent = formatCurrency(balanceAfter);

                // Show balance preview with animation
                const balancePreview = document.getElementById('balance-preview');
                balancePreview.style.display = 'block';
                setTimeout(() => {
                    balancePreview.style.opacity = '1';
                    balancePreview.style.transform = 'translateY(0)';
                }, 50);

                // Update help text with color coding
                const helpElement = document.getElementById('amount-help');
                if (fee > 0) {
                    helpElement.innerHTML =
                        `<span class="text-danger">Fee transfer: ${formatCurrency(fee)}</span> (dari jumlah asli ${formatCurrency(originalAmount)})`;
                } else if (fee < 0) {
                    helpElement.innerHTML = `<span class="text-success">Bonus: ${formatCurrency(Math.abs(fee))}</span> (lebih dari jumlah asli)`;
                } else {
                    helpElement.innerHTML = '<span class="text-success">Jumlah sama dengan pembayaran asli (tidak ada fee)</span>';
                }
            } else {
                document.getElementById('balance-preview').style.display = 'none';
            }
        }

        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        function resetForm() {
            document.getElementById('transfer-form').reset();
            document.getElementById('payment-info').style.display = 'none';
            document.getElementById('balance-preview').style.display = 'none';
            document.getElementById('amount-help').textContent = 'Bisa berbeda dari jumlah asli (dipotong fee transfer)';
        }

        // Form validation with SweetAlert
        document.getElementById('transfer-form').addEventListener('submit', function(e) {
            const paymentSelect = document.getElementById('payment_id');
            const transferAmount = parseFloat(document.getElementById('transfer_amount').value) || 0;

            if (!paymentSelect.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Pembayaran',
                    text: 'Silakan pilih pembayaran yang akan ditransfer!',
                    confirmButtonColor: '#8B5CF6'
                });
                return false;
            }

            if (transferAmount <= 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Jumlah Invalid',
                    text: 'Jumlah transfer harus lebih dari 0!',
                    confirmButtonColor: '#8B5CF6'
                });
                return false;
            }

            // Confirmation with SweetAlert
            e.preventDefault();
            const selectedOption = paymentSelect.options[paymentSelect.selectedIndex];
            const originalAmount = parseFloat(selectedOption.dataset.amount) || 0;
            const fee = originalAmount - transferAmount;

            let html = `
                <div class="text-start">
                    <p><strong>Pembayaran:</strong> ${formatCurrency(originalAmount)}</p>
                    <p><strong>Transfer:</strong> ${formatCurrency(transferAmount)}</p>
                    ${fee !== 0 ? `<p><strong>Fee:</strong> ${formatCurrency(fee)}</p>` : ''}
                    <p class="text-muted mt-3">Saldo setelah transfer: <strong>${formatCurrency(currentBankBalance + transferAmount)}</strong></p>
                </div>
            `;

            Swal.fire({
                title: 'Konfirmasi Transfer',
                html: html,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8B5CF6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Proses Transfer',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses Transfer...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    this.submit();
                }
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

        .form-control:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        .form-select:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        #payment-info,
        #balance-preview {
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .input-group-text {
            background: rgba(139, 92, 246, 0.05);
            border-color: rgba(139, 92, 246, 0.2);
        }
    </style>
@endpush
