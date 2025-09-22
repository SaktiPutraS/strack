@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-house-door me-2"></i>Dashboard
                    </h1>
                    @if (!$isMobile)
                        <p class="text-muted mb-0">{{ now()->format('F Y') }} • Selamat datang kembali</p>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Proyek Baru
                    </a>
                    <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-chart-bar me-1"></i>Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- TEST --}}
    <!-- Project Status & Cash Balance Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-kanban me-2 text-purple"></i>Status Proyek & Saldo Kas
            </h5>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-warning h-100 clickable-card" data-filter="status=WAITING">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $proyekMenunggu }}</h3>
                    <small class="text-muted fw-semibold">Menunggu</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-purple h-100 clickable-card" data-filter="status=PROGRESS">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-play-circle-fill text-purple fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-purple mb-1">{{ $proyekProgress }}</h3>
                    <small class="text-muted fw-semibold">Progress</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-primary h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-bank text-primary fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1 fs-6">{{ number_format($saldoBank, 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Bank Octo</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card luxury-card stat-card stat-card-success h-100">
                <div class="card-body text-center p-3">
                    <div class="luxury-icon mx-auto mb-2">
                        <i class="bi bi-cash-coin text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1 fs-6">{{ number_format($saldoCash, 0, ',', '.') }}</h3>
                    <small class="text-muted fw-semibold">Cash</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-bar-chart me-2 text-purple"></i>Statistik Keuangan
            </h5>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="col-12 col-lg-6">
            <div class="card luxury-card border-0 h-100">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-graph-up-arrow me-2 text-purple"></i>Pendapatan per Bulan
                    </h5>
                    <p class="text-muted mb-0">Total nilai proyek tahun {{ now()->year }}</p>
                </div>
                <div class="card-body">
                    <canvas id="monthlyRevenueChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-12 col-lg-6">
            <div class="card luxury-card border-0 h-100">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-0">Asset</h5>
                    <p class="text-muted mb-0">Total: <strong>Rp. {{ number_format($pieData['total']) }}</strong></p>
                </div>
                <div class="card-body position-relative">
                    <canvas id="pieChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Income vs Expense Chart -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-graph-up me-2 text-purple"></i>Pendapatan & Pengeluaran Mingguan
                    </h5>
                    <p class="text-muted mb-0">Akumulasi per minggu tahun ini (dimulai Juli 2025)</p>
                </div>
                <div class="card-body">
                    <canvas id="lineChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Notes & Project Deadlines Section -->
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
            // Clickable cards navigation
            const clickableCards = document.querySelectorAll('.clickable-card');

            clickableCards.forEach(card => {
                card.style.cursor = 'pointer';

                // Touch feedback for mobile
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                    this.style.transition = 'transform 0.1s ease';
                }, {
                    passive: true
                });

                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                    this.style.transition = 'transform 0.2s ease';
                }, {
                    passive: true
                });

                // Click handler
                card.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    if (filter) {
                        // Add loading state
                        this.style.opacity = '0.7';

                        // Navigate
                        setTimeout(() => {
                            window.location.href = `{{ route('projects.index') }}?${filter}`;
                        }, 100);
                    }
                });

                // Hover effect for desktop
                card.addEventListener('mouseenter', function() {
                    if (window.innerWidth > 768) {
                        this.style.transform = 'translateY(-4px)';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    if (window.innerWidth > 768) {
                        this.style.transform = 'translateY(0)';
                    }
                });
            });

            // Charts initialization (Monthly Revenue Chart)
            const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
            const monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
                type: 'bar',
                data: {
                    labels: @json(collect($monthlyRevenueData)->pluck('month')),
                    datasets: [{
                        label: 'Nilai Proyek',
                        data: @json(collect($monthlyRevenueData)->pluck('project_value')),
                        backgroundColor: 'rgba(139, 92, 246, 0.8)',
                        borderColor: '#8B5CF6',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const monthData = @json($monthlyRevenueData)[context.dataIndex];
                                    return 'Total: ' + monthData.formatted_value;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000000) {
                                        return 'Rp ' + (value / 1000000000).toFixed(1) + 'M';
                                    } else if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'Rb';
                                    }
                                    return 'Rp ' + value;
                                }
                            },
                            grid: {
                                color: 'rgba(139, 92, 246, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Line Chart
            const lineCtx = document.getElementById('lineChart').getContext('2d');
            const lineChart = new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: @json(collect($weeklyData)->pluck('week')),
                    datasets: [{
                        label: 'Pendapatan',
                        data: @json(collect($weeklyData)->pluck('income')),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }, {
                        label: 'Pengeluaran',
                        data: @json(collect($weeklyData)->pluck('expense')),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    const index = tooltipItems[0].dataIndex;
                                    const weekData = @json($weeklyData)[index];
                                    return weekData.start_date + ' - ' + weekData.end_date;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });

            // Pie Chart
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            const pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: @json($pieData['labels']),
                    datasets: [{
                        data: @json($pieData['data']),
                        backgroundColor: @json($pieData['colors']),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label.split(':')[0] || '';
                                    const value = context.raw || 0;
                                    return `${label}: Rp ${value.toLocaleString('id-ID')}`;
                                }
                            }
                        }
                    }
                }
            });

            // Enhanced Calendar functionality with project deadlines
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
                                        <div class="mt-2">
                                            <a href="${project.url}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="bi bi-arrow-up-right-square me-1"></i>Detail
                                            </a>
                                        </div>
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

            // Add enhanced CSS styles
            const style = document.createElement('style');
            style.textContent = `
                /* Enhanced Calendar Styles */
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

                /* Enhanced deadline styling */
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

                .deadline-indicator, .note-indicator {
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

                .deadline-preview, .note-preview {
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

                /* Legend styles */
                .legend-dot {
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                    display: inline-block;
                }

                /* Enhanced modal styling */
                .modal-lg {
                    max-width: 600px;
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

                @media (max-width: 768px) {
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

                    .deadline-preview, .note-preview {
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

                /* Existing dashboard styles */
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }

                .luxury-card {
                    transition: all 0.3s ease;
                }

                .clickable-card:hover {
                    cursor: pointer;
                }

                @media (max-width: 768px) {
                    .luxury-card:hover {
                        transform: none !important;
                    }
                }

                .bg-purple {
                    background: linear-gradient(135deg, #8B5CF6, #A855F7) !important;
                }

                .stat-card {
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(20px);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    border-radius: 16px;
                    padding: 1.5rem;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                    height: 100%;
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

                .stat-card-warning::before {
                    background: linear-gradient(90deg, #FFC107, #FF9800);
                }

                .stat-card-purple::before {
                    background: linear-gradient(90deg, #8B5CF6, #A855F7);
                }

                .stat-card-primary::before {
                    background: linear-gradient(90deg, #3B82F6, #2563EB);
                }

                .stat-card-success::before {
                    background: linear-gradient(90deg, #10B981, #059669);
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

                .bg-purple-light {
                    background-color: rgba(139, 92, 246, 0.1) !important;
                }

                .border-purple {
                    border-color: rgba(139, 92, 246, 0.3) !important;
                }

                .card-body canvas {
                    max-height: 300px;
                }

                #monthlyRevenueChart {
                    padding: 10px;
                }

                @media (max-width: 768px) {
                    .card-body canvas {
                        max-height: 200px;
                    }

                    .luxury-card .card-header h5 {
                        font-size: 1rem;
                    }

                    .luxury-card .card-header p {
                        font-size: 0.85rem;
                    }
                }

                .badge {
                    padding: 0.5em 0.75em;
                    font-weight: 600;
                    letter-spacing: 0.5px;
                    border-radius: 8px;
                }

                .fs-7 {
                    font-size: 0.8rem;
                }

                .clickable-card:active {
                    transform: scale(0.98);
                }

                @media (hover: none) and (pointer: coarse) {
                    .stat-card:hover {
                        transform: none !important;
                        box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08) !important;
                    }

                    .luxury-card:hover {
                        transform: none !important;
                        box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08) !important;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
@endpush
