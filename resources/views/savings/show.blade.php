@extends('layouts.app')
@section('title', 'Detail Tabungan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-piggy-bank"></i>Detail Tabungan
                </h1>
                <div class="btn-group">
                    <a href="{{ route('savings.edit', $saving) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('savings.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Saving Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-info-circle"></i>Informasi Tabungan
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Jumlah Tabungan</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-piggy-bank text-lilac me-2"></i>
                                <strong class="text-success fs-4">{{ $saving->formatted_amount }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $saving->status_icon }} text-{{ $saving->status_color }} me-2"></i>
                                @if ($saving->status === 'PENDING')
                                    <span class="badge bg-warning">Pending Transfer</span>
                                @else
                                    <span class="badge bg-success">Transferred</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tanggal Transaksi</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 text-lilac me-2"></i>
                                <strong>{{ $saving->transaction_date->format('d M Y') }}</strong>
                            </div>
                        </div>
                        @if ($saving->status === 'TRANSFERRED')
                            <div class="col-md-6">
                                <label class="form-label text-muted">Tanggal Transfer</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-bank text-lilac me-2"></i>
                                    <strong>{{ $saving->transfer_date->format('d M Y') }}</strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Metode Transfer</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-credit-card text-lilac me-2"></i>
                                    <strong>{{ $saving->transfer_method }}</strong>
                                </div>
                            </div>
                            @if ($saving->transfer_reference)
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Referensi Transfer</label>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-hash text-lilac me-2"></i>
                                        <strong>{{ $saving->transfer_reference }}</strong>
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if ($saving->notes)
                            <div class="col-12">
                                <label class="form-label text-muted">Catatan</label>
                                <p class="mb-0">{{ $saving->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-credit-card"></i>Detail Pembayaran Terkait
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Proyek</label>
                            <div>
                                <strong class="text-lilac">{{ $saving->payment->project->title }}</strong>
                                <br><small class="text-muted">{{ $saving->payment->project->type }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Klien</label>
                            <div class="d-flex align-items-center">
                                <strong>{{ $saving->payment->project->client->name }}</strong>
                                <a href="{{ $saving->payment->project->client->whatsapp_link }}" target="_blank" class="btn btn-sm btn-success ms-2">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Jumlah Pembayaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-currency-dollar text-lilac me-2"></i>
                                <strong>{{ $saving->payment->formatted_amount }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tipe Pembayaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $saving->payment->type_icon }} text-{{ $saving->payment->type_color }} me-2"></i>
                                <span class="badge bg-{{ $saving->payment->type_color }}">{{ $saving->payment->payment_type }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tanggal Pembayaran</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 text-lilac me-2"></i>
                                <strong>{{ $saving->payment->payment_date->format('d M Y') }}</strong>
                            </div>
                        </div>
                        @if ($saving->payment->payment_method)
                            <div class="col-md-6">
                                <label class="form-label text-muted">Metode Pembayaran</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-credit-card text-lilac me-2"></i>
                                    <strong>{{ $saving->payment->payment_method }}</strong>
                                </div>
                            </div>
                        @endif
                        @if ($saving->payment->notes)
                            <div class="col-12">
                                <label class="form-label text-muted">Catatan Pembayaran</label>
                                <p class="mb-0 text-muted">{{ $saving->payment->notes }}</p>
                            </div>
                        @endif
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
                        @if ($saving->status === 'PENDING')
                            <button class="btn btn-warning" onclick="transferSingle()">
                                <i class="bi bi-bank me-2"></i>Transfer ke Bank
                            </button>
                        @endif

                        <a href="{{ route('projects.show', $saving->payment->project) }}" class="btn btn-primary">
                            <i class="bi bi-folder2-open me-2"></i>Lihat Proyek
                        </a>

                        <a href="{{ route('payments.edit', $saving->payment) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil me-2"></i>Edit Pembayaran
                        </a>

                        <a href="{{ $saving->payment->project->client->whatsapp_link }}" target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp me-2"></i>Chat Client
                        </a>
                    </div>
                </div>
            </div>

            <!-- Calculation Info -->
            <div class="card">
                <div class="card-body">
                    <h6 class="section-title">
                        <i class="bi bi-calculator"></i>Perhitungan Tabungan
                    </h6>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Pembayaran:</span>
                            <strong>{{ $saving->payment->formatted_amount }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Persentase:</span>
                            <strong>10%</strong>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Tabungan:</span>
                            <strong class="text-success">{{ $saving->formatted_amount }}</strong>
                        </div>
                    </div>

                    @php
                        $expectedAmount = $saving->payment->amount * 0.1;
                        $actualAmount = $saving->amount;
                        $difference = $actualAmount - $expectedAmount;
                    @endphp

                    @if (abs($difference) > 1)
                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle me-1"></i>
                            @if ($difference > 0)
                                Lebih Rp {{ number_format($difference, 0, ',', '.') }} dari perhitungan otomatis
                            @else
                                Kurang Rp {{ number_format(abs($difference), 0, ',', '.') }} dari perhitungan otomatis
                            @endif
                        </div>
                    @else
                        <div class="alert alert-success small">
                            <i class="bi bi-check-circle me-1"></i>
                            Sesuai perhitungan otomatis (10%)
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function transferSingle() {
            if (confirm('Transfer tabungan ini ke bank?\n\nAnda akan diarahkan ke halaman transfer.')) {
                // Redirect to main savings page with this saving pre-selected
                window.location.href = '{{ route('savings.index') }}?transfer={{ $saving->id }}';
            }
        }
    </script>
@endpush
