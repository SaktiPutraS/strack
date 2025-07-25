@extends('layouts.app')
@section('title', 'Kerjakan Tugas')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-play-circle me-2"></i>Kerjakan Tugas
                    </h1>
                    <p class="text-muted mb-0">{{ $assignment->task->name }}</p>
                </div>
                <a href="{{ route('tasks.user.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <!-- Task Information Card -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-info-circle text-purple"></i>
                        </div>
                        Informasi Tugas
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Nama Tugas</label>
                                <div class="p-3 bg-light rounded">
                                    <h6 class="mb-0 fw-bold">{{ $assignment->task->name }}</h6>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Jadwal</label>
                                <div class="p-3 bg-light rounded">
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                        {{ $assignment->task->schedule_text }}
                                    </span>
                                    @if ($assignment->task->schedule === 'once' && $assignment->task->target_date)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                Target: {{ $assignment->task->target_date->format('d M Y') }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Tanggal</label>
                                <div class="p-3 bg-light rounded">
                                    <span class="fw-medium">{{ $assignment->assigned_date->format('d M Y') }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Status</label>
                                <div class="p-3 bg-light rounded">
                                    <span
                                        class="badge bg-{{ $assignment->status_color }} bg-opacity-10 text-{{ $assignment->status_color }} border border-{{ $assignment->status_color }}">
                                        {{ $assignment->status_text }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted">Deskripsi Tugas</label>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0">{{ $assignment->task->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($assignment->isSubmitted())
                <!-- Task Already Completed -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="luxury-icon me-3">
                                @if ($assignment->isValidated())
                                    <i class="bi bi-check-circle text-success"></i>
                                @else
                                    <i class="bi bi-hourglass-split text-info"></i>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold {{ $assignment->isValidated() ? 'text-success' : 'text-info' }} mb-2">
                                    {{ $assignment->isValidated() ? 'Tugas Selesai & Divalidasi' : 'Tugas Sudah Dikerjakan' }}
                                </h6>
                                <p class="text-muted mb-2">
                                    Tugas ini sudah dikerjakan dan dikirim pada {{ $assignment->submitted_at->format('d M Y H:i') }}.
                                </p>

                                @if ($assignment->isValidated())
                                    <p class="text-success mb-0">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Tugas sudah divalidasi oleh admin pada {{ $assignment->validated_at->format('d M Y H:i') }}
                                    </p>
                                @else
                                    <p class="text-warning mb-0">
                                        <i class="bi bi-hourglass-split me-1"></i>
                                        Tugas sedang menunggu validasi dari admin
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Details -->
                <div class="card luxury-card border-0">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-clipboard-check text-success"></i>
                            </div>
                            Detail Pengerjaan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Keterangan</label>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0">{{ $assignment->remarks }}</p>
                            </div>
                        </div>

                        @if ($assignment->attachment)
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">Lampiran</label>
                                <div class="p-3 bg-light rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                            <i class="bi bi-paperclip text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $assignment->attachment_name }}</h6>
                                            <a href="{{ route('tasks.download-attachment', $assignment) }}" class="btn btn-outline-primary">
                                                <i class="bi bi-download me-2"></i>Download File
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Waktu Pengerjaan</label>
                                <div class="p-2 bg-light rounded">
                                    <small class="fw-medium">{{ $assignment->submitted_at->format('d M Y H:i') }}</small>
                                </div>
                            </div>
                            @if ($assignment->validated_at)
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted">Waktu Validasi</label>
                                    <div class="p-2 bg-light rounded">
                                        <small class="fw-medium">{{ $assignment->validated_at->format('d M Y H:i') }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Task Form -->
                <form action="{{ route('tasks.user.submit', $assignment) }}" method="POST" enctype="multipart/form-data" id="task-submit-form">
                    @csrf

                    <div class="card luxury-card border-0 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <div class="luxury-icon me-3">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </div>
                                Form Pengerjaan Tugas
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label for="remarks" class="form-label fw-semibold">
                                    Keterangan Pengerjaan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="5" required
                                    placeholder="Jelaskan bagaimana Anda mengerjakan tugas ini, apa yang sudah dilakukan, dan hasil yang dicapai...">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Jelaskan secara detail apa yang sudah Anda kerjakan untuk tugas ini
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="attachment" class="form-label fw-semibold">Lampiran (Opsional)</label>
                                <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment"
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-paperclip me-1"></i>
                                    Upload foto hasil kerja, dokumen, atau file pendukung lainnya.<br>
                                    Format yang didukung: JPG, PNG, PDF, DOC, DOCX. Maksimal 10MB.
                                </div>
                            </div>

                            <!-- File Preview -->
                            <div id="file-preview" class="mb-4" style="display: none;">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                                <i class="bi bi-file-earmark text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1" id="file-name"></h6>
                                                <small class="text-muted" id="file-size"></small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning Card -->
                    <div class="card luxury-card border-0 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start">
                                <div class="luxury-icon me-3">
                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-warning mb-2">Perhatian</h6>
                                    <ul class="text-muted mb-0 small">
                                        <li>Pastikan keterangan yang Anda isi sudah lengkap dan jelas</li>
                                        <li>Setelah mengirim, tugas tidak dapat diubah lagi</li>
                                        <li>Tugas akan diperiksa dan divalidasi oleh admin</li>
                                        <li>Lampiran akan membantu admin memvalidasi hasil kerja Anda</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card luxury-card border-0">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <a href="{{ route('tasks.user.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                    <i class="bi bi-send me-2"></i>Kirim Tugas
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // File upload preview
            const fileInput = document.getElementById('attachment');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        fileName.textContent = file.name;
                        fileSize.textContent = formatFileSize(file.size);
                        filePreview.style.display = 'block';

                        // Update file icon based on type
                        const fileIcon = filePreview.querySelector('.bi-file-earmark');
                        if (file.type.startsWith('image/')) {
                            fileIcon.className = 'bi bi-file-earmark-image text-success';
                        } else if (file.type === 'application/pdf') {
                            fileIcon.className = 'bi bi-file-earmark-pdf text-danger';
                        } else if (file.type.includes('word')) {
                            fileIcon.className = 'bi bi-file-earmark-word text-primary';
                        } else {
                            fileIcon.className = 'bi bi-file-earmark text-primary';
                        }
                    } else {
                        filePreview.style.display = 'none';
                    }
                });
            }

            // Form submission
            const form = document.getElementById('task-submit-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const remarks = document.getElementById('remarks').value.trim();

                    if (!remarks) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Keterangan Wajib Diisi',
                            text: 'Silakan isi keterangan pengerjaan tugas terlebih dahulu.',
                            confirmButtonColor: '#8B5CF6'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Kirim Tugas?',
                        text: 'Yakin ingin mengirim tugas ini? Setelah dikirim tidak dapat diubah lagi.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#8B5CF6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Kirim!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const submitBtn = document.getElementById('submit-btn');
                            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengirim...';
                            submitBtn.disabled = true;

                            form.submit();
                        }
                    });
                });
            }

            // SweetAlert untuk session messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#8B5CF6',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#8B5CF6'
                });
            @endif

            // Character counter for remarks
            const remarksTextarea = document.getElementById('remarks');
            if (remarksTextarea) {
                const counterDiv = document.createElement('div');
                counterDiv.className = 'form-text text-end';
                counterDiv.id = 'remarks-counter';
                remarksTextarea.parentNode.appendChild(counterDiv);

                function updateCounter() {
                    const length = remarksTextarea.value.length;
                    counterDiv.innerHTML = `<small class="text-muted">${length} karakter</small>`;

                    if (length < 10) {
                        counterDiv.innerHTML += ' <small class="text-warning">(minimal 10 karakter)</small>';
                    }
                }

                remarksTextarea.addEventListener('input', updateCounter);
                updateCounter();
            }
        });

        function removeFile() {
            document.getElementById('attachment').value = '';
            document.getElementById('file-preview').style.display = 'none';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>

    <style>
        .luxury-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(139, 92, 246, 0.08);
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .luxury-card:hover {
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
            transform: translateY(-2px);
        }

        .luxury-icon {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.15));
            border-radius: 12px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
        }

        .form-control:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* File upload styling */
        .form-control[type="file"]::-webkit-file-upload-button {
            background: linear-gradient(135deg, #8B5CF6, #A855F7);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin-right: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-control[type="file"]::-webkit-file-upload-button:hover {
            background: linear-gradient(135deg, #7C3AED, #9333EA);
        }

        /* Animation for completed task */
        @keyframes successGlow {
            0% {
                box-shadow: 0 0 5px rgba(16, 185, 129, 0.5);
            }

            50% {
                box-shadow: 0 0 20px rgba(16, 185, 129, 0.8);
            }

            100% {
                box-shadow: 0 0 5px rgba(16, 185, 129, 0.5);
            }
        }

        .luxury-card:has(.text-success) {
            animation: successGlow 2s ease-in-out infinite;
        }

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }
        }
    </style>
@endpush
