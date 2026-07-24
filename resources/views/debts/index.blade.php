@extends('layouts.app')
@section('title', 'Catatan Hutang Piutang')

@section('content')
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-cash-stack me-2"></i>Catatan Hutang Piutang
                    </h1>
                    <p class="text-muted mb-0">Pantau sisa hutang &amp; piutang, catat tiap pembayaran</p>
                </div>
                <a href="{{ route('debts.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Catatan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2"><i class="bi bi-arrow-down-left-circle text-success fs-4"></i></div>
                    <h3 class="fw-bold text-success mb-1">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Total Piutang (hak tagih)</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2"><i class="bi bi-arrow-up-right-circle text-danger fs-4"></i></div>
                    <h3 class="fw-bold text-danger mb-1">Rp {{ number_format($totalHutang, 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Total Hutang (kewajiban)</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2"><i class="bi bi-calculator text-purple fs-4"></i></div>
                    <h3 class="fw-bold mb-1 {{ $selisih >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $selisih < 0 ? '-' : '' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                    </h3>
                    <small class="text-muted fw-semibold">Selisih (Piutang - Hutang)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter + Daftar -->
    <div class="card luxury-card border-0">
        <div class="card-body p-4 border-bottom">
            <form method="GET" class="row g-2">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control form-control-lg" value="{{ request('search') }}"
                        placeholder="Cari pihak, judul, atau catatan...">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select form-select-lg">
                        <option value="">Semua Tipe</option>
                        <option value="PIUTANG" {{ request('type') === 'PIUTANG' ? 'selected' : '' }}>Piutang</option>
                        <option value="HUTANG" {{ request('type') === 'HUTANG' ? 'selected' : '' }}>Hutang</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-purple text-white btn-lg" style="background:linear-gradient(135deg,#8B5CF6,#A855F7);border:none;">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                </div>
            </form>
        </div>

        <div class="table-responsive d-none d-lg-block">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="py-3">Pihak / Judul</th>
                        <th class="py-3 text-end">Total</th>
                        <th class="py-3 text-end">Terbayar</th>
                        <th class="py-3 text-end">Sisa</th>
                        <th class="py-3" style="width:160px;">Progress</th>
                        <th class="py-3 text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr class="{{ $record->is_overdue ? 'table-danger' : '' }}">
                            <td class="px-4">
                                <span class="badge bg-{{ $record->type_color }}">{{ strtoupper($record->type_label) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('debts.show', $record) }}" class="fw-semibold text-decoration-none text-dark">
                                    {{ $record->party_name }}
                                </a>
                                <div class="small text-muted">
                                    {{ $record->title ?: '-' }}
                                    @if ($record->due_date)
                                        <span class="badge bg-{{ $record->is_overdue ? 'danger' : ($record->is_due_soon ? 'warning' : 'light text-dark') }} ms-1">
                                            <i class="bi bi-calendar-event me-1"></i>{{ $record->due_date->format('d M Y') }}
                                            @if ($record->is_overdue) (lewat tempo) @elseif($record->is_due_soon) (dekat) @endif
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">{{ $record->formatted_principal }}</td>
                            <td class="text-end text-success">{{ $record->formatted_paid }}</td>
                            <td class="text-end fw-bold">{{ $record->formatted_remaining }}</td>
                            <td>
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-{{ $record->type_color }}" style="width: {{ $record->progress_percentage }}%;"></div>
                                </div>
                                <small class="text-muted">{{ $record->progress_percentage }}%
                                    <span class="badge bg-{{ $record->status_color }} ms-1">{{ $record->status_label }}</span>
                                </small>
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('debts.show', $record) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('debts.edit', $record) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                    data-action="{{ route('debts.destroy', $record) }}"
                                    data-name="{{ $record->party_name }}" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Belum ada catatan hutang/piutang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile -->
        <div class="d-lg-none p-3">
            @forelse ($records as $record)
                <div class="card luxury-card mb-3 {{ $record->is_overdue ? 'border-danger' : '' }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-{{ $record->type_color }}">{{ strtoupper($record->type_label) }}</span>
                                <div class="fw-bold mt-1">{{ $record->party_name }}</div>
                                <div class="small text-muted">{{ $record->title ?: '-' }}</div>
                            </div>
                            <span class="badge bg-{{ $record->status_color }}">{{ $record->status_label }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Total</span><span>{{ $record->formatted_principal }}</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Sisa</span><span class="fw-bold">{{ $record->formatted_remaining }}</span>
                        </div>
                        <div class="progress my-2" style="height:6px;">
                            <div class="progress-bar bg-{{ $record->type_color }}" style="width: {{ $record->progress_percentage }}%;"></div>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <a href="{{ route('debts.show', $record) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-eye me-1"></i>Detail</a>
                            <a href="{{ route('debts.edit', $record) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                data-action="{{ route('debts.destroy', $record) }}" data-name="{{ $record->party_name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted py-4 mb-0">Belum ada catatan hutang/piutang.</p>
            @endforelse
        </div>

        <div class="p-3">
            {{ $records->links() }}
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
                    title: 'Hapus catatan?',
                    text: `Catatan "${name}" beserta seluruh riwayat pembayarannya akan dihapus permanen.`,
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
    </script>
@endpush
