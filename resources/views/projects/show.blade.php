@extends('layouts.app')
@section('title', $project->title)

@section('content')
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-folder2-open me-2"></i>{{ $project->title }}
                    </h1>
                    <p class="text-muted mb-0">Detail lengkap proyek untuk {{ $project->client->name }}</p>
                </div>
                <div>
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Proyek
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Status & Progress Overview -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Status & Progress
            </h5>
        </div>
        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card stat-card-purple h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-flag text-purple fs-4"></i>
                    </div>
                    <div class="fw-bold mb-2">
                        @if ($project->status == 'WAITING')
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                <i class="bi bi-clock me-1"></i>MENUNGGU
                            </span>
                        @elseif($project->status == 'PROGRESS')
                            <span class="badge bg-purple-light text-purple border border-purple">
                                <i class="bi bi-play-fill me-1"></i>PROGRESS
                            </span>
                        @elseif($project->status == 'FINISHED')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                <i class="bi bi-check-circle-fill me-1"></i>SELESAI
                            </span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                <i class="bi bi-x-circle-fill me-1"></i>DIBATALKAN
                            </span>
                        @endif
                    </div>
                    <small class="text-muted fw-medium">Status Proyek</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card stat-card-info h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-wallet2 text-info fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-info mb-1">{{ $project->formatted_paid_amount }}</h5>
                    <small class="text-muted fw-medium">Terbayar</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clock-history text-success fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-1">{{ $project->formatted_remaining_amount }}</h5>
                    <small class="text-muted fw-medium">Sisa</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card luxury-card stat-card stat-card-{{ $project->testimoni_color }} h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-{{ $project->testimoni_icon }} text-{{ $project->testimoni_color }} fs-4"></i>
                    </div>
                    <div class="fw-bold mb-2">
                        @if ($project->testimoni)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                <i class="bi bi-check-circle-fill me-1"></i>SUDAH
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                <i class="bi bi-clock-history me-1"></i>BELUM
                            </span>
                        @endif
                    </div>
                    <small class="text-muted fw-medium">Testimoni</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Project Details -->
        <div class="col-lg-8">
            <!-- Main Project Information -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-info-circle text-purple"></i>
                        </div>
                        Detail Proyek
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Client Information -->
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Klien</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person text-purple"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $project->client->name }}</h6>
                                    <small class="text-muted">{{ $project->client->phone }}</small>
                                </div>
                                <a href="{{ $project->client->whatsapp_link }}" target="_blank" class="btn btn-success btn-sm">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Project Type -->
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Tipe Proyek</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-tag text-secondary"></i>
                                </div>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2">
                                    {{ $project->type }}
                                </span>
                            </div>
                        </div>

                        <!-- Deadline -->
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Deadline</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-calendar3 text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $project->deadline->format('d M Y') }}</h6>
                                    @if ($project->is_overdue)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Terlambat
                                        </span>
                                    @elseif($project->is_deadline_near)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                            <i class="bi bi-clock me-1"></i>Deadline Dekat
                                        </span>
                                    @else
                                        <small class="text-success">
                                            <i class="bi bi-check-circle me-1"></i>On Track
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Project Value -->
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Nilai Proyek</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-currency-dollar text-success"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-success">{{ $project->formatted_total_value }}</h6>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold">Deskripsi Proyek</label>
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-0">{{ $project->description }}</p>
                            </div>
                        </div>

                        @if ($project->notes)
                            <!-- Notes -->
                            <div class="col-12">
                                <label class="form-label text-muted fw-semibold">Catatan Khusus</label>
                                <div class="p-3 bg-warning bg-opacity-10 rounded-3 border-start border-warning border-3">
                                    <p class="mb-0 text-muted">{{ $project->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Payment History -->
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark d-flex align-items-center">
                                <div class="luxury-icon me-3">
                                    <i class="bi bi-credit-card text-success"></i>
                                </div>
                                Riwayat Pembayaran
                            </h5>
                            <p class="text-muted mb-0">{{ isset($paymentHistory) ? $paymentHistory->count() : 0 }} transaksi pembayaran</p>
                        </div>
                        @if ($project->status !== 'FINISHED')
                            <a href="{{ route('payments.create') }}?project={{ $project->id }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Pembayaran
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body p-0">
                    @if (isset($paymentHistory) && $paymentHistory->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($paymentHistory as $payment)
                                <div class="list-group-item border-0 p-4">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex">
                                            <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                                <i class="bi bi-credit-card text-success"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <h6 class="mb-0 fw-bold me-3">{{ $payment->formatted_amount }}</h6>
                                                    @if ($payment->payment_type == 'DP')
                                                        <span class="badge bg-purple-light text-purple border border-purple">
                                                            <i class="bi bi-wallet me-1"></i>DP
                                                        </span>
                                                    @elseif($payment->payment_type == 'INSTALLMENT')
                                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                                            <i class="bi bi-credit-card me-1"></i>CICILAN
                                                        </span>
                                                    @elseif($payment->payment_type == 'FULL')
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                            <i class="bi bi-check-circle me-1"></i>LUNAS
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                            <i class="bi bi-check-circle-fill me-1"></i>FINAL
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>{{ $payment->payment_date->format('d M Y') }}
                                                    @if ($payment->payment_method)
                                                        • <i class="bi bi-credit-card me-1"></i>{{ $payment->payment_method }}
                                                    @endif
                                                </div>
                                                @if ($payment->notes)
                                                    <p class="text-muted mt-2 mb-0 small bg-light p-2 rounded">
                                                        <i class="bi bi-chat-left-text me-1"></i>{{ $payment->notes }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $payment->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-credit-card text-muted" style="font-size: 2rem;"></i>
                            </div>
                            <h6 class="text-muted mb-2">Belum ada pembayaran</h6>
                            <p class="text-muted mb-4">Tambahkan pembayaran pertama untuk proyek ini</p>
                            <a href="{{ route('payments.create') }}?project={{ $project->id }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Pembayaran
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-lightning text-warning"></i>
                        </div>
                        Aksi Cepat
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        @if ($project->status !== 'FINISHED' && $project->status !== 'CANCELED' && $project->status !== 'WAITING')
                            <button class="btn btn-success d-flex align-items-center justify-content-center" onclick="updateStatus('FINISHED')">
                                <i class="bi bi-check-circle me-2"></i>Tandai Selesai
                            </button>
                        @endif

                        @if ($project->status === 'WAITING')
                            <button class="btn btn-primary d-flex align-items-center justify-content-center" onclick="updateStatus('PROGRESS')">
                                <i class="bi bi-play-circle me-2"></i>Mulai Proyek
                            </button>
                        @endif

                        @if ($project->status === 'FINISHED')
                            <button
                                class="btn btn-{{ $project->testimoni ? 'outline-primary' : 'primary' }} d-flex align-items-center justify-content-center"
                                onclick="updateTestimoni({{ $project->testimoni ? 'false' : 'true' }})">
                                <i class="bi bi-{{ $project->testimoni ? 'x-circle' : 'check-circle' }} me-2"></i>
                                {{ $project->testimoni ? 'Batalkan Testimoni' : 'Tandai Ada Testimoni' }}
                            </button>
                        @endif

                        @if ($project->type === 'BTOOLS')
                            <a href="{{ route('projects.print-invoice', $project) }}" target="_blank"
                                class="btn btn-info d-flex align-items-center justify-content-center">
                                <i class="bi bi-printer me-2"></i>Print Invoice
                            </a>
                        @endif

                        <a href="{{ $project->client->whatsapp_link }}" target="_blank"
                            class="btn btn-success d-flex align-items-center justify-content-center">
                            <i class="bi bi-whatsapp me-2"></i>Chat Client
                        </a>

                        <a href="{{ route('payments.create') }}?project={{ $project->id }}"
                            class="btn btn-outline-primary d-flex align-items-center justify-content-center">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Pembayaran
                        </a>

                        <a href="{{ route('projects.edit', $project) }}"
                            class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                            <i class="bi bi-pencil me-2"></i>Edit Proyek
                        </a>
                    </div>
                </div>
            </div>

            <!-- Project Timeline -->
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-clock-history text-info"></i>
                        </div>
                        Timeline Proyek
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-purple"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Proyek Dibuat</h6>
                                <small class="text-muted">{{ $project->created_at->format('d M Y H:i') }}</small>
                            </div>
                        </div>

                        @if (isset($paymentHistory) && $paymentHistory->count() > 0)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Pembayaran Pertama</h6>
                                    <small class="text-muted">{{ $paymentHistory->last()->payment_date->format('d M Y') }}</small>
                                </div>
                            </div>
                        @endif

                        @if ($project->status === 'PROGRESS')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Dalam Progress</h6>
                                    <small class="text-muted">Sedang dikerjakan</small>
                                </div>
                            </div>
                        @endif

                        @if ($project->testimoni)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Testimoni Dibuat</h6>
                                    <small class="text-muted">Sudah ada testimoni</small>
                                </div>
                            </div>
                        @endif

                        <div class="timeline-item">
                            <div
                                class="timeline-marker {{ $project->is_overdue ? 'bg-danger' : ($project->is_deadline_near ? 'bg-warning' : 'bg-info') }}">
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Target Selesai</h6>
                                <small class="text-muted">{{ $project->deadline->format('d M Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateStatus(newStatus) {
            const statusLabels = {
                'PROGRESS': 'PROGRESS',
                'FINISHED': 'SELESAI'
            };

            Swal.fire({
                title: 'Konfirmasi Perubahan Status',
                text: `Apakah Anda yakin ingin mengubah status menjadi ${statusLabels[newStatus]}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8B5CF6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const button = event.target;
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
                    button.disabled = true;

                    fetch(`{{ route('projects.status', $project) }}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#8B5CF6'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal mengubah status: ' + (data.message || 'Terjadi kesalahan'),
                                    icon: 'error',
                                    confirmButtonColor: '#8B5CF6'
                                });
                                button.innerHTML = originalText;
                                button.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan koneksi',
                                icon: 'error',
                                confirmButtonColor: '#8B5CF6'
                            });
                            button.innerHTML = originalText;
                            button.disabled = false;
                        });
                }
            });
        }

        function updateTestimoni(newTestimoni) {
            const testimoniLabels = {
                true: 'sudah dibuat',
                false: 'belum dibuat'
            };

            Swal.fire({
                title: 'Konfirmasi Perubahan Testimoni',
                text: `Apakah Anda yakin ingin mengubah status testimoni menjadi ${testimoniLabels[newTestimoni]}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8B5CF6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const button = event.target;
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
                    button.disabled = true;

                    fetch(`{{ route('projects.testimoni', $project) }}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                testimoni: newTestimoni
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#8B5CF6'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal mengubah status testimoni: ' + (data.message || 'Terjadi kesalahan'),
                                    icon: 'error',
                                    confirmButtonColor: '#8B5CF6'
                                });
                                button.innerHTML = originalText;
                                button.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan koneksi',
                                icon: 'error',
                                confirmButtonColor: '#8B5CF6'
                            });
                            button.innerHTML = originalText;
                            button.disabled = false;
                        });
                }
            });
        }
    </script>

    <style>
        /* Custom col-md-2-4 for 5 columns on medium screens */
        @media (min-width: 768px) {
            .col-md-2-4 {
                flex: 0 0 auto;
                width: 20%;
            }
        }

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

        .text-purple {
            color: #8B5CF6 !important;
        }

        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.1) !important;
        }

        .border-purple {
            border-color: rgba(139, 92, 246, 0.3) !important;
        }

        /* Timeline styling */
        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(139, 92, 246, 0.2);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 1px rgba(139, 92, 246, 0.2);
        }

        .timeline-content h6 {
            color: #374151;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .list-group-item {
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            background-color: rgba(139, 92, 246, 0.02);
        }

        @media (max-width: 768px) {
            .timeline {
                padding-left: 1.5rem;
            }

            .timeline-marker {
                left: -18px;
            }
        }
    </style>
@endpush
