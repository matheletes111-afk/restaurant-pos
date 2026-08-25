@extends('layouts.app')

@section('title')
<title>Restaurant || Available Plans</title>
@endsection

@section('style')
@include('includes.style')
<style>
    :root {
        --primary: #ff6a00;
        --primary-hover: #e65c00;
        --bg-light: #f8fafc;
        --surface: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #475569;
        --text-muted: #94a3b8;
        --border: #e2e8f0;
        --success: #10b981;
        --success-light: #ecfdf5;
        --warning: #f59e0b;
        --warning-light: #fffbeb;
        --info: #0ea5e9;
        --info-light: #f0f9ff;
        --radius-xl: 20px;
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        --shadow-lg: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.03);
    }

    body {
        background-color: var(--bg-light) !important;
        color: var(--text-primary);
        font-family: 'Public Sans', 'Inter', sans-serif;
    }

    /* Page Header */
    .page-header-custom {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: var(--radius-lg);
        padding: 30px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        color: white;
    }

    .page-header-custom::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 106, 0, 0.15), transparent 70%);
        border-radius: 50%;
    }

    .page-header-custom h2 {
        font-size: 1.6rem;
        font-weight: 700;
        color: white;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-header-custom h2 i {
        color: var(--primary);
    }

    .page-header-custom p {
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0;
        font-size: 0.95rem;
        position: relative;
        z-index: 1;
    }

    .user-welcome {
        font-size: 0.9rem;
        background: rgba(255,255,255,0.08);
        padding: 8px 16px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 1;
    }

    /* Plan Card Design */
    .plan-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .plan-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(255, 106, 0, 0.3);
    }

    .plan-card.default-plan {
        border: 2px solid var(--primary);
    }

    /* Badges style */
    .default-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: linear-gradient(135deg, var(--primary), #ff8c42);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 700;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(255,106,0,0.2);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .active-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--success-light);
        color: var(--success);
        border: 1px solid rgba(16, 185, 129, 0.2);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 700;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .assigned-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--warning-light);
        color: var(--warning);
        border: 1px solid rgba(245, 158, 11, 0.2);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 700;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Plan Header styling */
    .plan-header {
        padding: 40px 24px 20px;
        text-align: center;
        border-bottom: 1px solid #f1f5f9;
        background-color: #fafbfc;
        position: relative;
    }

    .plan-icon {
        width: 60px;
        height: 60px;
        background-color: #fff0e6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: var(--primary);
        font-size: 1.5rem;
        transition: all 0.3s;
    }

    .plan-card:hover .plan-icon {
        transform: scale(1.1);
        background-color: var(--primary);
        color: white;
    }

    .plan-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .plan-price {
        margin: 10px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        color: var(--text-primary);
    }

    .plan-price .active-price {
        font-size: 1.9rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .plan-price .cross-price {
        text-decoration: line-through;
        color: #aaa;
        font-size: 1.1rem;
        font-weight: normal;
        line-height: 1;
    }

    .plan-price small {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-muted);
    }

    .plan-duration {
        font-size: 0.75rem;
        color: var(--text-secondary);
        background: #f1f5f9;
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    /* Plan Body */
    .plan-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .plan-description {
        color: var(--text-secondary);
        font-size: 0.85rem;
        line-height: 1.5;
        margin-bottom: 24px;
        text-align: center;
        min-height: 45px;
    }

    .plan-features {
        list-style: none;
        padding: 0;
        margin: 0 0 24px 0;
        flex-grow: 1;
    }

    .plan-features li {
        padding: 10px 0;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.85rem;
        color: var(--text-secondary);
        border-bottom: 1px solid #f8fafc;
    }

    .plan-features li:last-child {
        border-bottom: none;
    }

    .plan-features li > i:first-child {
        color: var(--primary);
        font-size: 0.9rem;
        width: 16px;
        text-align: center;
    }

    /* Expiry Warning */
    .expiry-info {
        text-align: center;
        margin-bottom: 16px;
    }

    .expiry-info small {
        background: var(--success-light);
        padding: 4px 12px;
        border-radius: 20px;
        color: var(--success);
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid rgba(16, 185, 129, 0.15);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Premium Buttons */
    .btn-current {
        background-color: #f1f5f9;
        border: 1px solid var(--border);
        color: var(--text-secondary);
        padding: 12px 20px;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.9rem;
        width: 100%;
        cursor: not-allowed;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.25s;
    }

    .btn-select {
        background: linear-gradient(to right, var(--primary), #ff8c42);
        color: white !important;
        border: none;
        padding: 12px 20px;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.9rem;
        width: 100%;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(255, 106, 0, 0.15);
    }

    .btn-select:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 106, 0, 0.3);
        text-decoration: none;
    }

    .btn-select.disabled {
        background: #e2e8f0;
        color: #94a3b8 !important;
        box-shadow: none;
        cursor: not-allowed;
        transform: none;
    }

    .empty-state {
        text-align: center;
        padding: 50px 30px;
        background: var(--surface);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 20px;
    }

    .empty-state h5 {
        font-size: 1.15rem;
        color: var(--text-primary);
        font-weight: 700;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-bottom: 0;
        line-height: 1.6;
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">

        <!-- Breadcrumb -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Plan Catalogue</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Available Plans</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb -->

        <!-- Custom Page Header -->
        <div class="page-header-custom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 w-100">
                <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                    <div style="background: #ffffff; padding: 12px 18px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.08); flex-shrink: 0;">
                        <img src="{{ asset('logo.png') }}" alt="Bill & Bite Logo" style="height: 40px; width: auto;">
                    </div>
                    <div style="text-align: left;">
                        <h2 style="margin-bottom: 5px; color: white;"><i class="fas fa-gem"></i> Available Plans</h2>
                        <p>Upgrade or activate subscription plans customized for your restaurant operations</p>
                    </div>
                </div>
                <div class="user-welcome" style="margin-top: 0;">
                    <i class="fas fa-store"></i> {{ Auth::user()->restaurant->name ?? 'Your Restaurant' }}
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: var(--radius-sm);">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline:none; border:none; background:none; float:right; font-size:1.5rem; line-height:1; color:#000; opacity:0.5;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--radius-sm);">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline:none; border:none; background:none; float:right; font-size:1.5rem; line-height:1; color:#000; opacity:0.5;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                @if($plans->count() > 0)
                <div class="row">
                    @foreach($plans as $plan)
                        @php
                            $isDefault = ($plan->is_default_plan == 'Y' || $plan->is_default_free == 'Y' || $plan->is_default_paid == 'Y' || $plan->price == 0);
                            $isAssigned = in_array($plan->id, $assignedPlanIds);
                            $isActive = isset($activeSubscriptions[$plan->id]);
                            $activeSubscription = $isActive ? $activeSubscriptions[$plan->id] : null;
                            
                            $planIcons = [
                                'Basic' => 'fa-layer-group',
                                'Standard' => 'fa-chart-line',
                                'Premium' => 'fa-crown',
                                'Enterprise' => 'fa-building',
                                'Pro' => 'fa-rocket'
                            ];
                            $planIcon = $planIcons[$plan->name] ?? 'fa-gem';
                        @endphp
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="plan-card {{ $isDefault ? 'default-plan' : '' }}">
                                @if($plan->label_name)
                                    <div class="default-badge">
                                        <i class="fas fa-star"></i> {{ $plan->label_name }}
                                    </div>
                                @elseif($isDefault)
                                    <div class="default-badge">
                                        <i class="fas fa-star"></i> Default Plan
                                    </div>
                                @endif
                                
                                @if($isActive)
                                    <div class="active-badge">
                                        <i class="fas fa-check-circle"></i> Active
                                    </div>
                                @elseif($isAssigned && !$isActive)
                                    <div class="assigned-badge">
                                        <i class="fas fa-clock"></i> Assigned
                                    </div>
                                @endif

                                <div class="plan-header">
                                    <div class="plan-icon">
                                        <i class="fas {{ $planIcon }}"></i>
                                    </div>
                                    <h4 class="plan-name">{{ $plan->name }}</h4>
                                     <div class="plan-price">
                                         @if($plan->price == 0)
                                             <span class="active-price" style="color: var(--success);">FREE</span>
                                         @else
                                             @if($plan->cross_price)
                                                 <span class="cross-price">₹{{ number_format($plan->cross_price, 2) }}</span>
                                             @endif
                                             <span class="active-price">
                                                 ₹{{ number_format($plan->price, 2) }}
                                                 <small>/ {{ ucfirst($plan->billing_cycle) }}</small>
                                             </span>
                                         @endif
                                     </div>
                                    <div class="plan-duration">
                                        <i class="fas fa-calendar-alt me-1"></i> {{ $plan->duration_days }} days validity
                                    </div>
                                </div>

                                <div class="plan-body">
                                    <div class="plan-description">
                                        {{ $plan->description ?? 'Premium POS management package for your business' }}
                                    </div>

                                    <ul class="plan-features">
                                        <li><i class="fas fa-folder-open"></i> {{ $plan->category_number == 0 ? 'Unlimited' : number_format($plan->category_number) }} Categories</li>
                                        <li><i class="fas fa-utensils"></i> {{ $plan->total_number_of_dishes == 0 ? 'Unlimited' : number_format($plan->total_number_of_dishes) }} Dishes</li>
                                        <li><i class="fas fa-chair"></i> {{ $plan->total_number_of_table == 0 ? 'Unlimited' : number_format($plan->total_number_of_table) }} Tables</li>
                                        
                                        <li><i class="fas fa-check-circle text-success"></i> Menu Availability Management</li>
                                        <li><i class="fas fa-check-circle text-success"></i> Order Management</li>
                                        <li><i class="fas fa-check-circle text-success"></i> Kitchen Panel</li>
                                        <li><i class="fas fa-check-circle text-success"></i> Qr Code Ordering</li>
                                        <li><i class="fas fa-check-circle text-success"></i> Customer Support</li>
                                        <li><i class="fas fa-check-circle text-success"></i> Staff Management</li>
                                        <li><i class="fas fa-check-circle text-success"></i> Reports</li>

                                        @if($plan->inventory_checkbox == 'Y')
                                            <li><i class="fas fa-check-circle text-success"></i> Manage Product</li>
                                            <li><i class="fas fa-check-circle text-success"></i> Manage Purchase</li>
                                            <li><i class="fas fa-check-circle text-success"></i> Manage Stockout</li>
                                            <li><i class="fas fa-check-circle text-success"></i> Debit Note</li>
                                            <li><i class="fas fa-check-circle text-success"></i> Inventory</li>
                                        @else
                                            <li><i class="fas fa-times-circle text-danger"></i> Manage Product</li>
                                            <li><i class="fas fa-times-circle text-danger"></i> Manage Purchase</li>
                                            <li><i class="fas fa-times-circle text-danger"></i> Manage Stockout</li>
                                            <li><i class="fas fa-times-circle text-danger"></i> Debit Note</li>
                                            <li><i class="fas fa-times-circle text-danger"></i> Inventory</li>
                                        @endif
                                    </ul>

                                    @if($isActive)
                                        @if($activeSubscription)
                                            <div class="expiry-info">
                                                <small><i class="fas fa-hourglass-half me-1"></i> Expires: {{ $activeSubscription->end_date->format('d M Y') }}</small>
                                            </div>
                                        @endif
                                        <button class="btn-current" disabled>
                                            <i class="fas fa-check-circle me-2"></i> Currently Active
                                        </button>
                                    @else
                                        @if($plan->price == 0)
                                            @if($hasFreeTrial ?? false)
                                                <button class="btn-select disabled" disabled>
                                                    <i class="fas fa-ban me-2"></i> Free Trial Used
                                                </button>
                                            @else
                                                <a href="{{ route('admin.subscriptions.create', $plan->id) }}" class="btn-select">
                                                    <i class="fas fa-gift me-2"></i> Start Free Trial
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('admin.subscriptions.create', $plan->id) }}" class="btn-select">
                                                <i class="fas fa-shopping-cart me-2"></i> Subscribe Now
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h5>No Plans Available</h5>
                    <p>No plans have been assigned to your restaurant yet.<br>Please contact the administrator.</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

@endsection