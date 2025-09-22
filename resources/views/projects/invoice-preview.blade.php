@extends('layouts.app')
@section('title', 'Preview Invoice - ' . $project->title)

@section('content')
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 fw-bold text-purple mb-1">
                        <i class="bi bi-printer me-2"></i>Preview Invoice
                    </h1>
                    <p class="text-muted mb-0">Edit informasi client sebelum mencetak invoice untuk {{ $project->title }}</p>
                </div>
                <div>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Form Edit Client Info -->
        <div class="col-lg-6">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-person-lines-fill text-purple"></i>
                        </div>
                        Edit Informasi Client
                    </h5>
                    <p class="text-muted mb-0 mt-2 small">Informasi ini hanya untuk kebutuhan cetak, tidak akan mengubah data di database</p>
                </div>
                <div class="card-body p-4">
                    <form id="invoiceForm" method="GET" action="{{ route('projects.print-invoice', $project) }}" target="_blank">
                        <div class="mb-3">
                            <label for="client_name" class="form-label fw-semibold">Nama Client</label>
                            <input type="text" class="form-control" id="client_name" name="client_name" value="{{ $project->client->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="client_address" class="form-label fw-semibold">Alamat Client</label>
                            <textarea class="form-control" id="client_address" name="client_address" rows="3">{{ $project->client->address }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="client_phone" class="form-label fw-semibold">No. Telepon</label>
                            <input type="text" class="form-control" id="client_phone" name="client_phone" value="{{ $project->client->phone }}"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="client_email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="client_email" name="client_email" value="{{ $project->client->email }}">
                        </div>

                        <div class="border-top pt-3 mb-3">
                            <h6 class="fw-bold text-dark mb-3">Detail Penagihan</h6>

                            <div class="mb-3">
                                <label for="qty" class="form-label fw-semibold">Quantity</label>
                                <input type="number" class="form-control" id="qty" name="qty" value="1" min="1" step="1"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="unit_price" class="form-label fw-semibold">Unit Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="unit_price" name="unit_price"
                                        value="{{ number_format($project->total_value, 0, ',', '.') }}" readonly>
                                </div>
                                <small class="text-muted">Unit price akan otomatis dihitung berdasarkan quantity</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Total</label>
                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                    <i class="bi bi-calculator text-success me-2"></i>
                                    <strong class="text-success" id="total_display">{{ $project->formatted_total_value }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-printer me-2"></i>Print Invoice
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset ke Data Asli
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Project Summary -->
        <div class="col-lg-6">
            <div class="card luxury-card border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <div class="luxury-icon me-3">
                            <i class="bi bi-folder2-open text-purple"></i>
                        </div>
                        Ringkasan Proyek
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold">Nomor Invoice</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="bi bi-hash text-purple me-2"></i>
                                <strong>{{ $invoiceNumber }}</strong>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold">Judul Proyek</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="bi bi-folder text-purple me-2"></i>
                                <strong>{{ $project->title }}</strong>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold">Tipe Proyek</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="bi bi-tag text-secondary me-2"></i>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2">
                                    {{ $project->type }}
                                </span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold">Nilai Proyek</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="bi bi-currency-dollar text-success me-2"></i>
                                <h6 class="mb-0 fw-bold text-success">{{ $project->formatted_total_value }}</h6>
                            </div>
                        </div>

                        @if ($project->description)
                            <div class="col-12">
                                <label class="form-label text-muted fw-semibold">Deskripsi</label>
                                <div class="p-3 bg-light rounded-3">
                                    <p class="mb-0">{{ $project->description }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold">Tanggal Invoice</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="bi bi-calendar3 text-info me-2"></i>
                                <strong>{{ $project->deadline->format('d M Y') }}</strong>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold">Due Date</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="bi bi-calendar-x text-warning me-2"></i>
                                <strong>{{ $project->deadline->format('d M Y') }}</strong>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold">Status Pembayaran</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <i class="bi bi-credit-card text-info me-2"></i>
                                <div>
                                    <strong>{{ $project->formatted_paid_amount }}</strong> dari {{ $project->formatted_total_value }}
                                    <br>
                                    <small class="text-muted">Sisa: {{ $project->formatted_remaining_amount }}</small>
                                </div>
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
        const totalValue = {{ $project->total_value }};

        function resetForm() {
            // Reset ke data asli dari database
            document.getElementById('client_name').value = '{{ $project->client->name }}';
            document.getElementById('client_address').value = '{{ $project->client->address }}';
            document.getElementById('client_phone').value = '{{ $project->client->phone }}';
            document.getElementById('client_email').value = '{{ $project->client->email }}';
            document.getElementById('qty').value = '1';
            calculateUnitPrice();
        }

        function calculateUnitPrice() {
            const qty = parseInt(document.getElementById('qty').value) || 1;
            const unitPrice = totalValue / qty;

            // Format unit price dengan pemisah ribuan
            document.getElementById('unit_price').value = new Intl.NumberFormat('id-ID').format(Math.round(unitPrice));
        }

        // Event listener untuk qty change
        document.getElementById('qty').addEventListener('input', calculateUnitPrice);

        // Validasi form sebelum submit
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            const name = document.getElementById('client_name').value.trim();
            const phone = document.getElementById('client_phone').value.trim();
            const qty = parseInt(document.getElementById('qty').value);

            if (!name) {
                e.preventDefault();
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Nama client harus diisi',
                    icon: 'warning',
                    confirmButtonColor: '#8B5CF6'
                });
                return;
            }

            if (!phone) {
                e.preventDefault();
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'No. telepon client harus diisi',
                    icon: 'warning',
                    confirmButtonColor: '#8B5CF6'
                });
                return;
            }

            if (!qty || qty < 1) {
                e.preventDefault();
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Quantity harus diisi minimal 1',
                    icon: 'warning',
                    confirmButtonColor: '#8B5CF6'
                });
                return;
            }
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

        .luxury-card:hover {
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
            transform: translateY(-2px);
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

        .form-control:focus {
            border-color: rgba(139, 92, 246, 0.5);
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }
    </style>
@endpush
