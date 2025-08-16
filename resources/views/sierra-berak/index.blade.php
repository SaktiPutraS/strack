@extends('layouts.app')
@section('title', 'Sierra Berak')

@section('content')
    <!-- Modern Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-calendar3"></i>
            Sierra Berak
        </h1>
        <p class="page-subtitle">Kalender pencatatan tanggal dan waktu</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-calendar-month text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $stats['total_catatan_bulan_ini'] }}</h3>
                    <small class="text-muted fw-semibold">Bulan Ini</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-calendar-date text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $stats['total_catatan_hari_ini'] }}</h3>
                    <small class="text-muted fw-semibold">Hari Ini</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-calendar-range text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $stats['total_catatan_tahun_ini'] }}</h3>
                    <small class="text-muted fw-semibold">Tahun Ini</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card luxury-card h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-graph-up text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $stats['rata_rata_per_hari'] }}</h3>
                    <small class="text-muted fw-semibold">Rata-rata/Hari</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="card luxury-card">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 d-flex align-items-center">
                    <div class="luxury-icon me-3">
                        <i class="bi bi-calendar3 text-purple"></i>
                    </div>
                    Kalender {{ $currentDate->locale('id')->isoFormat('MMMM YYYY') }}
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('sierra-berak.index', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}"
                        class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <a href="{{ route('sierra-berak.index') }}" class="btn btn-primary btn-sm">Hari Ini</a>
                    <a href="{{ route('sierra-berak.index', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}"
                        class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Calendar Header -->
            <div class="calendar-header">
                <div class="row g-0">
                    <div class="col day-header">Senin</div>
                    <div class="col day-header">Selasa</div>
                    <div class="col day-header">Rabu</div>
                    <div class="col day-header">Kamis</div>
                    <div class="col day-header">Jumat</div>
                    <div class="col day-header weekend">Sabtu</div>
                    <div class="col day-header weekend">Minggu</div>
                </div>
            </div>

            <!-- Calendar Body -->
            <div class="calendar-body">
                @foreach ($calendarData as $week)
                    <div class="row g-0 calendar-week">
                        @foreach ($week as $day)
                            <div class="col calendar-day
                                {{ !$day['is_current_month'] ? 'other-month' : '' }}
                                {{ $day['is_today'] ? 'today' : '' }}
                                {{ $day['is_weekend'] ? 'weekend' : '' }}"
                                data-date="{{ $day['date']->format('Y-m-d') }}" data-bs-toggle="modal" data-bs-target="#recordModal">

                                <div class="day-number">{{ $day['date']->day }}</div>

                                @if ($day['record_count'] > 0)
                                    <div class="records-indicator">
                                        <span class="badge bg-purple-light text-purple">
                                            {{ $day['record_count'] }} catatan
                                        </span>
                                    </div>

                                    @foreach ($day['records']->take(2) as $record)
                                        <div class="record-preview">
                                            <small class="text-purple fw-bold">{{ $record->formatted_waktu }}</small>
                                            <small class="text-muted d-block">{{ Str::limit($record->keterangan, 20) }}</small>
                                        </div>
                                    @endforeach

                                    @if ($day['record_count'] > 2)
                                        <small class="text-muted">+{{ $day['record_count'] - 2 }} lainnya</small>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal untuk Add/Edit Record -->
    <div class="modal fade" id="recordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content luxury-card border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-2 text-purple"></i>
                        Tambah Catatan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="recordForm">
                        <input type="hidden" id="recordId" name="record_id">

                        <div class="mb-3">
                            <label for="tanggal" class="form-label fw-semibold">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>

                        <div class="mb-3">
                            <label for="waktu" class="form-label fw-semibold">Waktu</label>
                            <input type="time" class="form-control" id="waktu" name="waktu" required>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan catatan..." required></textarea>
                        </div>
                    </form>

                    <!-- Existing Records for Selected Date -->
                    <div id="existingRecords" class="mt-4" style="display: none;">
                        <h6 class="fw-bold text-purple border-bottom pb-2">Catatan pada Tanggal Ini</h6>
                        <div id="recordsList"></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveRecord">
                        <i class="bi bi-check-circle me-1"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recordModal = new bootstrap.Modal(document.getElementById('recordModal'));
            const recordForm = document.getElementById('recordForm');
            const saveBtn = document.getElementById('saveRecord');
            const modalTitle = document.querySelector('#recordModal .modal-title');

            let selectedDate = '';
            let editingRecordId = null;

            // Calendar day click handler
            document.querySelectorAll('.calendar-day').forEach(day => {
                day.addEventListener('click', function() {
                    selectedDate = this.dataset.date;
                    document.getElementById('tanggal').value = selectedDate;

                    // Reset form
                    resetForm();

                    // Load existing records for this date
                    loadRecordsForDate(selectedDate);

                    recordModal.show();
                });
            });

            // Save record
            saveBtn.addEventListener('click', function() {
                if (recordForm.checkValidity()) {
                    saveRecord();
                } else {
                    recordForm.reportValidity();
                }
            });

            function resetForm() {
                recordForm.reset();
                editingRecordId = null;
                modalTitle.innerHTML = '<i class="bi bi-plus-circle me-2 text-purple"></i>Tambah Catatan';
                saveBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Simpan';
                document.getElementById('tanggal').value = selectedDate;

                // Set default time to current time
                const now = new Date();
                const timeString = now.getHours().toString().padStart(2, '0') + ':' +
                    now.getMinutes().toString().padStart(2, '0');
                document.getElementById('waktu').value = timeString;
            }

            function loadRecordsForDate(date) {
                fetch(`{{ route('sierra-berak.index') }}/date/${date}`)
                    .then(response => response.json())
                    .then(records => {
                        const recordsList = document.getElementById('recordsList');
                        const existingRecords = document.getElementById('existingRecords');

                        if (records.length > 0) {
                            existingRecords.style.display = 'block';
                            recordsList.innerHTML = records.map(record => `
                        <div class="card luxury-card mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bi bi-clock me-2 text-purple"></i>
                                            <strong class="text-purple">${record.formatted_waktu}</strong>
                                        </div>
                                        <p class="mb-0 text-muted">${record.keterangan}</p>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-outline-primary edit-record"
                                                data-record-id="${record.id}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-record"
                                                data-record-id="${record.id}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');

                            // Add event listeners for edit and delete buttons
                            recordsList.querySelectorAll('.edit-record').forEach(btn => {
                                btn.addEventListener('click', () => editRecord(btn.dataset.recordId));
                            });

                            recordsList.querySelectorAll('.delete-record').forEach(btn => {
                                btn.addEventListener('click', () => deleteRecord(btn.dataset.recordId));
                            });
                        } else {
                            existingRecords.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading records:', error);
                    });
            }

            function saveRecord() {
                const formData = new FormData(recordForm);
                const url = editingRecordId ?
                    `{{ route('sierra-berak.index') }}/${editingRecordId}` :
                    '{{ route('sierra-berak.store') }}';
                const method = editingRecordId ? 'PUT' : 'POST';

                // Convert FormData to regular object for fetch
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }

                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: result.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(result.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.message || 'Terjadi kesalahan saat menyimpan'
                        });
                    });
            }

            function editRecord(recordId) {
                fetch(`{{ route('sierra-berak.index') }}/${recordId}`)
                    .then(response => response.json())
                    .then(record => {
                        editingRecordId = recordId;

                        document.getElementById('tanggal').value = record.tanggal;
                        document.getElementById('waktu').value = record.waktu.substring(0, 5); // Remove seconds
                        document.getElementById('keterangan').value = record.keterangan;

                        modalTitle.innerHTML = '<i class="bi bi-pencil me-2 text-purple"></i>Edit Catatan';
                        saveBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Update';
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal memuat data catatan'
                        });
                    });
            }

            function deleteRecord(recordId) {
                Swal.fire({
                    title: 'Hapus Catatan?',
                    text: 'Apakah Anda yakin ingin menghapus catatan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ route('sierra-berak.index') }}/${recordId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: result.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Gagal menghapus catatan'
                                });
                            });
                    }
                });
            }
        });
    </script>

    <style>
        /* Calendar Styles */
        .calendar-header {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.15));
            border-bottom: 2px solid rgba(139, 92, 246, 0.1);
        }

        .day-header {
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            color: #374151;
            border-right: 1px solid rgba(139, 92, 246, 0.1);
            font-size: 0.9rem;
        }

        .day-header:last-child {
            border-right: none;
        }

        .day-header.weekend {
            color: #8B5CF6;
            background: rgba(139, 92, 246, 0.05);
        }

        .calendar-week {
            border-bottom: 1px solid rgba(139, 92, 246, 0.05);
        }

        .calendar-day {
            min-height: 120px;
            padding: 8px;
            border-right: 1px solid rgba(139, 92, 246, 0.05);
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .calendar-day:last-child {
            border-right: none;
        }

        .calendar-day:hover {
            background: rgba(139, 92, 246, 0.05);
        }

        .calendar-day.other-month {
            background: rgba(0, 0, 0, 0.02);
            color: #9CA3AF;
        }

        .calendar-day.today {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.1));
            border: 2px solid #8B5CF6;
        }

        .calendar-day.weekend {
            background: rgba(139, 92, 246, 0.02);
        }

        .calendar-day.today.weekend {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(168, 85, 247, 0.15));
        }

        .day-number {
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
            font-size: 1rem;
        }

        .calendar-day.other-month .day-number {
            color: #9CA3AF;
        }

        .calendar-day.today .day-number {
            color: #8B5CF6;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .records-indicator {
            margin-bottom: 4px;
        }

        .record-preview {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 6px;
            padding: 4px 6px;
            margin-bottom: 2px;
            border-left: 3px solid #8B5CF6;
        }

        .record-preview:last-of-type {
            margin-bottom: 4px;
        }

        /* Modal Styles */
        .modal-content.luxury-card {
            box-shadow: 0 20px 60px rgba(139, 92, 246, 0.2);
            border: 1px solid rgba(139, 92, 246, 0.1);
        }

        .form-control:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        /* Badge untuk record count */
        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.1) !important;
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        /* Responsive Calendar */
        @media (max-width: 768px) {
            .calendar-day {
                min-height: 80px;
                padding: 4px;
                font-size: 0.8rem;
            }

            .day-header {
                padding: 8px 4px;
                font-size: 0.75rem;
            }

            .day-number {
                font-size: 0.9rem;
            }

            .record-preview {
                padding: 2px 4px;
                font-size: 0.7rem;
            }

            .records-indicator .badge {
                font-size: 0.6rem;
                padding: 2px 6px;
            }
        }

        @media (max-width: 576px) {
            .calendar-day {
                min-height: 60px;
                padding: 2px;
            }

            .day-header {
                padding: 6px 2px;
                font-size: 0.7rem;
            }

            .day-number {
                font-size: 0.8rem;
                margin-bottom: 2px;
            }

            .record-preview {
                display: none;
                /* Hide detailed preview on very small screens */
            }

            .records-indicator .badge {
                font-size: 0.5rem;
                padding: 1px 4px;
            }
        }

        /* Loading animation */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Smooth transitions */
        .calendar-day,
        .luxury-card,
        .btn {
            transition: all 0.2s ease;
        }

        /* Focus styles for accessibility */
        .calendar-day:focus {
            outline: 2px solid #8B5CF6;
            outline-offset: 2px;
        }

        /* Print styles */
        @media print {

            .modal,
            .btn,
            .card-header {
                display: none !important;
            }

            .calendar-day {
                border: 1px solid #000 !important;
                page-break-inside: avoid;
            }
        }
    </style>
@endpush
