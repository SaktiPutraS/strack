@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Ringkasan aktivitas freelance Anda')

@section('content')
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <!-- Total Nilai Proyek -->
            <div class="bg-white rounded-xl p-6 card-shadow animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Nilai Proyek</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">@currency($stats['financial']['total_value'])</p>
                        <p class="text-green-600 text-sm mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            {{ $stats['projects']['total'] }} proyek
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-trending-up text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Pendapatan -->
            <div class="bg-white rounded-xl p-6 card-shadow animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">@currency($stats['financial']['total_paid'])</p>
                        <p class="text-green-600 text-sm mt-1">
                            @percentage($stats['financial']['completion_percentage']) terealisasi
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-wallet text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Piutang -->
            <div class="bg-white rounded-xl p-6 card-shadow animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Piutang</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">@currency($stats['financial']['total_remaining'])</p>
                        <p class="text-orange-600 text-sm mt-1">
                            <i class="fas fa-clock mr-1"></i>
                            Perlu ditagih
                        </p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Tabungan 10% -->
            <div class="bg-white rounded-xl p-6 card-shadow animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Tabungan 10%</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">@currency($stats['savings']['total'])</p>
                        <p class="text-purple-600 text-sm mt-1 flex items-center">
                            <i class="fas fa-piggy-bank mr-1"></i>
                            Bank Octo
                            @if ($stats['savings']['is_balanced'])
                                <i class="fas fa-check-circle text-green-500 ml-1" title="Saldo sesuai"></i>
                            @else
                                <i class="fas fa-exclamation-triangle text-yellow-500 ml-1" title="Perlu verifikasi"></i>
                            @endif
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-piggy-bank text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rest of the dashboard content -->
        <!-- Proyek Aktif section dengan formatted attributes -->
        @if ($activeProjects->count() > 0)
            @foreach ($activeProjects as $project)
                <!-- Gunakan $project->formatted_total_value, $project->formatted_remaining_amount, dll -->
                <p>{{ $project->client->name }} • {{ $project->type }} • {{ $project->formatted_total_value }}</p>
                <span>Piutang: {{ $project->formatted_remaining_amount }}</span>
            @endforeach
        @endif

        <!-- Pembayaran Terbaru dengan formatted attributes -->
        @if ($recentPayments->count() > 0)
            @foreach ($recentPayments as $payment)
                <p class="font-semibold text-gray-800">{{ $payment->formatted_amount }}</p>
                <p class="text-xs text-purple-600">+{{ $payment->formatted_saving_amount }} (10%)</p>
            @endforeach
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // JavaScript functions untuk chart
        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        }
        // Income Chart
        let incomeChart;

        document.addEventListener('DOMContentLoaded', function() {
            initIncomeChart();
        });

        function initIncomeChart() {
            const ctx = document.getElementById('incomeChart').getContext('2d');
            const monthlyIncome = @json($stats['monthly_income']);

            incomeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlyIncome.map(item => item.month),
                    datasets: [{
                        label: 'Pendapatan',
                        data: monthlyIncome.map(item => item.amount),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(context) {
                                return 'Pendapatan: ' + formatCurrency(context.parsed.y);
                            }
                        }
                    }
                }
            });
        }

        async function refreshChart() {
            try {
                const response = await apiRequest('/api/financial-chart');
                const data = response.monthly_income;

                incomeChart.data.labels = data.map(item => item.month);
                incomeChart.data.datasets[0].data = data.map(item => item.income);
                incomeChart.update();

                showSuccess('Grafik berhasil diperbarui');
            } catch (error) {
                showError('Gagal memperbarui grafik');
            }
        }
    </script>
@endpush
