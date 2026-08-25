<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Plans - RestoPOS</title>
    @include('includes.style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            --success-bg: #d1fae5;
            --danger: #ef4444;
            --radius-xl: 18px;
            --radius-lg: 14px;
            --radius-md: 10px;
            --radius-sm: 6px;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Public Sans', 'Inter', sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        .plans-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }

        /* Page Header */
        .page-header-custom {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: var(--radius-xl);
            padding: 30px 40px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }

        .page-header-custom::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 106, 0, 0.12), transparent 70%);
            border-radius: 50%;
        }

        .page-header-custom h1 {
            font-weight: 700;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }

        .page-header-custom p {
            color: rgba(255,255,255,0.7);
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
            margin-bottom: 0;
        }

        .user-welcome {
            background: rgba(255,255,255,0.12);
            border-radius: 50px;
            padding: 8px 20px;
            display: inline-block;
            margin-top: 20px;
            font-size: 0.85rem;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
            z-index: 1;
        }

        /* Plan Cards */
        .plan-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: transform 0.3s cubic-bezier(.25,.8,.25,1), box-shadow 0.3s ease, border-color 0.2s;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .plan-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -5px rgba(0, 0, 0, 0.03);
            border-color: rgba(255, 106, 0, 0.3);
        }

        .plan-card.default-plan {
            border: 2px solid var(--primary);
            box-shadow: 0 10px 20px -3px rgba(255, 106, 0, 0.08), 0 4px 6px -2px rgba(255, 106, 0, 0.03);
        }

        .default-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, var(--primary), #ff8c42);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            z-index: 10;
            letter-spacing: 0.05em;
            box-shadow: 0 2px 8px rgba(255, 106, 0, 0.2);
        }

        .assigned-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
            z-index: 10;
        }

        .plan-header {
            padding: 35px 24px 20px;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        .plan-name {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .plan-price {
            margin: 15px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            color: var(--primary);
        }

        .plan-price .active-price {
            font-size: 2.2rem;
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
            font-size: 0.85rem;
            font-weight: normal;
            color: var(--text-secondary);
        }

        .plan-duration {
            font-size: 0.8rem;
            color: var(--text-secondary);
            background-color: #f1f5f9;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
        }

        .plan-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .plan-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
            line-height: 1.6;
            margin-bottom: 20px;
            min-height: 60px;
            text-align: center;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 0 0 30px 0;
            flex-grow: 1;
        }

        .plan-features li {
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.875rem;
            color: var(--text-secondary);
            border-bottom: 1px solid #f1f5f9;
        }

        .plan-features li:last-child {
            border-bottom: none;
        }

        .plan-features li > i:first-child {
            width: 20px;
            color: var(--primary);
            font-size: 0.95rem;
        }

        /* Buttons */
        .btn-current {
            background: #e2e8f0;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            width: 100%;
            cursor: not-allowed;
            text-align: center;
            display: block;
        }

        .btn-select {
            background: linear-gradient(to right, var(--primary), #ff8c42);
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(255, 106, 0, 0.15);
            text-decoration: none;
        }

        .btn-select:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,106,0,0.3);
            color: white;
            text-decoration: none;
        }

        .btn-select:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Alert */
        .alert-custom {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px 24px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .alert-custom.success {
            border-left: 4px solid var(--success);
            background-color: var(--success-bg);
            color: #065f46;
            font-weight: 500;
        }

        .alert-custom i {
            margin-right: 5px;
        }

        /* Grid */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        /* Back link */
        .back-link {
            text-align: center;
            margin-top: 50px;
        }

        .back-link a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            text-decoration: none;
        }

        .back-link a:hover {
            background: #f1f5f9;
            color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .empty-state i {
            font-size: 48px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .empty-state h5 {
            font-size: 1.25rem;
            color: var(--text-primary);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .empty-state p {
            color: var(--text-secondary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .plans-container { padding: 20px 15px; }
            .page-header-custom { padding: 30px 20px; }
            .page-header-custom h1 { font-size: 1.8rem; }
            .plans-grid { grid-template-columns: 1fr; }
            .plan-name { font-size: 1.3rem; }
            .plan-price { font-size: 1.8rem; }
        }

        /* Animation delays */
        .plan-card:nth-child(1) { animation-delay: 0.05s; }
        .plan-card:nth-child(2) { animation-delay: 0.10s; }
        .plan-card:nth-child(3) { animation-delay: 0.15s; }
    </style>
</head>

<body>
<div class="plans-container">
    <!-- Page Header -->
    <div class="page-header-custom">
        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <div style="background: #ffffff; padding: 12px 18px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.08); flex-shrink: 0;">
                <img src="{{ asset('logo.png') }}" alt="Bill & Bite Logo" style="height: 40px; width: auto;">
            </div>
            <div style="text-align: left;">
                <h1><i class="fas fa-gem me-2"></i>Available Plans</h1>
                <p>Choose a plan that fits your restaurant's size and needs</p>
            </div>
        </div>
        <div class="user-welcome" style="margin-top: 0;">
            <i class="fas fa-store"></i> {{ Auth::user()->restaurant->name ?? 'Your Restaurant' }}
        </div>
    </div>

    @if(session('success'))
        <div class="alert-custom success">
            <i class="fas fa-check-circle" style="color: var(--success);"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-custom" style="border-left-color: var(--danger); background-color: #fee2e2; color: #991b1b;">
            <i class="fas fa-exclamation-circle" style="color: var(--danger);"></i> {{ session('error') }}
        </div>
    @endif

@if($plans->count() > 0)
<div class="row justify-content-center">
    @foreach($plans as $plan)
        @php
            $isDefault = ($plan->is_default_plan == 'Y' || $plan->is_default_free == 'Y' || $plan->is_default_paid == 'Y' || $plan->price == 0);
            $isAssigned = in_array($plan->id, $assignedPlanIds);
            // Only show "Currently Active" if the plan is specifically assigned (not just default)
            $isCurrentlyActive = $isAssigned;
            // For default plan, show subscribe option instead
            $showSubscribe = !$isAssigned;
        @endphp
        <div class="col-md-4 col-lg-4 mb-4">
            <div class="plan-card {{ $isDefault ? 'default-plan' : '' }}">
                @if($plan->label_name)
                    <div class="default-badge">
                        <i class="fas fa-star"></i> {{ $plan->label_name }}
                    </div>
                @elseif($isDefault)
                    <div class="default-badge">
                        <i class="fas fa-star"></i> Default
                    </div>
                @endif
                @if($isAssigned)
                    <div class="assigned-badge">
                        <i class="fas fa-check-circle"></i> Assigned
                    </div>
                @endif

                <div class="plan-header">
                    <h3 class="plan-name">{{ $plan->name }}</h3>
                    <div class="plan-price">
                        @if($plan->price == 0)
                            <span class="active-price">FREE</span>
                        @else
                            @php
                                $gstPercentage = $plan->gst_percentage ?? 18;
                                $taxableAmount = $plan->taxable_amount ?? ($plan->price / (1 + ($gstPercentage / 100)));
                            @endphp
                            @if($plan->cross_price)
                                <span class="cross-price">₹{{ number_format($plan->cross_price, 2) }}</span>
                            @endif
                            <span class="active-price">₹{{ number_format($plan->price, 2) }}</span>
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
                        <li><i class="fas fa-folder"></i> {{ $plan->category_number == 0 ? 'Unlimited' : $plan->category_number }} Categories</li>
                        <li><i class="fas fa-utensils"></i> {{ $plan->total_number_of_dishes == 0 ? 'Unlimited' : $plan->total_number_of_dishes }} Dishes</li>
                        <li><i class="fas fa-table"></i> {{ $plan->total_number_of_table == 0 ? 'Unlimited' : $plan->total_number_of_table }} Tables</li>
                        
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

                    <a href="{{ route('admin.subscriptions.create', $plan->id) }}" class="btn-select">
                        <i class="fas fa-shopping-cart me-2"></i>
                        {{ $plan->price == 0 ? 'Start Free Trial' : 'Subscribe Now' }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@else
<div class="row w-100 justify-content-center">
    <div class="col-md-8">
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h5>No Plans Available</h5>
            <p>No plans have been assigned to your restaurant yet.<br>Please contact the administrator.</p>
            <a href="{{ route('logout') }}" class="btn-select" style="display: inline-flex; width: auto; margin-top: 20px; padding: 10px 30px;">
                <i class="fas fa-arrow-left me-2"></i> Go Back
            </a>
        </div>
    </div>
</div>
@endif

    <div class="back-link">
        <a href="{{ route('logout') }}">
            <i class="fas fa-arrow-left"></i> Logout
        </a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
    // Add hover effect animation
    document.addEventListener('DOMContentLoaded', function() {
        const planCards = document.querySelectorAll('.plan-card');
        planCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.zIndex = '10';
            });
            card.addEventListener('mouseleave', function() {
                this.style.zIndex = '1';
            });
        });
    });
</script>

</body>
</html>