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
            --lilac-dark: #7C3AED;
            --lilac-soft: #F3F0FF;
            --lilac-bg: #FAF8FF;
            --white: #FFFFFF;
            --gray-light: #F8FAFC;
            --gray-medium: #64748B;
            --gray-dark: #334155;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
        }

        body {
            background: linear-gradient(135deg, var(--lilac-bg) 0%, var(--lilac-soft) 50%, var(--lilac-light) 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--gray-dark);
            min-height: 100vh;
        }

        .navbar {
            background: rgba(139, 92, 246, 0.95);
            border-bottom: 1px solid var(--lilac-secondary);
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 30px rgba(139, 92, 246, 0.2);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--white) !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            border-radius: 12px;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: var(--white) !important;
            transform: translateY(-1px);
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.15);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(139, 92, 246, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(139, 92, 246, 0.25);
            border-color: var(--lilac-secondary);
        }

        .card-body {
            padding: 2rem;
        }

        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--lilac-primary) 0%, var(--lilac-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray-medium);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-icon {
            font-size: 2rem;
            background: linear-gradient(135deg, var(--lilac-primary) 0%, var(--lilac-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--lilac-primary) 0%, var(--lilac-dark) 100%);
            border: none;
            border-radius: 15px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
            background: linear-gradient(135deg, var(--lilac-dark) 0%, var(--lilac-primary) 100%);
        }

        .list-group-item {
            border: none;
            border-radius: 15px !important;
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, var(--lilac-soft) 0%, rgba(255, 255, 255, 0.9) 100%);
            padding: 1.25rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(139, 92, 246, 0.1);
        }

        .list-group-item:hover {
            background: linear-gradient(135deg, var(--lilac-light) 0%, rgba(255, 255, 255, 0.95) 100%);
            transform: translateX(5px);
            border-color: var(--lilac-secondary);
        }

        .badge {
            border-radius: 25px;
            font-weight: 600;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
        }

        .badge-lilac {
            background: linear-gradient(135deg, var(--lilac-secondary) 0%, var(--lilac-primary) 100%);
            color: var(--white);
        }

        .badge-success {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        }

        .badge-warning {
            background: linear-gradient(135deg, var(--warning) 0%, #D97706 100%);
        }

        .badge-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #DC2626 100%);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--lilac-primary) 0%, var(--lilac-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            font-size: 1.25rem;
            background: linear-gradient(135deg, var(--lilac-primary) 0%, var(--lilac-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-lilac {
            color: var(--lilac-primary);
        }

        .text-lilac-secondary {
            color: var(--lilac-secondary);
        }

        @media (max-width: 768px) {
            .navbar-nav {
                background: rgba(139, 92, 246, 0.95);
                border-radius: 15px;
                margin-top: 1rem;
                padding: 0.5rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .stat-card {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-kanban me-2"></i>STRACK
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-house me-1"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-plus-circle me-1"></i>Proyek Baru</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-list-task me-1"></i>Daftar Proyek</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-people me-1"></i>Klien</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-currency-dollar me-1"></i>Keuangan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-piggy-bank me-1"></i>Tabungan 10%</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-star me-1"></i>Testimoni</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
