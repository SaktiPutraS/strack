@extends('layouts.app')
@section('title', 'Detail Pembayaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-receipt me-2"></i>Detail Pembayaran
                    </h1>
                    <p class="text-muted mb-0">{{ $payment->project->title }} - {{ $payment->formatted_amount }}</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('payments.edit', $payment) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Payment Details -->
        <div class="col-md-8">
            <!-- Payment Information -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-credit-card text-purple"></i>
                        </div>
                        Informasi Pembayaran
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Jumlah Pembayaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash text-purple me-2"></i>
                                <strong class="text-success fs-3">{{ $payment->formatted_amount }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Tipe Pembayaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tag text-purple me-2"></i>
                                @if ($payment->payment_type == 'DP')
                                    <span class="badge bg-purple-light text-purple border border-purple fs-6">DP (Down Payment)</span>
                                @elseif($payment->payment_type == 'INSTALLMENT')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning fs-6">CICILAN</span>
                                @elseif($payment->payment_type == 'FULL')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success fs-6">PEMBAYARAN PENUH</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success fs-6">PEMBAYARAN TERAKHIR</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Tanggal Pembayaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 text-purple me-2"></i>
                                <div>
                                    <strong>{{ $payment->payment_date->format('d M Y') }}</strong>
                                    <small class="text-muted ms-2">({{ $payment->payment_date->diffForHumans() }})</small>
                                </div>
                            </div>
                        </div>
                        @if ($payment->payment_method)
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-semibold">Metode Pembayaran</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-credit-card text-purple me-2"></i>
                                    <strong>{{ $payment->payment_method }}</strong>
                                </div>
                            </div>
                        @endif
                        @if ($payment->notes)
                            <div class="col-12">
                                <label class="form-label text-muted fw-semibold">Catatan</label>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-journal-text text-purple me-2 mt-1"></i>
                                    <p class="mb-0 text-dark">{{ $payment->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Project Information -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-folder2-open text-purple"></i>
                        </div>
                        Informasi Proyek
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Judul Proyek</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-folder text-purple me-2"></i>
                                <div>
                                    <strong class="text-dark">{{ $payment->project->title }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $payment->project->type }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Klien</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person text-purple me-2"></i>
                                <div>
                                    <strong class="text-dark">{{ $payment->project->client->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $payment->project->client->phone }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Status Proyek</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-flag text-purple me-2"></i>
                                @if ($payment->project->status == 'WAITING')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">MENUNGGU</span>
                                @elseif($payment->project->status == 'PROGRESS')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">PROGRESS</span>
                                @elseif($payment->project->status == 'FINISHED')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">SELESAI</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">DIBATALKAN</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Deadline</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 text-purple me-2"></i>
                                <div>
                                    <strong>{{ $payment->project->deadline->format('d M Y') }}</strong>
                                    @if ($payment->project->is_overdue)
                                        <span class="badge bg-danger ms-2">Terlambat</span>
                                    @elseif($payment->project->is_deadline_near)
                                        <span class="badge bg-warning ms-2">Deadline Dekat</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Progress -->
                    <div class="mt-4 p-4 bg-purple-light rounded">
                        <h6 class="text-purple mb-3 fw-bold">Progress Keuangan Proyek</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <small class="text-muted fw-semibold">Nilai Total:</small>
                                <div class="fw-bold text-primary">{{ $payment->project->formatted_total_value }}</div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted fw-semibold">Sudah Dibayar:</small>
                                <div class="fw-bold text-success">{{ $payment->project->formatted_paid_amount }}</div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted fw-semibold">Sisa Pembayaran:</small>
                                <div class="fw-bold text-warning">{{ $payment->project->formatted_remaining_amount }}</div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted fw-semibold">Progress:</small>
                                <div class="fw-bold text-purple">{{ $payment->project->progress_percentage }}%</div>
                            </div>
                        </div>
                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-purple" style="width: {{ $payment->project->progress_percentage }}%;" role="progressbar">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-lightning text-purple"></i>
                        </div>
                        Aksi Cepat
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('projects.show', $payment->project) }}" class="btn btn-primary">
                            <i class="bi bi-folder2-open me-2"></i>Lihat Proyek
                        </a>
                        <a href="{{ route('clients.show', $payment->project->client) }}" class="btn btn-outline-primary">
                            <i class="bi bi-person me-2"></i>Lihat Klien
                        </a>
                        <a href="{{ $payment->project->client->whatsapp_link }}" target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp me-2"></i>Chat Klien
                        </a>
                        @if ($payment->project->remaining_amount > 0)
                            <a href="{{ route('payments.create') }}?project={{ $payment->project->id }}" class="btn btn-outline-success">
                                <i class="bi bi-plus-circle me-2"></i>Pembayaran Lagi
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-info-circle text-purple"></i>
                        </div>
                        Ringkasan Pembayaran
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">ID Pembayaran:</span>
                                <strong>#{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Tanggal Input:</span>
                                <strong>{{ $payment->created_at->format('d M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Terakhir Update:</span>
                                <strong>{{ $payment->updated_at->format('d M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">% dari Total Proyek:</span>
                                <strong class="text-purple">
                                    {{ $payment->project->total_value > 0 ? number_format(($payment->amount / $payment->project->total_value) * 100, 1) : 0 }}%
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Payments -->
            @if ($payment->project->payments->count() > 1)
                <div class="card luxury-card border-0">
                    <div class="card-header bg-white border-0 p-4">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                                <i class="bi bi-clock-history text-purple"></i>
                            </div>
                            Pembayaran Lainnya
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group list-group-flush">
                            @foreach ($payment->project->payments->where('id', '!=', $payment->id)->take(3) as $otherPayment)
<div class="list-group-item px-0 py-3 border-bottom clickable-row" data-url="{{ route('payments.show', $otherPayment) }}" style="cursor: pointer;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold text-success">{{ $otherPayment->formatted_amount }}</div>
                                            <small class="text-muted">
                                                {{ $otherPayment->payment_date->format('d M Y') }}
                                                • {{ $otherPayment->payment_method ?? 'N/A' }}
                                            </small>
                                        </div>
                                        <div>
                                            @if ($otherPayment->payment_type == 'DP')
<span class="badge bg-purple-light text-purple">DP</span>
@elseif($otherPayment->payment_type == 'INSTALLMENT')
<span class="badge bg-warning">CICILAN</span>
@elseif($otherPayment->payment_type == 'FULL')
<span class="badge bg-success">LUNAS</span>
@else
<span class="badge bg-success">FINAL</span>
@endif
                                        </div>
                                    </div>
                                </div>
@endforeach
                        </div>

                        @if ($payment->project->payments->count() > 4)
<div class="text-center mt-3">
                                <a href="{{ route('projects.show', $payment->project) }}" class="btn btn-sm btn-outline-primary">
                                    Lihat Semua ({{ $payment->project->payments->count() }})
                                </a>
                            </div>
@endif
                    </div>
                </div>
@endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert messages
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

            // Clickable rows
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    if (url) {
                        window.location.href = url;
                    }
                });

                // Hover effects
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(139, 92, 246, 0.05)';
                    this.style.transition = 'all 0.2s ease';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });

            // Add animation to cards
            const cards = document.querySelectorAll('.luxury-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
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
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
                        }

                        .text-purple {
                            color: #8B5CF6 !important;
                        }

                        .bg-purple-light {
                            background-color: rgba(139, 92, 246, 0.05) !important;
                        }

                        .bg-purple {
                            background: linear-gradient(90deg, #8B5CF6, #A855F7) !important;
                        }

                        .progress-bar.bg-purple {
                            background: linear-gradient(90deg, #8B5CF6, #A855F7) !important;
                        }

                        .clickable-row {
                            cursor: pointer;
                            transition: all 0.2s ease;
                        }

                        @media (max-width: 768px) {
                            .luxury-card:hover {
                                transform: none !important;
                            }
                        }
                    </style>
@endpush)
