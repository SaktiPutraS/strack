@extends('layouts.app')
@section('title', 'Tambah Proyek Baru')

@section('content')
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Proyek Baru
                    </h1>
                </div>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Main Form Container -->
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <form action="{{ route('projects.store') }}" method="POST" id="project-form">
                @csrf

                <!-- Main Project Information Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-info-circle text-purple"></i>
                            </div>
                            Informasi Proyek
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Client Selection -->
                            <div class="col-lg-4">
                                <label for="client_id" class="form-label fw-semibold">
                                    Klien <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex gap-2">
                                    <select name="client_id" id="client_id" class="form-select form-select-lg @error('client_id') is-invalid @enderror"
                                        required>
                                        <option value="">Pilih Klien</option>
                                        @if (isset($clients))
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->name }} - {{ $client->phone }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newClientModal">
                                        <i class="bi bi-plus-circle"></i>
                                    </button>
                                </div>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Project Type -->
                            <div class="col-lg-4">
                                <label for="type" class="form-label fw-semibold">
                                    Tipe Proyek <span class="text-danger">*</span>
                                </label>
                                <select name="type" id="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                    <option value="">Pilih Tipe Proyek</option>
                                    @if (isset($projectTypes))
                                        @foreach ($projectTypes as $projectType)
                                            <option value="{{ $projectType->name }}" {{ old('type') == $projectType->name ? 'selected' : '' }}>
                                                {{ $projectType->display_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deadline -->
                            <div class="col-lg-4">
                                <label for="deadline" class="form-label fw-semibold">
                                    Deadline <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control form-control-lg @error('deadline') is-invalid @enderror" id="deadline"
                                    name="deadline" value="{{ old('deadline', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Project Title -->
                            <div class="col-12">
                                <label for="title" class="form-label fw-semibold">
                                    Judul Proyek <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">
                                    Deskripsi Proyek <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Information Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-currency-dollar text-success"></i>
                            </div>
                            Informasi Keuangan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Total Value -->
                            <div class="col-lg-6">
                                <label for="total_value" class="form-label fw-semibold">
                                    Nilai Total Proyek <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-success bg-opacity-10 text-success fw-bold">Rp</span>
                                    <input type="number" class="form-control @error('total_value') is-invalid @enderror" id="total_value"
                                        name="total_value" value="{{ old('total_value') }}" min="0" step="1000" placeholder="0" required>
                                </div>
                                @error('total_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimal Rp 100.000</div>
                            </div>

                            <!-- DP Amount -->
                            <div class="col-lg-6">
                                <label for="dp_amount" class="form-label fw-semibold">
                                    Down Payment (DP)
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-warning bg-opacity-10 text-warning fw-bold">Rp</span>
                                    <input type="number" class="form-control @error('dp_amount') is-invalid @enderror" id="dp_amount"
                                        name="dp_amount" value="{{ old('dp_amount', 0) }}" min="0" step="1000" placeholder="0">
                                </div>
                                @error('dp_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Pembayaran awal (opsional)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    Catatan Khusus
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
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
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Simpan Proyek
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Klien Baru -->
    <div class="modal fade" id="newClientModal" tabindex="-1" aria-labelledby="newClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-purple" id="newClientModalLabel">
                        <i class="bi bi-person-plus me-2"></i>Tambah Klien Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="newClientForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_client_name" class="form-label fw-semibold">
                                Nama Klien <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="new_client_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_client_phone" class="form-label fw-semibold">
                                Nomor Telepon <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control" id="new_client_phone" name="phone" placeholder="08123456789" required>
                            <div class="form-text">Format: 08xxxxxxxxxx</div>
                        </div>
                        <div class="mb-3">
                            <label for="new_client_email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="new_client_email" name="email" placeholder="nama@email.com">
                            <div class="form-text">Opsional</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Simpan Klien
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert untuk session messages
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

            // Modal form submit handler
            document.getElementById('newClientForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                submitBtn.disabled = true;

                fetch('{{ route('api.clients.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Add new client to select
                            const clientSelect = document.getElementById('client_id');
                            const newOption = new Option(
                                `${data.client.name} - ${data.client.phone}`,
                                data.client.id,
                                true,
                                true
                            );
                            clientSelect.add(newOption);

                            // Close modal and reset form
                            const modal = bootstrap.Modal.getInstance(document.getElementById('newClientModal'));
                            modal.hide();
                            this.reset();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Klien baru berhasil ditambahkan',
                                confirmButtonColor: '#8B5CF6',
                                timer: 2000,
                                timerProgressBar: true
                            });
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: error.message,
                            confirmButtonColor: '#8B5CF6'
                        });
                    })
                    .finally(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });

            // Format phone number input
            document.getElementById('new_client_phone').addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value && !value.startsWith('0') && !value.startsWith('62')) {
                    value = '0' + value;
                }
                e.target.value = value;
            });

            // Project form validation
            document.getElementById('project-form').addEventListener('submit', function(e) {
                const totalValue = parseFloat(document.getElementById('total_value').value) || 0;
                const dpAmount = parseFloat(document.getElementById('dp_amount').value) || 0;

                if (dpAmount > totalValue) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Jumlah DP tidak boleh melebihi nilai total proyek!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return false;
                }

                if (totalValue < 100000) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Nilai proyek minimal Rp 100.000',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return false;
                }

                const submitBtn = e.target.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                submitBtn.disabled = true;
            });

            // Focus on client select
            document.getElementById('client_id').focus();
        });
    </script>

    <style>
        .form-control:focus,
        .form-select:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        .text-purple {
            color: #8B5CF6 !important;
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

        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(139, 92, 246, 0.1);
        }

        @media (max-width: 768px) {
            .card-header h5 {
                font-size: 1.1rem;
            }

            .input-group-lg .form-control,
            .input-group-lg .input-group-text {
                font-size: 1rem;
            }
        }
    </style>
@endpush
