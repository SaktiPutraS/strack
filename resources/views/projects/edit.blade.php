@extends('layouts.app')
@section('title', 'Edit Proyek: ' . $project->title)

@section('content')
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-pencil-square me-2"></i>Edit Proyek
                    </h1>
                    <p class="text-muted mb-0">Perbarui informasi proyek "{{ $project->title }}"</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Lihat Detail
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form Container -->
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <form action="{{ route('projects.update', $project) }}" method="POST" id="project-form">
                @csrf
                @method('PUT')

                <!-- Main Project Information Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-info-circle text-purple"></i>
                            </div>
                            Update data dasar proyek
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Client Selection -->
                            <div class="col-lg-6">
                                <label for="client_id" class="form-label fw-semibold">
                                    Klien <span class="text-danger">*</span>
                                </label>
                                <select name="client_id" id="client_id" class="form-select form-select-lg @error('client_id') is-invalid @enderror"
                                    required>
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
                            <div class="col-lg-6">
                                <label for="type" class="form-label fw-semibold">
                                    Tipe Proyek <span class="text-danger">*</span>
                                </label>
                                <select name="type" id="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                    <option value="">Pilih Tipe</option>
                                    @if (isset($projectTypes))
                                        @foreach ($projectTypes as $projectType)
                                            <option value="{{ $projectType->name }}"
                                                {{ old('type', $project->type) == $projectType->name ? 'selected' : '' }}>
                                                {{ $projectType->display_name }}
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
                                <label for="title" class="form-label fw-semibold">
                                    Judul Proyek <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ old('title', $project->title) }}" placeholder="e.g. Website E-Commerce Toko Online"
                                    required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deadline and Status -->
                            <div class="col-lg-6">
                                <label for="deadline" class="form-label fw-semibold">
                                    Deadline <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control form-control-lg @error('deadline') is-invalid @enderror" id="deadline"
                                    name="deadline" value="{{ old('deadline', $project->deadline->format('Y-m-d')) }}" required>
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6">
                                <label for="status" class="form-label fw-semibold">
                                    Status Proyek <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status" class="form-select form-select-lg @error('status') is-invalid @enderror" required>
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
                                <label for="description" class="form-label fw-semibold">
                                    Deskripsi Proyek <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5"
                                    placeholder="Deskripsikan detail proyek, fitur yang akan dibuat, teknologi yang digunakan, dll." required>{{ old('description', $project->description) }}</textarea>
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
                        <p class="text-muted mb-0">Update nilai proyek dan tracking pembayaran</p>
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
                                        name="total_value" value="{{ old('total_value', $project->total_value) }}" min="0" step="1000"
                                        required>
                                </div>
                                @error('total_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Tidak boleh lebih kecil dari jumlah yang sudah dibayar</div>
                            </div>

                            <!-- Paid Amount (Read Only) -->
                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">
                                    Sudah Dibayar
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-info bg-opacity-10 text-info fw-bold">Rp</span>
                                    <input type="text" class="form-control bg-light"
                                        value="{{ number_format($project->paid_amount, 0, ',', '.') }}" readonly>
                                </div>
                                <div class="form-text text-muted">Otomatis dihitung dari pembayaran</div>
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
                                    <i class="bi bi-journal-text text-warning me-2"></i>
                                    Catatan Khusus
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes', $project->notes) }}</textarea>
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
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-info">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash me-2"></i>Hapus Proyek
                                </button>
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
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
            document.getElementById('project-form').addEventListener('submit', function(e) {
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

                // Add loading state to submit button
                const submitBtn = e.target.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengupdate...';
                submitBtn.disabled = true;
            });

            // Enhanced form interactions
            const inputs = document.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.closest('.input-group, .form-control, .form-select')?.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    this.closest('.input-group, .form-control, .form-select')?.classList.remove('focused');
                });
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

    <style>
        /* Stat card styling dengan border atas berwarna */
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            transition: all 0.3s ease;
        }

        .stat-card-purple::before {
            background: linear-gradient(90deg, #8B5CF6, #A855F7);
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10B981, #059669);
        }

        .stat-card-info::before {
            background: linear-gradient(90deg, #06B6D4, #0891B2);
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #FFC107, #FF9800);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
        }

        .stat-card:hover::before {
            height: 6px;
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

        .form-control:focus,
        .form-select:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        .input-group.focused {
            transform: translateY(-1px);
            transition: transform 0.2s ease;
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.05) !important;
        }

        .border-purple {
            border-color: rgba(139, 92, 246, 0.3) !important;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        .badge {
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .card-header h5 {
                font-size: 1.1rem;
            }

            .btn-lg {
                font-size: 1rem;
                padding: 0.75rem 1.5rem;
            }
        }
    </style>
@endpush
