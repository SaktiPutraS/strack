@extends('layouts.app')
@section('title', 'Budgeting ' . $year . ' — STRACK')

@section('content')

@php
$monthNames = [
    1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
    7=>'Jul',8=>'Agt',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des',
];
$monthNamesFull = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
    7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
];
@endphp

{{-- ──────────────── ACTION BAR ──────────────── --}}
<div class="luxury-card mb-3 px-3 py-2 d-flex align-items-center flex-wrap gap-2">

    {{-- Year selector --}}
    <form method="GET" action="{{ route('budgets.index') }}" class="d-flex align-items-center gap-1 me-2">
        <label class="text-muted small mb-0 me-1"><i class="bi bi-calendar3"></i></label>
        <select name="year" class="form-select form-select-sm" style="width:90px" onchange="this.form.submit()">
            @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
            @if(!$availableYears->contains($year))
                <option value="{{ $year }}" selected>{{ $year }}</option>
            @endif
        </select>
    </form>

    <a href="{{ route('budgets.export-all') }}" class="btn btn-sm btn-outline-success">
        <i class="bi bi-download me-1"></i>Export All
    </a>
    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#importAllModal">
        <i class="bi bi-upload me-1"></i>Import All
    </button>
    <a href="{{ route('budgets.report', $year) }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-graph-up me-1"></i>Laporan {{ $year }}
    </a>

</div>

{{-- ──────────────── MATRIX TABLE ──────────────── --}}
<div class="luxury-card overflow-hidden p-0">
    <div class="matrix-wrapper">
        <table class="matrix-table">

            {{-- HEADER: nama bulan --}}
            <thead>
                <tr>
                    <th class="sticky-col head-item-col">
                        <span class="text-purple fw-bold fs-7">Budget {{ $year }}</span>
                    </th>
                    @for($m = 1; $m <= 12; $m++)
                    @php $bud = $budgets->get($m); @endphp
                    @php $isAllDone = $bud && $bud->is_fully_completed && $bud->total_items_count > 0; @endphp
                    <th class="{{ $bud ? 'has-budget' : 'no-budget' }} {{ $isAllDone ? 'month-all-done' : '' }}">
                        @if($bud)
                            <a href="{{ route('budgets.show', [$year, $m]) }}" class="month-link">
                                {{ $monthNames[$m] }}
                            </a>
                            @if($isAllDone)
                                <span class="close-badge">CLOSE</span>
                            @endif
                            <br>
                            <small class="month-progress">
                                {{ $bud->completed_items_count }}/{{ $bud->total_items_count }}
                                <span class="{{ $isAllDone ? 'text-white' : 'text-success' }}">✓</span>
                            </small>
                            <div class="month-total {{ $isAllDone ? 'text-white-75' : '' }}">{{ $bud->formatted_budget }}</div>
                        @else
                            <span class="month-no-budget">{{ $monthNames[$m] }}</span>
                        @endif
                    </th>
                    @endfor
                </tr>
            </thead>

            {{-- BODY: kategori + items --}}
            <tbody>
                @if(empty($categoryOrder))
                    <tr>
                        <td class="sticky-col text-muted fst-italic fs-7 py-4 text-center" colspan="13">
                            Belum ada data budget untuk tahun {{ $year }}. Silakan import data melalui tombol <strong>Import All</strong>.
                        </td>
                    </tr>
                @else
                    @foreach($categoryOrder as $cat)
                    @php
                        // Subtotal per bulan untuk kategori ini
                        $catSubtotals = [];
                        for ($m = 1; $m <= 12; $m++) {
                            $total = 0;
                            if (isset($matrix[$cat])) {
                                foreach ($matrix[$cat] as $itemName => $months) {
                                    if ($months[$m]) $total += $months[$m]->estimated_amount;
                                }
                            }
                            $catSubtotals[$m] = $total;
                        }
                    @endphp

                    {{-- Baris kategori --}}
                    <tr class="cat-row">
                        <td class="sticky-col cat-name-cell">[{{ $cat }}]</td>
                        @for($m = 1; $m <= 12; $m++)
                        <td class="cat-subtotal-cell {{ !$budgets->has($m) ? 'no-budget' : '' }}">
                            @if($catSubtotals[$m] > 0)
                                {{ number_format($catSubtotals[$m], 0, ',', '.') }}
                            @elseif($budgets->has($m))
                                <span class="text-muted">—</span>
                            @else
                                <span class="text-muted opacity-25">—</span>
                            @endif
                        </td>
                        @endfor
                    </tr>

                    {{-- Baris setiap item --}}
                    @foreach($matrix[$cat] as $itemName => $monthItems)
                    <tr class="item-row" data-item-name="{{ $itemName }}" data-category="{{ $cat }}">

                        {{-- Kolom item name (sticky) --}}
                        <td class="sticky-col item-name-cell" title="{{ $itemName }}">
                            {{ $itemName }}
                        </td>

                        {{-- Kolom tiap bulan --}}
                        @for($m = 1; $m <= 12; $m++)
                        @php
                            $item   = $monthItems[$m];
                            $budget = $budgets->get($m);
                        @endphp
                        <td class="amount-cell
                            @if($item) item-exists {{ $item->is_completed ? 'completed' : '' }}
                            @elseif($budget) has-budget no-item
                            @else no-budget
                            @endif"
                            data-month="{{ $m }}"
                            data-year="{{ $year }}"
                            data-item-id="{{ $item?->id }}">

                            @if($item)
                                {{-- Item ada di bulan ini --}}
                                <span class="toggle-pfx {{ $item->is_completed ? 'done' : '' }}"
                                    onclick="toggleItem({{ $item->id }}, this)">{{ $item->is_completed ? 'x' : '○' }}</span>
                                <span class="cell-amount" title="{{ $item->notes ?? '' }}">{{ number_format($item->estimated_amount, 0, ',', '.') }}</span>
                                @if($item->notes)
                                    <div class="cell-notes">({{ $item->notes }})</div>
                                @endif
                                <div class="cell-actions"
                                     data-item-id="{{ $item->id }}"
                                     data-item-name="{{ $item->item_name }}"
                                     data-amount="{{ $item->estimated_amount }}"
                                     data-notes="{{ $item->notes ?? '' }}">
                                    <button class="btn-cell-edit" onclick="startCellEdit(this)" title="Edit item">✎</button>
                                    <button class="btn-cell-delete" onclick="deleteCellItem(this)" title="Hapus item">×</button>
                                </div>

                            @elseif($budget)
                                {{-- Budget ada, tapi item tidak ada di bulan ini --}}
                                <button class="btn-add-to-month"
                                    onclick="addItemToMonth(this, {{ $m }}, {{ $year }}, '{{ addslashes($cat) }}', '{{ addslashes($itemName) }}')"
                                    title="Tambah ke {{ $monthNamesFull[$m] }}">+</button>

                            @else
                                {{-- Belum ada budget di bulan ini --}}
                                <span class="no-data">—</span>
                            @endif
                        </td>
                        @endfor
                    </tr>
                    @endforeach
                    @endforeach
                @endif
            </tbody>



        </table>
    </div>
</div>

{{-- ──────────────── IMPORT ALL MODAL ──────────────── --}}
<div class="modal fade" id="importAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content luxury-card border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-purple"><i class="bi bi-upload me-2"></i>Import Semua Budget</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('budgets.import-all') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Upload file Excel (.xlsx / .xls) untuk import semua budget sekaligus.</p>
                    <input type="file" name="excel_file" accept=".xlsx,.xls" class="form-control" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
/* ─── Matrix Wrapper ─── */
.matrix-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* ─── Table ─── */
.matrix-table {
    border-collapse: separate;
    border-spacing: 0;
    width: max-content;
    min-width: 100%;
    font-size: .8rem;
}

/* ─── Sticky first column ─── */
.sticky-col {
    position: sticky;
    left: 0;
    z-index: 3;
    background: white;
    box-shadow: 2px 0 6px rgba(139,92,246,.08);
}
thead .sticky-col {
    z-index: 4;
    background: linear-gradient(135deg, rgba(139,92,246,.08), rgba(168,85,247,.12));
}

/* ─── Header row ─── */
.matrix-table thead th {
    background: linear-gradient(135deg, rgba(139,92,246,.1), rgba(168,85,247,.14));
    padding: 8px 10px;
    text-align: center;
    white-space: nowrap;
    border-bottom: 2px solid rgba(139,92,246,.2);
    min-width: 88px;
    vertical-align: middle;
    font-weight: 600;
}
.head-item-col {
    min-width: 170px;
    text-align: left !important;
    padding-left: 14px !important;
}
.month-link {
    color: #6D28D9;
    text-decoration: none;
    font-weight: 700;
    font-size: .82rem;
}
.month-link:hover { text-decoration: underline; }
.month-no-budget { color: #C4B5FD; font-size: .82rem; }
.month-progress { color: #6B7280; font-size: .7rem; }
.month-total {
    font-size: .72rem;
    font-weight: 700;
    color: #6D28D9;
    margin-top: 2px;
    font-variant-numeric: tabular-nums;
    letter-spacing: -.3px;
}
.text-white-75 { color: rgba(255,255,255,.9) !important; }

/* ─── Month fully closed ─── */
thead th.month-all-done {
    background: linear-gradient(135deg, #15803d, #22c55e) !important;
    border-bottom: 2px solid #166534 !important;
}
thead th.month-all-done .month-link {
    color: #fff !important;
    font-weight: 800;
    text-shadow: 0 1px 2px rgba(0,0,0,.2);
}
thead th.month-all-done .month-progress {
    color: rgba(255,255,255,.9) !important;
    font-weight: 600;
}
thead th.month-all-done .month-total {
    color: rgba(255,255,255,.95) !important;
}
.close-badge {
    display: inline-block;
    background: rgba(255,255,255,.25);
    color: #fff;
    font-size: .6rem;
    font-weight: 800;
    letter-spacing: .5px;
    padding: 1px 5px;
    border-radius: 4px;
    vertical-align: middle;
    margin-left: 3px;
    line-height: 1.4;
}

/* ─── Category rows ─── */
.cat-row td {
    background: linear-gradient(135deg, rgba(139,92,246,.08), rgba(168,85,247,.12));
    padding: 5px 10px;
    border-bottom: 1px solid rgba(139,92,246,.12);
    border-top: 2px solid rgba(139,92,246,.12);
}
.cat-name-cell {
    font-weight: 700;
    font-size: .78rem;
    color: #6D28D9;
    text-transform: uppercase;
    letter-spacing: .2px;
    text-align: left;
    padding-left: 14px !important;
}
.cat-subtotal-cell {
    text-align: right;
    font-weight: 600;
    font-size: .78rem;
    color: #374151;
    font-variant-numeric: tabular-nums;
}

/* ─── Item rows ─── */
.item-row td {
    border-bottom: 1px solid rgba(139,92,246,.04);
    vertical-align: middle;
}
.item-row:hover td { background: rgba(139,92,246,.03); }
.item-row:hover .sticky-col { background: rgba(247,245,255,.98); }

.item-name-cell {
    padding: 4px 14px;
    font-size: .8rem;
    color: #374151;
    white-space: nowrap;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: left;
}

/* ─── Amount cells ─── */
.amount-cell {
    padding: 4px 8px;
    text-align: right;
    vertical-align: middle;
    min-height: 26px;
    position: relative;
}
.amount-cell.no-budget { background: rgba(0,0,0,.015); }
.no-data { color: #E5E7EB; font-size: .75rem; }

/* Item exists — PENDING (belum close) */
.amount-cell.item-exists .toggle-pfx {
    font-family: monospace;
    font-weight: 700;
    font-size: .78rem;
    cursor: pointer;
    color: #f59e0b;          /* amber — belum selesai */
    user-select: none;
    transition: color .15s;
    margin-right: 2px;
}
.amount-cell.item-exists .toggle-pfx:hover { color: #8B5CF6; }
.amount-cell.item-exists .toggle-pfx.done  { color: #16a34a; font-size: .72rem; }
.cell-amount {
    font-variant-numeric: tabular-nums;
    font-size: .8rem;
    color: #111827;
    font-weight: 600;
}
.cell-notes {
    font-size: .68rem;
    color: #9CA3AF;
    line-height: 1.2;
    margin-top: 1px;
}

/* Item COMPLETED (sudah close) */
.amount-cell.item-exists.completed {
    background: rgba(34, 197, 94, 0.10) !important;
    border-left: 2px solid rgba(34, 197, 94, 0.5);
}
.amount-cell.item-exists.completed .cell-amount {
    color: #86efac;
    text-decoration: line-through;
    font-weight: 400;
}
.amount-cell.item-exists.completed .cell-notes { color: #bbf7d0; }

/* ─── Cell edit/delete actions (muncul saat hover) ─── */
.cell-actions {
    display: none;
    position: absolute;
    top: 1px;
    right: 1px;
    gap: 1px;
    z-index: 4;
    background: rgba(255,255,255,.92);
    border-radius: 4px;
    box-shadow: 0 1px 4px rgba(0,0,0,.12);
    padding: 1px;
}
.amount-cell.item-exists:hover .cell-actions { display: flex; }

.btn-cell-edit, .btn-cell-delete {
    background: none;
    border: none;
    padding: 1px 4px;
    font-size: .72rem;
    cursor: pointer;
    border-radius: 3px;
    line-height: 1.4;
    color: #9CA3AF;
    transition: color .12s, background .12s;
}
.btn-cell-edit:hover   { color: #8B5CF6; background: rgba(139,92,246,.12); }
.btn-cell-delete:hover { color: #EF4444; background: rgba(239,68,68,.1); }

/* + button for missing items */
.btn-add-to-month {
    background: none;
    border: 1px dashed rgba(139,92,246,.25);
    border-radius: 4px;
    color: #C4B5FD;
    font-size: .75rem;
    padding: 1px 7px;
    cursor: pointer;
    transition: all .15s;
    display: none;
}
.amount-cell.no-item:hover .btn-add-to-month { display: inline-block; }
.btn-add-to-month:hover { color: #8B5CF6; background: rgba(139,92,246,.1); border-color: #8B5CF6; }

.total-row td {
    background: linear-gradient(135deg, rgba(139,92,246,.08), rgba(168,85,247,.12));
    border-top: 2px solid rgba(139,92,246,.2);
    padding: 8px 10px;
}
.total-label-cell {
    font-weight: 700;
    font-size: .8rem;
    color: #374151;
    text-align: left;
    padding-left: 14px !important;
}
.total-cell {
    text-align: right;
    font-size: .78rem;
    vertical-align: top;
}

tfoot .sticky-col {
    z-index: 4;
}

/* ─── Misc ─── */
.text-purple { color: #8B5CF6 !important; }
.bg-purple   { background-color: #8B5CF6 !important; }
.opacity-25  { opacity: .25; }
.opacity-40  { opacity: .4; }
</style>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

/* ─── Toggle item completion ─── */
function toggleItem(itemId, el) {
    fetch(`/budget-items/${itemId}/toggle-complete`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) return;
        const cell = el.closest('.amount-cell');
        if (d.is_completed) {
            cell.classList.add('completed');
            el.textContent = 'x';
            el.classList.add('done');
        } else {
            cell.classList.remove('completed');
            el.textContent = '○';
            el.classList.remove('done');
            // reset inline bg that might have been applied
            cell.style.background = '';
        }
    });
}

/* ─── Add item to a specific month ─── */
function addItemToMonth(btn, month, year, category, itemName) {
    Swal.fire({
        title: `Tambah ke bulan ini?`,
        html: `
            <div class="text-start">
                <div class="mb-2"><strong>Item:</strong> ${itemName}</div>
                <div class="mb-2"><strong>Kategori:</strong> ${category}</div>
                <label class="form-label small fw-bold">Jumlah (Rp)</label>
                <input type="number" id="swal-amount" class="form-control" placeholder="Contoh: 435109" min="0" step="1000">
                <label class="form-label small fw-bold mt-2">Catatan (opsional)</label>
                <input type="text" id="swal-notes" class="form-control" placeholder="Catatan tambahan">
            </div>`,
        showCancelButton: true,
        confirmButtonText: 'Tambah',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#8B5CF6',
        focusConfirm: false,
        preConfirm: () => {
            const amount = document.getElementById('swal-amount').value;
            if (!amount || Number(amount) < 0) {
                Swal.showValidationMessage('Jumlah wajib diisi dan harus lebih dari 0');
                return false;
            }
            return {
                amount: Number(amount),
                notes: document.getElementById('swal-notes').value.trim(),
            };
        },
    }).then(result => {
        if (!result.isConfirmed) return;
        const { amount, notes } = result.value;

        fetch(`/budgets/${year}/${month}/items`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ item_name: itemName, estimated_amount: amount, notes: notes || null, category }),
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return Swal.fire('Error', d.message, 'error');
            // Update cell
            const cell = btn.closest('.amount-cell');
            const amtFmt = Number(amount).toLocaleString('id-ID');
            const notesHtml = notes ? `<div class="cell-notes">(${notes})</div>` : '';
            cell.innerHTML = `
                <span class="toggle-pfx" onclick="toggleItem(${d.item.id}, this)">○</span>
                <span class="cell-amount">${amtFmt}</span>${notesHtml}`;
            cell.classList.remove('no-item');
            cell.classList.add('item-exists');
            cell.dataset.itemId = d.item.id;
        })
        .catch(() => Swal.fire('Error', 'Gagal menambahkan item', 'error'));
    });
}

/* ─── Edit item dari matrix ─── */
function startCellEdit(btn) {
    const actions  = btn.closest('.cell-actions');
    const itemId   = actions.dataset.itemId;
    const itemName = actions.dataset.itemName;
    const amount   = actions.dataset.amount;
    const notes    = actions.dataset.notes;

    Swal.fire({
        title: 'Edit Item',
        html: `
            <div class="text-start">
                <label class="form-label small fw-bold mb-1">Nama Item <span class="text-danger">*</span></label>
                <input type="text" id="swal-name" class="form-control form-control-sm mb-2" value="${itemName.replace(/"/g,'&quot;')}">
                <label class="form-label small fw-bold mb-1">Nominal (Rp) <span class="text-danger">*</span></label>
                <input type="number" id="swal-amount" class="form-control form-control-sm mb-2" value="${amount}" min="0" step="1000">
                <label class="form-label small fw-bold mb-1">Catatan</label>
                <input type="text" id="swal-notes" class="form-control form-control-sm" value="${(notes||'').replace(/"/g,'&quot;')}">
            </div>`,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#8B5CF6',
        focusConfirm: false,
        preConfirm: () => {
            const name = document.getElementById('swal-name').value.trim();
            const amt  = document.getElementById('swal-amount').value;
            if (!name)             { Swal.showValidationMessage('Nama item wajib diisi'); return false; }
            if (!amt || amt < 0)   { Swal.showValidationMessage('Nominal tidak valid'); return false; }
            return {
                item_name:        name,
                estimated_amount: Number(amt),
                notes:            document.getElementById('swal-notes').value.trim() || null,
            };
        },
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`/budget-items/${itemId}`, {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(result.value),
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return Swal.fire('Error', d.message, 'error');

            const td    = btn.closest('.amount-cell');
            const amtEl = td.querySelector('.cell-amount');
            if (amtEl) {
                amtEl.textContent = Number(d.item.estimated_amount).toLocaleString('id-ID');
                amtEl.title = d.item.notes ?? '';
            }

            // Update / buat elemen cell-notes
            let notesEl = td.querySelector('.cell-notes');
            if (d.item.notes) {
                if (!notesEl) {
                    notesEl = document.createElement('div');
                    notesEl.className = 'cell-notes';
                    td.querySelector('.cell-actions').before(notesEl);
                }
                notesEl.textContent = `(${d.item.notes})`;
            } else if (notesEl) {
                notesEl.textContent = '';
            }

            // Update data attributes agar edit berikutnya pakai nilai terbaru
            actions.dataset.itemName = d.item.item_name;
            actions.dataset.amount   = d.item.estimated_amount;
            actions.dataset.notes    = d.item.notes ?? '';
        })
        .catch(() => Swal.fire('Error', 'Gagal menyimpan perubahan', 'error'));
    });
}

/* ─── Hapus item dari matrix ─── */
function deleteCellItem(btn) {
    const actions  = btn.closest('.cell-actions');
    const itemId   = actions.dataset.itemId;
    const td       = btn.closest('.amount-cell');
    const row      = td.closest('tr');
    const itemName = row.dataset.itemName;
    const category = row.dataset.category;
    const month    = td.dataset.month;
    const year     = td.dataset.year;

    Swal.fire({
        title: 'Hapus item ini?',
        text: 'Item akan dihapus dari budget.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#EF4444',
    }).then(r => {
        if (!r.isConfirmed) return;
        fetch(`/budget-items/${itemId}/delete`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return Swal.fire('Error', d.message, 'error');

            // Ganti isi sel ke kondisi "has-budget, no-item" (tampilkan tombol +)
            const escapedCat  = (category  || '').replace(/'/g, "\\'");
            const escapedName = (itemName  || '').replace(/'/g, "\\'");
            td.classList.remove('item-exists', 'completed');
            td.classList.add('has-budget', 'no-item');
            td.innerHTML = `<button class="btn-add-to-month"
                onclick="addItemToMonth(this,${month},${year},'${escapedCat}','${escapedName}')"
                title="Tambah ke bulan ini">+</button>`;
        })
        .catch(() => Swal.fire('Error', 'Gagal menghapus item', 'error'));
    });
}
</script>
@endpush
