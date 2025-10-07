@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-house-door me-2"></i>Dashboard
                    </h1>
                    <p class="text-muted mb-0">{{ now()->format('d F Y') }} • Selamat datang kembali</p>
                </div>
                <div class="d-flex align-items-center">
                    <div class="luxury-icon me-2">
                        <i class="bi bi-calendar-day text-primary"></i>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary fs-6">
                        {{ now()->format('l') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Ringkasan Tugas Hari Ini
            </h5>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-purple h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-list-task text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $todayStats['total'] }}</h3>
                    <small class="text-muted fw-semibold">Total Tugas</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-warning h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $todayStats['pending'] }}</h3>
                    <small class="text-muted fw-semibold">Belum Dikerjakan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-info h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-hourglass-split text-info fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $todayStats['submitted'] }}</h3>
                    <small class="text-muted fw-semibold">Menunggu Validasi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 col-lg-4 col-md-6">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $todayStats['completed'] }}</h3>
                    <small class="text-muted fw-semibold">Selesai</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    @if ($todayStats['total'] > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card luxury-card border-0">
                    <div class="card-header bg-white border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1 d-flex align-items-center">
                                    <div class="luxury-icon me-3">
                                        <i class="bi bi-graph-up text-purple"></i>
                                    </div>
                                    Progress Hari Ini
                                </h5>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success fs-6">
                                {{ $progressPercentage }}% Selesai
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="progress mb-3" style="height: 12px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: {{ $progressPercentage }}%" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-3">
                                <small class="text-muted d-block">Pending</small>
                                <span class="fw-bold text-warning">{{ $todayStats['pending'] }}</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">Dikerjakan</small>
                                <span class="fw-bold text-info">{{ $todayStats['submitted'] }}</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">Selesai</small>
                                <span class="fw-bold text-success">{{ $todayStats['completed'] }}</span>
                            </div>
                            <div class="col-3">
                                <small class="text-muted d-block">Total</small>
                                <span class="fw-bold text-purple">{{ $todayStats['total'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-lightning text-warning"></i>
                        </div>
                        Aksi Cepat
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('tasks.user.index') }}" class="btn btn-outline-primary w-100 p-3">
                                <div class="d-flex align-items-center">
                                    <div class="luxury-icon me-3">
                                        <i class="bi bi-list-task text-primary"></i>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-0">Lihat Tugas Hari Ini</h6>
                                        <small class="text-muted">Daftar semua tugas yang perlu dikerjakan</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            @if ($todayStats['pending'] > 0)
                                <a href="{{ $nextTask ? route('tasks.user.show', $nextTask) : route('tasks.user.index') }}"
                                    class="btn btn-primary w-100 p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="luxury-icon me-3" style="background: rgba(255,255,255,0.2);">
                                            <i class="bi bi-play-circle text-white"></i>
                                        </div>
                                        <div class="text-start text-white">
                                            <h6 class="mb-0">Kerjakan Tugas</h6>
                                            <small class="opacity-75">Mulai mengerjakan tugas berikutnya</small>
                                        </div>
                                    </div>
                                </a>
                            @else
                                <div class="btn btn-success w-100 p-3" style="cursor: default;">
                                    <div class="d-flex align-items-center">
                                        <div class="luxury-icon me-3" style="background: rgba(255,255,255,0.2);">
                                            <i class="bi bi-check-circle text-white"></i>
                                        </div>
                                        <div class="text-start text-white">
                                            <h6 class="mb-0">Semua Tugas Selesai</h6>
                                            <small class="opacity-75">Bagus! Tidak ada tugas yang tertunda</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Tasks Overview -->
    @if ($todayTasks->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card luxury-card border-0">
                    <div class="card-header bg-white border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1 d-flex align-items-center">
                                    <div class="luxury-icon me-3">
                                        <i class="bi bi-calendar-day text-info"></i>
                                    </div>
                                    Tugas Hari Ini
                                </h5>
                            </div>
                            <a href="{{ route('tasks.user.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-3">
                            @foreach ($todayTasks->take(5) as $assignment)
                                <div
                                    class="d-flex align-items-center p-3 rounded mb-2 task-item {{ $assignment->isSubmitted() ? 'completed' : 'pending' }}">
                                    <div class="luxury-icon me-3" style="width: 40px; height: 40px;">
                                        @if ($assignment->isValidated())
                                            <i class="bi bi-check-circle text-success"></i>
                                        @elseif ($assignment->isSubmitted())
                                            <i class="bi bi-hourglass-split text-info"></i>
                                        @else
                                            <i class="bi bi-circle text-warning"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $assignment->task->name }}</h6>
                                        <small class="text-muted">{{ $assignment->task->schedule_text }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge bg-{{ $assignment->status_color }} bg-opacity-10 text-{{ $assignment->status_color }} border border-{{ $assignment->status_color }}">
                                            {{ $assignment->status_text }}
                                        </span>
                                        @if (!$assignment->isSubmitted())
                                            <div class="mt-1">
                                                <a href="{{ route('tasks.user.show', $assignment) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-play-circle me-1"></i>Kerjakan
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Tasks Today -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card luxury-card border-0">
                    <div class="card-body text-center py-5">
                        <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-emoji-smile text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Tidak Ada Tugas Hari Ini</h5>
                        <p class="text-muted mb-4">Selamat! Tidak ada tugas yang perlu dikerjakan untuk hari ini.</p>
                        <div class="d-flex justify-content-center">
                            <div class="luxury-icon" style="width: 60px; height: 60px;">
                                <i class="bi bi-cup-hot text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- NEW: Calendar Notes & Project Deadlines Section -->
    <div class="row">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                        <div>
                            <h4 class="fw-bold mb-1">
                                <i class="bi bi-calendar3 text-purple me-2"></i>Kalender & Deadline Proyek
                            </h4>
                            <p class="text-muted mb-0">Catatan pribadi dan deadline proyek dalam satu tampilan</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <button class="btn btn-sm btn-outline-primary" id="prevMonth">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <h5 class="mb-0 fw-bold text-purple" id="calendarTitle">
                                {{ $calendarData['currentMonth'] }}
                            </h5>
                            <button class="btn btn-sm btn-outline-primary" id="nextMonth">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Legend -->
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <div class="d-flex align-items-center">
                            <div class="legend-dot bg-success me-2"></div>
                            <small class="text-muted">Catatan Pribadi</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-dot bg-primary me-2"></div>
                            <small class="text-muted">Deadline Normal</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-dot bg-warning me-2"></div>
                            <small class="text-muted">Deadline Mendekat</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-dot bg-danger me-2"></div>
                            <small class="text-muted">Deadline Terlewat</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="calendar-container">
                        <div class="calendar-header">
                            <div class="calendar-day-header">Min</div>
                            <div class="calendar-day-header">Sen</div>
                            <div class="calendar-day-header">Sel</div>
                            <div class="calendar-day-header">Rab</div>
                            <div class="calendar-day-header">Kam</div>
                            <div class="calendar-day-header">Jum</div>
                            <div class="calendar-day-header">Sab</div>
                        </div>
                        <div class="calendar-body" id="calendarBody">
                            <!-- Calendar days will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Modal for Notes and Project Details -->
    <div class="modal fade" id="calendarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="calendarModalTitle">Detail Tanggal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Project Deadlines Section -->
                    <div id="projectDeadlinesSection" style="display: none;">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-calendar-event me-2"></i>Deadline Proyek
                        </h6>
                        <div id="projectDeadlinesList"></div>
                        <hr class="my-4">
                    </div>

                    <!-- Personal Notes Section -->
                    <h6 class="fw-bold text-success mb-3">
                        <i class="bi bi-sticky me-2"></i>Catatan Pribadi
                    </h6>
                    <form id="noteForm">
                        <input type="hidden" id="noteId">
                        <input type="hidden" id="noteDate">

                        <div class="mb-3">
                            <label for="noteTitle" class="form-label fw-semibold">Judul Catatan</label>
                            <input type="text" class="form-control" id="noteTitle" placeholder="Masukkan judul catatan...">
                        </div>

                        <div class="mb-3">
                            <label for="noteContent" class="form-label fw-semibold">Isi Catatan</label>
                            <textarea class="form-control" id="noteContent" rows="4" placeholder="Tulis catatan Anda di sini..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <div class="w-100 d-flex justify-content-between">
                        <button type="button" class="btn btn-danger" id="deleteNoteBtn" style="display: none;">
                            <i class="bi bi-trash me-1"></i>Hapus Catatan
                        </button>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-success" id="saveNoteBtn">
                                <i class="bi bi-save me-1"></i>Simpan Catatan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation for statistics cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Progress bar animation
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.width = '{{ $progressPercentage }}%';
                }, 500);
            }

            // Task item hover effects
            document.querySelectorAll('.task-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(139, 92, 246, 0.05)';
                    this.style.transition = 'background-color 0.2s ease';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });

            // NEW: Enhanced Calendar functionality with project deadlines
            let currentCalendarDate = new Date({{ $calendarData['currentYear'] }}, {{ $calendarData['currentMonthNumber'] - 1 }}, 1);
            let calendarNotes = @json($calendarNotes ?? []);
            let projectDeadlines = @json($projectDeadlines ?? []);

            function showSuccessToast(message) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    alert(message);
                }
            }

            function renderCalendar() {
                const calendarBody = document.getElementById('calendarBody');
                const calendarTitle = document.getElementById('calendarTitle');

                // Update title
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                calendarTitle.textContent = `${monthNames[currentCalendarDate.getMonth()]} ${currentCalendarDate.getFullYear()}`;

                // Clear calendar body
                calendarBody.innerHTML = '';

                // Get first day of month and number of days
                const firstDay = new Date(currentCalendarDate.getFullYear(), currentCalendarDate.getMonth(), 1);
                const lastDay = new Date(currentCalendarDate.getFullYear(), currentCalendarDate.getMonth() + 1, 0);
                const firstDayOfWeek = firstDay.getDay(); // 0 = Sunday
                const daysInMonth = lastDay.getDate();

                // Add empty cells for days before the first day of month
                for (let i = 0; i < firstDayOfWeek; i++) {
                    const emptyCell = document.createElement('div');
                    emptyCell.className = 'calendar-day empty';
                    calendarBody.appendChild(emptyCell);
                }

                // Add days of month
                for (let day = 1; day <= daysInMonth; day++) {
                    const dayCell = document.createElement('div');
                    dayCell.className = 'calendar-day';
                    dayCell.dataset.date =
                        `${currentCalendarDate.getFullYear()}-${String(currentCalendarDate.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                    // Check if it's today
                    const today = new Date();
                    if (currentCalendarDate.getFullYear() === today.getFullYear() &&
                        currentCalendarDate.getMonth() === today.getMonth() &&
                        day === today.getDate()) {
                        dayCell.classList.add('today');
                    }

                    let dayContent = `<span class="day-number">${day}</span>`;
                    let hasContent = false;

                    // Check for project deadlines
                    if (projectDeadlines[day]) {
                        hasContent = true;
                        dayCell.classList.add('has-deadline');

                        const deadlines = Array.isArray(projectDeadlines[day]) ? projectDeadlines[day] : [projectDeadlines[day]];
                        const firstDeadline = deadlines[0];

                        // Determine deadline status for styling
                        if (firstDeadline.is_overdue) {
                            dayCell.classList.add('deadline-overdue');
                        } else if (firstDeadline.is_deadline_near) {
                            dayCell.classList.add('deadline-near');
                        } else {
                            dayCell.classList.add('deadline-normal');
                        }

                        dayContent += `<div class="deadline-indicator">
                                <i class="bi bi-calendar-event"></i>
                            </div>`;

                        if (deadlines.length === 1) {
                            dayContent += `<div class="deadline-preview">${firstDeadline.title}</div>`;
                        } else {
                            dayContent += `<div class="deadline-preview">${deadlines.length} proyek</div>`;
                        }
                    }

                    // Check for personal notes
                    if (calendarNotes[day]) {
                        hasContent = true;
                        dayCell.classList.add('has-note');
                        dayContent += `<div class="note-indicator">
                                <i class="bi bi-sticky-fill"></i>
                            </div>`;

                        if (!projectDeadlines[day]) {
                            dayContent += `<div class="note-preview">${calendarNotes[day].title}</div>`;
                        }
                    }

                    dayCell.innerHTML = dayContent;

                    // Add click event
                    dayCell.addEventListener('click', function() {
                        openCalendarModal(this.dataset.date, calendarNotes[day] || null, projectDeadlines[day] || null);
                    });

                    calendarBody.appendChild(dayCell);
                }
            }

            function openCalendarModal(date, note = null, deadlines = null) {
                const modal = new bootstrap.Modal(document.getElementById('calendarModal'));
                const modalTitle = document.getElementById('calendarModalTitle');
                const noteId = document.getElementById('noteId');
                const noteDate = document.getElementById('noteDate');
                const noteTitle = document.getElementById('noteTitle');
                const noteContent = document.getElementById('noteContent');
                const deleteBtn = document.getElementById('deleteNoteBtn');
                const projectSection = document.getElementById('projectDeadlinesSection');
                const projectList = document.getElementById('projectDeadlinesList');

                // Format date for display
                const dateObj = new Date(date + 'T00:00:00');
                const formattedDate = dateObj.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                modalTitle.textContent = formattedDate;
                noteDate.value = date;

                // Handle project deadlines
                if (deadlines) {
                    projectSection.style.display = 'block';
                    projectList.innerHTML = '';

                    const deadlinesArray = Array.isArray(deadlines) ? deadlines : [deadlines];
                    deadlinesArray.forEach(project => {
                        const statusColor = project.is_overdue ? 'danger' : (project.is_deadline_near ? 'warning' : 'primary');
                        const statusText = project.is_overdue ? 'Terlewat' : (project.is_deadline_near ? 'Mendekat' : 'Normal');

                        const projectCard = document.createElement('div');
                        projectCard.className = 'card mb-2 border-start border-4 border-' + statusColor;
                        projectCard.innerHTML = `
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">${project.title}</h6>
                                        <p class="text-muted small mb-1">
                                            <i class="bi bi-building me-1"></i>${project.client_name}
                                        </p>
                                        <p class="text-muted small mb-1">
                                            <i class="bi bi-tag me-1"></i>${project.type} • ${project.status}
                                        </p>
                                        ${project.remaining_amount > 0 ?
                                            `<p class="text-muted small mb-0">
                                                    <i class="bi bi-currency-dollar me-1"></i>Sisa: ${project.formatted_remaining_amount}
                                                </p>` : ''
                                        }
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-${statusColor} bg-opacity-10 text-${statusColor} border border-${statusColor} rounded-pill">
                                            ${statusText}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `;
                        projectList.appendChild(projectCard);
                    });
                } else {
                    projectSection.style.display = 'none';
                }

                // Handle personal notes
                if (note) {
                    noteId.value = note.id;
                    noteTitle.value = note.title;
                    noteContent.value = note.content || '';
                    deleteBtn.style.display = 'block';
                } else {
                    noteId.value = '';
                    noteTitle.value = '';
                    noteContent.value = '';
                    deleteBtn.style.display = 'none';
                }

                modal.show();
            }

            function saveNote() {
                const noteId = document.getElementById('noteId').value;
                const noteDate = document.getElementById('noteDate').value;
                const noteTitle = document.getElementById('noteTitle').value;
                const noteContent = document.getElementById('noteContent').value;

                if (!noteTitle.trim()) {
                    alert('Judul catatan harus diisi!');
                    return;
                }

                const url = noteId ? `/calendar-notes/${noteId}` : '/calendar-notes';
                const method = noteId ? 'PUT' : 'POST';

                const data = {
                    date: noteDate,
                    title: noteTitle,
                    content: noteContent,
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };

                if (noteId) {
                    data._method = 'PUT';
                }

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': data._token
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update calendar notes data
                            const day = new Date(noteDate).getDate();
                            calendarNotes[day] = data.note;

                            // Re-render calendar
                            renderCalendar();

                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('calendarModal')).hide();

                            // Show success message
                            showSuccessToast(data.message);
                        } else {
                            alert('Terjadi kesalahan: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyimpan catatan');
                    });
            }

            function deleteNote() {
                const noteId = document.getElementById('noteId').value;

                if (!noteId) return;

                if (!confirm('Apakah Anda yakin ingin menghapus catatan ini?')) {
                    return;
                }

                fetch(`/calendar-notes/${noteId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove from calendar notes data
                            const noteDate = document.getElementById('noteDate').value;
                            const day = new Date(noteDate).getDate();
                            delete calendarNotes[day];

                            // Re-render calendar
                            renderCalendar();

                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('calendarModal')).hide();

                            // Show success message
                            showSuccessToast(data.message);
                        } else {
                            alert('Terjadi kesalahan: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus catatan');
                    });
            }

            function loadCalendarData(year, month) {
                // Load notes
                fetch(`/calendar-notes/month/${year}/${month}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        calendarNotes = data.notes || [];
                        return loadProjectDeadlines(year, month);
                    })
                    .then(() => {
                        renderCalendar();
                    })
                    .catch(error => {
                        console.error('Error loading calendar data:', error);
                        renderCalendar();
                    });
            }

            function loadProjectDeadlines(year, month) {
                return fetch(`/projects/deadlines/month/${year}/${month}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        projectDeadlines = data.deadlines || [];
                    })
                    .catch(error => {
                        console.error('Error loading project deadlines:', error);
                        projectDeadlines = [];
                    });
            }

            // Event listeners
            document.getElementById('prevMonth').addEventListener('click', function() {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
                loadCalendarData(currentCalendarDate.getFullYear(), currentCalendarDate.getMonth() + 1);
            });

            document.getElementById('nextMonth').addEventListener('click', function() {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
                loadCalendarData(currentCalendarDate.getFullYear(), currentCalendarDate.getMonth() + 1);
            });

            document.getElementById('saveNoteBtn').addEventListener('click', saveNote);
            document.getElementById('deleteNoteBtn').addEventListener('click', deleteNote);

            // Initial render
            renderCalendar();
        });
    </script>

    <style>
        /* Calendar Styles */
        .calendar-container {
            max-width: 100%;
            overflow-x: auto;
        }

        .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            margin-bottom: 1px;
            background: rgba(139, 92, 246, 0.1);
            border-radius: 8px 8px 0 0;
            overflow: hidden;
        }

        .calendar-day-header {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.15));
            color: #374151;
            font-weight: 600;
            text-align: center;
            padding: 12px 8px;
            font-size: 0.875rem;
        }

        .calendar-body {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: rgba(139, 92, 246, 0.05);
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }

        .calendar-day {
            background: white;
            min-height: 100px;
            padding: 8px;
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .calendar-day:hover {
            background: rgba(139, 92, 246, 0.05);
            transform: scale(1.02);
        }

        .calendar-day.empty {
            cursor: default;
            background: rgba(139, 92, 246, 0.02);
        }

        .calendar-day.empty:hover {
            transform: none;
            background: rgba(139, 92, 246, 0.02);
        }

        .calendar-day.today {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.1));
            border: 2px solid #8B5CF6;
        }

        .calendar-day.has-deadline {
            border-left: 4px solid #3B82F6;
        }

        .calendar-day.deadline-overdue {
            border-left-color: #EF4444;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(220, 38, 38, 0.1));
        }

        .calendar-day.deadline-near {
            border-left-color: #F59E0B;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(217, 119, 6, 0.1));
        }

        .calendar-day.deadline-normal {
            border-left-color: #3B82F6;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(37, 99, 235, 0.1));
        }

        .calendar-day.has-note {
            border-right: 4px solid #10B981;
        }

        .calendar-day.has-note.has-deadline {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(5, 150, 105, 0.1));
        }

        .day-number {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            margin-bottom: 4px;
            z-index: 1;
        }

        .deadline-indicator,
        .note-indicator {
            font-size: 0.7rem;
            margin-bottom: 2px;
            z-index: 1;
        }

        .deadline-indicator {
            color: #3B82F6;
        }

        .deadline-overdue .deadline-indicator {
            color: #EF4444;
        }

        .deadline-near .deadline-indicator {
            color: #F59E0B;
        }

        .note-indicator {
            color: #10B981;
        }

        .deadline-preview,
        .note-preview {
            font-size: 0.7rem;
            color: #6B7280;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            width: 100%;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), rgba(168, 85, 247, 0.1));
            border-radius: 16px 16px 0 0;
        }

        .form-control:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        /* Existing styles */
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            transition: all 0.3s ease;
        }

        .stat-card-purple::before {
            background: linear-gradient(90deg, #8B5CF6, #A855F7);
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10B981, #059669);
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #FFC107, #FF9800);
        }

        .stat-card-info::before {
            background: linear-gradient(90deg, #06B6D4, #0891B2);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
        }

        .stat-card:hover::before {
            height: 6px;
        }

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

        .text-purple {
            color: #8B5CF6 !important;
        }

        .task-item {
            border: 1px solid rgba(139, 92, 246, 0.1);
            transition: all 0.3s ease;
        }

        .task-item.completed {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .task-item.pending {
            background: rgba(255, 193, 7, 0.05);
            border-color: rgba(255, 193, 7, 0.2);
        }

        .progress {
            border-radius: 10px;
            background-color: rgba(139, 92, 246, 0.1);
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 1s ease-in-out;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .luxury-card:hover {
                transform: none !important;
            }

            .calendar-day {
                min-height: 80px;
                padding: 6px;
            }

            .calendar-day-header {
                padding: 8px 4px;
                font-size: 0.75rem;
            }

            .day-number {
                font-size: 0.75rem;
            }

            .deadline-preview,
            .note-preview {
                font-size: 0.65rem;
                -webkit-line-clamp: 1;
            }

            .modal-lg {
                max-width: 95%;
            }
        }

        @media (max-width: 480px) {
            .calendar-day {
                min-height: 60px;
                padding: 4px;
            }

            .calendar-day-header {
                padding: 6px 2px;
                font-size: 0.7rem;
            }

            .legend-dot {
                width: 8px;
                height: 8px;
            }
        }
    </style>
@endpush
