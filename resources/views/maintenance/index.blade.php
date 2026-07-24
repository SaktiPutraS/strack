@extends('layouts.app')
@section('title', 'Maintenance')

@section('content')
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1"><i class="bi bi-tools me-2"></i>Maintenance</h1>
                    <p class="text-muted mb-0">Catatan tugas perawatan (AC, motor, filter air, dll) beserta jadwalnya</p>
                </div>
                <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Catatan Baru
                </a>
            </div>
        </div>
    </div>

    <div class="card luxury-card border-0">
        <div class="card-body p-4 border-bottom">
            <form method="GET" class="row g-2">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control form-control-lg" value="{{ request('search') }}"
                        placeholder="Cari nama tugas atau catatan...">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select form-select-lg">
                        <option value="">Semua Tipe</option>
                        <option value="TEXT" {{ request('type') === 'TEXT' ? 'selected' : '' }}>Catatan</option>
                        <option value="DATE" {{ request('type') === 'DATE' ? 'selected' : '' }}>Tanggal</option>
                        <option value="MONTH" {{ request('type') === 'MONTH' ? 'selected' : '' }}>Bulan</option>
                        <option value="YEAR" {{ request('type') === 'YEAR' ? 'selected' : '' }}>Tahun</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-purple text-white btn-lg" style="background:linear-gradient(135deg,#8B5CF6,#A855F7);border:none;">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Desktop -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Nama Tugas</th>
                        <th class="py-3">Jadwal</th>
                        <th class="py-3">Catatan</th>
                        <th class="py-3 text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td class="px-4 fw-semibold">{{ $task->name }}</td>
                            <td>
                                <span class="badge bg-{{ $task->schedule_color }} me-1">{{ $task->schedule_type_label }}</span>
                                {{ $task->schedule_label }}
                            </td>
                            <td class="text-muted">{{ $task->notes ?: '-' }}</td>
                            <td class="text-end px-4">
                                <a href="{{ route('maintenance.edit', $task) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                    data-action="{{ route('maintenance.destroy', $task) }}" data-name="{{ $task->name }}" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-5">Belum ada catatan maintenance.</td></tr>
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
                            <h6 class="fw-bold mb-0">{{ $task->name }}</h6>
                            <span class="badge bg-{{ $task->schedule_color }}">{{ $task->schedule_type_label }}</span>
                        </div>
                        <div class="small mb-1"><i class="bi bi-calendar-event me-1 text-muted"></i>{{ $task->schedule_label }}</div>
                        @if ($task->notes)
                            <div class="small text-muted">{{ $task->notes }}</div>
                        @endif
                        <div class="d-flex gap-2 mt-2">
                            <a href="{{ route('maintenance.edit', $task) }}" class="btn btn-sm btn-outline-secondary flex-fill"><i class="bi bi-pencil me-1"></i>Edit</a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                data-action="{{ route('maintenance.destroy', $task) }}" data-name="{{ $task->name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted py-4 mb-0">Belum ada catatan maintenance.</p>
            @endforelse
        </div>

        <div class="p-3">
            {{ $tasks->links() }}
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
                    text: `Catatan "${name}" akan dihapus permanen.`,
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
