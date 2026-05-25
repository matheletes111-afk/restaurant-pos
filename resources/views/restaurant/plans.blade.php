@extends('layouts.app')

@section('title')
<title>Restaurant || Available Plans</title>
@endsection

@section('style')
@include('includes.style')
<style>
    :root {
        --gold: #C9A84C;
        --gold-light: #E8C97A;
        --gold-dim: rgba(201,168,76,0.15);
        --obsidian: #0A0A0B;
        --deep: #111114;
        --surface: #17171C;
        --surface-2: #1E1E25;
        --surface-3: #26262F;
        --rim: rgba(255,255,255,0.07);
        --rim-strong: rgba(255,255,255,0.12);
        --text-primary: #F2EEE6;
        --text-secondary: rgba(242,238,230,0.55);
        --text-muted: rgba(242,238,230,0.3);
        --success: #3DD68C;
        --success-dark: #2BBF7A;
        --danger: #FF6B6B;
        --warning: #FFB347;
        --radius-xl: 24px;
        --radius-lg: 18px;
        --radius-md: 12px;
        --radius-sm: 8px;
    }

    /* Page Header */
    .page-header-custom {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-radius: var(--radius-xl);
        padding: 32px 30px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .page-header-custom::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(201,168,76,0.15), transparent);
        border-radius: 50%;
    }

    .page-header-custom h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: white;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }

    .page-header-custom p {
        color: rgba(255,255,255,0.7);
        margin-bottom: 0;
        position: relative;
        z-index: 1;
    }

    /* Plan Cards */
    .plan-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--rim);
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
    }

    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        border-color: rgba(201,168,76,0.3);
    }

    .plan-card.default-plan {
        border: 2px solid var(--gold);
    }

    /* Badges */
    .default-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: linear-gradient(135deg, var(--gold), var(--gold-light));
        color: var(--obsidian);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 700;
        z-index: 10;
    }

    .active-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, var(--success), var(--success-dark));
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 600;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .assigned-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, var(--warning), #e67e22);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 600;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Plan Header */
    .plan-header {
        padding: 30px 20px 20px;
        text-align: center;
        border-bottom: 1px solid var(--rim);
    }

    .plan-icon {
        width: 65px;
        height: 65px;
        background: rgb(255 198 46);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }

    .plan-icon i {
        font-size: 1.8rem;
        color: var(--gold-light);
    }

    .plan-name {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .plan-price {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--gold-light);
        margin: 10px 0;
    }

    .plan-price small {
        font-size: 0.7rem;
        font-weight: normal;
        color: var(--text-muted);
    }

    .plan-duration {
        font-size: 0.7rem;
        color: var(--text-muted);
        background: var(--surface-2);
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
    }

    /* Plan Body */
    .plan-body {
        padding: 20px;
    }

    .plan-description {
        color: var(--text-secondary);
        font-size: 0.8rem;
        line-height: 1.5;
        margin-bottom: 20px;
        text-align: center;
        min-height: 60px;
    }

    .plan-features {
        list-style: none;
        padding: 0;
        margin: 0 0 20px 0;
    }

    .plan-features li {
        padding: 8px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.8rem;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--rim);
    }

    .plan-features li:last-child {
        border-bottom: none;
    }

    .plan-features i {
        width: 20px;
        color: var(--gold);
        font-size: 0.8rem;
    }

    /* Buttons */
    .btn-current {
        background: var(--surface-2);
        border: 1px solid var(--rim-strong);
        color: var(--text-secondary);
        padding: 10px 15px;
        border-radius: var(--radius-sm);
        font-weight: 600;
        width: 100%;
        cursor: not-allowed;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-select {
        background: linear-gradient(135deg, #FF6A00, #FF8C42);
        color: var(--obsidian);
        border: none;
        padding: 10px 15px;
        border-radius: var(--radius-sm);
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-select:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255,106,0,0.3);
        color: var(--obsidian);
        text-decoration: none;
    }

    .btn-select.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .expiry-info {
        text-align: center;
        margin-bottom: 15px;
    }

    .expiry-info small {
        background: rgba(61,214,140,0.15);
        padding: 3px 10px;
        border-radius: 20px;
        color: var(--success);
        font-size: 0.7rem;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--surface);
        border-radius: var(--radius-lg);
    }

    .empty-state i {
        font-size: 48px;
        color: var(--text-muted);
        margin-bottom: 15px;
    }

    .empty-state h5 {
        font-size: 1.2rem;
        color: var(--text-secondary);
        margin-bottom: 8px;
    }

    .empty-state p {
        color: var(--text-muted);
        font-size: 0.85rem;
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
                            <h5 class="m-b-10">Available Plans</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Plans</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb -->

        <!-- Custom Page Header -->
        <div class="page-header-custom">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2><i class="fas fa-gem me-2"></i> Available Plans</h2>
                    <p>Explore our premium plans and enhance your restaurant management experience</p>
                </div>
                <div class="user-welcome text-white">
                    <i class="fas fa-store"></i> {{ Auth::user()->restaurant->name ?? 'Your Restaurant' }}
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        @if($plans->count() > 0)
                        <div class="row">
                            @foreach($plans as $plan)
                                @php
                                    $isDefault = ($defaultPlan && $defaultPlan->id == $plan->id);
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
                                        @if($isDefault)
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
                                                <i class="fas fa-clock"></i> Available
                                            </div>
                                        @endif

                                        <div class="plan-header">
                                            <div class="plan-icon">
                                                <i class="fas {{ $planIcon }}"></i>
                                            </div>
                                            <h4 class="plan-name">{{ $plan->name }}</h4>
                                            <div class="plan-price">
                                                @if($plan->price == 0)
                                                    <span style="color: var(--success);">FREE</span>
                                                @else
                                                    ₹{{ number_format($plan->price, 2) }}
                                                    <small>/ {{ ucfirst($plan->billing_cycle) }}</small>
                                                @endif
                                            </div>
                                            <div class="plan-duration">
                                                <i class="fas fa-calendar-alt me-1"></i> {{ $plan->duration_days }} days validity
                                            </div>
                                        </div>

                                        <div class="plan-body">
                                            <div class="plan-description">
                                                {{ $plan->description ?? 'Perfect plan for your restaurant needs' }}
                                            </div>

                                            <ul class="plan-features">
                                                <li><i class="fas fa-folder"></i> {{ $plan->category_number == 0 ? 'Unlimited' : number_format($plan->category_number) }} Categories</li>
                                                <li><i class="fas fa-utensils"></i> {{ $plan->total_number_of_dishes == 0 ? 'Unlimited' : number_format($plan->total_number_of_dishes) }} Dishes</li>
                                                <li><i class="fas fa-table"></i> {{ $plan->total_number_of_table == 0 ? 'Unlimited' : number_format($plan->total_number_of_table) }} Tables</li>
                                                <li><i class="fas fa-boxes"></i> Inventory {{ $plan->inventory_checkbox == 'Y' ? 'Enabled' : 'Disabled' }}</li>
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
                                                            <i class="fas fa-ban me-2"></i> Trial Used
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

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
    $(document).ready(function() {
        // Add hover effect for cards
        $('.plan-card').on('mouseenter', function() {
            $(this).css('z-index', '10');
        }).on('mouseleave', function() {
            $(this).css('z-index', '1');
        });
    });
</script>

@endsection