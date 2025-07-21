<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'STRACK')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        :root {
            --lilac-primary: #8B5CF6;
            --lilac-secondary: #A78BFA;
            --lilac-light: #DDD6FE;
            --lilac-soft: #F3F0FF;
        }

        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .navbar {
            background-color: var(--lilac-primary);
            border-bottom: 1px solid var(--lilac-secondary);
        }

        .navbar-brand {
            font-weight: 700;
            color: white !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            border-radius: 8px;
            margin: 0 0.25rem;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.3);
            color: white !important;
            font-weight: 600;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--lilac-primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-icon {
            font-size: 2rem;
            color: var(--lilac-primary);
            margin-bottom: 1rem;
        }

        .btn-primary {
            background-color: var(--lilac-primary);
            border-color: var(--lilac-primary);
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: var(--lilac-secondary);
            border-color: var(--lilac-secondary);
        }

        .list-group-item {
            border: none;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            background-color: var(--lilac-soft);
            padding: 1rem;
        }

        .badge {
            border-radius: 20px;
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }

        .badge-lilac {
            background-color: var(--lilac-primary);
            color: white;
        }

        .badge-success {
            background-color: #10b981;
        }

        .badge-warning {
            background-color: #f59e0b;
        }

        .badge-danger {
            background-color: #ef4444;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--lilac-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .text-lilac {
            color: var(--lilac-primary);
        }

        .bg-lilac {
            background-color: var(--lilac-primary);
        }

        .bg-lilac-soft {
            background-color: var(--lilac-soft);
        }

        @media (max-width: 768px) {
            .stat-value {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 1rem;
            }
        }

        /* Override pagination Tailwind dengan Bootstrap */
        nav[role="navigation"] {
            display: none !important;
        }

        .bootstrap-pagination {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }

        .pagination-info-alt {
            text-align: center;
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-kanban me-2"></i>STRACK
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-house me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                            <i class="bi bi-list-task me-1"></i>Proyek
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                            <i class="bi bi-people me-1"></i>Klien
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">
                            <i class="bi bi-credit-card me-1"></i>Pembayaran
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('financial/*') ? 'active' : '' }}" href="#" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-calculator me-1"></i>Keuangan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('expenses.index') }}">
                                    <i class="bi bi-credit-card me-2"></i>Pengeluaran
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('bank-transfers.index') }}">
                                    <i class="bi bi-bank me-2"></i>Transfer Bank
                                </a></li>
                            <li><a class="dropdown-item" href="{{ route('gold.index') }}">
                                    <i class="bi bi-coin me-2"></i>Emas
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('financial-reports.index') }}">
                                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Keuangan
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form id="logout-form" action="{{ url('/logout') }}" method="GET" class="d-none">
                                    @csrf
                                </form>
                                <a class="dropdown-item" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Simple utility functions
        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        // CSRF Token for AJAX
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
    </script>

    @stack('scripts')
</body>

</html>
