@extends('layouts.app')
@section('title', 'Tabungan 10%')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-piggy-bank"></i>Tabungan 10%
                </h1>
                <div class="btn-group">
                    <button class="btn btn-warning" onclick="openTransferModal()" id="transferBtn">
                        <i class="bi bi-bank me-2"></i>Transfer ke Bank
                    </button>
                    <button class="btn btn-primary" onclick="updateBankBalance()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Update Saldo Bank
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Savings Overview -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">Rp {{ number_format($totalSavings ?? 0, 0, ',', '.') }}</h3>
                    <p class="mb-0 text-muted">Total Tabungan</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning" id="pendingAmount">Rp {{ number_format($pendingSavings ?? 0, 0, ',', '.') }}</h3>
                    <p class="mb-0 text-muted">Pending Transfer</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">Rp {{ number_format($currentBankBalance ?? 0, 0, ',', '.') }}</h3>
                    <p class="mb-0 text-muted">Saldo Bank</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Status -->
    @if (($pendingSavings ?? 0) > 10000)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-3"></i>
                    <strong>Siap Transfer!</strong>
                    Anda memiliki Rp {{ number_format($pendingSavings, 0, ',', '.') }} tabungan yang menunggu transfer ke bank.
                    <button class="btn btn-warning btn-sm ms-3" onclick="openTransferModal()">
                        <i class="bi bi-bank me-1"></i>Transfer Sekarang
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($isBalanced ?? true)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle-fill me-3"></i>
                    <strong>Tabungan Seimbang!</strong> Total transfer sudah sesuai dengan saldo bank.
                </div>
            </div>
        </div>
    @else
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle-fill me-3"></i>
                    <strong>Info:</strong> Selisih Rp {{ number_format(abs($difference ?? 0), 0, ',', '.') }}
                    antara total transfer dengan saldo bank.
                </div>
            </div>
        </div>
    @endif

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Dari Tanggal">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Sampai Tanggal">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                <option value="TRANSFERRED" {{ request('status') == 'TRANSFERRED' ? 'selected' : '' }}>Transferred</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Savings List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-list-ul"></i>Riwayat Tabungan ({{ $savings->total() ?? $savings->count() }} total)
                    </h5>

                    @if (isset($savings) && $savings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Proyek</th>
                                        <th>Klien</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Transfer</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($savings as $saving)
                                        <tr>
                                            <td>{{ $saving->transaction_date->format('d M Y') }}</td>
                                            <td>
                                                <strong class="text-lilac">{{ $saving->payment->project->title }}</strong>
                                                @if ($saving->notes)
                                                    <br><small class="text-muted">{{ Str::limit($saving->notes, 40) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $saving->payment->project->client->name }}</td>
                                            <td>
                                                <strong class="text-success">{{ $saving->formatted_amount }}</strong>
                                                <br><small class="text-muted">{{ $saving->payment->payment_type }}</small>
                                            </td>
                                            <td>
                                                @if ($saving->status === 'PENDING')
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-clock me-1"></i>Pending
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Transferred
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($saving->status === 'TRANSFERRED')
                                                    <div>
                                                        <strong>{{ $saving->transfer_date->format('d M Y') }}</strong>
                                                        <br><small class="text-muted">{{ $saving->transfer_method }}</small>
                                                        @if ($saving->transfer_reference)
                                                            <br><small class="text-muted">{{ $saving->transfer_reference }}</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('projects.show', $saving->payment->project) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Lihat Proyek">
                                                        <i class="bi bi-folder2-open"></i>
                                                    </a>
                                                    <a href="{{ route('savings.edit', $saving) }}" class="btn btn-sm btn-outline-secondary"
                                                        title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if (method_exists($savings, 'links'))
                            <div class="mt-4">
                                <div style="display: none;">
                                    {{ $savings->links() }}
                                </div>

                                <div class="pagination-info-alt">
                                    Menampilkan {{ $savings->firstItem() }}-{{ $savings->lastItem() }}
                                    dari {{ $savings->total() }} proyek
                                </div>

                                <nav class="bootstrap-pagination">
                                    <ul class="pagination">
                                        @if ($savings->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $savings->previousPageUrl() }}">
                                                    <i class="bi bi-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        @for ($i = 1; $i <= $savings->lastPage(); $i++)
                                            @if ($i == $savings->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $i }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $savings->url($i) }}">{{ $i }}</a>
                                                </li>
                                            @endif
                                        @endfor

                                        @if ($savings->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $savings->nextPageUrl() }}">
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
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-piggy-bank text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Belum ada tabungan</p>
                            <p class="text-muted">Tabungan akan otomatis terbuat saat ada pembayaran masuk</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-bank me-2"></i>Transfer Tabungan ke Bank
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="transferForm">
                    <div class="modal-body">
                        <!-- Pending Savings List -->
                        <div class="mb-4">
                            <h6>Pilih Tabungan yang Akan Ditransfer:</h6>
                            <div id="pendingSavingsList" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div class="text-center text-muted">
                                    <i class="bi bi-hourglass-split"></i> Loading...
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Details -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>Total yang akan ditransfer: <span id="selectedTotal">Rp 0</span></strong>
                                    <br>Jumlah item: <span id="selectedCount">0</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Transfer *</label>
                                <input type="date" id="transferDate" class="form-control" required max="{{ date('Y-m-d') }}"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bank/Metode *</label>
                                <select id="transferMethod" class="form-select" required>
                                    <option value="">Pilih Bank</option>
                                    <option value="Bank Octo">Bank Octo</option>
                                    <option value="BCA">BCA</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Nomor Referensi</label>
                                <input type="text" id="transferReference" class="form-control" placeholder="No. transaksi/referensi (opsional)">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <textarea id="transferNotes" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="submitTransfer" disabled>
                            <i class="bi bi-bank me-1"></i>Transfer <span id="submitAmount">Rp 0</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Bank Balance Modal -->
    <div class="modal fade" id="bankBalanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Saldo Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updateBankBalanceForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Saldo Bank Saat Ini *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" id="newBankBalance" class="form-control" required min="0" step="1000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bank *</label>
                                <select id="bankName" class="form-select" required>
                                    <option value="Bank Octo">Bank Octo</option>
                                    <option value="BCA">BCA</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal *</label>
                                <input type="date" id="balanceDate" class="form-control" required max="{{ date('Y-m-d') }}"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <textarea id="updateNotes" class="form-control" rows="3" placeholder="Catatan update saldo (opsional)"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update Saldo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let pendingSavings = [];
        let selectedSavings = [];

        function openTransferModal() {
            const modal = new bootstrap.Modal(document.getElementById('transferModal'));
            loadPendingSavings();
            modal.show();
        }

        function updateBankBalance() {
            const modal = new bootstrap.Modal(document.getElementById('bankBalanceModal'));
            modal.show();
        }

        function loadPendingSavings() {
            document.getElementById('pendingSavingsList').innerHTML =
                '<div class="text-center text-muted"><i class="bi bi-hourglass-split"></i> Loading...</div>';

            fetch('{{ route('savings.pending') }}')
                .then(response => response.json())
                .then(data => {
                    pendingSavings = data.pending_savings;
                    renderPendingSavings();
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('pendingSavingsList').innerHTML = '<div class="text-danger">Error loading data</div>';
                });
        }

        function renderPendingSavings() {
            const container = document.getElementById('pendingSavingsList');

            if (pendingSavings.length === 0) {
                container.innerHTML = '<div class="text-center text-muted">Tidak ada tabungan pending</div>';
                return;
            }

            let html = `
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        <i class="bi bi-check-all"></i> Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="clearSelection()">
                        <i class="bi bi-x-circle"></i> Clear
                    </button>
                </div>
            `;

            pendingSavings.forEach(saving => {
                html += `
                    <div class="form-check border-bottom py-2">
                        <input class="form-check-input" type="checkbox" value="${saving.id}"
                               id="saving_${saving.id}" onchange="updateSelection()">
                        <label class="form-check-label w-100" for="saving_${saving.id}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong class="text-success">${saving.formatted_amount}</strong>
                                    <br><small class="text-muted">${saving.transaction_date} - ${saving.project_title}</small>
                                    <br><small class="text-muted">${saving.client_name} (${saving.payment_type})</small>
                                </div>
                            </div>
                        </label>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function selectAll() {
            document.querySelectorAll('#pendingSavingsList input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelection();
        }

        function clearSelection() {
            document.querySelectorAll('#pendingSavingsList input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelection();
        }

        function updateSelection() {
            selectedSavings = [];
            let totalAmount = 0;

            document.querySelectorAll('#pendingSavingsList input[type="checkbox"]:checked').forEach(checkbox => {
                const savingId = parseInt(checkbox.value);
                const saving = pendingSavings.find(s => s.id === savingId);
                if (saving) {
                    selectedSavings.push(saving);
                    totalAmount += saving.amount;
                }
            });

            document.getElementById('selectedCount').textContent = selectedSavings.length;
            document.getElementById('selectedTotal').textContent = formatCurrency(totalAmount);
            document.getElementById('submitAmount').textContent = formatCurrency(totalAmount);

            const submitBtn = document.getElementById('submitTransfer');
            submitBtn.disabled = selectedSavings.length === 0;
        }

        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        // Handle transfer form submission
        document.getElementById('transferForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (selectedSavings.length === 0) {
                alert('Pilih minimal 1 tabungan untuk ditransfer');
                return;
            }

            const transferData = {
                saving_ids: selectedSavings.map(s => s.id),
                transfer_date: document.getElementById('transferDate').value,
                transfer_method: document.getElementById('transferMethod').value,
                transfer_reference: document.getElementById('transferReference').value,
                notes: document.getElementById('transferNotes').value
            };

            const submitBtn = document.getElementById('submitTransfer');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Processing...';

            fetch('{{ route('savings.transfer') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(transferData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        bootstrap.Modal.getInstance(document.getElementById('transferModal')).hide();
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat transfer');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-bank me-1"></i>Transfer <span id="submitAmount">Rp 0</span>';
                });
        });

        // Handle bank balance update
        document.getElementById('updateBankBalanceForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                balance: document.getElementById('newBankBalance').value,
                bank_name: document.getElementById('bankName').value,
                balance_date: document.getElementById('balanceDate').value,
                notes: document.getElementById('updateNotes').value
            };

            fetch('{{ route('savings.update-bank-balance') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        bootstrap.Modal.getInstance(document.getElementById('bankBalanceModal')).hide();
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        });
    </script>
@endpush
