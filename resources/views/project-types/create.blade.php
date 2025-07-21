@extends('layouts.app')
@section('title', 'Tambah Tipe Proyek')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Tipe Proyek
                    </h1>
                    <p class="text-muted mb-0">Buat tipe proyek baru untuk klasifikasi</p>
                </div>
                <a href="{{ route('project-types.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <form action="{{ route('project-types.store') }}" method="POST" id="project-type-form">
                @csrf

                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-info-circle text-purple"></i>
                            </div>
                            Informasi Tipe Proyek
                        </h5>
                        <p class="text-muted mb-0">Data dasar untuk tipe proyek baru</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="bi bi-tag text-purple me-2"></i>
                                    Nama Tipe <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" placeholder="e.g. LARAVEL" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Nama internal (huruf besar, tanpa spasi)</div>
                            </div>

                            <div class="col-lg-6">
                                <label for="display_name" class="form-label fw-semibold">
                                    <i class="bi bi-eye text-purple me-2"></i>
                                    Nama Tampilan <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('display_name') is-invalid @enderror" id="display_name"
                                    name="display_name" value="{{ old('display_name') }}" placeholder="e.g. Laravel Framework" required>
                                @error('display_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Nama yang ditampilkan di form</div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">
                                    <i class="bi bi-card-text text-purple me-2"></i>
                                    Deskripsi
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3"
                                    placeholder="Deskripsi singkat tentang tipe proyek ini (opsional)">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6">
                                <label for="sort_order" class="form-label fw-semibold">
                                    <i class="bi bi-sort-numeric-down text-purple me-2"></i>
                                    Urutan Tampil
                                </label>
                                <input type="number" class="form-control form-control-lg @error('sort_order') is-invalid @enderror" id="sort_order"
                                    name="sort_order" value="{{ old('sort_order') }}" min="1" placeholder="10">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kosongkan untuk otomatis</div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on text-purple me-2"></i>
                                    Status
                                </label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="is_active">
                                        Aktifkan tipe proyek ini
                                    </label>
                                </div>
                                <div class="form-text">Tipe yang tidak aktif tidak akan muncul di form proyek</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="card luxury-card border-0 mb-4" id="preview-card" style="display: none;">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-eye text-info"></i>
                            </div>
                            Preview
                        </h5>
                        <p class="text-muted mb-0">Pratinjau tipe proyek yang akan dibuat</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="bg-purple-light border border-2 border-dashed border-purple rounded-3 p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-bold mb-1" id="preview-display-name">-</h6>
                                    <small class="text-muted" id="preview-name">-</small>
                                    <p class="text-muted mb-0 mt-1" id="preview-description" style="display: none;"></p>
                                </div>
                                <div class="text-end">
                                    <span class="badge" id="preview-status">-</span>
                                    <div class="small text-muted mt-1">
                                        Urutan: <span id="preview-order">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <a href="{{ route('project-types.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>

                            <div class="d-flex gap-3">
                                <button type="button" class="btn btn-outline-primary btn-lg" onclick="resetForm()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Simpan Tipe
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
        function resetForm() {
            if (confirm('Apakah Anda yakin ingin mengatur ulang semua data form?')) {
                document.getElementById('project-type-form').reset();
                document.getElementById('is_active').checked = true;
                updatePreview();
            }
        }

        function updatePreview() {
            const name = document.getElementById('name').value || '-';
            const displayName = document.getElementById('display_name').value || '-';
            const description = document.getElementById('description').value;
            const sortOrder = document.getElementById('sort_order').value || 'Auto';
            const isActive = document.getElementById('is_active').checked;

            // Update preview elements
            document.getElementById('preview-name').textContent = name;
            document.getElementById('preview-display-name').textContent = displayName;
            document.getElementById('preview-order').textContent = sortOrder;

            // Description
            const descElement = document.getElementById('preview-description');
            if (description) {
                descElement.textContent = description;
                descElement.style.display = 'block';
            } else {
                descElement.style.display = 'none';
            }

            // Status badge
            const statusElement = document.getElementById('preview-status');
            if (isActive) {
                statusElement.className = 'badge bg-success bg-opacity-10 text-success border border-success';
                statusElement.innerHTML = '<i class="bi bi-check-circle me-1"></i>AKTIF';
            } else {
                statusElement.className = 'badge bg-warning bg-opacity-10 text-warning border border-warning';
                statusElement.innerHTML = '<i class="bi bi-pause-circle me-1"></i>NONAKTIF';
            }

            // Show/hide preview card
            const previewCard = document.getElementById('preview-card');
            if (name !== '-' || displayName !== '-') {
                previewCard.style.display = 'block';
            } else {
                previewCard.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate display name from name
            const nameInput = document.getElementById('name');
            const displayNameInput = document.getElementById('display_name');

            nameInput.addEventListener('input', function() {
                const name = this.value;
                if (name && !displayNameInput.value) {
                    // Convert to title case and replace underscores/dashes with spaces
                    const displayName = name.replace(/[_-]/g, ' ')
                        .toLowerCase()
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');
                    displayNameInput.value = displayName;
                }
                updatePreview();
            });

            // Update preview on input changes
            const inputs = ['name', 'display_name', 'description', 'sort_order', 'is_active'];
            inputs.forEach(inputId => {
                document.getElementById(inputId).addEventListener('input', updatePreview);
                document.getElementById(inputId).addEventListener('change', updatePreview);
            });

            // Form validation
            document.getElementById('project-type-form').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const displayName = document.getElementById('display_name').value.trim();

                if (!name || !displayName) {
                    e.preventDefault();
                    alert('Nama tipe dan nama tampilan wajib diisi!');
                    return false;
                }

                // Add loading state to submit button
                const submitBtn = e.target.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                submitBtn.disabled = true;
            });

            // Focus on first input
            document.getElementById('name').focus();

            // Initialize preview
            updatePreview();
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

        .form-check-input:focus {
            border-color: #A855F7;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.25);
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

        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.05) !important;
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .border-purple {
            border-color: rgba(139, 92, 246, 0.3) !important;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .card-header h5 {
                font-size: 1.1rem;
            }
        }
    </style>
@endpush
