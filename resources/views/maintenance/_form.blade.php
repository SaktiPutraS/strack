@php
    $sType = old('schedule_type', $task->schedule_type ?? 'TEXT');
    $val = $task->schedule_value ?? null;
    $sText  = old('schedule_text',  ($task && $task->schedule_type === 'TEXT')  ? $val : '');
    $sDate  = old('schedule_date',  ($task && $task->schedule_type === 'DATE')  ? $val : '');
    $sYear  = old('schedule_year',  ($task && $task->schedule_type === 'YEAR')  ? $val : '');

    $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    $sMonths = old('schedule_months',
        ($task && $task->schedule_type === 'MONTH' && $val && !str_contains($val, '-')) ? explode(',', $val) : []);
    $sMonths = array_map('strval', (array) $sMonths);
@endphp

<div class="col-12">
    <label class="form-label fw-semibold">Nama Tugas <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror"
        value="{{ old('name', $task->name ?? '') }}" placeholder="mis. Service AC ruang tamu, Ganti oli motor" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-5">
    <label class="form-label fw-semibold">Tipe Jadwal <span class="text-danger">*</span></label>
    <select name="schedule_type" id="scheduleType" class="form-select form-select-lg">
        <option value="TEXT" {{ $sType === 'TEXT' ? 'selected' : '' }}>Catatan (teks bebas)</option>
        <option value="DATE" {{ $sType === 'DATE' ? 'selected' : '' }}>Tanggal</option>
        <option value="MONTH" {{ $sType === 'MONTH' ? 'selected' : '' }}>Bulan (bisa lebih dari satu)</option>
        <option value="YEAR" {{ $sType === 'YEAR' ? 'selected' : '' }}>Tahun</option>
    </select>
</div>

<div class="col-md-7">
    <label class="form-label fw-semibold">Jadwal <span class="text-danger">*</span></label>

    <div class="sched-field" data-type="TEXT">
        <input type="text" name="schedule_text" class="form-control form-control-lg @error('schedule_text') is-invalid @enderror"
            value="{{ $sText }}" placeholder="mis. setiap 3 bulan sekali">
        @error('schedule_text')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="sched-field" data-type="DATE">
        <input type="date" name="schedule_date" class="form-control form-control-lg @error('schedule_date') is-invalid @enderror"
            value="{{ $sDate }}">
        @error('schedule_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="sched-field" data-type="MONTH">
        <div class="border rounded p-2">
            <div class="row g-1">
                @foreach ($monthNames as $num => $mn)
                    <div class="col-6 col-sm-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="schedule_months[]" value="{{ $num }}"
                                id="mon{{ $num }}" {{ in_array((string) $num, $sMonths, true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="mon{{ $num }}">{{ $mn }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-text">Pilih satu atau beberapa bulan (berulang tiap tahun).</div>
        @error('schedule_months')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="sched-field" data-type="YEAR">
        <input type="number" name="schedule_year" min="1900" max="2200" step="1"
            class="form-control form-control-lg @error('schedule_year') is-invalid @enderror"
            value="{{ $sYear }}" placeholder="mis. 2026">
        @error('schedule_year')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
</div>

<div class="col-12">
    <label class="form-label fw-semibold">Catatan</label>
    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
        placeholder="Catatan tambahan (opsional)">{{ old('notes', $task->notes ?? '') }}</textarea>
    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@push('scripts')
    <script>
        (function () {
            const sel = document.getElementById('scheduleType');
            const fields = document.querySelectorAll('.sched-field');
            if (!sel) return;
            function sync() {
                fields.forEach(function (f) {
                    const active = f.dataset.type === sel.value;
                    f.style.display = active ? '' : 'none';
                    f.querySelectorAll('input').forEach(function (inp) {
                        inp.disabled = !active;
                    });
                });
            }
            sel.addEventListener('change', sync);
            sync();
        })();
    </script>
@endpush
