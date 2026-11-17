@extends('layouts.app')

@section('title')
<title>Franchise Sponsorship Owner Dashboard || Bill&Bite</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --fso-primary: #ff6a00;
        --fso-primary-hover: #e55a00;
        --fso-dark: #0f172a;
        --fso-slate: #1e293b;
        --fso-card-bg: #ffffff;
        --fso-border: #f1f5f9;
        --fso-border-subtle: #e2e8f0;
        --fso-muted: #64748b;
        --fso-gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #312e81 100%);
    }

    body {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--fso-slate);
        background-color: #f8fafc;
    }

    /* Hero Banner */
    .fso-hero-card {
        background: var(--fso-gradient-hero);
        color: #ffffff;
        border-radius: 20px;
        padding: 30px 32px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .fso-hero-card::after {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 106, 0, 0.18) 0%, rgba(99, 102, 241, 0.1) 60%, transparent 80%);
        top: -100px;
        right: -60px;
        pointer-events: none;
    }
    .hero-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #ffd8a8;
        border: 1px solid rgba(255, 255, 255, 0.15);
        margin-bottom: 12px;
    }
    .hero-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.85rem;
        color: #ffffff;
        margin-bottom: 6px;
        line-height: 1.25;
    }
    .hero-subtitle {
        color: #cbd5e1;
        font-size: 0.92rem;
        margin-bottom: 0;
        max-width: 650px;
    }

    /* Metric Stat Cards */
    .metric-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 22px 24px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        border: 1px solid var(--fso-border);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
    }
    .metric-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .metric-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .metric-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--fso-muted);
        margin: 0;
    }
    .metric-value {
        font-family: 'Outfit', sans-serif;
        font-size: 1.95rem;
        font-weight: 800;
        color: var(--fso-dark);
        line-height: 1.15;
        margin-bottom: 4px;
    }
    .metric-footer-note {
        font-size: 0.78rem;
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--fso-muted);
    }

    /* Section Cards */
    .dashboard-panel {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid var(--fso-border);
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .dashboard-panel-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--fso-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .panel-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.15rem;
        color: var(--fso-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .panel-subtitle {
        font-size: 0.82rem;
        color: var(--fso-muted);
        margin-top: 2px;
        margin-bottom: 0;
    }

    /* Top Performing Parent Restaurant Card */
    .parent-restaurant-card {
        border-radius: 16px;
        border: 1.5px solid var(--fso-border-subtle);
        background: #ffffff;
        margin-bottom: 18px;
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .parent-restaurant-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }
    .parent-restaurant-card.rank-1 {
        border-color: #f59e0b;
        background: linear-gradient(180deg, #fffbeb 0%, #ffffff 80px);
    }
    .parent-header-bar {
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }
    .rank-pill {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .rank-pill.gold {
        background: #fef3c7;
        color: #b45309;
        border: 1.5px solid #fde68a;
    }
    .rank-pill.silver {
        background: #f1f5f9;
        color: #475569;
        border: 1.5px solid #e2e8f0;
    }
    .rank-pill.bronze {
        background: #ffedd5;
        color: #c2410c;
        border: 1.5px solid #fed7aa;
    }
    .rank-pill.regular {
        background: #f8fafc;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
    }

    /* Sub-restaurant Drawer */
    .sub-restaurants-drawer {
        background: #f8fafc;
        border-top: 1px solid var(--fso-border);
        padding: 16px 20px;
    }
    .sub-item-row {
        background: #ffffff;
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid var(--fso-border-subtle);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        transition: transform 0.15s ease;
    }
    .sub-item-row:hover {
        transform: translateX(4px);
        border-color: #cbd5e1;
    }
    .sub-item-row:last-child {
        margin-bottom: 0;
    }

    /* Top Dishes Table */
    .top-dishes-table th {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 0.6px;
        color: #64748b;
        background: #f8fafc;
        padding: 14px 18px;
        border-bottom: 1px solid var(--fso-border);
    }
    .top-dishes-table td {
        padding: 14px 18px;
        vertical-align: middle;
        border-bottom: 1px solid var(--fso-border);
    }
    .dish-img-thumb {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        object-fit: cover;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .dish-rank-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 800;
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">

        <!-- Top Breadcrumb & Super Admin Owner Switcher -->
        <div class="page-header mb-4">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-7 col-12 mb-2 mb-md-0">
                        <div class="page-header-title">
                            <h4 class="mb-1 fw-bold text-dark" style="font-family: 'Outfit', sans-serif;">
                                Franchise Sponsorship Executive Dashboard
                            </h4>
                        </div>
                        <ul class="breadcrumb bg-transparent p-0 m-0">
                            <li class="breadcrumb-item"><a href="{{ route('franchise.dashboard') }}">Franchise Portal</a></li>
                            <li class="breadcrumb-item text-muted" aria-current="page">Sponsorship Network Overview</li>
                        </ul>
                    </div>
                    <div class="col-md-5 col-12 text-md-end text-start">
                        @if(auth()->user()->role === 'SA' && $allFsoOwners->count() > 0)
                            <div class="d-inline-flex align-items-center gap-2 p-1 bg-white rounded-3 border shadow-sm">
                                <label for="switchFsoSelect" class="small text-muted mb-0 ps-2 fw-bold">
                                    <i class="fas fa-user-tie text-primary me-1"></i> FSO Owner:
                                </label>
                                <select id="switchFsoSelect" class="form-select form-select-sm border-0 fw-bold" style="min-width: 190px;" onchange="window.location.href='{{ route('franchise.dashboard') }}?fso_id=' + this.value">
                                    @foreach($allFsoOwners as $owner)
                                        <option value="{{ $owner->id }}" {{ $owner->id == $fsoUser->id ? 'selected' : '' }}>
                                            {{ $owner->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <span class="badge bg-white text-dark border px-3 py-2 shadow-sm rounded-pill fw-semibold">
                                <i class="fas fa-calendar-alt text-primary me-1"></i> {{ date('l, d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. Hero Welcome Card -->
        <div class="fso-hero-card">
            <div class="row align-items-center position-relative" style="z-index: 1;">
                <div class="col-lg-8 col-md-12">
                    <span class="hero-badge-pill">
                        <i class="fas fa-shield-alt"></i> Franchise Sponsorship Network
                    </span>
                    <h1 class="hero-title">
                        Welcome Back, {{ $fsoUser->name }}!
                    </h1>
                    <p class="hero-subtitle">
                        Live analytics, order volume, and revenue performance for your <strong>{{ $mappedSponsorsCount }} mapped parent restaurant brands</strong> and their <strong>{{ $subRestaurantsCount }} active branch outlets</strong>.
                    </p>
                </div>
                <div class="col-lg-4 col-md-12 text-lg-end text-start mt-3 mt-lg-0">
                    <div class="d-inline-flex flex-column align-items-lg-end align-items-start gap-1 p-3 rounded-4" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12);">
                        <span class="text-white-50 small text-uppercase fw-bold letter-spacing-1">Network Locations</span>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fs-2 fw-bold text-white font-monospace">{{ $totalLocationsCount }}</span>
                            <span class="text-warning small fw-bold">Active Kitchens</span>
                        </div>
                        <span class="text-white-50" style="font-size: 0.76rem;">
                            {{ $mappedSponsorsCount }} Parent HQs + {{ $subRestaurantsCount }} Sub-Outlets
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($mappedSponsorsCount === 0)
            <!-- Empty State for Unmapped Franchise Owners -->
            <div class="dashboard-panel text-center py-5 px-4">
                <div class="mb-3" style="font-size: 3rem; color: #cbd5e1;">
                    <i class="fas fa-store-slash"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif;">
                    No Parent Restaurants Mapped Yet
                </h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 540px;">
                    Your Franchise Owner account does not currently have any parent restaurants assigned to it. Once your administrator maps restaurant brands to your profile, all real-time orders, revenue, dishes, and branch analytics will automatically populate here.
                </p>
                @if(auth()->user()->role === 'SA')
                    <a href="{{ route('admin.fso.index') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold">
                        <i class="fas fa-sitemap me-2"></i> Open Restaurant Mapping Tool
                    </a>
                @endif
            </div>
        @else

            <!-- 2. Four Key Metric Cards Row -->
            <div class="row g-3 mb-4">
                <!-- Card 1: Mapped Sponsors Count -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="metric-card">
                        <div>
                            <div class="metric-header">
                                <h6 class="metric-title">Mapped Sponsors</h6>
                                <div class="metric-icon-box" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                                    <i class="fas fa-handshake"></i>
                                </div>
                            </div>
                            <div class="metric-value">{{ number_format($mappedSponsorsCount) }}</div>
                        </div>
                        <div class="metric-footer-note mt-3">
                            <span class="badge bg-indigo-subtle text-indigo rounded-pill px-2 py-1" style="background: #eef2ff; color: #4338ca; font-weight: 700;">
                                <i class="fas fa-store me-1"></i> {{ $mappedSponsorsCount }} Parent HQs
                            </span>
                            <span class="text-muted ms-auto">+{{ $subRestaurantsCount }} Sub-branches</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Today's Order Qty -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="metric-card">
                        <div>
                            <div class="metric-header">
                                <h6 class="metric-title">Today's Orders</h6>
                                <div class="metric-icon-box" style="background: rgba(255, 106, 0, 0.12); color: #ff6a00;">
                                    <i class="fas fa-receipt"></i>
                                </div>
                            </div>
                            <div class="metric-value">{{ number_format($todayOrdersCount) }}</div>
                        </div>
                        <div class="metric-footer-note mt-3">
                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-1" style="background: #fff7ed; color: #c2410c; font-weight: 700;">
                                <i class="fas fa-utensils me-1"></i> {{ number_format($todayItemsQty) }} Items Sold
                            </span>
                            <span class="text-muted ms-auto">Today's Total Qty</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Today's Revenue -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="metric-card">
                        <div>
                            <div class="metric-header">
                                <h6 class="metric-title">Today's Billing</h6>
                                <div class="metric-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                            <div class="metric-value" style="color: #059669;">
                                ₹{{ number_format($todayRevenue, 2) }}
                            </div>
                        </div>
                        <div class="metric-footer-note mt-3">
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1" style="background: #ecfdf5; color: #047857; font-weight: 700;">
                                <i class="fas fa-arrow-up me-1"></i> Today's Sales
                            </span>
                            <span class="text-muted ms-auto">{{ $totalLocationsCount }} Locations</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Total Lifetime Invoice Value -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="metric-card">
                        <div>
                            <div class="metric-header">
                                <h6 class="metric-title">Total Invoice Value</h6>
                                <div class="metric-icon-box" style="background: rgba(14, 165, 233, 0.12); color: #0284c7;">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                            <div class="metric-value text-dark">
                                ₹{{ number_format($totalInvoiceValue, 2) }}
                            </div>
                        </div>
                        <div class="metric-footer-note mt-3">
                            <span class="badge bg-info-subtle text-info rounded-pill px-2 py-1" style="background: #f0f9ff; color: #0369a1; font-weight: 700;">
                                <i class="fas fa-check-double me-1"></i> {{ number_format($totalOrdersCount) }} Invoices
                            </span>
                            <span class="text-muted ms-auto">AOV: ₹{{ number_format($avgOrderValue, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Interactive Analytics & Performance Graphs -->
            <div class="row g-3 mb-4">
                <!-- Sales & Orders Trajectory (14 Days) -->
                <div class="col-lg-8 col-12">
                    <div class="dashboard-panel h-100 mb-0">
                        <div class="dashboard-panel-header">
                            <div>
                                <h5 class="panel-title">
                                    <i class="fas fa-chart-line text-primary"></i> 14-Day Sales & Order Volume Trend
                                </h5>
                                <p class="panel-subtitle">Aggregated daily performance across all mapped parent brands and sub-branches</p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center gap-2 small fw-bold">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #ff6a00; display: inline-block;"></span>
                                    <span>Revenue (₹)</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 small fw-bold">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #3b82f6; display: inline-block;"></span>
                                    <span>Orders (Qty)</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4" style="height: 330px; position: relative;">
                            <canvas id="salesTrajectoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Network Revenue Distribution -->
                <div class="col-lg-4 col-12">
                    <div class="dashboard-panel h-100 mb-0">
                        <div class="dashboard-panel-header">
                            <div>
                                <h5 class="panel-title">
                                    <i class="fas fa-chart-pie text-indigo" style="color: #6366f1;"></i> Revenue Breakdown
                                </h5>
                                <p class="panel-subtitle">Parent Headquarters vs Sub-Outlets contribution</p>
                            </div>
                        </div>
                        <div class="p-4 d-flex flex-column justify-content-between" style="min-height: 330px;">
                            <div style="height: 200px; position: relative;">
                                <canvas id="networkShareChart"></canvas>
                            </div>
                            <div class="pt-3 border-top mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2 small">
                                    <span class="d-flex align-items-center gap-2 fw-semibold">
                                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #ff6a00; display: inline-block;"></span>
                                        Parent HQs
                                    </span>
                                    <strong class="text-dark font-monospace">₹{{ number_format($parentTotalInvoiceValue, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center small">
                                    <span class="d-flex align-items-center gap-2 fw-semibold">
                                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #6366f1; display: inline-block;"></span>
                                        Sub-Outlets
                                    </span>
                                    <strong class="text-dark font-monospace">₹{{ number_format($subTotalInvoiceValue, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Top Performing Restaurants (Parent & Sub-Restaurants) -->
            <div class="dashboard-panel mb-4">
                <div class="dashboard-panel-header">
                    <div>
                        <h5 class="panel-title">
                            <i class="fas fa-trophy text-warning"></i> Top Performing Parent Restaurants & Actual Sub-Outlets
                        </h5>
                        <p class="panel-subtitle">
                            Ranked by group invoice volume. Expand any parent restaurant to inspect how each of its sub-branches is actually performing.
                        </p>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">
                        <i class="fas fa-sitemap me-1 text-primary"></i> {{ count($parentPerformance) }} Parent Restaurant Groups
                    </span>
                </div>

                <div class="p-4">
                    @forelse($parentPerformance as $idx => $group)
                        @php
                            $parent = $group['parent'];
                            $rank = $idx + 1;
                            $rankClass = $rank === 1 ? 'rank-1' : '';
                            $pillClass = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : 'regular'));
                        @endphp

                        <div class="parent-restaurant-card {{ $rankClass }}">
                            <!-- Parent Restaurant Header -->
                            <div class="parent-header-bar">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rank-pill {{ $pillClass }}" title="Rank #{{ $rank }}">
                                        @if($rank === 1) <i class="fas fa-crown"></i> @else #{{ $rank }} @endif
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Outfit', sans-serif;">
                                                {{ $parent->name }}
                                            </h5>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.72rem;">
                                                {{ $parent->restaurant_id_unique ?: '#' . $parent->id }}
                                            </span>
                                            <span class="badge bg-dark text-white" style="font-size: 0.7rem;">
                                                Parent HQ
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-map-marker-alt me-1 text-danger opacity-75"></i> {{ $parent->address ?: 'Address not set' }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Group Financial & Order Stats -->
                                <div class="d-flex align-items-center gap-4 flex-wrap">
                                    <div class="text-end">
                                        <small class="text-muted text-uppercase d-block fw-bold" style="font-size: 0.7rem;">Group Invoice Value</small>
                                        <span class="fw-bold fs-5 text-dark font-monospace">₹{{ number_format($group['group_sales'], 2) }}</span>
                                    </div>
                                    <div class="text-end d-none d-sm-block">
                                        <small class="text-muted text-uppercase d-block fw-bold" style="font-size: 0.7rem;">Total Orders</small>
                                        <span class="fw-bold fs-5 text-primary font-monospace">{{ number_format($group['group_orders']) }}</span>
                                    </div>
                                    <div class="text-end d-none d-md-block">
                                        <small class="text-muted text-uppercase d-block fw-bold" style="font-size: 0.7rem;">Today's Orders</small>
                                        <span class="badge bg-success px-2 py-1 font-monospace" style="font-size: 0.8rem;">
                                            {{ number_format($group['today_orders'] + collect($group['sub_restaurants'])->sum('today_orders')) }} Today
                                        </span>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#subCollapse_{{ $parent->id }}" aria-expanded="true">
                                        <i class="fas fa-store-alt me-1 text-primary"></i>
                                        {{ $group['sub_count'] }} Sub-Outlets <i class="fas fa-chevron-down ms-1 small"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Performing Sub-Restaurants Drawer -->
                            <div class="collapse show" id="subCollapse_{{ $parent->id }}">
                                <div class="sub-restaurants-drawer">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.6px; font-size: 0.72rem;">
                                            <i class="fas fa-network-wired text-primary me-1"></i> Sub-Restaurant Branches (Ranked by actual performance)
                                        </span>
                                        <span class="badge bg-white text-muted border font-monospace" style="font-size: 0.7rem;">
                                            Parent Direct Sales: ₹{{ number_format($group['direct_sales'], 2) }} ({{ number_format($group['direct_orders']) }} orders)
                                        </span>
                                    </div>

                                    @if(count($group['sub_restaurants']) === 0)
                                        <div class="text-center py-3 bg-white rounded-3 border">
                                            <small class="text-muted">No child outlet branches registered yet under this parent restaurant.</small>
                                        </div>
                                    @else
                                        @foreach($group['sub_restaurants'] as $subIdx => $subItem)
                                            @php
                                                $sub = $subItem['restaurant'];
                                                $subRank = $subIdx + 1;
                                                $sharePct = $group['group_sales'] > 0 ? round(($subItem['sales'] / $group['group_sales']) * 100, 1) : 0;
                                            @endphp
                                            <div class="sub-item-row">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.72rem; min-width: 28px;">
                                                        #{{ $subRank }}
                                                    </span>
                                                    <div>
                                                        <strong class="text-dark d-block" style="font-size: 0.88rem;">{{ $sub->name }}</strong>
                                                        <small class="text-muted font-monospace" style="font-size: 0.74rem;">
                                                            {{ $sub->restaurant_id_unique ?: '#' . $sub->id }}
                                                            @if($sub->address) &bull; {{ \Illuminate\Support\Str::limit($sub->address, 30) }} @endif
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center gap-4">
                                                    <div class="text-end">
                                                        <small class="text-muted d-block" style="font-size: 0.68rem; text-transform: uppercase;">Actual Sales</small>
                                                        <span class="fw-bold text-dark font-monospace" style="font-size: 0.92rem;">
                                                            ₹{{ number_format($subItem['sales'], 2) }}
                                                        </span>
                                                        <small class="text-muted font-monospace" style="font-size: 0.68rem;">({{ $sharePct }}% group share)</small>
                                                    </div>

                                                    <div class="text-end d-none d-sm-block">
                                                        <small class="text-muted d-block" style="font-size: 0.68rem; text-transform: uppercase;">Total Orders</small>
                                                        <span class="fw-bold text-primary font-monospace" style="font-size: 0.92rem;">
                                                            {{ number_format($subItem['orders']) }}
                                                        </span>
                                                    </div>

                                                    <div class="text-end">
                                                        <small class="text-muted d-block" style="font-size: 0.68rem; text-transform: uppercase;">Today's Orders</small>
                                                        <span class="badge {{ $subItem['today_orders'] > 0 ? 'bg-success' : 'bg-light text-muted border' }}" style="font-size: 0.75rem;">
                                                            {{ $subItem['today_orders'] }} Today (₹{{ number_format($subItem['today_sales'], 0) }})
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">No parent restaurants to display.</div>
                    @endforelse
                </div>
            </div>

            <!-- 5. Top 10 Best Dishes Table -->
            <div class="dashboard-panel mb-4">
                <div class="dashboard-panel-header">
                    <div>
                        <h5 class="panel-title">
                            <i class="fas fa-fire text-danger"></i> Top 10 Best Performing Dishes
                        </h5>
                        <p class="panel-subtitle">Highest volume dishes and menu items across your mapped franchise network</p>
                    </div>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold">
                        <i class="fas fa-medal me-1"></i> Top Sellers
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 top-dishes-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Rank</th>
                                <th>Item / Dish</th>
                                <th>Menu Category</th>
                                <th>Base Price</th>
                                <th class="text-center">Orders Count</th>
                                <th class="text-center">Quantity Sold</th>
                                <th class="text-end">Total Revenue Generated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topDishes as $dishIdx => $dish)
                                @php
                                    $rank = $dishIdx + 1;
                                    $rankBadgeStyle = $rank === 1 ? 'background: #fef3c7; color: #b45309; border: 1px solid #fde68a;' :
                                                     ($rank === 2 ? 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;' :
                                                     ($rank === 3 ? 'background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa;' :
                                                                   'background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0;'));
                                    $isVeg = strtoupper($dish->food_type ?? '') === 'VEG';
                                @endphp
                                <tr>
                                    <td>
                                        <span class="dish-rank-badge" style="{{ $rankBadgeStyle }}">
                                            #{{ $rank }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($dish->dish_image && file_exists(public_path('uploads/products/' . $dish->dish_image)))
                                                <img src="{{ asset('uploads/products/' . $dish->dish_image) }}" alt="{{ $dish->dish_name }}" class="dish-img-thumb">
                                            @else
                                                <div class="dish-img-thumb">
                                                    <i class="fas fa-utensils"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge border {{ $isVeg ? 'text-success border-success' : 'text-danger border-danger' }} p-1" style="font-size: 0.65rem;" title="{{ $dish->food_type }}">
                                                        <i class="fas fa-circle"></i>
                                                    </span>
                                                    <strong class="text-dark">{{ $dish->dish_name }}</strong>
                                                </div>
                                                <small class="text-muted">Item ID: #{{ $dish->subcategory_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border fw-semibold">
                                            {{ $dish->category_name ?: 'General' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-semibold">₹{{ number_format($dish->base_price, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                            {{ number_format($dish->total_orders_count) }} orders
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold fs-6 text-primary font-monospace">
                                            {{ number_format($dish->total_qty) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-dark font-monospace fs-6">
                                            ₹{{ number_format($dish->total_revenue, 2) }}
                                        </strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No ordered dishes recorded yet across mapped restaurants.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @endif

    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if($mappedSponsorsCount > 0)
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Sales & Orders Dual Axis Chart
        const trajectoryCtx = document.getElementById('salesTrajectoryChart');
        if (trajectoryCtx) {
            new Chart(trajectoryCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartDays) !!},
                    datasets: [
                        {
                            label: 'Daily Sales (₹)',
                            data: {!! json_encode($chartSales) !!},
                            borderColor: '#ff6a00',
                            backgroundColor: 'rgba(255, 106, 0, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            yAxisID: 'ySales',
                            pointBackgroundColor: '#ff6a00',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Daily Orders (Qty)',
                            data: {!! json_encode($chartOrders) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'transparent',
                            borderWidth: 2.5,
                            borderDash: [5, 5],
                            tension: 0.35,
                            yAxisID: 'yOrders',
                            pointBackgroundColor: '#3b82f6',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.yAxisID === 'ySales') {
                                        return ' Sales: ₹' + Number(context.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 2});
                                    } else {
                                        return ' Orders: ' + context.parsed.y;
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        ySales: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: { color: 'rgba(226, 232, 240, 0.6)' },
                            ticks: {
                                callback: value => '₹' + Number(value).toLocaleString()
                            }
                        },
                        yOrders: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // 2. Parent vs Sub-Outlets Donut Chart
        const networkCtx = document.getElementById('networkShareChart');
        if (networkCtx) {
            new Chart(networkCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Parent HQs', 'Sub-Outlets'],
                    datasets: [{
                        data: [
                            {{ round($parentTotalInvoiceValue, 2) }},
                            {{ round($subTotalInvoiceValue, 2) }}
                        ],
                        backgroundColor: ['#ff6a00', '#6366f1'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = Number(context.parsed).toLocaleString('en-IN', {minimumFractionDigits: 2});
                                    return ' ' + context.label + ': ₹' + val;
                                }
                            }
                        }
                    },
                    cutout: '72%'
                }
            });
        }
    });
</script>
@endif
@endsection

