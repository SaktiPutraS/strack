@extends('layouts.app')
@section('title', 'Detail Maintenance')

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h1 class="h3 fw-bold text-purple mb-1">
                        <span class="badge bg-{{ $task->status_color }} me-2">{{ $task->status_label }}</span>{{ $task->name }}
                    </h1>
                    <p class="text-muted mb-0">
                        <span class="badge bg-{{ $task->schedule_color }} me-1">{{ $task->schedule_type_label }}</span>{{ $task->schedule_label }}
                        @if ($task->schedule_type === 'ODOMETER' && $task->next_km)
                            · berikutnya {{ number_format($task->next_km, 0, ',', '.') }} km
                        @endif
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('maintenance.edit', $task) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
                <button type="button" class="btn btn-outline-danger btn-hapus"
                    data-action="{{ route('maintenance.destroy', $task) }}" data-name="{{ $task->name }}"><i class="bi bi-trash me-1"></i>Hapus</button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Tandai selesai -->
        <div class="col-lg-5">
            <div class="card luxury-card border-0 mb-4">
                <div class="card-body p-4">
                    @if ($task->notes)<p class="text-muted">{{ $task->notes }}</p>@endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Terakhir dikerjakan</span>
                        <span class="fw-semibold">{{ $task->last_done_label ?? 'Belum pernah' }}</span>
                    </div>
                    @if ($task->schedule_type === 'ODOMETER')
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Odometer terakhir</span>
                            <span class="fw-semibold">{{ $task->last_km !== null ? number_format($task->last_km, 0, ',', '.') . ' km' : '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Servis berikutnya</span>
                            <span class="fw-semibold">{{ $task->next_km ? number_format($task->next_km, 0, ',', '.') . ' km' : '-' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-check-circle me-2 text-purple"></i>Tandai Selesai</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('maintenance.complete', $task) }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label fw-semibold">Tanggal Dikerjakan</label>
                            <input type="date" name="done_at" class="form-control form-control-lg @error('done_at') is-invalid @enderror"
                                value="{{ old('done_at', date('Y-m-d')) }}">
                            @error('done_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @if ($task->schedule_type === 'ODOMETER')
                            <div class="col-12">
                                <label class="form-label fw-semibold">Odometer sekarang (km) <span class="text-danger">*</span></label>
                                <input type="number" name="odometer" min="0" step="1" required
                                    class="form-control form-control-lg @error('odometer') is-invalid @enderror"
                                    value="{{ old('odometer') }}" placeholder="mis. 14000">
                                @error('odometer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan</label>
                            <input type="text" name="notes" class="form-control @error('notes') is-invalid @enderror"
                                value="{{ old('notes') }}" placeholder="mis. ganti sekalian filter (opsional)">
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check-lg me-2"></i>Catat Selesai</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Riwayat -->
        <div class="col-lg-7">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-purple"></i>Riwayat Penyelesaian
                        <span class="badge bg-light text-dark ms-1">{{ $task->logs->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    @if ($task->schedule_type === 'ODOMETER')<th class="py-3 text-end">Odometer</th>@endif
                                    <th class="py-3">Catatan</th>
                                    <th class="py-3 text-end px-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($task->logs as $log)
                                    <tr>
                                        <td class="px-4">{{ $log->done_at->format('d M Y') }}</td>
                                        @if ($task->schedule_type === 'ODOMETER')
                                            <td class="text-end">{{ $log->odometer !== null ? number_format($log->odometer, 0, ',', '.') . ' km' : '-' }}</td>
                                        @endif
                                        <td class="text-muted">{{ $log->notes ?: '-' }}</td>
                                        <td class="text-end px-4">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                                data-action="{{ route('maintenance-logs.destroy', $log) }}"
                                                data-name="riwayat {{ $log->done_at->format('d M Y') }}"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-5">Belum ada riwayat. Klik "Catat Selesai" saat tugas dikerjakan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" class="d-none">@csrf @method('DELETE')</form>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-hapus').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const action = this.dataset.action, name = this.dataset.name;
                Swal.fire({
                    title: 'Hapus?', text: `Hapus ${name}?`,
                    icon: 'warning', showCancelButton: true,
                    confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
                }).then((r) => {
                    if (r.isConfirmed) {
                        const f = document.getElementById('deleteForm');
                        f.action = action; f.submit();
                    }
                });
            });
        });
    </script>
@endpush
