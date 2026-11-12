<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.gtm_head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill&Bite - Complete Bar, Pub & Restaurant POS Management System</title>
    <meta name="description" content="Bill&Bite Bar POS is a complete bar and restaurant management system with ML liquid stock tracking, peg variations, brand management, Bar KOT, and table QR ordering.">
    <link rel="canonical" href="{{ url('/bar') }}">
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('frontend/style.css') }}">
    <style>
        .badge-bar-tag {
            background: linear-gradient(135deg, #ff6a00 0%, #ff8c42 100%);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }
        .hero-bar-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff3eb;
            color: #ff6a00;
            border: 1px solid #fed7aa;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .text-orange {
            color: #ff6a00;
        }
        .bar-feature-highlight {
            border-left: 3px solid #ff6a00;
            background: #fffaf5;
        }
    </style>
</head>
<body>
@include('includes.gtm_body')

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ asset('logo.png') }}" alt="Bill&Bite Logo" style="height: 40px; width: auto;">
            </a>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="{{ url('/bar') }}" style="color: #ff6a00; font-weight: 700;"><i class="ph ph-martini"></i> Bar POS</a>
                <a href="#pricing">Pricing</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#platform-benefits">Benefits</a>
                <a href="#faq">FAQ</a>
                <a href="{{ route('blog.index') }}">Blog</a>
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
                <div class="hero-bar-badge">
                    <i class="ph-fill ph-martini"></i> Complete Bar, Pub & Restaurant Management System
                </div>
                <h1 style="font-size: 44px;">Run Your Bar & Restaurant with <span class="text-orange">Precision & Speed</span>.</h1>
                <p>Bill&Bite Bar POS gives you complete control over liquid stock down to the milliliter (ML), peg & bottle variations, bar KOTs, liquor VAT compliance, and integrated restaurant dining — all from one powerful cloud dashboard.</p>
                
                <div class="hero-features">
                    <div class="hf-item">
                        <div class="hf-icon"><i class="ph ph-drop"></i></div>
                        <span style="white-space: nowrap;">ML Stock Tracking</span>
                    </div>
                    <div class="hf-item">
                        <div class="hf-icon"><i class="ph ph-martini"></i></div>
                        <span style="white-space: nowrap;">Peg Variations</span>
                    </div>
                    <div class="hf-item">
                        <div class="hf-icon"><i class="ph ph-receipt"></i></div>
                        <span>Bar KOT Counter</span>
                    </div>
                    <div class="hf-item">
                        <div class="hf-icon"><i class="ph ph-shield-check"></i></div>
                        <span style="white-space: nowrap;">Excise & VAT Ready</span>
                    </div>
                </div>

                <div class="hero-cta">
                    <a href="javascript:void(0)" class="btn btn-primary btn-lg open-enquiry-btn">Book a Free Demo <i class="ph ph-arrow-right"></i></a>
                    <a href="#bar-features" class="btn btn-outline btn-lg">Explore Bar Features</a>
                </div>
            </div>
            <div class="hero-graphics">
                <div class="graphic-wrapper" style="display: flex; justify-content: center; align-items: center;">
                    <img src="{{ asset('new_banner.png') }}" alt="Bar & Restaurant POS Interface" style="max-width: 100%; height: auto; border-radius: 12px;">
                </div>
            </div>
        </div>
    </header>

    <!-- Bar Facilities & Core Features Section -->
    <section class="features" id="features">
        <span id="bar-features"></span>
        <div class="container">
            <div class="section-header text-center">
                <span class="section-subtitle">DEDICATED BAR & RESTAURANT FACILITIES</span>
                <h2>Built for Bars, Pubs, Lounges & <span class="text-orange">Resto-Bars</span></h2>
                <p>Manage liquor inventory, rapid peak-hour orders, food kitchen operations, and multi-tax billing without breaking a sweat.</p>
            </div>
            
            <div class="features-grid">
                <!-- Bar Feature 1 -->
                <div class="feature-card bar-feature-highlight">
                    <div class="feature-icon"><i class="ph ph-drop"></i></div>
                    <h3>Liquid Stock & ML Inventory</h3>
                    <p>Track spirits, beers and wines in Milliliters (ML). Exact liquid deduction automatically happens when drinks are marked served.</p>
                </div>

                <!-- Bar Feature 2 -->
                <div class="feature-card bar-feature-highlight">
                    <div class="feature-icon"><i class="ph ph-brandy"></i></div>
                    <h3>Peg & Bottle Variations</h3>
                    <p>Configure 30ml, 60ml, 90ml pegs, tower drinks, and full bottles with custom pricing and automatic volume math.</p>
                </div>

                <!-- Bar Feature 3 -->
                <div class="feature-card bar-feature-highlight">
                    <div class="feature-icon"><i class="ph ph-squares-four"></i></div>
                    <h3>Brand & Brewery Cataloging</h3>
                    <p>Organize premium whiskies, rums, vodkas, gins, craft beers, and cocktails by brands, categories, and bottle sizes.</p>
                </div>

                <!-- Bar Feature 4 -->
                <div class="feature-card bar-feature-highlight">
                    <div class="feature-icon"><i class="ph ph-desktop"></i></div>
                    <h3>Dedicated Bar KOT Screen</h3>
                    <p>Direct beverage orders straight to the bar counter screen while food orders route to the kitchen simultaneously.</p>
                </div>

                <!-- Bar Feature 5 -->
                <div class="feature-card bar-feature-highlight">
                    <div class="feature-icon"><i class="ph ph-percent"></i></div>
                    <h3>Liquor VAT & Food GST Billing</h3>
                    <p>Comply with state excise tax. Automatic tax split for Food GST and Liquor VAT on the same customer invoice.</p>
                </div>

                <!-- Bar Feature 6 -->
                <div class="feature-card bar-feature-highlight">
                    <div class="feature-icon"><i class="ph ph-lightning"></i></div>
                    <h3>Rapid POS for Peak Hours</h3>
                    <p>High-speed one-page counter billing designed for crowded weekend nights, fast bar tabs, and split payments.</p>
                </div>

                <!-- Restaurant Feature 1 -->
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-qr-code"></i></div>
                    <h3>Table QR Code Ordering</h3>
                    <p>Let guests scan the QR code from their table or booth to view drink menus, custom cocktails, and order instantly.</p>
                </div>

                <!-- Restaurant Feature 2 -->
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-chef-hat"></i></div>
                    <h3>Kitchen Display System (KDS)</h3>
                    <p>Streamline food preparation with live order timers, item-ready alerts, and zero missed food tickets.</p>
                </div>

                <!-- Restaurant Feature 3 -->
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-users"></i></div>
                    <h3>Bartender & Staff Controls</h3>
                    <p>Role-based security permissions for bartenders, captains, and managers to eliminate unauthorized pours and edits.</p>
                </div>

                <!-- Restaurant Feature 4 -->
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-trash"></i></div>
                    <h3>Spillage & Wastage Audit</h3>
                    <p>Record bar spillage, breakage, expired kegs, and tasting samples to keep physical stock and ledger 100% matched.</p>
                </div>

                <!-- Restaurant Feature 5 -->
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-chart-line-up"></i></div>
                    <h3>Bar & Resto Sales Analytics</h3>
                    <p>Detailed reports on liquor consumption in ML, top-selling drinks, food vs. liquor revenue, and bartender performance.</p>
                </div>

                <!-- Restaurant Feature 6 -->
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-buildings"></i></div>
                    <h3>Multi-Outlet & Multi-Bar Chains</h3>
                    <p>Centrally control recipes, liquor pricing, inter-outlet stock transfers, and consolidated franchise reports.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Smart POS Walkthrough for Bars Section -->
    <section class="smart-pos" id="how-it-works" style="padding: 80px 0; background-color: #fff;">
        <span id="bar-facilities"></span>
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 60px;">
                <span class="section-subtitle" style="color: #ff6a00; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-size: 0.85rem;">SMART BAR FACILITIES</span>
                <h2 style="font-size: 2.5rem; margin-top: 10px; margin-bottom: 15px; color: #1f2937;">Engineered Specifically for<br><span class="text-orange">High-Volume Beverage Operations</span></h2>
                <p style="color: #6b7280; max-width: 650px; margin: 0 auto; font-size: 1.05rem;">Eliminate liquor pilferage, speed up drink dispensing, and keep kitchen and bar teams operating in sync.</p>
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
                    flex: 1.1;
                    background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
                    border: 1px solid #fed7aa;
                    border-radius: 20px;
                    padding: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 320px;
                    position: relative;
                    box-shadow: 0 16px 36px rgba(255, 106, 0, 0.12);
                }
                .spf-image-col img {
                    width: 100%;
                    max-width: 100%;
                    height: auto;
                    border-radius: 12px;
                    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.14);
                    border: 1px solid rgba(0, 0, 0, 0.06);
                    transition: transform 0.4s ease;
                    display: block;
                }
                .spf-image-col:hover img {
                    transform: translateY(-4px) scale(1.02);
                }
                .spf-text-col {
                    flex: 1;
                }
                .spf-text-col h3 {
                    font-size: 2rem;
                    color: #1f2937;
                    margin-bottom: 20px;
                    font-weight: 700;
                    line-height: 1.3;
                }
                .spf-text-col h3 span {
                    color: #ff6a00;
                }
                .spf-text-col p {
                    color: #4b5563;
                    font-size: 1.05rem;
                    line-height: 1.7;
                    margin-bottom: 25px;
                }
                .spf-points {
                    list-style: none;
                    padding: 0;
                    margin: 0 0 25px 0;
                }
                .spf-points li {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.95rem;
                    font-weight: 600;
                    color: #1f2937;
                    margin-bottom: 12px;
                }
                .spf-points li i {
                    color: #ff6a00;
                    font-size: 1.25rem;
                }
                .spf-explore-link {
                    color: #ff6a00;
                    font-weight: 700;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    text-decoration: none;
                    transition: gap 0.2s;
                }
                .spf-explore-link:hover {
                    gap: 12px;
                }
                @media (max-width: 768px) {
                    .spf-row, .spf-row.reverse {
                        flex-direction: column !important;
                        gap: 28px !important;
                        margin-bottom: 50px !important;
                    }
                    .spf-image-col {
                        min-height: auto !important;
                        padding: 14px !important;
                        width: 100% !important;
                    }
                    .spf-text-col h3 {
                        font-size: 1.5rem !important;
                    }
                }
            </style>

            <!-- Row 1: Liquid Stock & Peg Math -->
            <div class="spf-row">
                <div class="spf-image-col">
                    <img src="{{ asset('01.png') }}" alt="Bar Inventory, Liquor Bottles & ML Stock Management">
                </div>
                <div class="spf-text-col">
                    <div class="badge-bar-tag"><i class="ph ph-drop"></i> Milliliter Precision</div>
                    <h3>Real-time <span>Liquid Stock</span> & Peg Inventory Tracking</h3>
                    <p>Never worry about liquor shrinkage again. Set bottle sizes (750ml, 1000ml, etc.), opening stock, and define peg variations. The moment a drink is served, the exact volume is automatically deducted from inventory.</p>
                    <ul class="spf-points">
                        <li><i class="ph-fill ph-check-circle"></i> Opening Bottle + Extra ML calculation</li>
                        <li><i class="ph-fill ph-check-circle"></i> Automatic stock deduction when KOT is SERVED</li>
                        <li><i class="ph-fill ph-check-circle"></i> Real-time low stock & reorder notifications</li>
                    </ul>
                    <a href="javascript:void(0)" class="spf-explore-link open-enquiry-btn">Book Live Bar Demo <i class="ph ph-arrow-right"></i></a>
                </div>
            </div>

            <!-- Row 2: Separate Bar KOT Counter -->
            <div class="spf-row reverse">
                <div class="spf-image-col">
                    <img src="{{ asset('02.png') }}" alt="Dedicated Bartender Counter and Bar KOT Station">
                </div>
                <div class="spf-text-col">
                    <div class="badge-bar-tag"><i class="ph ph-desktop"></i> Smart Routing</div>
                    <h3>Separate <span>Bar KOT Screen</span> & Food Kitchen Display</h3>
                    <p>When a server or customer places an order containing both cocktails and starters, Bill&Bite routes the drinks to the Bartender Counter and the food to the Kitchen Display System without any manual intervention.</p>
                    <ul class="spf-points">
                        <li><i class="ph-fill ph-check-circle"></i> Dedicated Bartender touch interface</li>
                        <li><i class="ph-fill ph-check-circle"></i> One-click status updates: PENDING, PREPARING, SERVED</li>
                        <li><i class="ph-fill ph-check-circle"></i> Zero misplaced cocktail tickets or delay in dispensing</li>
                    </ul>
                    <a href="javascript:void(0)" class="spf-explore-link open-enquiry-btn">See Bar KOT in Action <i class="ph ph-arrow-right"></i></a>
                </div>
            </div>

            <!-- Row 3: Rapid Billing & Tax Compliance -->
            <div class="spf-row">
                <div class="spf-image-col">
                    <img src="{{ asset('03.png') }}" alt="Fast Rapid Bar POS and Excise Billing Register">
                </div>
                <div class="spf-text-col">
                    <div class="badge-bar-tag"><i class="ph ph-receipt"></i> Speed & Compliance</div>
                    <h3>Fast <span>Rapid Bar Bill</span> with State Excise & Food GST</h3>
                    <p>During packed hours, speed is everything. Bill&Bite Rapid Bill enables bartenders to punch drinks in seconds, apply bottle or peg discounts, split bills across patrons, and produce state excise-compliant VAT and GST receipts.</p>
                    <ul class="spf-points">
                        <li><i class="ph-fill ph-check-circle"></i> One-screen rapid billing designed for crowded counters</li>
                        <li><i class="ph-fill ph-check-circle"></i> Automated Food GST and Liquor VAT multi-tax calculation</li>
                        <li><i class="ph-fill ph-check-circle"></i> Multiple tender types: Cash, Card, UPI, and Split Payments</li>
                    </ul>
                    <a href="javascript:void(0)" class="spf-explore-link open-enquiry-btn">Explore Rapid POS <i class="ph ph-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

        @if(isset($defaultPlans) && $defaultPlans->count() > 0)
    <!-- Pricing / Packages Section -->
    <section class="pricing" id="pricing" style="padding: 80px 0; background-color: #f9fafb;">
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 25px;">
                <span class="section-subtitle" style="color: #ff6a00; font-weight: 700; letter-spacing: 1px;">BAR & RESTAURANT PLANS</span>
                <h2>Simple & Transparent <span class="text-orange">Bar Pricing</span></h2>
                <p>Complete Bar Management + Full Restaurant Management included in every plan.</p>
            </div>

            @php
                $hasYearly = $defaultPlans->contains(function($p) {
                    return strtolower($p->billing_cycle ?? '') === 'yearly';
                });
                $hasMonthly = $defaultPlans->contains(function($p) {
                    return strtolower($p->billing_cycle ?? '') === 'monthly';
                });
                $defaultActiveTimeframe = $hasYearly ? 'yearly' : ($hasMonthly ? 'monthly' : 'yearly');
            @endphp

            @if($hasYearly && $hasMonthly)
                <!-- Pricing Tabs Toggle (Shown only if both Annual and Monthly plans exist in database) -->
                <div class="pricing-tabs-wrapper text-center" style="margin-bottom: 45px;">
                    <div class="pricing-toggle-container">
                        <button type="button" class="pricing-tab-btn active" id="btn-tab-yearly" onclick="switchPricingTab('yearly')">
                            Annual Billing <span class="badge-save">Best Value</span>
                        </button>
                        <button type="button" class="pricing-tab-btn" id="btn-tab-monthly" onclick="switchPricingTab('monthly')">
                            Monthly Billing
                        </button>
                    </div>
                </div>
            @endif

            <style>
                .pricing-toggle-container {
                    display: inline-flex;
                    background: #f1f5f9;
                    padding: 6px;
                    border-radius: 40px;
                    border: 1px solid #e2e8f0;
                    box-shadow: inset 0 2px 4px rgba(0,0,0,0.04);
                    position: relative;
                }
                .pricing-tab-btn {
                    border: none;
                    background: transparent;
                    padding: 10px 28px;
                    border-radius: 30px;
                    font-size: 0.95rem;
                    font-weight: 700;
                    color: #64748b;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .pricing-tab-btn:hover {
                    color: #1e293b;
                }
                .pricing-tab-btn.active {
                    background: #ffffff;
                    color: #ff6a00;
                    box-shadow: 0 4px 12px rgba(255, 106, 0, 0.15);
                }
                .pricing-tab-btn .badge-save {
                    background: #ffedd5;
                    color: #ea580c;
                    font-size: 0.72rem;
                    font-weight: 800;
                    padding: 3px 8px;
                    border-radius: 12px;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                }
                .pricing-tab-btn.active .badge-save {
                    background: #ff6a00;
                    color: #ffffff;
                }
                .plans-wrapper {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                    align-items: stretch;
                    gap: 30px;
                    max-width: 1100px;
                    margin: 0 auto;
                    width: 100%;
                }
                .plan-card {
                    background: white;
                    border-radius: 20px;
                    padding: 36px 30px;
                    border: 1px solid #e2e8f0;
                    display: flex;
                    flex-direction: column;
                    position: relative;
                    transition: all 0.3s ease;
                    animation: fadeInPlan 0.4s ease forwards;
                    flex: 1 1 340px;
                    max-width: 440px;
                    width: 100%;
                    box-sizing: border-box;
                }
                @keyframes fadeInPlan {
                    from { opacity: 0; transform: translateY(12px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .plan-card:hover {
                    transform: translateY(-6px);
                    box-shadow: 0 20px 35px rgba(0,0,0,0.06);
                    border-color: #cbd5e1;
                }
                .plan-card.default-plan {
                    border: 2px solid #ff6a00;
                    box-shadow: 0 16px 35px rgba(255, 106, 0, 0.12);
                }
                .default-badge {
                    position: absolute;
                    top: -14px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: linear-gradient(135deg, #ff6a00, #ff8c42);
                    color: white;
                    font-size: 0.76rem;
                    font-weight: 800;
                    padding: 5px 16px;
                    border-radius: 20px;
                    text-transform: uppercase;
                    letter-spacing: 0.06em;
                    white-space: nowrap;
                    box-shadow: 0 4px 10px rgba(255, 106, 0, 0.3);
                }
                .plan-header {
                    text-align: center;
                    border-bottom: 1px solid #f1f5f9;
                    padding-bottom: 22px;
                    margin-bottom: 22px;
                }
                .plan-name {
                    font-size: 1.35rem;
                    font-weight: 800;
                    color: #1e293b;
                    margin-bottom: 10px;
                }
                .plan-price {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    gap: 4px;
                    margin-bottom: 12px;
                }
                .cross-price-box {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    margin-bottom: 2px;
                }
                .cross-price {
                    font-size: 1.25rem;
                    font-weight: 600;
                    color: #94a3b8;
                    text-decoration: line-through;
                    line-height: 1.2;
                }
                .main-price-box {
                    display: flex;
                    align-items: baseline;
                    justify-content: center;
                }
                .active-price {
                    font-size: 2.75rem;
                    font-weight: 800;
                    color: #0f172a;
                    line-height: 1.1;
                    letter-spacing: -0.5px;
                }
                .plan-duration {
                    font-size: 0.88rem;
                    color: #64748b;
                    margin-top: 6px;
                }
                .timeframe-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    font-size: 0.78rem;
                    font-weight: 700;
                    padding: 4px 12px;
                    border-radius: 14px;
                    margin-top: 8px;
                }
                .timeframe-badge.yearly {
                    background: #f0fdf4;
                    color: #16a34a;
                    border: 1px solid #bbf7d0;
                }
                .timeframe-badge.monthly {
                    background: #eff6ff;
                    color: #2563eb;
                    border: 1px solid #bfdbfe;
                }
                .plan-features-list {
                    list-style: none;
                    padding: 0;
                    margin: 0 0 28px 0;
                    flex-grow: 1;
                }
                .plan-features-list li {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.92rem;
                    color: #475569;
                    margin-bottom: 12px;
                    line-height: 1.4;
                }
                .plan-features-list i {
                    font-size: 1.15rem;
                    color: #ff6a00;
                    flex-shrink: 0;
                }
                .plan-features-list .bar-feature-item {
                    font-weight: 700;
                    color: #0f172a;
                }
                .btn-plan {
                    display: block;
                    width: 100%;
                    text-align: center;
                    padding: 13px 20px;
                    border-radius: 12px;
                    font-weight: 700;
                    font-size: 0.95rem;
                    text-decoration: none;
                    background: #f1f5f9;
                    color: #1e293b;
                    transition: all 0.2s ease;
                    border: none;
                    cursor: pointer;
                }
                .btn-plan:hover, .plan-card.default-plan .btn-plan {
                    background: linear-gradient(135deg, #ff6a00, #ff8c42);
                    color: white;
                    box-shadow: 0 6px 18px rgba(255, 106, 0, 0.25);
                }

                @media (max-width: 991px) {
                    .pricing {
                        padding: 60px 0 !important;
                    }
                    .plans-wrapper {
                        gap: 25px;
                    }
                    .plan-card {
                        padding: 32px 26px;
                    }
                }

                @media (max-width: 768px) {
                    .pricing {
                        padding: 50px 0 !important;
                    }
                    .section-header h2 {
                        font-size: 1.85rem !important;
                    }
                    .section-header p {
                        font-size: 0.95rem;
                    }
                    .plans-wrapper {
                        gap: 20px;
                        padding: 0 12px;
                    }
                    .plan-card {
                        padding: 28px 20px;
                        max-width: 100%;
                        border-radius: 16px;
                    }
                    .active-price {
                        font-size: 2.25rem;
                    }
                    .cross-price {
                        font-size: 1.1rem;
                    }
                    .plan-name {
                        font-size: 1.25rem;
                    }
                    .plan-features-list li {
                        font-size: 0.88rem;
                        gap: 8px;
                    }
                    .pricing-tab-btn {
                        padding: 8px 18px;
                        font-size: 0.88rem;
                    }
                }

                @media (max-width: 480px) {
                    .pricing {
                        padding: 40px 0 !important;
                    }
                    .plan-card {
                        padding: 24px 15px;
                    }
                    .active-price {
                        font-size: 1.95rem;
                    }
                    .cross-price {
                        font-size: 1rem;
                    }
                    .default-badge {
                        font-size: 0.7rem;
                        padding: 4px 12px;
                        top: -12px;
                    }
                    .pricing-toggle-container {
                        width: 100%;
                        max-width: 320px;
                        justify-content: center;
                    }
                    .pricing-tab-btn {
                        padding: 8px 12px;
                        font-size: 0.8rem;
                    }
                    .btn-plan {
                        padding: 12px 16px;
                        font-size: 0.9rem;
                    }
                }
            </style>
 
            <div class="plans-wrapper" id="welcomePlansContainer">
                @foreach($defaultPlans as $plan)
                    @php
                        $pTimeframe = strtolower($plan->billing_cycle ?? 'monthly');
                        $isCardVisible = ($hasYearly && $hasMonthly) 
                            ? ($pTimeframe === $defaultActiveTimeframe)
                            : true;
                    @endphp
                    <div class="plan-card {{ $plan->is_default_plan == 'Y' ? 'default-plan' : '' }}" data-timeframe="{{ $pTimeframe }}" style="{{ $isCardVisible ? '' : 'display: none;' }}">
                        @if($plan->label_name)
                            <div class="default-badge">{{ $plan->label_name }}</div>
                        @elseif($plan->is_default_plan == 'Y')
                            <div class="default-badge">Recommended for Bars</div>
                        @endif
                        <div class="plan-header">
                            <h3 class="plan-name">{{ $plan->name }}</h3>
                            <div class="plan-price">
                                @if($plan->price == 0)
                                    <div class="main-price-box">
                                        <span class="active-price">FREE</span>
                                    </div>
                                @else
                                    @if($plan->cross_price)
                                        <div class="cross-price-box">
                                            <span class="cross-price">
                                                @if(is_numeric($plan->cross_price))
                                                    ₹{{ number_format($plan->cross_price, 2) }}
                                                @else
                                                    {{ $plan->cross_price }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    <div class="main-price-box">
                                        <span class="active-price">₹{{ number_format($plan->price, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="plan-duration">
                                <i class="ph ph-calendar"></i> {{ $plan->duration_days }} days validity
                            </div>
                            <div>
                                @if($pTimeframe == 'yearly')
                                    <span class="timeframe-badge yearly"><i class="ph ph-calendar-check"></i> Annual Plan</span>
                                @else
                                    <span class="timeframe-badge monthly"><i class="ph ph-calendar"></i> Monthly Plan</span>
                                @endif
                            </div>
                        </div>
                        <ul class="plan-features-list">
                            <!-- Mandatory Bar Features (Unlimited) -->
                            <li class="bar-feature-item"><i class="ph-fill ph-check-circle"></i> Unlimited Bar Categories</li>
                            <li class="bar-feature-item"><i class="ph-fill ph-check-circle"></i> Unlimited Bar Brands</li>
                            <li class="bar-feature-item"><i class="ph-fill ph-check-circle"></i> Unlimited Bar Items & Variations (Peg/Bottle)</li>
                            <li class="bar-feature-item"><i class="ph-fill ph-check-circle"></i> Bar KOT & Kitchen Display System</li>
                            <li class="bar-feature-item"><i class="ph-fill ph-check-circle"></i> Bar Inventory & ML Tracking</li>
                            
                            <!-- Training & Support (Every Plan) -->
                            <li class="bar-feature-item"><i class="ph-fill ph-check-circle"></i> Installing & Training (Online)</li>
                            <li class="bar-feature-item"><i class="ph-fill ph-check-circle"></i> Support 24 x 7 (Online)</li>

                            <!-- Restaurant Features -->
                            <li><i class="ph-fill ph-check-circle"></i> {{ $plan->category_number == 0 ? 'Unlimited' : $plan->category_number }} Food Categories</li>
                            <li><i class="ph-fill ph-check-circle"></i> {{ $plan->total_number_of_dishes == 0 ? 'Unlimited' : $plan->total_number_of_dishes }} Food Dishes</li>
                            <li><i class="ph-fill ph-check-circle"></i> {{ $plan->total_number_of_table == 0 ? 'Unlimited' : $plan->total_number_of_table }} Tables</li>
                            
                            <li><i class="ph-fill ph-check-circle"></i> Rapid Bar & Restaurant POS</li>
                            <li><i class="ph-fill ph-check-circle"></i> Menu Availability Management</li>
                            <li><i class="ph-fill ph-check-circle"></i> Order Management (Dine-In, Takeaway)</li>
                            <li><i class="ph-fill ph-check-circle"></i> QR Code Ordering</li>
                            <li><i class="ph-fill ph-check-circle"></i> Invoice Whatsapp Sharing</li>
                            <li><i class="ph-fill ph-check-circle"></i> Staff & Bartender Management</li>
                            <li><i class="ph-fill ph-check-circle"></i> Cash Drawer Management</li>
                            @if(($plan->report_management_checkbox ?? 'Y') == 'Y')
                                <li><i class="ph-fill ph-check-circle" style="color: #2e7d32;"></i> Report & Sales Analytics</li>
                            @else
                                <li><i class="ph-fill ph-x-circle" style="color: #d32f2f;"></i> Report & Sales Analytics</li>
                            @endif

                            @if($plan->inventory_checkbox == 'Y')
                                <li><i class="ph-fill ph-check-circle" style="color: #2e7d32;"></i> Product, Purchase & Stockout</li>
                                <li><i class="ph-fill ph-check-circle" style="color: #2e7d32;"></i> Wastage & Spillage Tracking</li>
                                <li><i class="ph-fill ph-check-circle" style="color: #2e7d32;"></i> Debit Note Management</li>
                            @else
                                <li><i class="ph-fill ph-x-circle" style="color: #d32f2f;"></i> Product & Purchase</li>
                            @endif

                            @if($plan->multi_outlet_checkbox == 'Y')
                                <li><i class="ph-fill ph-check-circle" style="color: #2e7d32;"></i> Multi-Outlet Support (Up to {{ $plan->total_number_of_outlets == 0 ? 'Unlimited' : $plan->total_number_of_outlets }} Outlets)</li>
                            @else
                                <li><i class="ph-fill ph-x-circle" style="color: #d32f2f;"></i> Multi-Outlet Support</li>
                            @endif
                        </ul>
                        <a href="javascript:void(0)" class="btn-plan open-enquiry-btn">Get Started</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
<!-- Why Choose Bill&Bite for Bars Section -->
    <section class="benefits" id="platform-benefits" style="padding: 80px 0; background: #ffffff;">
        <span id="bar-benefits"></span>
        <div class="container">
            <div class="section-header text-center" style="margin-bottom: 50px;">
                <span class="section-subtitle">THE BILL&BITE ADVANTAGE</span>
                <h2>Why Leading Bars & Breweries <span class="text-orange">Trust Bill&Bite</span></h2>
                <p>Designed from the ground up to solve the real problems of high-volume nightlife operations.</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-shield-check"></i></div>
                    <h3>Zero Liquor Shrinkage</h3>
                    <p>Track opening bottle levels, peg deductions, and stock variance down to the single ML so every drop is accounted for.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-lightning"></i></div>
                    <h3>Blazing-Fast Service</h3>
                    <p>Serve thirsty crowds faster with intuitive one-touch rapid billing and instant drink routing to bartender counters.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-file-text"></i></div>
                    <h3>Dual Tax Compliance</h3>
                    <p>Hassle-free excise tax reports with separate Liquor VAT and Food GST computation on every single invoice.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph ph-arrows-split"></i></div>
                    <h3>Seamless Bar & Kitchen Sync</h3>
                    <p>One unified order bill for the customer while drinks flow from the bar and meals prepare in the kitchen.</p>
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
                        How does the Milliliter (ML) liquid stock tracking work?
                        <i class="ph ph-caret-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        When adding a bar product, you specify its bottle size (e.g. 750ml, 1000ml) and opening stock. You can create variations like 30ml, 60ml, 90ml, or Full Bottle. When an order item is marked as "SERVED" at the bar, the POS automatically deducts that exact ML amount from your current inventory in real time.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        Can I manage both my bar counter and food kitchen on the same system?
                        <i class="ph ph-caret-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        Yes, absolutely! When a customer places an order with both food and alcoholic beverages, Bill&Bite routes the drinks to the Bar KOT counter screen and the food to the Kitchen Display System (KDS), while generating a single consolidated bill for the customer.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        How does Bill&Bite handle state liquor VAT and restaurant GST?
                        <i class="ph ph-caret-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        In most states, alcohol is subject to Liquor VAT while food items are subject to GST. Bill&Bite POS automatically applies your configured VAT rate to alcoholic drinks and GST to food items on the same order, producing fully compliant tax invoices.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        Can customers order drinks from their mobile phones via Table QR?
                        <i class="ph ph-caret-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        Yes! Customers can scan the table QR code to browse your full beverage catalog, choose peg sizes or cocktails, add food items, and send orders straight to the bar and kitchen.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        Are Online Installation & 24x7 Support included with every plan?
                        <i class="ph ph-caret-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        Yes! Every Bill&Bite Bar plan includes free online installation, menu setup assistance, bartender training, and dedicated 24x7 online customer support.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="contact-form">
        <div class="container cta-container">
            <div class="cta-content">
                <h2>Ready to <span class="text-orange">Simplify</span> Your<br>Bar & Restaurant Operations?</h2>
                <p>Book a free demo and see how Bill&Bite can help your venue eliminate liquor shrinkage and speed up service.</p>
                <ul class="cta-list">
                    <li><i class="ph ph-check text-orange"></i> Free Personalized Bar & Restaurant Demo</li>
                    <li><i class="ph ph-check text-orange"></i> No Credit Card Required</li>
                    <li><i class="ph ph-check text-orange"></i> Setup in Minutes</li>
                </ul>
            </div>
            <div class="cta-form-wrapper">
                <div class="cta-form-card">
                    <h3>Book A Free Demo</h3>
                    <form class="demo-form" id="barDemoLeadForm" action="{{ route('book.demo') }}" method="POST">
                        @csrf
                        <div id="bar-form-alert" style="display: none; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 500; font-size: 0.9rem; text-align: center;"></div>
                        
                        <div class="form-group-row">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" placeholder="Enter name" required>
                            </div>
                            <div class="form-group">
                                <label>Bar / Restaurant Name</label>
                                <input type="text" name="restaurant_name" placeholder="Enter venue name">
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
                        <button type="submit" id="btnSubmitBarEnquiry" class="btn btn-primary btn-block">Book Free Bar Demo <i class="ph ph-arrow-right"></i></button>
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
                    <a href="{{ route('home') }}" class="logo mb-4">
                        <img src="{{ asset('logo.png') }}" alt="Bill&Bite Logo" style="height: 40px; width: auto;">
                    </a>
                    <p>Bill & Bite is a complete restaurant management system designed to simplify your operations and help your business grow.</p>
                    <style>
                        .footer-brand .social-links {
                            display: flex;
                            align-items: center;
                            gap: 12px;
                            margin-top: 18px;
                        }
                        .footer-brand .social-btn {
                            width: 40px;
                            height: 40px;
                            border-radius: 12px;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            color: #ffffff !important;
                            font-size: 1.3rem;
                            text-decoration: none;
                            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
                            position: relative;
                            overflow: hidden;
                        }
                        .footer-brand .social-btn::after {
                            content: '';
                            position: absolute;
                            inset: 0;
                            background: linear-gradient(rgba(255,255,255,0.22), transparent);
                            opacity: 0;
                            transition: opacity 0.25s ease;
                        }
                        .footer-brand .social-btn:hover::after {
                            opacity: 1;
                        }
                        .footer-brand .social-btn-facebook {
                            background: linear-gradient(135deg, #1877f2 0%, #0d65d9 100%) !important;
                            box-shadow: 0 4px 14px rgba(24, 119, 242, 0.35);
                        }
                        .footer-brand .social-btn-facebook:hover {
                            transform: translateY(-3px) scale(1.08);
                            box-shadow: 0 8px 22px rgba(24, 119, 242, 0.55);
                        }
                        .footer-brand .social-btn-instagram {
                            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%) !important;
                            box-shadow: 0 4px 14px rgba(220, 39, 67, 0.35);
                        }
                        .footer-brand .social-btn-instagram:hover {
                            transform: translateY(-3px) scale(1.08);
                            box-shadow: 0 8px 22px rgba(220, 39, 67, 0.55);
                        }
                        .footer-brand .social-btn-linkedin {
                            background: linear-gradient(135deg, #0a66c2 0%, #004182 100%) !important;
                            box-shadow: 0 4px 14px rgba(10, 102, 194, 0.35);
                        }
                        .footer-brand .social-btn-linkedin:hover {
                            transform: translateY(-3px) scale(1.08);
                            box-shadow: 0 8px 22px rgba(10, 102, 194, 0.55);
                        }
                        .footer-brand .social-btn-youtube {
                            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%) !important;
                            box-shadow: 0 4px 14px rgba(255, 0, 0, 0.35);
                        }
                        .footer-brand .social-btn-youtube:hover {
                            transform: translateY(-3px) scale(1.08);
                            box-shadow: 0 8px 22px rgba(255, 0, 0, 0.55);
                        }
                    </style>
                    <div class="social-links">
                        <a href="https://www.facebook.com/profile.php?id=61592626503884" class="social-btn social-btn-facebook" target="_blank" rel="noopener noreferrer" title="Facebook" aria-label="Facebook"><i class="ph-fill ph-facebook-logo"></i></a>
                        <a href="https://www.instagram.com/billnbite_official?stkn=MzI0Z2pkenN0YWJ1&utm_source=qr" class="social-btn social-btn-instagram" target="_blank" rel="noopener noreferrer" title="Instagram" aria-label="Instagram"><i class="ph-fill ph-instagram-logo"></i></a>
                        <a href="https://www.linkedin.com/company/bill-bite/home/?viewAsMember=true" class="social-btn social-btn-linkedin" target="_blank" rel="noopener noreferrer" title="LinkedIn" aria-label="LinkedIn"><i class="ph-fill ph-linkedin-logo"></i></a>
                        <a href="https://www.youtube.com/@BillnBite_Official" class="social-btn social-btn-youtube" target="_blank" rel="noopener noreferrer" title="YouTube" aria-label="YouTube"><i class="ph-fill ph-youtube-logo"></i></a>
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
                        <li><a href="{{ route('about.us') }}">About Us</a></li>
                        <li><a href="{{ route('blog.index') }}">Blog</a></li>
                        <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('terms.conditions') }}">Terms & Conditions</a></li>
                        <li><a href="{{ route('login') }}">Restaurant Login</a></li>
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

    <!-- Sticky Book Demo Trigger Button -->
    <div class="sticky-enquiry-btn open-enquiry-btn" id="openEnquiryModalBtn">
        Enquiry Now
    </div>

    <!-- Scripts -->
    <script>
        // Pricing Tab Switcher (Used when both Annual & Monthly plans exist in DB)
        function switchPricingTab(timeframe) {
            const btnYearly = document.getElementById('btn-tab-yearly');
            const btnMonthly = document.getElementById('btn-tab-monthly');
            const cards = document.querySelectorAll('.plan-card');

            if (btnYearly && btnMonthly) {
                if (timeframe === 'yearly') {
                    btnYearly.classList.add('active');
                    btnMonthly.classList.remove('active');
                } else {
                    btnMonthly.classList.add('active');
                    btnYearly.classList.remove('active');
                }
            }

            cards.forEach(card => {
                const cardTimeframe = card.getAttribute('data-timeframe');
                if (cardTimeframe === timeframe) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Smooth scroll to contact form for demo buttons
        document.querySelectorAll('.open-enquiry-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.getElementById('contact-form');
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 70,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // FAQ Accordion
        document.addEventListener('DOMContentLoaded', function () {
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                if (question) {
                    question.addEventListener('click', () => {
                        faqItems.forEach(otherItem => {
                            if (otherItem !== item && otherItem.classList.contains('active')) {
                                otherItem.classList.remove('active');
                            }
                        });
                        item.classList.toggle('active');
                    });
                }
            });
        });

        // Form Submit
        const barForm = document.getElementById('barDemoLeadForm');
        if (barForm) {
            barForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const alertBox = document.getElementById('bar-form-alert');
                const submitBtn = document.getElementById('btnSubmitBarEnquiry');

                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Submitting Request...';

                const formData = new FormData(this);

                fetch("{{ route('book.demo') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;

                    alertBox.style.display = 'block';
                    if (res.status === 'success' || res.success) {
                        alertBox.style.background = '#d1fae5';
                        alertBox.style.color = '#065f46';
                        alertBox.style.border = '1px solid #a7f3d0';
                        alertBox.innerHTML = res.message || 'Thank you! We have received your bar demo request. Our specialist will contact you shortly.';
                        barForm.reset();
                    } else {
                        alertBox.style.background = '#fee2e2';
                        alertBox.style.color = '#991b1b';
                        alertBox.style.border = '1px solid #fecaca';
                        alertBox.innerHTML = res.message || 'An error occurred. Please check your information and try again.';
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    alertBox.style.display = 'block';
                    alertBox.style.background = '#fee2e2';
                    alertBox.style.color = '#991b1b';
                    alertBox.style.border = '1px solid #fecaca';
                    alertBox.innerHTML = 'Unable to connect to the server. Please try again.';
                });
            });
        }
    </script>
</body>
</html>