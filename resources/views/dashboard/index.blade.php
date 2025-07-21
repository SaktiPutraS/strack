@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="section-title">
                <i class="bi bi-house"></i>Dashboard - {{ now()->format('F Y') }}
            </h1>
        </div>
    </div>

    <!-- Main Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Proyek Menunggu -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-clock stat-icon text-warning"></i>
                    <div class="stat-value">{{ $proyekMenunggu }}</div>
                    <div class="stat-label">Proyek Menunggu</div>
                </div>
            </div>
        </div>

        <!-- 2. Proyek Progress -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-play-circle stat-icon text-primary"></i>
                    <div class="stat-value">{{ $proyekProgress }}</div>
                    <div class="stat-label">Proyek Progress</div>
                </div>
            </div>
        </div>

        <!-- 3. Total Pendapatan -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-wallet2 stat-icon text-success"></i>
                    <div class="stat-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Pendapatan</div>
                </div>
            </div>
        </div>

        <!-- 4. Total Pengeluaran -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card">
                <div class="stat-card">
                                        <i class="bi bi-graph-down stat-icon text-danger"></i>
                    <div class="stat-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Pengeluaran</div>

                </div>
            </div>
        </div>
    </div>

    <!-- Financial Cards -->
    <div class="row g-3 mb-4">
        <!-- 5. Total Piutang -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-exclamation-triangle stat-icon text-warning"></i>
                    <div class="stat-value">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Piutang</div>
                </div>
            </div>
        </div>

        <!-- 6. Saldo Bank Octo -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-bank stat-icon text-info"></i>
                    <div class="stat-value">Rp {{ number_format($saldoOcto, 0, ',', '.') }}</div>
                    <div class="stat-label">Saldo Bank Octo</div>
                </div>
            </div>
        </div>

        <!-- 7. Saldo Emas -->
        <div class="col-12 col-md-12 col-lg-4">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-gem stat-icon text-warning"></i>
                    <div class="stat-value">Rp {{ number_format($saldoEmas, 0, ',', '.') }}</div>
                    <div class="stat-label">Saldo Emas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proyek Deadline Terdekat -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-calendar3"></i>Proyek Deadline Terdekat
                    </h5>

                    @if ($proyekDeadlineTermedekat->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Proyek</th>
                                        <th>Klien</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proyekDeadlineTermedekat as $project)
                                        <tr>
                                            <td>
                                                <strong class="text-lilac">{{ $project->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $project->type }}</small>
                                            </td>
                                            <td>{{ $project->client->name }}</td>
                                            <td>{{ $project->deadline->format('d M Y') }}</td>
                                            <td>
                                                @if ($project->status == 'WAITING')
                                                    <span class="badge bg-warning">MENUNGGU</span>
                                                @else
                                                    <span class="badge bg-primary">PROGRESS</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-folder2-open me-2"></i>Lihat Semua Proyek
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-check text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Tidak ada proyek dengan deadline mendekat</p>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Buat Proyek Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
