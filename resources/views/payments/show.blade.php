@extends('layouts.app')
@section('title', 'Detail Pembayaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-receipt"></i>Detail Pembayaran
                </h1>
                <div class="btn-group">
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
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-credit-card"></i>Informasi Pembayaran
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Jumlah Pembayaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash text-lilac me-2"></i>
                                <strong class="text-success fs-4">{{ $payment->formatted_amount }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tipe Pembayaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tag text-lilac me-2"></i>
                                @if ($payment->payment_type == 'DP')
                                    <span class="badge" style="background: var(--lilac-secondary); color: white;">DP (Down Payment)</span>
                                @elseif($payment->payment_type == 'INSTALLMENT')
                                    <span class="badge bg-warning">CICILAN</span>
                                @elseif($payment->payment_type == 'FULL')
                                    <span class="badge bg-success">PEMBAYARAN PENUH</span>
                                @else
                                    <span class="badge bg-success">PEMBAYARAN TERAKHIR</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tanggal Pembayaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 text-lilac me-2"></i>
                                <strong>{{ $payment->payment_date->format('d M Y') }}</strong>
                                <small class="text-muted ms-2">({{ $payment->payment_date->diffForHumans() }})</small>
                            </div>
                        </div>
                        @if ($payment->payment_method)
                            <div class="col-md-6">
                                <label class="form-label text-muted">Metode Pembayaran</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-credit-card text-lilac me-2"></i>
                                    <strong>{{ $payment->payment_method }}</strong>
                                </div>
                            </div>
                        @endif
                        @if ($payment->notes)
                            <div class="col-12">
                                <label class="form-label text-muted">Catatan</label>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-journal-text text-lilac me-2 mt-1"></i>
                                    <p class="mb-0">{{ $payment->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Project Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-folder2-open"></i>Informasi Proyek
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Judul Proyek</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-folder text-lilac me-2"></i>
                                <div>
                                    <strong>{{ $payment->project->title }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $payment->project->type }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Klien</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person text-lilac me-2"></i>
                                <div>
                                    <strong>{{ $payment->project->client->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $payment->project->client->phone }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status Proyek</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-flag text-lilac me-2"></i>
                                @if ($payment->project->status == 'WAITING')
                                    <span class="badge bg-warning">MENUNGGU</span>
                                @elseif($payment->project->status == 'PROGRESS')
                                    <span class="badge bg-primary">PROGRESS</span>
                                @elseif($payment->project->status == 'FINISHED')
                                    <span class="badge bg-success">SELESAI</span>
                                @else
                                    <span class="badge bg-danger">DIBATALKAN</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Deadline</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 text-lilac me-2"></i>
                                <strong>{{ $payment->project->deadline->format('d M Y') }}</strong>
                                @if ($payment->project->is_overdue)
                                    <span class="badge bg-danger ms-2">Terlambat</span>
                                @elseif($payment->project->is_deadline_near)
                                    <span class="badge bg-warning ms-2">Deadline Dekat</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Financial Progress -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-muted mb-3">Progress Keuangan Proyek</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <small class="text-muted">Nilai Total:</small>
                                <div class="fw-bold text-primary">{{ $payment->project->formatted_total_value }}</div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Sudah Dibayar:</small>
                                <div class="fw-bold text-success">{{ $payment->project->formatted_paid_amount }}</div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Sisa Pembayaran:</small>
                                <div class="fw-bold text-warning">{{ $payment->project->formatted_remaining_amount }}</div>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Progress:</small>
                                <div class="fw-bold text-info">{{ $payment->project->progress_percentage }}%</div>
                            </div>
                        </div>
                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar" style="width: {{ $payment->project->progress_percentage }}%; background: var(--lilac-primary);"
                                    role="progressbar">
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
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-lightning"></i>Aksi Cepat
                    </h6>

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
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-info-circle"></i>Ringkasan Pembayaran
                    </h6>

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
                                <strong>
                                    {{ $payment->project->total_value > 0 ? number_format(($payment->amount / $payment->project->total_value) * 100, 1) : 0 }}%
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Payments -->
            @if ($payment->project->payments->count() > 1)
                <div class="card">
                    <div class="card-body">
                        <h6 class="section-title">
                            <i class="bi bi-clock-history"></i>Pembayaran Lainnya
                        </h6>

                        <div class="list-group list-group-flush">
                            @foreach ($payment->project->payments->where('id', '!=', $payment->id)->take(3) as $otherPayment)
<div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold text-success">{{ $otherPayment->formatted_amount }}</div>
                                        <small class="text-muted">
                                            {{ $otherPayment->payment_date->format('d M Y') }}
                                            • {{ $otherPayment->payment_type }}
                                        </small>
                                    </div>
                                    <div>
                                        @if ($otherPayment->payment_type == 'DP')
<span class="badge badge-sm" style="background: var(--lilac-secondary); color: white;">DP</span>
@elseif($otherPayment->payment_type == 'INSTALLMENT')
<span class="badge badge-sm bg-warning">CICILAN</span>
@elseif($otherPayment->payment_type == 'FULL')
<span class="badge badge-sm bg-success">LUNAS</span>
@else
<span class="badge badge-sm bg-success">FINAL</span>
@endif
                                    </div>
                                </div>
                            </div>
@endforeach
                    </div>

                    @if ($payment->project->payments->count() > 4)
<div class="text-center mt-2">
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
        // Add any JavaScript functionality if needed
        document.addEventListener('DOMContentLoaded', function() {
            // You can add interactive features here
            console.log('Payment detail page loaded');
        });
    </script>
@endpush)
