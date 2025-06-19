@extends('layouts.app')
@section('title', 'Edit Tabungan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-pencil-square"></i>Edit Tabungan
                </h1>
                <div class="btn-group">
                    <a href="{{ route('savings.show', $saving) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Lihat
                    </a>
                    <a href="{{ route('savings.index') }}" class="btn btn-outline-secondary">
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
                    <form action="{{ route('savings.update', $saving) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Saving Info -->
                        <div class="p-3 bg-lilac-soft rounded mb-4">
                            <h6 class="text-lilac mb-3">Informasi Tabungan</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <small class="text-muted">Proyek:</small>
                                    <div class="fw-bold">{{ $saving->payment->project->title }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Klien:</small>
                                    <div class="fw-bold">{{ $saving->payment->project->client->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Payment Type:</small>
                                    <div class="fw-bold">{{ $saving->payment->payment_type }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Payment Amount:</small>
                                    <div class="fw-bold text-success">{{ $saving->payment->formatted_amount }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Jumlah Tabungan -->
                            <div class="col-md-6">
                                <label for="amount" class="form-label">
                                    <i class="bi bi-piggy-bank text-lilac me-2"></i>
                                    Jumlah Tabungan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount"
                                        value="{{ old('amount', $saving->amount) }}" min="0" step="1000" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tanggal Transaksi -->
                            <div class="col-md-6">
                                <label for="transaction_date" class="form-label">
                                    <i class="bi bi-calendar3 text-lilac me-2"></i>
                                    Tanggal Transaksi <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" id="transaction_date"
                                    name="transaction_date" value="{{ old('transaction_date', $saving->transaction_date->format('Y-m-d')) }}" required>
                                @error('transaction_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status (Read Only) -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-flag text-lilac me-2"></i>
                                    Status
                                </label>
                                <input type="text" class="form-control"
                                    value="{{ $saving->status === 'PENDING' ? 'Pending Transfer' : 'Transferred' }}" readonly>
                                <div class="form-text">Status tidak dapat diubah secara manual</div>
                            </div>

                            <!-- Transfer Info (if transferred) -->
                            @if ($saving->status === 'TRANSFERRED')
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-bank text-lilac me-2"></i>
                                        Transfer Info
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $saving->transfer_date->format('d M Y') }}</span>
                                        <input type="text" class="form-control" value="{{ $saving->transfer_method }}" readonly>
                                    </div>
                                    @if ($saving->transfer_reference)
                                        <div class="form-text">Ref: {{ $saving->transfer_reference }}</div>
                                    @endif
                                </div>
                            @endif

                            <!-- Catatan -->
                            <div class="col-12">
                                <label for="notes" class="form-label">
                                    <i class="bi bi-journal-text text-lilac me-2"></i>
                                    Catatan
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                    placeholder="Catatan tambahan (opsional)">{{ old('notes', $saving->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <div>
                                <a href="{{ route('savings.show', $saving) }}" class="btn btn-outline-info me-2">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </a>
                                @if ($saving->status === 'PENDING')
                                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                        <i class="bi bi-trash me-2"></i>Hapus
                                    </button>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('savings.index') }}" class="btn btn-secondary me-2">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Tabungan
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form (Hidden) -->
                    @if ($saving->status === 'PENDING')
                        <form id="delete-form" action="{{ route('savings.destroy', $saving) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus tabungan ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
                document.getElementById('delete-form').submit();
            }
        }

        // Auto calculate saving amount based on payment percentage
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            const originalPaymentAmount = {{ $saving->payment->amount }};
            const originalSavingAmount = {{ $saving->amount }};

            amountInput.addEventListener('blur', function() {
                const newAmount = parseFloat(this.value) || 0;
                const paymentAmount = originalPaymentAmount;
                const expectedAmount = paymentAmount * 0.1;

                if (Math.abs(newAmount - expectedAmount) > 1000) {
                    if (confirm(
                            `Jumlah yang dimasukkan (${formatCurrency(newAmount)}) berbeda dari 10% payment (${formatCurrency(expectedAmount)}).\n\nApakah Anda yakin?`
                            )) {
                        // User confirmed, keep the value
                    } else {
                        // Reset to expected amount
                        this.value = expectedAmount;
                    }
                }
            });

            function formatCurrency(amount) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            }
        });
    </script>
@endpush
