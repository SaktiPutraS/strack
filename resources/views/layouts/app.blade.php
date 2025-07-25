<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#8B5CF6">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <title>@yield('title', 'STRACK')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        /* Minimal Custom CSS - Focus on White Luxury Theme */
        :root {
            --bs-primary: #8B5CF6;
            --bs-primary-rgb: 139, 92, 246;
            --bs-secondary: #6B7280;
            --sidebar-width: 280px;
        }

        /* Apple Typography */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #fafbff 100%);
            letter-spacing: -0.003em;
            -webkit-font-smoothing: antialiased;
            transition: all 0.3s ease;
        }

        /* PERBAIKAN UTAMA: Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            max-width: 85vw;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(139, 92, 246, 0.08);
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.12);
            transform: translateX(-100%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1060;
        }

        .sidebar.show {
            transform: translateX(0);
        }

        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
                position: relative;
            }
        }

        /* White Luxury Cards */
        .luxury-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(139, 92, 246, 0.08);
            box-shadow: 0 4px 24px rgba(139, 92, 246, 0.08);
            border-radius: 16px;
        }

        .luxury-card:hover {
            box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
            transform: translateY(-2px);
        }

        /* Purple Accents on White */
        .btn-primary {
            background: linear-gradient(135deg, #8B5CF6, #A855F7);
            border: none;
            box-shadow: 0 4px 16px rgba(139, 92, 246, 0.25);
        }

        .btn-outline-primary {
            border: 1.5px solid #8B5CF6;
            color: #8B5CF6;
            background: rgba(255, 255, 255, 0.9);
        }

        /* Sidebar Navigation */
        .nav-link {
            color: #6B7280;
            border-radius: 12px;
            margin: 0.25rem 0;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(168, 85, 247, 0.1));
            color: #8B5CF6;
        }

        /* PERBAIKAN UTAMA: Toggle Button */
        .sidebar-toggle {
            position: fixed;
            top: 1.5rem;
            left: 1.5rem;
            z-index: 1070;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(139, 92, 246, 0.2);
            color: #8B5CF6;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 16px rgba(139, 92, 246, 0.15);
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .sidebar-toggle i {
            font-size: 1.5rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 0;
            padding-top: 50px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        @media (min-width: 992px) {
            .main-content {
                margin-left: var(--sidebar-width);
                padding-top: 0;
            }

            .sidebar-toggle {
                display: none;
            }
        }

        /* Mobile Font Sizes */
        .fs-7 {
            font-size: 0.8rem;
        }

        .fs-8 {
            font-size: 0.75rem;
        }

        .fs-9 {
            font-size: 0.7rem;
        }

        /* Luxury Purple Icons */
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

        /* PERBAIKAN UTAMA: Overlay */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(4px);
            z-index: 1050;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* Touch Feedback */
        .btn:active,
        .luxury-card:active {
            transform: scale(0.98);
        }

        /* Custom Purple Colors */
        .text-purple {
            color: #8B5CF6;
        }

        .bg-purple-light {
            background-color: rgba(139, 92, 246, 0.05);
        }

        .border-purple {
            border-color: rgba(139, 92, 246, 0.2);
        }

        /* PERBAIKAN: Nonaktifkan scroll saat sidebar terbuka */
        body.sidebar-open {
            overflow: hidden;
            position: relative;
            /* Ubah dari fixed ke relative */
            width: 100%;
        }

        /* PERBAIKAN: Dropdown menu animation */
        .nav-item .collapse {
            transition: height 0.3s ease;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Luxury White Sidebar -->
        <aside class="sidebar position-fixed top-0 start-0 vh-100 overflow-auto" id="sidebar">
            <!-- Header -->
            <div class="p-4 border-bottom border-purple text-center">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <h4 class="fw-bold text-purple mb-0 p-0">
                        <img src="{{ asset('Logo.png') }}" alt="STRACK Logo" class="d-inline-block align-middle me-2" style="width: 200px">
                    </h4>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="p-3">
                <ul class="nav nav-pills flex-column">

                    @if (session('role') === 'admin')
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="bi bi-house-door me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                                <i class="bi bi-list-task me-2"></i>Proyek
                            </a>
                        </li>

                        <!-- Kelompok Keuangan -->
                        <li class="nav-item dropdown">
                            <a href="#"
                                class="nav-link dropdown-toggle {{ request()->routeIs('payments.*') ||
                                request()->routeIs('expenses.*') ||
                                request()->routeIs('bank-transfers.*') ||
                                request()->routeIs('gold.*') ||
                                request()->routeIs('financial-reports.*')
                                    ? 'active'
                                    : '' }}"
                                data-bs-toggle="collapse" data-bs-target="#financialMenu">
                                <i class="bi bi-calculator me-2"></i>Keuangan
                            </a>
                            <div class="collapse ms-3 {{ request()->routeIs('payments.*') ||
                            request()->routeIs('expenses.*') ||
                            request()->routeIs('bank-transfers.*') ||
                            request()->routeIs('gold.*') ||
                            request()->routeIs('financial-reports.*')
                                ? 'show'
                                : '' }}"
                                id="financialMenu">
                                <ul class="nav nav-pills flex-column">
                                    <li>
                                        <a href="{{ route('payments.index') }}"
                                            class="nav-link py-2 fs-7 {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                                            <i class="bi bi-cash-coin me-2"></i>Pemasukan
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('expenses.index') }}"
                                            class="nav-link py-2 fs-7 {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                                            <i class="bi bi-credit-card me-2"></i>Pengeluaran
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('bank-transfers.index') }}"
                                            class="nav-link py-2 fs-7 {{ request()->routeIs('bank-transfers.*') ? 'active' : '' }}">
                                            <i class="bi bi-bank me-2"></i>Transfer
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('gold.index') }}"
                                            class="nav-link py-2 fs-7 {{ request()->routeIs('gold.*') ? 'active' : '' }}">
                                            <i class="bi bi-coin me-2"></i>Emas
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('financial-reports.index') }}"
                                            class="nav-link py-2 fs-7 {{ request()->routeIs('financial-reports.*') ? 'active' : '' }}">
                                            <i class="bi bi-graph-up me-2"></i>Laporan
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Kelompok Master -->
                        <li class="nav-item dropdown">
                            <a href="#"
                                class="nav-link dropdown-toggle {{ request()->routeIs('clients.*') || request()->routeIs('project-types.*') ? 'active' : '' }}"
                                data-bs-toggle="collapse" data-bs-target="#masterMenu">
                                <i class="bi bi-diagram-3 me-2"></i>Master
                            </a>
                            <div class="collapse ms-3 {{ request()->routeIs('clients.*') || request()->routeIs('project-types.*') ? 'show' : '' }}"
                                id="masterMenu">
                                <ul class="nav nav-pills flex-column">
                                    <li>
                                        <a href="{{ route('clients.index') }}"
                                            class="nav-link py-2 fs-7 {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                                            <i class="bi bi-people me-2"></i>Klien
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('project-types.index') }}"
                                            class="nav-link py-2 fs-7 {{ request()->routeIs('project-types.*') ? 'active' : '' }}">
                                            <i class="bi bi-tags me-2"></i>Tipe Proyek
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        {{-- Taks --}}
                        <li class="nav-item">
                            <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                                <i class="bi bi-list-task me-2"></i>Tugas
                            </a>
                        </li>
                    @endif
                    @if (session('role') === 'user')
                        <li class="nav-item">
                            <a href="{{ route('dashboard.user') }}" class="nav-link {{ request()->routeIs('dashboard.user') ? 'active' : '' }}">
                                <i class="bi bi-house-door me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('tasks.user.index') }}" class="nav-link {{ request()->routeIs('tasks.user.*') ? 'active' : '' }}">
                                <i class="bi bi-list-task me-2"></i>Tugas
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
            <!-- Footer -->
            <div class="mt-auto p-3 border-top border-purple">
                <form action="{{ url('/logout') }}" method="GET" class="d-inline w-100">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Toggle Button -->
        <button class="btn btn-light sidebar-toggle rounded-circle p-2 d-lg-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <!-- Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content -->
        <main class="main-content flex-grow-1">
            <div class="container-fluid p-4 p-md-4">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // PERBAIKAN UTAMA: Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.getElementById('sidebarToggle');
            const body = document.body;

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');

                if (sidebar.classList.contains('show')) {
                    body.classList.add('sidebar-open');
                } else {
                    body.classList.remove('sidebar-open');
                }
            }

            // Event listeners
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });

            overlay.addEventListener('click', function() {
                toggleSidebar();
            });

            // Close sidebar when clicking outside
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickInsideToggle = toggleBtn.contains(event.target);

                if (!isClickInsideSidebar && !isClickInsideToggle && sidebar.classList.contains('show')) {
                    toggleSidebar();
                }
            });

            // Auto-close sidebar saat resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    body.classList.remove('sidebar-open');
                }
            });

            // SweetAlert2 Setup
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            function showSuccessToast(message) {
                Toast.fire({
                    icon: 'success',
                    title: message
                });
            }

            function showErrorToast(message) {
                Toast.fire({
                    icon: 'error',
                    title: message
                });
            }

            // Session Messages
            @if (session('success'))
                showSuccessToast('{{ session('success') }}');
            @endif

            @if (session('error'))
                showErrorToast('{{ session('error') }}');
            @endif

            // CSRF Setup
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
