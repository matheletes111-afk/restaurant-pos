<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill&Bite - Complete Restaurant Management System</title>
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{asset('frontend/style.css')}}">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="#" class="logo">
                <img src="{{ asset('logo.png') }}" alt="Bill&Bite Logo" style="height: 40px; width: auto;">
            </a>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#pricing">Pricing</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#platform-benefits">Benefits</a>
                <a href="#faq">FAQ</a>
            </div>
            <div class="nav-actions">
                <a href="javascript:void(0)" class="btn-login open-enquiry-btn">Book A Demo</a>
                {{-- <a href="#" class="btn btn-primary">Book a Demo</a> --}}
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container hero-container">
            <div class="hero-content">
                {{-- <div class="badge">
                    <span class="badge-dot"></span> Complete Restaurant Management System
                </div> --}}
                <h1 style="font-size: 44px;">Simplify <span class="text-orange">Every Operation</span> For Your Restaurant.</h1>
                <p>Bill & Bite is an all-in-one restaurant management system that helps you manage orders, staff, kitchen, inventory, payments and more — from one powerful dashboard.</p>
                
                <div class="hero-features">
                    <div class="hf-item">
                        <div class="hf-icon"><i class="ph ph-puzzle-piece"></i></div>
                        <span>Easy To Use</span>
                    </div>
                    <div class="hf-item">
                        <div class="hf-icon"><i class="ph ph-squares-four"></i></div>
                        <span style="white-space: nowrap;">All-In-One Solution</span>
                    </div>
                    <div class="hf-item">
                        <div class="hf-icon"><i class="ph ph-cloud-arrow-up"></i></div>
                        <span>Cloud Based</span>
                    </div>
                    <div class="hf-item">
                        <div class="hf-icon"><i class="ph ph-shield-check"></i></div>
                        <span style="white-space: nowrap;">Secure & Reliable</span>
                    </div>
                </div>

                <div class="hero-cta">
                    <a href="javascript:void(0)" class="btn btn-primary btn-lg open-enquiry-btn">Book a Free Demo <i class="ph ph-arrow-right"></i></a>
                    <a href="javascript:void(0)" class="btn btn-outline btn-lg open-enquiry-btn">Explore Features</a>
                </div>

                {{-- <div class="trust-indicator">
                    <div class="avatars">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=100" alt="User 1">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=100" alt="User 2">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=100" alt="User 3">
                    </div>
                    <div class="trust-text">
                        <span>Trusted by 1000+ Restaurants</span>
                        <div class="stars">
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star-half"></i>
                            <span class="rating">4.8/5</span>
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="hero-graphics">
                <!-- Using the hero mockup from public directory -->
                <div class="graphic-wrapper" style="display: flex; justify-content: center; align-items: center;">
                    <img src="{{ asset('new_banner.png') }}" alt="Dashboard and Mobile Mockup" style="max-width: 100%; height: auto; border-radius: 12px;">
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-subtitle">POWERFUL FEATURES</span>
                <h2>Everything You Need to <span class="text-orange">Run Your Restaurant</span></h2>
                <p>From order management to inventory and analytics, we've got everything covered.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-receipt"></i></div>
                    <h3>Order Management</h3>
                    <p>Manage dine-in, takeaway, online orders seamlessly in one place.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-users"></i></div>
                    <h3>Staff Management</h3>
                    <p>Manage staff roles, attendance, performance and permissions.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-chef-hat"></i></div>
                    <h3>Kitchen Management</h3>
                    <p>Streamline KOT, track order status and improve kitchen efficiency.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-package"></i></div>
                    <h3>Inventory Management</h3>
                    <p>Track stock in real time, get low stock alerts and reduce wastage.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-qr-code"></i></div>
                    <h3>QR Code Ordering</h3>
                    <p>Let customers scan, order and pay from their table effortlessly.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-credit-card"></i></div>
                    <h3>Payments & Invoices</h3>
                    <p>Accept multiple payments and generate invoices in seconds.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-moped"></i></div>
                    <h3>Delivery Management</h3>
                    <p>Track delivery agents, live status and ensure timely deliveries.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-chart-line-up"></i></div>
                    <h3>Reports & Analytics</h3>
                    <p>Get detailed insights on sales, orders, staff, inventory and more.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Smart POS Features Section -->
    <section class="smart-pos" id="smart-pos" style="padding: 80px 0; background-color: #fff;">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 60px;">
                <span class="section-subtitle" style="color: #ef4444; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; font-size: 0.85rem;">SMART POS FEATURES</span>
                <h2 style="font-size: 2.5rem; margin-top: 10px; margin-bottom: 15px; color: #1f2937;">A restaurant POS made for<br>all your needs</h2>
                <p style="color: #6b7280; max-width: 600px; margin: 0 auto; font-size: 1.05rem;">A quick and easy-to-use restaurant billing software that makes managing high order volumes butter smooth</p>
            </div>

            <style>
                .spf-row {
                    display: flex;
                    align-items: center;
                    gap: 50px;
                    margin-bottom: 80px;
                }
                .spf-row:last-child {
                    margin-bottom: 0;
                }
                .spf-row.reverse {
                    flex-direction: row-reverse;
                }
                .spf-image-col {
                    flex: 1;
                    background-color: #ef4444;
                    border-radius: 12px;
                    padding: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 350px;
                    position: relative;
                    overflow: hidden;
                }
                .spf-image-col.bg-light-red {
                    background-color: #fcd3d3;
                }
                .spf-image-col img {
                    max-width: 90%;
                    height: auto;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                }
                .spf-text-col {
                    flex: 1;
                }
                .spf-text-col h3 {
                    font-size: 1.8rem;
                    color: #1f2937;
                    margin-bottom: 15px;
                }
                .spf-text-col h3 span {
                    color: #ef4444;
                }
                .spf-text-col p {
                    color: #6b7280;
                    line-height: 1.6;
                    margin-bottom: 20px;
                    font-size: 1rem;
                }
                .spf-explore-link {
                    display: inline-flex;
                    align-items: center;
                    color: #111;
                    font-weight: 600;
                    text-decoration: none;
                    font-size: 0.95rem;
                    transition: color 0.3s;
                }
                .spf-explore-link:hover {
                    color: #ef4444;
                }
                .spf-explore-link i {
                    margin-left: 5px;
                    font-size: 1.1rem;
                }

                @media (max-width: 768px) {
                    .spf-row, .spf-row.reverse {
                        flex-direction: column;
                        gap: 30px;
                        margin-bottom: 50px;
                    }
                    .spf-image-col {
                        width: 100%;
                        min-height: 250px;
                    }
                }
            </style>

            <div class="spf-row">
                <div class="spf-image-col">
                    <img src="{{asset('bill_new.png')}}" style="max-width:100% !important" alt="Billing POS" style="object-fit: cover;">
                </div>
                <div class="spf-text-col">
                    <h3>A quick 3-click restaurant <span>billing</span> software</h3>
                    <p>Take orders, punch bills and generate KOT. Accept payments either by splitting bill or merging tables. Easily apply discounts and coupons. All of this, and more, is easy and quick with Bill&Bite's restaurant POS.</p>
                    <a href="javascript:void(0)" class="spf-explore-link open-enquiry-btn">Explore all features <i class="ph ph-arrow-right"></i></a>
                </div>
            </div>

            <div class="spf-row reverse">
                <div class="spf-image-col bg-light-red">
                    <img src="{{asset('inventory_new.png')}}" style="max-width:100% !important" alt="Inventory Management" style="object-fit: cover;">
                </div>
                <div class="spf-text-col">
                    <h3>Restaurant <span>inventory</span> management made easier</h3>
                    <p>Do inventory management the smart way. Put your inventory on the item-wise auto deduction, get low-stock alerts, day-end inventory reports and more with Bill&Bite restaurant POS.</p>
                    <a href="javascript:void(0)" class="spf-explore-link open-enquiry-btn">Explore all features <i class="ph ph-arrow-right"></i></a>
                </div>
            </div>

            <div class="spf-row">
                <div class="spf-image-col">
                    <img src="{{asset('report_new.png')}}" style="max-width:100% !important" alt="Reports and Analytics" style="object-fit: cover;">
                </div>
                <div class="spf-text-col">
                    <h3>Get real-time restaurant <span>Reports</span></h3>
                    <p>Automate your restaurant reports and go paper-free! Let Bill&Bite POS automatically track your business activities and provide you error-free reports on your restaurant's day-end sales, online orders, staff actions, inventory consumption, and various 80+ essential business reports.</p>
                    <a href="javascript:void(0)" class="spf-explore-link open-enquiry-btn">Explore all features <i class="ph ph-arrow-right"></i></a>
                </div>
            </div>

            <div class="spf-row reverse">
                <div class="spf-image-col bg-light-red">
                    <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=600" alt="Online Ordering System" style="object-fit: cover;">
                </div>
                <div class="spf-text-col">
                    <h3>A single <span>Online Ordering</span> System to manage all your orders</h3>
                    <p>Accept online orders, manage online menu, mark food ready, collect payment and check revenue without shuffling between multiple screens.</p>
                    <a href="javascript:void(0)" class="spf-explore-link open-enquiry-btn">Explore all features <i class="ph ph-arrow-right"></i></a>
                </div>
            </div>

        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits">
        <div class="container benefits-container">
            <div class="benefits-content">
                <span class="section-subtitle text-left">WHY CHOOSE BILL & BITE?</span>
                <h2>Built to Help You<br><span class="text-orange">Serve Better</span> & Grow Faster</h2>
                <p>Bill & Bite helps you save time, reduce errors and increase profitability — so you can focus on what matters most, delighting your customers.</p>
                
                <ul class="benefits-list">
                    <li><i class="ph-fill ph-check-circle text-orange"></i> Easy to set up and use</li>
                    <li><i class="ph-fill ph-check-circle text-orange"></i> Accessible from anywhere, anytime</li>
                    <li><i class="ph-fill ph-check-circle text-orange"></i> Secure cloud backup of your data</li>
                    <li><i class="ph-fill ph-check-circle text-orange"></i> Regular updates and dedicated support</li>
                </ul>
            </div>
            <div class="benefits-image">
                <div class="b-img-wrapper">
                    <img src="{{asset('bb.png')}}" alt="Restaurant Manager" class="main-b-img">
                    
                    <!-- Floating UI Elements -->
                    <div class="floating-card revenue-card">
                        <div class="fc-header">Today's Revenue</div>
                        <div class="fc-amount">₹ 1,24,560</div>
                    </div>
                    <div class="floating-card orders-card">
                        <div class="fc-header">Orders</div>
                        <div class="fc-amount">249 <span class="trend up">+ 3.2%</span></div>
                    </div>
                    <div class="floating-card item-card">
                        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&q=80&w=100" alt="Pizza">
                        <div class="ic-text">
                            <span class="ic-title">Top Item</span>
                            <span class="ic-name">Paneer Pizza</span>
                            <span class="ic-sales">120 Orders</span>
                        </div>
                    </div>
                    <div class="floating-card alert-card">
                        <i class="ph ph-warning-circle text-orange"></i>
                        <div class="ac-text">
                            <span class="ac-title">Low Stock Alert</span>
                            <span class="ac-name">Tomato Sauce</span>
                            <span class="ac-desc">Only 2 Ltr</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container stats-container">
            <div class="stat-item">
                <i class="ph ph-rocket-launch"></i>
                <h3>< 10 Mins</h3>
                <p>Quick Setup & Launch</p>
            </div>
            <div class="stat-item">
                <i class="ph ph-chef-hat"></i>
                <h3>Real-Time</h3>
                <p>Track Food Status in Kitchen</p>
            </div>
            <div class="stat-item">
                <i class="ph ph-shield-check"></i>
                <h3>99.9%</h3>
                <p>Uptime & Reliability</p>
            </div>
            <div class="stat-item">
                <i class="ph ph-headset"></i>
                <h3>24/7</h3>
                <p>Dedicated Support</p>
            </div>
        </div>
    </section>

    <!-- Customer Platform Benefits Section -->
    <section class="platform-benefits" id="platform-benefits" style="padding: 80px 0; background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 50px;">
                <span class="section-subtitle" style="color: #ff6a00; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; font-size: 0.85rem;">WHY CHOOSE OUR PLATFORM</span>
                <h2 style="font-size: 2.3rem; margin-top: 10px; margin-bottom: 15px; color: #111827;">Designed for <span class="text-orange">Maximum Efficiency</span> & Delight</h2>
                <p style="color: #6b7280; max-width: 650px; margin: 0 auto; font-size: 1.05rem;">Discover how our platform delivers an exceptional experience for both restaurant staff and dining customers.</p>
            </div>

            <style>
                .benefits-grid-cards {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                    gap: 25px;
                }
                .benefit-card-item {
                    background: #ffffff;
                    border-radius: 16px;
                    padding: 32px 24px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
                    border: 1px solid #f3f4f6;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                }
                .benefit-card-item::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 4px;
                    background: linear-gradient(90deg, #ff6a00, #ff8c42);
                    opacity: 0;
                    transition: opacity 0.3s ease;
                }
                .benefit-card-item:hover {
                    transform: translateY(-6px);
                    box-shadow: 0 20px 40px rgba(255, 106, 0, 0.12);
                    border-color: #ffe4d6;
                }
                .benefit-card-item:hover::before {
                    opacity: 1;
                }
                .b-card-icon {
                    width: 56px;
                    height: 56px;
                    border-radius: 12px;
                    background: rgba(255, 106, 0, 0.1);
                    color: #ff6a00;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 28px;
                    margin-bottom: 20px;
                    transition: all 0.3s ease;
                }
                .benefit-card-item:hover .b-card-icon {
                    background: #ff6a00;
                    color: #ffffff;
                }
                .b-card-title {
                    font-size: 1.25rem;
                    font-weight: 700;
                    color: #111827;
                    margin-bottom: 12px;
                }
                .b-card-desc {
                    font-size: 0.95rem;
                    color: #6b7280;
                    line-height: 1.6;
                    flex-grow: 1;
                }
                .b-card-badge {
                    display: inline-block;
                    margin-top: 18px;
                    padding: 4px 12px;
                    border-radius: 20px;
                    background-color: #fff4ed;
                    color: #ff6a00;
                    font-size: 0.8rem;
                    font-weight: 600;
                    align-self: flex-start;
                }
            </style>

            <div class="benefits-grid-cards">
                <div class="benefit-card-item">
                    <div class="b-card-icon"><i class="ph ph-qr-code"></i></div>
                    <h3 class="b-card-title">Instant Digital QR Ordering</h3>
                    <p class="b-card-desc">Customers can view menus, customize dishes, and place orders directly from their phone with zero waiting time.</p>
                    <span class="b-card-badge">Speed & Convenience</span>
                </div>

                <div class="benefit-card-item">
                    <div class="b-card-icon"><i class="ph ph-lightning"></i></div>
                    <h3 class="b-card-title">Real-Time Kitchen Sync</h3>
                    <p class="b-card-desc">Orders stream directly to KOT displays instantly, eliminating human errors and speeding up preparation.</p>
                    <span class="b-card-badge">Zero Errors</span>
                </div>

                <div class="benefit-card-item">
                    <div class="b-card-icon"><i class="ph ph-package"></i></div>
                    <h3 class="b-card-title">Automated Inventory Management</h3>
                    <p class="b-card-desc">Track stock ingredients in real time, enable item-wise auto-deduction, and get instant low-stock alerts.</p>
                    <span class="b-card-badge">Smart Stock Control</span>
                </div>

                <div class="benefit-card-item">
                    <div class="b-card-icon"><i class="ph ph-chart-bar"></i></div>
                    <h3 class="b-card-title">Data-Driven Insights</h3>
                    <p class="b-card-desc">Track customer preferences, peak dining hours, and top-selling dishes to boost profit margins effortlessly.</p>
                    <span class="b-card-badge">Smart Analytics</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing / Packages Section -->
    <section class="pricing" id="pricing" style="padding: 80px 0; background-color: #f9fafb;">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 50px;">
                <span class="section-subtitle">PRICING PLANS</span>
                <h2>Choose the Right <span class="text-orange">Package</span></h2>
                <p>Simple and transparent pricing for restaurants of all sizes.</p>
            </div>

            <style>
                .plans-wrapper {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 30px;
                    max-width: 1000px;
                    margin: 0 auto;
                }
                .plan-card {
                    background: #fff;
                    border-radius: 16px;
                    padding: 40px 30px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                    border: 1px solid #eee;
                    transition: all 0.3s ease;
                    position: relative;
                }
                .plan-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                    border-color: #ff6a00;
                }
                .plan-card.default-plan {
                    border: 2px solid #ff6a00;
                }
                .default-badge {
                    position: absolute;
                    top: -15px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: linear-gradient(135deg, #ff6a00, #ff8c42);
                    color: white;
                    padding: 6px 16px;
                    border-radius: 20px;
                    font-size: 0.8rem;
                    font-weight: bold;
                }
                .plan-header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 1px solid #eee;
                }
                .plan-name {
                    font-size: 1.5rem;
                    font-weight: 700;
                    margin-bottom: 15px;
                    color: #111;
                }
                .plan-price {
                    font-size: 2.5rem;
                    font-weight: 800;
                    color: #ff6a00;
                }
                .plan-duration {
                    color: #666;
                    font-size: 0.9rem;
                    margin-top: 10px;
                }
                .plan-features-list {
                    list-style: none;
                    padding: 0;
                    margin: 0 0 30px 0;
                }
                .plan-features-list li {
                    padding: 10px 0;
                    color: #555;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .plan-features-list i {
                    color: #ff6a00;
                }
                .btn-plan {
                    display: block;
                    width: 100%;
                    text-align: center;
                    padding: 14px;
                    border-radius: 8px;
                    background: #fff;
                    border: 2px solid #ff6a00;
                    color: #ff6a00;
                    font-weight: 600;
                    transition: all 0.3s;
                    text-decoration: none;
                }
                .btn-plan:hover, .plan-card.default-plan .btn-plan {
                    background: linear-gradient(135deg, #ff6a00, #ff8c42);
                    color: white;
                }
            </style>

            <div class="plans-wrapper">
                @if(isset($defaultPlans) && $defaultPlans->count() > 0)
                    @foreach($defaultPlans as $plan)
                        <div class="plan-card {{ $plan->is_default_plan == 'Y' ? 'default-plan' : '' }}">
                            @if($plan->is_default_plan == 'Y')
                                <div class="default-badge">Popular Choice</div>
                            @endif
                            <div class="plan-header">
                                <h3 class="plan-name">{{ $plan->name }}</h3>
                                {{-- <div class="plan-price">
                                    @if($plan->price == 0)
                                        FREE
                                    @else
                                        ₹{{ number_format($plan->price, 2) }}
                                    @endif
                                </div> --}}
                                <div class="plan-duration">
                                    <i class="ph ph-calendar"></i> {{ $plan->duration_days }} days validity
                                </div>
                            </div>
                            <ul class="plan-features-list">
                                <li><i class="ph-fill ph-check-circle"></i> {{ $plan->category_number == 0 ? 'Unlimited' : $plan->category_number }} Categories</li>
                                <li><i class="ph-fill ph-check-circle"></i> {{ $plan->total_number_of_dishes == 0 ? 'Unlimited' : $plan->total_number_of_dishes }} Dishes</li>
                                <li><i class="ph-fill ph-check-circle"></i> {{ $plan->total_number_of_table == 0 ? 'Unlimited' : $plan->total_number_of_table }} Tables</li>
                                @if($plan->inventory_checkbox == 'Y')
                                    <li><i class="ph-fill ph-check-circle" style="color: #2e7d32;"></i> Inventory</li>
                                @else
                                    <li><i class="ph-fill ph-x-circle" style="color: #d32f2f;"></i> Inventory</li>
                                @endif
                            </ul>
                            <a href="javascript:void(0)" class="btn-plan open-enquiry-btn">Get Started</a>
                        </div>
                    @endforeach
                @else
                    <div class="text-center w-100 p-5" style="background:#fff; border-radius:16px;">
                        <p class="text-muted">Packages will be displayed here.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works" style="padding: 80px 0;">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 50px;">
                <span class="section-subtitle">PROCESS</span>
                <h2>How It <span class="text-orange">Works</span></h2>
                <p>Getting started with Bill & Bite is quick and easy.</p>
            </div>
            
            <style>
                .steps-container {
                    display: flex;
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 30px;
                    position: relative;
                }
                .step-card {
                    flex: 1;
                    min-width: 250px;
                    text-align: center;
                    padding: 30px 20px;
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
                    border: 1px solid #f0f0f0;
                    position: relative;
                    z-index: 1;
                }
                .step-number {
                    width: 60px;
                    height: 60px;
                    background: linear-gradient(135deg, #ff6a00, #ff8c42);
                    color: white;
                    font-size: 1.5rem;
                    font-weight: bold;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 20px auto;
                    box-shadow: 0 8px 16px rgba(255, 106, 0, 0.2);
                }
                .step-card h3 {
                    font-size: 1.25rem;
                    margin-bottom: 15px;
                    color: #111;
                }
                .step-card p {
                    color: #666;
                    font-size: 0.95rem;
                    line-height: 1.5;
                }
            </style>
            
            <div class="steps-container">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>Sign Up</h3>
                    <p>Create your restaurant account in minutes. No credit card required to start.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>Setup Profile</h3>
                    <p>Add your menu, tables, and staff members to your new dashboard.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>Choose Plan</h3>
                    <p>Select the package that best fits your restaurant's size and needs.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h3>Start Managing</h3>
                    <p>Go live and streamline your entire restaurant operation effortlessly.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq" id="faq" style="padding: 80px 0; background-color: #f9fafb;">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 50px;">
                <span class="section-subtitle">QUESTIONS</span>
                <h2>Frequently Asked <span class="text-orange">Questions</span></h2>
                <p>Find answers to common questions about our platform.</p>
            </div>
            
            <style>
                .faq-container {
                    max-width: 800px;
                    margin: 0 auto;
                }
                .faq-item {
                    background: white;
                    border-radius: 10px;
                    margin-bottom: 15px;
                    border: 1px solid #eee;
                    box-shadow: 0 4px 10px rgba(0,0,0,0.02);
                    overflow: hidden;
                }
                .faq-question {
                    padding: 20px 25px;
                    font-weight: 600;
                    font-size: 1.1rem;
                    color: #111;
                    cursor: pointer;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .faq-question:hover {
                    color: #ff6a00;
                }
                .faq-answer {
                    padding: 0 25px;
                    max-height: 0;
                    overflow: hidden;
                    transition: all 0.3s ease;
                    color: #555;
                    line-height: 1.6;
                }
                .faq-item.active .faq-answer {
                    padding: 0 25px 20px 25px;
                    max-height: 500px;
                }
                .faq-icon {
                    transition: transform 0.3s;
                }
                .faq-item.active .faq-icon {
                    transform: rotate(180deg);
                }
            </style>
            
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        Is it difficult to set up the system?
                        <i class="ph ph-caret-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        Not at all! Our system is designed to be user-friendly. You can set up your entire restaurant profile, including menus and tables, in under 30 minutes. We also provide full support if you need assistance.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        Can I use the system on multiple devices?
                        <i class="ph ph-caret-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        Yes, Bill & Bite is a cloud-based solution. You and your staff can access it from tablets, mobile phones, or desktop computers simultaneously without any issues.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        Do you take a commission on my orders?
                        <i class="ph ph-caret-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        No, we do not charge any commission on your orders. You simply pay a flat subscription fee based on the package you choose, allowing you to keep 100% of your profits.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        What if I need help or run into issues?
                        <i class="ph ph-caret-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        We offer dedicated customer support to help you resolve any issues quickly. You can reach out to us via phone or email, and our team will assist you promptly.
                    </div>
                </div>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const faqItems = document.querySelectorAll('.faq-item');
                    faqItems.forEach(item => {
                        const question = item.querySelector('.faq-question');
                        question.addEventListener('click', () => {
                            // Close other open FAQs
                            faqItems.forEach(otherItem => {
                                if (otherItem !== item && otherItem.classList.contains('active')) {
                                    otherItem.classList.remove('active');
                                }
                            });
                            // Toggle current FAQ
                            item.classList.toggle('active');
                        });
                    });
                    
                    // Smooth scrolling for nav links
                    document.querySelectorAll('.nav-links a[href^="#"]').forEach(anchor => {
                        anchor.addEventListener('click', function (e) {
                            e.preventDefault();
                            const targetId = this.getAttribute('href');
                            if (targetId === '#') return;
                            
                            const targetElement = document.querySelector(targetId);
                            if (targetElement) {
                                window.scrollTo({
                                    top: targetElement.offsetTop - 80, // offset for fixed navbar if any
                                    behavior: 'smooth'
                                });
                            }
                        });
                    });
                });
            </script>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="contact-form">
        <div class="container cta-container">
            <div class="cta-content">
                <h2>Ready to <span class="text-orange">Simplify</span> Your<br>Restaurant Operations?</h2>
                <p>Book a free demo and see how Bill & Bite can help your restaurant grow.</p>
                <ul class="cta-list">
                    <li><i class="ph ph-check text-orange"></i> Free Personalized Demo</li>
                    <li><i class="ph ph-check text-orange"></i> No Credit Card Required</li>
                    <li><i class="ph ph-check text-orange"></i> Setup in Minutes</li>
                </ul>
            </div>
            <div class="cta-form-wrapper">
                <div class="cta-form-card">
                    <h3>Book A Free Demo</h3>
                    <form class="demo-form" id="demoLeadForm">
                        @csrf
                        <div id="form-alert" style="display: none; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 500; font-size: 0.9rem; text-align: center;"></div>
                        
                        <div class="form-group-row">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" placeholder="Enter name" required>
                            </div>
                            <div class="form-group">
                                <label>Restaurant Name</label>
                                <input type="text" name="restaurant_name" placeholder="Enter restaurant name">
                            </div>
                        </div>
                        <div class="form-group-row">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" name="phone_number" placeholder="Enter mobile">
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email_address" placeholder="Enter email" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>How did you hear about us?</label>
                            <select name="source">
                                <option value="">Select an option</option>
                                <option value="Social Media">Social Media</option>
                                <option value="Search Engine">Search Engine</option>
                                <option value="Friend/Colleague">Friend/Colleague</option>
                            </select>
                        </div>
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-block">Book Free Demo <i class="ph ph-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" style="background:white !important;">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="logo mb-4">
                        <img src="{{ asset('logo.png') }}" alt="Bill&Bite Logo" style="height: 40px; width: auto;">
                    </a>
                    <p>Bill & Bite is a complete restaurant management system designed to simplify your operations and help your business grow.</p>
                    <div class="social-links">
                        <a href="#"><i class="ph-fill ph-facebook-logo"></i></a>
                        <a href="#"><i class="ph-fill ph-instagram-logo"></i></a>
                        <a href="#"><i class="ph-fill ph-linkedin-logo"></i></a>
                        <a href="#"><i class="ph-fill ph-youtube-logo"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#testimonials">Testimonials</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="javascript:void(0)" class="open-enquiry-btn">Enquiry Now</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('terms.conditions') }}">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contact Us</h4>
                    <ul>
                        <li><i class="ph ph-phone"></i> +91 7001769472</li>
                        <li><i class="ph ph-envelope-simple"></i> info@billnbite.com</li>
                        <li><i class="ph ph-map-pin"></i> Siliguri, India</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 Bill & Bite. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('demoLeadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const alertDiv = document.getElementById('form-alert');
            
            // Get form data
            const formData = new FormData(form);
            
            // Disable button and show loading status
            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Submitting...';
            
            // Hide previous alerts
            alertDiv.style.display = 'none';
            alertDiv.textContent = '';
            
            // Post data to /book-demo
            fetch('{{ route("book.demo") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                
                alertDiv.style.display = 'block';
                if (res.status === 200 && res.body.success) {
                    // Success!
                    alertDiv.style.backgroundColor = '#d1fae5';
                    alertDiv.style.color = '#065f46';
                    alertDiv.style.border = '1px solid #10b981';
                    alertDiv.textContent = res.body.message;
                    form.reset();
                } else {
                    // Validation or other error
                    alertDiv.style.backgroundColor = '#fee2e2';
                    alertDiv.style.color = '#991b1b';
                    alertDiv.style.border = '1px solid #ef4444';
                    alertDiv.textContent = res.body.message || 'Something went wrong. Please check your details.';
                }
            })
            .catch(err => {
                console.error(err);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                
                alertDiv.style.display = 'block';
                alertDiv.style.backgroundColor = '#fee2e2';
                alertDiv.style.color = '#991b1b';
                alertDiv.style.border = '1px solid #ef4444';
                alertDiv.textContent = 'A network error occurred. Please try again later.';
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swiper = new Swiper('.testimonials-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                },
            });
        });
    </script>
    <style>
        .sticky-enquiry-btn {
            position: fixed;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            background-color: #ff6a00;
            color: white;
            padding: 20px 10px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px 0 0 8px;
            cursor: pointer;
            z-index: 9999;
            box-shadow: -4px 0 10px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sticky-enquiry-btn:hover {
            background-color: #e65c00;
            padding-right: 15px;
        }
        .enquiry-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 2000;
            display: none;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .enquiry-modal-overlay.show {
            display: flex;
            opacity: 1;
        }
        .enquiry-modal {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            position: relative;
            transform: translateY(-30px);
            transition: transform 0.3s ease;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        .enquiry-modal-overlay.show .enquiry-modal {
            transform: translateY(0);
        }
        .close-modal-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease;
            line-height: 1;
        }
        .close-modal-btn:hover {
            color: #333;
        }
        .enquiry-modal h3 {
            margin-bottom: 25px;
            text-align: center;
            color: #111;
            font-size: 1.5rem;
        }
        .demo-form input::placeholder {
            font-size: 12px;
            opacity: 0.7;
        }
        .demo-form input::-webkit-input-placeholder {
            font-size: 12px;
            opacity: 0.7;
        }
        .demo-form input::-moz-placeholder {
            font-size: 12px;
            opacity: 0.7;
        }
        .demo-form input:-ms-input-placeholder {
            font-size: 12px;
            opacity: 0.7;
        }
    </style>

    <!-- Sticky Enquiry Button -->
    <div class="sticky-enquiry-btn open-enquiry-btn" id="openEnquiryModalBtn">
        Enquiry Now
    </div>





    <!-- Enquiry Modal -->
    <div class="enquiry-modal-overlay" id="enquiryModalOverlay">
        <div class="enquiry-modal">
            <button class="close-modal-btn" id="closeEnquiryModalBtn">&times;</button>
            <h3>Enquiry Now</h3>
            <form class="demo-form" id="popupEnquiryForm">
                @csrf
                <div id="popup-form-alert" style="display: none; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 500; font-size: 0.9rem; text-align: center;"></div>
                
                <div class="form-group-row">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" placeholder="Enter name" required>
                    </div>
                    <div class="form-group">
                        <label>Restaurant Name</label>
                        <input type="text" name="restaurant_name" placeholder="Enter restaurant name">
                    </div>
                </div>
                <div class="form-group-row">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone_number" placeholder="Enter mobile">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email_address" placeholder="Enter email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>How did you hear about us?</label>
                    <select name="source">
                        <option value="">Select an option</option>
                        <option value="Social Media">Social Media</option>
                        <option value="Search Engine">Search Engine</option>
                        <option value="Friend/Colleague">Friend/Colleague</option>
                    </select>
                </div>
                <button type="submit" id="popupSubmitBtn" class="btn btn-primary btn-block" style="width: 100%;">Submit Enquiry <i class="ph ph-arrow-right"></i></button>
            </form>
        </div>
    </div>

    <script>
        // Scroll Logic instead of Modal
        const enquiryBtns = document.querySelectorAll('.open-enquiry-btn, #openEnquiryModalBtn');
        
        enquiryBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const targetElement = document.getElementById('contact-form');
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        const enquiryModal = document.getElementById('enquiryModalOverlay');
        const closeModal = () => {
            if (enquiryModal) {
                enquiryModal.classList.remove('show');
                setTimeout(() => {
                    enquiryModal.style.display = 'none';
                }, 300);
            }
        };

        if (enquiryModal) {
            enquiryModal.addEventListener('click', (e) => {
                if (e.target === enquiryModal) {
                    closeModal();
                }
            });
        }

        // Form Submit Logic
        document.getElementById('popupEnquiryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('popupSubmitBtn');
            const alertDiv = document.getElementById('popup-form-alert');
            
            const formData = new FormData(form);
            
            submitBtn.disabled = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Submitting...';
            
            alertDiv.style.display = 'none';
            alertDiv.textContent = '';
            
            fetch('{{ route("book.demo") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                
                alertDiv.style.display = 'block';
                if (res.status === 200 && res.body.success) {
                    alertDiv.style.backgroundColor = '#d1fae5';
                    alertDiv.style.color = '#065f46';
                    alertDiv.style.border = '1px solid #10b981';
                    alertDiv.textContent = res.body.message;
                    form.reset();
                    setTimeout(() => {
                        closeModal();
                        alertDiv.style.display = 'none';
                    }, 3000);
                } else {
                    alertDiv.style.backgroundColor = '#fee2e2';
                    alertDiv.style.color = '#991b1b';
                    alertDiv.style.border = '1px solid #ef4444';
                    alertDiv.textContent = res.body.message || 'Something went wrong. Please check your details.';
                }
            })
            .catch(err => {
                console.error(err);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                
                alertDiv.style.display = 'block';
                alertDiv.style.backgroundColor = '#fee2e2';
                alertDiv.style.color = '#991b1b';
                alertDiv.style.border = '1px solid #ef4444';
                alertDiv.textContent = 'A network error occurred. Please try again later.';
            });
        });
    </script>
</body>
</html>
