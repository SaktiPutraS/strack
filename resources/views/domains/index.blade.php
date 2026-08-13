@extends('layouts.app')
@section('title', 'Domain & Hosting')

@section('content')
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-globe2 me-2"></i>Domain &amp; Hosting
                    </h1>
                    <p class="text-muted mb-0">Pendataan domain &amp; pengingat perpanjangan sebelum kedaluwarsa</p>
                </div>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('domains.sync') }}" id="syncForm">
                        @csrf
                        <button type="button" class="btn btn-outline-primary" onclick="confirmSync()">
                            <i class="bi bi-arrow-repeat me-2"></i>Sync dari Hosting
                        </button>
                    </form>
                    <a href="{{ route('domains.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Domain
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <!-- Ringkasan -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2"><i class="bi bi-globe text-purple fs-4"></i></div>
                    <h3 class="fw-bold mb-1" style="font-size:clamp(1.1rem,5vw,1.5rem);">{{ $summary['total'] }}</h3>
                    <small class="text-muted fw-semibold">Total Domain</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2"><i class="bi bi-hdd-network text-info fs-4"></i></div>
                    <h3 class="fw-bold text-info mb-1" style="font-size:clamp(1.1rem,5vw,1.5rem);">{{ $summary['hosted'] }}</h3>
                    <small class="text-muted fw-semibold">Di Hosting</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2"><i class="bi bi-clock-history text-warning fs-4"></i></div>
                    <h3 class="fw-bold text-warning mb-1" style="font-size:clamp(1.1rem,5vw,1.5rem);">{{ $summary['expiring'] }}</h3>
                    <small class="text-muted fw-semibold">Akan Habis (&le;30 hari)</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2"><i class="bi bi-exclamation-octagon text-danger fs-4"></i></div>
                    <h3 class="fw-bold text-danger mb-1" style="font-size:clamp(1.1rem,5vw,1.5rem);">{{ $summary['expired'] }}</h3>
                    <small class="text-muted fw-semibold">Kedaluwarsa</small>
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
                        placeholder="Cari domain, provider, klien, atau catatan...">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-lg">
                        <option value="">Semua Status</option>
                        <option value="EXPIRED" {{ request('status') === 'EXPIRED' ? 'selected' : '' }}>Kedaluwarsa</option>
                        <option value="EXPIRING_SOON" {{ request('status') === 'EXPIRING_SOON' ? 'selected' : '' }}>Akan Habis</option>
                        <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                        <option value="UNKNOWN" {{ request('status') === 'UNKNOWN' ? 'selected' : '' }}>Tanpa Tanggal</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn text-white btn-lg"
                        style="background:linear-gradient(135deg,#8B5CF6,#A855F7);border:none;">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if ($domains->count() > 0)
                <!-- Desktop -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">Domain</th>
                                <th class="px-4 py-3 border-0">Klien / Project</th>
                                <th class="px-4 py-3 border-0">Provider</th>
                                <th class="px-4 py-3 border-0">Kedaluwarsa</th>
                                <th class="px-4 py-3 border-0">Biaya Perpanjang</th>
                                <th class="px-4 py-3 border-0 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($domains as $domain)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="fw-bold text-dark">{{ $domain->name }}</div>
                                        @if ($domain->is_hosted)
                                            <small class="text-info"><i class="bi bi-hdd-network me-1"></i>Di hosting</small>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($domain->client)
                                            <div><i class="bi bi-person me-1 text-muted"></i>{{ $domain->client->name }}</div>
                                        @endif
                                        @if ($domain->project)
                                            <small class="text-muted"><i class="bi bi-folder me-1"></i>{{ $domain->project->title }}</small>
                                        @endif
                                        @if (!$domain->client && !$domain->project)
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $domain->provider ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($domain->expires_at)
                                            <div>{{ $domain->expires_at->format('d M Y') }}</div>
                                            <span class="badge bg-{{ $domain->status_color }} bg-opacity-10 text-{{ $domain->status_color }} border border-{{ $domain->status_color }}">
                                                @if ($domain->status === 'EXPIRED')
                                                    Lewat {{ abs($domain->days_until_expiry) }} hari
                                                @else
                                                    {{ $domain->days_until_expiry }} hari lagi
                                                @endif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">Tanpa tanggal</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $domain->formatted_renewal_cost }}</td>
                                    <td class="px-4 py-3 text-end">
                                        <a href="{{ route('domains.edit', $domain) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('domains.destroy', $domain) }}"
                                            id="del-{{ $domain->id }}" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete({{ $domain->id }}, '{{ addslashes($domain->name) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile -->
                <div class="d-lg-none p-3">
                    @foreach ($domains as $domain)
                        <div class="card luxury-card mb-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="fw-bold text-dark flex-grow-1">{{ $domain->name }}</div>
                                    <span class="badge bg-{{ $domain->status_color }} bg-opacity-10 text-{{ $domain->status_color }} border border-{{ $domain->status_color }}">
                                        {{ $domain->status_label }}
                                    </span>
                                </div>
                                @if ($domain->client)
                                    <div><i class="bi bi-person me-1 text-muted"></i>{{ $domain->client->name }}</div>
                                @endif
                                @if ($domain->project)
                                    <div><small class="text-muted"><i class="bi bi-folder me-1"></i>{{ $domain->project->title }}</small></div>
                                @endif
                                <div class="mt-1">
                                    <i class="bi bi-calendar-x me-1 text-muted"></i>
                                    {{ $domain->expires_at ? $domain->expires_at->format('d M Y') : 'Tanpa tanggal' }}
                                    @if ($domain->expires_at)
                                        <small class="text-{{ $domain->status_color }}">
                                            ({{ $domain->status === 'EXPIRED' ? 'lewat ' . abs($domain->days_until_expiry) . ' hari' : $domain->days_until_expiry . ' hari lagi' }})
                                        </small>
                                    @endif
                                </div>
                                @if ($domain->provider)
                                    <div><small class="text-muted"><i class="bi bi-building me-1"></i>{{ $domain->provider }}</small></div>
                                @endif
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ route('domains.edit', $domain) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('domains.destroy', $domain) }}"
                                        id="delm-{{ $domain->id }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $domain->id }}, '{{ addslashes($domain->name) }}', true)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-globe2 fs-1 d-block mb-2 opacity-50"></i>
                    Belum ada domain. Tambah manual atau klik <strong>Sync dari Hosting</strong>.
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmSync() {
            Swal.fire({
                title: 'Sync domain dari hosting?',
                text: 'Nama domain di folder hosting akan ditambahkan bila belum ada. Tanggal kedaluwarsa tetap diisi manual.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8B5CF6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, sync',
                cancelButtonText: 'Batal'
            }).then((r) => {
                if (r.isConfirmed) document.getElementById('syncForm').submit();
            });
        }

        function confirmDelete(id, name, mobile = false) {
            Swal.fire({
                title: 'Hapus domain?',
                text: name,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((r) => {
                if (r.isConfirmed) document.getElementById((mobile ? 'delm-' : 'del-') + id).submit();
            });
        }
    </script>
@endpush
