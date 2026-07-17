<!DOCTYPE html>
<html lang="en">
<head>
    <title>Subscribe to {{ $plan->name }} - RestoPOS</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
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
            --warning: #f59e0b;
            --warning-bg: #fffbeb;
            --warning-border: #f59e0b;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow-card: 0 4px 20px rgba(0,0,0,0.03);
            --shadow-hover: 0 10px 30px rgba(0,0,0,0.06);
        }

        body {
            background-color: var(--bg-light) !important;
            color: var(--text-primary);
        }

        .checkout-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .checkout-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 30px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .checkout-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255, 106, 0, 0.15), transparent 75%);
            border-radius: 50%;
        }

        .checkout-header h3 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 700;
            color: white;
            position: relative;
            z-index: 1;
        }

        .checkout-header p {
            margin: 8px 0 0 0;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }

        .checkout-body {
            padding: 35px;
        }

        /* Modern Details List */
        .info-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
        }

        .info-section-title i {
            color: var(--primary);
        }

        .details-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background-color: #f8fafc;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            transition: all 0.25s ease;
        }

        .detail-item:hover {
            background-color: #ffffff;
            border-color: rgba(255, 106, 0, 0.25);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            transform: translateY(-1px);
        }

        .detail-icon {
            width: 44px;
            height: 44px;
            background-color: #fff0e6;
            color: var(--primary);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .detail-text {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .detail-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .detail-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* Pricing highlight */
        .detail-value.price-tag {
            color: var(--primary);
            font-size: 1.1rem;
        }

        /* User profile cards */
        .user-detail-icon {
            background-color: #e0f2fe;
            color: #0284c7;
        }

        /* Premium Warning Alert */
        .alert-premium {
            background-color: var(--warning-bg);
            border: 1px solid #fde68a;
            border-left: 4px solid var(--warning);
            border-radius: var(--radius-md);
            padding: 20px;
            color: #92400e;
            display: flex;
            gap: 15px;
            align-items: flex-start;
            margin-top: 20px;
        }

        .alert-premium i {
            font-size: 1.3rem;
            color: var(--warning);
            margin-top: 2px;
        }

        .alert-premium-content {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .alert-premium-title {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .alert-premium-desc {
            font-size: 0.85rem;
            line-height: 1.5;
            color: #b45309;
        }

        /* Checkout Actions */
        .checkout-actions-wrapper {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-submit-checkout {
            background: linear-gradient(to right, var(--primary), #ff8c42);
            color: white !important;
            border: none;
            padding: 15px 35px;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(255, 106, 0, 0.2);
            cursor: pointer;
        }

        .btn-submit-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 106, 0, 0.35);
        }

        .btn-submit-checkout:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn-cancel-checkout {
            background-color: var(--surface);
            color: var(--text-secondary) !important;
            border: 1px solid var(--border);
            padding: 15px 30px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-cancel-checkout:hover {
            background-color: #f1f5f9;
            color: var(--text-primary) !important;
            transform: translateY(-1px);
        }

        .btn-update-profile {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--primary);
            color: white !important;
            border: none;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.85rem;
            margin-top: 10px;
            align-self: flex-start;
            transition: all 0.25s;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(255, 106, 0, 0.15);
        }

        .btn-update-profile:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 106, 0, 0.25);
            text-decoration: none;
        }
    </style>
</head>
<body data-pc-theme="light">
    <!-- Loader -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <!-- Sidebar -->
    @include('includes.sidebar')

    <!-- Main Container -->
    <div class="pc-container">
        <div class="pc-content">
            
            <!-- Breadcrumb -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Subscription Setup</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('select.plan.page') }}">Plans</a></li>
                                <li class="breadcrumb-item" aria-current="page">Subscribe</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Breadcrumb end -->

            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12">
                    <div class="checkout-card">
                        
                        <!-- Header banner -->
                        <div class="checkout-header">
                            <h3><i class="fas fa-shopping-basket me-2"></i>Review Subscription Checkout</h3>
                            <p>Verify your selected plan details and store information before proceeding to secure payment</p>
                        </div>
                        
                        <!-- Body -->
                        <div class="checkout-body">
                            
                            @include('includes.message')
                            
                            <div class="row">
                                <!-- Left Column: Plan Details -->
                                <div class="col-md-6 mb-4 mb-md-0">
                                    <h5 class="info-section-title"><i class="fas fa-gem"></i> Plan Specifications</h5>
                                    
                                    <div class="details-grid">
                                        <div class="detail-item">
                                            <div class="detail-icon"><i class="fas fa-bookmark"></i></div>
                                            <div class="detail-text">
                                                <span class="detail-label">Selected Plan</span>
                                                <span class="detail-value">{{ $plan->name }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-icon"><i class="fas fa-tag"></i></div>
                                            <div class="detail-text">
                                                <span class="detail-label">Plan Price</span>
                                                <span class="detail-value price-tag">
                                                    @if($plan->price == 0)
                                                        FREE
                                                    @else
                                                        ₹{{ number_format($plan->price, 2) }} {{ $plan->currency }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-icon"><i class="fas fa-history"></i></div>
                                            <div class="detail-text">
                                                <span class="detail-label">Billing Frequency</span>
                                                <span class="detail-value">{{ ucfirst($plan->billing_cycle) }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-icon"><i class="fas fa-clock"></i></div>
                                            <div class="detail-text">
                                                <span class="detail-label">Validity Period</span>
                                                <span class="detail-value">{{ $plan->duration_days }} Days</span>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-icon"><i class="fas fa-info-circle"></i></div>
                                            <div class="detail-text">
                                                <span class="detail-label">Description</span>
                                                <span class="detail-value" style="font-weight: 500; font-size: 0.85rem;">
                                                    {{ $plan->description ?? 'All standard package features included.' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Column: Billing Information -->
                                <div class="col-md-6">
                                    <h5 class="info-section-title"><i class="fas fa-user-circle"></i> Subscriber Details</h5>
                                    
                                    <div class="details-grid">
                                        <div class="detail-item">
                                            <div class="detail-icon user-detail-icon" style="background-color: #f0fdf4; color: #15803d;"><i class="fas fa-store"></i></div>
                                            <div class="detail-text">
                                                <span class="detail-label">Restaurant Name</span>
                                                <span class="detail-value">{{ $user->restaurant->name ?? 'Your Restaurant' }}</span>
                                            </div>
                                        </div>
                                         
                                        <div class="detail-item">
                                            <div class="detail-icon user-detail-icon"><i class="fas fa-user"></i></div>
                                            <div class="detail-text">
                                                <span class="detail-label">Authorized Contact</span>
                                                <span class="detail-value">{{ $user->name }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-icon user-detail-icon"><i class="fas fa-envelope"></i></div>
                                            <div class="detail-text">
                                                <span class="detail-label">Notification Email</span>
                                                <span class="detail-value">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-icon user-detail-icon"><i class="fas fa-phone-alt"></i></div>
                                            <div class="detail-text">
                                                <span class="detail-label">Mobile Number</span>
                                                <span class="detail-value">{{ $user->phone ?? 'Not Registered' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Phone number registration warning -->
                                    @if(!$user->phone)
                                        <div class="alert-premium">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <div class="alert-premium-content">
                                                <span class="alert-premium-title">Contact Number Required</span>
                                                <span class="alert-premium-desc">
                                                    A valid mobile number is required by the payment gateway to complete transactions securely. Please register your contact number now.
                                                </span>
                                                <a href="{{ route('restaurant.profile.index') }}" class="btn-update-profile">
                                                    <i class="fas fa-user-edit"></i> Edit Profile
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="checkout-actions-wrapper">
                                <form action="{{ route('admin.subscriptions.store', $plan->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    
                                    @if(!$user->phone)
                                        <button type="button" class="btn-submit-checkout" disabled title="Please update your profile contact number to proceed.">
                                            <i class="fas fa-lock-open me-2"></i> Register Mobile Number to Pay
                                        </button>
                                    @else
                                        <button type="submit" class="btn-submit-checkout">
                                            <i class="fas fa-shield-alt"></i> Proceed to Secure Payment
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('select.plan.page') }}" class="btn-cancel-checkout">
                                        <i class="fas fa-arrow-left"></i> Return to Plans
                                    </a>
                                </form>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('includes.script')
</body>
</html>