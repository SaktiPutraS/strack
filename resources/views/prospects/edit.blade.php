@extends('layouts.app')
@section('title', 'Edit Prospek: ' . $prospect->name)

@section('content')
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-pencil-square me-2"></i>Edit Prospek
                    </h1>
                    <p class="text-muted mb-0">Perbarui informasi prospek "{{ $prospect->name }}"</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('prospects.show', $prospect) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Lihat Detail
                    </a>
                    <a href="{{ route('prospects.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form Container -->
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <form action="{{ route('prospects.update', $prospect) }}" method="POST" id="prospect-form">
                @csrf
                @method('PUT')

                <!-- Main Information Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-info-circle text-purple"></i>
                            </div>
                            Update data prospek
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Name -->
                            <div class="col-lg-6">
                                <label for="name" class="form-label fw-semibold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name', $prospect->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- WhatsApp -->
                            <div class="col-lg-6">
                                <label for="whatsapp" class="form-label fw-semibold">
                                    Nomor WhatsApp <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control form-control-lg @error('whatsapp') is-invalid @enderror" id="whatsapp"
                                    name="whatsapp" value="{{ old('whatsapp', $prospect->whatsapp) }}" placeholder="08123456789" required>
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format: 08xxxxxxxxxx</div>
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">
                                    Alamat
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $prospect->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Social Media -->
                            <div class="col-12">
                                <label for="social_media" class="form-label fw-semibold">
                                    Link Sosial Media
                                </label>
                                <input type="url" class="form-control form-control-lg @error('social_media') is-invalid @enderror" id="social_media"
                                    name="social_media" value="{{ old('social_media', $prospect->social_media) }}"
                                    placeholder="https://instagram.com/username">
                                @error('social_media')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Link Instagram, Facebook, atau platform lainnya</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status & Notes Card -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-flag text-warning"></i>
                            </div>
                            Status & Keterangan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Status -->
                            <div class="col-12">
                                <label for="status" class="form-label fw-semibold">
                                    Status Prospek <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status" class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                                    <option value="BELUM_DIHUBUNGI" {{ old('status', $prospect->status) == 'BELUM_DIHUBUNGI' ? 'selected' : '' }}>Belum
                                        Dihubungi</option>
                                    <option value="PENGECEKAN_KEAKTIFAN"
                                        {{ old('status', $prospect->status) == 'PENGECEKAN_KEAKTIFAN' ? 'selected' : '' }}>Pengecekan Keaktifan Usaha
                                    </option>
                                    <option value="PENAWARAN" {{ old('status', $prospect->status) == 'PENAWARAN' ? 'selected' : '' }}>Penawaran</option>
                                    <option value="FOLLOW_UP" {{ old('status', $prospect->status) == 'FOLLOW_UP' ? 'selected' : '' }}>Follow Up Penawaran
                                    </option>
                                    <option value="TOLAK" {{ old('status', $prospect->status) == 'TOLAK' ? 'selected' : '' }}>Tolak</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    Keterangan / Catatan
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="5"
                                    placeholder="Catatan penting tentang prospek ini...">{{ old('notes', $prospect->notes) }}</textarea>
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
                                <a href="{{ route('prospects.show', $prospect) }}" class="btn btn-outline-info">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash me-2"></i>Hapus Prospek
                                </button>
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('prospects.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Update Prospek
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Delete Form (Hidden) -->
            <form id="delete-form" action="{{ route('prospects.destroy', $prospect) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format phone number input
            const whatsappInput = document.getElementById('whatsapp');

            whatsappInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value && !value.startsWith('0') && !value.startsWith('62')) {
                    value = '0' + value;
                }
                e.target.value = value;
            });

            // Form validation
            const prospectForm = document.getElementById('prospect-form');

            prospectForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
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
            Swal.fire({
                title: 'Hapus Prospek?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC3545',
                cancelButtonColor: '#6C757D',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
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

        .input-group.focused {
            transform: translateY(-1px);
            transition: transform 0.2s ease;
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
