@extends('layouts.app')
@section('title', 'Tambah Proyek Baru')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-plus-circle"></i>Tambah Proyek Baru
                </h1>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
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
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <a href="{{ route('clients.create') }}" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah klien baru
                                    </a>
                                </div>
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
                                    <option value="LARAVEL" {{ old('type') == 'LARAVEL' ? 'selected' : '' }}>Laravel Framework</option>
                                    <option value="WORDPRESS" {{ old('type') == 'WORDPRESS' ? 'selected' : '' }}>WordPress</option>
                                    <option value="REACT" {{ old('type') == 'REACT' ? 'selected' : '' }}>React.js</option>
                                    <option value="VUE" {{ old('type') == 'VUE' ? 'selected' : '' }}>Vue.js</option>
                                    <option value="FLUTTER" {{ old('type') == 'FLUTTER' ? 'selected' : '' }}>Flutter</option>
                                    <option value="MOBILE" {{ old('type') == 'MOBILE' ? 'selected' : '' }}>Mobile App</option>
                                    <option value="OTHER" {{ old('type') == 'OTHER' ? 'selected' : '' }}>Other</option>
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

        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

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
    </script>
@endpush
