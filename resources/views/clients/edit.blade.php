@extends('layouts.app')
@section('title', 'Edit Klien: ' . $client->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-person-gear me-2"></i>Edit Klien
                    </h1>
                    <p class="text-muted mb-0">{{ $client->name }}</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Lihat Detail
                    </a>
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <form action="{{ route('clients.update', $client) }}" method="POST" id="client-form">
                @csrf
                @method('PUT')

                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-info-circle text-purple"></i>
                            </div>
                            Update Data Klien
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Nama Klien -->
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold">
                                    Nama Klien <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name', $client->name) }}" placeholder="Masukkan nama lengkap klien" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nomor Telepon -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    Nomor Telepon <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control form-control-lg @error('phone') is-invalid @enderror" id="phone"
                                    name="phone" value="{{ old('phone', $client->phone) }}" placeholder="08123456789" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format: 08xxxxxxxxxx (untuk WhatsApp)</div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email', $client->email) }}" placeholder="nama@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opsional - untuk komunikasi email</div>
                            </div>

                            <!-- Alamat -->
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Alamat</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                    placeholder="Alamat lengkap klien (opsional)">{{ old('address', $client->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opsional - untuk referensi lokasi</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Statistics -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-graph-up text-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-info mb-2">Statistik Klien</h6>
                                <div class="row g-3">
                                    <div class="col-6 col-md-3">
                                        <div class="text-center">
                                            <div class="fw-bold text-purple">{{ $client->projects->count() }}</div>
                                            <small class="text-muted">Total Proyek</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="text-center">
                                            <div class="fw-bold text-success fs-7">Rp {{ number_format($client->total_project_value, 0, ',', '.') }}
                                            </div>
                                            <small class="text-muted">Nilai Total</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="text-center">
                                            <div class="fw-bold text-primary fs-7">Rp {{ number_format($client->total_paid, 0, ',', '.') }}</div>
                                            <small class="text-muted">Sudah Dibayar</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="text-center">
                                            <div class="fw-bold text-warning fs-7">Rp {{ number_format($client->total_remaining, 0, ',', '.') }}</div>
                                            <small class="text-muted">Sisa Tagihan</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($client->projects()->count() > 0)
                    <div class="card luxury-card border-0 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start">
                                <div class="luxury-icon me-3">
                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-warning mb-2">Perhatian</h6>
                                    <p class="text-muted mb-2">
                                        Klien ini memiliki <strong>{{ $client->projects()->count() }} proyek</strong>.
                                        Perubahan data akan mempengaruhi semua proyek yang terkait.
                                    </p>
                                    <small class="text-muted">Pastikan perubahan sudah sesuai sebelum menyimpan.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                @if ($client->projects()->count() == 0)
                                    <button type="button" class="btn btn-outline-danger btn-lg" onclick="confirmDelete()">
                                        <i class="bi bi-trash me-2"></i>Hapus Klien
                                    </button>
                                @endif
                                <a href="{{ $client->whatsapp_link }}" target="_blank" class="btn btn-outline-success btn-lg">
                                    <i class="bi bi-whatsapp me-2"></i>WhatsApp
                                </a>
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Update Klien
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Delete Form -->
            <form id="delete-form" action="{{ route('clients.destroy', $client) }}" method="POST" style="display: none;">
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
                title: 'Yakin menghapus klien?',
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

            // Form validation
            document.getElementById('client-form').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const phone = document.getElementById('phone').value.trim();

                if (!name || !phone) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Form Tidak Lengkap',
                        text: 'Nama klien dan nomor telepon wajib diisi!',
                        confirmButtonColor: '#8B5CF6',
                    });
                    return false;
                }

                const submitBtn = e.target.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengupdate...';
                submitBtn.disabled = true;
            });

            // Format phone number input
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');

                if (value && !value.startsWith('0') && !value.startsWith('62')) {
                    value = '0' + value;
                }

                e.target.value = value;
            });
        });
    </script>

    <style>
        .form-control:focus,
        .form-select:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
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

        .fs-7 {
            font-size: 0.875rem;
        }
    </style>
@endpush
