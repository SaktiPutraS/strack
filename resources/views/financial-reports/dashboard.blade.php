@extends('layouts.app')
@section('title', 'Dashboard Keuangan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="section-title">
                <i class="bi bi-graph-up-arrow"></i>Dashboard Keuangan - {{ now()->format('F Y') }}
            </h1>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-bank stat-icon"></i>
                    <div class="stat-value" data-metric="bank-balance">Rp {{ number_format($bankBalance, 0, ',', '.') }}</div>
                    <div class="stat-label">Saldo Bank Octo</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-coin stat-icon"></i>
                    <div class="stat-value">{{ number_format($goldPortfolio['summary']['total_grams'], 3) }} gram</div>
                    <div class="stat-label">Emas (Rp {{ number_format($goldPortfolio['summary']['current_value'], 0, ',', '.') }})</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-trophy stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($netWorth, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Net Worth</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Performance -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-graph-up stat-icon text-success"></i>
                    <div class="stat-value">Rp {{ number_format($monthlyProfit['result']['operational_profit'], 0, ',', '.') }}</div>
                    <div class="stat-label">Laba Operasional Bulan Ini</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-exclamation-triangle stat-icon text-warning"></i>
                    <div class="stat-value">Rp {{ number_format($untransferredAmount, 0, ',', '.') }}</div>
                    <div class="stat-label">Pembayaran Belum Transfer</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-lightning"></i>Aksi Cepat
                    </h5>
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <button class="btn btn-outline-danger w-100" onclick="openQuickExpenseModal()">
                                <i class="bi bi-credit-card me-2"></i>Input Pengeluaran
                            </button>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('gold.create', ['type' => 'BUY']) }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-coin me-2"></i>Beli Emas
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('bank-transfers.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-bank me-2"></i>Transfer Bank
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Expenses Breakdown -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-pie-chart"></i>Pengeluaran Bulan Ini
                    </h5>

                    @if ($monthlyExpensesByCategory->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach ($monthlyExpensesByCategory as $category => $amount)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $category }}</span>
                                    <span class="fw-bold text-danger">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total Pengeluaran:</strong>
                                <strong class="text-danger">Rp {{ number_format($monthlyExpensesByCategory->sum(), 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-wallet2 text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Belum ada pengeluaran bulan ini</p>
                            <button class="btn btn-primary" onclick="openQuickExpenseModal()">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Pengeluaran
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Gold Transactions -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-coin"></i>Transaksi Emas Terbaru
                        </h5>
                        <a href="{{ route('gold.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>

                    @if ($goldPortfolio['transactions']->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach ($goldPortfolio['transactions']->take(5) as $transaction)
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="d-flex align-items-center mb-1">
                                                @if ($transaction['type'] === 'BELI')
                                                    <span class="badge bg-success me-2">BELI</span>
                                                @else
                                                    <span class="badge bg-warning me-2">JUAL</span>
                                                @endif
                                                <strong>{{ $transaction['grams'] }} gram</strong>
                                            </div>
                                            <small class="text-muted">
                                                {{ $transaction['date'] }} • {{ $transaction['formatted_total_price'] }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">Sisa: {{ number_format($transaction['running_grams'], 3) }} gram</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-coin text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Belum ada transaksi emas</p>
                            <a href="{{ route('gold.create', ['type' => 'BUY']) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Beli Emas Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Expense Modal -->
    <div class="modal fade" id="quickExpenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-credit-card me-2"></i>Input Pengeluaran Cepat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickExpenseForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Jumlah Pengeluaran *</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" id="quickAmount" class="form-control" min="1" step="1000" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori *</label>
                            <select id="quickCategory" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="OPERASIONAL">Operasional</option>
                                <option value="MARKETING">Marketing</option>
                                <option value="PENGEMBANGAN">Pengembangan</option>
                                <option value="GAJI_FREELANCE">Gaji & Freelance</option>
                                <option value="ENTERTAINMENT">Entertainment</option>
                                <option value="LAIN_LAIN">Lain-lain</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi *</label>
                            <input type="text" id="quickDescription" class="form-control" placeholder="Contoh: Hosting bulanan, kopi meeting, dll"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Quick Expense Modal
        function openQuickExpenseModal() {
            new bootstrap.Modal(document.getElementById('quickExpenseModal')).show();
        }

        // Quick Expense Form Submission
        document.getElementById('quickExpenseForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                amount: document.getElementById('quickAmount').value,
                category: document.getElementById('quickCategory').value,
                description: document.getElementById('quickDescription').value,
            };

            fetch('{{ route('quick-expense') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('quickExpenseModal')).hide();
                        this.reset();
                        showSuccess(data.message);

                        // Reload page to show updated data
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showError(data.message || 'Gagal menyimpan pengeluaran');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Terjadi kesalahan saat menyimpan');
                });
        });

        // Auto-refresh data every 5 minutes
        setInterval(function() {
            // Update key financial metrics without full page reload
            updateFinancialMetrics();
        }, 300000); // 5 minutes

        function updateFinancialMetrics() {
            fetch('{{ route('api.financial-reports.summary') }}')
                .then(response => response.json())
                .then(data => {
                    // Update bank balance if element exists
                    const bankBalanceEl = document.querySelector('[data-metric="bank-balance"]');
                    if (bankBalanceEl && data.balance_sheet) {
                        bankBalanceEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(
                            data.balance_sheet.assets.bank_octo_balance
                        );
                    }
                })
                .catch(error => console.error('Failed to update metrics:', error));
        }

        // Show success/error messages
        function showSuccess(message) {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove from DOM after hiding
            toast.addEventListener('hidden.bs.toast', () => {
                document.body.removeChild(toast);
            });
        }

        function showError(message) {
            // Create error toast notification
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-danger border-0 position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove from DOM after hiding
            toast.addEventListener('hidden.bs.toast', () => {
                document.body.removeChild(toast);
            });
        }

        // Format currency in real-time for input
        document.getElementById('quickAmount').addEventListener('input', function(e) {
            const value = this.value.replace(/[^0-9]/g, '');
            if (value) {
                // Show formatted preview
                const formatted = 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                this.title = formatted;
            }
        });

        // Auto-suggest descriptions based on category
        document.getElementById('quickCategory').addEventListener('change', function() {
            const descriptions = {
                'OPERASIONAL': ['Hosting bulanan', 'Domain renewal', 'Software license', 'Internet'],
                'MARKETING': ['Google Ads', 'Facebook Ads', 'Promosi', 'Content tools'],
                'PENGEMBANGAN': ['Course online', 'Hardware', 'Third-party API'],
                'GAJI_FREELANCE': ['Gaji freelancer', 'Fee project', 'Bonus'],
                'ENTERTAINMENT': ['Kopi meeting', 'Makan siang', 'Snack'],
                'LAIN_LAIN': ['Transportasi', 'Pajak', 'Administrasi']
            };

            const suggestions = descriptions[this.value] || [];
            const descInput = document.getElementById('quickDescription');

            if (suggestions.length > 0) {
                descInput.placeholder = 'Contoh: ' + suggestions.join(', ');
            }
        });
    </script>
@endpush
