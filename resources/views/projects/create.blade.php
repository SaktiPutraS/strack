@extends('layouts.app')

@section('title', 'Tambah Proyek Baru')
@section('page-title', 'Tambah Proyek Baru')
@section('page-description', 'Buat proyek freelance baru')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('projects.store') }}" method="POST" id="project-form" class="space-y-6">
            @csrf

            <!-- Main Project Information -->
            <div class="bg-white rounded-xl p-6 card-shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Informasi Proyek
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Client Selection -->
                    <div class="md:col-span-2">
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Klien <span class="text-red-500">*</span>
                        </label>
                        <div class="flex space-x-2">
                            <select name="client_id" id="client_id" required
                                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('client_id') border-red-500 @enderror">
                                <option value="">Pilih Klien</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} - {{ $client->phone }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openNewClientModal()"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Proyek <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            placeholder="e.g. Website E-Commerce Toko Online"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Proyek <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                            <option value="">Pilih Tipe</option>
                            @foreach ($projectTypes as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">
                            Deadline <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" required
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('deadline') border-red-500 @enderror">
                        @error('deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Proyek <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="4" required
                            placeholder="Deskripsikan detail proyek, fitur yang akan dibuat, teknologi yang digunakan, dll."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="bg-white rounded-xl p-6 card-shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">
                    <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                    Informasi Keuangan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Total Value -->
                    <div>
                        <label for="total_value" class="block text-sm font-medium text-gray-700 mb-2">
                            Nilai Total Proyek <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="total_value" id="total_value" value="{{ old('total_value') }}" required min="0"
                                step="1000" placeholder="0"
                                class="w-full pl-8 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('total_value') border-red-500 @enderror">
                        </div>
                        @error('total_value')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Masukkan nilai tanpa titik atau koma</p>
                    </div>

                    <!-- DP Amount -->
                    <div>
                        <label for="dp_amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Down Payment (DP)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="dp_amount" id="dp_amount" value="{{ old('dp_amount', 0) }}" min="0" step="1000"
                                placeholder="0"
                                class="w-full pl-8 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('dp_amount') border-red-500 @enderror">
                        </div>
                        @error('dp_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-2">
                            <div class="flex space-x-2">
                                <button type="button" onclick="setDPPercentage(10)"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded">10%</button>
                                <button type="button" onclick="setDPPercentage(25)"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded">25%</button>
                                <button type="button" onclick="setDPPercentage(50)"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded">50%</button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Persentase DP: <span id="dp-percentage">0%</span>
                            </p>
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-800 mb-3">Ringkasan Keuangan</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Nilai Total:</span>
                                <p class="font-semibold text-gray-800" id="summary-total">Rp 0</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Down Payment:</span>
                                <p class="font-semibold text-green-600" id="summary-dp">Rp 0</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Sisa Pembayaran:</span>
                                <p class="font-semibold text-orange-600" id="summary-remaining">Rp 0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="bg-white rounded-xl p-6 card-shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">
                    <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                    Catatan Tambahan
                </h3>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Catatan khusus tentang proyek ini (opsional)"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('projects.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>

                <div class="space-x-3">
                    <button type="button" onclick="resetForm()"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Proyek
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- New Client Modal -->
    <div id="newClientModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center p-4 min-h-screen">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Tambah Klien Baru</h3>
                    <button onclick="closeNewClientModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="newClientForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Klien *</label>
                        <input type="text" id="newClientName" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon *</label>
                        <input type="tel" id="newClientPhone" required placeholder="08123456789"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="newClientEmail"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeNewClientModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Simpan Klien
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Financial calculations
        function updateFinancialSummary() {
            const totalValue = parseFloat(document.getElementById('total_value').value) || 0;
            const dpAmount = parseFloat(document.getElementById('dp_amount').value) || 0;
            const remaining = totalValue - dpAmount;

            document.getElementById('summary-total').textContent = formatCurrency(totalValue);
            document.getElementById('summary-dp').textContent = formatCurrency(dpAmount);
            document.getElementById('summary-remaining').textContent = formatCurrency(remaining);

            // Update DP percentage
            const dpPercentage = totalValue > 0 ? ((dpAmount / totalValue) * 100).toFixed(1) : 0;
            document.getElementById('dp-percentage').textContent = dpPercentage + '%';
        }

        // Set DP percentage
        function setDPPercentage(percentage) {
            const totalValue = parseFloat(document.getElementById('total_value').value) || 0;
            const dpAmount = (totalValue * percentage) / 100;
            document.getElementById('dp_amount').value = Math.round(dpAmount);
            updateFinancialSummary();
        }

        // Reset form
        function resetForm() {
            if (confirm('Apakah Anda yakin ingin mereset form?')) {
                document.getElementById('project-form').reset();
                updateFinancialSummary();
            }
        }

        // New client modal functions
        function openNewClientModal() {
            document.getElementById('newClientModal').classList.remove('hidden');
        }

        function closeNewClientModal() {
            document.getElementById('newClientModal').classList.add('hidden');
            document.getElementById('newClientForm').reset();
        }

        // Handle new client form submission
        document.getElementById('newClientForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = {
                name: document.getElementById('newClientName').value,
                phone: document.getElementById('newClientPhone').value,
                email: document.getElementById('newClientEmail').value,
            };

            try {
                showLoading();
                const response = await fetch('{{ route('clients.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menyimpan klien');
                }

                // Add new client to select option
                const clientSelect = document.getElementById('client_id');
                const newOption = new Option(
                    `${data.client.name} - ${data.client.phone}`,
                    data.client.id,
                    true,
                    true
                );
                clientSelect.add(newOption);

                closeNewClientModal();
                showSuccess('Klien baru berhasil ditambahkan!');

            } catch (error) {
                showError(error.message);
            } finally {
                hideLoading();
            }
        });

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Update financial summary on input change
            document.getElementById('total_value').addEventListener('input', updateFinancialSummary);
            document.getElementById('dp_amount').addEventListener('input', updateFinancialSummary);

            // Initial calculation
            updateFinancialSummary();

            // Close modal on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeNewClientModal();
                }
            });

            // Set minimum deadline to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('deadline').min = tomorrow.toISOString().split('T')[0];

            // Form validation
            document.getElementById('project-form').addEventListener('submit', function(e) {
                const totalValue = parseFloat(document.getElementById('total_value').value) || 0;
                const dpAmount = parseFloat(document.getElementById('dp_amount').value) || 0;

                if (dpAmount > totalValue) {
                    e.preventDefault();
                    showError('Jumlah DP tidak boleh melebihi nilai total proyek!');
                    return false;
                }

                if (totalValue <script 100000) {
                    e.preventDefault();
                    showError('Nilai proyek minimal Rp 100.000');
                    return false;
                }

                // Show loading while submitting
                showLoading();
            });

            // Phone number formatting
            document.getElementById('newClientPhone').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.startsWith('0')) {
                    // Keep Indonesian format
                } else if (value.startsWith('62')) {
                    // Convert to Indonesian format
                    value = '0' + value.substring(2);
                }
                e.target.value = value;
            });

            // Auto-save to localStorage on form change (optional)
            const formInputs = document.querySelectorAll('#project-form input, #project-form select, #project-form textarea');
            formInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Could implement auto-save to localStorage here
                    // localStorage.setItem('project_form_' + input.name, input.value);
                });
            });
        });

        // Format currency input display (optional enhancement)
        function formatInputCurrency(inputId) {
            const input = document.getElementById(inputId);
            input.addEventListener('blur', function() {
                const value = parseFloat(this.value) || 0;
                // Could add thousand separators here for display
            });
        }

        // Auto-calculate common project values
        const commonProjectValues = {
            'HTML/PHP': [500000, 1000000, 1500000, 2000000],
            'LARAVEL': [1500000, 2500000, 3500000, 5000000],
            'WORDPRESS': [800000, 1200000, 2000000, 3000000],
            'REACT': [2000000, 3000000, 4500000, 6000000],
            'VUE': [1800000, 2800000, 4000000, 5500000],
            'FLUTTER': [3000000, 5000000, 7500000, 10000000],
            'MOBILE': [2500000, 4000000, 6000000, 8000000],
            'OTHER': [500000, 1000000, 2000000, 3000000]
        };

        // Show suggested prices based on project type
        document.getElementById('type').addEventListener('change', function() {
            const selectedType = this.value;
            if (selectedType && commonProjectValues[selectedType]) {
                const suggestions = commonProjectValues[selectedType];
                console.log(`Suggested prices for ${selectedType}:`, suggestions);
                // Could display these as quick buttons
            }
        });
    </script>
@endpush
