@extends('layouts.app')

@section('title')
<title>Associated Outlets - {{ $parent->name }}</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ================================================================
   FRANCHISE OUTLETS NETWORK ULTRA-PREMIUM DESIGN SYSTEM
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

.fp-back-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 6px 16px;
    border-radius: 30px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #ffffff;
    text-decoration: none;
    transition: all 0.2s ease;
    margin-bottom: 12px;
}

.fp-back-pill:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
    transform: translateX(-3px);
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
    max-width: 750px;
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

/* 2. Executive KPI Counter Cards */
.fp-kpi-card {
    background: #ffffff;
    border-radius: var(--fp-radius);
    padding: 22px 24px;
    border: 1px solid var(--fp-slate-200);
    box-shadow: var(--fp-shadow-card);
    transition: all 0.25s ease;
    height: 100%;
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

/* 3. Luxury Outlet Cards */
.fp-outlet-card {
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

.fp-outlet-card.is-hq {
    border-top: 5px solid #4f46e5;
}

.fp-outlet-card.is-branch {
    border-top: 5px solid #06b6d4;
}

.fp-outlet-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--fp-shadow-hover);
    border-color: #c7d2fe;
}

.fp-outlet-header {
    padding: 24px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
}

.fp-avatar-monogram {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    color: #ffffff;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);
    flex-shrink: 0;
}

.fp-avatar-hq {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}

.fp-avatar-branch {
    background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%);
}

.fp-outlet-name {
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

.fp-outlet-badge-hq {
    background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
    color: #ffffff;
    font-size: 0.74rem;
    font-weight: 800;
    padding: 6px 12px;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
}

.fp-outlet-badge-branch {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: #ffffff;
    font-size: 0.74rem;
    font-weight: 800;
    padding: 6px 12px;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 8px rgba(6, 182, 212, 0.3);
}

.fp-outlet-body {
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
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-top: 18px;
    padding-top: 18px;
    border-top: 1px dashed var(--fp-slate-200);
}

.fp-mini-stat {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    padding: 12px;
    text-align: center;
    transition: all 0.2s ease;
}

.fp-outlet-card:hover .fp-mini-stat {
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
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--fp-slate-900);
    line-height: 1.2;
}

.fp-mini-stat-sub {
    font-size: 0.72rem;
    color: var(--fp-slate-500);
    margin-top: 2px;
}

.fp-lifetime-box {
    grid-column: span 2;
    background: #f0fdf4;
    border: 1px solid #dcfce7;
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.fp-lifetime-box span {
    font-size: 0.78rem;
    font-weight: 700;
    color: #166534;
}

.fp-lifetime-box strong {
    font-family: 'Outfit', sans-serif;
    font-size: 1.15rem;
    font-weight: 800;
    color: #15803d;
}

.fp-outlet-footer {
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

.fp-btn-action-branch {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(6, 182, 212, 0.28);
}

.fp-btn-action-branch:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(6, 182, 212, 0.38);
}

.fp-btn-action i.fa-arrow-right {
    transition: transform 0.2s ease;
}

.fp-btn-action:hover i.fa-arrow-right {
    transform: translateX(4px);
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
                        <a href="{{ route('franchise.performance.index') }}" class="fp-back-pill">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back to Mapped Sponsors</span>
                        </a>
                        <h1 class="fp-header-title text-white">{{ $parent->name }}</h1>
                        <p class="fp-header-desc">
                            Showing {{ $allRestaurants->count() }} locations (1 Parent HQ + {{ $parent->outlets->count() }} Associated Branch Outlets). Click any restaurant to track its dedicated Master Report.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('franchise.performance.index') }}" class="fp-btn-glass">
                            <i class="fas fa-th-large me-1"></i>
                            <span>All Sponsors</span>
                        </a>
                        <a href="{{ route('franchise.dashboard') }}" class="fp-btn-glass">
                            <i class="fas fa-chart-line text-warning"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. EXECUTIVE KPI OVERVIEW COUNTER CARDS FOR THIS GROUP --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="fp-kpi-card" style="border-left: 4px solid var(--fp-primary);">
                    <div class="fp-kpi-top">
                        <span class="fp-kpi-label">Network Outlets</span>
                        <div class="fp-kpi-icon-box" style="background: var(--fp-primary-light); color: var(--fp-primary);">
                            <i class="fas fa-store-alt"></i>
                        </div>
                    </div>
                    <div class="fp-kpi-value">{{ $allRestaurants->count() }}</div>
                    <p class="fp-kpi-sub">
                        <i class="fas fa-code-branch text-primary"></i> 1 HQ + {{ $parent->outlets->count() }} Branch Outlets
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="fp-kpi-card" style="border-left: 4px solid var(--fp-accent);">
                    <div class="fp-kpi-top">
                        <span class="fp-kpi-label">Today Orders</span>
                        <div class="fp-kpi-icon-box" style="background: var(--fp-accent-light); color: var(--fp-accent);">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <div class="fp-kpi-value">{{ number_format($parentTodayNetworkOrders ?? $allRestaurants->sum('today_orders')) }}</div>
                    <p class="fp-kpi-sub">
                        <i class="fas fa-calendar-day text-warning"></i> ₹{{ number_format(($parentTodayNetworkRevenue ?? $allRestaurants->sum('today_revenue')), 0) }} generated today
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="fp-kpi-card" style="border-left: 4px solid var(--fp-info);">
                    <div class="fp-kpi-top">
                        <span class="fp-kpi-label">Cumulative Orders</span>
                        <div class="fp-kpi-icon-box" style="background: var(--fp-info-light); color: var(--fp-info);">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                    <div class="fp-kpi-value">{{ number_format($parentTotalNetworkOrders ?? $allRestaurants->sum('total_orders')) }}</div>
                    <p class="fp-kpi-sub">
                        <i class="fas fa-check-circle text-info"></i> All-time completed orders
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="fp-kpi-card" style="border-left: 4px solid var(--fp-success);">
                    <div class="fp-kpi-top">
                        <span class="fp-kpi-label">Total Group Revenue</span>
                        <div class="fp-kpi-icon-box" style="background: var(--fp-success-light); color: var(--fp-success);">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div class="fp-kpi-value text-success">₹{{ number_format(($parentTotalNetworkRevenue ?? $allRestaurants->sum('total_revenue')), 0) }}</div>
                    <p class="fp-kpi-sub">
                        <i class="fas fa-chart-line text-success"></i> Combined network sales
                    </p>
                </div>
            </div>
        </div>

        {{-- 3. RESTAURANTS DIRECTORY (HQ FIRST, FOLLOWED BY ASSOCIATED BRANCHES) --}}
        <div class="row g-4">
            @foreach($allRestaurants as $rest)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="fp-outlet-card {{ $rest->is_hq ? 'is-hq' : 'is-branch' }}">
                        {{-- Card Header --}}
                        <div class="fp-outlet-header">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <span class="fp-code-tag">
                                    <i class="fas fa-hashtag"></i> {{ $rest->restaurant_id_unique ?: 'REST-' . $rest->id }}
                                </span>

                                @if($rest->is_hq)
                                    <span class="fp-outlet-badge-hq">
                                        <i class="fas fa-building"></i> HQ / Parent Restaurant
                                    </span>
                                @else
                                    <span class="fp-outlet-badge-branch">
                                        <i class="fas fa-code-branch"></i> Associated Sub-Outlet
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <div class="fp-avatar-monogram {{ $rest->is_hq ? 'fp-avatar-hq' : 'fp-avatar-branch' }}">
                                    {{ strtoupper(substr($rest->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="fp-outlet-name">{{ $rest->name }}</h4>
                                    <span class="badge {{ $rest->status === 'A' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' }} px-2 py-1 small" style="font-size: 11px; font-weight: 700;">
                                        <i class="fas {{ $rest->status === 'A' ? 'fa-check-circle' : 'fa-pause-circle' }} me-1"></i>
                                        {{ $rest->status === 'A' ? 'Active Outlet' : 'Inactive Outlet' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="fp-outlet-body">
                            {{-- Address & Details --}}
                            <div class="fp-info-row">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <div>
                                    <span class="fw-bold text-dark">Location:</span> 
                                    {{ $rest->address ?: 'Address not configured' }}
                                    @if($rest->pincode)
                                        <span class="text-dark fw-bold">({{ $rest->pincode }})</span>
                                    @endif
                                </div>
                            </div>

                            @if($rest->gstin)
                                <div class="fp-info-row">
                                    <i class="fas fa-file-invoice text-primary"></i>
                                    <div>
                                        <span class="fw-bold text-dark">GSTIN:</span> 
                                        <span>{{ $rest->gstin }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Metrics Grid --}}
                            <div class="fp-stats-grid">
                                <div class="fp-mini-stat">
                                    <div class="fp-mini-stat-label">Today Orders</div>
                                    <div class="fp-mini-stat-val text-warning">{{ number_format($rest->today_orders) }}</div>
                                    <div class="fp-mini-stat-sub">₹{{ number_format($rest->today_revenue, 0) }}</div>
                                </div>

                                <div class="fp-mini-stat">
                                    <div class="fp-mini-stat-label">Total Orders</div>
                                    <div class="fp-mini-stat-val text-primary">{{ number_format($rest->total_orders) }}</div>
                                    <div class="fp-mini-stat-sub">{{ $rest->total_dishes }} Menu Dishes</div>
                                </div>

                                <div class="fp-lifetime-box">
                                    <span><i class="fas fa-wallet me-1"></i> Lifetime Sales:</span>
                                    <strong>₹{{ number_format($rest->total_revenue, 2) }}</strong>
                                </div>
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="fp-outlet-footer">
                            <a href="{{ $rest->report_url }}" class="fp-btn-action {{ $rest->is_hq ? 'fp-btn-action-primary' : 'fp-btn-action-branch' }}">
                                <span><i class="fas fa-chart-line me-2"></i> View Master Report</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

</div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
@endsection
