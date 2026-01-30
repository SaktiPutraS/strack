@extends('layouts.app')
@section('title', 'Detail Budget')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-calendar-check me-2"></i>Budget {{ $budget->period }}
                    </h1>
                    <p class="text-muted mb-0">
                        <span class="badge bg-{{ $budget->status_color }} me-2">{{ $budget->status_text }}</span>
                        {{ $budget->total_items_count }} item pengeluaran
                    </p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Budget
                    </a>
                    <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigasi Pindah Bulan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card luxury-card border-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        @if($prevBudget)
                            <a href="{{ route('budgets.show', $prevBudget) }}" class="btn btn-outline-primary">
                                <i class="bi bi-chevron-left me-1"></i>
                                {{ $prevBudget->period }}
                            </a>
                        @else
                            <span class="btn btn-outline-secondary disabled">
                                <i class="bi bi-chevron-left me-1"></i>
                                Bulan Sebelumnya
                            </span>
                        @endif

                        <span class="fw-bold text-purple">
                            <i class="bi bi-calendar3 me-2"></i>{{ $budget->period }}
                        </span>

                        @if($nextBudget)
                            <a href="{{ route('budgets.show', $nextBudget) }}" class="btn btn-outline-primary">
                                {{ $nextBudget->period }}
                                <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        @else
                            <span class="btn btn-outline-secondary disabled">
                                Bulan Berikutnya
                                <i class="bi bi-chevron-right ms-1"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Budget Items -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-list-check text-purple"></i>
                            </div>
                            Daftar Item Pengeluaran
                        </h5>
                        <div class="text-end">
                            <small class="text-muted d-block">Progress</small>
                            <strong class="text-{{ $budget->status_color }}">
                                {{ $budget->completed_items_count }}/{{ $budget->total_items_count }}
                            </strong>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 10px;">
                        <div class="progress-bar bg-{{ $budget->status_color }}" role="progressbar" style="width: {{ $budget->progress_percentage }}%">
                        </div>
                    </div>
                    <small class="text-muted">{{ $budget->progress_percentage }}% selesai</small>

                    <!-- Bulk Actions -->
                    @if ($budget->items->count() > 0)
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll" style="width: 20px; height: 20px;">
                                    <label class="form-check-label ms-1" for="selectAll">Pilih Semua</label>
                                </div>
                                <span class="text-muted" id="selectedCount">(0 dipilih)</span>
                                <div class="ms-auto d-flex gap-2" id="bulkActions" style="display: none !important;">
                                    <button type="button" class="btn btn-success btn-sm" id="bulkComplete" disabled>
                                        <i class="bi bi-check-circle me-1"></i>Tandai Selesai
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="bulkUncomplete" disabled>
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Batalkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-body p-0">
                    @php $groupedItems = $budget->items_grouped_by_category; @endphp
                    @if (count($groupedItems) > 0)
                        <div class="p-4">
                            <!-- Toggle All Button -->
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleAllCategories">
                                    <i class="bi bi-eye-slash me-1"></i>
                                    <span>Sembunyikan Semua</span>
                                </button>
                            </div>
                            <div class="accordion" id="categoryAccordion">
                                @foreach ($groupedItems as $categoryKey => $category)
                                    @php
                                        $categoryId = 'category-' . Str::slug($categoryKey);
                                        $isCompleted = $category['is_completed'];
                                    @endphp
                                    <div class="category-group mb-3 border rounded {{ $isCompleted ? 'border-success' : '' }}"
                                        data-category="{{ $categoryKey }}">
                                        <!-- Category Header -->
                                        <div class="category-header p-3 d-flex align-items-center gap-3 {{ $isCompleted ? 'bg-success bg-opacity-10' : 'bg-light' }}"
                                            style="cursor: pointer;"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#{{ $categoryId }}"
                                            aria-expanded="true">

                                            <!-- Category Checkbox -->
                                            <div class="flex-shrink-0" onclick="event.stopPropagation();">
                                                <input type="checkbox"
                                                    class="form-check-input category-checkbox"
                                                    data-category="{{ $categoryKey }}"
                                                    {{ $isCompleted ? 'checked' : '' }}
                                                    style="width: 24px; height: 24px; cursor: pointer;">
                                            </div>

                                            <!-- Category Icon -->
                                            <div class="flex-shrink-0">
                                                @if ($isCompleted)
                                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                                @else
                                                    <i class="bi bi-folder2 text-purple fs-4"></i>
                                                @endif
                                            </div>

                                            <!-- Category Info -->
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-bold {{ $isCompleted ? 'text-success' : 'text-dark' }}">
                                                    {{ $categoryKey }}
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $category['completed_count'] }}/{{ $category['total_count'] }} item selesai
                                                    &bull; Rp {{ number_format($category['total_amount'], 0, ',', '.') }}
                                                </small>
                                            </div>

                                            <!-- Category Progress -->
                                            <div class="flex-shrink-0 text-end me-2">
                                                <span class="badge bg-{{ $isCompleted ? 'success' : ($category['progress'] > 0 ? 'warning' : 'secondary') }} px-3 py-2">
                                                    {{ $category['progress'] }}%
                                                </span>
                                            </div>

                                            <!-- Collapse Icon -->
                                            <div class="flex-shrink-0">
                                                <i class="bi bi-chevron-down collapse-icon"></i>
                                            </div>
                                        </div>

                                        <!-- Category Items (Collapsible) -->
                                        <div class="collapse show" id="{{ $categoryId }}">
                                            <div class="category-items p-3 pt-0">
                                                @foreach ($category['items'] as $item)
                                                    <div class="budget-item {{ $item->is_completed ? 'completed' : '' }} mb-2 p-3 border rounded"
                                                        data-item-id="{{ $item->id }}"
                                                        data-category="{{ $categoryKey }}">
                                                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">
                                                            <!-- Checkbox for bulk select -->
                                                            <div class="flex-shrink-0">
                                                                <input type="checkbox" class="form-check-input item-checkbox"
                                                                    data-item-id="{{ $item->id }}"
                                                                    data-category="{{ $categoryKey }}"
                                                                    style="width: 22px; height: 22px; cursor: pointer;">
                                                            </div>

                                                            <!-- Item Info -->
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex align-items-start">
                                                                    <div class="me-3">
                                                                        @if ($item->is_completed)
                                                                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                                                        @else
                                                                            <i class="bi bi-circle text-warning fs-4"></i>
                                                                        @endif
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-1 fw-semibold item-name {{ $item->is_completed ? 'text-decoration-line-through text-muted' : 'text-dark' }}"
                                                                            data-item-id="{{ $item->id }}">
                                                                            {{ $item->item_name }}
                                                                        </h6>
                                                                        <div class="item-notes" data-item-id="{{ $item->id }}">
                                                                            @if ($item->notes)
                                                                                <small class="text-muted">→ {{ $item->notes }}</small>
                                                                            @endif
                                                                        </div>
                                                                        @if ($item->is_completed && $item->completed_at)
                                                                            <div class="mt-1">
                                                                                <small class="text-success">
                                                                                    <i class="bi bi-calendar-check me-1"></i>
                                                                                    {{ $item->completed_date }}
                                                                                </small>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Amount Badge -->
                                                            <div class="flex-shrink-0">
                                                                <span class="badge bg-{{ $item->is_completed ? 'success' : 'warning' }} bg-opacity-10
                                                                    text-{{ $item->is_completed ? 'success' : 'warning' }}
                                                                    border border-{{ $item->is_completed ? 'success' : 'warning' }} fs-6 px-3 py-2 item-amount"
                                                                    data-item-id="{{ $item->id }}">
                                                                    {{ $item->formatted_amount }}
                                                                </span>
                                                            </div>

                                                            <!-- Action Buttons -->
                                                            <div class="flex-shrink-0 d-flex gap-2">
                                                                <!-- Edit Button -->
                                                                <button type="button" class="btn btn-outline-primary edit-item-btn px-3 py-2"
                                                                    data-item-id="{{ $item->id }}"
                                                                    data-item-name="{{ $item->item_name }}"
                                                                    data-item-amount="{{ $item->estimated_amount }}"
                                                                    data-item-notes="{{ $item->notes }}"
                                                                    data-item-category="{{ $item->category }}">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>

                                                                <!-- Toggle Complete Button -->
                                                                @if ($item->is_completed)
                                                                    <button type="button" class="btn btn-outline-secondary toggle-complete-btn px-3 py-2"
                                                                        data-item-id="{{ $item->id }}">
                                                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Batalkan
                                                                    </button>
                                                                @else
                                                                    <button type="button" class="btn btn-success toggle-complete-btn px-3 py-2"
                                                                        data-item-id="{{ $item->id }}">
                                                                        <i class="bi bi-check-circle me-1"></i>Tandai Selesai
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">Tidak ada item</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if ($budget->notes)
                <div class="card luxury-card border-0">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-journal-text text-purple"></i>
                            </div>
                            Catatan Budget
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-0 text-muted">{{ $budget->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Summary -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-calculator text-purple"></i>
                        </div>
                        Ringkasan Budget
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <small class="text-muted d-block mb-1">Total Budget</small>
                        <h3 class="fw-bold text-purple mb-0">{{ $budget->formatted_budget }}</h3>
                    </div>

                    <hr>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Item:</span>
                                <strong>{{ $budget->total_items_count }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-success">Selesai:</span>
                                <strong class="text-success">
                                    {{ $budget->completed_items_count }}
                                    ({{ number_format($budget->completed_amount, 0, ',', '.') }})
                                </strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-warning">Belum Selesai:</span>
                                <strong class="text-warning">
                                    {{ $budget->total_items_count - $budget->completed_items_count }}
                                    ({{ number_format($budget->remaining_amount, 0, ',', '.') }})
                                </strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <small class="text-muted d-block mb-2">Progress Keseluruhan</small>
                        <div class="position-relative d-inline-block">
                            <svg width="120" height="120">
                                <circle cx="60" cy="60" r="50" fill="none" stroke="#E5E7EB" stroke-width="10" />
                                <circle cx="60" cy="60" r="50" fill="none"
                                    stroke="{{ $budget->status == 'completed' ? '#10B981' : ($budget->status == 'progress' ? '#F59E0B' : '#6B7280') }}"
                                    stroke-width="10" stroke-dasharray="{{ 2 * 3.14159 * 50 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 50 * (1 - $budget->progress_percentage / 100) }}" transform="rotate(-90 60 60)"
                                    stroke-linecap="round" />
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <h4 class="fw-bold mb-0 text-{{ $budget->status_color }}">{{ $budget->progress_percentage }}%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-lightning text-purple"></i>
                        </div>
                        Aksi Cepat
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Budget
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash me-2"></i>Hapus Budget
                        </button>
                    </div>
                </div>
            </div>

            <!-- Import/Export Excel -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-file-earmark-excel text-purple"></i>
                        </div>
                        Import / Export Excel
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">
                        Export data budget ke Excel untuk diedit secara massal, lalu import kembali untuk update.
                    </p>

                    <!-- Export Button -->
                    <div class="d-grid gap-2 mb-3">
                        <a href="{{ route('budgets.export', $budget) }}" class="btn btn-success">
                            <i class="bi bi-download me-2"></i>Export ke Excel
                        </a>
                    </div>

                    <hr>

                    <!-- Import Form -->
                    <form action="{{ route('budgets.import', $budget) }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label fw-semibold">Upload File Excel</label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file"
                                accept=".xlsx,.xls" required>
                            <small class="text-muted">Format: .xlsx atau .xls (Max 5MB)</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary" id="importBtn">
                                <i class="bi bi-upload me-2"></i>Import dari Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info -->
            <div class="card luxury-card border-0">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <small class="text-muted d-block">Periode</small>
                            <strong>{{ $budget->period }}</strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-{{ $budget->status_color }}">{{ $budget->status_text }}</span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Dibuat</small>
                            <strong>{{ $budget->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Terakhir Update</small>
                            <strong>{{ $budget->updated_at->format('d M Y H:i') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" action="{{ route('budgets.destroy', $budget) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">
                        <i class="bi bi-pencil me-2"></i>Edit Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editItemId">
                    <div class="mb-3">
                        <label for="editItemCategory" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="editItemCategory" list="allCategoryList"
                            placeholder="Contoh: Kartu Kredit CIMB">
                        <datalist id="allCategoryList">
                            @foreach ($allCategories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                        <small class="text-muted">Kosongkan jika tidak ingin dikelompokkan</small>
                    </div>
                    <div class="mb-3">
                        <label for="editItemName" class="form-label">Nama Item <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editItemName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editItemAmount" class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="editItemAmount" min="0" step="1000" required>
                    </div>
                    <div class="mb-3">
                        <label for="editItemNotes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="editItemNotes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveItemBtn">
                        <i class="bi bi-check-circle me-1"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Yakin menghapus budget?',
                text: "Semua item budget akan ikut terhapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // ==================== BULK SELECT ====================
            const selectAllCheckbox = document.getElementById('selectAll');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const selectedCountEl = document.getElementById('selectedCount');
            const bulkActionsEl = document.getElementById('bulkActions');
            const bulkCompleteBtn = document.getElementById('bulkComplete');
            const bulkUncompleteBtn = document.getElementById('bulkUncomplete');

            function updateBulkUI() {
                const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
                const count = checkedBoxes.length;

                selectedCountEl.textContent = `(${count} dipilih)`;

                if (count > 0) {
                    bulkActionsEl.style.display = 'flex';
                    bulkCompleteBtn.disabled = false;
                    bulkUncompleteBtn.disabled = false;
                } else {
                    bulkActionsEl.style.display = 'none';
                    bulkCompleteBtn.disabled = true;
                    bulkUncompleteBtn.disabled = true;
                }

                // Update select all checkbox state
                if (count === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (count === itemCheckboxes.length) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    itemCheckboxes.forEach(cb => cb.checked = this.checked);
                    updateBulkUI();
                });
            }

            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkUI);
            });

            function getSelectedItemIds() {
                return Array.from(document.querySelectorAll('.item-checkbox:checked'))
                    .map(cb => cb.dataset.itemId);
            }

            function bulkToggle(markComplete) {
                const itemIds = getSelectedItemIds();
                if (itemIds.length === 0) return;

                const btn = markComplete ? bulkCompleteBtn : bulkUncompleteBtn;
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Loading...';

                fetch('/budget-items/bulk-toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_ids: itemIds,
                        mark_complete: markComplete
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan',
                        confirmButtonColor: '#8B5CF6'
                    });
                    btn.disabled = false;
                    btn.innerHTML = markComplete
                        ? '<i class="bi bi-check-circle me-1"></i>Tandai Selesai'
                        : '<i class="bi bi-arrow-counterclockwise me-1"></i>Batalkan';
                });
            }

            if (bulkCompleteBtn) {
                bulkCompleteBtn.addEventListener('click', () => bulkToggle(true));
            }
            if (bulkUncompleteBtn) {
                bulkUncompleteBtn.addEventListener('click', () => bulkToggle(false));
            }

            // ==================== SINGLE TOGGLE COMPLETE ====================
            document.querySelectorAll('.toggle-complete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const itemElement = document.querySelector(`.budget-item[data-item-id="${itemId}"]`);
                    const btn = this;

                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Loading...';

                    fetch(`/budget-items/${itemId}/toggle-complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengubah status',
                            confirmButtonColor: '#8B5CF6'
                        });
                        btn.disabled = false;
                        const isCompleted = itemElement.classList.contains('completed');
                        btn.innerHTML = isCompleted
                            ? '<i class="bi bi-arrow-counterclockwise me-1"></i>Batalkan'
                            : '<i class="bi bi-check-circle me-1"></i>Tandai Selesai';
                    });
                });
            });

            // ==================== CATEGORY TOGGLE ====================
            const budgetId = {{ $budget->id }};

            document.querySelectorAll('.category-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const category = this.dataset.category;
                    const markComplete = this.checked;
                    const checkbox = this;

                    fetch('/budget-category/toggle', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            budget_id: budgetId,
                            category: category,
                            mark_complete: markComplete
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        checkbox.checked = !markComplete; // Revert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan',
                            confirmButtonColor: '#8B5CF6'
                        });
                    });
                });
            });

            // ==================== EDIT ITEM ====================
            const editModal = new bootstrap.Modal(document.getElementById('editItemModal'));
            const editItemId = document.getElementById('editItemId');
            const editItemCategory = document.getElementById('editItemCategory');
            const editItemName = document.getElementById('editItemName');
            const editItemAmount = document.getElementById('editItemAmount');
            const editItemNotes = document.getElementById('editItemNotes');
            const saveItemBtn = document.getElementById('saveItemBtn');

            document.querySelectorAll('.edit-item-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    editItemId.value = this.dataset.itemId;
                    editItemCategory.value = this.dataset.itemCategory || '';
                    editItemName.value = this.dataset.itemName;
                    editItemAmount.value = this.dataset.itemAmount;
                    editItemNotes.value = this.dataset.itemNotes || '';
                    editModal.show();
                });
            });

            saveItemBtn.addEventListener('click', function() {
                const itemId = editItemId.value;
                const itemName = editItemName.value.trim();
                const itemAmount = editItemAmount.value;

                if (!itemName || !itemAmount) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Nama item dan jumlah harus diisi!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return;
                }

                saveItemBtn.disabled = true;
                saveItemBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

                fetch(`/budget-items/${itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_name: itemName,
                        estimated_amount: parseFloat(itemAmount),
                        notes: editItemNotes.value.trim() || null,
                        category: editItemCategory.value.trim() || null
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        editModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan',
                        confirmButtonColor: '#8B5CF6'
                    });
                })
                .finally(() => {
                    saveItemBtn.disabled = false;
                    saveItemBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Simpan';
                });
            });

            // ==================== TOGGLE ALL CATEGORIES ====================
            const toggleAllBtn = document.getElementById('toggleAllCategories');
            let allExpanded = true;

            if (toggleAllBtn) {
                toggleAllBtn.addEventListener('click', function() {
                    const collapseElements = document.querySelectorAll('#categoryAccordion .collapse');

                    if (allExpanded) {
                        // Collapse all
                        collapseElements.forEach(el => {
                            const bsCollapse = bootstrap.Collapse.getOrCreateInstance(el);
                            bsCollapse.hide();
                        });
                        this.innerHTML = '<i class="bi bi-eye me-1"></i><span>Tampilkan Semua</span>';
                        allExpanded = false;
                    } else {
                        // Expand all
                        collapseElements.forEach(el => {
                            const bsCollapse = bootstrap.Collapse.getOrCreateInstance(el);
                            bsCollapse.show();
                        });
                        this.innerHTML = '<i class="bi bi-eye-slash me-1"></i><span>Sembunyikan Semua</span>';
                        allExpanded = true;
                    }
                });
            }

            // ==================== IMPORT EXCEL ====================
            const importForm = document.getElementById('importForm');
            const importBtn = document.getElementById('importBtn');

            if (importForm) {
                importForm.addEventListener('submit', function(e) {
                    const fileInput = document.getElementById('excel_file');
                    if (!fileInput.files.length) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: 'Pilih file Excel terlebih dahulu!',
                            confirmButtonColor: '#8B5CF6'
                        });
                        return;
                    }

                    // Check file size (max 5MB)
                    const maxSize = 5 * 1024 * 1024;
                    if (fileInput.files[0].size > maxSize) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'File Terlalu Besar',
                            text: 'Ukuran file maksimal 5MB',
                            confirmButtonColor: '#8B5CF6'
                        });
                        return;
                    }

                    // Show loading
                    importBtn.disabled = true;
                    importBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengimport...';
                });
            }

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#8B5CF6'
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
        });
    </script>

    <style>
        .budget-item {
            background: white;
            border-color: rgba(139, 92, 246, 0.1) !important;
        }

        .budget-item.completed {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.2) !important;
        }

        /* Tombol toggle lebih besar dan mudah diklik */
        .toggle-complete-btn {
            min-width: 140px;
            min-height: 44px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        /* Responsive adjustment */
        @media (max-width: 767.98px) {
            .toggle-complete-btn {
                width: 100%;
            }
        }

        /* Checkbox styling */
        .item-checkbox {
            cursor: pointer;
            border: 2px solid #8B5CF6;
        }

        .item-checkbox:checked {
            background-color: #8B5CF6;
            border-color: #8B5CF6;
        }

        /* Edit button styling */
        .edit-item-btn {
            min-width: 44px;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .luxury-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(139, 92, 246, 0.08);
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
            border-radius: 16px;
        }

        .luxury-icon {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.15));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .progress {
            border-radius: 10px;
            background-color: rgba(139, 92, 246, 0.1);
        }

        .progress-bar {
            border-radius: 10px;
        }

        /* Category styling */
        .category-group {
            overflow: hidden;
        }

        .category-header {
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .category-header .collapse-icon {
            font-size: 1.2rem;
            color: #6c757d;
        }

        .category-header[aria-expanded="false"] .collapse-icon {
            transform: rotate(-90deg);
        }

        .category-checkbox {
            border: 2px solid #8B5CF6;
        }

        .category-checkbox:checked {
            background-color: #10B981;
            border-color: #10B981;
        }

        .category-items {
            background: #fafafa;
        }

        .category-items .budget-item {
            background: white;
        }
    </style>
@endpush
