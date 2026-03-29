<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Plans - RestoPOS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a237e;
            --primary-light: #534bae;
            --primary-dark: #000051;
            --secondary: #ff6f00;
            --secondary-light: #ffa040;
            --secondary-dark: #c43e00;
            --accent: #00b0ff;
            --light: #f8f9ff;
            --dark: #121212;
            --gray: #8a8d93;
            --success: #4caf50;
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 30px 60px rgba(0, 0, 0, 0.12);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
            color: var(--dark);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .plans-container {
            padding: 40px 0 80px 0;
            position: relative;
        }
        
        /* Background decorative elements */
        .bg-decoration {
            position: absolute;
            z-index: -1;
        }
        
        .bg-circle-1 {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(26, 35, 126, 0.05) 0%, rgba(255, 111, 0, 0.05) 100%);
            top: -150px;
            right: -150px;
        }
        
        .bg-circle-2 {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(0, 176, 255, 0.05) 0%, rgba(76, 175, 80, 0.05) 100%);
            bottom: -100px;
            left: -100px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            padding-top: 20px;
        }
        
        .page-header h1 {
            color: var(--primary);
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }
        
        .page-header h1:after {
            content: '';
            position: absolute;
            width: 60%;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            bottom: -10px;
            left: 20%;
            border-radius: 2px;
        }
        
        .page-header .subtitle {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 25px auto 0;
            line-height: 1.6;
        }
        
        .user-welcome {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 20px;
            box-shadow: 0 8px 16px rgba(26, 35, 126, 0.15);
            font-size: 0.95rem;
        }
        
        .user-welcome i {
            color: var(--secondary-light);
            margin-right: 8px;
        }
        
        /* Plan cards */
        .plan-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            position: relative;
            border: none;
            margin-bottom: 30px;
        }
        
        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }
        
        .plan-card.popular {
            border: 2px solid var(--secondary);
            z-index: 10;
        }
        
        .popular-badge {
            position: absolute;
            top: 15px;
            right: -10px;
            background: linear-gradient(135deg, var(--secondary), var(--secondary-dark));
            color: white;
            padding: 6px 20px;
            border-radius: 20px 5px 5px 20px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(255, 111, 0, 0.3);
            z-index: 20;
        }
        
        .popular-badge:before {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            width: 0;
            height: 0;
            border-left: 10px solid var(--secondary-dark);
            border-top: 8px solid transparent;
        }
        
        .plan-header {
            padding: 30px 20px 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .plan-header:before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
            z-index: 1;
        }
        
        .plan-header-content {
            position: relative;
            z-index: 2;
        }
        
        .plan-name {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 10px;
        }
        
        .plan-price {
            font-size: 2.8rem;
            font-weight: 800;
            color: white;
            margin: 8px 0;
            line-height: 1;
        }
        
        .plan-price sup {
            font-size: 1.2rem;
            font-weight: 600;
            top: -1.2rem;
        }
        
        .plan-duration {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            margin-top: 5px;
        }
        
        .plan-body {
            padding: 30px 20px;
            text-align: center;
        }
        
        .plan-description {
            color: var(--gray);
            line-height: 1.6;
            margin-bottom: 25px;
            min-height: 60px;
            font-size: 0.95rem;
        }
        
        /* Plan color variations */
        .plan-basic .plan-header:before {
            background: linear-gradient(135deg, #607d8b, #455a64);
        }
        
        .plan-premium .plan-header:before {
            background: linear-gradient(135deg, var(--secondary), var(--secondary-dark));
        }
        
        .plan-enterprise .plan-header:before {
            background: linear-gradient(135deg, #9c27b0, #7b1fa2);
        }
        
        /* Buttons */
        .btn-select {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 14px 25px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            width: 100%;
            max-width: 280px;
            transition: all 0.3s;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 20px rgba(26, 35, 126, 0.2);
            display: block;
            margin: 0 auto;
        }
        
        .btn-select:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(26, 35, 126, 0.3);
            color: white;
        }
        
        .btn-secondary {
            background: #e9ecef;
            color: #6c757d;
            padding: 14px 25px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            max-width: 280px;
            border: none;
            display: block;
            margin: 0 auto;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #212529;
            padding: 14px 25px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            max-width: 280px;
            border: none;
            display: block;
            margin: 0 auto;
        }
        
        /* Success alert */
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border: none;
            border-radius: 15px;
            border-left: 5px solid var(--success);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.1);
            margin-bottom: 30px;
        }
        
        /* Back link */
        .back-link {
            text-align: center;
            margin-top: 50px;
        }
        
        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            border: 2px solid var(--primary);
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            font-size: 0.95rem;
        }
        
        .back-link a:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(26, 35, 126, 0.2);
        }
        
        .back-link a i {
            margin-right: 8px;
        }
        
        a {
            text-decoration: none;
        }
        
        /* Animation for cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .plan-card {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }
        
        .plan-card:nth-child(1) { animation-delay: 0.1s; }
        .plan-card:nth-child(2) { animation-delay: 0.2s; }
        .plan-card:nth-child(3) { animation-delay: 0.3s; }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .plans-container {
                padding: 30px 0 60px 0;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .page-header .subtitle {
                font-size: 1rem;
                padding: 0 15px;
            }
            
            .user-welcome {
                font-size: 0.9rem;
                padding: 10px 16px;
            }
            
            .plan-header {
                padding: 25px 15px 20px;
            }
            
            .plan-body {
                padding: 25px 15px;
            }
            
            .plan-name {
                font-size: 1.3rem;
            }
            
            .plan-price {
                font-size: 2rem;
            }
            
            .btn-select, .btn-secondary, .btn-warning {
                padding: 12px 20px;
                font-size: 0.9rem;
                max-width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .plan-price {
                font-size: 1.8rem;
            }
            
            .plan-description {
                font-size: 0.9rem;
            }
            
            .back-link a {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
        }
    </style>
</head>
<body>
    @auth
    <div class="plans-container">
        <!-- Background decorations -->
        <div class="bg-decoration bg-circle-1 d-none d-lg-block"></div>
        <div class="bg-decoration bg-circle-2 d-none d-md-block"></div>
        
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-gem me-3"></i>Premium Plans</h1>
                <p class="subtitle">Elevate your restaurant management with our premium plans. Choose the perfect solution tailored for your business needs.</p>
                
                <div class="user-welcome">
                    <i class="fas fa-user-check"></i> Welcome, {{ Auth::user()->name }}! Restaurant: {{ Auth::user()->restaurant->name ?? 'Your Restaurant' }}
                </div>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif
            
            <div class="row justify-content-center">
                @foreach($plans as $plan)
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
                    <div class="plan-card {{ $loop->index == 1 ? 'popular' : '' }} plan-{{ $plan->name == 'Premium' ? 'premium' : ($plan->name == 'Enterprise' ? 'enterprise' : 'basic') }}">
                        @if($loop->index == 1)
                            {{-- <div class="popular-badge">RECOMMENDED</div> --}}
                        @endif
                        
                        <div class="plan-header">
                            <div class="plan-header-content">
                                <h3 class="plan-name">{{ $plan->name }}</h3>
                                <div class="plan-price">₹ {{ $plan->price }}</div>
                                <div class="plan-duration">Per {{ $plan->duration_days }} days</div>
                            </div>
                        </div>
                        
                        <div class="plan-body">
                            <div class="plan-description">
                                {{ $plan->description }}
                            </div>
                            
                            <?php
                                $user = auth()->user();
                                $hasFreeTrial = \App\Models\Subscription::where('user_id', $user->id)
                                    ->whereHas('plan', function($query) {
                                        $query->where('price', 0);
                                    })
                                    ->exists();
                                
                                $isActive = \App\Models\Subscription::where('user_id', $user->id)
                                    ->where('plan_id', $plan->id)
                                    ->where('status', 'active')
                                    ->exists();
                            ?>
                            
                            @if($isActive)
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-check-circle me-2"></i> Current Plan
                                </button>
                            @elseif($plan->price == 0 && $hasFreeTrial)
                                <button class="btn btn-warning" disabled title="You have already used your free trial">
                                    <i class="fas fa-ban me-2"></i> Trial Already Used
                                </button>
                            @else
                                <a href="{{ route('admin.subscriptions.create', $plan->id) }}" 
                                   class="btn btn-select">
                                    <i class="fas fa-shopping-cart me-2"></i> 
                                    {{ $plan->price == 0 ? 'Start Free Trial' : 'Subscribe Now' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="back-link">
                <a href="{{ route('logout') }}"><i class="fas fa-arrow-left me-2"></i> Logout</a>
            </div>
        </div>
    </div>
    @else
    <div class="container text-center py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-danger" style="border-radius: 15px; padding: 30px;">
                    <h4><i class="fas fa-exclamation-triangle me-2"></i> Authentication Required</h4>
                    <p>Please log in to view our premium plans.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary mt-3">Login to Continue</a>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Enhanced animation for plan cards
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effect to cards
            const planCards = document.querySelectorAll('.plan-card');
            planCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.zIndex = '100';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.zIndex = '1';
                });
            });
        });
    </script>
</body>
</html>