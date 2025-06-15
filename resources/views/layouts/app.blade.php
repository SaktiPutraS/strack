<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - strack.my.id</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }

        .card-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .sidebar-active {
            transform: translateX(0);
        }

        .sidebar-inactive {
            transform: translateX(-100%);
        }

        /* Progress bars */
        .progress-bar {
            background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
            transition: width 0.3s ease;
        }

        /* Animations */
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Status colors */
        .status-waiting { @apply bg-yellow-100 text-yellow-800; }
        .status-progress { @apply bg-blue-100 text-blue-800; }
        .status-finished { @apply bg-green-100 text-green-800; }
        .status-cancelled { @apply bg-red-100 text-red-800; }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .mobile-padding { padding: 1rem; }
            .mobile-text { font-size: 0.875rem; }
            .mobile-card { margin-bottom: 1rem; }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Mobile Menu Button -->
    <div class="md:hidden fixed top-4 left-4 z-50">
        <button id="mobile-menu-btn" class="bg-white p-2 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
            <i class="fas fa-bars w-6 h-6 text-gray-600"></i>
        </button>
    </div>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed left-0 top-0 h-full w-64 gradient-bg text-white z-40 transition-transform duration-300 sidebar-inactive md:sidebar-active custom-scrollbar overflow-y-auto">
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center space-x-3 mb-8">
                <div class="bg-white/20 p-2 rounded-lg">
                    <i class="fas fa-briefcase text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">strack.my.id</h1>
                    <p class="text-white/70 text-sm">Freelance Tracker</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 {{ request()->routeIs('dashboard') ? 'bg-white/20' : 'hover:bg-white/10' }} px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('projects.create') }}" class="flex items-center space-x-3 {{ request()->routeIs('projects.create') ? 'bg-white/20' : 'hover:bg-white/10' }} px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-plus w-5"></i>
                    <span>Proyek Baru</span>
                </a>

                <a href="{{ route('projects.index') }}" class="flex items-center space-x-3 {{ request()->routeIs('projects.*') && !request()->routeIs('projects.create') ? 'bg-white/20' : 'hover:bg-white/10' }} px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-list w-5"></i>
                    <span>Daftar Proyek</span>
                </a>

                <a href="{{ route('clients.index') }}" class="flex items-center space-x-3 {{ request()->routeIs('clients.*') ? 'bg-white/20' : 'hover:bg-white/10' }} px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-users w-5"></i>
                    <span>Klien</span>
                </a>

                <a href="{{ route('payments.index') }}" class="flex items-center space-x-3 {{ request()->routeIs('payments.*') ? 'bg-white/20' : 'hover:bg-white/10' }} px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-wallet w-5"></i>
                    <span>Keuangan</span>
                </a>

                <a href="{{ route('savings.index') }}" class="flex items-center space-x-3 {{ request()->routeIs('savings.*') ? 'bg-white/20' : 'hover:bg-white/10' }} px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-piggy-bank w-5"></i>
                    <span>Tabungan 10%</span>
                </a>

                <a href="{{ route('testimonials.index') }}" class="flex items-center space-x-3 {{ request()->routeIs('testimonials.*') ? 'bg-white/20' : 'hover:bg-white/10' }} px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-star w-5"></i>
                    <span>Testimoni</span>
                </a>
            </nav>

            <!-- Stats Summary -->
            <div class="mt-8 p-4 bg-white/10 rounded-lg">
                <h3 class="text-sm font-medium text-white/70 mb-2">Quick Stats</h3>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-white/60">Proyek Aktif</span>
                        <span class="font-medium" id="sidebar-active-projects">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/60">Total Piutang</span>
                        <span class="font-medium" id="sidebar-total-remaining">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-4 sticky top-0 z-30">
            <div class="flex items-center justify-between">
                <div class="md:ml-0 ml-12">
                    <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-gray-600 text-sm">@yield('page-description', 'Kelola proyek freelance Anda')</p>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Current Date -->
                    <div class="hidden md:flex items-center bg-gray-100 rounded-lg px-3 py-2">
                        <i class="fas fa-calendar text-gray-500 mr-2"></i>
                        <span class="text-sm text-gray-600" id="current-date"></span>
                    </div>

                    <!-- Online Status -->
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                        Online
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-4 md:p-6">
            @yield('content')
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-75 z-50 hidden">
        <div class="flex items-center justify-center h-full">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600"></div>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="success-toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="success-message">Berhasil!</span>
        </div>
    </div>

    <!-- Error Toast -->
    <div id="error-toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span id="error-message">Terjadi kesalahan!</span>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.0/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <!-- Base JavaScript -->
    <script>
        // CSRF Token setup for AJAX
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        // Set CSRF token for all AJAX requests
        if (window.fetch) {
            const originalFetch = window.fetch;
            window.fetch = function(url, options = {}) {
                options.headers = options.headers || {};
                options.headers['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
                options.headers['X-Requested-With'] = 'XMLHttpRequest';
                return originalFetch(url, options);
            };
        }

        // Currency formatter
        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        // Date formatter
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // Show loading
        function showLoading() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        }

        // Hide loading
        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('hidden');
        }

        // Show success toast
        function showSuccess(message) {
            const toast = document.getElementById('success-toast');
            const messageEl = document.getElementById('success-message');
            messageEl.textContent = message;
            toast.classList.remove('hidden');

            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Show error toast
        function showError(message) {
            const toast = document.getElementById('error-toast');
            const messageEl = document.getElementById('error-message');
            messageEl.textContent = message;
            toast.classList.remove('hidden');

            setTimeout(() => {
                toast.classList.add('hidden');
            }, 5000);
        }

        // API helper function
        async function apiRequest(url, options = {}) {
            showLoading();
            try {
                const response = await fetch(url, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    ...options
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }

                return data;
            } catch (error) {
                showError(error.message);
                throw error;
            } finally {
                hideLoading();
            }
        }

        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobile-overlay');

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('sidebar-active');
                    sidebar.classList.toggle('sidebar-inactive');
                    mobileOverlay.classList.toggle('hidden');
                });
            }

            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.add('sidebar-inactive');
                    sidebar.classList.remove('sidebar-active');
                    mobileOverlay.classList.add('hidden');
                });
            }

            // Set current date
            const currentDateEl = document.getElementById('current-date');
            if (currentDateEl) {
                currentDateEl.textContent = new Date().toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            // Load sidebar stats
            loadSidebarStats();
        });

        // Load sidebar quick stats
        async function loadSidebarStats() {
            try {
                const stats = await apiRequest('/api/stats');

                const activeProjectsEl = document.getElementById('sidebar-active-projects');
                const totalRemainingEl = document.getElementById('sidebar-total-remaining');

                if (activeProjectsEl) {
                    activeProjectsEl.textContent = stats.projects.active;
                }

                if (totalRemainingEl) {
                    totalRemainingEl.textContent = formatCurrency(stats.financial.total_remaining);
                }
            } catch (error) {
                console.error('Error loading sidebar stats:', error);
            }
        }

        // Auto refresh sidebar stats every 5 minutes
        setInterval(loadSidebarStats, 300000);

        // Form validation helper
        function validateForm(formId) {
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    isValid = false;
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            return isValid;
        }

        // Confirm delete helper
        function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
            return confirm(message);
        }

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-auto-hide');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
