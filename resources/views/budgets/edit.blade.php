@extends('layouts.app')
@section('title', 'Edit Budget')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-pencil-square me-2"></i>Edit Budget
                    </h1>
                    <p class="text-muted mb-0">{{ $budget->period }}</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('budgets.show', $budget) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Lihat Detail
                    </a>
                    <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <form action="{{ route('budgets.update', $budget) }}" method="POST" id="budget-form">
                @csrf
                @method('PUT')

                <!-- Period Selection -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-calendar3 text-purple"></i>
                            </div>
                            Periode Budget
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="month" class="form-label fw-semibold">
                                    Bulan <span class="text-danger">*</span>
                                </label>
                                <select name="month" id="month" class="form-select form-select-lg @error('month') is-invalid @enderror" required>
                                    <option value="">Pilih Bulan</option>
                                    @php
                                        $months = [
                                            1 => 'Januari',
                                            2 => 'Februari',
                                            3 => 'Maret',
                                            4 => 'April',
                                            5 => 'Mei',
                                            6 => 'Juni',
                                            7 => 'Juli',
                                            8 => 'Agustus',
                                            9 => 'September',
                                            10 => 'Oktober',
                                            11 => 'November',
                                            12 => 'Desember',
                                        ];
                                    @endphp
                                    @foreach ($months as $num => $name)
                                        <option value="{{ $num }}" {{ old('month', $budget->month) == $num ? 'selected' : '' }}
                                            {{ in_array($num, $usedMonths) ? 'disabled' : '' }}>
                                            {{ $name }}
                                            {{ in_array($num, $usedMonths) ? '(Sudah ada budget)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="year" class="form-label fw-semibold">
                                    Tahun <span class="text-danger">*</span>
                                </label>
                                <select name="year" id="year" class="form-select form-select-lg @error('year') is-invalid @enderror" required>
                                    @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                        <option value="{{ $y }}" {{ old('year', $budget->year) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    Catatan Budget
                                </label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                    placeholder="Catatan umum untuk budget ini (opsional)">{{ old('notes', $budget->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget Items -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <div class="luxury-icon me-3">
                                    <i class="bi bi-list-check text-purple"></i>
                                </div>
                                Item Pengeluaran
                            </h5>
                            <button type="button" class="btn btn-primary btn-sm" id="add-item-btn">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="items-container">
                            @foreach ($budget->items as $index => $item)
                                <div class="item-row mb-3 p-3 border rounded {{ $item->is_completed ? 'item-completed' : '' }}"
                                    data-index="{{ $index }}">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    <div class="row g-3">
                                        <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <h6 class="mb-0 fw-bold text-purple me-3">Item {{ $index + 1 }}</h6>
                                                @if ($item->is_completed)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Selesai
                                                    </span>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Kategori</label>
                                            <input type="text" name="items[{{ $index }}][category]" class="form-control item-category"
                                                value="{{ old("items.$index.category", $item->category) }}"
                                                placeholder="Contoh: Kartu Kredit CIMB" list="categoryList">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Nama Item <span class="text-danger">*</span></label>
                                            <input type="text" name="items[{{ $index }}][item_name]" class="form-control"
                                                value="{{ old("items.$index.item_name", $item->item_name) }}"
                                                placeholder="Contoh: Gaji, Bensin, Internet" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Estimasi Nominal <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" name="items[{{ $index }}][estimated_amount]"
                                                    class="form-control item-amount"
                                                    value="{{ old("items.$index.estimated_amount", $item->estimated_amount) }}" min="0"
                                                    placeholder="0" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Keterangan</label>
                                            <input type="text" name="items[{{ $index }}][notes]" class="form-control"
                                                value="{{ old("items.$index.notes", $item->notes) }}" placeholder="Catatan untuk item ini (opsional)">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <datalist id="categoryList">
                            @foreach ($budget->categories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <!-- Total Budget -->
                <div class="card luxury-card border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark">Total Estimasi Budget</h5>
                            <h3 class="mb-0 fw-bold text-purple" id="total-budget">Rp 0</h3>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card luxury-card border-0">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
                            <button type="button" class="btn btn-outline-danger btn-lg" onclick="confirmDelete()">
                                <i class="bi bi-trash me-2"></i>Hapus Budget
                            </button>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('budgets.show', $budget) }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                    <i class="bi bi-check-circle me-2"></i>Update Budget
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Delete Form -->
            <form id="delete-form" action="{{ route('budgets.destroy', $budget) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
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

        let itemIndex = {{ $budget->items->count() }};

        document.addEventListener('DOMContentLoaded', function() {
            const addItemBtn = document.getElementById('add-item-btn');
            const itemsContainer = document.getElementById('items-container');
            const totalBudgetEl = document.getElementById('total-budget');
            const submitBtn = document.getElementById('submit-btn');

            // Add item
            addItemBtn.addEventListener('click', function() {
                const itemHtml = `
                    <div class="item-row mb-3 p-3 border rounded animate-item" data-index="${itemIndex}">
                        <div class="row g-3">
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-bold text-purple">Item ${itemIndex + 1}</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kategori</label>
                                <input type="text" name="items[${itemIndex}][category]"
                                    class="form-control item-category" placeholder="Contoh: Kartu Kredit CIMB" list="categoryList">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nama Item <span class="text-danger">*</span></label>
                                <input type="text" name="items[${itemIndex}][item_name]"
                                    class="form-control" placeholder="Contoh: Gaji, Bensin, Internet" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Estimasi Nominal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="items[${itemIndex}][estimated_amount]"
                                        class="form-control item-amount" min="0" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <input type="text" name="items[${itemIndex}][notes]"
                                    class="form-control" placeholder="Catatan untuk item ini (opsional)">
                            </div>
                        </div>
                    </div>
                `;
                itemsContainer.insertAdjacentHTML('beforeend', itemHtml);
                itemIndex++;
                updateItemNumbers();
                calculateTotal();
                updateCategoryList();
            });

            // Update category datalist
            function updateCategoryList() {
                const categories = new Set();
                document.querySelectorAll('.item-category').forEach(input => {
                    if (input.value.trim()) {
                        categories.add(input.value.trim());
                    }
                });
                const datalist = document.getElementById('categoryList');
                if (datalist) {
                    datalist.innerHTML = Array.from(categories).map(cat => `<option value="${cat}">`).join('');
                }
            }

            // Update category list on input
            itemsContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('item-category')) {
                    updateCategoryList();
                }
            });

            // Initialize category list
            updateCategoryList();

            // Remove item
            itemsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    const itemRow = e.target.closest('.item-row');

                    Swal.fire({
                        title: 'Hapus item ini?',
                        text: "Item akan dihapus dari budget",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            itemRow.remove();
                            updateItemNumbers();
                            calculateTotal();
                        }
                    });
                }
            });

            // Calculate total on amount change
            itemsContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('item-amount')) {
                    calculateTotal();
                }
            });

            // Update item numbers
            function updateItemNumbers() {
                const items = itemsContainer.querySelectorAll('.item-row');
                items.forEach((item, index) => {
                    const header = item.querySelector('h6');
                    if (header) {
                        const badge = header.nextElementSibling;
                        header.textContent = `Item ${index + 1}`;
                        if (badge && badge.classList.contains('badge')) {
                            header.appendChild(document.createTextNode(' '));
                            header.appendChild(badge);
                        }
                    }
                });
            }

            // Calculate total budget
            function calculateTotal() {
                let total = 0;
                const amounts = itemsContainer.querySelectorAll('.item-amount');
                amounts.forEach(input => {
                    const value = parseFloat(input.value) || 0;
                    total += value;
                });
                totalBudgetEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            // Form validation
            document.getElementById('budget-form').addEventListener('submit', function(e) {
                const itemCount = itemsContainer.querySelectorAll('.item-row').length;

                if (itemCount === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Item Belum Ada',
                        text: 'Minimal harus ada 1 item pengeluaran!',
                        confirmButtonColor: '#8B5CF6'
                    });
                    return;
                }

                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                submitBtn.disabled = true;
            });

            // Initial calculation
            calculateTotal();
        });
    </script>

    <style>
        .form-control:focus,
        .form-select:focus {
            border-color: #8B5CF6;
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        .luxury-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(139, 92, 246, 0.08);
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
            border-radius: 16px;
            transition: all 0.3s ease;
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

        .item-row {
            background: rgba(139, 92, 246, 0.02);
            border-color: rgba(139, 92, 246, 0.1) !important;
            transition: all 0.3s ease;
        }

        .item-row:hover {
            background: rgba(139, 92, 246, 0.05);
            border-color: rgba(139, 92, 246, 0.2) !important;
        }

        .item-row.item-completed {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.2) !important;
        }

        .animate-item {
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush
