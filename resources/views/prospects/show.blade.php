@extends('layouts.app')
@section('title', 'Detail Prospek: ' . $prospect->name)

@section('content')
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-person-badge me-2"></i>Detail Prospek
                    </h1>
                    <p class="text-muted mb-0">Informasi lengkap prospek {{ $prospect->name }}</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('prospects.edit', $prospect) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('prospects.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- Basic Info Card -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-person-circle text-purple"></i>
                        </div>
                        Informasi Prospek
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold text-uppercase mb-2">Nama Lengkap</label>
                            <h5 class="fw-bold mb-0">{{ $prospect->name }}</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold text-uppercase mb-2">Status</label>
                            <div>
                                @php
                                    $colorMap = [
                                        'secondary' => 'secondary',
                                        'info' => 'info',
                                        'warning' => 'warning',
                                        'purple' => 'purple',
                                        'danger' => 'danger',
                                    ];
                                    $color = $colorMap[$prospect->status_color] ?? 'secondary';
                                @endphp
                                <span
                                    class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} px-3 py-2">
                                    {{ $prospect->status_label }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="my-2">
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold text-uppercase mb-2">
                                <i class="bi bi-whatsapp me-1"></i>WhatsApp
                            </label>
                            <div>
                                <a href="{{ $prospect->whatsapp_link }}" target="_blank" class="btn btn-success">
                                    <i class="bi bi-whatsapp me-2"></i>{{ $prospect->whatsapp }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-semibold text-uppercase mb-2">
                                <i class="bi bi-link-45deg me-1"></i>Sosial Media
                            </label>
                            <div>
                                @if ($prospect->social_media)
                                    <a href="{{ $prospect->social_media }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="bi bi-box-arrow-up-right me-2"></i>Buka Link
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="my-2">
                        </div>
                        <div class="col-12">
                            <label class="text-muted small fw-semibold text-uppercase mb-2">
                                <i class="bi bi-geo-alt me-1"></i>Alamat
                            </label>
                            <p class="mb-0">{{ $prospect->address ?? 'Tidak ada alamat' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Card -->
            @if ($prospect->notes)
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-journal-text text-warning"></i>
                            </div>
                            Keterangan & Catatan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $prospect->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Timeline Card -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-clock-history text-info"></i>
                        </div>
                        Timeline
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Ditambahkan</small>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-plus text-success me-2"></i>
                            <span class="fw-semibold">{{ $prospect->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">Terakhir Diupdate</small>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-check text-primary me-2"></i>
                            <span class="fw-semibold">{{ $prospect->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-lightning-charge text-warning"></i>
                        </div>
                        Aksi Cepat
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ $prospect->whatsapp_link }}" target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp me-2"></i>Hubungi via WhatsApp
                        </a>
                        @if ($prospect->social_media)
                            <a href="{{ $prospect->social_media }}" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Lihat Sosial Media
                            </a>
                        @endif
                        <a href="{{ route('prospects.edit', $prospect) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Edit Prospek
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash me-2"></i>Hapus Prospek
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="delete-form" action="{{ route('prospects.destroy', $prospect) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
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

        .bg-purple {
            background-color: #8B5CF6 !important;
        }

        .border-purple {
            border-color: rgba(139, 92, 246, 0.3) !important;
        }

        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.1) !important;
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
