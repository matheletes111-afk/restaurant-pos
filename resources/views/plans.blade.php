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
            --danger: #FF6B6B;
            --radius-xl: 24px;
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --glow: 0 0 40px rgba(201,168,76,0.12);
        }

        body {
            background-color: var(--obsidian);
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(201,168,76,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 100%, rgba(201,168,76,0.05) 0%, transparent 50%);
            font-family: 'DM Sans', sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.6;
        }

        .plans-container {
            max-width: 1380px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }

        /* Page Header */
        .page-header-custom {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: var(--radius-xl);
            padding: 48px 40px;
            margin-bottom: 48px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .page-header-custom::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(201,168,76,0.15), transparent);
            border-radius: 50%;
        }

        .page-header-custom h1 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 700;
            font-size: 2.8rem;
            color: white;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .page-header-custom p {
            color: rgba(255,255,255,0.7);
            font-size: 1rem;
            position: relative;
            z-index: 1;
        }

        .user-welcome {
            background: rgba(255,255,255,0.15);
            border-radius: 50px;
            padding: 8px 20px;
            display: inline-block;
            margin-top: 20px;
            font-size: 0.85rem;
        }

        /* Plan Cards */
        .plan-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--rim);
            transition: transform 0.35s cubic-bezier(.22,.68,0,1.2), box-shadow 0.35s ease, border-color 0.25s;
            position: relative;
            height: 100%;
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .plan-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 0 1px rgba(201,168,76,0.2);
            border-color: rgba(201,168,76,0.25);
        }

        .plan-card.default-plan {
            border: 2px solid var(--gold);
            box-shadow: 0 0 30px rgba(201,168,76,0.2);
        }

        .default-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: var(--obsidian);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            z-index: 10;
            letter-spacing: 0.05em;
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
            padding: 30px 24px 20px;
            text-align: center;
            border-bottom: 1px solid var(--rim);
        }

        .plan-name {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .plan-price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gold-light);
            margin: 15px 0;
        }

        .plan-price small {
            font-size: 0.8rem;
            font-weight: normal;
            color: var(--text-muted);
        }

        .plan-duration {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .plan-body {
            padding: 24px;
        }

        .plan-description {
            color: var(--text-secondary);
            font-size: 0.85rem;
            line-height: 1.6;
            margin-bottom: 20px;
            min-height: 70px;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .plan-features li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
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
            padding: 12px 20px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            width: 100%;
            cursor: not-allowed;
        }

        .btn-select {
            background: linear-gradient(to right, #FF6A00, #FF8C42);
            color: var(--obsidian);
            border: none;
            padding: 12px 20px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-select:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255,106,0,0.35);
            color: var(--obsidian);
        }

        .btn-select:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Alert */
        .alert-custom {
            background: var(--surface);
            border: 1px solid var(--rim-strong);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .alert-custom.success {
            border-left: 4px solid var(--success);
        }

        .alert-custom i {
            margin-right: 10px;
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
            background: var(--surface-2);
            border: 1px solid var(--rim-strong);
            color: var(--text-secondary);
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .back-link a:hover {
            background: var(--surface-3);
            border-color: rgba(201,168,76,0.4);
            color: var(--gold-light);
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--rim);
        }

        .empty-state i {
            font-size: 48px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .empty-state h5 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            color: var(--text-secondary);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .plans-container { padding: 20px 15px; }
            .page-header-custom { padding: 30px 20px; }
            .page-header-custom h1 { font-size: 2rem; }
            .plans-grid { grid-template-columns: 1fr; }
            .plan-name { font-size: 1.3rem; }
            .plan-price { font-size: 1.5rem; }
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
        <h1><i class="fas fa-gem me-2"></i>Your Plans</h1>
        <p>Here are the plans available for your restaurant</p>
        <div class="user-welcome">
            <i class="fas fa-store"></i> {{ Auth::user()->restaurant->name ?? 'Your Restaurant' }}
        </div>
    </div>

    @if(session('success'))
        <div class="alert-custom success">
            <i class="fas fa-check-circle" style="color: var(--success);"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-custom" style="border-left-color: var(--danger);">
            <i class="fas fa-exclamation-circle" style="color: var(--danger);"></i> {{ session('error') }}
        </div>
    @endif

@if($plans->count() > 0)
<div class="row justify-content-center">
    @foreach($plans as $plan)
        @php
            $isDefault = ($defaultPlan && $defaultPlan->id == $plan->id);
            $isAssigned = in_array($plan->id, $assignedPlanIds);
            // Only show "Currently Active" if the plan is specifically assigned (not just default)
            $isCurrentlyActive = $isAssigned;
            // For default plan, show subscribe option instead
            $showSubscribe = !$isAssigned;
        @endphp
        <div class="col-md-4 col-lg-4 mb-4">
            <div class="plan-card {{ $isDefault ? 'default-plan' : '' }}">
                @if($isDefault)
                    <div class="default-badge">
                        <i class="fas fa-star"></i> Default Plan
                    </div>
                @endif
                @if($isAssigned)
                    <div class="assigned-badge">
                        <i class="fas fa-check-circle"></i> Assigned to You
                    </div>
                @endif

                <div class="plan-header">
    <h3 class="plan-name">{{ $plan->name }}</h3>
    <div class="plan-price">
        @if($plan->price == 0)
            FREE
        @else
            @php
                $gstPercentage = $plan->gst_percentage ?? 18;
                $taxableAmount = $plan->taxable_amount ?? ($plan->price / (1 + ($gstPercentage / 100)));
            @endphp
            <div>₹{{ number_format($plan->price, 2) }}{{--  <small>/ {{ ucfirst($plan->billing_cycle) }}</small> --}}</div>
            {{-- <div class="small text-muted mt-1">
                <i class="fas fa-info-circle"></i> ₹{{ number_format($taxableAmount, 2) }} + {{ $gstPercentage }}% GST
            </div> --}}
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
                        <li><i class="fas fa-boxes"></i> Inventory {{ $plan->inventory_checkbox == 'Y' ? 'Enabled' : 'Disabled' }}</li>
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
<div class="row">
    <div class="col-md-12">
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h5>No Plans Available</h5>
            <p>No plans have been assigned to your restaurant yet.<br>Please contact the administrator.</p>
            <a href="{{ route('logout') }}" class="btn-select" style="display: inline-block; width: auto; margin-top: 20px; padding: 10px 30px;">
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