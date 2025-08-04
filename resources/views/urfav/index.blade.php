@extends('layouts.app')
@section('title', 'Urfav - Update Shopee')

@section('content')
    <!-- Modern Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-shop me-2"></i>Urfav - Update Shopee
                    </h1>
                    <p class="text-muted mb-0">Import dan sinkronisasi data Jakmall ke Shopee</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('urfav.export-all') }}" class="btn btn-success">
                        <i class="bi bi-file-excel me-2"></i>Export All
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Input Manual Produk Baru -->
    <div class="card luxury-card border-0 mb-4">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-bold mb-0 d-flex align-items-center">
                <div class="luxury-icon me-3">
                    <i class="bi bi-plus-circle text-purple"></i>
                </div>
                Input Manual Produk Baru
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('urfav.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-2 col-6">
                    <label class="form-label fw-semibold">Jakmall SKU</label>
                    <input type="text" name="jakmall_sku" class="form-control" placeholder="SKU" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label fw-semibold">Harga</label>
                    <input type="number" name="jakmall_harga" class="form-control" placeholder="0" step="0.01" required>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label fw-semibold">Stock</label>
                    <select name="jakmall_stock" class="form-select" required>
                        <option value="">Pilih</option>
                        <option value="tersedia">Tersedia</option>
                        <option value="tidak">Tidak</option>
                    </select>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label fw-semibold">Shopee ID</label>
                    <input type="text" name="shopee_id" class="form-control" placeholder="ID">
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label fw-semibold">Margin %</label>
                    <input type="number" name="shopee_margin" class="form-control" placeholder="0" step="0.01" min="0" max="100">
                </div>
                <div class="col-md-2 col-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus me-1"></i>Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="text-uppercase text-muted fw-bold mb-3 fs-6">
                <i class="bi bi-gear me-2 text-purple"></i>Workflow Actions
            </h5>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card luxury-card border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="luxury-icon mx-auto mb-3">
                        <i class="bi bi-cloud-upload text-primary fs-4"></i>
                    </div>
                    <h6 class="fw-bold mb-3">1. Import Jakmall</h6>
                    <form action="{{ route('urfav.import-jakmall') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="jakmall_file" class="form-control mb-3" accept=".xlsx,.xls,.csv" required>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-upload me-1"></i>Import
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card luxury-card border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="luxury-icon mx-auto mb-3">
                        <i class="bi bi-arrow-repeat text-success fs-4"></i>
                    </div>
                    <h6 class="fw-bold mb-3">2. Sync ke Shopee</h6>
                    <form action="{{ route('urfav.sync-shopee') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm w-100 mb-2">
                            <i class="bi bi-cloud-check me-1"></i>Sync All
                        </button>
                    </form>
                    <small class="text-muted">Sync produk yang sudah ada margin</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card luxury-card border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="luxury-icon mx-auto mb-3">
                        <i class="bi bi-sort-numeric-down text-warning fs-4"></i>
                    </div>
                    <h6 class="fw-bold mb-3">3. Update Urutan</h6>
                    <form action="{{ route('urfav.update-urutan-file') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="urutan_file" class="form-control mb-3" accept=".xlsx,.xls" required>
                        <button type="submit" class="btn btn-warning btn-sm w-100">
                            <i class="bi bi-arrow-up me-1"></i>Update
                        </button>
                    </form>
                    <small class="text-muted">Excel: Shopee_ID | Shopee_SKU</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card luxury-card border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="luxury-icon mx-auto mb-3">
                        <i class="bi bi-download text-info fs-4"></i>
                    </div>
                    <h6 class="fw-bold mb-3">4. Export Shopee</h6>
                    <a href="{{ route('urfav.export-shopee') }}" class="btn btn-info btn-sm w-100">
                        <i class="bi bi-file-excel me-1"></i>Download Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card luxury-card border-0">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1 d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-table text-purple"></i>
                        </div>
                        Data Produk
                    </h5>
                    <p class="text-muted mb-0">Total: {{ $products->total() }} item</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control" placeholder="Cari SKU, Shopee ID..."
                            value="{{ request('search') }}" style="min-width: 200px;">
                        <button type="submit" class="btn btn-outline-primary ms-2">
                            <i class="bi bi-search"></i>
                        </button>
                        @if (request('search'))
                            <a href="{{ route('urfav.index') }}" class="btn btn-outline-secondary ms-1">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($products->count() > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark">Jakmall SKU</span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark">Jakmall Harga</span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark">Jakmall Stock</span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark">Shopee Urut</span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark">Shopee ID</span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark">Shopee SKU</span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark">Shopee Harga</span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark">Margin %</span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark">Shopee Stock</span>
                                    </th>
                                    <th class="px-4 py-3 border-0">
                                        <span class="fw-semibold text-dark">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr class="align-middle">
                                        <td class="px-4 py-3">
                                            <span class="fw-medium">{{ $product->jakmall_sku }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-success fw-medium">Rp {{ number_format($product->jakmall_harga, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($product->jakmall_stock === 'tersedia')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Tersedia
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                    <i class="bi bi-x-circle-fill me-1"></i>Tidak
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-muted">{{ $product->shopee_urut ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" class="form-control form-control-sm border-purple" value="{{ $product->shopee_id }}"
                                                onchange="updateProduct({{ $product->id }}, 'shopee_id', this.value)" placeholder="Input Shopee ID">
                                        </td>
                                        <td class="px-0 py-3">
                                            <span class="text-muted">{{ $product->shopee_sku ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-success fw-medium">Rp {{ number_format($product->shopee_harga, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" class="form-control form-control-sm border-purple"
                                                value="{{ $product->shopee_margin }}"
                                                onchange="updateProduct({{ $product->id }}, 'shopee_margin', this.value)" placeholder="%"
                                                step="0.01" min="0" max="100">
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-muted">{{ $product->shopee_stock }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <form method="POST" action="{{ route('urfav.destroy', $product) }}"
                                                onsubmit="return confirm('Yakin hapus produk ini?')" style="display: inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-lg-none">
                    <div class="row g-3 p-3">
                        @foreach ($products as $product)
                            <div class="col-12">
                                <div class="card luxury-card border-0">
                                    <div class="card-body p-3">
                                        <!-- Header -->
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="fw-bold text-purple mb-1">{{ $product->jakmall_sku }}</h6>
                                                <small class="text-muted">SKU: {{ $product->shopee_sku ?? 'Belum ada' }}</small>
                                            </div>
                                            <form method="POST" action="{{ route('urfav.destroy', $product) }}"
                                                onsubmit="return confirm('Yakin hapus produk ini?')" style="display: inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Price Info -->
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Harga Jakmall</small>
                                                <span class="fw-medium text-success">Rp {{ number_format($product->jakmall_harga, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Harga Shopee</small>
                                                <span class="fw-medium text-success">Rp {{ number_format($product->shopee_harga, 0, ',', '.') }}</span>
                                            </div>
                                        </div>

                                        <!-- Stock Status -->
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Stock Jakmall</small>
                                                @if ($product->jakmall_stock === 'tersedia')
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success fs-7">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Tersedia
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger fs-7">
                                                        <i class="bi bi-x-circle-fill me-1"></i>Tidak
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Stock Shopee</small>
                                                <span class="text-muted">{{ $product->shopee_stock }}</span>
                                            </div>
                                        </div>

                                        <!-- Input Fields -->
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold fs-7">Shopee ID</label>
                                                <input type="text" class="form-control form-control-sm border-purple"
                                                    value="{{ $product->shopee_id }}"
                                                    onchange="updateProduct({{ $product->id }}, 'shopee_id', this.value)"
                                                    placeholder="Input Shopee ID">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold fs-7">Margin %</label>
                                                <input type="number" class="form-control form-control-sm border-purple"
                                                    value="{{ $product->shopee_margin }}"
                                                    onchange="updateProduct({{ $product->id }}, 'shopee_margin', this.value)" placeholder="%"
                                                    step="0.01" min="0" max="100">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold fs-7">Urutan</label>
                                                <input type="text" class="form-control form-control-sm" value="{{ $product->shopee_urut ?? '-' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Enhanced Pagination -->
                @if (method_exists($products, 'links'))
                    <div class="card-footer bg-light border-0 p-4">
                        <div style="display: none;">
                            {{ $products->links() }}
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-muted mb-0">
                                    Menampilkan <strong>{{ $products->firstItem() }}-{{ $products->lastItem() }}</strong>
                                    dari <strong>{{ $products->total() }}</strong> produk
                                </p>
                            </div>
                            <div class="col-md-6">
                                <nav class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                                    <ul class="pagination mb-0">
                                        @if ($products->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $products->previousPageUrl() }}">
                                                    <i class="bi bi-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        <!-- Show current page info -->
                                        <li class="page-item active">
                                            <span class="page-link">{{ $products->currentPage() }} / {{ $products->lastPage() }}</span>
                                        </li>

                                        @if ($products->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $products->nextPageUrl() }}">
                                                    <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="luxury-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-inbox text-muted" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Belum ada data produk</h5>
                    <p class="text-muted mb-4">Mulai dengan import data Jakmall atau tambah produk manual</p>
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                        <button class="btn btn-primary" onclick="document.querySelector('input[name=jakmall_file]').click()">
                            <i class="bi bi-upload me-2"></i>Import Jakmall
                        </button>
                        <button class="btn btn-outline-primary" onclick="document.querySelector('input[name=jakmall_sku]').focus()">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Manual
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Touch feedback for mobile cards
            const cards = document.querySelectorAll('.luxury-card');
            cards.forEach(card => {
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
            });

            // Auto-resize textareas and inputs on mobile
            const inputs = document.querySelectorAll('input[type="text"], input[type="number"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                });
            });
        });

        function updateProduct(productId, field, value) {
            const data = {};
            data[field] = value;

            // Show loading state
            const loadingToast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            loadingToast.fire({
                title: 'Mengupdate...'
            });

            fetch(`/urfav/products/${productId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success notification
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                        Toast.fire({
                            icon: 'success',
                            title: 'Berhasil diupdate!'
                        });

                        // Update harga display if margin was changed
                        if (field === 'shopee_margin' && data.data.shopee_harga) {
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message || 'Terjadi kesalahan',
                            confirmButtonColor: '#8B5CF6'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan koneksi',
                        confirmButtonColor: '#8B5CF6'
                    });
                });
        }

        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const fileInputs = this.querySelectorAll('input[type="file"]');
                fileInputs.forEach(input => {
                    if (input.hasAttribute('required') && !input.files.length) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'File diperlukan!',
                            text: 'Silakan pilih file terlebih dahulu',
                            confirmButtonColor: '#8B5CF6'
                        });
                    }
                });
            });
        });
    </script>
@endpush
