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
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Klien</th>
                                        <th>Kontak</th>
                                        <th>Total Proyek</th>
                                        <th>Nilai Total</th>
                                        <th>Sudah Dibayar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clients as $client)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <strong class="text-lilac">{{ $client->name }}</strong>
                                                        @if ($client->has_testimonial)
                                                            <span class="badge badge-success ms-2">
                                                                <i class="bi bi-star-fill"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ $client->phone }}</div>
                                                @if ($client->email)
                                                    <small class="text-muted">{{ $client->email }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $client->projects->count() ?? 0 }}</td>
                                            <td>Rp {{ number_format($client->total_project_value ?? 0, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($client->total_paid ?? 0, 0, ',', '.') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ $client->whatsapp_link }}" target="_blank" class="btn btn-sm btn-success"
                                                        title="WhatsApp">
                                                        <i class="bi bi-whatsapp"></i>
                                                    </a>
                                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-secondary"
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

                        <!-- Pagination -->
                        @if (method_exists($clients, 'links'))
                            <div class="d-flex justify-content-center mt-4">
                                {{ $clients->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
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
