@extends('layouts.app')
@section('title', 'Edit Maintenance')

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="h2 fw-bold text-purple mb-1"><i class="bi bi-pencil-square me-2"></i>Edit Maintenance</h1>
                <p class="text-muted mb-0">Perbarui tugas perawatan &amp; jadwalnya</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card luxury-card border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('maintenance.update', $task) }}" class="row g-3">
                        @csrf
                        @method('PUT')
                        @include('maintenance._form', ['task' => $task])

                        <div class="col-12 d-flex gap-2 justify-content-end pt-2">
                            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary btn-lg">Batal</a>
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
