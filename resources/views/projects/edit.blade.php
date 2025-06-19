@extends('layouts.app')
@section('title', 'Edit Proyek: ' . $project->title)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-pencil-square"></i>Edit Proyek
                </h1>
                <div class="btn-group">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Lihat
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')

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
                                            <option value="{{ $client->id }}"
                                                {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} - {{ $client->phone }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
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
                                    @if (isset($projectTypes))
                                        @foreach ($projectTypes as $type)
                                            <option value="{{ $type }}" {{ old('type', $project->type) == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    @endif
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
                                    value="{{ old('title', $project->title) }}" placeholder="e.g. Website E-Commerce Toko Online" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deadline and Status -->
                            <div class="col-md-6">
                                <label for="deadline" class="form-label">
                                    <i class="bi bi-calendar3 text-lilac me-2"></i>
                                    Deadline <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline"
                                    value="{{ old('deadline', $project->deadline->format('Y-m-d')) }}" required>
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">
                                    <i class="bi bi-flag text-lilac me-2"></i>
                                    Status Proyek <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="WAITING" {{ old('status', $project->status) == 'WAITING' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="PROGRESS" {{ old('status', $project->status) == 'PROGRESS' ? 'selected' : '' }}>Dalam Progress
                                    </option>
                                    <option value="FINISHED" {{ old('status', $project->status) == 'FINISHED' ? 'selected' : '' }}>Selesai</option>
                                    <option value="CANCELLED" {{ old('status', $project->status) == 'CANCELLED' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="bi bi-card-text text-lilac me-2"></i>
                                    Deskripsi Proyek <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4"
                                    placeholder="Deskripsikan detail proyek, fitur yang akan dibuat, teknologi yang digunakan, dll." required>{{ old('description', $project->description) }}</textarea>
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
                                        name="total_value" value="{{ old('total_value', $project->total_value) }}" min="0" step="1000"
                                        required>
                                </div>
                                @error('total_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Masukkan nilai tanpa titik atau koma</div>
                            </div>

                            <!-- Paid Amount (Read Only) -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-wallet2 text-lilac me-2"></i>
                                    Sudah Dibayar
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" value="{{ number_format($project->paid_amount, 0, ',', '.') }}"
                                        readonly>
                                </div>
                                <div class="form-text text-muted">Otomatis dihitung dari pembayaran</div>
                            </div>

                            <!-- Financial Summary -->
                            <div class="col-12">
                                <div class="p-3 bg-light rounded">
                                    <h6 class="text-muted mb-3">Ringkasan Keuangan</h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <small class="text-muted">Nilai Total:</small>
                                            <div class="fw-bold text-primary" id="summary-total">{{ $project->formatted_total_value }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Sudah Dibayar:</small>
                                            <div class="fw-bold text-success">{{ $project->formatted_paid_amount }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Sisa Pembayaran:</small>
                                            <div class="fw-bold text-warning" id="summary-remaining">{{ $project->formatted_remaining_amount }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Progress:</small>
                                            <div class="fw-bold text-info" id="summary-progress">{{ $project->progress_percentage }}%</div>
                                        </div>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="mt-2">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar"
                                                style="width: {{ $project->progress_percentage }}%; background: var(--lilac-primary);"
                                                id="progress-bar"></div>
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
                                    placeholder="Catatan khusus tentang proyek ini (opsional)">{{ old('notes', $project->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-info me-2">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash me-2"></i>Hapus Proyek
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('projects.index') }}" class="btn btn-secondary me-2">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Proyek
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Delete Form (Hidden) -->
            <form id="delete-form" action="{{ route('projects.destroy', $project) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalValueInput = document.getElementById('total_value');
            const paidAmount = {{ $project->paid_amount }};

            function updateFinancialSummary() {
                const totalValue = parseFloat(totalValueInput.value) || 0;
                const remaining = totalValue - paidAmount;
                const progress = totalValue > 0 ? ((paidAmount / totalValue) * 100).toFixed(1) : 0;

                document.getElementById('summary-total').textContent = formatCurrency(totalValue);
                document.getElementById('summary-remaining').textContent = formatCurrency(remaining);
                document.getElementById('summary-progress').textContent = progress + '%';

                // Update progress bar
                const progressBar = document.getElementById('progress-bar');
                progressBar.style.width = progress + '%';
            }

            function formatCurrency(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }

            totalValueInput.addEventListener('input', updateFinancialSummary);

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const totalValue = parseFloat(totalValueInput.value) || 0;

                if (totalValue < paidAmount) {
                    e.preventDefault();
                    alert('Nilai total proyek tidak boleh lebih kecil dari jumlah yang sudah dibayar!');
                    return false;
                }

                if (totalValue < 100000) {
                    e.preventDefault();
                    alert('Nilai proyek minimal Rp 100.000');
                    return false;
                }
            });
        });

        function confirmDelete() {
            if ({{ $project->payments->count() }} > 0) {
                alert('Tidak dapat menghapus proyek yang sudah memiliki pembayaran!');
                return;
            }

            if (confirm('Apakah Anda yakin ingin menghapus proyek ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
@endpush
