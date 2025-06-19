@extends('layouts.app')
@section('title', 'Tambah Proyek Baru')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-plus-circle"></i>Tambah Proyek Baru
                </h1>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <form action="{{ route('projects.store') }}" method="POST" id="project-form">
                @csrf

                <!-- Main Project Information -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="section-title">
                            <i class="bi bi-info-circle"></i>Informasi Proyek
                        </h5>

                        <div class="row g-3">
                            <!-- Client Selection -->
                            <div class="col-md-6">
                                <label for="client_id" class="form-label">
                                    <i class="bi bi-person text-lilac me-2"></i>
                                    Klien <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex gap-2">
                                    <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                                        <option value="">Pilih Klien</option>
                                        {{-- Data klien akan diambil dari controller --}}
                                        @if (isset($clients))
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->name }} - {{ $client->phone }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="openNewClientModal()">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Project Type -->
                            <div class="col-md-6">
                                <label for="type" class="form-label">
                                    <i class="bi bi-tag text-lilac me-2"></i>
                                    Tipe Proyek <span class="text-danger">*</span>
                                </label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="HTML/PHP" {{ old('type') == 'HTML/PHP' ? 'selected' : '' }}>HTML/PHP</option>
                                    <option value="LARAVEL" {{ old('type') == 'LARAVEL' ? 'selected' : '' }}>LARAVEL</option>
                                    <option value="WORDPRESS" {{ old('type') == 'WORDPRESS' ? 'selected' : '' }}>WORDPRESS</option>
                                    <option value="REACT" {{ old('type') == 'REACT' ? 'selected' : '' }}>REACT</option>
                                    <option value="VUE" {{ old('type') == 'VUE' ? 'selected' : '' }}>VUE</option>
                                    <option value="FLUTTER" {{ old('type') == 'FLUTTER' ? 'selected' : '' }}>FLUTTER</option>
                                    <option value="MOBILE" {{ old('type') == 'MOBILE' ? 'selected' : '' }}>MOBILE</option>
                                    <option value="OTHER" {{ old('type') == 'OTHER' ? 'selected' : '' }}>OTHER</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Project Title -->
                            <div class="col-12">
                                <label for="title" class="form-label">
                                    <i class="bi bi-card-text text-lilac me-2"></i>
                                    Judul Proyek <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                                    value="{{ old('title') }}" placeholder="e.g. Website E-Commerce Toko Online" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deadline -->
                            <div class="col-md-6">
                                <label for="deadline" class="form-label">
                                    <i class="bi bi-calendar3 text-lilac me-2"></i>
                                    Deadline <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline"
                                    value="{{ old('deadline') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="bi bi-journal-text text-lilac me-2"></i>
                                    Deskripsi Proyek <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4"
                                    placeholder="Deskripsikan detail proyek, fitur yang akan dibuat, teknologi yang digunakan, dll." required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="section-title">
                            <i class="bi bi-currency-dollar"></i>Informasi Keuangan
                        </h5>

                        <div class="row g-3">
                            <!-- Total Value -->
                            <div class="col-md-6">
                                <label for="total_value" class="form-label">
                                    <i class="bi bi-cash text-lilac me-2"></i>
                                    Nilai Total Proyek <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('total_value') is-invalid @enderror" id="total_value"
                                        name="total_value" value="{{ old('total_value') }}" min="0" step="1000" placeholder="0" required>
                                </div>
                                @error('total_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Masukkan nilai tanpa titik atau koma</div>
                            </div>

                            <!-- DP Amount -->
                            <div class="col-md-6">
                                <label for="dp_amount" class="form-label">
                                    <i class="bi bi-wallet text-lilac me-2"></i>
                                    Down Payment (DP)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('dp_amount') is-invalid @enderror" id="dp_amount"
                                        name="dp_amount" value="{{ old('dp_amount', 0) }}" min="0" step="1000" placeholder="0">
                                </div>
                                @error('dp_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Financial Summary -->
                            <div class="col-12">
                                <div class="p-3 bg-lilac-soft rounded">
                                    <h6 class="text-lilac mb-3">Ringkasan Keuangan</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <small class="text-muted">Nilai Total:</small>
                                            <div class="fw-bold text-primary" id="summary-total">Rp 0</div>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Down Payment:</small>
                                            <div class="fw-bold text-success" id="summary-dp">Rp 0</div>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Sisa Pembayaran:</small>
                                            <div class="fw-bold text-warning" id="summary-remaining">Rp 0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="section-title">
                            <i class="bi bi-sticky-note"></i>Catatan Tambahan
                        </h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="notes" class="form-label">
                                    <i class="bi bi-journal-text text-lilac me-2"></i>
                                    Catatan
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                    placeholder="Catatan khusus tentang proyek ini (opsional)">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-warning" onclick="resetForm()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Simpan Proyek
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- New Client Modal -->
    <div class="modal fade" id="newClientModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i>Tambah Klien Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="newClientForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Klien *</label>
                            <input type="text" id="newClientName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon *</label>
                            <input type="tel" id="newClientPhone" class="form-control" placeholder="08123456789" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" id="newClientEmail" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Klien</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Financial calculations
            function updateFinancialSummary() {
                const totalValue = parseFloat(document.getElementById('total_value').value) || 0;
                const dpAmount = parseFloat(document.getElementById('dp_amount').value) || 0;
                const remaining = totalValue - dpAmount;

                document.getElementById('summary-total').textContent = formatCurrency(totalValue);
                document.getElementById('summary-dp').textContent = formatCurrency(dpAmount);
                document.getElementById('summary-remaining').textContent = formatCurrency(remaining);

                // Update DP percentage
                const dpPercentage = totalValue > 0 ? ((dpAmount / totalValue) * 100).toFixed(1) : 0;
                document.getElementById('dp-percentage').textContent = dpPercentage + '%';
            }

            // Reset form
            function resetForm() {
                if (confirm('Apakah Anda yakin ingin mereset form?')) {
                    document.getElementById('project-form').reset();
                    updateFinancialSummary();
                }
            }
            window.resetForm = resetForm;

            // New client modal functions
            function openNewClientModal() {
                const modal = new bootstrap.Modal(document.getElementById('newClientModal'));
                modal.show();
            }
            window.openNewClientModal = openNewClientModal;

            function closeNewClientModal() {
                const modal = bootstrap.Modal.getInstance(document.getElementById('newClientModal'));
                if (modal) {
                    modal.hide();
                }
                document.getElementById('newClientForm').reset();
            }

            // Handle new client form submission
            document.getElementById('newClientForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = {
                    name: document.getElementById('newClientName').value,
                    phone: document.getElementById('newClientPhone').value,
                    email: document.getElementById('newClientEmail').value,
                };

                // Simulasi penambahan klien baru
                // Dalam implementasi nyata, ini akan melakukan AJAX call ke server
                const clientSelect = document.getElementById('client_id');
                const newOption = new Option(
                    `${formData.name} - ${formData.phone}`,
                    'new_' + Date.now(), // ID sementara
                    true,
                    true
                );
                clientSelect.add(newOption);

                closeNewClientModal();
                showSuccess('Klien baru berhasil ditambahkan!');
            });

            // Event listeners
            document.getElementById('total_value').addEventListener('input', updateFinancialSummary);
            document.getElementById('dp_amount').addEventListener('input', updateFinancialSummary);

            // Initial calculation
            updateFinancialSummary();

            // Set minimum deadline to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('deadline').min = tomorrow.toISOString().split('T')[0];

            // Form validation
            document.getElementById('project-form').addEventListener('submit', function(e) {
                const totalValue = parseFloat(document.getElementById('total_value').value) || 0;
                const dpAmount = parseFloat(document.getElementById('dp_amount').value) || 0;

                if (dpAmount > totalValue) {
                    e.preventDefault();
                    showError('Jumlah DP tidak boleh melebihi nilai total proyek!');
                    return false;
                }

                if (totalValue < 100000) {
                    e.preventDefault();
                    showError('Nilai proyek minimal Rp 100.000');
                    return false;
                }

                // Show loading while submitting
                showLoading();
            });

            // Phone number formatting
            const phoneInput = document.getElementById('newClientPhone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.startsWith('0')) {
                        // Keep Indonesian format
                    } else if (value.startsWith('62')) {
                        // Convert to Indonesian format
                        value = '0' + value.substring(2);
                    }
                    e.target.value = value;
                });
            }

            // Auto-suggest common project values based on type
            const commonProjectValues = {
                'HTML/PHP': [500000, 1000000, 1500000, 2000000],
                'LARAVEL': [1500000, 2500000, 3500000, 5000000],
                'WORDPRESS': [800000, 1200000, 2000000, 3000000],
                'REACT': [2000000, 3000000, 4500000, 6000000],
                'VUE': [1800000, 2800000, 4000000, 5500000],
                'FLUTTER': [3000000, 5000000, 7500000, 10000000],
                'MOBILE': [2500000, 4000000, 6000000, 8000000],
                'OTHER': [500000, 1000000, 2000000, 3000000]
            };

            // Show suggested prices based on project type
            document.getElementById('type').addEventListener('change', function() {
                const selectedType = this.value;
                if (selectedType && commonProjectValues[selectedType]) {
                    const suggestions = commonProjectValues[selectedType];

                    // Remove existing suggestion buttons
                    const existingSuggestions = document.querySelector('#price-suggestions');
                    if (existingSuggestions) {
                        existingSuggestions.remove();
                    }

                    // Add price suggestion buttons
                    const suggestionsDiv = document.createElement('div');
                    suggestionsDiv.id = 'price-suggestions';
                    suggestionsDiv.className = 'mt-2';

                    const label = document.createElement('small');
                    label.className = 'text-muted d-block mb-2';
                    label.textContent = 'Saran harga untuk ' + selectedType + ':';
                    suggestionsDiv.appendChild(label);

                    const buttonGroup = document.createElement('div');
                    buttonGroup.className = 'd-flex gap-2 flex-wrap';

                    suggestions.forEach(price => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-sm btn-outline-primary';
                        btn.textContent = formatCurrency(price);
                        btn.onclick = () => {
                            document.getElementById('total_value').value = price;
                            updateFinancialSummary();
                        };
                        buttonGroup.appendChild(btn);
                    });

                    suggestionsDiv.appendChild(buttonGroup);
                    document.getElementById('total_value').parentNode.appendChild(suggestionsDiv);
                }
            });
        });
    </script>
@endpush
