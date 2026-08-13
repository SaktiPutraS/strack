@extends('layouts.app')
@section('title', $formTitle)

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h2 fw-bold text-purple mb-0">
                <i class="bi bi-globe2 me-2"></i>{{ $formTitle }}
            </h1>
            <a href="{{ route('domains.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card luxury-card border-0">
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @if ($method === 'PUT')
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Domain <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                value="{{ old('name', $domain->name) }}" placeholder="contoh.com">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Provider / Registrar</label>
                                <input type="text" name="provider" class="form-control"
                                    value="{{ old('provider', $domain->provider) }}" placeholder="mis. Hostinger, Niagahoster">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Biaya Perpanjang (Rp)</label>
                                <input type="number" name="renewal_cost" class="form-control" min="0" step="1000"
                                    value="{{ old('renewal_cost', $domain->renewal_cost ? (int) $domain->renewal_cost : '') }}">
                            </div>
                        </div>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-semibold">Klien (opsional)</label>
                                <select name="client_id" id="client_id" class="form-select" style="width:100%;">
                                    <option value="">- Tidak ditautkan -</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ (string) old('client_id', $domain->client_id) === (string) $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}{{ $client->phone ? ' - ' . $client->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3 d-grid">
                                <a id="showProjectBtn" href="#" target="_blank" class="btn btn-outline-secondary disabled">
                                    <i class="bi bi-folder2-open me-2"></i>Show Project
                                </a>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tanggal Daftar</label>
                                <input type="date" name="registered_at" class="form-control"
                                    value="{{ old('registered_at', optional($domain->registered_at)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tanggal Kedaluwarsa</label>
                                <input type="date" name="expires_at" class="form-control"
                                    value="{{ old('expires_at', optional($domain->expires_at)->format('Y-m-d')) }}">
                                <small class="text-muted">Dipakai untuk pengingat perpanjangan.</small>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_hosted" name="is_hosted"
                                value="1" {{ old('is_hosted', $domain->is_hosted) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_hosted">Ada di hosting saya</label>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $domain->notes) }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Simpan
                            </button>
                            <a href="{{ route('domains.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        /* Samakan tinggi Select2 dengan form-control Bootstrap 5 */
        .select2-container .select2-selection--single {
            height: calc(2.5rem + 2px);
            display: flex;
            align-items: center;
            border-color: #dee2e6;
            border-radius: .5rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: rgba(139, 92, 246, .5);
            box-shadow: 0 0 0 .2rem rgba(139, 92, 246, .25);
        }
    </style>
    <script>
        $(function() {
            $('#client_id').select2({
                placeholder: '- Tidak ditautkan -',
                allowClear: true,
                width: '100%'
            });

            const clientBase = "{{ url('clients') }}/";
            const $btn = $('#showProjectBtn');

            function updateShowProject() {
                const id = $('#client_id').val();
                if (id) {
                    $btn.attr('href', clientBase + id).removeClass('disabled');
                } else {
                    $btn.attr('href', '#').addClass('disabled');
                }
            }

            updateShowProject();
            $('#client_id').on('change', updateShowProject);
        });
    </script>
@endpush
