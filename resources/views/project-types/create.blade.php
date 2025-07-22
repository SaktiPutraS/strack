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
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <label for="name" class="form-label fw-semibold">
                                    Nama Tipe <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Nama internal (huruf besar, tanpa spasi)</div>
                            </div>

                            <div class="col-lg-6">
                                <label for="display_name" class="form-label fw-semibold">
                                    Nama Tampilan <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('display_name') is-invalid @enderror" id="display_name"
                                    name="display_name" value="{{ old('display_name') }}" required>
                                @error('display_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Nama yang ditampilkan di form</div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6">
                                <label for="sort_order" class="form-label fw-semibold">Urutan Tampil</label>
                                <input type="number" class="form-control form-control-lg @error('sort_order') is-invalid @enderror" id="sort_order"
                                    name="sort_order" value="{{ old('sort_order') }}" min="1" placeholder="10">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kosongkan untuk otomatis</div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
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

                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <a href="{{ route('project-types.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <div class="d-flex gap-3">
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
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate display name from name
            const nameInput = document.getElementById('name');
            const displayNameInput = document.getElementById('display_name');

            nameInput.addEventListener('input', function() {
                const name = this.value;
                if (name && !displayNameInput.value) {
                    const displayName = name.replace(/[_-]/g, ' ')
                        .toLowerCase()
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');
                    displayNameInput.value = displayName;
                }
            });

            // Form validation dengan SweetAlert
            document.getElementById('project-type-form').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const displayName = document.getElementById('display_name').value.trim();

                if (!name || !displayName) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Form Tidak Lengkap',
                        text: 'Nama tipe dan nama tampilan wajib diisi!',
                        confirmButtonColor: '#8B5CF6',
                    });
                    return false;
                }

                const submitBtn = e.target.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                submitBtn.disabled = true;
            });

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

            // Focus on first input
            document.getElementById('name').focus();
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

        .text-purple {
            color: #8B5CF6 !important;
        }
    </style>
@endpush
