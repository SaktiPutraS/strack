@extends('layouts.app')
@section('title', 'Kalender')

@section('content')
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-calendar-week me-2"></i>Kalender
                    </h1>
                    <p class="text-muted mb-0">Agenda, todo, dan seluruh tanggal penting strack dalam satu tampilan</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" id="btnNewTodo">
                        <i class="bi bi-check2-square me-2"></i>Todo Baru
                    </button>
                    <button type="button" class="btn btn-primary" id="btnNewEvent">
                        <i class="bi bi-plus-circle me-2"></i>Agenda Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kalender -->
        <div class="col-12 col-lg-9">
            <div class="card luxury-card border-0">
                <div class="card-body p-2 p-md-3">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Panel samping -->
        <div class="col-12 col-lg-3 calendar-side">
            <!-- Filter sumber data -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-funnel text-purple me-2"></i>Tampilkan
                    </h6>
                    <div class="source-filters">
                        <label class="source-filter">
                            <input type="checkbox" class="form-check-input me-2" data-source="own" checked>
                            <span class="source-dot" style="background:#8B5CF6"></span>
                            <span>Agenda</span>
                        </label>
                        <label class="source-filter">
                            <input type="checkbox" class="form-check-input me-2" data-source="projects" checked>
                            <span class="source-dot" style="background:#3B82F6"></span>
                            <span>Deadline Proyek</span>
                        </label>
                        <label class="source-filter">
                            <input type="checkbox" class="form-check-input me-2" data-source="domains" checked>
                            <span class="source-dot" style="background:#0EA5E9"></span>
                            <span>Domain Kedaluwarsa</span>
                        </label>
                        <label class="source-filter">
                            <input type="checkbox" class="form-check-input me-2" data-source="maintenance" checked>
                            <span class="source-dot" style="background:#F59E0B"></span>
                            <span>Maintenance</span>
                        </label>
                        <label class="source-filter">
                            <input type="checkbox" class="form-check-input me-2" data-source="debts" checked>
                            <span class="source-dot" style="background:#EF4444"></span>
                            <span>Hutang Piutang</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Daftar todo -->
            <div class="card luxury-card border-0">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-check2-square text-purple me-2"></i>Todo
                        </h6>
                        <span class="badge bg-purple-light text-purple" id="todoCount">0</span>
                    </div>

                    <form id="quickTodoForm" class="input-group input-group-sm mb-3">
                        <input type="text" class="form-control" id="quickTodoTitle" placeholder="Tambah todo untuk hari ini..."
                            maxlength="500">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-plus-lg"></i></button>
                    </form>

                    <div id="todoList" class="todo-list">
                        <p class="text-muted small text-center py-3 mb-0">Memuat...</p>
                    </div>

                    <div class="border-top pt-2 mt-2">
                        <button type="button" class="btn btn-link btn-sm text-muted p-0 text-decoration-none"
                            id="toggleDoneList">
                            <i class="bi bi-chevron-right me-1" id="doneCaret"></i>Selesai terakhir
                        </button>
                        <div id="doneList" class="todo-list mt-2" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form agenda / todo -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content luxury-card border-0">
                <form id="eventForm">
                    <div class="modal-header border-0 p-4 pb-2">
                        <h5 class="modal-title fw-bold text-purple" id="eventModalTitle">Agenda Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 pt-2">
                        <input type="hidden" id="eventId">

                        <!-- Tipe -->
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="eventType" id="typeEvent" value="EVENT" checked>
                            <label class="btn btn-outline-primary" for="typeEvent">
                                <i class="bi bi-calendar-event me-1"></i>Agenda
                            </label>
                            <input type="radio" class="btn-check" name="eventType" id="typeTodo" value="TODO">
                            <label class="btn btn-outline-primary" for="typeTodo">
                                <i class="bi bi-check2-square me-1"></i>Todo
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="eventTitle" maxlength="500" required>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="eventAllDay" checked>
                            <label class="form-check-label" for="eventAllDay">Seharian (tanpa jam)</label>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-7">
                                <label class="form-label fw-semibold small">Tanggal mulai <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="eventStartDate" required>
                            </div>
                            <div class="col-5 time-field">
                                <label class="form-label fw-semibold small">Jam mulai</label>
                                <input type="time" class="form-control" id="eventStartTime">
                            </div>
                            <div class="col-7">
                                <label class="form-label fw-semibold small">Tanggal selesai</label>
                                <input type="date" class="form-control" id="eventEndDate">
                            </div>
                            <div class="col-5 time-field">
                                <label class="form-label fw-semibold small">Jam selesai</label>
                                <input type="time" class="form-control" id="eventEndTime">
                            </div>
                        </div>

                        <!-- Pengulangan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="repeatPreset">
                                <i class="bi bi-arrow-repeat me-1"></i>Ulangi
                            </label>
                            <select class="form-select" id="repeatPreset">
                                <option value="NONE">Tidak diulang</option>
                                <option value="DAILY">Setiap hari</option>
                                <option value="WEEKDAY">Setiap hari kerja (Sen-Jum)</option>
                                <option value="WEEKLY">Mingguan (pilih hari)</option>
                                <option value="MONTHLY">Bulanan (tanggal tertentu)</option>
                                <option value="YEARLY">Tahunan</option>
                                <option value="CUSTOM">Kustom...</option>
                            </select>

                            <div class="repeat-box mt-2" id="repeatOptions" style="display:none;">
                                <!-- Interval (hanya untuk pilihan Kustom) -->
                                <div class="row g-2 align-items-center mb-2" id="repeatIntervalRow"
                                    style="display:none;">
                                    <div class="col-auto"><span class="small fw-semibold">Ulangi tiap</span></div>
                                    <div class="col-3">
                                        <input type="number" class="form-control form-control-sm" id="repeatInterval"
                                            min="1" max="365" value="1">
                                    </div>
                                    <div class="col">
                                        <select class="form-select form-select-sm" id="repeatUnit">
                                            <option value="DAILY">hari</option>
                                            <option value="WEEKLY">minggu</option>
                                            <option value="MONTHLY">bulan</option>
                                            <option value="YEARLY">tahun</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Hari terpilih (pola mingguan) -->
                                <div class="mb-2" id="repeatDaysRow" style="display:none;">
                                    <div class="small fw-semibold mb-1">Pada hari</div>
                                    <div class="d-flex flex-wrap gap-1" id="repeatDays">
                                        @foreach ($dayNames as $index => $name)
                                            <button type="button" class="day-toggle" data-day="{{ $index }}">
                                                {{ $name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Tanggal terpilih (pola bulanan) -->
                                <div class="row g-2 align-items-center mb-2" id="repeatDomRow" style="display:none;">
                                    <div class="col-auto"><span class="small fw-semibold">Pada</span></div>
                                    <div class="col">
                                        <select class="form-select form-select-sm" id="repeatDayOfMonth">
                                            <option value="">tanggal yang sama dengan tanggal mulai</option>
                                            @for ($d = 1; $d <= 31; $d++)
                                                <option value="{{ $d }}">tanggal {{ $d }}</option>
                                            @endfor
                                            <option value="-1">hari terakhir bulan</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Batas akhir -->
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="small fw-semibold">Berakhir</span>
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="radio" name="repeatEnd"
                                            id="repeatEndNever" value="never" checked>
                                        <label class="form-check-label small" for="repeatEndNever">Tidak pernah</label>
                                    </div>
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="radio" name="repeatEnd" id="repeatEndOn"
                                            value="on">
                                        <label class="form-check-label small" for="repeatEndOn">Pada tanggal</label>
                                    </div>
                                    <input type="date" class="form-control form-control-sm" id="repeatUntil"
                                        style="max-width:170px;" disabled>
                                </div>

                                <p class="small text-muted mb-0 mt-2" id="repeatSummary"></p>
                            </div>
                        </div>

                        <div class="alert alert-light border small py-2 px-3 mb-3" id="seriesNotice"
                            style="display:none;">
                            <i class="bi bi-info-circle me-1"></i>
                            Perubahan berlaku untuk <strong>seluruh rangkaian</strong>, bukan satu tanggal saja.
                        </div>

                        <div class="alert alert-light border small py-2 px-3 mb-3" id="todoNotice"
                            style="display:none;">
                            <i class="bi bi-check2-square me-1"></i>
                            Todo tidak ditampilkan di kotak tanggal, hanya di panel <strong>Todo</strong>
                            sebelah kanan.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea class="form-control" id="eventDescription" rows="3"
                                placeholder="Rincian tambahan (opsional)"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Warna</label>
                            <div class="d-flex flex-wrap gap-2" id="colorPicker">
                                @foreach ($colors as $hex => $label)
                                    <button type="button" class="color-swatch" data-color="{{ $hex }}"
                                        style="background:{{ $hex }}" title="{{ $label }}"></button>
                                @endforeach
                            </div>
                            <input type="hidden" id="eventColor" value="{{ array_key_first($colors) }}">
                        </div>

                        <div class="form-check mb-0" id="doneWrapper" style="display:none;">
                            <input class="form-check-input" type="checkbox" id="eventIsDone">
                            <label class="form-check-label" for="eventIsDone">Sudah selesai</label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-outline-danger me-auto" id="btnDeleteEvent"
                            style="display:none;">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal detail data dari modul lain -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content luxury-card border-0">
                <div class="modal-header border-0 p-4 pb-2">
                    <h5 class="modal-title fw-bold text-purple" id="detailTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <p class="text-muted small mb-3" id="detailMeta"></p>
                    <div id="detailBody"></div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" class="btn btn-primary" id="detailLink">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Buka
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales-all.global.min.js"></script>

    <style>
        /* ── Penyesuaian FullCalendar ke tema ungu strack ── */
        #calendar {
            --fc-border-color: rgba(139, 92, 246, .12);
            --fc-today-bg-color: rgba(139, 92, 246, .07);
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: rgba(139, 92, 246, .04);
            --fc-list-event-hover-bg-color: rgba(139, 92, 246, .06);
        }

        #calendar .fc-toolbar-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #7C3AED;
        }

        #calendar .fc .fc-button {
            background: #fff;
            border: 1px solid rgba(139, 92, 246, .2);
            color: #6D28D9;
            font-weight: 600;
            font-size: .8rem;
            padding: .35rem .7rem;
            box-shadow: none;
            text-transform: capitalize;
        }

        #calendar .fc .fc-button:hover {
            background: rgba(139, 92, 246, .08);
            border-color: rgba(139, 92, 246, .35);
            color: #5B21B6;
        }

        #calendar .fc .fc-button-primary:not(:disabled).fc-button-active,
        #calendar .fc .fc-button-primary:not(:disabled):active {
            background: #8B5CF6;
            border-color: #8B5CF6;
            color: #fff;
        }

        #calendar .fc .fc-button:focus,
        #calendar .fc .fc-button:focus-visible {
            box-shadow: 0 0 0 .2rem rgba(139, 92, 246, .2);
        }

        #calendar .fc .fc-button-primary:disabled {
            background: rgba(139, 92, 246, .35);
            border-color: transparent;
        }

        #calendar .fc-col-header-cell {
            background: rgba(139, 92, 246, .05);
            padding: .5rem 0;
        }

        #calendar .fc-col-header-cell-cushion {
            color: #6D28D9;
            font-weight: 700;
            font-size: .88rem;
            text-decoration: none;
        }

        #calendar .fc-daygrid-day-number {
            font-size: .95rem;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
            padding: .35rem .5rem;
        }

        #calendar .fc-day-today .fc-daygrid-day-number {
            background: #8B5CF6;
            color: #fff;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 3px;
            padding: 0;
        }

        /* Beri ruang napas di dalam sel supaya kotak terasa lega */
        #calendar .fc-daygrid-day-frame {
            padding: 2px;
        }

        #calendar .fc-daygrid-day-events {
            margin-bottom: 2px;
        }

        #calendar .fc-event {
            border-radius: 6px;
            padding: 3px 6px;
            font-size: .82rem;
            font-weight: 600;
            line-height: 1.35;
            cursor: pointer;
            border: none;
            margin-bottom: 2px;
        }

        #calendar .fc-daygrid-more-link {
            font-size: .78rem;
            font-weight: 600;
            color: #7C3AED;
        }

        #calendar .fc-timegrid-slot {
            height: 2.4em;
        }

        #calendar .fc-list-event-title,
        #calendar .fc-list-event-time {
            font-size: .9rem;
        }

        #calendar .fc-event-done .fc-event-title {
            text-decoration: line-through;
            opacity: .85;
        }

        #calendar .fc-daygrid-event-dot {
            border-color: currentColor;
        }

        #calendar .fc-list-event:hover td {
            cursor: pointer;
        }

        #calendar .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1rem;
            gap: .5rem;
            flex-wrap: wrap;
        }

        /* ── Panel samping ── */
        .source-filter {
            display: flex;
            align-items: center;
            gap: .35rem;
            font-size: .85rem;
            font-weight: 500;
            color: #4B5563;
            padding: .3rem 0;
            cursor: pointer;
            margin-bottom: 0;
        }

        .source-filter input {
            margin-top: 0;
            cursor: pointer;
        }

        .source-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        @media (min-width: 992px) {
            .calendar-side {
                position: sticky;
                top: 1rem;
                align-self: flex-start;
            }
        }

        .todo-list {
            max-height: 340px;
            overflow-y: auto;
        }

        @media (min-width: 992px) {
            .todo-list {
                max-height: clamp(180px, 34vh, 460px);
            }
        }

        .todo-item {
            display: flex;
            align-items: flex-start;
            gap: .5rem;
            padding: .5rem .25rem;
            border-bottom: 1px solid rgba(139, 92, 246, .07);
        }

        .todo-item:last-child {
            border-bottom: none;
        }

        .todo-item .form-check-input {
            margin-top: .15rem;
            cursor: pointer;
            flex-shrink: 0;
        }

        .todo-body {
            flex: 1;
            min-width: 0;
            cursor: pointer;
        }

        .todo-title {
            font-size: .84rem;
            font-weight: 600;
            color: #374151;
            word-break: break-word;
        }

        .todo-item.done .todo-title {
            text-decoration: line-through;
            color: #9CA3AF;
        }

        .todo-meta {
            font-size: .72rem;
            color: #9CA3AF;
        }

        .color-swatch {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid transparent;
            outline: 2px solid transparent;
            padding: 0;
            transition: transform .15s ease;
        }

        .color-swatch:hover {
            transform: scale(1.1);
        }

        .color-swatch.selected {
            border-color: #fff;
            outline-color: #4B5563;
        }

        /* ── Blok pengaturan pengulangan ── */
        .repeat-box {
            background: rgba(139, 92, 246, .05);
            border: 1px solid rgba(139, 92, 246, .15);
            border-radius: .5rem;
            padding: .75rem;
        }

        .day-toggle {
            border: 1px solid rgba(139, 92, 246, .25);
            background: #fff;
            color: #6B7280;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            padding: .25rem .6rem;
            transition: all .15s ease;
        }

        .day-toggle:hover {
            border-color: rgba(139, 92, 246, .5);
            color: #6D28D9;
        }

        .day-toggle.active {
            background: #8B5CF6;
            border-color: #8B5CF6;
            color: #fff;
        }

        /* Kemunculan berulang: garis kiri tipis sebagai penanda */
        #calendar .fc-event-repeat {
            box-shadow: inset 3px 0 0 rgba(255, 255, 255, .55);
        }

        .todo-repeat {
            color: #8B5CF6;
            margin-left: .3rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: .45rem 0;
            border-bottom: 1px dashed rgba(139, 92, 246, .12);
            font-size: .87rem;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row span:first-child {
            color: #6B7280;
        }

        .detail-row span:last-child {
            font-weight: 600;
            color: #374151;
            text-align: right;
        }

        @media (max-width: 575.98px) {
            #calendar .fc-toolbar.fc-header-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            #calendar .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const routes = {
                feed: '{{ route('calendar.feed') }}',
                todos: '{{ route('calendar.todos') }}',
                store: '{{ route('calendar.events.store') }}',
                base: '{{ url('calendar/events') }}',
            };
            const initialDate = @json($initialDate);
            const DEFAULT_COLOR = '{{ array_key_first($colors) }}';
            const STORAGE_KEY = 'strack_calendar_sources';

            // ── Util ────────────────────────────────────────────────────────
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text == null ? '' : String(text);
                return div.innerHTML;
            }

            function todayStr() {
                const d = new Date();
                return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' +
                    String(d.getDate()).padStart(2, '0');
            }

            function toast(icon, title) {
                Swal.fire({
                    icon: icon,
                    title: title,
                    toast: true,
                    position: 'top-end',
                    timer: 2200,
                    showConfirmButton: false
                });
            }

            function request(url, method, body) {
                return fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: body ? JSON.stringify(body) : undefined
                }).then(function(res) {
                    return res.json().then(function(data) {
                        if (!res.ok || data.success === false) {
                            const msg = data.message || (data.errors ? Object.values(data.errors)[0][0] :
                                'Terjadi kesalahan');
                            throw new Error(msg);
                        }
                        return data;
                    });
                });
            }

            // ── Filter sumber data ──────────────────────────────────────────
            const sourceInputs = Array.from(document.querySelectorAll('[data-source]'));

            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                const allowed = saved.split(',');
                sourceInputs.forEach(function(input) {
                    input.checked = allowed.indexOf(input.dataset.source) !== -1;
                });
            }

            function activeSources() {
                return sourceInputs.filter(function(i) {
                    return i.checked;
                }).map(function(i) {
                    return i.dataset.source;
                });
            }

            sourceInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    localStorage.setItem(STORAGE_KEY, activeSources().join(','));
                    calendar.refetchEvents();
                });
            });

            // ── Kalender ────────────────────────────────────────────────────
            const isMobile = window.matchMedia('(max-width: 767.98px)').matches;
            const calendarEl = document.getElementById('calendar');

            /**
             * Tinggi kalender dibuat mengisi sisa layar supaya kotak tanggalnya besar.
             * Di layar kecil biarkan mengalir apa adanya (mode Agenda lebih enak digulir).
             */
            function calendarHeight() {
                if (window.matchMedia('(max-width: 991.98px)').matches) {
                    return 'auto';
                }
                const offsetTop = calendarEl.getBoundingClientRect().top + window.scrollY;
                const bottomGap = 28; // sisa ruang bawah biar tidak mepet
                return Math.max(560, Math.round(window.innerHeight - offsetTop - bottomGap));
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'id',
                timeZone: 'local',
                initialView: isMobile ? 'listWeek' : '{{ $initialView }}',
                initialDate: initialDate || undefined,
                height: calendarHeight(),
                firstDay: 0,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: isMobile ? 'dayGridMonth,listWeek' :
                        'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                buttonText: {
                    today: 'Hari ini',
                    month: 'Bulan',
                    week: 'Minggu',
                    day: 'Hari',
                    list: 'Agenda'
                },
                views: {
                    listWeek: {
                        buttonText: 'Agenda'
                    },
                    listMonth: {
                        buttonText: 'Agenda'
                    }
                },
                noEventsText: 'Tidak ada agenda pada rentang ini',
                allDayText: 'Seharian',
                moreLinkText: function(n) {
                    return '+' + n + ' lagi';
                },
                dayMaxEvents: true, // sesuaikan sendiri dengan tinggi sel
                navLinks: true,
                nowIndicator: true,
                selectable: true,
                selectMirror: true,
                editable: true,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                eventSources: [{
                    url: routes.feed,
                    extraParams: function() {
                        return {
                            sources: activeSources().join(',')
                        };
                    },
                    failure: function() {
                        toast('error', 'Gagal memuat data kalender');
                    }
                }],

                // Klik-seret pada area kosong = buat agenda baru
                select: function(info) {
                    const end = new Date(info.end.getTime() - 86400000);
                    const endStr = info.allDay ? isoDate(end) : null;
                    openForm({
                        type: 'EVENT',
                        startDate: info.startStr.substring(0, 10),
                        endDate: info.allDay && endStr > info.startStr.substring(0, 10) ? endStr :
                            null,
                        allDay: info.allDay,
                        startTime: info.allDay ? null : info.startStr.substring(11, 16),
                        endTime: info.allDay ? null : info.endStr.substring(11, 16),
                    });
                    calendar.unselect();
                },

                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    const props = info.event.extendedProps;
                    if (props.source === 'own') {
                        // Salin SELURUH extendedProps supaya aturan pengulangan
                        // ikut terbawa ke form (kalau tidak, edit satu kali klik
                        // akan menghapus polanya).
                        openForm(Object.assign({}, props, {
                            id: props.recordId,
                            title: info.event.title,
                        }));
                    } else {
                        openDetail(info.event);
                    }
                },

                eventDrop: function(info) {
                    saveMove(info);
                },
                eventResize: function(info) {
                    saveMove(info);
                },
            });

            calendar.render();

            // Hitung ulang setelah semua aset (font, CSS CDN) selesai dimuat, karena
            // posisi kalender bisa bergeser sedikit dan tingginya jadi kurang pas.
            window.addEventListener('load', function() {
                calendar.setOption('height', calendarHeight());
            });

            // Hitung ulang tinggi saat ukuran jendela berubah (mis. rotasi / resize)
            let resizeTimer = null;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    calendar.setOption('height', calendarHeight());
                }, 150);
            });

            function isoDate(date) {
                return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' +
                    String(date.getDate()).padStart(2, '0');
            }

            // Simpan hasil drag & drop / resize
            function saveMove(info) {
                const event = info.event;
                const allDay = event.allDay;
                const startDate = isoDate(event.start);
                let endDate = null;
                let startTime = null;
                let endTime = null;

                if (allDay) {
                    if (event.end) {
                        // `end` di FullCalendar bersifat eksklusif untuk acara seharian
                        const last = new Date(event.end.getTime() - 86400000);
                        const lastStr = isoDate(last);
                        if (lastStr > startDate) endDate = lastStr;
                    }
                } else {
                    startTime = String(event.start.getHours()).padStart(2, '0') + ':' + String(event.start
                        .getMinutes()).padStart(2, '0');
                    if (event.end) {
                        endTime = String(event.end.getHours()).padStart(2, '0') + ':' + String(event.end
                            .getMinutes()).padStart(2, '0');
                        const endDay = isoDate(event.end);
                        if (endDay > startDate) endDate = endDay;
                    }
                }

                request(routes.base + '/' + event.extendedProps.recordId + '/move', 'POST', {
                        start_date: startDate,
                        end_date: endDate,
                        start_time: startTime,
                        end_time: endTime,
                        all_day: allDay,
                    })
                    .then(function() {
                        toast('success', 'Jadwal diperbarui');
                        loadTodos();
                    })
                    .catch(function(err) {
                        info.revert();
                        toast('error', err.message);
                    });
            }

            // ── Modal form ──────────────────────────────────────────────────
            const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            const form = document.getElementById('eventForm');
            const fields = {
                id: document.getElementById('eventId'),
                title: document.getElementById('eventTitle'),
                description: document.getElementById('eventDescription'),
                startDate: document.getElementById('eventStartDate'),
                endDate: document.getElementById('eventEndDate'),
                startTime: document.getElementById('eventStartTime'),
                endTime: document.getElementById('eventEndTime'),
                allDay: document.getElementById('eventAllDay'),
                color: document.getElementById('eventColor'),
                isDone: document.getElementById('eventIsDone'),
            };
            const modalTitle = document.getElementById('eventModalTitle');
            const btnDelete = document.getElementById('btnDeleteEvent');
            const doneWrapper = document.getElementById('doneWrapper');
            const timeFields = Array.from(document.querySelectorAll('.time-field'));

            function syncTimeFields() {
                const hide = fields.allDay.checked;
                timeFields.forEach(function(el) {
                    el.style.display = hide ? 'none' : '';
                });
                if (hide) {
                    fields.startTime.value = '';
                    fields.endTime.value = '';
                }
            }
            fields.allDay.addEventListener('change', syncTimeFields);

            function selectedType() {
                return document.querySelector('input[name="eventType"]:checked').value;
            }

            const seriesNotice = document.getElementById('seriesNotice');
            const todoNotice = document.getElementById('todoNotice');

            function syncTypeUi() {
                const isTodo = selectedType() === 'TODO';
                const recurring = repeatType() !== null;
                todoNotice.style.display = isTodo ? 'block' : 'none';
                // Data berulang tidak punya satu status selesai: centangnya per
                // tanggal, lewat panel todo. Jadi saklarnya disembunyikan di sini.
                doneWrapper.style.display = (isTodo && fields.id.value && !recurring) ? 'block' : 'none';
                seriesNotice.style.display = (fields.id.value && recurring) ? 'block' : 'none';
                if (!fields.id.value) {
                    modalTitle.textContent = isTodo ? 'Todo Baru' : 'Agenda Baru';
                }
            }
            Array.from(document.querySelectorAll('input[name="eventType"]')).forEach(function(radio) {
                radio.addEventListener('change', syncTypeUi);
            });

            // ── Pengaturan pengulangan ──────────────────────────────────────
            const DAY_NAMES = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const repeatEls = {
                preset: document.getElementById('repeatPreset'),
                options: document.getElementById('repeatOptions'),
                intervalRow: document.getElementById('repeatIntervalRow'),
                interval: document.getElementById('repeatInterval'),
                unit: document.getElementById('repeatUnit'),
                daysRow: document.getElementById('repeatDaysRow'),
                domRow: document.getElementById('repeatDomRow'),
                dayOfMonth: document.getElementById('repeatDayOfMonth'),
                endNever: document.getElementById('repeatEndNever'),
                endOn: document.getElementById('repeatEndOn'),
                until: document.getElementById('repeatUntil'),
                summary: document.getElementById('repeatSummary'),
            };
            const dayToggles = Array.from(document.querySelectorAll('.day-toggle'));

            /** Pola yang benar-benar dikirim ke server (null = tidak diulang). */
            function repeatType() {
                const preset = repeatEls.preset.value;
                if (preset === 'NONE') return null;
                // "Kustom" cuma pembungkus: polanya diambil dari satuan yang dipilih.
                return preset === 'CUSTOM' ? repeatEls.unit.value : preset;
            }

            function isCustomPreset() {
                return repeatEls.preset.value === 'CUSTOM';
            }

            function repeatInterval() {
                return isCustomPreset() ? Math.max(1, parseInt(repeatEls.interval.value, 10) || 1) : 1;
            }

            function selectedDays() {
                return dayToggles.filter(function(b) {
                    return b.classList.contains('active');
                }).map(function(b) {
                    return parseInt(b.dataset.day, 10);
                });
            }

            function setSelectedDays(days) {
                const list = days || [];
                dayToggles.forEach(function(b) {
                    b.classList.toggle('active', list.indexOf(parseInt(b.dataset.day, 10)) !== -1);
                });
            }

            function startDayOfWeek() {
                return new Date((fields.startDate.value || todayStr()) + 'T00:00:00').getDay();
            }

            function startDayOfMonth() {
                return fields.startDate.value ? Number(fields.startDate.value.substring(8, 10)) : null;
            }

            /** Kalimat ringkas di bawah pengaturan, biar user yakin polanya benar. */
            function repeatSummaryText() {
                const type = repeatType();
                if (!type) return '';

                const every = repeatInterval();
                let text;

                if (type === 'DAILY') {
                    text = every === 1 ? 'Setiap hari' : 'Setiap ' + every + ' hari';
                } else if (type === 'WEEKDAY') {
                    text = 'Setiap hari kerja (Sen-Jum)';
                } else if (type === 'WEEKLY') {
                    let days = selectedDays();
                    if (!days.length) days = [startDayOfWeek()];
                    const names = days.map(function(d) {
                        return DAY_NAMES[d];
                    }).join(', ');
                    text = every === 1 ? 'Setiap ' + names : 'Setiap ' + every + ' minggu pada ' + names;
                } else if (type === 'MONTHLY') {
                    const dom = repeatEls.dayOfMonth.value;
                    let on;
                    if (dom === '-1') on = 'hari terakhir bulan';
                    else if (dom) on = 'tanggal ' + dom;
                    else on = 'tanggal ' + (startDayOfMonth() || '-');
                    text = every === 1 ? 'Setiap bulan pada ' + on : 'Setiap ' + every + ' bulan pada ' + on;
                } else {
                    const label = fields.startDate.value ?
                        new Date(fields.startDate.value + 'T00:00:00')
                        .toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long'
                        }) :
                        '-';
                    text = every === 1 ? 'Setiap tahun pada ' + label :
                        'Setiap ' + every + ' tahun pada ' + label;
                }

                if (repeatEls.endOn.checked && repeatEls.until.value) {
                    text += ', sampai ' + new Date(repeatEls.until.value + 'T00:00:00')
                        .toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                }

                if (type === 'MONTHLY' && repeatEls.dayOfMonth.value !== '-1') {
                    const dom = parseInt(repeatEls.dayOfMonth.value || startDayOfMonth(), 10);
                    if (dom >= 29) {
                        text += '. Bulan yang tidak punya tanggal ' + dom + ' dilewati.';
                    }
                }

                return text;
            }

            function syncRepeatUi() {
                const type = repeatType();
                repeatEls.options.style.display = type ? 'block' : 'none';
                repeatEls.intervalRow.style.display = isCustomPreset() ? 'flex' : 'none';
                repeatEls.daysRow.style.display = type === 'WEEKLY' ? 'block' : 'none';
                repeatEls.domRow.style.display = type === 'MONTHLY' ? 'flex' : 'none';

                repeatEls.until.disabled = !repeatEls.endOn.checked;
                if (!repeatEls.endOn.checked) repeatEls.until.value = '';

                repeatEls.summary.textContent = repeatSummaryText();
                syncTypeUi();
            }

            /** Isi pengaturan dari data yang tersimpan (dipakai saat buka form). */
            function setRepeatFromData(data) {
                const type = data.repeatType || null;
                const interval = Math.max(1, parseInt(data.repeatInterval, 10) || 1);

                repeatEls.interval.value = interval;
                repeatEls.unit.value = (type && type !== 'WEEKDAY') ? type : 'DAILY';
                setSelectedDays(data.repeatDays || []);
                repeatEls.dayOfMonth.value = (data.repeatDayOfMonth === null ||
                    data.repeatDayOfMonth === undefined) ? '' : String(data.repeatDayOfMonth);

                if (!type) {
                    repeatEls.preset.value = 'NONE';
                } else if (type === 'WEEKDAY') {
                    repeatEls.preset.value = 'WEEKDAY';
                } else if (interval > 1) {
                    // Interval di atas 1 hanya bisa dibuat lewat pilihan Kustom.
                    repeatEls.preset.value = 'CUSTOM';
                } else {
                    repeatEls.preset.value = type;
                }

                if (data.repeatUntil) {
                    repeatEls.endOn.checked = true;
                    repeatEls.until.value = data.repeatUntil;
                } else {
                    repeatEls.endNever.checked = true;
                    repeatEls.until.value = '';
                }

                syncRepeatUi();
            }

            function repeatPayload() {
                const type = repeatType();
                if (!type) {
                    return {
                        repeat_type: null,
                        repeat_interval: 1,
                        repeat_days: [],
                        repeat_day_of_month: null,
                        repeat_until: null,
                    };
                }

                const dom = repeatEls.dayOfMonth.value;

                return {
                    repeat_type: type,
                    repeat_interval: repeatInterval(),
                    repeat_days: type === 'WEEKLY' ? selectedDays() : [],
                    repeat_day_of_month: (type === 'MONTHLY' && dom !== '') ? parseInt(dom, 10) : null,
                    repeat_until: (repeatEls.endOn.checked && repeatEls.until.value) ?
                        repeatEls.until.value : null,
                };
            }

            repeatEls.preset.addEventListener('change', function() {
                // Beri hari default begitu pola mingguan dipilih, biar tidak kosong.
                if (repeatType() === 'WEEKLY' && !selectedDays().length) {
                    setSelectedDays([startDayOfWeek()]);
                }
                syncRepeatUi();
            });
            repeatEls.unit.addEventListener('change', function() {
                if (repeatEls.unit.value === 'WEEKLY' && !selectedDays().length) {
                    setSelectedDays([startDayOfWeek()]);
                }
                syncRepeatUi();
            });
            repeatEls.interval.addEventListener('input', syncRepeatUi);
            repeatEls.dayOfMonth.addEventListener('change', syncRepeatUi);
            repeatEls.endNever.addEventListener('change', syncRepeatUi);
            repeatEls.endOn.addEventListener('change', function() {
                if (!repeatEls.until.value) {
                    repeatEls.until.value = fields.startDate.value || todayStr();
                }
                syncRepeatUi();
            });
            repeatEls.until.addEventListener('change', syncRepeatUi);
            dayToggles.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    btn.classList.toggle('active');
                    syncRepeatUi();
                });
            });
            fields.startDate.addEventListener('change', syncRepeatUi);

            // Pemilih warna
            const swatches = Array.from(document.querySelectorAll('.color-swatch'));

            function setColor(hex) {
                fields.color.value = hex;
                swatches.forEach(function(s) {
                    s.classList.toggle('selected', s.dataset.color === hex);
                });
            }
            swatches.forEach(function(s) {
                s.addEventListener('click', function() {
                    setColor(s.dataset.color);
                });
            });

            function openForm(data) {
                data = data || {};
                form.reset();

                fields.id.value = data.id || '';
                fields.title.value = data.title || '';
                fields.description.value = data.description || '';
                fields.startDate.value = data.startDate || todayStr();
                fields.endDate.value = data.endDate || '';
                fields.startTime.value = data.startTime || '';
                fields.endTime.value = data.endTime || '';
                fields.allDay.checked = data.allDay === undefined ? true : !!data.allDay;
                fields.isDone.checked = !!data.isDone;

                document.getElementById(data.type === 'TODO' ? 'typeTodo' : 'typeEvent').checked = true;
                setColor(data.color || DEFAULT_COLOR);
                setRepeatFromData(data);
                syncTimeFields();
                syncTypeUi();

                if (data.id) {
                    modalTitle.textContent = data.type === 'TODO' ? 'Ubah Todo' : 'Ubah Agenda';
                    btnDelete.style.display = 'inline-block';
                } else {
                    btnDelete.style.display = 'none';
                }

                eventModal.show();
                setTimeout(function() {
                    fields.title.focus();
                }, 300);
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const id = fields.id.value;
                const payload = Object.assign({
                    title: fields.title.value.trim(),
                    description: fields.description.value.trim() || null,
                    type: selectedType(),
                    start_date: fields.startDate.value,
                    end_date: fields.endDate.value || null,
                    start_time: fields.startTime.value || null,
                    end_time: fields.endTime.value || null,
                    all_day: fields.allDay.checked,
                    color: fields.color.value,
                    is_done: fields.isDone.checked,
                }, repeatPayload());

                if (!payload.title) {
                    toast('error', 'Judul wajib diisi');
                    return;
                }

                const url = id ? routes.base + '/' + id : routes.store;
                request(url, id ? 'PUT' : 'POST', payload)
                    .then(function(data) {
                        eventModal.hide();
                        toast('success', data.message);
                        calendar.refetchEvents();
                        loadTodos();
                    })
                    .catch(function(err) {
                        toast('error', err.message);
                    });
            });

            btnDelete.addEventListener('click', function() {
                const id = fields.id.value;
                if (!id) return;

                const recurring = repeatType() !== null;

                Swal.fire({
                    title: recurring ? 'Hapus seluruh rangkaian?' : 'Hapus data ini?',
                    text: recurring ?
                        'Semua kemunculan berikut riwayat centangnya ikut terhapus dan tidak bisa dikembalikan.' :
                        'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    request(routes.base + '/' + id, 'DELETE')
                        .then(function(data) {
                            eventModal.hide();
                            toast('success', data.message);
                            calendar.refetchEvents();
                            loadTodos();
                        })
                        .catch(function(err) {
                            toast('error', err.message);
                        });
                });
            });

            document.getElementById('btnNewEvent').addEventListener('click', function() {
                openForm({
                    type: 'EVENT'
                });
            });
            document.getElementById('btnNewTodo').addEventListener('click', function() {
                openForm({
                    type: 'TODO'
                });
            });

            // ── Modal detail (data modul lain) ──────────────────────────────
            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

            function openDetail(event) {
                const props = event.extendedProps;
                document.getElementById('detailTitle').innerHTML =
                    '<i class="bi ' + escapeHtml(props.icon || 'bi-info-circle') + ' me-2"></i>' +
                    escapeHtml(event.title);

                const tanggal = new Date(props.startDate + 'T00:00:00').toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                document.getElementById('detailMeta').textContent = (props.sourceLabel || '') + ' - ' + tanggal;

                let html = '';
                const details = props.details || {};
                Object.keys(details).forEach(function(key) {
                    html += '<div class="detail-row"><span>' + escapeHtml(key) + '</span><span>' +
                        escapeHtml(details[key]) + '</span></div>';
                });
                document.getElementById('detailBody').innerHTML = html ||
                    '<p class="text-muted small mb-0">Tidak ada rincian tambahan.</p>';

                document.getElementById('detailLink').href = props.url || '#';
                detailModal.show();
            }

            // ── Panel todo ──────────────────────────────────────────────────
            // Dikunci per KEMUNCULAN (id + tanggal), bukan per id, karena satu
            // rangkaian berulang bisa muncul di daftar pending dan daftar selesai
            // sekaligus dengan tanggal yang berbeda.
            let todosByKey = {};
            const todoList = document.getElementById('todoList');
            const doneList = document.getElementById('doneList');
            const todoCount = document.getElementById('todoCount');

            function todoKey(todo) {
                return todo.id + '|' + todo.occurrenceDate;
            }

            function todoItemHtml(todo, isDone) {
                let meta = todo.dateLabel;
                if (todo.timeLabel && todo.timeLabel !== 'Seharian') meta += ' - ' + todo.timeLabel;

                let badge = '';
                if (!isDone && todo.isOverdue) {
                    badge = '<span class="badge bg-danger-subtle text-danger ms-1">Terlewat</span>';
                } else if (!isDone && todo.isToday) {
                    badge = '<span class="badge bg-purple-light text-purple ms-1">Hari ini</span>';
                }

                const repeat = todo.isRecurring ?
                    '<span class="todo-repeat" title="' + escapeHtml(todo.repeatLabel || 'Berulang') +
                    '"><i class="bi bi-arrow-repeat"></i></span>' : '';

                const key = todoKey(todo);

                return '<div class="todo-item' + (isDone ? ' done' : '') + '" data-key="' + key +
                    '" data-id="' + todo.id + '">' +
                    '<input class="form-check-input todo-check" type="checkbox"' + (isDone ? ' checked' : '') +
                    '>' +
                    '<div class="todo-body" data-key="' + key + '">' +
                    '<div class="todo-title">' + escapeHtml(todo.title) + repeat + '</div>' +
                    '<div class="todo-meta">' + escapeHtml(meta) + badge + '</div>' +
                    '</div></div>';
            }

            function loadTodos() {
                fetch(routes.todos, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        const pending = data.pending || [];
                        const done = data.done || [];

                        todosByKey = {};
                        pending.concat(done).forEach(function(t) {
                            todosByKey[todoKey(t)] = t;
                        });

                        todoCount.textContent = pending.length;
                        todoList.innerHTML = pending.length ?
                            pending.map(function(t) {
                                return todoItemHtml(t, false);
                            }).join('') :
                            '<p class="text-muted small text-center py-3 mb-0">Belum ada todo. Tambahkan lewat kolom di atas.</p>';

                        doneList.innerHTML = done.length ?
                            done.map(function(t) {
                                return todoItemHtml(t, true);
                            }).join('') :
                            '<p class="text-muted small mb-0">Belum ada todo yang selesai.</p>';
                    })
                    .catch(function() {
                        todoList.innerHTML =
                            '<p class="text-danger small text-center py-3 mb-0">Gagal memuat todo.</p>';
                    });
            }

            function handleTodoClick(e) {
                const check = e.target.closest('.todo-check');
                if (check) {
                    const item = check.closest('.todo-item');
                    const todo = todosByKey[item.dataset.key];
                    // Rangkaian berulang dicentang per tanggal kemunculan.
                    const payload = (todo && todo.isRecurring) ? {
                        date: todo.occurrenceDate
                    } : null;

                    request(routes.base + '/' + item.dataset.id + '/toggle-done', 'POST', payload)
                        .then(function(data) {
                            toast('success', data.message);
                            loadTodos();
                            calendar.refetchEvents();
                        })
                        .catch(function(err) {
                            check.checked = !check.checked;
                            toast('error', err.message);
                        });
                    return;
                }

                const body = e.target.closest('.todo-body');
                if (body) {
                    const todo = todosByKey[body.dataset.key];
                    if (todo) openForm(todo);
                }
            }

            todoList.addEventListener('click', handleTodoClick);
            doneList.addEventListener('click', handleTodoClick);

            // Tambah todo cepat (untuk hari ini)
            document.getElementById('quickTodoForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const input = document.getElementById('quickTodoTitle');
                const title = input.value.trim();
                if (!title) return;

                request(routes.store, 'POST', {
                        title: title,
                        type: 'TODO',
                        start_date: todayStr(),
                        all_day: true,
                        color: DEFAULT_COLOR,
                    })
                    .then(function() {
                        input.value = '';
                        toast('success', 'Todo ditambahkan');
                        loadTodos();
                        calendar.refetchEvents();
                    })
                    .catch(function(err) {
                        toast('error', err.message);
                    });
            });

            // Buka / tutup daftar selesai
            document.getElementById('toggleDoneList').addEventListener('click', function() {
                const shown = doneList.style.display !== 'none';
                doneList.style.display = shown ? 'none' : 'block';
                document.getElementById('doneCaret').className = shown ?
                    'bi bi-chevron-right me-1' : 'bi bi-chevron-down me-1';
            });

            loadTodos();
        });
    </script>
@endpush
