@extends('layouts.app')
@section('title', 'Tambah Pemasukan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-credit-card-2-front me-2"></i>Tambah Pemasukan
                    </h1>
                    <p class="text-muted mb-0">Catat pemasukan dari klien</p>
                </div>
                <a href="{{ route('payments.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <form action="{{ route('payments.store') }}" method="POST" id="payment-form">
                @csrf

                <!-- Project Selection Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-folder2-open text-purple"></i>
                            </div>
                            Pilih Proyek
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="project_id" class="form-label fw-semibold">
                                    Proyek <span class="text-danger">*</span>
                                </label>
                                <select name="project_id" id="project_id" class="form-select form-select-lg @error('project_id') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Proyek</option>
                                    @if (isset($projects))
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}" data-client="{{ $project->client->name }}"
                                                data-total="{{ $project->total_value }}" data-paid="{{ $project->paid_amount }}"
                                                data-remaining="{{ $project->remaining_amount }}"
                                                {{ old('project_id', request('project')) == $project->id ? 'selected' : '' }}>
                                                {{ $project->client->name }} - {{ $project->title }}
                                                (Sisa: Rp {{ number_format($project->remaining_amount, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Project Info Dynamic -->
                            <div class="col-12" id="project-info" style="display: none;">
                                <div class="p-4 bg-purple-light rounded">
                                    <h6 class="text-purple mb-3 fw-bold">Informasi Proyek</h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <small class="text-muted fw-semibold">Klien:</small>
                                            <div class="fw-bold" id="project-client">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted fw-semibold">Nilai Total:</small>
                                            <div class="fw-bold text-success" id="project-total">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted fw-semibold">Sudah Dibayar:</small>
                                            <div class="fw-bold text-primary" id="project-paid">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted fw-semibold">Sisa Tagihan:</small>
                                            <div class="fw-bold text-warning" id="project-remaining">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-cash text-success"></i>
                            </div>
                            Detail Pembayaran
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Payment Amount -->
                            <div class="col-md-6">
                                <label for="amount" class="form-label fw-semibold">
                                    Jumlah Pembayaran <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-success bg-opacity-10 text-success fw-bold">Rp</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount"
                                        value="{{ old('amount') }}" min="0" placeholder="0" required>
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
                                <label for="payment_type" class="form-label fw-semibold">
                                    Tipe Pembayaran <span class="text-danger">*</span>
                                </label>
                                <select name="payment_type" id="payment_type"
                                    class="form-select form-select-lg @error('payment_type') is-invalid @enderror" required>
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
                                <label for="payment_date" class="form-label fw-semibold">
                                    Tanggal Pembayaran <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control form-control-lg @error('payment_date') is-invalid @enderror" id="payment_date"
                                    name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label fw-semibold">
                                    Metode Pembayaran
                                </label>
                                <select name="payment_method" id="payment_method"
                                    class="form-select form-select-lg @error('payment_method') is-invalid @enderror">
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
                                <label for="notes" class="form-label fw-semibold">Catatan</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                                    placeholder="Catatan tambahan tentang pembayaran ini (opsional)">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Simpan Pembayaran
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
            // SweetAlert messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#8B5CF6',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#8B5CF6'
                });
            @endif

            const projectSelect = document.getElementById('project_id');
            const amountInput = document.getElementById('amount');
            const projectInfo = document.getElementById('project-info');

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

                    amountInput.max = remaining;
                    document.getElementById('amount-help').textContent =
                        `Maksimal: ${formatCurrency(remaining)} (sisa tagihan)`;
                } else {
                    projectInfo.style.display = 'none';
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
                    const remaining = parseFloat(selectedOption.dataset.remaining) || 0;

                    if (amount > remaining) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Jumlah Melebihi Sisa',
                            text: `Jumlah pembayaran tidak boleh melebihi sisa tagihan (${formatCurrency(remaining)})`,
                            confirmButtonColor: '#8B5CF6'
                        });
                        return false;
                    }
                }

                if (amount <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Tidak Valid',
                        text: 'Jumlah pembayaran harus lebih dari 0',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return false;
                }

                const submitBtn = e.target.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                submitBtn.disabled = true;
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

            // Focus on project select
            projectSelect.focus();
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

        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.05) !important;
        }

        @media (max-width: 768px) {
            .card-header h5 {
                font-size: 1.1rem;
            }
        }
    </style>
@endpush
