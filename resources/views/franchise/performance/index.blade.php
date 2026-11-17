@extends('layouts.app')

@section('title')
<title>Performance Tracking - Mapped Sponsors</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ================================================================
   FRANCHISE PERFORMANCE ULTRA-PREMIUM DESIGN SYSTEM
   ================================================================ */
:root {
    --fp-primary: #4f46e5;
    --fp-primary-dark: #3730a3;
    --fp-primary-light: #eef2ff;
    --fp-accent: #ff6a00;
    --fp-accent-light: #fff3eb;
    --fp-success: #10b981;
    --fp-success-light: #ecfdf5;
    --fp-info: #06b6d4;
    --fp-info-light: #ecfeff;
    --fp-slate-900: #0f172a;
    --fp-slate-800: #1e293b;
    --fp-slate-700: #334155;
    --fp-slate-600: #475569;
    --fp-slate-500: #64748b;
    --fp-slate-400: #94a3b8;
    --fp-slate-200: #e2e8f0;
    --fp-slate-100: #f1f5f9;
    --fp-slate-50: #f8fafc;
    --fp-radius: 18px;
    --fp-shadow-card: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
    --fp-shadow-hover: 0 20px 35px -8px rgba(79, 70, 229, 0.18), 0 6px 12px -2px rgba(15, 23, 42, 0.04);
}

.fp-wrap {
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    color: var(--fp-slate-800);
}

/* 1. Header Deck */
.fp-header-deck {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 55%, #2e1065 100%);
    border-radius: 24px;
    padding: 32px 36px;
    color: #ffffff;
    box-shadow: 0 16px 40px -10px rgba(15, 23, 42, 0.35);
    margin-bottom: 26px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.fp-header-deck::before {
    content: '';
    position: absolute;
    top: -60%;
    right: -10%;
    width: 380px;
    height: 380px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.fp-header-deck::after {
    content: '';
    position: absolute;
    bottom: -60%;
    left: 20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255, 106, 0, 0.22) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.fp-header-content {
    position: relative;
    z-index: 2;
}

.fp-eyebrow-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: #ffffff;
    margin-bottom: 12px;
}

.fp-dot-pulse {
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: fpPulse 2s infinite;
}

@keyframes fpPulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.fp-header-title {
    font-family: 'Outfit', sans-serif;
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    line-height: 1.2;
    margin-bottom: 8px;
}

.fp-header-desc {
    color: #cbd5e1;
    font-size: 0.95rem;
    max-width: 650px;
    line-height: 1.5;
    margin-bottom: 0;
}

.fp-btn-glass {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    border-radius: 12px;
    padding: 10px 20px;
    font-size: 0.88rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.fp-btn-glass:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
    transform: translateY(-2px);
}

/* 2. Executive KPI Summary Cards */
.fp-kpi-card {
    background: #ffffff;
    border-radius: var(--fp-radius);
    padding: 22px 24px;
    border: 1px solid var(--fp-slate-200);
    box-shadow: var(--fp-shadow-card);
    transition: all 0.25s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.fp-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 25px -4px rgba(0, 0, 0, 0.08);
}

.fp-kpi-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.fp-kpi-label {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--fp-slate-500);
    margin: 0;
}

.fp-kpi-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
}

.fp-kpi-value {
    font-family: 'Outfit', sans-serif;
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--fp-slate-900);
    letter-spacing: -0.5px;
    line-height: 1.2;
    margin-bottom: 4px;
}

.fp-kpi-sub {
    font-size: 0.78rem;
    color: var(--fp-slate-500);
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 0;
}

/* 3. Search and Filter Bar */
.fp-search-card {
    background: #ffffff;
    border-radius: var(--fp-radius);
    padding: 16px 20px;
    border: 1px solid var(--fp-slate-200);
    box-shadow: var(--fp-shadow-card);
    margin-bottom: 26px;
}

.fp-search-input-group {
    background: #f8fafc;
    border: 1px solid var(--fp-slate-200);
    border-radius: 12px;
    padding: 4px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.2s ease;
}

.fp-search-input-group:focus-within {
    border-color: var(--fp-primary);
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}

.fp-search-input {
    border: none;
    background: transparent;
    padding: 8px 0;
    font-size: 0.92rem;
    color: var(--fp-slate-800);
    width: 100%;
    outline: none;
}

.fp-btn-primary {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: #ffffff;
    border: none;
    border-radius: 12px;
    padding: 11px 22px;
    font-weight: 700;
    font-size: 0.88rem;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
}

.fp-btn-primary:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(79, 70, 229, 0.4);
}

.fp-btn-outline {
    background: #ffffff;
    color: var(--fp-slate-600);
    border: 1px solid var(--fp-slate-200);
    border-radius: 12px;
    padding: 11px 18px;
    font-weight: 600;
    font-size: 0.88rem;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.fp-btn-outline:hover {
    background: var(--fp-slate-50);
    color: var(--fp-slate-800);
    border-color: var(--fp-slate-400);
}

/* 4. Luxury Sponsor Cards */
.fp-sponsor-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid var(--fp-slate-200);
    box-shadow: var(--fp-shadow-card);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}

.fp-sponsor-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--fp-shadow-hover);
    border-color: #c7d2fe;
}

.fp-sponsor-header {
    padding: 24px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
}

.fp-avatar-monogram {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: #ffffff;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.28);
    flex-shrink: 0;
}

.fp-sponsor-name {
    font-family: 'Outfit', sans-serif;
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--fp-slate-900);
    line-height: 1.3;
    margin: 0 0 4px 0;
}

.fp-code-tag {
    font-size: 0.75rem;
    font-weight: 700;
    background: #eef2ff;
    color: #4f46e5;
    padding: 2px 8px;
    border-radius: 6px;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Associated Network Badge */
.fp-badge-network {
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    color: #4338ca;
    border: 1px solid #c7d2fe;
    font-size: 0.74rem;
    font-weight: 800;
    padding: 6px 12px;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    letter-spacing: 0.3px;
}

.fp-badge-standalone {
    background: #f8fafc;
    color: #475569;
    border: 1px solid var(--fp-slate-200);
    font-size: 0.74rem;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.fp-sponsor-body {
    padding: 22px 24px;
    flex-grow: 1;
}

.fp-info-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 12px;
    font-size: 0.85rem;
    color: var(--fp-slate-600);
    line-height: 1.45;
}

.fp-info-row i {
    font-size: 0.95rem;
    flex-shrink: 0;
    margin-top: 3px;
}

/* Mini KPI Stats Grid */
.fp-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-top: 18px;
    padding-top: 18px;
    border-top: 1px dashed var(--fp-slate-200);
}

.fp-mini-stat {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    padding: 12px 10px;
    text-align: center;
    transition: all 0.2s ease;
}

.fp-sponsor-card:hover .fp-mini-stat {
    background: #ffffff;
    border-color: var(--fp-slate-200);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
}

.fp-mini-stat-label {
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--fp-slate-400);
    margin-bottom: 4px;
}

.fp-mini-stat-val {
    font-family: 'Outfit', sans-serif;
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--fp-slate-900);
    line-height: 1.2;
}

.fp-sponsor-footer {
    padding: 18px 24px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
}

.fp-btn-action {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.88rem;
    text-decoration: none;
    transition: all 0.25s ease;
}

.fp-btn-action-primary {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.28);
}

.fp-btn-action-primary:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(79, 70, 229, 0.38);
}

.fp-btn-action-outline {
    background: #ffffff;
    color: #4f46e5;
    border: 1.5px solid #c7d2fe;
}

.fp-btn-action-outline:hover {
    background: #eef2ff;
    color: #3730a3;
    border-color: #818cf8;
    transform: translateY(-2px);
}

.fp-btn-action i.fa-arrow-right {
    transition: transform 0.2s ease;
}

.fp-btn-action:hover i.fa-arrow-right {
    transform: translateX(4px);
}

.fp-card-hint {
    font-size: 0.72rem;
    color: var(--fp-slate-400);
    text-align: center;
    margin-top: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

/* Empty State Card */
.fp-empty-card {
    background: #ffffff;
    border-radius: 24px;
    border: 1px solid var(--fp-slate-200);
    padding: 60px 24px;
    text-align: center;
    box-shadow: var(--fp-shadow-card);
}
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="fp-wrap">
        {{-- Flash Messages --}}
        @include('includes.message')

        {{-- 1. HEADER DECK --}}
        <div class="fp-header-deck">
            <div class="fp-header-content">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="fp-eyebrow-pill">
                            <span class="fp-dot-pulse"></span>
                            <span>Franchise Performance Monitor</span>
                        </div>
                        <h1 class="fp-header-title text-white">Performance Tracking</h1>
                        <p class="fp-header-desc">
                            Select any sponsor restaurant to inspect live multi-branch operations, real-time orders, revenue volume, and master business intelligence.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('franchise.dashboard') }}" class="fp-btn-glass">
                            <i class="fas fa-chart-line text-warning"></i>
                            <span>Executive Dashboard</span>
                        </a>
                        <a href="{{ route('franchise.performance.index') }}" class="fp-btn-glass" title="Refresh Data">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. EXECUTIVE KPI OVERVIEW COUNTER CARDS --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="fp-kpi-card" style="border-left: 4px solid var(--fp-primary);">
                    <div class="fp-kpi-top">
                        <span class="fp-kpi-label">Mapped Sponsors</span>
                        <div class="fp-kpi-icon-box" style="background: var(--fp-primary-light); color: var(--fp-primary);">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    <div class="fp-kpi-value">{{ number_format($totalSponsorsCount ?? $parents->count()) }}</div>
                    <p class="fp-kpi-sub">
                        <i class="fas fa-check-circle text-success"></i> Parent sponsor restaurants
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="fp-kpi-card" style="border-left: 4px solid var(--fp-info);">
                    <div class="fp-kpi-top">
                        <span class="fp-kpi-label">Total Outlets Network</span>
                        <div class="fp-kpi-icon-box" style="background: var(--fp-info-light); color: var(--fp-info);">
                            <i class="fas fa-code-branch"></i>
                        </div>
                    </div>
                    <div class="fp-kpi-value">{{ number_format($totalNetworkOutlets ?? $parents->sum(fn($p) => 1 + $p->associated_count)) }}</div>
                    <p class="fp-kpi-sub">
                        <i class="fas fa-network-wired text-info"></i> Parents + Associated branches
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="fp-kpi-card" style="border-left: 4px solid var(--fp-accent);">
                    <div class="fp-kpi-top">
                        <span class="fp-kpi-label">Network Today Orders</span>
                        <div class="fp-kpi-icon-box" style="background: var(--fp-accent-light); color: var(--fp-accent);">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <div class="fp-kpi-value">{{ number_format($totalTodayOrders ?? $parents->sum('group_today_orders')) }}</div>
                    <p class="fp-kpi-sub">
                        <i class="fas fa-calendar-day text-warning"></i> Orders recorded today
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="fp-kpi-card" style="border-left: 4px solid var(--fp-success);">
                    <div class="fp-kpi-top">
                        <span class="fp-kpi-label">Gross Network Sales</span>
                        <div class="fp-kpi-icon-box" style="background: var(--fp-success-light); color: var(--fp-success);">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div class="fp-kpi-value text-success">₹{{ number_format(($totalNetworkRevenue ?? $parents->sum('group_total_revenue')), 0) }}</div>
                    <p class="fp-kpi-sub">
                        <i class="fas fa-chart-line text-success"></i> Cumulative sales volume
                    </p>
                </div>
            </div>
        </div>

        {{-- 3. SEARCH & FILTER DECK --}}
        <div class="fp-search-card">
            <form method="GET" action="{{ route('franchise.performance.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-8 col-lg-9">
                    <div class="fp-search-input-group">
                        <i class="fas fa-search text-muted"></i>
                        <input type="text" name="search" class="fp-search-input" 
                               placeholder="Search mapped sponsor by restaurant name, unique ID, or location..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-8 col-md-2 col-lg-2">
                    <button type="submit" class="fp-btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                <div class="col-4 col-md-2 col-lg-1">
                    <a href="{{ route('franchise.performance.index') }}" class="fp-btn-outline w-100" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 4. MAPPED PARENT RESTAURANTS GRID --}}
        <div class="row g-4">
            @forelse($parents as $parent)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="fp-sponsor-card">
                        {{-- Card Header --}}
                        <div class="fp-sponsor-header">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <span class="fp-code-tag">
                                    <i class="fas fa-hashtag"></i> {{ $parent->restaurant_id_unique ?: 'REST-' . $parent->id }}
                                </span>

                                @if($parent->associated_count > 0)
                                    <span class="fp-badge-network">
                                        <i class="fas fa-code-branch"></i> {{ $parent->associated_count }} Associated Restaurant{{ $parent->associated_count > 1 ? 's' : '' }}
                                    </span>
                                @else
                                    <span class="fp-badge-standalone">
                                        <i class="fas fa-store"></i> 0 Associated Restaurants
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <div class="fp-avatar-monogram">
                                    {{ strtoupper(substr($parent->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="fp-sponsor-name">{{ $parent->name }}</h4>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small" style="font-size: 11px; font-weight: 700;">
                                        <i class="fas fa-check-circle me-1"></i> Active Sponsor
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="fp-sponsor-body">
                            {{-- Address --}}
                            <div class="fp-info-row">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <div>
                                    <span class="fw-bold text-dark">Location:</span> 
                                    {{ $parent->address ?: 'Address not configured' }}
                                    @if($parent->pincode)
                                        <span class="text-dark fw-bold">({{ $parent->pincode }})</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Owner Information --}}
                            @if($parent->owner)
                                <div class="fp-info-row">
                                    <i class="fas fa-user-tie text-primary"></i>
                                    <div>
                                        <span class="fw-bold text-dark">Owner:</span> 
                                        {{ $parent->owner->name }}
                                        <span class="text-muted small">({{ $parent->owner->phone }})</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Quick Metrics Stats Grid --}}
                            <div class="fp-stats-grid">
                                <div class="fp-mini-stat">
                                    <div class="fp-mini-stat-label">Today Orders</div>
                                    <div class="fp-mini-stat-val text-warning">{{ number_format($parent->group_today_orders) }}</div>
                                </div>

                                <div class="fp-mini-stat">
                                    <div class="fp-mini-stat-label">Total Orders</div>
                                    <div class="fp-mini-stat-val text-primary">{{ number_format($parent->group_total_orders) }}</div>
                                </div>

                                <div class="fp-mini-stat">
                                    <div class="fp-mini-stat-label">Total Sales</div>
                                    <div class="fp-mini-stat-val text-success">₹{{ number_format($parent->group_total_revenue, 0) }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Footer with Dynamic Click Actions --}}
                        <div class="fp-sponsor-footer">
                            @if($parent->associated_count === 0)
                                {{-- 0 Associated: Direct Entry into Master Report --}}
                                <a href="{{ $parent->target_url }}" class="fp-btn-action fp-btn-action-primary">
                                    <span><i class="fas fa-chart-pie me-2"></i> View Master Report</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                                <div class="fp-card-hint">
                                    <i class="fas fa-bolt text-warning"></i> Direct entry to restaurant master report
                                </div>
                            @else
                                {{-- Has Associated Outlets: Open Associated Restaurants Directory --}}
                                <a href="{{ $parent->target_url }}" class="fp-btn-action fp-btn-action-outline">
                                    <span><i class="fas fa-network-wired me-2"></i> Explore Outlets Network ({{ $parent->associated_count + 1 }})</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                                <div class="fp-card-hint">
                                    <i class="fas fa-info-circle text-primary"></i> Includes Parent HQ + {{ $parent->associated_count }} Branch Outlet{{ $parent->associated_count > 1 ? 's' : '' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="fp-empty-card">
                        <div class="mb-3">
                            <i class="fas fa-store-slash text-muted" style="font-size: 3.5rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">No Mapped Sponsors Found</h4>
                        <p class="text-muted small mb-4" style="max-width: 480px; margin: 0 auto;">
                            @if(request('search'))
                                No sponsor restaurant matched your search query "<strong>{{ request('search') }}</strong>". Try searching with different keywords.
                            @else
                                You do not have any mapped parent restaurants assigned to your franchise owner account currently.
                            @endif
                        </p>
                        <a href="{{ route('franchise.dashboard') }}" class="fp-btn-primary">
                            <i class="fas fa-chart-line me-1"></i> Return to Franchise Dashboard
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

    </div>

</div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
@endsection
