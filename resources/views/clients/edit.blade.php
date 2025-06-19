@extends('layouts.app')
@section('title', 'Edit Klien: ' . $client->name)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-person-gear"></i>Edit Klien: {{ $client->name }}
                </h1>
                <div class="btn-group">
                    <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Lihat
                    </a>
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('clients.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Nama Klien -->
                            <div class="col-12">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person text-lilac me-2"></i>
                                    Nama Klien <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                    value="{{ old('name', $client->name) }}" placeholder="Masukkan nama lengkap klien" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nomor Telepon -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    <i class="bi bi-telephone text-lilac me-2"></i>
                                    Nomor Telepon <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                    value="{{ old('phone', $client->phone) }}" placeholder="08123456789" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Format: 08xxxxxxxxxx (untuk WhatsApp)
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope text-lilac me-2"></i>
                                    Email
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                    value="{{ old('email', $client->email) }}" placeholder="nama@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opsional - untuk komunikasi email</div>
                            </div>

                            <!-- Alamat -->
                            <div class="col-12">
                                <label for="address" class="form-label">
                                    <i class="bi bi-geo-alt text-lilac me-2"></i>
                                    Alamat
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                    placeholder="Alamat lengkap klien (opsional)">{{ old('address', $client->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opsional - untuk referensi lokasi</div>
                            </div>
                        </div>

                        <!-- Preview WhatsApp Link -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-whatsapp text-success me-2"></i>
                                Preview Link WhatsApp
                            </h6>
                            <div id="whatsapp-preview" class="text-muted">
                                <a href="{{ $client->whatsapp_link }}" target="_blank" class="text-success text-decoration-none">
                                    <i class="bi bi-whatsapp me-2"></i>{{ $client->whatsapp_link }}
                                </a>
                            </div>
                        </div>

                        <!-- Client Statistics -->
                        <div class="mt-4 p-3 bg-lilac-soft rounded">
                            <h6 class="text-lilac mb-3">
                                <i class="bi bi-graph-up me-2"></i>
                                Statistik Klien
                            </h6>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div class="text-center">
                                        <div class="fw-bold text-lilac">{{ $client->projects->count() }}</div>
                                        <small class="text-muted">Total Proyek</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center">
                                        <div class="fw-bold text-success">Rp {{ number_format($client->total_project_value, 0, ',', '.') }}</div>
                                        <small class="text-muted">Nilai Total</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center">
                                        <div class="fw-bold text-primary">Rp {{ number_format($client->total_paid, 0, ',', '.') }}</div>
                                        <small class="text-muted">Sudah Dibayar</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center">
                                        <div class="fw-bold text-warning">Rp {{ number_format($client->total_remaining, 0, ',', '.') }}</div>
                                        <small class="text-muted">Sisa Tagihan</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <div>
                                <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-info me-2">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash me-2"></i>Hapus
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('clients.index') }}" class="btn btn-secondary me-2">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Klien
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form (Hidden) -->
                    <form id="delete-form" action="{{ route('clients.destroy', $client) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            const whatsappPreview = document.getElementById('whatsapp-preview');

            function updateWhatsAppPreview() {
                const phone = phoneInput.value.trim();
                if (phone) {
                    // Clean phone number
                    let cleanPhone = phone.replace(/[^0-9]/g, '');

                    // Convert to international format
                    if (cleanPhone.startsWith('0')) {
                        cleanPhone = '62' + cleanPhone.substring(1);
                    } else if (!cleanPhone.startsWith('62')) {
                        cleanPhone = '62' + cleanPhone;
                    }

                    const whatsappUrl = `https://wa.me/${cleanPhone}`;
                    whatsappPreview.innerHTML = `
                <a href="${whatsappUrl}" target="_blank" class="text-success text-decoration-none">
                    <i class="bi bi-whatsapp me-2"></i>${whatsappUrl}
                </a>
            `;
                } else {
                    whatsappPreview.innerHTML = 'Masukkan nomor telepon untuk melihat link WhatsApp';
                    whatsappPreview.className = 'text-muted';
                }
            }

            phoneInput.addEventListener('input', updateWhatsAppPreview);

            // Format phone number input
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');

                // Ensure starts with 0 for Indonesian format
                if (value && !value.startsWith('0') && !value.startsWith('62')) {
                    value = '0' + value;
                }

                e.target.value = value;
                updateWhatsAppPreview();
            });
        });

        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus klien ini?\n\nPerhatian: Semua data proyek yang terkait juga akan ikut terhapus!')) {
                if (confirm('Konfirmasi sekali lagi - Tindakan ini tidak dapat dibatalkan!')) {
                    document.getElementById('delete-form').submit();
                }
            }
        }
    </script>
@endpush
