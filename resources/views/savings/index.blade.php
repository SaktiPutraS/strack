@extends('layouts.app')
@section('title', 'Tabungan 10%')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-piggy-bank"></i>Tabungan 10%
                </h1>
                <button class="btn btn-primary" onclick="updateBankBalance()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Update Saldo Bank
                </button>
            </div>
        </div>
    </div>

    <!-- Savings Overview -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-piggy-bank-fill stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($totalSavings ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Tabungan</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-bank stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($currentBankBalance ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Saldo Bank Octo</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-{{ $isBalanced ? 'check-circle text-success' : 'exclamation-triangle text-warning' }} stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format(abs($difference ?? 0), 0, ',', '.') }}</div>
                    <div class="stat-label">{{ ($difference ?? 0) >= 0 ? 'Lebih' : 'Kurang' }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-calendar-month stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($monthlySavings ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Tabungan Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if ($isBalanced ?? true)
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle-fill me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Tabungan Seimbang! 🎉</h6>
                                <p class="mb-0">Total tabungan sudah sesuai dengan saldo Bank Octo Anda.</p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Perlu Verifikasi Saldo</h6>
                                <p class="mb-0">
                                    Ada selisih Rp {{ number_format(abs($difference ?? 0), 0, ',', '.') }}
                                    antara total tabungan dengan saldo bank. Silakan verifikasi dan update saldo bank.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
                            <select name="is_verified" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('is_verified') == '1' ? 'selected' : '' }}>Terverifikasi</option>
                                <option value="0" {{ request('is_verified') == '0' ? 'selected' : '' }}>Belum Verifikasi</option>
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="section-title mb-0">
                            <i class="bi bi-list-ul"></i>Riwayat Tabungan ({{ $savings->total() ?? $savings->count() }} total)
                        </h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-success" onclick="bulkVerify()">
                                <i class="bi bi-check-all me-1"></i>Verifikasi Semua
                            </button>
                        </div>
                    </div>

                    @if (isset($savings) && $savings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($savings as $saving)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-start">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 50px; height: 50px; background: var(--lilac-light); color: var(--lilac-primary);">
                                            <i class="bi bi-piggy-bank"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1 text-lilac">{{ $saving->payment->project->title }}</h6>
                                                    <small class="text-muted">{{ $saving->payment->project->client->name }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold text-success fs-5">{{ $saving->formatted_amount }}</div>
                                                    @if ($saving->is_verified)
                                                        <span class="badge badge-success">
                                                            <i class="bi bi-check-circle me-1"></i>Terverifikasi
                                                        </span>
                                                    @else
                                                        <span class="badge badge-warning">
                                                            <i class="bi bi-clock me-1"></i>Belum Verifikasi
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-2">
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-calendar3 text-muted me-2"></i>
                                                        <small class="text-muted">{{ $saving->transaction_date->format('d M Y') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-credit-card text-muted me-2"></i>
                                                        <small class="text-muted">{{ $saving->payment->payment_type }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-cash text-muted me-2"></i>
                                                        <small class="text-muted">Dari: {{ $saving->payment->formatted_amount }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-bank text-muted me-2"></i>
                                                        <small class="text-muted">Saldo: {{ $saving->formatted_bank_balance }}</small>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($saving->notes)
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-journal-text me-1"></i>
                                                        {{ $saving->notes }}
                                                    </small>
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="text-muted small">
                                                    10% dari pembayaran {{ $saving->payment->payment_type }}
                                                </div>
                                                <div class="btn-group" role="group">
                                                    @if (!$saving->is_verified)
                                                        <button class="btn btn-sm btn-success" onclick="verifySaving({{ $saving->id }})">
                                                            <i class="bi bi-check-circle"></i> Verifikasi
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('projects.show', $saving->payment->project) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-folder2-open"></i>
                                                    </a>
                                                    <a href="{{ route('savings.edit', $saving) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if (method_exists($savings, 'links'))
                            <div class="d-flex justify-content-center mt-4">
                                {{ $savings->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-piggy-bank text-lilac-secondary" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Belum ada tabungan</p>
                            <p class="text-muted">Tabungan akan otomatis terbuat saat ada pembayaran masuk</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateBankBalance() {
            const modal = new bootstrap.Modal(document.getElementById('bankBalanceModal'));
            modal.show();
        }

        function verifySaving(savingId) {
            if (confirm('Apakah Anda yakin ingin memverifikasi tabungan ini?')) {
                fetch(`{{ route('savings.verify', ':id') }}`.replace(':id', savingId), {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Gagal memverifikasi tabungan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan');
                    });
            }
        }

        function bulkVerify() {
            if (confirm('Apakah Anda yakin ingin memverifikasi semua tabungan yang belum terverifikasi?')) {
                fetch('{{ route('savings.bulk-verify') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`Berhasil memverifikasi ${data.verified_count} tabungan`);
                            location.reload();
                        } else {
                            alert('Gagal memverifikasi tabungan');
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
