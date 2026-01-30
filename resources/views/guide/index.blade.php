@extends('layouts.app')
@section('title', 'Guide Chat - Client Handling Flowchart')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1e293b;
            --light: #f8fafc;
            --border: #e2e8f0;
            --text: #334155;
            --text-light: #64748b;
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            color: var(--text);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 2rem 1rem;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 0.95rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        @media (min-width: 768px) {
            .header {
                padding: 3rem 2rem;
            }

            .header h1 {
                font-size: 2.5rem;
            }

            .header p {
                font-size: 1.1rem;
            }
        }

        /* Mobile Select Navigation */
        .mobile-nav-select {
            display: block;
            width: 100%;
            padding: 1rem;
            background: white;
            border: 2px solid var(--primary);
            border-radius: 12px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 20px;
            padding-right: 3rem;
        }

        .mobile-nav-select:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Desktop Tab Navigation */
        .nav-tabs {
            display: none;
            background: var(--light);
            border-bottom: 2px solid var(--border);
            overflow-x: auto;
            padding: 0 1rem;
        }

        @media (min-width: 768px) {
            .mobile-nav-select {
                display: none;
            }

            .nav-tabs {
                display: flex;
            }
        }

        .nav-tab {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-light);
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
            text-decoration: none;
            display: inline-block;
        }

        .nav-tab:hover {
            color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }

        .nav-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .content {
            padding: 1.5rem;
        }

        @media (min-width: 768px) {
            .content {
                padding: 2rem;
            }
        }

        .welcome-section {
            text-align: center;
            padding: 3rem 1rem;
        }

        .welcome-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .welcome-text {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .guide-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }

        @media (min-width: 768px) {
            .guide-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .guide-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .guide-card {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            border: 3px solid var(--primary);
            border-radius: 16px;
            padding: 1.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
        }

        .guide-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.25);
        }

        .guide-card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
        }

        .guide-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .guide-card-desc {
            font-size: 0.9rem;
            color: var(--text);
            line-height: 1.5;
        }

        .guide-card.pricing {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-color: var(--warning);
        }

        .guide-card.pricing .guide-card-title {
            color: #b45309;
        }
    </style>

    <div class="container">
        <div class="header">
            <h1>📊 Guide Chat - Client Handling Flowchart</h1>
            <p>Panduan lengkap menangani klien dari awal hingga selesai</p>
        </div>

        <!-- Mobile Select Navigation -->
        <div class="content">
            <select class="mobile-nav-select" onchange="window.location.href = this.value;">
                <option value="{{ route('guide-chat.index') }}">📍 Pilih Phase...</option>
                <option value="{{ route('guide-chat.phase1') }}">Phase 1: Initial Contact</option>
                <option value="{{ route('guide-chat.phase2') }}">Phase 2: Requirement Gathering</option>
                <option value="{{ route('guide-chat.phase3') }}">Phase 3: Quotation & Deal</option>
                <option value="{{ route('guide-chat.phase4') }}">Phase 4: Development</option>
                <option value="{{ route('guide-chat.phase5') }}">Phase 5: Delivery & Payment</option>
                <option value="{{ route('guide-chat.pricing') }}">💰 Pricing Guide</option>
            </select>
        </div>

        <!-- Desktop Tab Navigation -->
        <div class="nav-tabs">
            <a href="{{ route('guide-chat.phase1') }}" class="nav-tab">Phase 1</a>
            <a href="{{ route('guide-chat.phase2') }}" class="nav-tab">Phase 2</a>
            <a href="{{ route('guide-chat.phase3') }}" class="nav-tab">Phase 3</a>
            <a href="{{ route('guide-chat.phase4') }}" class="nav-tab">Phase 4</a>
            <a href="{{ route('guide-chat.phase5') }}" class="nav-tab">Phase 5</a>
            <a href="{{ route('guide-chat.pricing') }}" class="nav-tab">💰 Pricing</a>
        </div>

        <div class="content">
            <div class="welcome-section">
                <div class="welcome-icon">🚀</div>
                <h2 class="welcome-title">Selamat Datang di Guide Chat!</h2>
                <p class="welcome-text">
                    Pilih salah satu phase di atas untuk melihat panduan detail menangani klien, atau lihat pricing guide untuk referensi harga.
                </p>

                <div class="guide-grid">
                    <a href="{{ route('guide-chat.phase1') }}" class="guide-card">
                        <span class="guide-card-icon">📞</span>
                        <div class="guide-card-title">Phase 1</div>
                        <p class="guide-card-desc">Initial Contact - Kontak pertama dengan klien</p>
                    </a>

                    <a href="{{ route('guide-chat.phase2') }}" class="guide-card">
                        <span class="guide-card-icon">📋</span>
                        <div class="guide-card-title">Phase 2</div>
                        <p class="guide-card-desc">Requirement Gathering - Menggali kebutuhan klien</p>
                    </a>

                    <a href="{{ route('guide-chat.phase3') }}" class="guide-card">
                        <span class="guide-card-icon">💼</span>
                        <div class="guide-card-title">Phase 3</div>
                        <p class="guide-card-desc">Quotation & Deal - Penawaran dan kesepakatan</p>
                    </a>

                    <a href="{{ route('guide-chat.phase4') }}" class="guide-card">
                        <span class="guide-card-icon">⚙️</span>
                        <div class="guide-card-title">Phase 4</div>
                        <p class="guide-card-desc">Development - Proses pengerjaan project</p>
                    </a>

                    <a href="{{ route('guide-chat.phase5') }}" class="guide-card">
                        <span class="guide-card-icon">✅</span>
                        <div class="guide-card-title">Phase 5</div>
                        <p class="guide-card-desc">Delivery & Payment - Serah terima dan pembayaran</p>
                    </a>

                    <a href="{{ route('guide-chat.pricing') }}" class="guide-card pricing">
                        <span class="guide-card-icon">💰</span>
                        <div class="guide-card-title">Pricing Guide</div>
                        <p class="guide-card-desc">Referensi harga untuk berbagai jenis project</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
