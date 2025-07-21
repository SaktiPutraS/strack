@extends('layouts.app')
@section('title', 'Detail Pengeluaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-receipt"></i>Detail Pengeluaran
                </h1>
                <div class="btn-group">
                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="bi bi-info-circle"></i>Informasi Pengeluaran
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tanggal</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 text-lilac me-2"></i>
                                <strong>{{ $expense->expense_date->format('d M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Jumlah</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash text-lilac me-2"></i>
                                <strong class="text-danger fs-4">{{ $expense->formatted_amount }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Kategori</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tag text-lilac me-2"></i>
                                <span class="badge bg-secondary">{{ $expense->category_label }}</span>
                            </div>
                        </div>
                        @if ($expense->subcategory)
                            <div class="col-md-6">
                                <label class="form-label text-muted">Sub Kategori</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-tags text-lilac me-2"></i>
                                    <span class="badge bg-light text-dark">{{ $expense->subcategory_label }}</span>
                                </div>
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label text-muted">Deskripsi</label>
                            <div class="d-flex align-items-start">
                                <i class="bi bi-journal-text text-lilac me-2 mt-1"></i>
                                <p class="mb-0">{{ $expense->description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-muted mb-3">Informasi Tambahan</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <small class="text-muted">Dibuat pada:</small>
                                <div class="fw-bold">{{ $expense->created_at->format('d M Y H:i') }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Terakhir diperbarui:</small>
                                <div class="fw-bold">{{ $expense->updated_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash me-2"></i>Hapus Pengeluaran
                        </button>
                        <div>
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-list me-2"></i>Kembali ke List
                            </a>
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                        </div>
                    </div>

                    <!-- Delete Form -->
                    <form id="delete-form" action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
@endpush
