@extends('layouts.app')
@section('title', 'Tambah Pengeluaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Pengeluaran
                    </h1>
                    <p class="text-muted mb-0">Catat pengeluaran dari Bank atau Cash</p>
                </div>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-primary">
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
                            <h6 class="mb-0 text-white-50">Saldo Cash</h6>
                            <h3 class="mb-0 fw-bold text-white" id="cashBalance">Rp {{ number_format($currentCashBalance, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <form action="{{ route('expenses.store') }}" method="POST" id="expense-form">
                @csrf

                <!-- Basic Information Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-info-circle text-purple"></i>
                            </div>
                            Informasi Pengeluaran
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Expense Date -->
                            <div class="col-md-6">
                                <label for="expense_date" class="form-label fw-semibold">
                                    Tanggal Pengeluaran <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control form-control-lg @error('expense_date') is-invalid @enderror" id="expense_date"
                                    name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Source -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Sumber Dana <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex gap-3 mt-2">
                                    @foreach (\App\Models\Expense::SOURCES as $key => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="source" id="source_{{ $key }}"
                                                value="{{ $key }}" {{ old('source', 'BANK') == $key ? 'checked' : '' }} required>
                                            <label class="form-check-label fw-semibold" for="source_{{ $key }}">
                                                <i class="bi bi-{{ $key == 'BANK' ? 'bank' : 'cash-coin' }} me-1"></i>
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('source')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted mt-1 d-block" id="balanceInfo">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <span id="selectedBalanceText">Saldo Bank: Rp {{ number_format($currentBankBalance, 0, ',', '.') }}</span>
                                </small>
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <label for="amount" class="form-label fw-semibold">
                                    Jumlah Pengeluaran <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-danger bg-opacity-10 text-danger fw-bold">Rp</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount"
                                        value="{{ old('amount') }}" min="1" placeholder="0" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="maxAmountInfo">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <span id="maxAmountText">Maksimal: Rp {{ number_format($currentBankBalance, 0, ',', '.') }}</span>
                                </small>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-semibold">
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select name="category" id="category" class="form-select form-select-lg @error('category') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach (\App\Models\Expense::CATEGORIES as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">
                                    Deskripsi <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4"
                                    placeholder="Contoh: Hosting bulanan untuk website client, Kopi meeting dengan klien, dll" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-2 text-dark">
                            <i class="bi bi-eye me-2 text-purple"></i>Preview Pengeluaran
                        </h6>
                        <div class="row text-sm">
                            <div class="col-6">
                                <strong>Sumber:</strong><br>
                                <span id="previewSource">Bank Octo</span>
                            </div>
                            <div class="col-6">
                                <strong>Saldo Sebelum:</strong><br>
                                <span id="previewBalanceBefore">Rp {{ number_format($currentBankBalance, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row text-sm" id="previewAfter" style="display: none;">
                            <div class="col-6">
                                <strong>Jumlah Pengeluaran:</strong><br>
                                <span class="text-danger" id="expenseAmount">-</span>
                            </div>
                            <div class="col-6">
                                <strong>Saldo Sesudah:</strong><br>
                                <span id="previewBalanceAfter">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="bi bi-check-circle me-2"></i>Simpan Pengeluaran
                                </button>
                            </div>
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
            const sourceRadios = document.querySelectorAll('input[name="source"]');
            const amountInput = document.getElementById('amount');
            const selectedBalanceText = document.getElementById('selectedBalanceText');
            const maxAmountText = document.getElementById('maxAmountText');
            const previewSource = document.getElementById('previewSource');
            const previewBalanceBefore = document.getElementById('previewBalanceBefore');
            const previewAfter = document.getElementById('previewAfter');
            const expenseAmount = document.getElementById('expenseAmount');
            const previewBalanceAfter = document.getElementById('previewBalanceAfter');
            const submitBtn = document.getElementById('submitBtn');

            const bankBalance = {{ $currentBankBalance }};
            const cashBalance = {{ $currentCashBalance }};

            function formatCurrency(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            function updateBalanceInfo() {
                const selectedSource = document.querySelector('input[name="source"]:checked').value;
                const isBank = selectedSource === 'BANK';
                const currentBalance = isBank ? bankBalance : cashBalance;
                const sourceName = isBank ? 'Bank Octo' : 'Cash';

                selectedBalanceText.textContent = `Saldo ${sourceName}: ${formatCurrency(currentBalance)}`;
                maxAmountText.textContent = `Maksimal: ${formatCurrency(currentBalance)}`;
                amountInput.max = currentBalance;

                previewSource.textContent = sourceName;
                previewBalanceBefore.textContent = formatCurrency(currentBalance);
                previewBalanceBefore.className = isBank ? 'text-primary' : 'text-success';

                validateAmount();
            }

            function validateAmount() {
                const selectedSource = document.querySelector('input[name="source"]:checked').value;
                const currentBalance = selectedSource === 'BANK' ? bankBalance : cashBalance;
                const amount = parseFloat(amountInput.value) || 0;

                if (amount > 0) {
                    const newBalance = currentBalance - amount;

                    expenseAmount.textContent = `- ${formatCurrency(amount)}`;
                    previewBalanceAfter.textContent = formatCurrency(newBalance);
                    previewAfter.style.display = 'block';

                    if (amount > currentBalance) {
                        amountInput.classList.add('is-invalid');
                        previewBalanceAfter.style.color = '#EF4444';
                        submitBtn.disabled = true;
                    } else {
                        amountInput.classList.remove('is-invalid');
                        previewBalanceAfter.style.color = selectedSource === 'BANK' ? '#3B82F6' : '#10B981';
                        submitBtn.disabled = false;
                    }
                } else {
                    previewAfter.style.display = 'none';
                    amountInput.classList.remove('is-invalid');
                    submitBtn.disabled = false;
                }
            }

            sourceRadios.forEach(radio => {
                radio.addEventListener('change', updateBalanceInfo);
            });

            amountInput.addEventListener('input', validateAmount);
            updateBalanceInfo();

            // Form validation
            document.getElementById('expense-form').addEventListener('submit', function(e) {
                const selectedSource = document.querySelector('input[name="source"]:checked').value;
                const currentBalance = selectedSource === 'BANK' ? bankBalance : cashBalance;
                const amount = parseFloat(amountInput.value) || 0;
                const sourceName = selectedSource === 'BANK' ? 'Bank Octo' : 'Cash';

                if (amount <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Tidak Valid',
                        text: 'Masukkan jumlah pengeluaran yang valid!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return;
                }

                if (amount > currentBalance) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Saldo Tidak Cukup',
                        text: `Saldo ${sourceName} tidak mencukupi untuk pengeluaran ini!`,
                        confirmButtonColor: '#8B5CF6'
                    });
                    return;
                }

                const submitBtn = e.target.querySelector('button[type="submit"]');
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

        .form-check-input:checked {
            background-color: #8B5CF6;
            border-color: #8B5CF6;
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

        .bg-gradient-primary {
            background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10B981, #059669) !important;
        }
    </style>
@endpush
