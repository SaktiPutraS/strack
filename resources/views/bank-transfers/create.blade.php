{{-- resources/views/bank-transfers/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Transfer ke Bank Octo')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-bank"></i>Transfer ke Bank Octo
                </h1>
                <a href="{{ route('bank-transfers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bank-transfers.store') }}" method="POST" id="transfer-form">
                        @csrf

                        <div class="row g-3">
                            <!-- Payment Selection -->
                            <div class="col-12">
                                <label for="payment_id" class="form-label">
                                    <i class="bi bi-credit-card text-lilac me-2"></i>
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
                                                {{ $payment->formatted_amount }} - {{ $payment->project->title }} ({{ $payment->project->client->name }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('payment_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Info (Dynamic) -->
                            <div class="col-12" id="payment-info" style="display: none;">
                                <div class="p-3 bg-lilac-soft rounded">
                                    <h6 class="text-lilac mb-3">Informasi Pembayaran</h6>
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <small class="text-muted">Proyek:</small>
                                            <div class="fw-bold" id="payment-project">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Klien:</small>
                                            <div class="fw-bold" id="payment-client">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Tanggal Bayar:</small>
                                            <div class="fw-bold" id="payment-date">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Jumlah Asli:</small>
                                            <div class="fw-bold text-success" id="payment-amount">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer Date -->
                            <div class="col-md-6">
                                <label for="transfer_date" class="form-label">
                                    <i class="bi bi-calendar3 text-lilac me-2"></i>
                                    Tanggal Transfer <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('transfer_date') is-invalid @enderror" id="transfer_date"
                                    name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                @error('transfer_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Transfer Amount - FIXED: Removed step attribute -->
                            <div class="col-md-6">
                                <label for="transfer_amount" class="form-label">
                                    <i class="bi bi-cash text-lilac me-2"></i>
                                    Jumlah Transfer <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('transfer_amount') is-invalid @enderror" id="transfer_amount"
                                        name="transfer_amount" value="{{ old('transfer_amount') }}" min="1" placeholder="0" required>
                                </div>
                                @error('transfer_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="amount-help">Bisa berbeda dari jumlah asli (dipotong fee transfer)</span>
                                </div>
                            </div>

                            <!-- Reference Number -->
                            <div class="col-md-6">
                                <label for="reference_number" class="form-label">
                                    <i class="bi bi-hash text-lilac me-2"></i>
                                    Nomor Referensi
                                </label>
                                <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number"
                                    name="reference_number" value="{{ old('reference_number') }}" placeholder="Nomor transaksi bank">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opsional - nomor transaksi dari bank</div>
                            </div>

                            <!-- Transfer Fee Info -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-info-circle text-lilac me-2"></i>
                                    Fee Transfer
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="transfer-fee" readonly>
                                </div>
                                <div class="form-text">Otomatis terhitung dari selisih</div>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label">
                                    <i class="bi bi-journal-text text-lilac me-2"></i>
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
                        <div class="mt-4 p-3 bg-light rounded" id="balance-preview" style="display: none;">
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-bank text-primary me-2"></i>
                                Preview Saldo Bank Octo
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Saldo Saat Ini:</small>
                                    <div class="fw-bold text-primary" id="current-balance">Loading...</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Transfer Masuk:</small>
                                    <div class="fw-bold text-success" id="transfer-in">Rp 0</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Saldo Setelah Transfer:</small>
                                    <div class="fw-bold text-warning" id="balance-after">Rp 0</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('bank-transfers.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2" onclick="resetForm()">
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
        let currentBankBalance = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const paymentSelect = document.getElementById('payment_id');
            const transferAmountInput = document.getElementById('transfer_amount');

            // Load current bank balance (fallback if API not available)
            currentBankBalance = 5000000; // Default fallback
            document.getElementById('current-balance').textContent = formatCurrency(currentBankBalance);

            // Event listeners
            paymentSelect.addEventListener('change', updatePaymentInfo);
            transferAmountInput.addEventListener('input', updateBalancePreview);

            // Auto-select if payment_id in URL
            if (paymentSelect.value) {
                updatePaymentInfo();
            }
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

                // Show payment info
                paymentInfo.style.display = 'block';

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

                // Show balance preview
                document.getElementById('balance-preview').style.display = 'block';

                // Update help text
                if (fee > 0) {
                    document.getElementById('amount-help').textContent =
                        `Fee transfer: ${formatCurrency(fee)} (dari jumlah asli ${formatCurrency(originalAmount)})`;
                } else if (fee < 0) {
                    document.getElementById('amount-help').textContent =
                        `Bonus: ${formatCurrency(Math.abs(fee))} (lebih dari jumlah asli)`;
                } else {
                    document.getElementById('amount-help').textContent =
                        'Jumlah sama dengan pembayaran asli (tidak ada fee)';
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
        }

        // Form validation
        document.getElementById('transfer-form').addEventListener('submit', function(e) {
            const paymentSelect = document.getElementById('payment_id');
            const transferAmount = parseFloat(document.getElementById('transfer_amount').value) || 0;

            if (!paymentSelect.value) {
                e.preventDefault();
                alert('Silakan pilih pembayaran yang akan ditransfer!');
                return false;
            }

            if (transferAmount <= 0) {
                e.preventDefault();
                alert('Jumlah transfer harus lebih dari 0!');
                return false;
            }

            // Optional: Ask for confirmation
            const selectedOption = paymentSelect.options[paymentSelect.selectedIndex];
            const originalAmount = parseFloat(selectedOption.dataset.amount) || 0;
            const fee = originalAmount - transferAmount;

            let confirmMessage = `Konfirmasi Transfer:\n\n`;
            confirmMessage += `Pembayaran: ${formatCurrency(originalAmount)}\n`;
            confirmMessage += `Transfer: ${formatCurrency(transferAmount)}\n`;
            if (fee !== 0) {
                confirmMessage += `Fee: ${formatCurrency(fee)}\n`;
            }
            confirmMessage += `\nProses transfer?`;

            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endpush
