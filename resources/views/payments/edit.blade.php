@extends('layouts.app')
@section('title', 'Edit Pembayaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-pencil-square"></i>Edit Pembayaran
                </h1>
                <div class="btn-group">
                    <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Lihat
                    </a>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('payments.update', $payment) }}" method="POST" id="payment-form">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Project Selection -->
                            <div class="col-12">
                                <label for="project_id" class="form-label">
                                    <i class="bi bi-folder2-open text-lilac me-2"></i>
                                    Proyek <span class="text-danger">*</span>
                                </label>
                                <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                    <option value="">Pilih Proyek</option>
                                    @if (isset($projects))
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}" data-client="{{ $project->client->name }}"
                                                data-total="{{ $project->total_value }}" data-paid="{{ $project->paid_amount }}"
                                                data-remaining="{{ $project->remaining_amount }}" data-current-payment="{{ $payment->amount }}"
                                                {{ old('project_id', $payment->project_id) == $project->id ? 'selected' : '' }}>
                                                {{ $project->title }} - {{ $project->client->name }}
                                                (Sisa: Rp {{ number_format($project->remaining_amount + $payment->amount, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Project Info (Dynamic) -->
                            <div class="col-12" id="project-info">
                                <div class="p-3 bg-lilac-soft rounded">
                                    <h6 class="text-lilac mb-3">Informasi Proyek</h6>
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <small class="text-muted">Klien:</small>
                                            <div class="fw-bold" id="project-client">{{ $payment->project->client->name }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Nilai Total:</small>
                                            <div class="fw-bold text-success" id="project-total">{{ $payment->project->formatted_total_value }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Sudah Dibayar:</small>
                                            <div class="fw-bold text-primary" id="project-paid">Rp
                                                {{ number_format($payment->project->paid_amount - $payment->amount, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Sisa Tagihan:</small>
                                            <div class="fw-bold text-warning" id="project-remaining">Rp
                                                {{ number_format($payment->project->remaining_amount + $payment->amount, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Amount -->
                            <div class="col-md-6">
                                <label for="amount" class="form-label">
                                    <i class="bi bi-cash text-lilac me-2"></i>
                                    Jumlah Pembayaran <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount"
                                        value="{{ old('amount', $payment->amount) }}" min="0" step="1000" placeholder="0" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="amount-help">Masukkan jumlah pembayaran</span>
                                </div>
                            </div>

                            <!-- Payment Type -->
                            <div class="col-md-6">
                                <label for="payment_type" class="form-label">
                                    <i class="bi bi-tag text-lilac me-2"></i>
                                    Tipe Pembayaran <span class="text-danger">*</span>
                                </label>
                                <select name="payment_type" id="payment_type" class="form-select @error('payment_type') is-invalid @enderror" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="DP" {{ old('payment_type', $payment->payment_type) == 'DP' ? 'selected' : '' }}>DP (Down Payment)
                                    </option>
                                    <option value="INSTALLMENT" {{ old('payment_type', $payment->payment_type) == 'INSTALLMENT' ? 'selected' : '' }}>
                                        Cicilan</option>
                                    <option value="FULL" {{ old('payment_type', $payment->payment_type) == 'FULL' ? 'selected' : '' }}>Pembayaran Penuh
                                    </option>
                                    <option value="FINAL" {{ old('payment_type', $payment->payment_type) == 'FINAL' ? 'selected' : '' }}>Pembayaran
                                        Terakhir</option>
                                </select>
                                @error('payment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Date -->
                            <div class="col-md-6">
                                <label for="payment_date" class="form-label">
                                    <i class="bi bi-calendar3 text-lilac me-2"></i>
                                    Tanggal Pembayaran <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date"
                                    name="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}"
                                    max="{{ date('Y-m-d') }}" required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">
                                    <i class="bi bi-credit-card text-lilac me-2"></i>
                                    Metode Pembayaran
                                </label>
                                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                                    <option value="">Pilih Metode</option>
                                    <option value="Transfer Bank"
                                        {{ old('payment_method', $payment->payment_method) == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="Cash" {{ old('payment_method', $payment->payment_method) == 'Cash' ? 'selected' : '' }}>Cash/Tunai
                                    </option>
                                    <option value="E-Wallet" {{ old('payment_method', $payment->payment_method) == 'E-Wallet' ? 'selected' : '' }}>
                                        E-Wallet</option>
                                    <option value="Kartu Kredit"
                                        {{ old('payment_method', $payment->payment_method) == 'Kartu Kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                                    <option value="Kartu Debit"
                                        {{ old('payment_method', $payment->payment_method) == 'Kartu Debit' ? 'selected' : '' }}>Kartu Debit</option>
                                    <option value="Cek" {{ old('payment_method', $payment->payment_method) == 'Cek' ? 'selected' : '' }}>Cek</option>
                                    <option value="Lainnya" {{ old('payment_method', $payment->payment_method) == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                    </option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label">
                                    <i class="bi bi-journal-text text-lilac me-2"></i>
                                    Catatan
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                    placeholder="Catatan tambahan tentang pembayaran ini (opsional)">{{ old('notes', $payment->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Payment Info -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-info-circle text-info me-2"></i>
                                Informasi Pembayaran Saat Ini
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Tanggal Awal:</small>
                                    <div class="fw-bold">{{ $payment->payment_date->format('d M Y') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Jumlah Awal:</small>
                                    <div class="fw-bold text-primary">{{ $payment->formatted_amount }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Tipe Awal:</small>
                                    <div class="fw-bold">{{ $payment->payment_type }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <div>
                                <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline-info me-2">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash me-2"></i>Hapus
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('payments.index') }}" class="btn btn-secondary me-2">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Pembayaran
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form (Hidden) -->
                    <form id="delete-form" action="{{ route('payments.destroy', $payment) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const projectSelect = document.getElementById('project_id');
            const amountInput = document.getElementById('amount');
            const currentPaymentAmount = {{ $payment->amount }};

            function updateProjectInfo() {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];

                if (selectedOption.value) {
                    const client = selectedOption.dataset.client;
                    const total = parseFloat(selectedOption.dataset.total) || 0;
                    const paid = parseFloat(selectedOption.dataset.paid) || 0;
                    const remaining = parseFloat(selectedOption.dataset.remaining) || 0;
                    const currentPayment = parseFloat(selectedOption.dataset.currentPayment) || 0;

                    // Calculate actual remaining (excluding current payment)
                    const actualPaid = paid - currentPayment;
                    const actualRemaining = remaining + currentPayment;

                    document.getElementById('project-client').textContent = client;
                    document.getElementById('project-total').textContent = formatCurrency(total);
                    document.getElementById('project-paid').textContent = formatCurrency(actualPaid);
                    document.getElementById('project-remaining').textContent = formatCurrency(actualRemaining);

                    // Update amount input max value
                    amountInput.max = actualRemaining;

                    // Update help text
                    document.getElementById('amount-help').textContent =
                        `Maksimal: ${formatCurrency(actualRemaining)} (sisa tagihan + pembayaran saat ini)`;
                } else {
                    amountInput.max = '';
                    document.getElementById('amount-help').textContent = 'Masukkan jumlah pembayaran';
                }
            }

            function formatCurrency(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            // Event listeners
            projectSelect.addEventListener('change', updateProjectInfo);

            // Form validation
            document.getElementById('payment-form').addEventListener('submit', function(e) {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                const amount = parseFloat(amountInput.value) || 0;

                if (selectedOption.value) {
                    const total = parseFloat(selectedOption.dataset.total) || 0;
                    const paid = parseFloat(selectedOption.dataset.paid) || 0;
                    const currentPayment = parseFloat(selectedOption.dataset.currentPayment) || 0;

                    // Calculate max allowed amount (total - other payments)
                    const maxAmount = total - (paid - currentPayment);

                    if (amount > maxAmount) {
                        e.preventDefault();
                        alert(`Jumlah pembayaran tidak boleh melebihi ${formatCurrency(maxAmount)}`);
                        return false;
                    }
                }

                if (amount <= 0) {
                    e.preventDefault();
                    alert('Jumlah pembayaran harus lebih dari 0');
                    return false;
                }
            });

            // Initial setup
            updateProjectInfo();
        });

        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus pembayaran ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
@endpush
