@extends('layouts.app')
@section('title', 'Detail Perlengkapan')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-box-seam me-2"></i>Detail Perlengkapan
                    </h1>
                    <p class="text-muted mb-0">{{ $supply->name }}</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a href="{{ route('supplies.edit', $supply) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('supplies.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Supply Details -->
        <div class="col-md-8">
            <!-- Main Information -->
            <div class="card luxury-card border-0 mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-info-circle text-purple"></i>
                        </div>
                        Informasi Perlengkapan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Nama Barang</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-box-seam text-purple me-2 fs-4"></i>
                                <strong class="fs-5">{{ $supply->name }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Status Stok</label>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $supply->stock_status_color }} fs-5">
                                    <i class="bi bi-circle-fill me-1"></i>
                                    {{ $supply->stock_status_text }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Stok Saat Ini</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-boxes text-purple me-2 fs-4"></i>
                                <strong class="fs-3 text-{{ $supply->stock_status_color }}">{{ $supply->qty }}</strong>
                                <span class="text-muted ms-2">unit</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Minimum Stok</label>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle text-warning me-2 fs-4"></i>
                                <strong class="fs-4">{{ $supply->minimum_stock }}</strong>
                                <span class="text-muted ms-2">unit</span>
                            </div>
                        </div>
                        @if ($supply->order_link)
                            <div class="col-12">
                                <label class="form-label text-muted fw-semibold">Link Order/Pembelian</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-link-45deg text-primary me-2 fs-4"></i>
                                    <a href="{{ $supply->order_link }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Buka Link Order
                                    </a>
                                </div>
                            </div>
                        @endif
                        @if ($supply->notes)
                            <div class="col-12">
                                <label class="form-label text-muted fw-semibold">Keterangan</label>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-journal-text text-purple me-2 mt-1"></i>
                                    <p class="mb-0 text-dark">{{ $supply->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Usage History -->
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <div class="luxury-icon me-3">
                                <i class="bi bi-clock-history text-purple"></i>
                            </div>
                            Riwayat Penggunaan
                        </h5>
                        <span class="badge bg-purple">{{ $supply->usages->count() }} riwayat</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($supply->usages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 border-0 fw-semibold">Tanggal</th>
                                        <th class="px-4 py-3 border-0 fw-semibold text-center">Jumlah</th>
                                        <th class="px-4 py-3 border-0 fw-semibold">Keterangan</th>
                                        <th class="px-4 py-3 border-0 fw-semibold text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supply->usages as $usage)
                                        <tr class="border-bottom">
                                            <td class="px-4 py-3">
                                                <strong>{{ $usage->usage_date->format('d M Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $usage->usage_date->diffForHumans() }}</small>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger fs-6">
                                                    -{{ $usage->qty_used }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $usage->notes ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDeleteUsage({{ $usage->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <form id="delete-usage-{{ $usage->id }}" action="{{ route('supply-usages.destroy', $usage) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="luxury-icon mx-auto mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-clock-history text-muted fs-3"></i>
                            </div>
                            <p class="text-muted mb-0">Belum ada riwayat penggunaan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
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
                        <a href="{{ route('supplies.use-form', $supply) }}" class="btn btn-success">
                            <i class="bi bi-arrow-down-circle me-2"></i>Catat Penggunaan
                        </a>
                        <a href="{{ route('supplies.add-stock-form', $supply) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Stok
                        </a>
                        <a href="{{ route('supplies.edit', $supply) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Perlengkapan
                        </a>
                        @if ($supply->order_link)
                            <a href="{{ $supply->order_link }}" target="_blank" class="btn btn-outline-info">
                                <i class="bi bi-cart me-2"></i>Order Barang
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stock Alert -->
            @if ($supply->is_low_stock)
                <div class="card luxury-card border-0 mb-4 border-{{ $supply->stock_status_color }}" style="border-width: 2px !important;">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-exclamation-triangle-fill text-{{ $supply->stock_status_color }} fs-1 mb-3"></i>
                        <h5 class="fw-bold text-{{ $supply->stock_status_color }}">
                            @if ($supply->qty == 0)
                                Stok Habis!
                            @else
                                Stok Rendah!
                            @endif
                        </h5>
                        <p class="text-muted mb-3">
                            @if ($supply->qty == 0)
                                Barang ini sudah habis dan perlu segera dipesan.
                            @else
                                Stok di bawah minimum ({{ $supply->minimum_stock }} unit). Segera order untuk menghindari kehabisan.
                            @endif
                        </p>
                        @if ($supply->order_link)
                            <a href="{{ $supply->order_link }}" target="_blank" class="btn btn-{{ $supply->stock_status_color }}">
                                <i class="bi bi-cart me-2"></i>Order Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Supply Summary -->
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3" style="width: 36px; height: 36px;">
                            <i class="bi bi-calculator text-purple"></i>
                        </div>
                        Ringkasan
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">ID:</span>
                                <strong>#{{ str_pad($supply->id, 4, '0', STR_PAD_LEFT) }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Penggunaan:</span>
                                <strong>{{ $supply->usages->sum('qty_used') }} unit</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Ditambahkan:</span>
                                <strong>{{ $supply->created_at->format('d M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Terakhir Update:</span>
                                <strong>{{ $supply->updated_at->format('d M Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDeleteUsage(usageId) {
            Swal.fire({
                title: 'Yakin menghapus riwayat?',
                text: "Stok akan dikembalikan sesuai jumlah penggunaan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-usage-' + usageId).submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#8B5CF6'
                });
            @endif
        });
    </script>

    <style>
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
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
        }

        .text-purple {
            color: #8B5CF6 !important;
        }

        .bg-purple {
            background-color: #8B5CF6 !important;
        }
    </style>
@endpush
