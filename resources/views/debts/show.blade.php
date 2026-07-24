@extends('layouts.app')
@section('title', 'Detail Catatan')

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h1 class="h3 fw-bold text-purple mb-1">
                        <span class="badge bg-{{ $debt->type_color }} me-2">{{ strtoupper($debt->type_label) }}</span>
                        {{ $debt->party_name }}
                    </h1>
                    <p class="text-muted mb-0">{{ $debt->title ?: '-' }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('debts.edit', $debt) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
                <button type="button" class="btn btn-outline-danger btn-hapus"
                    data-action="{{ route('debts.destroy', $debt) }}" data-name="{{ $debt->party_name }}">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Ringkasan + tambah pembayaran -->
        <div class="col-lg-5">
            <div class="card luxury-card border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nilai Total</span>
                        <span class="fw-bold">{{ $debt->formatted_principal }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Sudah Terbayar</span>
                        <span class="fw-bold text-success">{{ $debt->formatted_paid }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Sisa</span>
                        <span class="fw-bold fs-5">{{ $debt->formatted_remaining }}</span>
                    </div>
                    <div class="progress mb-2" style="height:10px;">
                        <div class="progress-bar bg-{{ $debt->type_color }}" style="width: {{ $debt->progress_percentage }}%;"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">{{ $debt->progress_percentage }}% terbayar</small>
                        <span class="badge bg-{{ $debt->status_color }}">{{ $debt->status_label }}</span>
                    </div>

                    @if ($debt->due_date)
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-calendar-event me-1"></i>Jatuh Tempo</span>
                            <span>
                                {{ $debt->due_date->format('d M Y') }}
                                @if ($debt->is_overdue)
                                    <span class="badge bg-danger ms-1">Lewat tempo</span>
                                @elseif ($debt->is_due_soon)
                                    <span class="badge bg-warning ms-1">Dekat tempo</span>
                                @endif
                            </span>
                        </div>
                    @endif

                    @if ($debt->notes)
                        <hr>
                        <div class="text-muted small">{{ $debt->notes }}</div>
                    @endif
                </div>
            </div>

            @if ($debt->status !== 'PAID')
                <div class="card luxury-card border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-purple"></i>Tambah Pembayaran</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('debts.payments.store', $debt) }}" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nominal <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="amount" min="1" step="1" max="{{ (int) $debt->remaining_amount }}"
                                        class="form-control @error('amount') is-invalid @enderror"
                                        value="{{ old('amount') }}" placeholder="0" required>
                                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-text">Sisa saat ini: {{ $debt->formatted_remaining }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tanggal Bayar <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control form-control-lg @error('payment_date') is-invalid @enderror"
                                    value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan</label>
                                <input type="text" name="notes" class="form-control @error('notes') is-invalid @enderror"
                                    value="{{ old('notes') }}" placeholder="mis. cicilan ke-1 (opsional)">
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 d-grid">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-circle me-2"></i>Catat Pembayaran</button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Catatan ini sudah <strong>LUNAS</strong>.</div>
            @endif
        </div>

        <!-- Riwayat pembayaran -->
        <div class="col-lg-7">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-purple"></i>Riwayat Pembayaran
                        <span class="badge bg-light text-dark ms-1">{{ $debt->payments->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="py-3 text-end">Nominal</th>
                                    <th class="py-3">Catatan</th>
                                    <th class="py-3 text-end px-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($debt->payments as $p)
                                    <tr>
                                        <td class="px-4">{{ $p->payment_date->format('d M Y') }}</td>
                                        <td class="text-end fw-semibold text-success">{{ $p->formatted_amount }}</td>
                                        <td class="text-muted">{{ $p->notes ?: '-' }}</td>
                                        <td class="text-end px-4">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                                data-action="{{ route('debt-payments.destroy', $p) }}"
                                                data-name="pembayaran {{ $p->formatted_amount }}" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-5">Belum ada pembayaran tercatat.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-hapus').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const action = this.dataset.action;
                const name = this.dataset.name;
                Swal.fire({
                    title: 'Hapus?',
                    text: `Hapus ${name}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('deleteForm');
                        form.action = action;
                        form.submit();
                    }
                });
            });
        });

        @if ($errors->has('amount'))
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.querySelector('[name=amount]');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        @endif
    </script>
@endpush
