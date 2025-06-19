@extends('layouts.app')
@section('title', $project->title)

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-folder2-open"></i>{{ $project->title }}
                </h1>
                <div class="btn-group">
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Status & Progress -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-flag stat-icon"></i>
                    <div class="stat-value">
                        @if ($project->status == 'WAITING')
                            <span class="badge badge-warning fs-6">MENUNGGU</span>
                        @elseif($project->status == 'PROGRESS')
                            <span class="badge fs-6" style="background: var(--lilac-primary); color: white;">PROGRESS</span>
                        @elseif($project->status == 'FINISHED')
                            <span class="badge badge-success fs-6">SELESAI</span>
                        @else
                            <span class="badge badge-danger fs-6">DIBATALKAN</span>
                        @endif
                    </div>
                    <div class="stat-label">Status Proyek</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-graph-up stat-icon"></i>
                    <div class="stat-value">{{ $project->progress_percentage }}%</div>
                    <div class="stat-label">Progress Pembayaran</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-wallet2 stat-icon"></i>
                    <div class="stat-value">{{ $project->formatted_paid_amount }}</div>
                    <div class="stat-label">Sudah Dibayar</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-clock-history stat-icon"></i>
                    <div class="stat-value">{{ $project->formatted_remaining_amount }}</div>
                    <div class="stat-label">Sisa Pembayaran</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Project Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-info-circle"></i>Detail Proyek
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Klien</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person text-lilac me-2"></i>
                                <strong>{{ $project->client->name }}</strong>
                                <a href="{{ $project->client->whatsapp_link }}" target="_blank" class="btn btn-sm btn-success ms-2">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tipe Proyek</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tag text-lilac me-2"></i>
                                <span class="badge bg-secondary">{{ $project->type }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Deadline</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 text-lilac me-2"></i>
                                <strong>{{ $project->deadline->format('d M Y') }}</strong>
                                @if ($project->is_overdue)
                                    <span class="badge badge-danger ms-2">Terlambat</span>
                                @elseif($project->is_deadline_near)
                                    <span class="badge badge-warning ms-2">Deadline Dekat</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Nilai Proyek</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-currency-dollar text-lilac me-2"></i>
                                <strong>{{ $project->formatted_total_value }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Deskripsi</label>
                            <p class="mb-0">{{ $project->description }}</p>
                        </div>
                        @if ($project->notes)
                            <div class="col-12">
                                <label class="form-label text-muted">Catatan</label>
                                <p class="mb-0 text-muted">{{ $project->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <label class="form-label text-muted">Progress Pembayaran</label>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar" style="width: {{ $project->progress_percentage }}%; background: var(--lilac-primary);"
                                role="progressbar" aria-valuenow="{{ $project->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $project->progress_percentage }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-credit-card"></i>Riwayat Pembayaran
                        </h5>
                        @if ($project->status !== 'FINISHED')
                            <a href="{{ route('payments.create') }}?project={{ $project->id }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Pembayaran
                            </a>
                        @endif
                    </div>

                    @if (isset($paymentHistory) && $paymentHistory->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($paymentHistory as $payment)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="bi bi-credit-card text-success me-2"></i>
                                                <strong>{{ $payment->formatted_amount }}</strong>
                                                @if ($payment->payment_type == 'DP')
                                                    <span class="badge" style="background: var(--lilac-secondary); color: white;"
                                                        class="ms-2">DP</span>
                                                @elseif($payment->payment_type == 'INSTALLMENT')
                                                    <span class="badge badge-warning ms-2">CICILAN</span>
                                                @elseif($payment->payment_type == 'FULL')
                                                    <span class="badge badge-success ms-2">LUNAS</span>
                                                @else
                                                    <span class="badge badge-success ms-2">FINAL</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>{{ $payment->payment_date->format('d M Y') }}
                                                @if ($payment->payment_method)
                                                    • {{ $payment->payment_method }}
                                                @endif
                                            </small>
                                            @if ($payment->notes)
                                                <p class="text-muted mt-1 mb-0 small">{{ $payment->notes }}</p>
                                            @endif
                                        </div>
                                        <small class="text-success">
                                            <i class="bi bi-piggy-bank me-1"></i>{{ $payment->formatted_saving_amount }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-credit-card text-lilac-secondary" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Belum ada pembayaran</p>
                        </div>
                    @endif
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
                        @if ($project->status !== 'FINISHED')
                            <button class="btn btn-success" onclick="updateStatus('FINISHED')">
                                <i class="bi bi-check-circle me-2"></i>Tandai Selesai
                            </button>
                        @endif

                        @if ($project->status === 'WAITING')
                            <button class="btn btn-primary" onclick="updateStatus('PROGRESS')">
                                <i class="bi bi-play-circle me-2"></i>Mulai Proyek
                            </button>
                        @endif

                        @if ($project->status === 'FINISHED' && !$project->has_testimonial)
                            <a href="{{ route('testimonials.create') }}?project={{ $project->id }}" class="btn btn-warning">
                                <i class="bi bi-star me-2"></i>Tambah Testimoni
                            </a>
                        @endif

                        <a href="{{ $project->client->whatsapp_link }}" target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp me-2"></i>Chat Client
                        </a>
                    </div>
                </div>
            </div>


            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-star"></i>Status Testimoni
                    </h6>

                    @if ($project->has_testimonial)
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            Proyek ini sudah ada testimoni
                        </div>
                        @if ($project->status === 'FINISHED')
                            <button class="btn btn-outline-warning btn-sm" onclick="toggleTestimonial({{ $project->id }})">
                                <i class="bi bi-x-circle me-1"></i>Tandai Belum Ada
                            </button>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Proyek ini belum ada testimoni
                        </div>
                        @if ($project->status === 'FINISHED')
                            <button class="btn btn-success btn-sm" onclick="toggleTestimonial({{ $project->id }})">
                                <i class="bi bi-check-circle me-1"></i>Tandai Sudah Ada
                            </button>
                        @endif
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateStatus(newStatus) {
            if (confirm(`Apakah Anda yakin ingin mengubah status menjadi ${newStatus}?`)) {
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
                            location.reload();
                        } else {
                            alert('Gagal mengubah status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan');
                    });
            }
        }
    </script>
@endpush
