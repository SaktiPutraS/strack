@extends('layouts.app')
@section('title', 'Tambah Pembayaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-credit-card-2-front"></i>Tambah Pembayaran
                </h1>
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('payments.store') }}" method="POST" id="payment-form">
                        @csrf

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
                                                data-remaining="{{ $project->remaining_amount }}"
                                                {{ old('project_id', request('project')) == $project->id ? 'selected' : '' }}>
                                                {{ $project->title }} - {{ $project->client->name }}
                                                (Sisa: Rp {{ number_format($project->remaining_amount, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Project Info (Dynamic) -->
                            <div class="col-12" id="project-info" style="display: none;">
                                <div class="p-3 bg-lilac-soft rounded">
                                    <h6 class="text-lilac mb-3">Informasi Proyek</h6>
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <small class="text-muted">Klien:</small>
                                            <div class="fw-bold" id="project-client">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Nilai Total:</small>
                                            <div class="fw-bold text-success" id="project-total">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Sudah Dibayar:</small>
                                            <div class="fw-bold text-primary" id="project-paid">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Sisa Tagihan:</small>
                                            <div class="fw-bold text-warning" id="project-remaining">-</div>
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
                                        value="{{ old('amount') }}" min="1" step="1000" placeholder="0" required>
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
                                    <option value="DP" {{ old('payment_type') == 'DP' ? 'selected' : '' }}>DP (Down Payment)</option>
                                    <option value="INSTALLMENT" {{ old('payment_type') == 'INSTALLMENT' ? 'selected' : '' }}>Cicilan</option>
                                    <option value="FULL" {{ old('payment_type') == 'FULL' ? 'selected' : '' }}>Pembayaran Penuh</option>
                                    <option value="FINAL" {{ old('payment_type') == 'FINAL' ? 'selected' : '' }}>Pembayaran Terakhir</option>
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
                                    name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
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
                                    <option value="Transfer Bank" {{ old('payment_method') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank
                                    </option>
                                    <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash/Tunai</option>
                                    <option value="E-Wallet" {{ old('payment_method') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                                    <option value="Kartu Kredit" {{ old('payment_method') == 'Kartu Kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                                    <option value="Kartu Debit" {{ old('payment_method') == 'Kartu Debit' ? 'selected' : '' }}>Kartu Debit</option>
                                    <option value="Cek" {{ old('payment_method') == 'Cek' ? 'selected' : '' }}>Cek</option>
                                    <option value="Lainnya" {{ old('payment_method') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                                    placeholder="Catatan tambahan tentang pembayaran ini (opsional)">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Savings Preview -->
                        <div class="mt-4 p-3 bg-light rounded" id="savings-preview" style="display: none;">
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-piggy-bank text-success me-2"></i>
                                Preview Tabungan 10%
                            </h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Jumlah yang akan ditabung:</span>
                                <strong class="text-success" id="savings-amount">Rp 0</strong>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                10% dari pembayaran akan otomatis masuk ke tabungan
                            </small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Simpan Pembayaran
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
        document.addEventListener('DOMContentLoaded', function() {
            const projectSelect = document.getElementById('project_id');
            const amountInput = document.getElementById('amount');
            const projectInfo = document.getElementById('project-info');
            const savingsPreview = document.getElementById('savings-preview');

            function updateProjectInfo() {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];

                if (selectedOption.value) {
                    const client = selectedOption.dataset.client;
                    const total = parseFloat(selectedOption.dataset.total) || 0;
                    const paid = parseFloat(selectedOption.dataset.paid) || 0;
                    const remaining = parseFloat(selectedOption.dataset.remaining) || 0;

                    document.getElementById('project-client').textContent = client;
                    document.getElementById('project-total').textContent = formatCurrency(total);
                    document.getElementById('project-paid').textContent = formatCurrency(paid);
                    document.getElementById('project-remaining').textContent = formatCurrency(remaining);

                    projectInfo.style.display = 'block';

                    // Update amount input max value
                    amountInput.max = remaining;

                    // Update help text
                    document.getElementById('amount-help').textContent =
                        `Maksimal: ${formatCurrency(remaining)} (sisa tagihan)`;
                } else {
                    projectInfo.style.display = 'none';
                    amountInput.max = '';
                    document.getElementById('amount-help').textContent = 'Masukkan jumlah pembayaran';
                }

                updateSavingsPreview();
            }

            function updateSavingsPreview() {
                const amount = parseFloat(amountInput.value) || 0;
                const savings = amount * 0.1;

                if (amount > 0) {
                    document.getElementById('savings-amount').textContent = formatCurrency(savings);
                    savingsPreview.style.display = 'block';
                } else {
                    savingsPreview.style.display = 'none';
                }
            }

            function formatCurrency(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            function resetForm() {
                document.getElementById('payment-form').reset();
                projectInfo.style.display = 'none';
                savingsPreview.style.display = 'none';
            }

            // Event listeners
            projectSelect.addEventListener('change', updateProjectInfo);
            amountInput.addEventListener('input', updateSavingsPreview);

            // Form validation
            document.getElementById('payment-form').addEventListener('submit', function(e) {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                const amount = parseFloat(amountInput.value) || 0;

                if (selectedOption.value) {
                    const remaining = parseFloat(selectedOption.dataset.remaining) || 0;

                    if (amount > remaining) {
                        e.preventDefault();
                        alert(`Jumlah pembayaran tidak boleh melebihi sisa tagihan (${formatCurrency(remaining)})`);
                        return false;
                    }
                }

                if (amount <= 0) {
                    e.preventDefault();
                    alert('Jumlah pembayaran harus lebih dari 0');
                    return false;
                }
            });

            // Auto-suggest payment type based on amount
            amountInput.addEventListener('blur', function() {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                const paymentTypeSelect = document.getElementById('payment_type');

                if (selectedOption.value && !paymentTypeSelect.value) {
                    const amount = parseFloat(this.value) || 0;
                    const remaining = parseFloat(selectedOption.dataset.remaining) || 0;
                    const total = parseFloat(selectedOption.dataset.total) || 0;
                    const paid = parseFloat(selectedOption.dataset.paid) || 0;

                    // Auto suggest payment type
                    if (paid === 0 && amount < total * 0.8) {
                        paymentTypeSelect.value = 'DP';
                    } else if (amount === remaining) {
                        paymentTypeSelect.value = 'FINAL';
                    } else if (amount === total) {
                        paymentTypeSelect.value = 'FULL';
                    } else {
                        paymentTypeSelect.value = 'INSTALLMENT';
                    }
                }
            });

            // Initial setup if project is pre-selected
            if (projectSelect.value) {
                updateProjectInfo();
            }

            // Quick amount buttons
            function addQuickAmountButtons() {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                if (selectedOption.value) {
                    const remaining = parseFloat(selectedOption.dataset.remaining) || 0;
                    const total = parseFloat(selectedOption.dataset.total) || 0;

                    const quickAmounts = [{
                            label: '25%',
                            value: Math.round(total * 0.25)
                        },
                        {
                            label: '50%',
                            value: Math.round(total * 0.50)
                        },
                        {
                            label: 'Sisa',
                            value: remaining
                        }
                    ];

                    // Remove existing buttons
                    const existingButtons = document.querySelector('#quick-amounts');
                    if (existingButtons) {
                        existingButtons.remove();
                    }

                    // Add new buttons
                    const buttonsDiv = document.createElement('div');
                    buttonsDiv.id = 'quick-amounts';
                    buttonsDiv.className = 'mt-2';

                    quickAmounts.forEach(qa => {
                        if (qa.value > 0 && qa.value <= remaining) {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'btn btn-sm btn-outline-secondary me-2';
                            btn.textContent = qa.label;
                            btn.onclick = () => {
                                amountInput.value = qa.value;
                                updateSavingsPreview();
                            };
                            buttonsDiv.appendChild(btn);
                        }
                    });

                    if (buttonsDiv.children.length > 0) {
                        amountInput.parentNode.appendChild(buttonsDiv);
                    }
                }
            }

            projectSelect.addEventListener('change', addQuickAmountButtons);
        });
    </script>
@endpush
