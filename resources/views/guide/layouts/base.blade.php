@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    :root {
        --primary: #6366f1; --primary-dark: #4f46e5; --secondary: #8b5cf6;
        --success: #10b981; --warning: #f59e0b; --danger: #ef4444;
        --dark: #1e293b; --light: #f8fafc; --border: #e2e8f0;
        --text: #334155; --text-light: #64748b;
    }
    
    body { font-family: 'Space Grotesk', sans-serif; color: var(--text); }
    
    .container { max-width: 1400px; margin: 0 auto; background: white; border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; }
    
    .header { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        padding: 2rem 1rem; color: white; text-align: center; position: relative; overflow: hidden; }
    
    .header::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    
    .header h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; position: relative; z-index: 1; }
    .header p { font-size: 0.9rem; opacity: 0.9; position: relative; z-index: 1; }
    
    @media (min-width: 768px) {
        .header { padding: 3rem 2rem; }
        .header h1 { font-size: 2.5rem; }
        .header p { font-size: 1.1rem; }
    }
    
    .mobile-nav-select { display: block; width: 100%; padding: 1rem; background: white;
        border: 2px solid var(--primary); border-radius: 12px; font-family: 'Space Grotesk', sans-serif;
        font-size: 0.95rem; font-weight: 600; color: var(--primary); cursor: pointer; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 1rem center; background-size: 20px; padding-right: 3rem; }
    
    .mobile-nav-select:focus { outline: none; border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
    
    .nav-tabs { display: none; background: var(--light); border-bottom: 2px solid var(--border);
        overflow-x: auto; padding: 0 0.5rem; }
    
    @media (min-width: 768px) {
        .mobile-nav-select { display: none; }
        .nav-tabs { display: flex; padding: 0 1rem; }
    }
    
    .nav-tab { padding: 0.75rem 1rem; background: none; border: none; cursor: pointer;
        font-family: 'Space Grotesk', sans-serif; font-size: 0.85rem; font-weight: 600;
        color: var(--text-light); border-bottom: 3px solid transparent; transition: all 0.3s ease;
        white-space: nowrap; text-decoration: none; display: inline-block; }
    
    @media (min-width: 768px) {
        .nav-tab { padding: 1rem 1.5rem; font-size: 0.95rem; }
    }
    
    .nav-tab:hover { color: var(--primary); background: rgba(99, 102, 241, 0.05); }
    .nav-tab.active { color: var(--primary); border-bottom-color: var(--primary); }
    
    .content { padding: 1rem; }
    @media (min-width: 768px) { .content { padding: 2rem; } }
    
    .flowchart-container { position: relative; padding: 1.5rem 0; }
    @media (min-width: 768px) { .flowchart-container { padding: 2rem 0; } }
    
    .flow-node { background: white; border: 3px solid var(--primary); border-radius: 16px;
        padding: 1.25rem; margin: 1.5rem auto; max-width: 600px;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15); transition: all 0.3s ease; position: relative; }
    
    @media (min-width: 768px) { .flow-node { padding: 1.5rem; } }
    
    .flow-node:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(99, 102, 241, 0.25); }
    
    .flow-node.start { border-color: var(--success);
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
    
    .flow-node.decision { border-color: var(--warning);
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 20px; }
    
    .flow-node.process { border-color: var(--primary);
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); }
    
    .flow-node.end { border-color: var(--danger);
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); }
    
    .flow-node.success { border-color: var(--success);
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
    
    .node-label { display: inline-block; background: var(--primary); color: white;
        padding: 0.3rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.75rem; }
    
    @media (min-width: 768px) {
        .node-label { padding: 0.35rem 0.75rem; font-size: 0.75rem; }
    }
    
    .node-label.success { background: var(--success); }
    .node-label.warning { background: var(--warning); }
    .node-label.danger { background: var(--danger); }
    
    .node-title { font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 0.75rem; }
    @media (min-width: 768px) { .node-title { font-size: 1.3rem; } }
    
    .node-content { color: var(--text); line-height: 1.6; font-size: 0.9rem; }
    @media (min-width: 768px) { .node-content { font-size: 0.95rem; } }
    
    .node-code, .node-example { background: rgba(255, 255, 255, 0.6); border-left: 3px solid var(--primary);
        padding: 0.75rem; border-radius: 8px; font-size: 0.8rem; margin-top: 0.75rem; }
    
    @media (min-width: 768px) {
        .node-code, .node-example { font-size: 0.85rem; }
    }
    
    .branches { display: grid; grid-template-columns: 1fr; gap: 1rem; margin-top: 1rem; }
    
    @media (min-width: 640px) { .branches { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 768px) { .branches { grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); } }
    
    .branch { background: white; border: 2px solid var(--border); border-radius: 12px;
        padding: 1rem; cursor: pointer; transition: all 0.3s ease; }
    
    .branch:hover { border-color: var(--primary); transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15); }
    
    .branch-icon { width: 36px; height: 36px; background: var(--primary); border-radius: 10px;
        display: flex; align-items: center; justify-content: center; color: white;
        font-weight: 700; margin-bottom: 0.75rem; font-size: 0.9rem; }
    
    @media (min-width: 768px) { .branch-icon { width: 40px; height: 40px; } }
    
    .branch.success .branch-icon { background: var(--success); }
    .branch.warning .branch-icon { background: var(--warning); }
    .branch.danger .branch-icon { background: var(--danger); }
    
    .branch-title { font-weight: 600; color: var(--dark); margin-bottom: 0.5rem; font-size: 0.95rem; }
    .branch-desc { font-size: 0.8rem; color: var(--text-light); line-height: 1.5; }
    @media (min-width: 768px) { .branch-desc { font-size: 0.85rem; } }
    
    .arrow { text-align: center; font-size: 1.5rem; color: var(--primary); margin: 1rem 0;
        animation: bounce 2s infinite; }
    
    @media (min-width: 768px) { .arrow { font-size: 2rem; } }
    
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    
    .info-box { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-left: 4px solid #0ea5e9; padding: 1rem; border-radius: 12px; margin: 1.5rem 0; }
    
    @media (min-width: 768px) { .info-box { padding: 1.25rem; } }
    
    .info-box-title { font-weight: 700; color: #0369a1; margin-bottom: 0.5rem;
        display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem; }
    
    @media (min-width: 768px) { .info-box-title { font-size: 1rem; } }
    
    .info-box-content { color: #075985; font-size: 0.85rem; line-height: 1.6; }
    @media (min-width: 768px) { .info-box-content { font-size: 0.9rem; } }
    
    .legend { display: flex; flex-wrap: wrap; gap: 0.75rem; padding: 1rem;
        background: var(--light); border-radius: 12px; margin-bottom: 2rem; }
    
    @media (min-width: 768px) { .legend { gap: 1rem; padding: 1.5rem; } }
    
    .legend-item { display: flex; align-items: center; gap: 0.5rem; }
    
    .legend-color { width: 20px; height: 20px; border-radius: 6px; border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
    
    @media (min-width: 768px) { .legend-color { width: 24px; height: 24px; } }
    
    .legend-color.start { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
    .legend-color.decision { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); }
    .legend-color.process { background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); }
    .legend-color.end { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); }
    
    .legend-text { font-size: 0.8rem; font-weight: 600; color: var(--text); }
    @media (min-width: 768px) { .legend-text { font-size: 0.85rem; } }
    
    .timeline-connector { width: 3px; height: 30px;
        background: linear-gradient(to bottom, var(--primary), transparent); margin: 0 auto; }
    
    @media (min-width: 768px) { .timeline-connector { height: 40px; } }
    
    .price-table { background: white; border: 2px solid var(--border); border-radius: 12px;
        overflow: hidden; margin: 1.5rem 0; }
    
    .price-row { display: flex; flex-direction: column; gap: 0.5rem; padding: 0.875rem 1rem;
        border-bottom: 1px solid var(--border); }
    
    @media (min-width: 640px) {
        .price-row { flex-direction: row; justify-content: space-between; align-items: center;
            gap: 1rem; padding: 1rem 1.5rem; }
    }
    
    .price-row:last-child { border-bottom: none; }
    .price-row:nth-child(even) { background: var(--light); }
    
    .price-label { font-weight: 600; color: var(--dark); font-size: 0.875rem; }
    @media (min-width: 640px) { .price-label { font-size: 1rem; } }
    
    .price-value { color: var(--primary); font-weight: 700;
        font-family: 'JetBrains Mono', monospace; font-size: 0.9rem; }
    
    @media (min-width: 640px) { .price-value { font-size: 1rem; } }
</style>

<div class="container">
    <div class="header">
        <h1>@yield('page-title')</h1>
        <p>@yield('page-subtitle')</p>
    </div>
    
    <div class="content">
        <select class="mobile-nav-select" onchange="window.location.href = this.value;">
            <option value="{{ route('guide-chat.index') }}" {{ request()->routeIs('guide-chat.index') ? 'selected' : '' }}>📍 Pilih Phase...</option>
            <option value="{{ route('guide-chat.phase1') }}" {{ request()->routeIs('guide-chat.phase1') ? 'selected' : '' }}>Phase 1: Initial Contact</option>
            <option value="{{ route('guide-chat.phase2') }}" {{ request()->routeIs('guide-chat.phase2') ? 'selected' : '' }}>Phase 2: Requirement Gathering</option>
            <option value="{{ route('guide-chat.phase3') }}" {{ request()->routeIs('guide-chat.phase3') ? 'selected' : '' }}>Phase 3: Quotation & Deal</option>
            <option value="{{ route('guide-chat.phase4') }}" {{ request()->routeIs('guide-chat.phase4') ? 'selected' : '' }}>Phase 4: Development</option>
            <option value="{{ route('guide-chat.phase5') }}" {{ request()->routeIs('guide-chat.phase5') ? 'selected' : '' }}>Phase 5: Delivery & Payment</option>
            <option value="{{ route('guide-chat.pricing') }}" {{ request()->routeIs('guide-chat.pricing') ? 'selected' : '' }}>💰 Pricing Guide</option>
        </select>
    </div>
    
    <div class="nav-tabs">
        <a href="{{ route('guide-chat.phase1') }}" class="nav-tab {{ request()->routeIs('guide-chat.phase1') ? 'active' : '' }}">Phase 1</a>
        <a href="{{ route('guide-chat.phase2') }}" class="nav-tab {{ request()->routeIs('guide-chat.phase2') ? 'active' : '' }}">Phase 2</a>
        <a href="{{ route('guide-chat.phase3') }}" class="nav-tab {{ request()->routeIs('guide-chat.phase3') ? 'active' : '' }}">Phase 3</a>
        <a href="{{ route('guide-chat.phase4') }}" class="nav-tab {{ request()->routeIs('guide-chat.phase4') ? 'active' : '' }}">Phase 4</a>
        <a href="{{ route('guide-chat.phase5') }}" class="nav-tab {{ request()->routeIs('guide-chat.phase5') ? 'active' : '' }}">Phase 5</a>
        <a href="{{ route('guide-chat.pricing') }}" class="nav-tab {{ request()->routeIs('guide-chat.pricing') ? 'selected' : '' }}">💰 Pricing</a>
    </div>
    
    <div class="content">
        @yield('phase-content')
    </div>
</div>

@stack('scripts')
@endsection
