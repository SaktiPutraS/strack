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

    <!-- Project List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-calendar-event"></i>Proyek Terdekat
                    </h5>
                    @if ($upcomingDeadlines->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($upcomingDeadlines as $project)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-folder2-open text-lilac me-3 mt-1" style="font-size: 1.25rem;"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2 text-lilac">{{ $project->title }}</h6>
                                            <div class="mb-2">
                                                <span class="badge badge-lilac">
                                                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($project->deadline)->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="bi bi-person text-muted me-2"></i>
                                                <p class="mb-0 text-muted">{{ $project->client->name }}</p>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar3 text-muted me-2"></i>
                                                <small class="text-muted">Deadline:
                                                    {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-lilac-secondary" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada proyek dengan deadline mendatang</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
