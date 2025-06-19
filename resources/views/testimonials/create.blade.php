@extends('layouts.app')
@section('title', 'Tambah Testimoni')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-star-fill"></i>Tambah Testimoni
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
                    <form action="{{ route('testimonials.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <!-- Project Selection -->
                            <div class="col-12">
                                <label for="project_id" class="form-label">
                                    <i class="bi bi-folder2-open text-lilac me-2"></i>
                                    Proyek <span class="text-danger">*</span>
                                </label>
                                <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                    <option value="">Pilih Proyek yang Sudah Selesai</option>
                                    @if (isset($finishedProjects))
                                        @foreach ($finishedProjects as $project)
                                            <option value="{{ $project->id }}" data-client="{{ $project->client->name }}"
                                                data-type="{{ $project->type }}" data-value="{{ $project->formatted_total_value }}"
                                                {{ old('project_id', request('project')) == $project->id ? 'selected' : '' }}>
                                                {{ $project->title }} - {{ $project->client->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Hanya proyek dengan status FINISHED yang bisa diberi testimoni</div>
                            </div>

                            <!-- Project Info (Dynamic) -->
                            <div class="col-12" id="project-info" style="display: none;">
                                <div class="p-3 bg-lilac-soft rounded">
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

                            <!-- Rating -->
                            <div class="col-md-6">
                                <label for="rating" class="form-label">
                                    <i class="bi bi-star text-lilac me-2"></i>
                                    Rating <span class="text-danger">*</span>
                                </label>
                                <div class="rating-input">
                                    <div class="d-flex align-items-center">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star rating-star me-1" data-rating="{{ $i }}"
                                                style="font-size: 1.5rem; cursor: pointer; color: #ddd;"></i>
                                        @endfor
                                        <span class="ms-2 text-muted" id="rating-text">Pilih rating</span>
                                    </div>
                                    <input type="hidden" name="rating" id="rating" value="{{ old('rating') }}" required>
                                </div>
                                @error('rating')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Klik bintang untuk memberikan rating 1-5</div>
                            </div>

                            <!-- Published Status -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-eye text-lilac me-2"></i>
                                    Status Publikasi
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1"
                                        {{ old('is_published') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_published">
                                        <span id="publish-label">Publikasikan testimoni</span>
                                    </label>
                                </div>
                                <div class="form-text">Testimoni yang dipublikasi akan tampil di halaman portfolio</div>
                            </div>

                            <!-- Testimonial Content -->
                            <div class="col-12">
                                <label for="content" class="form-label">
                                    <i class="bi bi-chat-quote text-lilac me-2"></i>
                                    Testimoni <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="6"
                                    placeholder="Tulis testimoni dari klien di sini..." required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="char-count">0</span>/500 karakter • Tip: Testimoni yang baik mencakup hasil yang dicapai dan kepuasan klien
                                </div>
                            </div>

                            <!-- Client Photo (Optional) -->
                            <div class="col-12">
                                <label for="client_photo" class="form-label">
                                    <i class="bi bi-camera text-lilac me-2"></i>
                                    Foto Klien (Opsional)
                                </label>
                                <input type="file" class="form-control @error('client_photo') is-invalid @enderror" id="client_photo"
                                    name="client_photo" accept="image/*">
                                @error('client_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB. Akan ditampilkan di testimoni publik.</div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="mt-4 p-3 bg-light rounded" id="testimonial-preview" style="display: none;">
                            <h6 class="text-muted mb-3">
                                <i class="bi bi-eye me-2"></i>
                                Preview Testimoni
                            </h6>
                            <div class="card bg-white border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-lilac text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 50px; height: 50px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0" id="preview-client">Nama Klien</h6>
                                            <small class="text-muted" id="preview-project-type">Tipe Proyek</small>
                                        </div>
                                    </div>

                                    <div class="mb-2" id="preview-rating">
                                        <!-- Stars will be added by JavaScript -->
                                    </div>

                                    <blockquote class="blockquote">
                                        <p class="mb-0" id="preview-content">"Isi testimoni akan muncul di sini..."</p>
                                    </blockquote>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('testimonials.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Simpan Testimoni
                                </button>
                            </div>
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
            const ratingStars = document.querySelectorAll('.rating-star');
            const ratingInput = document.getElementById('rating');
            const ratingText = document.getElementById('rating-text');
            const contentTextarea = document.getElementById('content');
            const charCount = document.getElementById('char-count');
            const projectInfo = document.getElementById('project-info');
            const testimonialPreview = document.getElementById('testimonial-preview');
            const publishCheckbox = document.getElementById('is_published');
            const publishLabel = document.getElementById('publish-label');

            // Rating functionality
            ratingStars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    ratingInput.value = rating;
                    updateStarDisplay(rating);
                    updateRatingText(rating);
                    updatePreview();
                });

                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.dataset.rating);
                    updateStarDisplay(rating, true);
                });
            });

            document.querySelector('.rating-input').addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value) || 0;
                updateStarDisplay(currentRating);
            });

            function updateStarDisplay(rating, isHover = false) {
                ratingStars.forEach((star, index) => {
                    const starRating = index + 1;
                    if (starRating <= rating) {
                        star.className = 'bi bi-star-fill rating-star me-1';
                        star.style.color = isHover ? '#ffc107' : '#f39c12';
                    } else {
                        star.className = 'bi bi-star rating-star me-1';
                        star.style.color = '#ddd';
                    }
                });
            }

            function updateRatingText(rating) {
                const ratingTexts = {
                    1: '1 Bintang - Kurang Puas',
                    2: '2 Bintang - Cukup',
                    3: '3 Bintang - Baik',
                    4: '4 Bintang - Sangat Baik',
                    5: '5 Bintang - Luar Biasa'
                };
                ratingText.textContent = ratingTexts[rating] || 'Pilih rating';
            }

            // Project selection
            function updateProjectInfo() {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];

                if (selectedOption.value) {
                    const client = selectedOption.dataset.client;
                    const type = selectedOption.dataset.type;
                    const value = selectedOption.dataset.value;

                    document.getElementById('project-client').textContent = client;
                    document.getElementById('project-type').textContent = type;
                    document.getElementById('project-value').textContent = value;

                    projectInfo.style.display = 'block';
                } else {
                    projectInfo.style.display = 'none';
                }

                updatePreview();
            }

            // Character count
            function updateCharCount() {
                const length = contentTextarea.value.length;
                charCount.textContent = length;

                if (length > 500) {
                    charCount.style.color = 'red';
                } else if (length > 400) {
                    charCount.style.color = 'orange';
                } else {
                    charCount.style.color = 'inherit';
                }
            }

            // Preview update
            function updatePreview() {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                const rating = parseInt(ratingInput.value) || 0;
                const content = contentTextarea.value.trim();

                if (selectedOption.value && rating > 0 && content) {
                    // Update preview content
                    document.getElementById('preview-client').textContent = selectedOption.dataset.client || 'Nama Klien';
                    document.getElementById('preview-project-type').textContent = selectedOption.dataset.type || 'Tipe Proyek';
                    document.getElementById('preview-content').textContent = `"${content}"`;

                    // Update preview rating
                    const previewRating = document.getElementById('preview-rating');
                    previewRating.innerHTML = '';
                    for (let i = 1; i <= 5; i++) {
                        const star = document.createElement('i');
                        star.className = i <= rating ? 'bi bi-star-fill text-warning' : 'bi bi-star text-muted';
                        previewRating.appendChild(star);
                    }

                    testimonialPreview.style.display = 'block';
                } else {
                    testimonialPreview.style.display = 'none';
                }
            }

            // Publish status
            function updatePublishLabel() {
                publishLabel.textContent = publishCheckbox.checked ? 'Akan dipublikasikan' : 'Simpan sebagai draft';
            }

            // Event listeners
            projectSelect.addEventListener('change', updateProjectInfo);
            contentTextarea.addEventListener('input', function() {
                updateCharCount();
                updatePreview();
            });
            publishCheckbox.addEventListener('change', updatePublishLabel);

            // Reset form
            window.resetForm = function() {
                if (confirm('Apakah Anda yakin ingin mereset form?')) {
                    document.querySelector('form').reset();
                    ratingInput.value = '';
                    updateStarDisplay(0);
                    updateRatingText(0);
                    updateCharCount();
                    projectInfo.style.display = 'none';
                    testimonialPreview.style.display = 'none';
                    updatePublishLabel();
                }
            }

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const rating = parseInt(ratingInput.value);
                const content = contentTextarea.value.trim();

                if (!rating || rating < 1 || rating > 5) {
                    e.preventDefault();
                    alert('Silakan berikan rating 1-5 bintang');
                    return false;
                }

                if (!content) {
                    e.preventDefault();
                    alert('Silakan tulis testimoni');
                    return false;
                }

                if (content.length > 500) {
                    e.preventDefault();
                    alert('Testimoni maksimal 500 karakter');
                    return false;
                }
            });

            // Initialize
            updateCharCount();
            updatePublishLabel();

            // Set initial rating if exists
            const oldRating = {{ old('rating', 0) }};
            if (oldRating > 0) {
                ratingInput.value = oldRating;
                updateStarDisplay(oldRating);
                updateRatingText(oldRating);
            }

            // Update project info if pre-selected
            if (projectSelect.value) {
                updateProjectInfo();
            }

            // Initial preview update
            updatePreview();
        });
    </script>
@endpush
