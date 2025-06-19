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
                                <div class="d-flex gap-2">
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">Pilih Tipe</option>
                                        @if (isset($projectTypes))
                                            @foreach ($projectTypes as $projectType)
                                                <option value="{{ $projectType->name }}" {{ old('type') == $projectType->name ? 'selected' : '' }}>
                                                    {{ $projectType->formatted_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="openNewProjectTypeModal()">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
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
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- New Project Type Modal -->
    <div class="modal fade" id="newProjectTypeModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-tag-fill me-2"></i>Tambah Tipe Proyek
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="newProjectTypeForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Tipe Proyek *</label>
                            <input type="text" id="newProjectTypeName" class="form-control" placeholder="Contoh: Next.js, Angular" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Financial calculations
        function updateFinancialSummary() {
            const totalValue = parseFloat(document.getElementById('total_value').value) || 0;
            const dpAmount = parseFloat(document.getElementById('dp_amount').value) || 0;
            const remaining = totalValue - dpAmount;

            document.getElementById('summary-total').textContent = formatCurrency(totalValue);
            document.getElementById('summary-dp').textContent = formatCurrency(dpAmount);
            document.getElementById('summary-remaining').textContent = formatCurrency(remaining);
        }

        // Client Modal
        function openNewClientModal() {
            new bootstrap.Modal(document.getElementById('newClientModal')).show();
        }

        function closeClientModal() {
            bootstrap.Modal.getInstance(document.getElementById('newClientModal'))?.hide();
        }

        // Project Type Modal
        function openNewProjectTypeModal() {
            new bootstrap.Modal(document.getElementById('newProjectTypeModal')).show();
        }

        function closeProjectTypeModal() {
            bootstrap.Modal.getInstance(document.getElementById('newProjectTypeModal'))?.hide();
        }

        // Client form submission
        document.getElementById('newClientForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                name: document.getElementById('newClientName').value.trim(),
                phone: document.getElementById('newClientPhone').value.trim(),
                email: document.getElementById('newClientEmail').value.trim(),
                address: ''
            };

            if (!formData.name || !formData.phone) {
                alert('Nama dan nomor telepon wajib diisi!');
                return;
            }

            fetch('{{ route('api.clients.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.client) {
                        const clientSelect = document.getElementById('client_id');
                        const newOption = new Option(
                            `${data.client.name} - ${data.client.phone}`,
                            data.client.id, true, true
                        );
                        clientSelect.add(newOption);
                        closeClientModal();
                        this.reset();
                        alert('Klien berhasil ditambahkan!');
                    } else {
                        alert('Gagal menyimpan klien');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan klien');
                });
        });

        // Project Type form submission
        document.getElementById('newProjectTypeForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const typeName = document.getElementById('newProjectTypeName').value.trim();
            if (!typeName) {
                alert('Nama tipe proyek wajib diisi!');
                return;
            }

            const technicalName = typeName.toUpperCase().replace(/[^A-Z0-9]/g, '_').replace(/_+/g, '_');
            const displayName = typeName.replace(/\b\w/g, l => l.toUpperCase());

            const formData = {
                name: technicalName,
                display_name: displayName,
                description: `Proyek menggunakan teknologi ${displayName}`
            };

            fetch('{{ route('api.project-types.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.project_type) {
                        const typeSelect = document.getElementById('type');
                        const newOption = new Option(
                            data.project_type.display_name,
                            data.project_type.name, true, true
                        );
                        typeSelect.add(newOption);
                        closeProjectTypeModal();
                        this.reset();
                        alert('Tipe proyek berhasil ditambahkan!');
                    } else {
                        alert('Gagal menyimpan tipe proyek');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan tipe proyek');
                });
        });

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('total_value').addEventListener('input', updateFinancialSummary);
            document.getElementById('dp_amount').addEventListener('input', updateFinancialSummary);
            updateFinancialSummary();

            // Set minimum deadline
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('deadline').min = tomorrow.toISOString().split('T')[0];

            // Form validation
            document.getElementById('project-form').addEventListener('submit', function(e) {
                const totalValue = parseFloat(document.getElementById('total_value').value) || 0;
                const dpAmount = parseFloat(document.getElementById('dp_amount').value) || 0;

                if (dpAmount > totalValue) {
                    e.preventDefault();
                    alert('Jumlah DP tidak boleh melebihi nilai total proyek!');
                    return false;
                }

                if (totalValue < 100000) {
                    e.preventDefault();
                    alert('Nilai proyek minimal Rp 100.000');
                    return false;
                }
            });
        });

        // Make functions global
        window.openNewClientModal = openNewClientModal;
        window.openNewProjectTypeModal = openNewProjectTypeModal;
    </script>
@endpush
