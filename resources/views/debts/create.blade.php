@extends('layouts.app')
@section('title', 'Catatan Baru')

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center gap-3">
            <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="h2 fw-bold text-purple mb-1"><i class="bi bi-plus-circle me-2"></i>Catatan Hutang / Piutang Baru</h1>
                <p class="text-muted mb-0">Catat hutang (kamu meminjam) atau piutang (kamu meminjamkan)</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card luxury-card border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('debts.store') }}" class="row g-3">
                        @csrf
                        @include('debts._form', ['debt' => null])

                        <div class="col-12 d-flex gap-2 justify-content-end pt-2">
                            <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary btn-lg">Batal</a>
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
