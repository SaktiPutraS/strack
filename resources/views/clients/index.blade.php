@extends('layouts.app')
@section('title', 'Daftar Klien')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-people"></i>Daftar Klien
                </h1>
                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Klien Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control" placeholder="Cari klien berdasarkan nama, telepon, atau email..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-person-lines-fill"></i>Klien ({{ $clients->total() ?? 0 }} total)
                    </h5>

                    @if (isset($clients) && $clients->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($clients as $client)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-lilac text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 50px; height: 50px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 text-lilac">{{ $client->name }}</h6>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ $client->whatsapp_link }}" target="_blank" class="btn btn-sm btn-success">
                                                        <i class="bi bi-whatsapp"></i>
                                                    </a>
                                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-2">
                                                <div class="col-md-4">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-telephone text-muted me-2"></i>
                                                        <small class="text-muted">{{ $client->phone }}</small>
                                                    </div>
                                                </div>
                                                @if ($client->email)
                                                    <div class="col-md-4">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-envelope text-muted me-2"></i>
                                                            <small class="text-muted">{{ $client->email }}</small>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($client->address)
                                                    <div class="col-md-4">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-geo-alt text-muted me-2"></i>
                                                            <small class="text-muted">{{ Str::limit($client->address, 30) }}</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-md-3">
                                                    <small class="text-muted">Total Proyek:</small>
                                                    <div class="fw-bold text-lilac">{{ $client->projects->count() ?? 0 }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Nilai Total:</small>
                                                    <div class="fw-bold text-success">Rp
                                                        {{ number_format($client->total_project_value ?? 0, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Sudah Dibayar:</small>
                                                    <div class="fw-bold text-primary">Rp {{ number_format($client->total_paid ?? 0, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Testimoni:</small>
                                                    <div>
                                                        @if ($client->has_testimonial)
                                                            <span class="badge badge-success">
                                                                <i class="bi bi-star-fill"></i> Ada
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">
                                                                <i class="bi bi-star"></i> Belum
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if (method_exists($clients, 'links'))
                            <div class="d-flex justify-content-center mt-4">
                                {{ $clients->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-x text-lilac-secondary" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada klien ditemukan</p>
                            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Klien Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
