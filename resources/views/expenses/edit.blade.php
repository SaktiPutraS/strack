@extends('layouts.app')
@section('title', 'Edit Pengeluaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-pencil-square"></i>Edit Pengeluaran
                </h1>
                <div class="btn-group">
                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Lihat
                    </a>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('expenses.update', $expense) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Expense Date -->
                            <div class="col-md-6">
                                <label for="expense_date" class="form-label">
                                    <i class="bi bi-calendar3 text-lilac me-2"></i>
                                    Tanggal Pengeluaran <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date"
                                    name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                                    max="{{ date('Y-m-d') }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <label for="amount" class="form-label">
                                    <i class="bi bi-cash text-lilac me-2"></i>
                                    Jumlah Pengeluaran <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount"
                                        value="{{ old('amount', $expense->amount) }}" min="1000" placeholder="50000" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimal Rp 1.000</div>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <label for="category" class="form-label">
                                    <i class="bi bi-tag text-lilac me-2"></i>
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="OPERASIONAL" {{ old('category', $expense->category) == 'OPERASIONAL' ? 'selected' : '' }}>Operasional
                                    </option>
                                    <option value="MARKETING" {{ old('category', $expense->category) == 'MARKETING' ? 'selected' : '' }}>Marketing
                                    </option>
                                    <option value="PENGEMBANGAN" {{ old('category', $expense->category) == 'PENGEMBANGAN' ? 'selected' : '' }}>
                                        Pengembangan</option>
                                    <option value="GAJI_FREELANCE" {{ old('category', $expense->category) == 'GAJI_FREELANCE' ? 'selected' : '' }}>Gaji &
                                        Freelance</option>
                                    <option value="ENTERTAINMENT" {{ old('category', $expense->category) == 'ENTERTAINMENT' ? 'selected' : '' }}>
                                        Entertainment</option>
                                    <option value="LAIN_LAIN" {{ old('category', $expense->category) == 'LAIN_LAIN' ? 'selected' : '' }}>Lain-lain
                                    </option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Subcategory -->
                            <div class="col-md-6">
                                <label for="subcategory" class="form-label">
                                    <i class="bi bi-tags text-lilac me-2"></i>
                                    Sub Kategori
                                </label>
                                <select name="subcategory" id="subcategory" class="form-select @error('subcategory') is-invalid @enderror">
                                    <option value="">Pilih Sub Kategori</option>
                                </select>
                                @error('subcategory')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opsional - untuk klasifikasi lebih detail</div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="bi bi-journal-text text-lilac me-2"></i>
                                    Deskripsi <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3"
                                    placeholder="Contoh: Hosting bulanan untuk website client, Kopi meeting dengan klien, dll" required>{{ old('description', $expense->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Data Info -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-info-circle text-info me-2"></i>
                                Data Saat Ini
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <small class="text-muted">Tanggal Awal:</small>
                                    <div class="fw-bold">{{ $expense->expense_date->format('d M Y') }}</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Jumlah Awal:</small>
                                    <div class="fw-bold text-danger">{{ $expense->formatted_amount }}</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Kategori Awal:</small>
                                    <div class="fw-bold">{{ $expense->category_label }}</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Dibuat:</small>
                                    <div class="fw-bold">{{ $expense->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <div>
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-info me-2">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash me-2"></i>Hapus
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('expenses.index') }}" class="btn btn-secondary me-2">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Pengeluaran
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form (Hidden) -->
                    <form id="delete-form" action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');
            const currentSubcategory = '{{ old('subcategory', $expense->subcategory) }}';

            // Load subcategories when category changes
            categorySelect.addEventListener('change', function() {
                loadSubcategories(this.value);
            });

            // Load initial subcategories for current category
            if (categorySelect.value) {
                loadSubcategories(categorySelect.value, currentSubcategory);
            }

            function loadSubcategories(category, selectedValue = '') {
                subcategorySelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';

                if (category) {
                    // Fallback subcategories if route doesn't work
                    const fallbackSubcategories = {
                        'OPERASIONAL': {
                            'hosting_domain': 'Hosting & Domain',
                            'software_tools': 'Software & Tools',
                            'internet_komunikasi': 'Internet & Komunikasi',
                            'listrik_utilitas': 'Listrik & Utilitas'
                        },
                        'MARKETING': {
                            'iklan_online': 'Iklan Online',
                            'promosi_campaign': 'Promosi & Campaign',
                            'content_tools': 'Content Creation Tools'
                        },
                        'PENGEMBANGAN': {
                            'training_course': 'Training & Course',
                            'hardware_equipment': 'Hardware & Equipment',
                            'third_party_services': 'Third-party Services'
                        },
                        'GAJI_FREELANCE': {
                            'gaji_freelancer': 'Gaji Freelancer',
                            'fee_project': 'Fee Project',
                            'bonus_insentif': 'Bonus & Insentif'
                        },
                        'ENTERTAINMENT': {
                            'Alfa_Indomaret': 'Alfa & Indomaret',
                            'Jajan_diluar': 'Jajan diluar',
                            'Grab_Gojek_Shoopefood': 'Grab/Gojek/Shoopefood'
                        },
                        'LAIN_LAIN': {
                            'transportasi': 'Transportasi',
                            'pajak_admin': 'Pajak & Administrasi',
                            'misc': 'Misc Expenses'
                        }
                    };

                    const subcategories = fallbackSubcategories[category] || {};
                    Object.entries(subcategories).forEach(([key, label]) => {
                        const option = new Option(label, key);
                        if (key === selectedValue) {
                            option.selected = true;
                        }
                        subcategorySelect.add(option);
                    });
                }
            }

            // Format amount display
            document.getElementById('amount').addEventListener('input', function() {
                const value = this.value;
                if (value) {
                    this.title = 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                }
            });
        });

        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
@endpush
