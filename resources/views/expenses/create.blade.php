{{-- resources/views/expenses/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Pengeluaran')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="section-title">
                    <i class="bi bi-plus-circle"></i>Tambah Pengeluaran Baru
                </h1>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('expenses.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <!-- Expense Date -->
                            <div class="col-md-6">
                                <label for="expense_date" class="form-label">
                                    <i class="bi bi-calendar3 text-lilac me-2"></i>
                                    Tanggal Pengeluaran <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date"
                                    name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
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
                                        value="{{ old('amount') }}" min="1000" step="1000" placeholder="50000" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimal Rp 1.000, kelipatan Rp 1.000</div>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <label for="category" class="form-label">
                                    <i class="bi bi-tag text-lilac me-2"></i>
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach (\App\Models\Expense::CATEGORIES as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
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
                                    placeholder="Contoh: Hosting bulanan untuk website client, Kopi meeting dengan klien, dll" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Simpan Pengeluaran
                                </button>
                            </div>
                        </div>
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

            // Load subcategories when category changes
            categorySelect.addEventListener('change', function() {
                const category = this.value;
                subcategorySelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';

                if (category) {
                    // Perbaiki URL untuk route yang benar
                    const url = `/financial/expenses/subcategories/${category}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            Object.entries(data).forEach(([key, label]) => {
                                const option = new Option(label, key);
                                subcategorySelect.add(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error loading subcategories:', error);
                            // Fallback: Add manual options
                            addFallbackSubcategories(category);
                        });
                }
            });

            // Fallback function untuk subcategories
            function addFallbackSubcategories(category) {
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
                        'kopi_makanan': 'Kopi & Makanan',
                        'makan_kerja': 'Makan Kerja',
                        'snack_minuman': 'Snack & Minuman',
                        'entertainment_pribadi': 'Entertainment Pribadi'
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
                    subcategorySelect.add(option);
                });
            }

            // Format amount display
            document.getElementById('amount').addEventListener('input', function() {
                const value = this.value;
                if (value) {
                    this.title = 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                }
            });
        });
    </script>
@endpush
