@extends('layouts.app')
@section('title', 'Maintenance')

@section('content')
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1"><i class="bi bi-tools me-2"></i>Maintenance</h1>
                    <p class="text-muted mb-0">Checklist perawatan (AC, motor, filter air, dll) - tandai selesai tiap dikerjakan</p>
                </div>
                <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Catatan Baru
                </a>
            </div>
        </div>
    </div>

    <div class="card luxury-card border-0">
        <div class="card-body p-4 border-bottom">
            {{-- Filter status --}}
            @php
                $tabs = [
                    '' => ['Semua', $counts['all']],
                    'perlu' => ['Perlu Dikerjakan', $counts['due']],
                    'terjadwal' => ['Terjadwal', $counts['scheduled']],
                    'selesai' => ['Selesai', $counts['done']],
                ];
            @endphp
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach ($tabs as $key => $tab)
                    <a href="{{ route('maintenance.index', array_filter(['status' => $key, 'search' => request('search')])) }}"
                        class="btn btn-sm {{ (string) $filter === (string) $key ? 'btn-purple text-white' : 'btn-outline-secondary' }}"
                        @if ((string) $filter === (string) $key) style="background:linear-gradient(135deg,#8B5CF6,#A855F7);border:none;" @endif>
                        {{ $tab[0] }} <span class="badge bg-light text-dark ms-1">{{ $tab[1] }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" class="row g-2">
                <input type="hidden" name="status" value="{{ $filter }}">
                <div class="col-9 col-md-10">
                    <input type="text" name="search" class="form-control form-control-lg" value="{{ request('search') }}"
                        placeholder="Cari nama tugas atau catatan...">
                </div>
                <div class="col-3 col-md-2 d-grid">
                    <button type="submit" class="btn btn-purple text-white btn-lg" style="background:linear-gradient(135deg,#8B5CF6,#A855F7);border:none;">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Desktop -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Status</th>
                        <th class="py-3">Nama Tugas</th>
                        <th class="py-3">Jadwal</th>
                        <th class="py-3">Terakhir</th>
                        <th class="py-3 text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td class="px-4"><span class="badge bg-{{ $task->status_color }}">{{ $task->status_label }}</span></td>
                            <td>
                                <a href="{{ route('maintenance.show', $task) }}" class="fw-semibold text-decoration-none text-dark">{{ $task->name }}</a>
                                @if ($task->notes)<div class="small text-muted">{{ \Illuminate\Support\Str::limit($task->notes, 50) }}</div>@endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $task->schedule_color }} me-1">{{ $task->schedule_type_label }}</span>
                                {{ $task->schedule_label }}
                                @if ($task->schedule_type === 'ODOMETER' && $task->next_km)
                                    <div class="small text-muted">berikutnya: {{ number_format($task->next_km, 0, ',', '.') }} km</div>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $task->last_done_label ?? '-' }}
                                @if ($task->schedule_type === 'ODOMETER' && $task->last_km !== null)
                                    <div>{{ number_format($task->last_km, 0, ',', '.') }} km</div>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <button type="button" class="btn btn-sm btn-success btn-selesai"
                                    data-complete="{{ route('maintenance.complete', $task) }}"
                                    data-name="{{ $task->name }}" data-type="{{ $task->schedule_type }}" title="Tandai selesai">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <a href="{{ route('maintenance.show', $task) }}" class="btn btn-sm btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('maintenance.edit', $task) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                    data-action="{{ route('maintenance.destroy', $task) }}" data-name="{{ $task->name }}" title="Hapus"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">Tidak ada tugas untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile -->
        <div class="d-lg-none p-3">
            @forelse ($tasks as $task)
                <div class="card luxury-card mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <a href="{{ route('maintenance.show', $task) }}" class="fw-bold text-decoration-none text-dark">{{ $task->name }}</a>
                            <span class="badge bg-{{ $task->status_color }}">{{ $task->status_label }}</span>
                        </div>
                        <div class="small mb-1">
                            <span class="badge bg-{{ $task->schedule_color }} me-1">{{ $task->schedule_type_label }}</span>{{ $task->schedule_label }}
                            @if ($task->schedule_type === 'ODOMETER' && $task->next_km)
                                <span class="text-muted">· berikutnya {{ number_format($task->next_km, 0, ',', '.') }} km</span>
                            @endif
                        </div>
                        @if ($task->last_done_label)<div class="small text-muted mb-1">Terakhir: {{ $task->last_done_label }}</div>@endif
                        @if ($task->notes)<div class="small text-muted">{{ $task->notes }}</div>@endif
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-success flex-fill btn-selesai"
                                data-complete="{{ route('maintenance.complete', $task) }}"
                                data-name="{{ $task->name }}" data-type="{{ $task->schedule_type }}">
                                <i class="bi bi-check-lg me-1"></i>Selesai
                            </button>
                            <a href="{{ route('maintenance.show', $task) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('maintenance.edit', $task) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                data-action="{{ route('maintenance.destroy', $task) }}" data-name="{{ $task->name }}"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted py-4 mb-0">Tidak ada tugas untuk filter ini.</p>
            @endforelse
        </div>
    </div>

    <form id="deleteForm" method="POST" class="d-none">@csrf @method('DELETE')</form>
    <form id="completeForm" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="odometer" id="completeOdometer">
    </form>
@endsection

@push('scripts')
    <script>
        // Hapus tugas
        document.querySelectorAll('.btn-hapus').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const action = this.dataset.action, name = this.dataset.name;
                Swal.fire({
                    title: 'Hapus catatan?',
                    text: `Catatan "${name}" beserta riwayatnya akan dihapus permanen.`,
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

        // Tandai selesai
        document.querySelectorAll('.btn-selesai').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const action = this.dataset.complete, name = this.dataset.name, type = this.dataset.type;
                const form = document.getElementById('completeForm');
                const odo = document.getElementById('completeOdometer');
                if (type === 'ODOMETER') {
                    Swal.fire({
                        title: `Selesai: ${name}`,
                        input: 'number',
                        inputLabel: 'Odometer sekarang (km)',
                        inputPlaceholder: 'mis. 14000',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        inputValidator: (v) => (!v || v < 0) ? 'Masukkan angka odometer' : undefined
                    }).then((r) => {
                        if (r.isConfirmed) { odo.value = r.value; form.action = action; form.submit(); }
                    });
                } else {
                    Swal.fire({
                        title: 'Tandai selesai?',
                        text: `"${name}" akan dicatat selesai hari ini.`,
                        icon: 'question', showCancelButton: true,
                        confirmButtonColor: '#198754', confirmButtonText: 'Ya, selesai',
                        cancelButtonText: 'Batal'
                    }).then((r) => {
                        if (r.isConfirmed) { odo.value = ''; form.action = action; form.submit(); }
                    });
                }
            });
        });
    </script>
@endpush
