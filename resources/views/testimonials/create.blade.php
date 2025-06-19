@extends('layouts.app')
@section('title', 'Tandai Testimoni')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-check-square"></i>Tandai Testimoni
                </h1>
                <a href="{{ route('testimonials.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
            <p class="text-muted">Tandai bahwa proyek ini sudah memiliki testimoni</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('testimonials.store') }}" method="POST">
                        @csrf

                        <!-- Project Selection -->
                        <div class="mb-4">
                            <label for="project_id" class="form-label">
                                <i class="bi bi-folder2-open text-lilac me-2"></i>
                                Proyek <span class="text-danger">*</span>
                            </label>
                            <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">Pilih Proyek yang Sudah Selesai</option>
                                @if (isset($finishedProjects))
                                    @foreach ($finishedProjects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id', request('project')) == $project->id ? 'selected' : '' }}>
                                            {{ $project->title }} - {{ $project->client->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Hanya proyek dengan status SELESAI yang bisa ditandai memiliki testimoni</div>
                        </div>

                        <!-- Project Info (Dynamic) -->
                        <div id="project-info" style="display: none;">
                            <div class="p-3 bg-lilac-soft rounded mb-4">
                                <h6 class="text-lilac mb-3">Informasi Proyek</h6>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <small class="text-muted">Klien:</small>
                                        <div class="fw-bold" id="project-client">-</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Tipe Proyek:</small>
                                        <div class="fw-bold" id="project-type">-</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Nilai Proyek:</small>
                                        <div class="fw-bold text-success" id="project-value">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Simple Note -->
                        <div class="mb-4">
                            <label for="content" class="form-label">
                                <i class="bi bi-chat-quote text-lilac me-2"></i>
                                Catatan Testimoni
                            </label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="4"
                                placeholder="Catatan singkat tentang testimoni ini (opsional)">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Catatan internal untuk referensi (tidak ditampilkan di mana pun)</div>
                        </div>

                        <!-- Hidden fields with default values -->
                        <input type="hidden" name="rating" value="5">
                        <input type="hidden" name="is_published" value="0">

                        <!-- Confirmation -->
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Yang Akan Terjadi:</h6>
                            <ul class="mb-0">
                                <li>Proyek akan ditandai sebagai <strong>"sudah memiliki testimoni"</strong></li>
                                <li>Status ini akan muncul di daftar proyek dengan icon ✅</li>
                                <li>Membantu tracking proyek mana yang sudah/belum ada testimoninya</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('testimonials.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Tandai Sudah Ada Testimoni
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const projectSelect = document.getElementById('project_id');
            const projectInfo = document.getElementById('project-info');

            function updateProjectInfo() {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];

                if (selectedOption.value) {
                    // Extract data from option attributes (you'll need to add these in the blade)
                    const optionText = selectedOption.text;
                    const parts = optionText.split(' - ');

                    document.getElementById('project-client').textContent = parts[1] || '-';
                    document.getElementById('project-type').textContent = selectedOption.dataset.type || '-';
                    document.getElementById('project-value').textContent = selectedOption.dataset.value || '-';

                    projectInfo.style.display = 'block';
                } else {
                    projectInfo.style.display = 'none';
                }
            }

            projectSelect.addEventListener('change', updateProjectInfo);

            // Initial setup if project is pre-selected
            if (projectSelect.value) {
                updateProjectInfo();
            }
        });
    </script>
@endpush
