@php $type = old('type', $debt->type ?? 'HUTANG'); @endphp

<div class="col-md-6">
    <label class="form-label fw-semibold">Tipe <span class="text-danger">*</span></label>
    <select name="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
        <option value="HUTANG" {{ $type === 'HUTANG' ? 'selected' : '' }}>Hutang (saya meminjam)</option>
        <option value="PIUTANG" {{ $type === 'PIUTANG' ? 'selected' : '' }}>Piutang (saya meminjamkan)</option>
    </select>
    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-6">
    <label class="form-label fw-semibold">Nama Pihak <span class="text-danger">*</span></label>
    <input type="text" name="party_name" class="form-control form-control-lg @error('party_name') is-invalid @enderror"
        value="{{ old('party_name', $debt->party_name ?? '') }}" placeholder="mis. Kantor, Budi, Bank" required>
    @error('party_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-12">
    <label class="form-label fw-semibold">Judul / Keterangan</label>
    <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror"
        value="{{ old('title', $debt->title ?? '') }}" placeholder="mis. Pinjaman modal usaha">
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-6">
    <label class="form-label fw-semibold">Nilai Total <span class="text-danger">*</span></label>
    <div class="input-group input-group-lg">
        <span class="input-group-text">Rp</span>
        <input type="number" name="principal_amount" min="0" step="1"
            class="form-control @error('principal_amount') is-invalid @enderror"
            value="{{ old('principal_amount', isset($debt) ? (int) $debt->principal_amount : '') }}" placeholder="0" required>
        @error('principal_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="col-md-6">
    <label class="form-label fw-semibold d-block">Jatuh Tempo</label>
    <div class="form-check form-switch mt-2">
        <input class="form-check-input" type="checkbox" role="switch" id="useDueDate" name="use_due_date" value="1"
            {{ old('use_due_date', (isset($debt) && $debt->due_date) ? '1' : null) ? 'checked' : '' }}>
        <label class="form-check-label" for="useDueDate">Pakai tanggal jatuh tempo (opsional)</label>
    </div>
    <input type="date" name="due_date" id="dueDateInput"
        class="form-control form-control-lg mt-2 @error('due_date') is-invalid @enderror"
        value="{{ old('due_date', isset($debt) && $debt->due_date ? $debt->due_date->format('Y-m-d') : '') }}">
    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@push('scripts')
    <script>
        (function () {
            const sw = document.getElementById('useDueDate');
            const inp = document.getElementById('dueDateInput');
            if (!sw || !inp) return;
            function sync() {
                if (sw.checked) {
                    inp.classList.remove('d-none');
                    inp.disabled = false;
                } else {
                    inp.classList.add('d-none');
                    inp.disabled = true;
                    inp.value = '';
                }
            }
            sw.addEventListener('change', sync);
            sync();
        })();
    </script>
@endpush

<div class="col-12">
    <label class="form-label fw-semibold">Catatan</label>
    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
        placeholder="Catatan tambahan (opsional)">{{ old('notes', $debt->notes ?? '') }}</textarea>
    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
