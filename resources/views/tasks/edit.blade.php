@extends('layouts.app')
@section('title', 'Edit Tugas: ' . $task->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-pencil-square me-2"></i>Edit Tugas
                    </h1>
                    <p class="text-muted mb-0">{{ $task->name }}</p>
                </div>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <form action="{{ route('tasks.update', $task) }}" method="POST" id="task-form">
                @csrf
                @method('PUT')

                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-info-circle text-purple"></i>
                            </div>
                            Update Data Tugas
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold">
                                    Nama Tugas <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name', $task->name) }}" required placeholder="Masukkan nama tugas">
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
                                    placeholder="Jelaskan detail tugas yang harus dikerjakan">{{ old('description', $task->description) }}</textarea>
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
                                    <option value="daily" {{ old('schedule', $task->schedule) === 'daily' ? 'selected' : '' }}>
                                        Senin-Jumat (Setiap Hari)
                                    </option>
                                    <option value="weekly" {{ old('schedule', $task->schedule) === 'weekly' ? 'selected' : '' }}>
                                        Seminggu Sekali (Setiap Senin)
                                    </option>
                                    <option value="monthly" {{ old('schedule', $task->schedule) === 'monthly' ? 'selected' : '' }}>
                                        Sebulan Sekali (Tanggal 1)
                                    </option>
                                    <option value="once" {{ old('schedule', $task->schedule) === 'once' ? 'selected' : '' }}>
                                        Sekali Kerja (Tanggal Tertentu)
                                    </option>
                                </select>
                                @error('schedule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6 {{ $task->schedule === 'once' ? '' : 'd-none' }}" id="target-date-field">
                                <label for="target_date" class="form-label fw-semibold">
                                    Tanggal Target <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-select form-select-lg @error('target_date') is-invalid @enderror" id="target_date"
                                    name="target_date" value="{{ old('target_date', $task->target_date ? $task->target_date->format('Y-m-d') : '') }}"
                                    min="{{ date('Y-m-d') }}">
                                @error('target_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Tanggal tugas akan muncul untuk user</div>
                                @if ($task->target_date)
                                    <div class="mt-2">
                                        <small class="text-info">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Saat ini: {{ $task->target_date->format('d M Y') }}
                                        </small>
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-6">
                                <label for="status" class="form-label fw-semibold">Status Tugas</label>
                                <select class="form-select form-select-lg @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="active" {{ old('status', $task->status) === 'active' ? 'selected' : '' }}>
                                        Aktif
                                    </option>
                                    <option value="inactive" {{ old('status', $task->status) === 'inactive' ? 'selected' : '' }}>
                                        Tidak Aktif
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Tugas yang tidak aktif tidak akan muncul di daftar tugas user</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($task->assignments()->count() > 0)
                    <div class="card luxury-card border-0 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start">
                                <div class="luxury-icon me-3">
                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-warning mb-2">Perhatian</h6>
                                    <p class="text-muted mb-2">
                                        Tugas ini memiliki <strong>{{ $task->assignments()->count() }} assignment</strong>.
                                        Perubahan jadwal akan mempengaruhi kapan tugas ini muncul untuk user.
                                    </p>
                                    <small class="text-muted">Pastikan perubahan sudah sesuai sebelum menyimpan.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                @if ($task->assignments()->count() == 0)
                                    <button type="button" class="btn btn-outline-danger btn-lg" onclick="confirmDelete()">
                                        <i class="bi bi-trash me-2"></i>Hapus Tugas
                                    </button>
                                @endif
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Update Tugas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Delete Form -->
            <form id="delete-form" action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Yakin menghapus tugas ini?',
                text: "Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }

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
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengupdate...';
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

            // Trigger initial highlight based on current schedule
            document.getElementById('schedule').dispatchEvent(new Event('change'));
        });
    </script>

    <style>
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
