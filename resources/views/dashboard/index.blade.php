@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="section-title">
                <i class="bi bi-speedometer2"></i>Dashboard
            </h1>
        </div>
    </div>

    <!-- Financial Stats -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-graph-up-arrow stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($stats['financial']['total_value'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Nilai Proyek</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-wallet2 stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($stats['financial']['total_paid'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Pendapatan</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-clock-history stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($stats['financial']['total_remaining'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Piutang</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="stat-card">
                    <i class="bi bi-piggy-bank stat-icon"></i>
                    <div class="stat-value">Rp {{ number_format($stats['savings']['total'], 0, ',', '.') }}</div>
                    <div class="stat-label">Tabungan 10%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Status Overview -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $stats['projects']['waiting'] }}</h3>
                    <p class="mb-0 text-muted">Menunggu</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $stats['projects']['progress'] }}</h3>
                    <p class="mb-0 text-muted">Progress</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $stats['projects']['finished'] }}</h3>
                    <p class="mb-0 text-muted">Selesai</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-danger">{{ $stats['projects']['cancelled'] }}</h3>
                    <p class="mb-0 text-muted">Dibatalkan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Deadlines -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-calendar-event"></i>Proyek Dengan Deadline Terdekat
                    </h5>

                    @if ($upcomingDeadlines->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Proyek</th>
                                        <th>Klien</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($upcomingDeadlines as $project)
                                        <tr>
                                            <td>
                                                <strong>{{ $project->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $project->type }}</small>
                                            </td>
                                            <td>{{ $project->client->name }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($project->deadline)->diffForHumans() }}
                                                </small>
                                            </td>
                                            <td>
                                                @if ($project->status == 'WAITING')
                                                    <span class="badge bg-warning">MENUNGGU</span>
                                                @elseif($project->status == 'PROGRESS')
                                                    <span class="badge bg-primary">PROGRESS</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada proyek dengan deadline mendatang</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
