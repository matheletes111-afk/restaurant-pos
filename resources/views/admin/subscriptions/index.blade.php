<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Subscriptions & Billing || Bill&Bite</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <!-- Subscriptions Page CSS -->
    <link rel="stylesheet" href="{{ asset('admin_template/css/subscriptions-page.css') }}">
</head>
<body data-pc-theme="light">
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    @include('includes.sidebar')

    <div class="pc-container">
        <div class="pc-content">
            <div class="sub-page-wrapper">
                
                <!-- 1. Breadcrumb -->
                <div class="page-header mb-3">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <ul class="breadcrumb mb-2" style="background: transparent; padding: 0;">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                                    <li class="breadcrumb-item text-muted" aria-current="page">Subscriptions & Billing</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Flash / Error Messages --}}
                @include('includes.message')

                @php
                    $activeSub = $subscriptions->first(fn($s) => $s->status === 'active') ?? $subscriptions->first();
                    $activePlan = $activeSub->plan ?? null;
                    $totalCount = $subscriptions->count();
                    $activeCount = $subscriptions->where('status', 'active')->count();
                    $yearlyCount = $subscriptions->filter(fn($s) => strtolower($s->plan->billing_cycle ?? '') == 'yearly')->count();
                    $monthlyCount = $subscriptions->filter(fn($s) => strtolower($s->plan->billing_cycle ?? '') == 'monthly')->count();
                    $inactiveCount = $subscriptions->filter(fn($s) => in_array($s->status, ['cancelled', 'expired', 'halted']))->count();

                    // Calculate remaining days for active sub
                    $daysRemaining = 0;
                    $totalDuration = 30;
                    $progressPercent = 100;
                    if ($activeSub && $activeSub->end_date) {
                        $now = \Carbon\Carbon::now();
                        $end = \Carbon\Carbon::parse($activeSub->end_date);
                        $start = $activeSub->start_date ? \Carbon\Carbon::parse($activeSub->start_date) : $now;
                        $totalDuration = max(1, $start->diffInDays($end));
                        $daysRemaining = max(0, $now->diffInDays($end, false));
                        $progressPercent = min(100, max(5, round(($daysRemaining / $totalDuration) * 100)));
                    }
                @endphp

                <!-- 2. Header Deck -->
                <div class="sub-header-deck">
                    <div class="sub-header-left">
                        <div class="sub-header-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div>
                            <h3 class="sub-header-title">My Subscriptions & Billing</h3>
                            <p class="sub-header-subtitle">Manage your restaurant plan, AutoPay settings, and tax invoices.</p>
                        </div>
                    </div>
                    <div class="sub-header-actions">
                        <a href="{{ route('restaurant.plans') }}" class="btn-sub-upgrade">
                            <i class="fas fa-rocket"></i> Upgrade / Change Plan
                        </a>
                        @if($activeSub)
                            <a href="{{ route('admin.subscriptions.invoice', $activeSub->id) }}" class="btn-sub-secondary" title="Download latest invoice">
                                <i class="fas fa-file-invoice-dollar"></i> Latest Invoice
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 3. Top Stats & Overview Cards Grid -->
                <div class="sub-stats-grid">
                    
                    <!-- Card 1: Active Subscription Hero -->
                    <div class="sub-stat-card hero-plan-card">
                        <div>
                            <div class="sub-stat-top">
                                <div>
                                    <div class="sub-stat-label">Active Plan</div>
                                    <div class="sub-stat-value">
                                        {{ $activePlan->name ?? 'Free Tier' }}
                                    </div>
                                </div>
                                <div class="sub-stat-icon-wrap">
                                    <i class="fas fa-gem"></i>
                                </div>
                            </div>

                            @if($activeSub && $activeSub->status === 'active')
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="sub-status-pill active" style="font-size: 0.72rem; padding: 2px 8px;">
                                        <span class="dot"></span> Active Subscription
                                    </span>
                                    @if(isset($activePlan->label_name) && $activePlan->label_name)
                                        <span class="plan-label-pill" style="font-size: 0.65rem;">{{ $activePlan->label_name }}</span>
                                    @endif
                                </div>

                                <div class="sub-stat-meta mb-1">
                                    <i class="fas fa-clock text-warning"></i>
                                    <span>
                                        @if($daysRemaining > 0)
                                            <strong>{{ $daysRemaining }} days</strong> remaining
                                        @else
                                            Expires today
                                        @endif
                                        <small class="text-white-50">({{ $activeSub->end_date ? \Carbon\Carbon::parse($activeSub->end_date)->format('d M Y') : 'N/A' }})</small>
                                    </span>
                                </div>
                                <div class="sub-progress-track">
                                    <div class="sub-progress-bar" style="width: {{ $progressPercent }}%;"></div>
                                </div>
                            @else
                                <p class="text-white-50 small mb-0 mt-2">
                                    <i class="fas fa-info-circle me-1"></i> No active paid plan. Upgrade now to unlock premium features.
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Card 2: AutoPay & Payment Method with Direct Auto-Renew Switch -->
                    <div class="sub-stat-card">
                        <div>
                            <div class="sub-stat-top">
                                <div>
                                    <div class="sub-stat-label">Auto-Debit & AutoPay</div>
                                    <div class="sub-stat-value" style="font-size: 1.25rem;">
                                        @if($activeSub && $activeSub->status === 'active')
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <label class="sub-switch">
                                                    <input type="checkbox" class="toggle-auto-renew" data-id="{{ $activeSub->id }}" {{ $activeSub->auto_renew ? 'checked' : '' }}>
                                                    <span class="sub-switch-slider"></span>
                                                </label>
                                                <span class="auto-renew-status badge {{ $activeSub->auto_renew ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px;">
                                                    {{ $activeSub->auto_renew ? 'ON' : 'OFF' }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted" style="font-size: 0.95rem;"><i class="fas fa-pause-circle me-1"></i> Disabled</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="sub-stat-icon-wrap amber">
                                    <i class="fas fa-university"></i>
                                </div>
                            </div>
                            
                            @php
                                $latestPayment = $activeSub ? $activeSub->payments->first() : null;
                                $payMethodText = $latestPayment->payment_method ?? 'Razorpay Gateway';
                            @endphp
                            <div class="sub-stat-meta">
                                <i class="fas fa-credit-card text-muted"></i>
                                <span>{{ strlen($payMethodText) > 20 ? substr($payMethodText, 0, 18).'...' : $payMethodText }}</span>
                            </div>

                            @if($activeSub && ($activePlan->price ?? 0) > 0 && in_array($activeSub->status, ['active', 'completed', 'pending', 'halted', 'authenticated']))
                                <div class="mt-2 pt-2 border-top">
                                    <a href="{{ route('admin.subscriptions.changePaymentMethod', $activeSub->id) }}" 
                                       class="text-warning font-weight-bold small change-bank-btn" 
                                       data-plan="{{ $activePlan->name ?? 'Plan' }}">
                                        <i class="fas fa-exchange-alt me-1"></i> Change Payment Method
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card 3: Plan Limits Capacity -->
                    <div class="sub-stat-card">
                        <div>
                            <div class="sub-stat-top">
                                <div>
                                    <div class="sub-stat-label">Resource Limits</div>
                                    <div class="sub-stat-value" style="font-size: 1.15rem; color: #0f172a;">
                                        {{ $activePlan->dish_display ?? ($activePlan->dishes_limit ? $activePlan->dishes_limit.' Dishes' : 'Unlimited') }}
                                    </div>
                                </div>
                                <div class="sub-stat-icon-wrap blue">
                                    <i class="fas fa-sliders-h"></i>
                                </div>
                            </div>
                            <div class="sub-stat-meta" style="font-size: 0.8rem;">
                                <span><i class="fas fa-folder text-primary me-1"></i> {{ $activePlan->category_display ?? ($activePlan->categories_limit ? $activePlan->categories_limit.' Cats' : 'Unlimited') }}</span>
                                <span>•</span>
                                <span><i class="fas fa-chair text-success me-1"></i> {{ $activePlan->table_display ?? ($activePlan->tables_limit ? $activePlan->tables_limit.' Tables' : 'Unlimited') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Subscriptions Overview -->
                    <div class="sub-stat-card">
                        <div>
                            <div class="sub-stat-top">
                                <div>
                                    <div class="sub-stat-label">Total History</div>
                                    <div class="sub-stat-value">{{ $totalCount }}</div>
                                </div>
                                <div class="sub-stat-icon-wrap green">
                                    <i class="fas fa-receipt"></i>
                                </div>
                            </div>
                            <div class="sub-stat-meta" style="font-size: 0.82rem;">
                                <span class="badge bg-light text-dark border"><i class="fas fa-calendar-check text-success me-1"></i> {{ $yearlyCount }} Yearly</span>
                                <span class="badge bg-light text-dark border"><i class="fas fa-calendar-alt text-primary me-1"></i> {{ $monthlyCount }} Monthly</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 4. Subscriptions List & History Card -->
                <div class="sub-main-card">
                    
                    <!-- Toolbar & Filters -->
                    <div class="sub-toolbar">
                        <div class="sub-filter-tabs">
                            <button type="button" class="sub-filter-tab active" data-filter="all">
                                <i class="fas fa-layer-group"></i> All
                                <span class="tab-badge">{{ $totalCount }}</span>
                            </button>
                            <button type="button" class="sub-filter-tab" data-filter="active">
                                <i class="fas fa-check-circle"></i> Active
                                <span class="tab-badge">{{ $activeCount }}</span>
                            </button>
                            <button type="button" class="sub-filter-tab" data-filter="yearly">
                                <i class="fas fa-calendar-check"></i> Yearly
                                <span class="tab-badge">{{ $yearlyCount }}</span>
                            </button>
                            <button type="button" class="sub-filter-tab" data-filter="monthly">
                                <i class="fas fa-calendar-alt"></i> Monthly
                                <span class="tab-badge">{{ $monthlyCount }}</span>
                            </button>
                            @if($inactiveCount > 0)
                            <button type="button" class="sub-filter-tab" data-filter="inactive">
                                <i class="fas fa-history"></i> Inactive / Past
                                <span class="tab-badge">{{ $inactiveCount }}</span>
                            </button>
                            @endif
                        </div>

                        <div class="sub-search-box">
                            <i class="fas fa-search sub-search-icon"></i>
                            <input type="text" id="subSearchInput" class="sub-search-input" placeholder="Search plan, id, price...">
                        </div>
                    </div>

                    @if($subscriptions->isEmpty())
                        <!-- Empty State -->
                        <div class="sub-empty-state">
                            <div class="sub-empty-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h4 class="sub-empty-title">No Subscriptions Found</h4>
                            <p class="sub-empty-text">You have not subscribed to any plan yet. Get started with our flexible pricing plans designed for your restaurant.</p>
                            <a href="{{ route('restaurant.plans') }}" class="btn-sub-upgrade">
                                <i class="fas fa-rocket"></i> Explore Plans & Pricing
                            </a>
                        </div>
                    @else
                        <!-- Desktop & Tablet Table View -->
                        <div class="sub-table-responsive">
                            <table id="subscriptionsTable" class="sub-table">
                                <thead>
                                    <tr>
                                        <th># ID</th>
                                        <th>Plan Name</th>
                                        <th>Billing Cycle</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Start Date</th>
                                        <th>Expiry Date</th>
                                        <th>Auto-Renew (ON/OFF)</th>
                                        <th style="text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subscriptions as $subscription)
                                    @php
                                        $cycle = strtolower($subscription->plan->billing_cycle ?? 'monthly');
                                        $status = strtolower($subscription->status ?? 'active');
                                        $isFree = ($subscription->plan->price ?? 0) == 0;
                                    @endphp
                                    <tr class="sub-table-row" 
                                        data-timeframe="{{ $cycle }}" 
                                        data-status="{{ $status }}" 
                                        data-search="{{ strtolower(($subscription->plan->name ?? '').' '.$subscription->id.' '.$subscription->status.' '.$cycle) }}">
                                        <td>
                                            <span class="text-muted fw-bold">#{{ $subscription->id }}</span>
                                        </td>
                                        <td>
                                            <div class="plan-cell-title">
                                                <span>{{ $subscription->plan->name ?? 'N/A' }}</span>
                                                @if(isset($subscription->plan->label_name) && $subscription->plan->label_name)
                                                    <span class="plan-label-pill">{{ $subscription->plan->label_name }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($cycle === 'yearly')
                                                <span class="cycle-badge yearly">
                                                    <i class="fas fa-calendar-check"></i> Yearly
                                                </span>
                                            @else
                                                <span class="cycle-badge monthly">
                                                    <i class="fas fa-calendar-alt"></i> Monthly
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isFree)
                                                <span class="sub-free-badge">FREE</span>
                                            @else
                                                <span class="sub-price-text">₹{{ number_format($subscription->plan->price ?? 0, 2) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="sub-status-pill {{ $status }}">
                                                <span class="dot"></span>
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted" style="font-size: 0.84rem;">
                                                <i class="far fa-calendar text-muted me-1"></i>
                                                {{ $subscription->start_date ? \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted" style="font-size: 0.84rem;">
                                                <i class="far fa-calendar-times text-muted me-1"></i>
                                                {{ $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($subscription->status === 'active')
                                                <div class="d-flex align-items-center gap-2">
                                                    <label class="sub-switch" title="Toggle Auto-Renew ON/OFF">
                                                        <input type="checkbox" class="toggle-auto-renew" data-id="{{ $subscription->id }}" {{ $subscription->auto_renew ? 'checked' : '' }}>
                                                        <span class="sub-switch-slider"></span>
                                                    </label>
                                                    <span class="auto-renew-status badge {{ $subscription->auto_renew ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.72rem; border-radius: 12px; padding: 3px 8px;">
                                                        {{ $subscription->auto_renew ? 'ON' : 'OFF' }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="badge bg-light text-muted border" style="font-size: 0.75rem;">
                                                    {{ $subscription->auto_renew ? 'Yes' : 'No' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="sub-actions-cell justify-content-end">
                                                <button type="button" class="sub-btn-action sub-btn-view view-btn" data-id="{{ $subscription->id }}" title="View Plan Details">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                
                                                <a href="{{ route('admin.subscriptions.invoice', $subscription->id) }}" class="sub-btn-action sub-btn-invoice" title="Download Invoice">
                                                    <i class="fas fa-file-invoice"></i> Invoice
                                                </a>

                                                @if(!$isFree && in_array($subscription->status, ['active', 'completed', 'pending', 'halted', 'authenticated']))
                                                    <a href="{{ route('admin.subscriptions.changePaymentMethod', $subscription->id) }}" 
                                                       class="sub-btn-action sub-btn-bank change-bank-btn" 
                                                       data-plan="{{ $subscription->plan->name ?? 'Plan' }}"
                                                       title="Change AutoPay Bank Account / Card">
                                                        <i class="fas fa-university"></i> Bank
                                                    </a>
                                                @endif

                                                @if($subscription->status === 'active')
                                                    <button type="button" class="sub-btn-action sub-btn-cancel cancel-btn" data-id="{{ $subscription->id }}" data-plan="{{ $subscription->plan->name ?? 'N/A' }}" title="Cancel Subscription">
                                                        <i class="fas fa-ban"></i> Cancel
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View (< 992px) -->
                        <div class="sub-mobile-cards">
                            @foreach($subscriptions as $subscription)
                            @php
                                $cycle = strtolower($subscription->plan->billing_cycle ?? 'monthly');
                                $status = strtolower($subscription->status ?? 'active');
                                $isFree = ($subscription->plan->price ?? 0) == 0;
                            @endphp
                            <div class="sub-card-item" 
                                 data-timeframe="{{ $cycle }}" 
                                 data-status="{{ $status }}" 
                                 data-search="{{ strtolower(($subscription->plan->name ?? '').' '.$subscription->id.' '.$subscription->status.' '.$cycle) }}">
                                
                                <div class="sub-card-header">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h5 class="mb-0 fw-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; color: #0f172a;">
                                                {{ $subscription->plan->name ?? 'N/A' }}
                                            </h5>
                                            @if(isset($subscription->plan->label_name) && $subscription->plan->label_name)
                                                <span class="plan-label-pill">{{ $subscription->plan->label_name }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">Subscription #{{ $subscription->id }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="sub-status-pill {{ $status }}">
                                            <span class="dot"></span>
                                            {{ ucfirst($subscription->status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="sub-card-grid">
                                    <div class="sub-card-stat">
                                        <div class="sub-card-stat-label">Price & Cycle</div>
                                        <div class="sub-card-stat-value">
                                            @if($isFree)
                                                <span class="sub-free-badge">FREE</span>
                                            @else
                                                ₹{{ number_format($subscription->plan->price ?? 0, 2) }}
                                            @endif
                                            <span class="badge {{ $cycle == 'yearly' ? 'bg-success' : 'bg-primary' }} text-white ms-1" style="font-size: 0.65rem;">
                                                {{ ucfirst($cycle) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="sub-card-stat">
                                        <div class="sub-card-stat-label">Auto-Renew (ON/OFF)</div>
                                        <div class="sub-card-stat-value d-flex align-items-center gap-2">
                                            @if($subscription->status === 'active')
                                                <label class="sub-switch" style="transform: scale(0.85); transform-origin: left center;">
                                                    <input type="checkbox" class="toggle-auto-renew" data-id="{{ $subscription->id }}" {{ $subscription->auto_renew ? 'checked' : '' }}>
                                                    <span class="sub-switch-slider"></span>
                                                </label>
                                                <span class="auto-renew-status badge {{ $subscription->auto_renew ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.68rem;">
                                                    {{ $subscription->auto_renew ? 'ON' : 'OFF' }}
                                                </span>
                                            @else
                                                <span class="text-muted">{{ $subscription->auto_renew ? 'Yes' : 'No' }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="sub-card-stat">
                                        <div class="sub-card-stat-label">Start Date</div>
                                        <div class="sub-card-stat-value text-muted" style="font-size: 0.8rem;">
                                            <i class="far fa-calendar me-1"></i>
                                            {{ $subscription->start_date ? \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') : 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="sub-card-stat">
                                        <div class="sub-card-stat-label">Expiry Date</div>
                                        <div class="sub-card-stat-value text-muted" style="font-size: 0.8rem;">
                                            <i class="far fa-calendar-times me-1"></i>
                                            {{ $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="sub-card-actions">
                                    <button type="button" class="sub-btn-action sub-btn-view view-btn" data-id="{{ $subscription->id }}">
                                        <i class="fas fa-eye"></i> Details
                                    </button>
                                    
                                    <a href="{{ route('admin.subscriptions.invoice', $subscription->id) }}" class="sub-btn-action sub-btn-invoice">
                                        <i class="fas fa-file-invoice"></i> Invoice
                                    </a>

                                    @if(!$isFree && in_array($subscription->status, ['active', 'completed', 'pending', 'halted', 'authenticated']))
                                        <a href="{{ route('admin.subscriptions.changePaymentMethod', $subscription->id) }}" 
                                           class="sub-btn-action sub-btn-bank change-bank-btn" 
                                           data-plan="{{ $subscription->plan->name ?? 'Plan' }}">
                                            <i class="fas fa-university"></i> Bank
                                        </a>
                                    @endif

                                    @if($subscription->status === 'active')
                                        <button type="button" class="sub-btn-action sub-btn-cancel cancel-btn" data-id="{{ $subscription->id }}" data-plan="{{ $subscription->plan->name ?? 'N/A' }}">
                                            <i class="fas fa-ban"></i> Cancel
                                        </button>
                                    @endif
                                </div>

                            </div>
                            @endforeach
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>

    <!-- Floating Toast Container -->
    <div class="sub-toast-container" id="subToastContainer"></div>

    <!-- View Subscription Modal -->
    <div class="modal fade" id="viewSubscriptionModal" tabindex="-1" aria-labelledby="viewSubscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: var(--sub-radius-lg); overflow: hidden; border: none; box-shadow: var(--sub-shadow-lg);">
                
                <div class="modal-header sub-modal-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div style="background: rgba(255, 94, 20, 0.2); width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ff8c42; font-size: 1.3rem; border: 1px solid rgba(255, 140, 66, 0.3);">
                            <i class="fas fa-gem"></i>
                        </div>
                        <div>
                            <h5 class="sub-modal-title text-white mb-0" id="viewSubscriptionModalLabel" style="color: #ffffff !important; font-family: 'Outfit', sans-serif !important; font-weight: 700 !important; font-size: 1.25rem !important;">
                                Subscription Details
                            </h5>
                            <small class="text-white" style="color: rgba(255, 255, 255, 0.85) !important;" id="modalSubRef">Loading subscription...</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="modalStatusBadge" class="badge bg-success px-3 py-2 text-white" style="font-size: 0.8rem; border-radius: 20px;">Active</span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                
                <div class="modal-body" id="modalSubscriptionBody" style="padding: 1.5rem !important; background: #f8fafc;">
                    <!-- Loading state -->
                    <div id="modalLoadingState" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; color: #ff5e14 !important;">
                            <span class="sr-only">Loading details...</span>
                        </div>
                        <p class="mt-3 text-muted fw-bold">Fetching subscription & plan details...</p>
                    </div>

                    <!-- Content (hidden initially) -->
                    <div id="modalContentState" style="display: none;">
                        
                        <!-- 1. Plan Hero Card -->
                        <div class="sub-modal-hero">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h4 class="mb-0 fw-bold text-dark" style="font-family: 'Outfit', sans-serif;" id="modalPlanName">Plan Name</h4>
                                        <span id="modalPlanLabel" class="plan-label-pill" style="display: none;">Popular</span>
                                    </div>
                                    <p class="text-muted mb-0 small" id="modalPlanDescription" style="max-width: 480px;">Plan description goes here.</p>
                                </div>
                                <div class="text-md-end">
                                    <div class="d-flex align-items-baseline justify-content-md-end gap-1">
                                        <span id="modalPlanCrossPrice" class="text-muted text-decoration-line-through small me-1" style="display: none;">₹999</span>
                                        <h3 class="mb-0 fw-bold" style="color: #ff5e14; font-family: 'Outfit', sans-serif;" id="modalPlanPrice">₹499.00</h3>
                                    </div>
                                    <span class="badge bg-white border text-muted px-2 py-1 mt-1" id="modalPlanBillingCycle">Monthly Plan</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Subscription Timeline & Schedule -->
                        <div class="sub-detail-card">
                            <h6 class="sub-detail-card-title"><i class="fas fa-clock text-primary"></i> Subscription Timeline & Validity</h6>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div class="sub-card-stat-label">Start Date</div>
                                    <div class="sub-card-stat-value" id="modalStartDate"><i class="far fa-calendar-check text-success me-1"></i> --</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="sub-card-stat-label">Expiry Date</div>
                                    <div class="sub-card-stat-value" id="modalEndDate"><i class="far fa-calendar-times text-danger me-1"></i> --</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="sub-card-stat-label">Next Renewal</div>
                                    <div class="sub-card-stat-value" id="modalRenewalDate"><i class="fas fa-redo-alt text-info me-1"></i> --</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="sub-card-stat-label">Auto Renewal (ON/OFF)</div>
                                    <div class="sub-card-stat-value" id="modalAutoRenewStatusWrapper">
                                        <div class="d-flex align-items-center gap-2">
                                            <label class="sub-switch" style="transform: scale(0.9); transform-origin: left center;">
                                                <input type="checkbox" class="toggle-auto-renew" id="modalAutoRenewCheckbox" data-id="">
                                                <span class="sub-switch-slider"></span>
                                            </label>
                                            <span class="auto-renew-status badge bg-success" id="modalAutoRenewStatus" style="font-size: 0.72rem; padding: 3px 8px; border-radius: 12px;">
                                                ON
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <small class="text-muted">
                                    <i class="fas fa-fingerprint me-1"></i> <strong>Gateway Sub ID:</strong> 
                                    <code id="modalRazorpaySubId" style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #0f172a;">--</code>
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-day me-1"></i> <strong>Duration:</strong> 
                                    <span id="modalDurationDays">30</span> Days Validity
                                </small>
                            </div>
                        </div>

                        <!-- 3. Active Bank Account / AutoPay Card -->
                        <div class="sub-detail-card" id="modalBankCardSection" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-left: 4px solid #f59e0b; display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <h6 class="sub-detail-card-title mb-0" style="color: #0f172a;">
                                    <i class="fas fa-university text-warning"></i> Active AutoPay Bank / Payment Method
                                </h6>
                                <span id="modalAutoPayBadge" class="badge bg-success text-white px-2 py-1" style="font-size: 0.75rem; border-radius: 12px;">
                                    <i class="fas fa-check-circle me-1"></i> Auto-Debit Active
                                </span>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded border flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 1.3rem;">
                                        <i id="modalBankIcon" class="fas fa-credit-card"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" id="modalBankType">Visa Credit Card</div>
                                        <div class="text-muted font-monospace" id="modalBankAccount" style="font-size: 0.88rem;">•••• •••• •••• 4366</div>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-muted small">Issuing Bank</div>
                                    <div class="fw-bold text-dark" id="modalBankIssuer">Axis Bank</div>
                                </div>
                                <div class="text-end">
                                    <a id="modalChangeBankCardBtn" href="#" class="btn btn-warning btn-sm change-bank-btn text-white fw-bold px-3 py-1" style="border-radius: 20px;">
                                        <i class="fas fa-exchange-alt me-1"></i> Change
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Payment Information -->
                        <div class="sub-detail-card">
                            <h6 class="sub-detail-card-title"><i class="fas fa-receipt text-primary"></i> Payment Details</h6>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div class="sub-card-stat-label">Amount Paid</div>
                                    <div class="sub-card-stat-value text-success" id="modalPaymentAmount">₹0.00</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="sub-card-stat-label">Payment Status</div>
                                    <div class="sub-card-stat-value" id="modalPaymentStatus"><span class="badge bg-success">Success</span></div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="sub-card-stat-label">Payment Method</div>
                                    <div class="sub-card-stat-value" id="modalPaymentMethod">Online / Gateway</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="sub-card-stat-label">Payment Date</div>
                                    <div class="sub-card-stat-value" id="modalPaymentDate">--</div>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-top row">
                                <div class="col-md-7 mb-2 mb-md-0">
                                    <small class="text-muted">
                                        <i class="fas fa-hashtag me-1"></i> <strong>Razorpay Payment ID:</strong> 
                                        <code id="modalRazorpayPaymentId" style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #0f172a;">--</code>
                                    </small>
                                </div>
                                <div class="col-md-5 text-md-end" id="modalRefundRow" style="display: none;">
                                    <small class="text-info fw-bold">
                                        <i class="fas fa-undo-alt me-1"></i> Refund Credit: <span id="modalRefundAmount">₹0.00</span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Plan Capacity & Limits -->
                        <div class="sub-detail-card">
                            <h6 class="sub-detail-card-title"><i class="fas fa-sliders-h text-primary"></i> Plan Capacity & Resource Limits</h6>
                            <div class="sub-limit-grid">
                                <div class="sub-limit-box">
                                    <div class="sub-limit-box-icon">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <div>
                                        <div class="sub-card-stat-label">Menu Categories</div>
                                        <div class="sub-card-stat-value" id="modalLimitCategories">Unlimited</div>
                                    </div>
                                </div>
                                <div class="sub-limit-box">
                                    <div class="sub-limit-box-icon">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <div>
                                        <div class="sub-card-stat-label">Total Dishes</div>
                                        <div class="sub-card-stat-value" id="modalLimitDishes">Unlimited</div>
                                    </div>
                                </div>
                                <div class="sub-limit-box">
                                    <div class="sub-limit-box-icon">
                                        <i class="fas fa-chair"></i>
                                    </div>
                                    <div>
                                        <div class="sub-card-stat-label">Dining Tables</div>
                                        <div class="sub-card-stat-value" id="modalLimitTables">Unlimited</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Included Features -->
                        <div class="sub-detail-card">
                            <h6 class="sub-detail-card-title"><i class="fas fa-check-double text-primary"></i> Included Features & Modules</h6>
                            
                            <div class="sub-feature-grid mb-3">
                                <div class="sub-feature-pill active">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>Menu Management</span>
                                </div>
                                <div class="sub-feature-pill active">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>Order Management</span>
                                </div>
                                <div class="sub-feature-pill active">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>Kitchen Display (KDS)</span>
                                </div>
                                <div class="sub-feature-pill active">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>QR Digital Ordering</span>
                                </div>
                                <div class="sub-feature-pill active">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>Staff Permissions</span>
                                </div>
                                <div class="sub-feature-pill active">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>Reports & Analytics</span>
                                </div>
                            </div>

                            <div class="sub-card-stat-label mb-2"><i class="fas fa-boxes me-1"></i> Inventory Suite Modules:</div>
                            <div class="sub-feature-grid" id="modalInventorySuiteGrid">
                                <div class="sub-feature-pill" id="featProduct">
                                    <i class="fas fa-circle"></i>
                                    <span>Manage Products</span>
                                </div>
                                <div class="sub-feature-pill" id="featPurchase">
                                    <i class="fas fa-circle"></i>
                                    <span>Manage Purchases</span>
                                </div>
                                <div class="sub-feature-pill" id="featStockout">
                                    <i class="fas fa-circle"></i>
                                    <span>Manage Stockout</span>
                                </div>
                                <div class="sub-feature-pill" id="featDebitNote">
                                    <i class="fas fa-circle"></i>
                                    <span>Debit Note</span>
                                </div>
                                <div class="sub-feature-pill" id="featInventory">
                                    <i class="fas fa-circle"></i>
                                    <span>Real-Time Inventory</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="modal-footer d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #ffffff; padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2">
                        <a href="#" id="modalInvoiceDownloadBtn" class="btn btn-sub-upgrade py-2 px-3" target="_blank" style="font-size: 0.85rem;">
                            <i class="fas fa-file-download me-1"></i> Download Invoice
                        </a>
                        <a href="#" id="modalChangePaymentBtn" class="btn btn-warning btn-sm py-2 px-3 change-bank-btn text-white fw-bold" style="border-radius: 30px; display: none;">
                            <i class="fas fa-university me-1"></i> Change Bank / Card
                        </a>
                    </div>
                    <button type="button" class="btn btn-light btn-sm px-4 py-2" data-bs-dismiss="modal" data-dismiss="modal" style="border-radius: 30px; border: 1px solid #cbd5e1; font-weight: 600;">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: var(--sub-radius-lg); overflow: hidden; border: none; box-shadow: var(--sub-shadow-lg);">
                <div class="modal-header bg-danger text-white py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                        <h5 class="modal-title text-white mb-0 fw-bold" id="cancelModalLabel">Cancel Subscription</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-2">Are you sure you want to cancel your subscription to <strong id="planName" class="text-danger"></strong>?</p>
                    <div class="alert alert-warning mb-0 mt-3 d-flex gap-2">
                        <i class="fas fa-info-circle text-warning mt-1"></i>
                        <div class="small">
                            <strong>Important Note:</strong> Your active features will remain accessible until the end of your billing cycle. No further automatic charges will occur.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <form id="cancelForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary px-3 py-2 rounded-pill" data-bs-dismiss="modal" data-dismiss="modal">Keep Subscription</button>
                        <button type="submit" class="btn btn-danger px-4 py-2 rounded-pill fw-bold">Confirm Cancellation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('includes.script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Toast Notification Helper
            function showToast(message, type = 'success') {
                let icon = type === 'success' ? 'fa-check-circle text-success' : (type === 'warning' ? 'fa-exclamation-triangle text-warning' : 'fa-times-circle text-danger');
                let toastHtml = $('<div class="sub-toast ' + type + '">' +
                    '<i class="fas ' + icon + ' fa-lg"></i>' +
                    '<span>' + message + '</span>' +
                '</div>');

                $('#subToastContainer').append(toastHtml);
                setTimeout(function() {
                    toastHtml.css('animation', 'subToastSlideOut 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards');
                    setTimeout(function() { toastHtml.remove(); }, 320);
                }, 3500);
            }

            // Subscription timeframe & status tab filtering
            $('.sub-filter-tab').on('click', function(e) {
                e.preventDefault();
                $('.sub-filter-tab').removeClass('active');
                $(this).addClass('active');

                applyFilterAndSearch();
            });

            // Search input real-time handler
            $('#subSearchInput').on('keyup input', function() {
                applyFilterAndSearch();
            });

            function applyFilterAndSearch() {
                let activeFilter = $('.sub-filter-tab.active').data('filter') || 'all';
                let query = $('#subSearchInput').val().toLowerCase().trim();

                // Desktop table filtering
                $('.sub-table-row').each(function() {
                    let row = $(this);
                    let rowTf = row.data('timeframe') || '';
                    let rowStatus = row.data('status') || '';
                    let rowSearch = row.data('search') || '';

                    let matchesFilter = false;
                    if (activeFilter === 'all') matchesFilter = true;
                    else if (activeFilter === 'active') matchesFilter = (rowStatus === 'active');
                    else if (activeFilter === 'yearly') matchesFilter = (rowTf === 'yearly');
                    else if (activeFilter === 'monthly') matchesFilter = (rowTf === 'monthly');
                    else if (activeFilter === 'inactive') matchesFilter = ['cancelled', 'expired', 'halted'].indexOf(rowStatus) !== -1;

                    let matchesSearch = (query === '' || rowSearch.indexOf(query) !== -1);

                    if (matchesFilter && matchesSearch) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });

                // Mobile cards filtering
                $('.sub-card-item').each(function() {
                    let card = $(this);
                    let cardTf = card.data('timeframe') || '';
                    let cardStatus = card.data('status') || '';
                    let cardSearch = card.data('search') || '';

                    let matchesFilter = false;
                    if (activeFilter === 'all') matchesFilter = true;
                    else if (activeFilter === 'active') matchesFilter = (cardStatus === 'active');
                    else if (activeFilter === 'yearly') matchesFilter = (cardTf === 'yearly');
                    else if (activeFilter === 'monthly') matchesFilter = (cardTf === 'monthly');
                    else if (activeFilter === 'inactive') matchesFilter = ['cancelled', 'expired', 'halted'].indexOf(cardStatus) !== -1;

                    let matchesSearch = (query === '' || cardSearch.indexOf(query) !== -1);

                    if (matchesFilter && matchesSearch) {
                        card.show();
                    } else {
                        card.hide();
                    }
                });
            }

            // View Details Modal Handler
            $(document).on('click', '.view-btn', function() {
                let id = $(this).data('id');
                
                // Show modal & set loading state
                $('#modalLoadingState').show();
                $('#modalContentState').hide();
                $('#modalSubRef').text('Loading subscription #' + id + '...');
                
                let modalEl = document.getElementById('viewSubscriptionModal');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else {
                    $('#viewSubscriptionModal').modal('show');
                }

                $.ajax({
                    url: '{{ url("admin/subscriptions") }}/' + id + '/details',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data) {
                            let data = response.data;
                            let plan = data.plan || {};
                            let payment = data.payment || {};

                            // Header meta
                            $('#modalSubRef').text('Subscription ID: #SUB-' + String(data.id).padStart(6, '0'));
                            
                            // Status badge
                            let statusClass = 'bg-secondary';
                            if (data.status === 'active') statusClass = 'bg-success';
                            else if (data.status === 'cancelled') statusClass = 'bg-danger';
                            else if (data.status === 'expired') statusClass = 'bg-warning text-dark';
                            
                            $('#modalStatusBadge')
                                .removeClass('bg-success bg-danger bg-warning bg-secondary text-dark text-white')
                                .addClass(statusClass + ' text-white')
                                .text(data.status_badge || data.status);

                            // Plan hero
                            $('#modalPlanName').text(plan.name || 'N/A');
                            if (plan.label_name) {
                                $('#modalPlanLabel').text(plan.label_name).show();
                            } else {
                                $('#modalPlanLabel').hide();
                            }
                            $('#modalPlanDescription').text(plan.description || 'All-in-one restaurant POS plan.');
                            $('#modalPlanPrice').text(plan.formatted_price || '₹0.00');
                            
                            if (plan.cross_price) {
                                $('#modalPlanCrossPrice').text(plan.cross_price).show();
                            } else {
                                $('#modalPlanCrossPrice').hide();
                            }
                            $('#modalPlanBillingCycle').text((plan.billing_cycle || 'Monthly') + ' Plan');

                            // Timeline
                            $('#modalStartDate').html('<i class="far fa-calendar-check text-success me-1"></i> ' + (data.start_date || 'N/A'));
                            $('#modalEndDate').html('<i class="far fa-calendar-times text-danger me-1"></i> ' + (data.end_date || 'N/A'));
                            $('#modalRenewalDate').html('<i class="fas fa-redo-alt text-info me-1"></i> ' + (data.renewal_date || 'N/A'));
                            
                            // Auto-renew toggle in modal
                            $('#modalAutoRenewCheckbox').data('id', data.id).prop('checked', !!data.auto_renew);
                            if (data.auto_renew) {
                                $('#modalAutoRenewStatus').text('ON').removeClass('bg-secondary bg-info').addClass('bg-success');
                            } else {
                                $('#modalAutoRenewStatus').text('OFF').removeClass('bg-success bg-info').addClass('bg-secondary');
                            }

                            $('#modalRazorpaySubId').text(data.razorpay_subscription_id || 'N/A');
                            $('#modalDurationDays').text(plan.duration_days || '30');

                            // Payment details
                            $('#modalPaymentAmount').text(payment.formatted_amount || '₹0.00');
                            
                            let payStatusClass = 'bg-secondary';
                            if (payment.status && payment.status.toLowerCase() === 'success') payStatusClass = 'bg-success';
                            else if (payment.status && payment.status.toLowerCase() === 'pending') payStatusClass = 'bg-warning text-dark';
                            else if (payment.status && payment.status.toLowerCase() === 'failed') payStatusClass = 'bg-danger';

                            $('#modalPaymentStatus').html('<span class="badge ' + payStatusClass + ' text-white">' + (payment.status || 'N/A') + '</span>');
                            $('#modalPaymentMethod').text(payment.payment_method || 'Online');
                            $('#modalPaymentDate').text(payment.payment_date || 'N/A');
                            $('#modalRazorpayPaymentId').text(payment.razorpay_payment_id || 'N/A');

                            // AutoPay Bank Account / Card Section
                            if (payment && payment.has_active_autopay) {
                                $('#modalBankType').text(payment.method_type || 'Active Card / Bank');
                                $('#modalBankAccount').text(payment.account_number || '•••• •••• •••• 4366');
                                $('#modalBankIssuer').text(payment.bank_name || 'Payment Gateway');
                                
                                let iconClass = payment.method_icon || 'fa-credit-card';
                                $('#modalBankIcon').attr('class', 'fas ' + iconClass);

                                if (data.auto_renew) {
                                    $('#modalAutoPayBadge').html('<i class="fas fa-check-circle me-1"></i> Auto-Debit Active').removeClass('bg-secondary bg-danger').addClass('bg-success text-white');
                                } else {
                                    $('#modalAutoPayBadge').html('<i class="fas fa-pause-circle me-1"></i> Auto-Debit Disabled').removeClass('bg-success').addClass('bg-secondary text-white');
                                }

                                $('#modalChangeBankCardBtn').attr('href', payment.change_bank_url || '{{ url("admin/subscriptions") }}/' + data.id + '/change-payment-method');
                                $('#modalBankCardSection').show();
                            } else {
                                $('#modalBankCardSection').hide();
                            }

                            if (payment.refund_amount && payment.refund_amount > 0) {
                                $('#modalRefundAmount').text('₹' + parseFloat(payment.refund_amount).toFixed(2));
                                $('#modalRefundRow').show();
                            } else {
                                $('#modalRefundRow').hide();
                            }

                            // Resource limits
                            $('#modalLimitCategories').text(plan.category_display || 'Unlimited');
                            $('#modalLimitDishes').text(plan.dish_display || 'Unlimited');
                            $('#modalLimitTables').text(plan.table_display || 'Unlimited');

                            // Inventory suite items
                            let isInventory = !!plan.inventory_enabled;
                            let invIcon = isInventory ? '<i class="fas fa-check-circle text-success me-1"></i>' : '<i class="fas fa-times-circle text-danger me-1"></i>';
                            let invClass = isInventory ? 'active' : 'inactive';

                            ['featProduct', 'featPurchase', 'featStockout', 'featDebitNote', 'featInventory'].forEach(function(featId) {
                                let box = $('#' + featId);
                                box.removeClass('active inactive').addClass(invClass);
                                let title = box.find('span').text();
                                box.html(invIcon + '<span>' + title + '</span>');
                            });

                            // Invoice download button
                            $('#modalInvoiceDownloadBtn').attr('href', data.invoice_url || '#');

                            // Change Payment Method button in modal
                            if (plan && plan.price > 0 && ['active', 'completed', 'pending', 'halted', 'authenticated'].indexOf(data.status) !== -1) {
                                $('#modalChangePaymentBtn')
                                    .attr('href', '{{ url("admin/subscriptions") }}/' + data.id + '/change-payment-method')
                                    .data('plan', plan.name || 'Plan')
                                    .show();
                            } else {
                                $('#modalChangePaymentBtn').hide();
                            }

                            // Hide loader & display content
                            $('#modalLoadingState').hide();
                            $('#modalContentState').fadeIn(200);
                        } else {
                            $('#modalLoadingState').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i> Failed to retrieve subscription details.</div>');
                        }
                    },
                    error: function(xhr) {
                        $('#modalLoadingState').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i> Error loading subscription details. Please try again.</div>');
                    }
                });
            });

            // Change Bank / Card button confirmation
            $(document).on('click', '.change-bank-btn, #modalChangePaymentBtn', function(e) {
                let plan = $(this).data('plan') || 'your subscription';
                let msg = '💳 AutoPay Mandate Setup for ' + plan + ':\n\n' +
                          '• Why is the amount shown? Razorpay requires authorizing your plan\'s renewal mandate limit.\n' +
                          '• Will it deduct immediately? NO. Your current paid plan validity and remaining days will remain 100% active. Future automatic renewals will only be charged when your current billing cycle expires.\n\n' +
                          'Do you want to continue to Razorpay secure mandate authentication?';
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });

            // Cancel button
            $(document).on('click', '.cancel-btn', function() {
                let id = $(this).data('id');
                let planName = $(this).data('plan');
                
                $('#planName').text(planName);
                $('#cancelForm').attr('action', '{{ url("admin/subscriptions") }}/' + id + '/cancel');
                
                let cancelModalEl = document.getElementById('cancelModal');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(cancelModalEl).show();
                } else {
                    $('#cancelModal').modal('show');
                }
            });

            // Toggle Auto-Renew AJAX Handler with Synchronized State across Page
            $(document).on('change', '.toggle-auto-renew', function() {
                let checkbox = $(this);
                let id = checkbox.data('id');
                if (!id) return;

                let allSwitchesForId = $('.toggle-auto-renew[data-id="' + id + '"]');
                let isChecked = checkbox.is(':checked');

                // Optimistically update all badges for this subscription
                allSwitchesForId.prop('checked', isChecked);
                allSwitchesForId.each(function() {
                    let badge = $(this).closest('div').find('.auto-renew-status');
                    badge.text('Updating...').removeClass('bg-success bg-secondary').addClass('bg-info text-white');
                });
                
                $.ajax({
                    url: '{{ url("admin/subscriptions") }}/' + id + '/toggle-auto-renew',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            let isEnabled = !!response.auto_renew;
                            allSwitchesForId.prop('checked', isEnabled);
                            allSwitchesForId.each(function() {
                                let badge = $(this).closest('div').find('.auto-renew-status');
                                if (isEnabled) {
                                    badge.text('ON').removeClass('bg-info bg-secondary').addClass('bg-success text-white');
                                } else {
                                    badge.text('OFF').removeClass('bg-info bg-success').addClass('bg-secondary text-white');
                                }
                            });

                            showToast(response.message || ('Auto-renew turned ' + (isEnabled ? 'ON' : 'OFF')), isEnabled ? 'success' : 'warning');
                        } else {
                            // Revert on failure
                            allSwitchesForId.prop('checked', !isChecked);
                            allSwitchesForId.each(function() {
                                let badge = $(this).closest('div').find('.auto-renew-status');
                                badge.text(!isChecked ? 'ON' : 'OFF')
                                    .removeClass('bg-info')
                                    .addClass(!isChecked ? 'bg-success text-white' : 'bg-secondary text-white');
                            });
                            showToast(response.message || 'Failed to toggle auto-renew.', 'error');
                        }
                    },
                    error: function(xhr) {
                        // Revert on error
                        allSwitchesForId.prop('checked', !isChecked);
                        allSwitchesForId.each(function() {
                            let badge = $(this).closest('div').find('.auto-renew-status');
                            badge.text(!isChecked ? 'ON' : 'OFF')
                                .removeClass('bg-info')
                                .addClass(!isChecked ? 'bg-success text-white' : 'bg-secondary text-white');
                        });
                        showToast('Error connecting to server. Please try again.', 'error');
                    }
                });
            });
        });
    </script>
</body>
</html>