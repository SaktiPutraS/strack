@extends('layouts.app')
@section('title', 'Transfer ke Bank Octo')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-bank me-2"></i>Transfer ke Bank Octo
                    </h1>
                    <p class="text-muted mb-0">Kelola transfer pembayaran ke bank</p>
                </div>
                <a href="{{ route('bank-transfers.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Transfer Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Bank Balance Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0 bg-gradient-success text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="luxury-icon me-3 bg-white bg-opacity-25">
                            <i class="bi bi-bank text-white"></i>
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
            <div class="card luxury-card border-0">
                <div class="card-body text-center py-4">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-success">Rp {{ number_format($totalTransferred ?? 0, 0, ',', '.') }}</h3>
                            <p class="mb-0 text-muted">Total Sudah Transfer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card luxury-card border-0">
                <div class="card-body text-center py-4">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-clock text-warning"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-warning">Rp {{ number_format($totalUntransferred ?? 0, 0, ',', '.') }}</h3>
                            <p class="mb-0 text-muted">Belum Transfer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Untransferred Payments -->
    @if (isset($untransferredPayments) && $untransferredPayments->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card luxury-card border-0 border-warning">
                    <div class="card-header bg-gradient-warning text-white border-0 p-4">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                            <div class="d-flex align-items-center">
                                <div class="luxury-icon me-3 bg-white bg-opacity-25">
                                    <i class="bi bi-exclamation-triangle text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-white">Pembayaran Belum Transfer</h5>
                                    <p class="mb-0 text-white-50">{{ $untransferredPayments->count() }} pembayaran pending</p>
                                </div>
                            </div>
                            <button class="btn btn-light" onclick="showBatchTransferModal()">
                                <i class="bi bi-bank me-2"></i>Transfer Batch
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <!-- Desktop Table View -->
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Tanggal</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Proyek</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Klien</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark text-end">Jumlah</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($untransferredPayments as $payment)
                                            <tr class="border-bottom">
                                                <td class="px-4 py-4">
                                                    <input type="checkbox" name="payment_ids[]" value="{{ $payment->id }}"
                                                        class="payment-checkbox form-check-input">
                                                </td>
                                                <td class="px-4 py-4">
                                                    <div class="fw-medium">{{ $payment->payment_date->format('d M Y') }}</div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $payment->project->title }}</h6>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <span class="badge bg-secondary">{{ $payment->project->client->name }}</span>
                                                </td>
                                                <td class="px-4 py-4 text-end">
                                                    <strong class="text-success fs-5">{{ $payment->formatted_amount }}</strong>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <a href="{{ route('bank-transfers.create', ['payment_id' => $payment->id]) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="bi bi-bank"></i> Transfer
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-lg-none p-3">
                            @foreach ($untransferredPayments as $payment)
                                <div class="card luxury-card mb-3">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <input type="checkbox" name="payment_ids[]" value="{{ $payment->id }}"
                                                        class="payment-checkbox form-check-input me-2">
                                                    <h6 class="fw-bold mb-0">{{ $payment->project->title }}</h6>
                                                </div>
                                                <small class="text-muted">{{ $payment->project->client->name }}</small>
                                            </div>
                                            <div class="text-end">
                                                <strong class="text-success">{{ $payment->formatted_amount }}</strong>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                                <small class="text-muted">{{ $payment->payment_date->format('d M Y') }}</small>
                                            </div>
                                            <a href="{{ route('bank-transfers.create', ['payment_id' => $payment->id]) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-bank"></i> Transfer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Transfer History -->
    <div class="row">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-clock-history text-purple"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Riwayat Transfer</h5>
                            <p class="mb-0 text-muted">{{ $transfers->total() ?? $transfers->count() }} transfer</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if (isset($transfers) && $transfers->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Tanggal Transfer</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Proyek</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Klien</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark text-end">Jumlah</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark">Referensi</th>
                                            <th class="px-4 py-3 border-0 fw-semibold text-dark text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transfers as $transfer)
                                            <tr class="border-bottom">
                                                <td class="px-4 py-4">
                                                    <div class="fw-medium">{{ $transfer->transfer_date->format('d M Y') }}</div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $transfer->payment->project->title }}</h6>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <span class="badge bg-secondary">{{ $transfer->payment->project->client->name }}</span>
                                                </td>
                                                <td class="px-4 py-4 text-end">
                                                    <strong class="text-success fs-5">{{ $transfer->formatted_transfer_amount }}</strong>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <span class="text-muted">{{ $transfer->reference_number ?? '-' }}</span>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <form action="{{ route('bank-transfers.destroy', $transfer) }}" method="POST"
                                                        style="display: inline;" onsubmit="return confirmDelete(event)">
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
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-lg-none">
                            <div class="p-3">
                                @foreach ($transfers as $transfer)
                                    <div class="card luxury-card mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold mb-1">{{ $transfer->payment->project->title }}</h6>
                                                    <small class="text-muted">{{ $transfer->payment->project->client->name }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <strong class="text-success">{{ $transfer->formatted_transfer_amount }}</strong>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                                    <small class="text-muted">{{ $transfer->transfer_date->format('d M Y') }}</small>
                                                    @if ($transfer->reference_number)
                                                        <span class="ms-2 badge bg-light text-dark">{{ $transfer->reference_number }}</span>
                                                    @endif
                                                </div>
                                                <form action="{{ route('bank-transfers.destroy', $transfer) }}" method="POST"
                                                    style="display: inline;" onsubmit="return confirmDelete(event)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if (method_exists($transfers, 'links'))
                            <div class="card-footer bg-light border-0 p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <p class="text-muted mb-0">
                                            Menampilkan {{ $transfers->firstItem() }}-{{ $transfers->lastItem() }}
                                            dari {{ $transfers->total() }} transfer
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        @if ($transfers->hasPages())
                                            <nav class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                                                <ul class="pagination mb-0">
                                                    @if ($transfers->onFirstPage())
                                                        <li class="page-item disabled">
                                                            <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                                        </li>
                                                    @else
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $transfers->previousPageUrl() }}">
                                                                <i class="bi bi-chevron-left"></i>
                                                            </a>
                                                        </li>
                                                    @endif

                                                    <li class="page-item active">
                                                        <span class="page-link">{{ $transfers->currentPage() }} / {{ $transfers->lastPage() }}</span>
                                                    </li>

                                                    @if ($transfers->hasMorePages())
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $transfers->nextPageUrl() }}">
                                                                <i class="bi bi-chevron-right"></i>
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li class="page-item disabled">
                                                            <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </nav>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-bank text-muted" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Belum ada transfer</h5>
                            <p class="text-muted mb-4">Mulai dengan melakukan transfer pertama</p>
                            <a href="{{ route('bank-transfers.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Transfer Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Transfer Modal -->
    <div class="modal fade" id="batchTransferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content luxury-card border-0">
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <div class="luxury-icon me-2 bg-white bg-opacity-25">
                            <i class="bi bi-bank text-white"></i>
                        </div>
                        Transfer Batch ke Bank Octo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('bank-transfers.batch') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar3 text-purple me-2"></i>
                                Tanggal Transfer <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-hash text-purple me-2"></i>
                                Nomor Referensi
                            </label>
                            <input type="text" name="reference_number" class="form-control" placeholder="Nomor transaksi bank">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-journal-text text-purple me-2"></i>
                                Catatan
                            </label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan transfer batch"></textarea>
                        </div>
                        <div id="selectedPayments"></div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-bank me-2"></i>Proses Transfer
                        </button>
                    </div>
                </form>
            </div>
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

            // Select all checkbox functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.payment-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            // Animation
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

        function showBatchTransferModal() {
            const selectedPayments = document.querySelectorAll('.payment-checkbox:checked');

            if (selectedPayments.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Pembayaran',
                    text: 'Pilih pembayaran yang akan ditransfer!',
                    confirmButtonColor: '#8B5CF6'
                });
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

        function confirmDelete(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Yakin ingin menghapus transfer ini? Aksi ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });

            return false;
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

        .bg-gradient-primary {
            background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10B981, #059669) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #F59E0B, #D97706) !important;
        }

        .table th {
            background-color: rgba(139, 92, 246, 0.05);
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        }

        .pagination .page-link {
            color: #8B5CF6;
            border-color: rgba(139, 92, 246, 0.2);
        }

        .pagination .page-item.active .page-link {
            background-color: #8B5CF6;
            border-color: #8B5CF6;
        }

        .border-warning {
            border-color: rgba(245, 158, 11, 0.2) !important;
        }

        .modal-content.luxury-card {
            border: none;
            box-shadow: 0 20px 60px rgba(139, 92, 246, 0.15);
        }
    </style>
@endpush
