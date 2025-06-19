@extends('layouts.app')
@section('title', 'Edit Testimoni')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-pencil-square"></i>Edit Testimoni
                </h1>
                <a href="{{ route('testimonials.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('testimonials.update', $testimonial) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Project Info (Read Only) -->
                        <div class="p-3 bg-lilac-soft rounded mb-4">
                            <h6 class="text-lilac mb-3">Informasi Proyek</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <small class="text-muted">Proyek:</small>
                                    <div class="fw-bold">{{ $testimonial->project->title }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Klien:</small>
                                    <div class="fw-bold">{{ $testimonial->project->client->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Tipe:</small>
                                    <div class="fw-bold">{{ $testimonial->project->type }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Status:</small>
                                    <span class="badge bg-success">{{ $testimonial->project->status }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Current Status -->
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle me-2"></i>Status Saat Ini:</h6>
                            <p class="mb-0">Proyek ini sudah ditandai <strong>memiliki testimoni</strong> sejak
                                {{ $testimonial->created_at->format('d M Y') }}</p>
                        </div>

                        <!-- Simple Note -->
                        <div class="mb-4">
                            <label for="content" class="form-label">
                                <i class="bi bi-chat-quote text-lilac me-2"></i>
                                Catatan Testimoni
                            </label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="4"
                                placeholder="Catatan singkat tentang testimoni ini (opsional)">{{ old('content', $testimonial->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Catatan internal untuk referensi (tidak ditampilkan di mana pun)</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <div>
                                <a href="{{ route('testimonials.index') }}" class="btn btn-secondary me-2">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash me-2"></i>Hapus Penanda
                                </button>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Catatan
                            </button>
                        </div>
                    </form>

                    <!-- Delete Form (Hidden) -->
                    <form id="delete-form" action="{{ route('testimonials.destroy', $testimonial) }}" method="POST" style="display: none;">
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
            if (confirm('Apakah Anda yakin ingin menghapus penanda testimoni ini?\n\nProyek akan ditandai sebagai belum memiliki testimoni.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
@endpush
