@extends('layouts.app')
@section('title', 'Transfer ke Bank Octo')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-bank"></i>Transfer ke Bank Octo
                </h1>
                <a href="{{ route('bank-transfers.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Transfer Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Bank Balance Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-success text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-bank text-white fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-white-50">Saldo Bank Octo Saat Ini</h6>
                            <h3 class="mb-0 fw-bold text-white">{{ $formattedBankBalance }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">Rp {{ number_format($totalTransferred ?? 0, 0, ',', '.') }}</h3>
                    <p class="mb-0 text-muted">Total Sudah Transfer</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">Rp {{ number_format($totalUntransferred ?? 0, 0, ',', '.') }}</h3>
                    <p class="mb-0 text-muted">Belum Transfer</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Untransferred Payments -->
    @if (isset($untransferredPayments) && $untransferredPayments->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="section-title mb-0 text-warning">
                                <i class="bi bi-exclamation-triangle"></i>Pembayaran Belum Transfer ({{ $untransferredPayments->count() }})
                            </h5>
                            <button class="btn btn-warning" onclick="showBatchTransferModal()">
                                <i class="bi bi-bank me-2"></i>Transfer Batch
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Tanggal</th>
                                        <th>Proyek</th>
                                        <th>Klien</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($untransferredPayments as $payment)
                                        <tr>
                                            <td><input type="checkbox" name="payment_ids[]" value="{{ $payment->id }}" class="payment-checkbox">
                                            </td>
                                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                            <td>{{ $payment->project->title }}</td>
                                            <td>{{ $payment->project->client->name }}</td>
                                            <td><strong class="text-success">{{ $payment->formatted_amount }}</strong></td>
                                            <td>
                                                <a href="{{ route('bank-transfers.create', ['payment_id' => $payment->id]) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-bank"></i> Transfer
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Transfer History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-clock-history"></i>Riwayat Transfer
                    </h5>

                    @if (isset($transfers) && $transfers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal Transfer</th>
                                        <th>Proyek</th>
                                        <th>Klien</th>
                                        <th>Jumlah</th>
                                        <th>Referensi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transfers as $transfer)
                                        <tr>
                                            <td>{{ $transfer->transfer_date->format('d M Y') }}</td>
                                            <td>{{ $transfer->payment->project->title }}</td>
                                            <td>{{ $transfer->payment->project->client->name }}</td>
                                            <td><strong class="text-success">{{ $transfer->formatted_transfer_amount }}</strong></td>
                                            <td>{{ $transfer->reference_number ?? '-' }}</td>
                                            <td>
                                                <form action="{{ route('bank-transfers.destroy', $transfer) }}" method="POST"
                                                    onsubmit="return confirm('Yakin hapus transfer ini?')" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if (method_exists($transfers, 'links'))
                            <div class="mt-4">{{ $transfers->links() }}</div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bank text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Belum ada transfer</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Transfer Modal -->
    <div class="modal fade" id="batchTransferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-bank me-2"></i>Transfer Batch ke Bank Octo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('bank-transfers.batch') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Transfer *</label>
                            <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Referensi</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="Nomor transaksi bank">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan transfer batch"></textarea>
                        </div>
                        <div id="selectedPayments"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Select all checkbox functionality
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.payment-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        function showBatchTransferModal() {
            const selectedPayments = document.querySelectorAll('.payment-checkbox:checked');

            if (selectedPayments.length === 0) {
                alert('Pilih pembayaran yang akan ditransfer!');
                return;
            }

            // Add hidden inputs for selected payments
            const selectedDiv = document.getElementById('selectedPayments');
            selectedDiv.innerHTML = '';

            selectedPayments.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'payment_ids[]';
                input.value = checkbox.value;
                selectedDiv.appendChild(input);
            });

            new bootstrap.Modal(document.getElementById('batchTransferModal')).show();
        }
    </script>

    <style>
        .bg-gradient-success {
            background: linear-gradient(135deg, #10B981, #059669) !important;
        }
    </style>
@endpush
