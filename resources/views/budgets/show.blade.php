@extends('layouts.app')

@section('title', 'Budget ' . $budget->period . ' — STRACK')

@section('content')

{{-- ───────────────────────────── TOP NAV ───────────────────────────── --}}
<div class="budget-topbar luxury-card mb-3 px-3 py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">

    {{-- Navigasi prev --}}
    @if($prevBudget)
        <a href="{{ route('budgets.show', [$prevBudget->year, $prevBudget->month]) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-chevron-left"></i> {{ $prevBudget->period }}
        </a>
    @else
        <span class="btn btn-sm btn-outline-secondary disabled opacity-50"><i class="bi bi-chevron-left"></i> Belum ada</span>
    @endif

    {{-- Judul periode --}}
    <div class="text-center flex-grow-1">
        <span class="fw-semibold text-purple fs-6">
            {{ $prevBudget ? $prevBudget->period : '—' }}
            <span class="text-secondary mx-2">●</span>
            {{ $budget->period }}
        </span>
    </div>

    {{-- Navigasi next --}}
    @if($nextBudget)
        <a href="{{ route('budgets.show', [$nextBudget->year, $nextBudget->month]) }}" class="btn btn-sm btn-outline-primary">
            {{ $nextBudget->period }} <i class="bi bi-chevron-right"></i>
        </a>
    @else
        <span class="btn btn-sm btn-outline-secondary disabled opacity-50">Belum ada <i class="bi bi-chevron-right"></i></span>
    @endif
</div>

{{-- ─────────────────────────── ACTION BAR ──────────────────────────── --}}
<div class="luxury-card mb-3 px-3 py-2 d-flex align-items-center flex-wrap gap-2">
    <a href="{{ route('budgets.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-grid me-1"></i>Semua Budget
    </a>
    <a href="{{ route('budgets.edit', [$budget->year, $budget->month]) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a href="{{ route('budgets.export', [$budget->year, $budget->month]) }}" class="btn btn-sm btn-outline-success">
        <i class="bi bi-download me-1"></i>Export Excel
    </a>
    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-upload me-1"></i>Import Excel
    </button>
    <a href="{{ route('budgets.report', $budget->year) }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-graph-up me-1"></i>Laporan {{ $budget->year }}
    </a>
    <button class="btn btn-sm btn-outline-danger ms-auto" onclick="confirmDelete()">
        <i class="bi bi-trash me-1"></i>Hapus
    </button>
</div>

{{-- ──────────────────────────── EXCEL GRID ─────────────────────────── --}}
<div class="excel-grid-wrapper luxury-card overflow-hidden">

    {{-- ── COLUMN HEADERS ── --}}
    <div class="excel-col-headers">

        <div class="excel-col-head left-head">
            @if($prevBudget)
                <div class="col-head-title">{{ $prevBudget->period }}</div>
                <div class="col-head-meta">
                    <span class="fw-bold text-purple">{{ $prevBudget->formatted_budget }}</span>
                    <span class="text-muted ms-2 fs-8">{{ $prevBudget->completed_items_count }}/{{ $prevBudget->total_items_count }} item selesai</span>
                    <div class="progress mt-1" style="height:3px">
                        <div class="progress-bar bg-purple" style="width:{{ $prevBudget->progress_percentage }}%"></div>
                    </div>
                </div>
            @else
                <div class="col-head-title text-muted">—</div>
                <div class="col-head-meta text-muted fs-8">Belum ada budget bulan lalu</div>
            @endif
        </div>

        <div class="excel-col-divider-head"></div>

        <div class="excel-col-head right-head">
            <div class="col-head-title">{{ $budget->period }}</div>
            <div class="col-head-meta">
                <span class="fw-bold text-purple" id="rightColTotal">{{ $budget->formatted_budget }}</span>
                <span class="text-muted ms-2 fs-8" id="rightColProgress">{{ $budget->completed_items_count }}/{{ $budget->total_items_count }} item selesai</span>
                <div class="progress mt-1" style="height:3px">
                    <div class="progress-bar bg-purple" id="rightColBar" style="width:{{ $budget->progress_percentage }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TWO COLUMNS BODY ── --}}
    <div class="excel-body">

        {{-- ══════ KOLOM KIRI (bulan lalu) ══════ --}}
        <div class="excel-col left-col">
            @if($prevBudget)
                @php $leftGrouped = $prevBudget->items_grouped_by_category; @endphp
                @forelse($leftGrouped as $catName => $catData)
                    <div class="cat-section">

                        <div class="cat-header">
                            <span class="cat-name">[{{ $catName }}]</span>
                            <span class="cat-total">{{ number_format($catData['total_amount'], 0, ',', '.') }}</span>
                            <button class="btn-cat-toggle {{ $catData['is_completed'] ? 'all-done' : '' }}"
                                onclick="toggleCategory(this, {{ $prevBudget->month }}, {{ $prevBudget->year }}, '{{ addslashes($catName) }}', {{ $catData['is_completed'] ? 0 : 1 }})">
                                {{ $catData['is_completed'] ? '☑' : '☐' }}
                            </button>
                        </div>

                        <div class="cat-items-list">
                            @foreach($catData['items'] as $item)
                                <div class="item-row {{ $item->is_completed ? 'completed' : '' }}" data-id="{{ $item->id }}">
                                    <div class="item-display">
                                        <span class="toggle-prefix {{ $item->is_completed ? 'done' : '' }}"
                                            onclick="toggleItem({{ $item->id }}, this)">{{ $item->is_completed ? 'x' : '○' }}</span>
                                        <span class="item-name">{{ $item->item_name }}</span>
                                        <span class="item-amount">{{ number_format($item->estimated_amount, 0, ',', '.') }}</span>
                                        @if($item->notes)<span class="item-notes">({{ $item->notes }})</span>@endif
                                        <div class="item-actions">
                                            <button class="btn-edit" onclick="startEdit(this, {{ $item->id }})" title="Edit"><i class="bi bi-pencil-fill"></i></button>
                                            <button class="btn-delete" onclick="deleteItem(this, {{ $item->id }})" title="Hapus">×</button>
                                        </div>
                                    </div>
                                    <div class="item-edit-form">
                                        <input type="text" class="ei-name" value="{{ $item->item_name }}" placeholder="Nama item">
                                        <input type="number" class="ei-amount" value="{{ $item->estimated_amount }}" placeholder="Jumlah">
                                        <input type="text" class="ei-notes" value="{{ $item->notes ?? '' }}" placeholder="Catatan">
                                        <button class="ei-save" onclick="saveEdit(this, {{ $item->id }})"><i class="bi bi-check-lg"></i></button>
                                        <button class="ei-cancel" onclick="cancelEdit(this)"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Add item kiri --}}
                        <div class="add-item-trigger"
                             data-month="{{ $prevBudget->month }}"
                             data-year="{{ $prevBudget->year }}"
                             data-category="{{ $catName }}">
                            <button class="btn-add-trigger" onclick="showAddForm(this)">
                                <i class="bi bi-plus-sm"></i> tambah item
                            </button>
                            <div class="add-item-form" style="display:none">
                                <input type="text" class="ei-name" placeholder="Nama item">
                                <input type="number" class="ei-amount" placeholder="Jumlah" min="0" step="1000">
                                <input type="text" class="ei-notes" placeholder="Catatan (opsional)">
                                <button class="ei-save" onclick="saveNewItem(this)"><i class="bi bi-check-lg"></i></button>
                                <button class="ei-cancel" onclick="cancelAddForm(this)"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="empty-col-msg">Tidak ada item</div>
                @endforelse
            @else
                <div class="empty-col-msg">
                    <i class="bi bi-calendar-x d-block mb-2" style="font-size:2rem;color:#D1D5DB"></i>
                    Belum ada budget<br>untuk bulan lalu
                    <br><a href="{{ route('budgets.create') }}" class="btn btn-sm btn-outline-primary mt-2">Buat Budget</a>
                </div>
            @endif
        </div>

        {{-- ── Divider ── --}}
        <div class="excel-col-divider"></div>

        {{-- ══════ KOLOM KANAN (bulan ini) ══════ --}}
        <div class="excel-col right-col">
            @php $rightGrouped = $budget->items_grouped_by_category; @endphp
            @forelse($rightGrouped as $catName => $catData)
                <div class="cat-section" data-month="{{ $budget->month }}" data-year="{{ $budget->year }}" data-category="{{ $catName }}">

                    <div class="cat-header">
                        <span class="cat-name">[{{ $catName }}]</span>
                        <span class="cat-total" id="ct-{{ $budget->month }}-{{ Str::slug($catName) }}">
                            {{ number_format($catData['total_amount'], 0, ',', '.') }}
                        </span>
                        <button class="btn-cat-toggle {{ $catData['is_completed'] ? 'all-done' : '' }}"
                            onclick="toggleCategory(this, {{ $budget->month }}, {{ $budget->year }}, '{{ addslashes($catName) }}', {{ $catData['is_completed'] ? 0 : 1 }})">
                            {{ $catData['is_completed'] ? '☑' : '☐' }}
                        </button>
                    </div>

                    <div class="cat-items-list" id="items-{{ $budget->month }}-{{ Str::slug($catName) }}">
                        @foreach($catData['items'] as $item)
                            <div class="item-row {{ $item->is_completed ? 'completed' : '' }}" data-id="{{ $item->id }}">
                                <div class="item-display">
                                    <span class="toggle-prefix {{ $item->is_completed ? 'done' : '' }}"
                                        onclick="toggleItem({{ $item->id }}, this)">{{ $item->is_completed ? 'x' : '○' }}</span>
                                    <span class="item-name">{{ $item->item_name }}</span>
                                    <span class="item-amount">{{ number_format($item->estimated_amount, 0, ',', '.') }}</span>
                                    @if($item->notes)<span class="item-notes">({{ $item->notes }})</span>@endif
                                    <div class="item-actions">
                                        <button class="btn-edit" onclick="startEdit(this, {{ $item->id }})" title="Edit"><i class="bi bi-pencil-fill"></i></button>
                                        <button class="btn-delete" onclick="deleteItem(this, {{ $item->id }})" title="Hapus">×</button>
                                    </div>
                                </div>
                                <div class="item-edit-form">
                                    <input type="text" class="ei-name" value="{{ $item->item_name }}" placeholder="Nama item">
                                    <input type="number" class="ei-amount" value="{{ $item->estimated_amount }}" placeholder="Jumlah">
                                    <input type="text" class="ei-notes" value="{{ $item->notes ?? '' }}" placeholder="Catatan">
                                    <button class="ei-save" onclick="saveEdit(this, {{ $item->id }})"><i class="bi bi-check-lg"></i></button>
                                    <button class="ei-cancel" onclick="cancelEdit(this)"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="add-item-trigger"
                         data-month="{{ $budget->month }}"
                         data-year="{{ $budget->year }}"
                         data-category="{{ $catName }}">
                        <button class="btn-add-trigger" onclick="showAddForm(this)">
                            <i class="bi bi-plus-sm"></i> tambah item
                        </button>
                        <div class="add-item-form" style="display:none">
                            <input type="text" class="ei-name" placeholder="Nama item">
                            <input type="number" class="ei-amount" placeholder="Jumlah" min="0" step="1000">
                            <input type="text" class="ei-notes" placeholder="Catatan (opsional)">
                            <button class="ei-save" onclick="saveNewItem(this)"><i class="bi bi-check-lg"></i></button>
                            <button class="ei-cancel" onclick="cancelAddForm(this)"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>

                </div>
            @empty
                <div class="empty-col-msg">Tidak ada item. Tambahkan kategori baru di bawah.</div>
            @endforelse

            {{-- ── Tambah Kategori Baru ── --}}
            <div class="add-category-section">
                <button class="btn-add-category" onclick="showNewCategoryForm(this)">
                    <i class="bi bi-folder-plus me-1"></i>+ Kategori Baru
                </button>
                <div class="new-category-form" style="display:none">
                    <input type="text" id="newCatName" placeholder="Nama kategori" list="catSuggestions">
                    <datalist id="catSuggestions">
                        @foreach($allCategories as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                    <input type="text" id="newCatItemName" placeholder="Nama item pertama">
                    <input type="number" id="newCatItemAmount" placeholder="Jumlah" min="0" step="1000">
                    <input type="text" id="newCatItemNotes" placeholder="Catatan (opsional)">
                    <button onclick="saveNewCategory({{ $budget->year }}, {{ $budget->month }})"><i class="bi bi-check-lg"></i> Simpan</button>
                    <button onclick="cancelNewCategory()"><i class="bi bi-x-lg"></i> Batal</button>
                </div>
            </div>

        </div>{{-- end right-col --}}
    </div>{{-- end excel-body --}}

    {{-- ── FOOTER TOTAL ── --}}
    <div class="excel-footer">
        <div class="footer-left">
            @if($prevBudget)
                <span class="fw-bold">TOTAL: {{ $prevBudget->formatted_budget }}</span>
                <span class="text-muted ms-2 fs-8">
                    Lunas: Rp {{ number_format($prevBudget->completed_amount, 0, ',', '.') }}
                    · Sisa: Rp {{ number_format($prevBudget->remaining_amount, 0, ',', '.') }}
                </span>
            @endif
        </div>
        <div class="footer-divider"></div>
        <div class="footer-right">
            <span class="fw-bold" id="footerTotal">TOTAL: {{ $budget->formatted_budget }}</span>
            <span class="text-muted ms-2 fs-8" id="footerSub">
                Lunas: Rp {{ number_format($budget->completed_amount, 0, ',', '.') }}
                · Sisa: Rp {{ number_format($budget->remaining_amount, 0, ',', '.') }}
            </span>
        </div>
    </div>

</div>{{-- end excel-grid-wrapper --}}

{{-- ─────────────────────── IMPORT MODAL ───────────────────────── --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content luxury-card border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-purple"><i class="bi bi-upload me-2"></i>Import Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('budgets.import', [$budget->year, $budget->month]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Upload file Excel (.xlsx / .xls) untuk mengimport item ke budget <strong>{{ $budget->period }}</strong>.</p>
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

{{-- ─────────────────────── DELETE FORM ────────────────────────── --}}
<form id="deleteForm" action="{{ route('budgets.destroy', [$budget->year, $budget->month]) }}" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>

@endsection

@push('scripts')
<style>
/* ─── Budget Excel Grid ─── */
.excel-grid-wrapper { border-radius: 16px; overflow: hidden; }

.excel-col-headers {
    display: grid;
    grid-template-columns: 1fr 2px 1fr;
    background: linear-gradient(135deg, rgba(139,92,246,.08), rgba(168,85,247,.12));
    border-bottom: 2px solid rgba(139,92,246,.15);
}
.excel-col-head { padding: 14px 16px; }
.col-head-title { font-size:.95rem; font-weight:700; color:#374151; margin-bottom:4px; }
.col-head-meta  { font-size:.82rem; }
.excel-col-divider-head { background:rgba(139,92,246,.2); }

.excel-body {
    display: grid;
    grid-template-columns: 1fr 2px 1fr;
    align-items: start;
}
.excel-col { padding-bottom: 8px; }
.excel-col-divider { background:rgba(139,92,246,.15); }

/* ─── Category ─── */
.cat-section { border-bottom: 1px solid rgba(139,92,246,.07); padding-bottom:2px; }
.cat-header {
    display: flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, rgba(139,92,246,.07), rgba(168,85,247,.1));
    padding: 5px 12px;
    border-bottom: 1px solid rgba(139,92,246,.1);
}
.cat-name  { font-weight:700; font-size:.8rem; color:#6D28D9; flex:1; text-transform:uppercase; letter-spacing:.2px; }
.cat-total { font-size:.78rem; font-weight:600; color:#374151; font-variant-numeric:tabular-nums; }
.btn-cat-toggle {
    background:none; border:none; padding:0 2px; font-size:.95rem; cursor:pointer;
    color:#D1D5DB; line-height:1; transition:color .15s;
}
.btn-cat-toggle:hover, .btn-cat-toggle.all-done { color:#22c55e; }

/* ─── Item Row ─── */
.item-row {
    display: flex; flex-direction: column;
    padding: 0 12px 0 8px;
    border-bottom: 1px solid rgba(139,92,246,.03);
    min-height: 28px; position: relative;
    transition: background .12s;
}
.item-row:hover { background: rgba(139,92,246,.04); }
.item-row.completed .item-name,
.item-row.completed .item-amount { color:#9CA3AF; text-decoration:line-through; }
.item-row.completed .item-notes  { color:#C4C4C4; }

.item-display { display:flex; align-items:center; width:100%; gap:5px; padding: 4px 0; }

.toggle-prefix {
    font-family: monospace; font-weight:700; font-size:.78rem;
    width:18px; flex-shrink:0; cursor:pointer;
    color:#D1D5DB; user-select:none; transition:color .15s;
}
.toggle-prefix:hover { color:#8B5CF6; }
.toggle-prefix.done  { color:#22c55e; }

.item-name   { flex:1; font-size:.82rem; color:#374151; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.item-amount { font-size:.82rem; font-weight:600; color:#374151; font-variant-numeric:tabular-nums; white-space:nowrap; min-width:65px; text-align:right; flex-shrink:0; }
.item-notes  { font-size:.72rem; color:#9CA3AF; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px; flex-shrink:0; }

.item-actions { display:none; gap:1px; flex-shrink:0; }
.item-row:hover .item-actions { display:flex; }
.item-actions button {
    background:none; border:none; padding:1px 4px; font-size:.72rem;
    cursor:pointer; border-radius:4px; color:#9CA3AF; line-height:1; transition:color .15s, background .15s;
}
.item-actions .btn-edit:hover   { color:#8B5CF6; background:rgba(139,92,246,.1); }
.item-actions .btn-delete:hover { color:#EF4444; background:rgba(239,68,68,.1); }

/* ─── Inline Edit Form ─── */
.item-edit-form {
    display: none; align-items: center; gap: 4px;
    flex-wrap: wrap; padding: 4px 0;
}
.item-edit-form.active { display: flex; }
.item-edit-form .ei-name   { flex:2; min-width:80px; }
.item-edit-form .ei-amount { flex:1; min-width:60px; }
.item-edit-form .ei-notes  { flex:1.5; min-width:70px; }
.item-edit-form input, .add-item-form input, .new-category-form input {
    border:1px solid rgba(139,92,246,.3); border-radius:6px;
    padding:3px 7px; font-size:.78rem; outline:none; background:white;
}
.item-edit-form input:focus, .add-item-form input:focus, .new-category-form input:focus {
    border-color:#8B5CF6; box-shadow:0 0 0 2px rgba(139,92,246,.1);
}
.ei-save, .ei-cancel {
    border:none; border-radius:6px; padding:3px 8px; font-size:.78rem; cursor:pointer; flex-shrink:0;
}
.ei-save   { background:rgba(34,197,94,.15); color:#16a34a; }
.ei-cancel { background:rgba(239,68,68,.1); color:#dc2626; }

/* ─── Add Item Trigger ─── */
.add-item-trigger { padding: 3px 12px; }
.btn-add-trigger {
    background:none; border:none; font-size:.75rem; color:#C4B5FD;
    cursor:pointer; padding:2px 4px; border-radius:5px; transition:color .15s, background .15s;
}
.btn-add-trigger:hover { color:#8B5CF6; background:rgba(139,92,246,.08); }

.add-item-form { display:flex; gap:4px; flex-wrap:wrap; align-items:center; padding:4px 0; }
.add-item-form .ei-name   { flex:2; min-width:100px; }
.add-item-form .ei-amount { flex:1; min-width:70px; }
.add-item-form .ei-notes  { flex:1.5; min-width:80px; }

/* ─── Add Category ─── */
.add-category-section { padding:10px 12px; border-top:1px dashed rgba(139,92,246,.15); margin-top:4px; }
.btn-add-category {
    background:none; border:1px dashed rgba(139,92,246,.35); border-radius:8px;
    padding:5px 14px; font-size:.78rem; color:#8B5CF6; cursor:pointer; transition:all .15s;
}
.btn-add-category:hover { background:rgba(139,92,246,.08); }

.new-category-form { display:flex; flex-wrap:wrap; gap:6px; padding-top:8px; align-items:center; }
.new-category-form input { flex:1; min-width:100px; padding:4px 8px; font-size:.8rem; }
.new-category-form button {
    border:none; border-radius:8px; padding:5px 12px; font-size:.8rem; cursor:pointer;
}
.new-category-form button:first-of-type { background:rgba(139,92,246,.15); color:#6D28D9; }
.new-category-form button:last-of-type  { background:rgba(239,68,68,.1); color:#dc2626; }

/* ─── Empty State ─── */
.empty-col-msg { text-align:center; padding:40px 20px; color:#9CA3AF; font-size:.82rem; }

/* ─── Footer ─── */
.excel-footer {
    display: grid; grid-template-columns: 1fr 2px 1fr;
    background: linear-gradient(135deg, rgba(139,92,246,.06), rgba(168,85,247,.09));
    border-top: 2px solid rgba(139,92,246,.12);
}
.footer-left, .footer-right { padding:10px 16px; font-size:.82rem; }
.footer-divider { background:rgba(139,92,246,.15); }

/* ─── Responsive ─── */
@media (max-width:767px) {
    .excel-col-headers, .excel-body, .excel-footer { grid-template-columns:1fr; }
    .excel-col-divider, .excel-col-divider-head, .footer-divider { display:none; }
    .left-col   { border-bottom:3px solid rgba(139,92,246,.2); padding-bottom:12px; margin-bottom:4px; }
    .left-head  { border-bottom:1px solid rgba(139,92,246,.1); }
    .footer-left{ border-bottom:1px solid rgba(139,92,246,.1); }
    .item-notes { display:none; }
}

.bg-purple   { background-color:#8B5CF6 !important; }
.text-purple { color:#8B5CF6 !important; }
</style>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

/* ─── Toggle single item ─── */
function toggleItem(itemId, el) {
    fetch(`/budget-items/${itemId}/toggle-complete`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) return;
        const row = el.closest('.item-row');
        if (d.is_completed) {
            row.classList.add('completed');
            el.textContent = 'x'; el.classList.add('done');
        } else {
            row.classList.remove('completed');
            el.textContent = '○'; el.classList.remove('done');
        }
    });
}

/* ─── Toggle category ─── */
function toggleCategory(btn, month, year, category, markComplete) {
    fetch('/budget-category/toggle', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ month, year, category, mark_complete: markComplete === 1 }),
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) return;
        const section = btn.closest('.cat-section') || btn.closest('.cat-header').parentElement;
        section.querySelectorAll('.item-row').forEach(row => {
            const pfx = row.querySelector('.toggle-prefix');
            if (markComplete === 1) {
                row.classList.add('completed');
                if (pfx) { pfx.textContent = 'x'; pfx.classList.add('done'); }
            } else {
                row.classList.remove('completed');
                if (pfx) { pfx.textContent = '○'; pfx.classList.remove('done'); }
            }
        });
        btn.textContent = markComplete === 1 ? '☑' : '☐';
        btn.classList.toggle('all-done', markComplete === 1);
        const next = markComplete === 1 ? 0 : 1;
        btn.setAttribute('onclick', `toggleCategory(this,${month},${year},'${category.replace(/'/g,"\\'")}',${next})`);
    });
}

/* ─── Inline Edit ─── */
function startEdit(btn, itemId) {
    const row = btn.closest('.item-row');
    row.querySelector('.item-display').style.display = 'none';
    const form = row.querySelector('.item-edit-form');
    form.classList.add('active');
    form.querySelector('.ei-name').focus();
}
function cancelEdit(btn) {
    const form = btn.closest('.item-edit-form');
    form.classList.remove('active');
    form.closest('.item-row').querySelector('.item-display').style.display = 'flex';
}
function saveEdit(btn, itemId) {
    const form   = btn.closest('.item-edit-form');
    const name   = form.querySelector('.ei-name').value.trim();
    const amount = form.querySelector('.ei-amount').value;
    const notes  = form.querySelector('.ei-notes').value.trim();
    if (!name || !amount) return Swal.fire('Perhatian','Nama dan jumlah wajib diisi','warning');
    fetch(`/budget-items/${itemId}`, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ item_name: name, estimated_amount: amount, notes: notes || null }),
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) return Swal.fire('Error', d.message, 'error');
        const row = form.closest('.item-row');
        row.querySelector('.item-name').textContent   = d.item.item_name;
        row.querySelector('.item-amount').textContent = Number(d.item.estimated_amount).toLocaleString('id-ID');
        const notesEl = row.querySelector('.item-notes');
        if (notesEl) notesEl.textContent = d.item.notes ? `(${d.item.notes})` : '';
        cancelEdit(btn);
    });
}

/* ─── Delete item ─── */
function deleteItem(btn, itemId) {
    Swal.fire({
        title:'Hapus item ini?', text:'Item akan dihapus permanen.', icon:'warning',
        showCancelButton:true, confirmButtonText:'Hapus', cancelButtonText:'Batal',
        confirmButtonColor:'#EF4444',
    }).then(r => {
        if (!r.isConfirmed) return;
        fetch(`/budget-items/${itemId}/delete`, {
            method:'DELETE', headers:{ 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return Swal.fire('Error', d.message, 'error');
            btn.closest('.item-row').remove();
            updateRightTotal(d.new_total);
        });
    });
}

/* ─── Add item form ─── */
function showAddForm(btn) {
    const trigger = btn.closest('.add-item-trigger');
    btn.style.display = 'none';
    trigger.querySelector('.add-item-form').style.display = 'flex';
    trigger.querySelector('.ei-name').focus();
}
function cancelAddForm(btn) {
    const trigger = btn.closest('.add-item-trigger');
    trigger.querySelector('.add-item-form').style.display = 'none';
    trigger.querySelector('.btn-add-trigger').style.display = '';
    trigger.querySelectorAll('input').forEach(i => i.value = '');
}
function saveNewItem(btn) {
    const trigger  = btn.closest('.add-item-trigger');
    const month    = trigger.dataset.month;
    const year     = trigger.dataset.year;
    const category = trigger.dataset.category;
    const name     = trigger.querySelector('.ei-name').value.trim();
    const amount   = trigger.querySelector('.ei-amount').value;
    const notes    = trigger.querySelector('.ei-notes').value.trim();
    if (!name || !amount) return Swal.fire('Perhatian','Nama dan jumlah wajib diisi','warning');
    fetch(`/budgets/${year}/${month}/items`, {
        method:'POST',
        headers:{ 'X-CSRF-TOKEN':CSRF, 'Content-Type':'application/json', 'Accept':'application/json' },
        body: JSON.stringify({ item_name: name, estimated_amount: amount, notes: notes || null, category }),
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) return Swal.fire('Error', d.message, 'error');
        const list = trigger.closest('.cat-section').querySelector('.cat-items-list');
        list.insertAdjacentHTML('beforeend', buildItemHtml(d.item));
        cancelAddForm(btn);
        updateRightTotal(d.new_total);
    });
}

/* ─── New Category ─── */
function showNewCategoryForm(btn) {
    btn.style.display = 'none';
    btn.nextElementSibling.style.display = 'flex';
    document.getElementById('newCatName').focus();
}
function cancelNewCategory() {
    document.querySelector('.new-category-form').style.display = 'none';
    document.querySelector('.btn-add-category').style.display = '';
    ['newCatName','newCatItemName','newCatItemAmount','newCatItemNotes'].forEach(id => {
        document.getElementById(id).value = '';
    });
}
function saveNewCategory(year, month) {
    const catName  = document.getElementById('newCatName').value.trim();
    const itemName = document.getElementById('newCatItemName').value.trim();
    const amount   = document.getElementById('newCatItemAmount').value;
    const notes    = document.getElementById('newCatItemNotes').value.trim();
    if (!catName || !itemName || !amount) return Swal.fire('Perhatian','Kategori, nama item, dan jumlah wajib diisi','warning');
    fetch(`/budgets/${year}/${month}/items`, {
        method:'POST',
        headers:{ 'X-CSRF-TOKEN':CSRF, 'Content-Type':'application/json', 'Accept':'application/json' },
        body: JSON.stringify({ item_name: itemName, estimated_amount: amount, notes: notes || null, category: catName }),
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) return Swal.fire('Error', d.message, 'error');
        window.location.reload();
    });
}

/* ─── Update totals ─── */
function updateRightTotal(newTotal) {
    const fmt = 'Rp ' + Number(newTotal).toLocaleString('id-ID');
    const el = document.getElementById('rightColTotal');
    if (el) el.textContent = fmt;
    const ft = document.getElementById('footerTotal');
    if (ft) ft.textContent = 'TOTAL: ' + fmt;
}

/* ─── Build item HTML ─── */
function buildItemHtml(item) {
    const notesHtml = item.notes ? `<span class="item-notes">(${item.notes})</span>` : '';
    const amt = Number(item.estimated_amount).toLocaleString('id-ID');
    return `
<div class="item-row" data-id="${item.id}">
  <div class="item-display">
    <span class="toggle-prefix" onclick="toggleItem(${item.id},this)">○</span>
    <span class="item-name">${item.item_name}</span>
    <span class="item-amount">${amt}</span>
    ${notesHtml}
    <div class="item-actions">
      <button class="btn-edit" onclick="startEdit(this,${item.id})"><i class="bi bi-pencil-fill"></i></button>
      <button class="btn-delete" onclick="deleteItem(this,${item.id})">×</button>
    </div>
  </div>
  <div class="item-edit-form">
    <input type="text" class="ei-name" value="${item.item_name}" placeholder="Nama item">
    <input type="number" class="ei-amount" value="${item.estimated_amount}" placeholder="Jumlah">
    <input type="text" class="ei-notes" value="${item.notes||''}" placeholder="Catatan">
    <button class="ei-save" onclick="saveEdit(this,${item.id})"><i class="bi bi-check-lg"></i></button>
    <button class="ei-cancel" onclick="cancelEdit(this)"><i class="bi bi-x-lg"></i></button>
  </div>
</div>`;
}

/* ─── Delete budget ─── */
function confirmDelete() {
    Swal.fire({
        title:'Hapus Budget?', text:'Semua item akan ikut terhapus!', icon:'warning',
        showCancelButton:true, confirmButtonText:'Ya, hapus!', cancelButtonText:'Batal',
        confirmButtonColor:'#EF4444',
    }).then(r => { if (r.isConfirmed) document.getElementById('deleteForm').submit(); });
}

/* ─── Enter key in edit forms ─── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const activeForm = document.querySelector('.item-edit-form.active');
        if (activeForm) cancelEdit(activeForm.querySelector('.ei-cancel'));
    }
});
</script>
@endpush
