@extends('layouts.app')

@section('title')
<title>Dashboard - Restaurant Management POS</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<!-- FontAwesome 6.5 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    :root {
        --dash-primary: #ff5e14;
        --dash-primary-dark: #ea580c;
        --dash-primary-light: rgba(255, 94, 20, 0.08);
        --dash-primary-gradient: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
        --dash-dark: #0f172a;
        --dash-slate: #1e293b;
        --dash-muted: #64748b;
        --dash-border: #e2e8f0;
        --dash-border-light: #edf2f7;
        --dash-bg: #f4f6fb;
        --dash-card-bg: #ffffff;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        background-color: var(--dash-bg) !important;
        color: var(--dash-slate) !important;
    }

    .dash-page-wrap {
        padding: 24px 28px;
        max-width: 100%;
    }
    @media (max-width: 768px) {
        .dash-page-wrap {
            padding: 16px 14px;
        }
    }

    /* 1. Header Banner Deck */
    .dash-welcome-deck {
        background: var(--dash-card-bg);
        border-radius: 20px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.04);
        border: 1px solid var(--dash-border-light);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
    }
    .dash-welcome-deck::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 6px;
        height: 100%;
        background: var(--dash-primary-gradient);
    }
    .dash-welcome-left {
        display: flex;
        align-items: center;
        gap: 18px;
        flex: 1 1 auto;
    }
    .dash-welcome-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: var(--dash-primary-gradient);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
        box-shadow: 0 8px 20px rgba(255, 94, 20, 0.28);
        flex-shrink: 0;
    }
    .dash-welcome-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--dash-dark);
        margin: 0 0 4px 0;
        letter-spacing: -0.02em;
    }
    .dash-welcome-sub {
        font-size: 0.86rem;
        color: var(--dash-muted);
        margin: 0;
    }
    .dash-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .dash-live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseLive 1.8s infinite;
    }
    @keyframes pulseLive {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .btn-dash-action {
        height: 42px;
        padding: 0 20px;
        border-radius: 30px;
        font-size: 0.86rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        white-space: nowrap;
    }
    .btn-dash-primary {
        background: var(--dash-primary-gradient);
        color: #ffffff !important;
        border: none;
        box-shadow: 0 4px 14px rgba(255, 94, 20, 0.25);
    }
    .btn-dash-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 94, 20, 0.35);
    }
    .btn-dash-secondary {
        background: #ffffff;
        color: var(--dash-slate) !important;
        border: 1px solid var(--dash-border);
    }
    .btn-dash-secondary:hover {
        background: #f8fafc;
        border-color: var(--dash-primary);
        color: var(--dash-primary) !important;
        transform: translateY(-1px);
    }

    /* 2. Stat Cards Grid */
    .dash-stat-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 22px 24px;
        border: 1px solid var(--dash-border-light);
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .dash-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
    }
    .dash-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 4px;
    }
    .stat-rev-today::before { background: var(--dash-primary-gradient); }
    .stat-ord-today::before { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
    .stat-rev-month::before { background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%); }
    .stat-tables::before { background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%); }
    .stat-dishes::before { background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%); }
    .stat-staff::before { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }

    .dash-stat-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .dash-stat-label {
        font-size: 0.74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--dash-muted);
    }
    .dash-stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .stat-rev-today .dash-stat-icon-wrap { background: rgba(255, 94, 20, 0.1); color: var(--dash-primary); }
    .stat-ord-today .dash-stat-icon-wrap { background: #ecfdf5; color: #10b981; }
    .stat-rev-month .dash-stat-icon-wrap { background: #eef2ff; color: #6366f1; }
    .stat-tables .dash-stat-icon-wrap { background: #f0f9ff; color: #0284c7; }
    .stat-dishes .dash-stat-icon-wrap { background: #f5f3ff; color: #8b5cf6; }
    .stat-staff .dash-stat-icon-wrap { background: #fffbeb; color: #d97706; }

    .dash-stat-val {
        font-family: 'Outfit', sans-serif;
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--dash-dark);
        line-height: 1.15;
        letter-spacing: -0.02em;
    }
    .dash-stat-footer {
        margin-top: 10px;
        font-size: 0.78rem;
        color: var(--dash-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* 3. Modern Content Cards */
    .dash-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid var(--dash-border-light);
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .dash-card-header {
        padding: 22px 28px;
        background: #ffffff;
        border-bottom: 1px solid var(--dash-border-light);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .dash-card-title-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .dash-card-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .dash-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--dash-dark);
        margin: 0;
        letter-spacing: -0.01em;
    }
    .dash-card-sub {
        font-size: 0.82rem;
        color: var(--dash-muted);
        margin: 0;
    }
    .dash-card-body {
        padding: 24px 28px;
    }

    /* Period Filter Pills */
    .period-filter-pills {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 30px;
        gap: 4px;
    }
    .period-tab-btn {
        padding: 6px 18px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
        background: transparent;
        border: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .period-tab-btn:hover {
        color: #0f172a;
    }
    .period-tab-btn.active {
        background: #ffffff;
        color: var(--dash-primary);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    /* Hot Dishes List */
    .hot-dish-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-radius: 14px;
        background: #f8fafc;
        margin-bottom: 10px;
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }
    .hot-dish-row:hover {
        background: #fff8f5;
        border-color: rgba(255, 94, 20, 0.2);
        transform: translateX(3px);
    }
    .hot-rank-badge {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.88rem;
        flex-shrink: 0;
    }
    .rank-1 { background: #fef3c7; color: #b45309; }
    .rank-2 { background: #e2e8f0; color: #475569; }
    .rank-3 { background: #ffedd5; color: #c2410c; }
    .rank-other { background: #f1f5f9; color: #64748b; }

    .hot-dish-title {
        font-weight: 700;
        color: var(--dash-dark);
        font-size: 0.92rem;
    }
    .hot-dish-qty-pill {
        padding: 5px 12px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid var(--dash-border);
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--dash-primary);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    /* Trend Selector */
    .trend-dish-select {
        border-radius: 30px;
        border: 1px solid var(--dash-border);
        padding: 10px 20px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--dash-slate);
        outline: none;
        transition: all 0.2s ease;
        background-color: #f8fafc;
    }
    .trend-dish-select:focus {
        border-color: var(--dash-primary);
        box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.12);
        background-color: #ffffff;
    }

    /* Table Styles */
    #dashboardOrdersTable {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }
    #dashboardOrdersTable thead th {
        background: #f8fafc !important;
        padding: 14px 20px !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 0.76rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.06em !important;
        color: var(--dash-muted) !important;
        border-bottom: 2px solid var(--dash-border) !important;
        white-space: nowrap !important;
    }
    #dashboardOrdersTable tbody td {
        padding: 16px 20px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle !important;
        font-size: 0.86rem !important;
        color: var(--dash-slate) !important;
    }
    #dashboardOrdersTable tbody tr:hover td {
        background-color: #fafcff !important;
    }

    .order-status-pill {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.74rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .pill-paid { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .pill-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .pill-cancelled { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    .dataTables-toolbar-deck {
        padding: 16px 28px !important;
        background: #fbfcfe !important;
        border-bottom: 1px solid #edf2f7 !important;
    }
    .dataTables-footer-deck {
        padding: 16px 28px !important;
        background: #ffffff !important;
        border-top: 1px solid #edf2f7 !important;
    }
    .dataTables_filter input {
        border-radius: 30px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 6px 16px !important;
        font-size: 0.85rem !important;
        outline: none !important;
        margin-left: 8px !important;
        background: #ffffff !important;
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="dash-page-wrap">
        {{-- Flash messages --}}
        @include('includes.message')

        {{-- 1. Welcome Header Deck --}}
        <div class="dash-welcome-deck">
            <div class="dash-welcome-left">
                <div class="dash-welcome-icon">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.06em;">Restaurant Control Center</span>
                        <div class="dash-live-badge">
                            <span class="dash-live-dot"></span> POS Online
                        </div>
                    </div>
                    <h1 class="dash-welcome-title">Executive Dashboard</h1>
                    <p class="dash-welcome-sub">Real-time revenue stream, live orders, kitchen metrics & inventory overview.</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('order.create') }}" class="btn-dash-action btn-dash-primary">
                    <i class="fa-solid fa-cash-register"></i> New POS Order
                </a>
                <a href="{{ route('order.management.dashboard') }}" class="btn-dash-action btn-dash-secondary">
                    <i class="fa-solid fa-table-cells-large"></i> Live Tables & KOT
                </a>
                <a href="{{ route('inventory.live') }}" class="btn-dash-action btn-dash-secondary">
                    <i class="fa-solid fa-boxes-stacked"></i> Live Stocks
                </a>
            </div>
        </div>

        {{-- 2. Performance Stats Grid (6 Key Metrics) --}}
        <div class="row g-3 mb-4">
            {{-- Metric 1: Today Revenue --}}
            <div class="col-xl-2 col-lg-4 col-md-6 col-6">
                <div class="dash-stat-card stat-rev-today">
                    <div class="dash-stat-head">
                        <span class="dash-stat-label">Today's Revenue</span>
                        <div class="dash-stat-icon-wrap">
                            <i class="fa-solid fa-indian-rupee-sign"></i>
                        </div>
                    </div>
                    <div>
                        <div class="dash-stat-val text-primary">₹{{ number_format($totalRevenueToday, 2) }}</div>
                        <div class="dash-stat-footer">
                            <i class="fa-solid fa-chart-line text-success"></i>
                            <span>Avg Order: ₹{{ number_format($avgOrderValue, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metric 2: Today Orders --}}
            <div class="col-xl-2 col-lg-4 col-md-6 col-6">
                <div class="dash-stat-card stat-ord-today">
                    <div class="dash-stat-head">
                        <span class="dash-stat-label">Orders Today</span>
                        <div class="dash-stat-icon-wrap">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </div>
                    </div>
                    <div>
                        <div class="dash-stat-val text-success">{{ number_format($totalOrdersToday) }}</div>
                        <div class="dash-stat-footer">
                            <i class="fa-solid fa-clock text-warning"></i>
                            <span>{{ $pendingOrders }} Pending / In-Kitchen</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metric 3: Monthly Revenue --}}
            <div class="col-xl-2 col-lg-4 col-md-6 col-6">
                <div class="dash-stat-card stat-rev-month">
                    <div class="dash-stat-head">
                        <span class="dash-stat-label">This Month Sale</span>
                        <div class="dash-stat-icon-wrap">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                    </div>
                    <div>
                        <div class="dash-stat-val" style="color: #6366f1;">₹{{ number_format($totalRevenueMonth, 2) }}</div>
                        <div class="dash-stat-footer">
                            <i class="fa-solid fa-file-invoice text-muted"></i>
                            <span>{{ number_format($totalOrdersMonth) }} paid orders</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metric 4: Table Occupancy --}}
            <div class="col-xl-2 col-lg-4 col-md-6 col-6">
                <div class="dash-stat-card stat-tables">
                    <div class="dash-stat-head">
                        <span class="dash-stat-label">Table Occupancy</span>
                        <div class="dash-stat-icon-wrap">
                            <i class="fa-solid fa-chair"></i>
                        </div>
                    </div>
                    <div>
                        <div class="dash-stat-val" style="color: #0284c7;">{{ $occupiedTables }} / {{ $totalTables }}</div>
                        <div class="dash-stat-footer">
                            <i class="fa-solid fa-circle-dot text-info"></i>
                            <span>{{ $totalTables > 0 ? round(($occupiedTables / $totalTables) * 100) : 0 }}% tables occupied</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metric 5: Active Menu Dishes --}}
            <div class="col-xl-2 col-lg-4 col-md-6 col-6">
                <div class="dash-stat-card stat-dishes">
                    <div class="dash-stat-head">
                        <span class="dash-stat-label">Active Dishes</span>
                        <div class="dash-stat-icon-wrap">
                            <i class="fa-solid fa-bowl-food"></i>
                        </div>
                    </div>
                    <div>
                        <div class="dash-stat-val" style="color: #8b5cf6;">{{ number_format($totalDishes) }}</div>
                        <div class="dash-stat-footer">
                            <span class="badge bg-success bg-opacity-10 text-success p-1 rounded font-monospace">{{ $totalVeg }} Veg</span>
                            <span class="badge bg-danger bg-opacity-10 text-danger p-1 rounded font-monospace">{{ $totalNonVeg }} Non-Veg</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metric 6: Active Staff --}}
            <div class="col-xl-2 col-lg-4 col-md-6 col-6">
                <div class="dash-stat-card stat-staff">
                    <div class="dash-stat-head">
                        <span class="dash-stat-label">Active Staff</span>
                        <div class="dash-stat-icon-wrap">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                    </div>
                    <div>
                        <div class="dash-stat-val" style="color: #d97706;">{{ number_format($totalStaff) }}</div>
                        <div class="dash-stat-footer">
                            <i class="fa-solid fa-shield-halved text-success"></i>
                            <span>Authorized Team</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Top Products Analytics & Hot Dishes Row --}}
        <div class="row g-4 mb-4">
            {{-- Left: Top Products Chart --}}
            <div class="col-lg-8">
                <div class="dash-card h-100">
                    <div class="dash-card-header">
                        <div class="dash-card-title-group">
                            <div class="dash-card-icon" style="background: rgba(255, 94, 20, 0.1); color: var(--dash-primary);">
                                <i class="fa-solid fa-chart-simple"></i>
                            </div>
                            <div>
                                <h3 class="dash-card-title">Top Selling Menu Items</h3>
                                <p class="dash-card-sub">Highest order volume ranked by selected timeframe</p>
                            </div>
                        </div>

                        <div class="period-filter-pills">
                            <button type="button" class="period-tab-btn active" id="tab-daily">Daily</button>
                            <button type="button" class="period-tab-btn" id="tab-monthly">Monthly</button>
                            <button type="button" class="period-tab-btn" id="tab-yearly">Yearly</button>
                        </div>
                    </div>

                    <div class="dash-card-body">
                        <div style="position: relative; height: 280px; width: 100%;">
                            <canvas id="topProductsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Hot Dishes Today --}}
            <div class="col-lg-4">
                <div class="dash-card h-100">
                    <div class="dash-card-header">
                        <div class="dash-card-title-group">
                            <div class="dash-card-icon" style="background: #fff1f2; color: #f43f5e;">
                                <i class="fa-solid fa-fire-flame-curved"></i>
                            </div>
                            <div>
                                <h3 class="dash-card-title">Hot Items Today</h3>
                                <p class="dash-card-sub">Real-time bestsellers right now</p>
                            </div>
                        </div>
                    </div>

                    <div class="dash-card-body p-3">
                        @forelse($hotDaily ?? [] as $index => $hot)
                            @php
                                $rankClass = match($index) {
                                    0 => 'rank-1',
                                    1 => 'rank-2',
                                    2 => 'rank-3',
                                    default => 'rank-other',
                                };
                                $medal = match($index) {
                                    0 => '🥇',
                                    1 => '🥈',
                                    2 => '🥉',
                                    default => '#' . ($index + 1),
                                };
                            @endphp
                            <div class="hot-dish-row">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="hot-rank-badge {{ $rankClass }}">
                                        {{ $medal }}
                                    </div>
                                    <div>
                                        <div class="hot-dish-title">{{ $hot->subcategory->name ?? 'Dish Item' }}</div>
                                        <small class="text-muted">{{ $hot->subcategory->food_type ?? 'Menu' }}</small>
                                    </div>
                                </div>
                                <div class="hot-dish-qty-pill">
                                    {{ $hot->total }} Sold
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="fa-solid fa-fire fa-2x mb-2 opacity-50 text-warning"></i>
                                <p class="small mb-0">No sales recorded yet today.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Dish Monthly Performance Trend Section --}}
        <div class="dash-card mb-4">
            <div class="dash-card-header">
                <div class="dash-card-title-group">
                    <div class="dash-card-icon" style="background: #eef2ff; color: #6366f1;">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <h3 class="dash-card-title">Dish Performance Trend (12 Months)</h3>
                        <p class="dash-card-sub">Select any dish to visualize its monthly sales trajectory and seasonal demand</p>
                    </div>
                </div>

                <div style="min-width: 260px;">
                    <select id="dishSelect" class="form-select trend-dish-select">
                        <option value="">-- Choose a Dish to Inspect --</option>
                        @foreach($dishes ?? [] as $d)
                            <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->food_type ?? 'Dish' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="dash-card-body">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="dishMonthlyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- 5. Recent Orders Table Card --}}
        <div class="dash-card">
            <div class="dash-card-header">
                <div class="dash-card-title-group">
                    <div class="dash-card-icon" style="background: #ecfdf5; color: #10b981;">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="dash-card-title">Recent Orders</h3>
                        <p class="dash-card-sub">Latest transactions and dine-in activities processed</p>
                    </div>
                </div>

                <div>
                    <a href="{{ route('order.report') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-dark">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View All Orders
                    </a>
                </div>
            </div>

            <div class="dash-card-body p-0">
                <div class="table-responsive">
                    <table id="dashboardOrdersTable" class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Order ID</th>
                                <th>Table / Mode</th>
                                <th>Customer</th>
                                <th>Items Ordered</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-end">Total Paid</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th class="text-center" style="width: 100px;">Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders ?? [] as $key => $o)
                                @php
                                    $paymentStatus = strtoupper($o->payment_status ?? 'PENDING');
                                    $statusPillClass = match($paymentStatus) {
                                        'PAID' => 'pill-paid',
                                        'CANCELLED', 'REJECTED' => 'pill-cancelled',
                                        default => 'pill-pending',
                                    };
                                    $tableName = $o->table ? $o->table->table_name : ($o->order_type ?? 'Dine-In');
                                    $itemsCount = $o->orderItems ? $o->orderItems->count() : 0;
                                @endphp
                                <tr>
                                    <td class="text-muted fw-bold">{{ $key + 1 }}</td>
                                    <td>
                                        <span class="fw-bold font-monospace text-dark" style="font-size: 0.9rem;">
                                            {{ $o->order_id ?? ('#' . $o->id) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-chair me-1 text-primary"></i> {{ $tableName }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $o->customer_name ?: 'Walk-in Guest' }}</div>
                                        @if($o->customer_phone)
                                            <small class="text-muted font-monospace">{{ $o->customer_phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 rounded-pill">
                                            {{ $itemsCount }} Item(s)
                                        </span>
                                    </td>
                                    <td class="text-end font-monospace text-muted">
                                        ₹{{ number_format($o->total_amount ?? 0, 2) }}
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-dark font-monospace" style="font-size: 0.95rem;">
                                            ₹{{ number_format($o->grand_total ?? 0, 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="order-status-pill {{ $statusPillClass }}">
                                            @if($paymentStatus === 'PAID')
                                                <i class="fa-solid fa-circle-check"></i> Paid
                                            @elseif($paymentStatus === 'CANCELLED')
                                                <i class="fa-solid fa-circle-xmark"></i> Cancelled
                                            @else
                                                <i class="fa-solid fa-clock"></i> Pending
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small text-muted">{{ \Carbon\Carbon::parse($o->created_at)->format('h:i A') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('order.invoice', $o->id) }}" 
                                           class="btn btn-sm btn-light border rounded-pill px-2 py-1" 
                                           title="View Invoice"
                                           target="_blank">
                                            <i class="fa-solid fa-file-invoice text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
</div>

@endsection

@section('script')
<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
    // 1. Initialize DataTable for Recent Orders
    $("#dashboardOrdersTable").DataTable({
        "pageLength": 10,
        "ordering": false,
        "dom": '<"dataTables-toolbar-deck d-flex flex-wrap justify-content-between align-items-center gap-2"f>rt<"dataTables-footer-deck d-flex flex-wrap justify-content-between align-items-center gap-2"ip>',
        "language": {
            "emptyTable": '<div class="py-4 text-center text-muted"><i class="fa-solid fa-receipt fa-2x mb-2 opacity-50"></i><div>No recent orders found.</div></div>',
            "search": "_INPUT_",
            "searchPlaceholder": "Filter recent orders...",
            "info": "Showing _START_ to _END_ of _TOTAL_ orders",
            "infoEmpty": "Showing 0 to 0 of 0 orders"
        }
    });

    // 2. Chart Configurations & Colors
    const primaryColor = '#ff5e14';
    const primaryGradientStart = '#ff5e14';
    const primaryGradientEnd = '#ff8c42';

    const topDaily   = @json($topDailySeries ?? []);
    const topMonthly = @json($topMonthlySeries ?? []);
    const topYearly  = @json($topYearlySeries ?? []);

    function formatData(series) {
        if (!series || !series.length) {
            return { labels: ['No Sales Data'], data: [0] };
        }
        return {
            labels: series.map(s => s.subcategory?.name ?? "Dish"),
            data: series.map(s => Number(s.total ?? 0))
        };
    }

    // Top Products Chart
    const ctxTop = document.getElementById("topProductsChart").getContext("2d");
    const gradientTop = ctxTop.createLinearGradient(0, 0, 0, 260);
    gradientTop.addColorStop(0, '#ff5e14');
    gradientTop.addColorStop(1, '#ff9e66');

    let initialTopData = formatData(topDaily);
    let topChart = new Chart(ctxTop, {
        type: "bar",
        data: { 
            labels: initialTopData.labels, 
            datasets: [{
                label: "Units Sold",
                data: initialTopData.data,
                backgroundColor: gradientTop,
                borderRadius: 12,
                barPercentage: 0.55,
                categoryPercentage: 0.75
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { size: 13, family: 'Plus Jakarta Sans', weight: 'bold' },
                    bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return ` Total Sold: ${context.raw} units`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { family: 'Outfit', size: 12 }, precision: 0 }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' } }
                }
            }
        }
    });

    // Switch Chart Periods
    $(".period-tab-btn").click(function() {
        $(".period-tab-btn").removeClass("active");
        $(this).addClass("active");
        
        let tabId = $(this).attr("id").replace("tab-", "");
        let seriesData = tabId === "daily" ? topDaily : (tabId === "monthly" ? topMonthly : topYearly);
        let formatted = formatData(seriesData);
        
        topChart.data.labels = formatted.labels;
        topChart.data.datasets[0].data = formatted.data;
        topChart.update();
    });

    // 3. Dish Monthly Trend Chart
    const ctxMonthly = document.getElementById("dishMonthlyChart").getContext("2d");
    const gradientMonthly = ctxMonthly.createLinearGradient(0, 0, 0, 240);
    gradientMonthly.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
    gradientMonthly.addColorStop(1, 'rgba(99, 102, 241, 0.00)');

    let dishChart = new Chart(ctxMonthly, {
        type: "line",
        data: { 
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 
            datasets: [{
                label: "Units Sold",
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                borderColor: '#6366f1',
                backgroundColor: gradientMonthly,
                borderWidth: 3,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { size: 13, family: 'Plus Jakarta Sans', weight: 'bold' },
                    bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                    padding: 12,
                    cornerRadius: 10
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { family: 'Outfit', size: 12 }, precision: 0 }
                },
                x: {
                    grid: { color: '#f8fafc' },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' } }
                }
            }
        }
    });

    // Load Dish Data via Ajax
    $("#dishSelect").change(function () {
        let id = $(this).val();
        if (!id) return;

        let url = "{{ route('dashboard.dish.monthly', ':id') }}".replace(":id", id);
        $.getJSON(url, function (res) {
            dishChart.data.labels = res.labels;
            dishChart.data.datasets[0].data = res.data;
            dishChart.update();
        });
    });

    // Auto-select first dish if available
    if ($("#dishSelect option").length > 1) {
        $("#dishSelect").prop('selectedIndex', 1).trigger('change');
    }
});
</script>
@endsection