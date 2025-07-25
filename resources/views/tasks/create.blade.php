@extends('layouts.app')
@section('title', 'Tambah Tugas Baru')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Tugas Harian Baru
                    </h1>
                    <p class="text-muted mb-0">Buat tugas harian baru untuk tim</p>
                </div>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <form action="{{ route('tasks.store') }}" method="POST" id="task-form">
                @csrf

                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-info-circle text-purple"></i>
                            </div>
                            Informasi Tugas
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold">
                                    Nama Tugas <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" required placeholder="Masukkan nama tugas">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Nama tugas yang jelas dan mudah dipahami</div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">
                                    Deskripsi Tugas <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required
                                    placeholder="Jelaskan detail tugas yang harus dikerjakan">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Berikan instruksi yang jelas tentang apa yang harus dikerjakan</div>
                            </div>

                            <div class="col-lg-6">
                                <label for="schedule" class="form-label fw-semibold">
                                    Jadwal Tugas <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg @error('schedule') is-invalid @enderror" id="schedule" name="schedule"
                                    required>
                                    <option value="">Pilih jadwal tugas</option>
                                    <option value="daily" {{ old('schedule') === 'daily' ? 'selected' : '' }}>
                                        Senin-Jumat (Setiap Hari)
                                    </option>
                                    <option value="weekly" {{ old('schedule') === 'weekly' ? 'selected' : '' }}>
                                        Seminggu Sekali (Setiap Senin)
                                    </option>
                                    <option value="monthly" {{ old('schedule') === 'monthly' ? 'selected' : '' }}>
                                        Sebulan Sekali (Tanggal 1)
                                    </option>
                                    <option value="once" {{ old('schedule') === 'once' ? 'selected' : '' }}>
                                        Sekali Kerja (Tanggal Tertentu)
                                    </option>
                                </select>
                                @error('schedule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6 d-none" id="target-date-field">
                                <label for="target_date" class="form-label fw-semibold">
                                    Tanggal Target <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-select form-select-lg @error('target_date') is-invalid @enderror" id="target_date"
                                    name="target_date" value="{{ old('target_date') }}" min="{{ date('Y-m-d') }}">
                                @error('target_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Tanggal tugas akan muncul untuk user</div>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="status" value="active" checked>
                                    <label class="form-check-label fw-medium" for="is_active">
                                        Aktifkan tugas ini
                                    </label>
                                </div>
                                <div class="form-text">Tugas yang tidak aktif tidak akan muncul untuk user</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Simpan Tugas
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
            // Form validation dengan SweetAlert
            document.getElementById('task-form').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const description = document.getElementById('description').value.trim();
                const schedule = document.getElementById('schedule').value;
                const targetDate = document.getElementById('target_date').value;

                if (!name || !description || !schedule) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Form Tidak Lengkap',
                        text: 'Semua field wajib diisi!',
                        confirmButtonColor: '#8B5CF6',
                    });
                    return false;
                }

                if (schedule === 'once' && !targetDate) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Tanggal Target Diperlukan',
                        text: 'Pilih tanggal target untuk tugas sekali kerja!',
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

            // Schedule change handler for better UX
            document.getElementById('schedule').addEventListener('change', function() {
                const value = this.value;
                const targetDateField = document.getElementById('target-date-field');
                const targetDateInput = document.getElementById('target_date');

                if (value === 'once') {
                    targetDateField.classList.remove('d-none');
                    targetDateInput.required = true;
                } else {
                    targetDateField.classList.add('d-none');
                    targetDateInput.required = false;
                }

                const infoCard = document.querySelector('.card-body .row.g-3');

                // Reset all highlights
                infoCard.querySelectorAll('.col-md-4').forEach(col => {
                    col.style.backgroundColor = '';
                    col.style.borderRadius = '';
                    col.style.transition = '';
                });

                // Highlight selected schedule
                let targetIndex = -1;
                if (value === 'daily') targetIndex = 0;
                else if (value === 'weekly') targetIndex = 1;
                else if (value === 'monthly') targetIndex = 2;

                if (targetIndex >= 0) {
                    const targetCol = infoCard.children[targetIndex];
                    targetCol.style.backgroundColor = 'rgba(139, 92, 246, 0.05)';
                    targetCol.style.borderRadius = '12px';
                    targetCol.style.transition = 'all 0.3s ease';
                }
            });
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

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }
        }
    </style>
@endpush
