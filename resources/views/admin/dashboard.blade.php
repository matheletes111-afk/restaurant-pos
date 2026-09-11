@extends('layouts.app')

@section('title')
<title>Admin || Executive Dashboard</title>
@endsection

@section('style')
@include('includes.style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --dash-primary: #ff6a00;
        --dash-primary-dark: #e55a00;
        --dash-dark: #0f172a;
        --dash-card-bg: #ffffff;
        --dash-border: #f1f5f9;
        --dash-text-muted: #64748b;
    }

    /* Welcome Hero Card */
    .welcome-card-custom {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e1b4b 100%);
        color: white;
        border-radius: 20px;
        padding: 35px 35px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.12);
        position: relative;
        overflow: hidden;
        margin-bottom: 25px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .welcome-card-custom::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -5%;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(255, 106, 0, 0.22), transparent 70%);
        border-radius: 50%;
        z-index: 1;
        pointer-events: none;
    }
    .welcome-card-custom::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 30%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15), transparent 70%);
        border-radius: 50%;
        z-index: 1;
        pointer-events: none;
    }
    .welcome-title {
        font-size: 1.85rem;
        font-weight: 850;
        margin-bottom: 8px;
        z-index: 2;
        position: relative;
        letter-spacing: -0.5px;
        color: #ffffff;
    }
    .welcome-subtitle {
        font-size: 0.95rem;
        z-index: 2;
        position: relative;
        font-weight: 400;
        color: #cbd5e1;
        margin: 0;
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        font-size: 0.78rem;
        font-weight: 600;
        color: #f8fafc;
        border: 1px solid rgba(255, 255, 255, 0.15);
        margin-bottom: 12px;
        z-index: 2;
        position: relative;
    }

    /* Metric Cards */
    .metric-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 22px 22px 18px 22px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid #f1f5f9;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
        border-color: rgba(255, 106, 0, 0.3);
    }
    .metric-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .metric-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .metric-label {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .metric-value {
        font-size: 1.85rem;
        font-weight: 850;
        color: #0f172a;
        line-height: 1.15;
        letter-spacing: -0.5px;
    }
    .metric-value-sm {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .metric-footer {
        margin-top: 14px;
        padding-top: 10px;
        border-top: 1px dashed #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8rem;
    }

    /* Gradient Palettes for Metric Cards */
    .icon-primary { background: linear-gradient(135deg, #fff7ed, #ffedd5); color: #ea580c; }
    .icon-success { background: linear-gradient(135deg, #ecfdf5, #d1fae5); color: #059669; }
    .icon-warning { background: linear-gradient(135deg, #fffbeb, #fef3c7); color: #d97706; }
    .icon-danger  { background: linear-gradient(135deg, #fff1f2, #ffe4e6); color: #e11d48; }
    .icon-indigo  { background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: #4f46e5; }
    .icon-purple  { background: linear-gradient(135deg, #faf5ff, #f3e8ff); color: #9333ea; }
    .icon-cyan    { background: linear-gradient(135deg, #ecfeff, #cffafe); color: #0891b2; }

    /* CRM Status Card Styles */
    .crm-stat-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 18px 14px 18px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }
    .crm-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }
    .crm-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }
    .crm-stat-number {
        font-size: 1.6rem;
        font-weight: 850;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }
    .crm-stat-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 2px;
    }

    /* Pills & Badges */
    .pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .pill-success { background: #dcfce7; color: #15803d; }
    .pill-warning { background: #fef3c7; color: #b45309; }
    .pill-danger  { background: #fee2e2; color: #b91c1c; }
    .pill-indigo  { background: #e0e7ff; color: #4338ca; }
    .pill-purple  { background: #f3e8ff; color: #7e22ce; }
    .pill-cyan    { background: #cffafe; color: #0e7490; }
    .pill-muted   { background: #f1f5f9; color: #475569; }

    /* Chart & Widget Container Cards */
    .widget-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 25px;
        overflow: hidden;
    }
    .widget-header {
        padding: 22px 25px;
        border-bottom: 1px solid #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .widget-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .widget-subtitle {
        font-size: 0.82rem;
        color: #64748b;
        margin: 4px 0 0 0;
    }
    .widget-body {
        padding: 24px 25px;
    }

    /* Year Selector */
    .year-select-custom {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        color: #0f172a;
        font-size: 0.85rem;
        font-weight: 700;
        border-radius: 10px;
        padding: 6px 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        outline: none;
    }
    .year-select-custom:hover, .year-select-custom:focus {
        border-color: #ff6a00;
        background: #ffffff;
    }

    /* Summary Metric Pill in Chart Header */
    .chart-metric-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 6px 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .chart-metric-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    /* Leaderboard & Watchlist Table */
    .custom-table {
        width: 100%;
        margin-bottom: 0;
    }
    .custom-table th {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 14px;
        background: #f8fafc;
    }
    .custom-table td {
        padding: 14px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.86rem;
        color: #1e293b;
    }
    .custom-table tr:last-child td {
        border-bottom: none;
    }
    .custom-table tr:hover td {
        background-color: #fafafa;
    }

    /* Rank Badge */
    .rank-badge {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.8rem;
    }
    .rank-1 { background: #fef3c7; color: #b45309; }
    .rank-2 { background: #f1f5f9; color: #475569; }
    .rank-3 { background: #ffedd5; color: #c2410c; }
    .rank-normal { background: #f8fafc; color: #94a3b8; }

    /* Quote Card */
    .quote-card-custom {
        border: none;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .quote-icon {
        font-size: 2.2rem;
        color: #ff6a00;
        opacity: 0.12;
        position: absolute;
        top: 20px;
        right: 20px;
    }
    .quote-text {
        font-size: 1.05rem;
        font-style: italic;
        color: #334155;
        font-weight: 600;
        line-height: 1.6;
        margin: 0;
    }

    /* Shortcut Card styling */
    .shortcut-card {
        border: none;
        border-radius: 16px;
        background: #fff;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        text-decoration: none !important;
    }
    .shortcut-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.06) !important;
        border-color: rgba(255, 106, 0, 0.25) !important;
    }
    .shortcut-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .shortcut-card:hover .shortcut-icon {
        transform: scale(1.08);
    }
    .shortcut-title {
        margin: 0 0 3px 0;
        font-weight: 700;
        color: #1e293b;
        font-size: 0.98rem;
    }
    .shortcut-desc {
        color: #64748b;
        font-size: 0.8rem;
        margin: 0;
    }

    .plan-stat-item {
        padding: 12px 14px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }
    .plan-stat-item:hover {
        background: #ffffff;
        border-color: #e2e8f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')
<div class="pc-container">
    <div class="pc-content">

        <!-- Breadcrumb -->
        <div class="page-header mb-4">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="page-header-title">
                            <h4 class="m-b-5" style="font-weight: 800; color: #0f172a;">Executive SaaS Dashboard</h4>
                        </div>
                        <ul class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Platform Overview & Metrics</li>
                        </ul>
                    </div>
                    <div class="col-md-4 text-md-right text-left mt-2 mt-md-0">
                        <span class="badge bg-white text-dark border px-3 py-2 shadow-sm" style="font-size: 0.82rem; font-weight: 600; border-radius: 8px;">
                            <i class="fas fa-calendar-day text-primary me-1"></i> {{ date('l, d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb -->

        @php
          $saUser = auth()->user();
          $saPerms = $saUser->permissions ?? [];
        @endphp

        <!-- Top Hero Row -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="welcome-card-custom">
                    <span class="hero-badge">
                        <i class="fas fa-bolt text-warning"></i> Super Admin Control Center
                    </span>
                    <h1 class="welcome-title">Hi {{ $saUser->name }}, welcome back!</h1>
                    <p class="welcome-subtitle">Here is real-time performance, restaurant onboarding health, CRM leads funnel, and subscription analytics for your platform.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="quote-card-custom">
                    <div style="height: 4px; background: linear-gradient(90deg, #ff6a00, #ff8c42);"></div>
                    <div class="card-body p-4 position-relative">
                        <i class="fas fa-quote-right quote-icon"></i>
                        <span style="color: #64748b; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px; display: block; margin-bottom: 8px;">Daily Inspiration</span>
                        <blockquote style="margin: 0; position: relative; z-index: 2;">
                            <p class="quote-text">"{{ $quote }}"</p>
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: Top 4 Primary Metric Cards -->
        <div class="row g-3 mb-4">
            <!-- 1. Current Restaurant Count -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="metric-card">
                    <div>
                        <div class="metric-top">
                            <div>
                                <div class="metric-label">Current Restaurants</div>
                                <div class="metric-value">{{ number_format($totalRestaurants) }}</div>
                            </div>
                            <div class="metric-icon-box icon-primary">
                                <i class="fas fa-utensils"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="pill-badge pill-success"><i class="fas fa-check"></i> {{ $activeRestaurants }} Active</span>
                            @if($inactiveRestaurants > 0)
                                <span class="pill-badge pill-muted">{{ $inactiveRestaurants }} Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="metric-footer">
                        <span class="text-muted">Total registered restaurants</span>
                        <a href="{{ route('manage.restaurant') }}" class="text-primary font-weight-bold" style="text-decoration: none;">View All &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- 2. With Plans Restaurant Count -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="metric-card">
                    <div>
                        <div class="metric-top">
                            <div>
                                <div class="metric-label">With Active Plans</div>
                                <div class="metric-value text-success">{{ number_format($withPlanCount) }}</div>
                            </div>
                            <div class="metric-icon-box icon-success">
                                <i class="fas fa-shield-check"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="pill-badge pill-success">
                                <i class="fas fa-arrow-trend-up"></i> 
                                {{ $totalRestaurants > 0 ? round(($withPlanCount / $totalRestaurants) * 100, 1) : 0 }}% Subscribed
                            </span>
                        </div>
                    </div>
                    <div class="metric-footer">
                        <span class="text-muted">Active subscribed partners</span>
                        <a href="{{ route('manage.restaurant') }}?plan_id=all" class="text-success font-weight-bold" style="text-decoration: none;">Explore &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- 3. Restaurant With No Plans -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="metric-card">
                    <div>
                        <div class="metric-top">
                            <div>
                                <div class="metric-label">With No Active Plans</div>
                                <div class="metric-value text-warning">{{ number_format($withoutPlanCount) }}</div>
                            </div>
                            <div class="metric-icon-box icon-warning">
                                <i class="fas fa-store-slash"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="pill-badge pill-warning">
                                <i class="fas fa-user-clock"></i> Unsubscribed / Expired
                            </span>
                        </div>
                    </div>
                    <div class="metric-footer">
                        <span class="text-muted">Conversion opportunities</span>
                        <a href="{{ route('manage.restaurant') }}?plan_id=none" class="text-warning font-weight-bold" style="text-decoration: none;">Convert &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- 4. 30 Days Nearby Expiry Count -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="metric-card">
                    <div>
                        <div class="metric-top">
                            <div>
                                <div class="metric-label">30-Day Expiry Watch</div>
                                <div class="metric-value {{ $expiringSoonCount > 0 ? 'text-danger' : 'text-dark' }}">{{ number_format($expiringSoonCount) }}</div>
                            </div>
                            <div class="metric-icon-box icon-danger">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            @if($expiringSoonCount > 0)
                                <span class="pill-badge pill-danger"><i class="fas fa-exclamation-triangle"></i> Renewals Needed</span>
                            @else
                                <span class="pill-badge pill-success"><i class="fas fa-check-circle"></i> All Plans Healthy</span>
                            @endif
                        </div>
                    </div>
                    <div class="metric-footer">
                        <span class="text-muted">Expiring in next 30 days</span>
                        <a href="#expiryWatchlistSection" class="text-danger font-weight-bold" style="text-decoration: none;">View List &darr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Commercial & Operational Highlights (Today's Orders, Popular Package, Best Restaurant) -->
        <div class="row g-3 mb-4">
            <!-- 5. Total Order Made By Restaurant Today -->
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="metric-card">
                    <div>
                        <div class="metric-top">
                            <div>
                                <div class="metric-label">Total Orders Today</div>
                                <div class="metric-value text-indigo">{{ number_format($todayOrdersCount) }}</div>
                            </div>
                            <div class="metric-icon-box icon-indigo">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 mt-1">
                            <span class="text-dark font-weight-bold" style="font-size: 1rem;">
                                ₹{{ number_format($todayOrdersAmount, 2) }} <small class="text-muted font-weight-normal">Gross Value</small>
                            </span>
                            <span class="text-muted" style="font-size: 0.8rem;">
                                Active across <strong class="text-dark">{{ $todayActiveRestaurantsCount }}</strong> restaurants today
                            </span>
                        </div>
                    </div>
                    <div class="metric-footer">
                        <span class="text-muted">Today's Restaurant Activity</span>
                        <span class="pill-badge pill-indigo"><i class="fas fa-clock"></i> Live Today</span>
                    </div>
                </div>
            </div>

            <!-- 6. Popular Package -->
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="metric-card">
                    <div>
                        <div class="metric-top">
                            <div>
                                <div class="metric-label">Most Popular Package</div>
                                <div class="metric-value-sm text-truncate" style="max-width: 220px;" title="{{ $popularPackage ? $popularPackage->name : 'None' }}">
                                    {{ $popularPackage ? $popularPackage->name : 'None Created' }}
                                </div>
                            </div>
                            <div class="metric-icon-box icon-purple">
                                <i class="fas fa-crown"></i>
                            </div>
                        </div>
                        @if($popularPackage)
                        <div class="d-flex flex-column gap-1 mt-1">
                            <span class="text-dark font-weight-bold" style="font-size: 1rem;">
                                ₹{{ number_format($popularPackage->price, 0) }} <small class="text-muted font-weight-normal">/ {{ ucfirst($popularPackage->billing_cycle ?? 'Month') }}</small>
                            </span>
                            <span class="text-muted" style="font-size: 0.8rem;">
                                <strong class="text-purple">{{ $popularPackage->subscriptions_count }}</strong> total subscriptions taken
                            </span>
                        </div>
                        @else
                        <p class="text-muted mb-0" style="font-size: 0.82rem;">No subscription plans registered yet.</p>
                        @endif
                    </div>
                    <div class="metric-footer">
                        <span class="text-muted">Top Choice Plan</span>
                        <a href="{{ route('plans.index') }}" class="font-weight-bold" style="color: #9333ea; text-decoration: none;">Manage Plans &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- 7. Best Restaurant (maximum no orders) -->
            <div class="col-xl-4 col-md-12 mb-3">
                <div class="metric-card">
                    <div>
                        <div class="metric-top">
                            <div>
                                <div class="metric-label">Best Performing Restaurant</div>
                                <div class="metric-value-sm text-truncate" style="max-width: 220px;" title="{{ $bestRestaurant ? $bestRestaurant->name : 'None' }}">
                                    {{ $bestRestaurant ? $bestRestaurant->name : 'No Orders Yet' }}
                                </div>
                            </div>
                            <div class="metric-icon-box icon-cyan">
                                <i class="fas fa-trophy"></i>
                            </div>
                        </div>
                        @if($bestRestaurant)
                        <div class="d-flex flex-column gap-1 mt-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border font-weight-bold">
                                    {{ $bestRestaurant->restaurant_id_unique ?? ('ID #'.$bestRestaurant->id) }}
                                </span>
                                <span class="text-dark font-weight-bold" style="font-size: 0.95rem;">
                                    {{ number_format($bestRestaurant->orders_count) }} <small class="text-muted">Orders</small>
                                </span>
                            </div>
                            <span class="text-muted" style="font-size: 0.8rem;">
                                Lifetime Volume: <strong class="text-dark">₹{{ number_format($bestRestaurant->orders_sum_grand_total ?? 0, 2) }}</strong>
                            </span>
                        </div>
                        @else
                        <p class="text-muted mb-0" style="font-size: 0.82rem;">No restaurant orders recorded yet.</p>
                        @endif
                    </div>
                    <div class="metric-footer">
                        <span class="text-muted">Maximum Order Volume</span>
                        <span class="pill-badge pill-success"><i class="fas fa-star"></i> Rank #1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: CRM Sales Pipeline & Status Breakdown -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="widget-card">
                    <div class="widget-header">
                        <div>
                            <h4 class="widget-title">
                                <i class="fas fa-address-book text-indigo"></i>
                                CRM Lead Pipeline & Status Overview
                            </h4>
                            <p class="widget-subtitle">Real-time status breakdown of prospect demo requests and sales pipeline conversion stages</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($crmStats['followups_pending'] > 0)
                                <span class="pill-badge pill-warning px-3 py-2" style="font-size: 0.78rem;">
                                    <i class="fas fa-bell"></i> {{ $crmStats['followups_pending'] }} Follow-ups Due
                                </span>
                            @endif
                            <a href="{{ route('admin.crm.index') }}" class="btn btn-sm btn-primary px-3" style="border-radius: 8px; font-weight: 700;">
                                Manage Leads in CRM &rarr;
                            </a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <!-- CRM Status Metric Cards Grid -->
                        <div class="row g-3">
                            <!-- Total Leads -->
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="crm-stat-card" style="border-top: 3px solid #0f172a;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="crm-stat-title text-dark">Total Leads</span>
                                        <div class="crm-stat-icon" style="background: #f1f5f9; color: #0f172a;">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="crm-stat-number">{{ number_format($crmStats['total']) }}</div>
                                    <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                                        All Inbound Prospects
                                    </div>
                                </div>
                            </div>

                            <!-- Contacted -->
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="crm-stat-card" style="border-top: 3px solid #0284c7;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="crm-stat-title" style="color: #0284c7;">Contacted</span>
                                        <div class="crm-stat-icon" style="background: #e0f2fe; color: #0284c7;">
                                            <i class="fas fa-phone-volume"></i>
                                        </div>
                                    </div>
                                    <div class="crm-stat-number" style="color: #0284c7;">{{ number_format($crmStats['contacted']) }}</div>
                                    <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                                        {{ $crmStats['total'] > 0 ? round(($crmStats['contacted'] / $crmStats['total']) * 100, 1) : 0 }}% of pipeline
                                    </div>
                                </div>
                            </div>

                            <!-- Qualified -->
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="crm-stat-card" style="border-top: 3px solid #7c3aed;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="crm-stat-title" style="color: #7c3aed;">Qualified</span>
                                        <div class="crm-stat-icon" style="background: #faf5ff; color: #7c3aed;">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                    </div>
                                    <div class="crm-stat-number" style="color: #7c3aed;">{{ number_format($crmStats['qualified']) }}</div>
                                    <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                                        {{ $crmStats['total'] > 0 ? round(($crmStats['qualified'] / $crmStats['total']) * 100, 1) : 0 }}% of pipeline
                                    </div>
                                </div>
                            </div>

                            <!-- Nurturing -->
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="crm-stat-card" style="border-top: 3px solid #d97706;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="crm-stat-title" style="color: #d97706;">Nurturing</span>
                                        <div class="crm-stat-icon" style="background: #fef3c7; color: #d97706;">
                                            <i class="fas fa-comments"></i>
                                        </div>
                                    </div>
                                    <div class="crm-stat-number" style="color: #d97706;">{{ number_format($crmStats['nurturing']) }}</div>
                                    <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                                        {{ $crmStats['total'] > 0 ? round(($crmStats['nurturing'] / $crmStats['total']) * 100, 1) : 0 }}% of pipeline
                                    </div>
                                </div>
                            </div>

                            <!-- Converted -->
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="crm-stat-card" style="border-top: 3px solid #059669;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="crm-stat-title text-success">Converted</span>
                                        <div class="crm-stat-icon" style="background: #ecfdf5; color: #059669;">
                                            <i class="fas fa-trophy"></i>
                                        </div>
                                    </div>
                                    <div class="crm-stat-number text-success">{{ number_format($crmStats['converted']) }}</div>
                                    <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                                        {{ $crmStats['total'] > 0 ? round(($crmStats['converted'] / $crmStats['total']) * 100, 1) : 0 }}% won deals
                                    </div>
                                </div>
                            </div>

                            <!-- Lost -->
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="crm-stat-card" style="border-top: 3px solid #e11d48;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="crm-stat-title text-danger">Lost</span>
                                        <div class="crm-stat-icon" style="background: #fff1f2; color: #e11d48;">
                                            <i class="fas fa-circle-xmark"></i>
                                        </div>
                                    </div>
                                    <div class="crm-stat-number text-danger">{{ number_format($crmStats['lost']) }}</div>
                                    <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                                        {{ $crmStats['total'] > 0 ? round(($crmStats['lost'] / $crmStats['total']) * 100, 1) : 0 }}% dropped
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Multi-Segment Pipeline Distribution Bar -->
                        @if($crmStats['total'] > 0)
                        @php
                            $total = $crmStats['total'];
                            $pContacted = ($crmStats['contacted'] / $total) * 100;
                            $pQualified = ($crmStats['qualified'] / $total) * 100;
                            $pNurturing = ($crmStats['nurturing'] / $total) * 100;
                            $pConverted = ($crmStats['converted'] / $total) * 100;
                            $pLost      = ($crmStats['lost'] / $total) * 100;
                        @endphp
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted font-weight-bold" style="text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.6px;">
                                    Pipeline Funnel Distribution
                                </small>
                                <span class="badge pill-success px-2 py-1" style="font-size: 0.72rem;">
                                    <i class="fas fa-bullseye"></i> {{ round($pConverted, 1) }}% Conversion Win Rate
                                </span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px; overflow: hidden; background: #f1f5f9;">
                                @if($pContacted > 0)
                                    <div class="progress-bar" role="progressbar" style="width: {{ $pContacted }}%; background: #0284c7;" title="Contacted: {{ $crmStats['contacted'] }}" aria-valuenow="{{ $pContacted }}" aria-valuemin="0" aria-valuemax="100"></div>
                                @endif
                                @if($pQualified > 0)
                                    <div class="progress-bar" role="progressbar" style="width: {{ $pQualified }}%; background: #7c3aed;" title="Qualified: {{ $crmStats['qualified'] }}" aria-valuenow="{{ $pQualified }}" aria-valuemin="0" aria-valuemax="100"></div>
                                @endif
                                @if($pNurturing > 0)
                                    <div class="progress-bar" role="progressbar" style="width: {{ $pNurturing }}%; background: #d97706;" title="Nurturing: {{ $crmStats['nurturing'] }}" aria-valuenow="{{ $pNurturing }}" aria-valuemin="0" aria-valuemax="100"></div>
                                @endif
                                @if($pConverted > 0)
                                    <div class="progress-bar" role="progressbar" style="width: {{ $pConverted }}%; background: #059669;" title="Converted: {{ $crmStats['converted'] }}" aria-valuenow="{{ $pConverted }}" aria-valuemin="0" aria-valuemax="100"></div>
                                @endif
                                @if($pLost > 0)
                                    <div class="progress-bar" role="progressbar" style="width: {{ $pLost }}%; background: #e11d48;" title="Lost: {{ $crmStats['lost'] }}" aria-valuenow="{{ $pLost }}" aria-valuemin="0" aria-valuemax="100"></div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Interactive Yearly Graph (Monthly Registrations vs Monthly Subscriptions Taken) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="widget-card">
                    <div class="widget-header">
                        <div>
                            <h4 class="widget-title">
                                <i class="fas fa-chart-line text-primary"></i>
                                Yearly Platform Growth: Registrations vs Subscriptions Taken
                            </h4>
                            <p class="widget-subtitle">Month-by-month comparative analysis of new restaurant registrations vs subscription plans taken</p>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <!-- Summary Counters for Selected Year -->
                            <div class="chart-metric-pill d-none d-md-flex">
                                <div class="chart-metric-dot" style="background: #4f46e5;"></div>
                                <span style="font-size: 0.8rem; color: #64748b;">Registrations:</span>
                                <strong id="chartTotalRegs" class="text-dark" style="font-size: 0.85rem;">{{ array_sum($monthlyRegistrations) }}</strong>
                            </div>
                            <div class="chart-metric-pill d-none d-md-flex">
                                <div class="chart-metric-dot" style="background: #10b981;"></div>
                                <span style="font-size: 0.8rem; color: #64748b;">Subscriptions:</span>
                                <strong id="chartTotalSubs" class="text-dark" style="font-size: 0.85rem;">{{ array_sum($monthlySubscriptions) }}</strong>
                            </div>

                            <!-- Year Selector Dropdown -->
                            <div class="d-flex align-items-center gap-2 ms-md-2">
                                <label for="chartYearSelect" class="mb-0 text-muted" style="font-size: 0.82rem; font-weight: 700;">Year:</label>
                                <select id="chartYearSelect" class="year-select-custom">
                                    @foreach($availableYears as $y)
                                        <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div style="position: relative; height: 350px; width: 100%;">
                            <canvas id="yearlyGrowthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Detailed Breakdown Grid (Top Performing Restaurants & 30-Day Expiry Watchlist) -->
        <div class="row mb-4">
            <!-- Best Performing Restaurants Leaderboard -->
            <div class="col-lg-7 mb-4">
                <div class="widget-card h-100">
                    <div class="widget-header">
                        <div>
                            <h4 class="widget-title">
                                <i class="fas fa-medal text-warning"></i>
                                Top Performing Restaurants
                            </h4>
                            <p class="widget-subtitle">Leading restaurants ranked by total orders processed</p>
                        </div>
                        <a href="{{ route('manage.restaurant') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-weight: 600;">
                            View All Restaurants
                        </a>
                    </div>
                    <div class="widget-body p-0">
                        <div class="table-responsive">
                            <table class="custom-table table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">Rank</th>
                                        <th>Restaurant</th>
                                        <th>Owner</th>
                                        <th>Active Plan</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-right">Total Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topRestaurants as $index => $rest)
                                    <tr>
                                        <td>
                                            <span class="rank-badge {{ $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : ($index == 2 ? 'rank-3' : 'rank-normal')) }}">
                                                #{{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #0f172a;">{{ $rest->name }}</div>
                                            <small class="text-muted">{{ $rest->restaurant_id_unique ?? ('ID #'.$rest->id) }}</small>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.85rem; font-weight: 600;">{{ $rest->owner->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $rest->owner->email ?? '' }}</small>
                                        </td>
                                        <td>
                                            @if($rest->active_subscription && $rest->active_subscription->plan)
                                                <span class="badge bg-success-light text-success border border-success px-2 py-1" style="font-size: 0.72rem; border-radius: 6px;">
                                                    {{ $rest->active_subscription->plan->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.72rem; border-radius: 6px;">
                                                    No Active Plan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                                {{ number_format($rest->orders_count) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <span class="font-weight-bold text-success" style="font-size: 0.92rem;">
                                                ₹{{ number_format($rest->orders_sum_grand_total ?? 0, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                                            No order history recorded yet across restaurants.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 30-Day Expiry Watchlist -->
            <div class="col-lg-5 mb-4" id="expiryWatchlistSection">
                <div class="widget-card h-100">
                    <div class="widget-header">
                        <div>
                            <h4 class="widget-title">
                                <i class="fas fa-clock text-danger"></i>
                                30-Day Expiry Watchlist
                            </h4>
                            <p class="widget-subtitle">Subscriptions requiring upcoming renewal attention</p>
                        </div>
                        <span class="badge {{ $expiringSoonCount > 0 ? 'bg-danger text-white' : 'bg-light text-muted' }} px-2 py-1" style="border-radius: 6px;">
                            {{ $expiringSoonCount }} Expiring
                        </span>
                    </div>
                    <div class="widget-body p-0">
                        @if($expiringSoonRestaurants->count() > 0)
                        <div class="table-responsive">
                            <table class="custom-table table">
                                <thead>
                                    <tr>
                                        <th>Restaurant</th>
                                        <th>Plan & Expiry</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringSoonRestaurants as $expRest)
                                    @php
                                        $sub = $expRest->active_subscription;
                                        $endDate = $sub && $sub->end_date ? \Carbon\Carbon::parse($sub->end_date) : null;
                                        $daysLeft = $endDate ? (int) max(0, \Carbon\Carbon::now()->diffInDays($endDate, false)) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; color: #0f172a;">{{ $expRest->name }}</div>
                                            <small class="text-muted">{{ $expRest->owner->name ?? 'Owner' }} ({{ $expRest->owner->phone ?? 'N/A' }})</small>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; font-size: 0.82rem; color: #475569;">
                                                {{ $sub->plan->name ?? 'Custom Plan' }}
                                            </div>
                                            <span class="pill-badge {{ $daysLeft <= 7 ? 'pill-danger' : 'pill-warning' }}">
                                                <i class="fas fa-hourglass-end"></i> {{ $daysLeft }} days left ({{ $endDate ? $endDate->format('d M') : '' }})
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('manage.restaurant.show.plans', $expRest->id) }}" class="btn btn-sm btn-light text-primary border" style="font-size: 0.75rem; border-radius: 6px; font-weight: 700;">
                                                Renew
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="p-4 text-center text-muted">
                            <div class="mb-2" style="font-size: 2.2rem; color: #10b981;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h6 style="font-weight: 700; color: #1e293b;">No Immediate Expiries</h6>
                            <p class="mb-0" style="font-size: 0.82rem;">All active subscriptions are valid beyond the next 30 days.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Package Popularity Distribution & Breakdown -->
        @if($topPackages->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="widget-card">
                    <div class="widget-header">
                        <div>
                            <h4 class="widget-title">
                                <i class="fas fa-layer-group text-purple"></i>
                                Package Popularity & Subscription Breakdown
                            </h4>
                            <p class="widget-subtitle">Overview of subscription tiers and total restaurant adoption</p>
                        </div>
                        <a href="{{ route('plans.index') }}" class="text-purple font-weight-bold" style="font-size: 0.85rem; text-decoration: none;">
                            Configure Plans &rarr;
                        </a>
                    </div>
                    <div class="widget-body">
                        <div class="row g-3">
                            @foreach($topPackages as $pkg)
                            <div class="col-xl-3 col-md-6 mb-2">
                                <div class="plan-stat-item">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <strong style="color: #0f172a; font-size: 0.95rem;">{{ $pkg->name }}</strong>
                                        <span class="badge bg-purple-light text-purple border px-2 py-1" style="font-size: 0.72rem; border-radius: 6px; background: #faf5ff; color: #9333ea; border-color: #e9d5ff;">
                                            ₹{{ number_format($pkg->price, 0) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="text-muted" style="font-size: 0.8rem;">Total Subscribed:</span>
                                        <strong class="text-dark" style="font-size: 0.9rem;">{{ $pkg->subscriptions_count }}</strong>
                                    </div>
                                    <div class="progress mt-2" style="height: 6px; border-radius: 3px; background: #f1f5f9;">
                                        @php
                                            $totalAllSubs = max(1, $topPackages->sum('subscriptions_count'));
                                            $pct = round(($pkg->subscriptions_count / $totalAllSubs) * 100, 1);
                                        @endphp
                                        <div class="progress-bar bg-purple" role="progressbar" style="width: {{ $pct }}%; background: #9333ea;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted d-block text-right mt-1" style="font-size: 0.72rem;">{{ $pct }}% share</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Section 7: Quick Access Action Hub -->
        <div class="row mt-2">
            <div class="col-md-12 mb-3">
                <h4 style="font-weight: 800; color: #0f172a; font-size: 1.2rem; letter-spacing: -0.3px;">Quick Access Menu</h4>
            </div>
            
            @if($saUser->id == 1 || in_array('restaurant_master', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{ route('manage.restaurant') }}" class="shortcut-card">
                    <div class="shortcut-icon" style="background: #fff0e6; color: #ff6a00;">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div>
                        <h5 class="shortcut-title">Restaurant Master</h5>
                        <p class="shortcut-desc">Manage restaurants, owners & custom plans</p>
                    </div>
                </a>
            </div>
            @endif

            @if($saUser->id == 1 || in_array('plan_master', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{ route('plans.index') }}" class="shortcut-card">
                    <div class="shortcut-icon" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <h5 class="shortcut-title">Plan Master</h5>
                        <p class="shortcut-desc">Configure subscription plans & features</p>
                    </div>
                </a>
            </div>
            @endif

            @if($saUser->id == 1 || in_array('payment_history', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{ route('admin.payment.history') }}" class="shortcut-card">
                    <div class="shortcut-icon" style="background: #ecfdf5; color: #059669;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h5 class="shortcut-title">Payment History</h5>
                        <p class="shortcut-desc">View payments, Razorpay logs & invoices</p>
                    </div>
                </a>
            </div>
            @endif

            @if($saUser->id == 1 || in_array('admin_crm', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{ route('admin.crm.index') }}" class="shortcut-card">
                    <div class="shortcut-icon" style="background: #faf5ff; color: #7c3aed;">
                        <i class="fas fa-address-book"></i>
                    </div>
                    <div>
                        <h5 class="shortcut-title">Admin CRM</h5>
                        <p class="shortcut-desc">Track sales leads & interaction tasks</p>
                    </div>
                </a>
            </div>
            @endif

            @if($saUser->id == 1 || in_array('customer_support', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{ route('admin.support.tickets') }}" class="shortcut-card">
                    <div class="shortcut-icon" style="background: #fff1f2; color: #e11d48;">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h5 class="shortcut-title">Customer Support</h5>
                        <p class="shortcut-desc">Resolve user support requests & tickets</p>
                    </div>
                </a>
            </div>
            @endif

            @if($saUser->id == 1 || in_array('admin_user_management', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{ route('admin.users.index') }}" class="shortcut-card">
                    <div class="shortcut-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <h5 class="shortcut-title">Admin Users</h5>
                        <p class="shortcut-desc">Configure sub-admins & permissions</p>
                    </div>
                </a>
            </div>
            @endif
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@include('includes.script')

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById("yearlyGrowthChart").getContext("2d");
        
        // Initial data passed from backend
        let chartLabels = {!! json_encode($months) !!};
        let registrationsData = {!! json_encode($monthlyRegistrations) !!};
        let subscriptionsData = {!! json_encode($monthlySubscriptions) !!};

        // Create gradient fills
        const regGradient = ctx.createLinearGradient(0, 0, 0, 300);
        regGradient.addColorStop(0, "rgba(79, 70, 229, 0.28)");
        regGradient.addColorStop(1, "rgba(79, 70, 229, 0.0)");

        const subGradient = ctx.createLinearGradient(0, 0, 0, 300);
        subGradient.addColorStop(0, "rgba(16, 185, 129, 0.28)");
        subGradient.addColorStop(1, "rgba(16, 185, 129, 0.0)");

        const chartConfig = {
            type: "line",
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: "Monthly Restaurant Registrations",
                        data: registrationsData,
                        borderColor: "#4f46e5",
                        backgroundColor: regGradient,
                        borderWidth: 3,
                        pointBackgroundColor: "#ffffff",
                        pointBorderColor: "#4f46e5",
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.35
                    },
                    {
                        label: "Monthly Subscriptions Taken",
                        data: subscriptionsData,
                        borderColor: "#10b981",
                        backgroundColor: subGradient,
                        borderWidth: 3,
                        pointBackgroundColor: "#ffffff",
                        pointBorderColor: "#10b981",
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: "index",
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: "top",
                        align: "end",
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 18,
                            font: {
                                size: 12,
                                weight: "600",
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: "#0f172a",
                        titleFont: { size: 13, weight: "bold" },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: true,
                        callbacks: {
                            label: function (context) {
                                return ` ${context.dataset.label}: ${context.parsed.y} restaurants`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: "500"
                            },
                            color: "#64748b"
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "#f1f5f9"
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                size: 12,
                                weight: "500"
                            },
                            color: "#64748b"
                        }
                    }
                }
            }
        };

        let growthChart = new Chart(ctx, chartConfig);

        // AJAX Year Switcher
        const yearSelect = document.getElementById("chartYearSelect");
        if (yearSelect) {
            yearSelect.addEventListener("change", function () {
                const selectedYear = this.value;
                
                fetch(`{{ route('admin.dashboard') }}?year=${selectedYear}`, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Accept": "application/json"
                    }
                })
                .then(response => response.json())
                .then(res => {
                    if (res && res.labels) {
                        growthChart.data.labels = res.labels;
                        growthChart.data.datasets[0].data = res.registrations;
                        growthChart.data.datasets[1].data = res.subscriptions;
                        growthChart.update();

                        // Update header badges
                        const totalRegEl = document.getElementById("chartTotalRegs");
                        const totalSubEl = document.getElementById("chartTotalSubs");
                        if (totalRegEl) totalRegEl.textContent = res.totalRegistrationsYear;
                        if (totalSubEl) totalSubEl.textContent = res.totalSubscriptionsYear;
                    }
                })
                .catch(err => {
                    console.error("Error loading yearly chart data:", err);
                });
            });
        }
    });
</script>
@endsection
