<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.gtm_head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Bill&Bite</title>
    <meta name="description" content="Bill&amp;Bite was built in 2026 by a small India-based team to give every restaurant, cafe, and food stall affordable POS software — not just the big chains.">
    <link rel="canonical" href="https://billnbite.com/about-us">
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="{{ asset('frontend/style.css') }}">
    <style>
        html { scroll-behavior: smooth; }

        .about-header {
            background: linear-gradient(135deg, #fffaf5 0%, #ffffff 100%);
            padding: 80px 0 60px;
            border-bottom: 1px solid #eaeaea;
        }
        .about-header .header-content { max-width: 760px; }
        .about-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 15px 0 18px;
            letter-spacing: -0.5px;
        }
        .about-header p.lead {
            font-size: 1.15rem;
            color: var(--text-light);
            line-height: 1.7;
        }

        .about-section { padding: 60px 0; }
        .about-section.alt { background: var(--bg-light); }
        .about-section h2 {
            font-size: 1.9rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
        }
        .about-section p {
            color: #444444;
            line-height: 1.8;
            margin-bottom: 16px;
            font-size: 1.02rem;
        }
        .about-narrow { max-width: 780px; }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-top: 30px;
        }
        .team-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 28px 24px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }
        .team-card .team-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--bg-subtle);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin: 0 auto 16px;
        }
        .team-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }
        .team-card span {
            color: var(--text-light);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .mission-box {
            background: var(--bg-subtle);
            border-left: 4px solid var(--primary-color);
            padding: 28px 32px;
            border-radius: 0 16px 16px 0;
            margin: 30px 0;
        }
        .mission-box p {
            font-weight: 500;
            color: var(--text-dark);
            margin: 0;
            font-size: 1.08rem;
            line-height: 1.7;
        }

        .about-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .about-features .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .about-features .feature-item i {
            color: var(--primary-color);
            font-size: 1.4rem;
            margin-top: 2px;
        }
        .about-features .feature-item h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 3px;
        }
        .about-features .feature-item p {
            font-size: 0.92rem;
            color: var(--text-light);
            margin: 0;
        }

        .about-cta {
            text-align: center;
            padding: 70px 0;
        }
        .about-cta h2 { margin-bottom: 14px; }
        .about-cta p { color: var(--text-light); margin-bottom: 28px; }
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
                <a href="{{ route('home') }}#features">Features</a>
                <a href="{{ route('home') }}#pricing">Pricing</a>
                <a href="{{ route('home') }}#how-it-works">How It Works</a>
                <a href="{{ route('blog.index') }}">Blog</a>
                <a href="{{ route('home') }}#faq">FAQ</a>
            </div>
            <div class="nav-actions">
                <a href="{{ route('home') }}" class="btn-login"><i class="ph ph-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="about-header">
        <div class="container">
            <div class="header-content">
                <span class="badge"><i class="ph ph-heart"></i> Our Story</span>
                <h1>Built for the restaurant that got left out of "restaurant software."</h1>
                <p class="lead">Bill&amp;Bite started in 2026 because we kept seeing the same gap: POS software built for big chains, priced for big chains — and nothing for the tea stall, the small dhaba, or the food cart next door.</p>
            </div>
        </div>
    </header>

    <!-- Origin story -->
    <section class="about-section">
        <div class="container about-narrow">
            <h2>Why we built Bill&amp;Bite</h2>
            <p>We looked at the restaurant software already out there and noticed a pattern: it was expensive, and it was built with large, multi-outlet restaurants in mind — onboarding calls, long contracts, pricing that only makes sense once you're already doing serious volume.</p>
            <p>That left out most of the food businesses we actually see every day — small stalls, carts, single-counter shops, home kitchens going commercial for the first time. They need billing, order tracking, and basic reporting just as much as a large restaurant does. They just can't justify paying large-restaurant prices for it.</p>
            <p>So we built Bill&amp;Bite to work for both ends of that range, and we're currently offering a <strong>free basic subscription</strong> to restaurant owners and food stall operators of every size, so cost isn't the reason someone stays on pen and paper.</p>
        </div>
    </section>

    <!-- Mission -->
    <section class="about-section alt">
        <div class="container about-narrow">
            <h2>What we're trying to do</h2>
            <div class="mission-box">
                <p>Give every food business — whatever size it is today — software that actually fits how it runs, without pricing them out before they've even grown.</p>
            </div>
            <p>That shows up in what we built: order management, staff management, kitchen (KOT) tracking, inventory, QR code table ordering, payments and invoicing, delivery tracking, and reporting — all in one dashboard, set up in under 10 minutes, with support available 24/7.</p>

            <div class="about-features">
                <div class="feature-item">
                    <i class="ph ph-receipt"></i>
                    <div><h4>Order Management</h4><p>Dine-in, takeaway, and online orders in one place.</p></div>
                </div>
                <div class="feature-item">
                    <i class="ph ph-chef-hat"></i>
                    <div><h4>Kitchen (KOT) Tracking</h4><p>Orders reach the kitchen instantly, no shouting across the counter.</p></div>
                </div>
                <div class="feature-item">
                    <i class="ph ph-qr-code"></i>
                    <div><h4>QR Code Ordering</h4><p>Customers scan, order, and pay from the table.</p></div>
                </div>
                <div class="feature-item">
                    <i class="ph ph-package"></i>
                    <div><h4>Inventory Management</h4><p>Real-time stock tracking with low-stock alerts.</p></div>
                </div>
                <div class="feature-item">
                    <i class="ph ph-credit-card"></i>
                    <div><h4>Payments &amp; Invoices</h4><p>Accept multiple payment methods, generate invoices instantly.</p></div>
                </div>
                <div class="feature-item">
                    <i class="ph ph-chart-line-up"></i>
                    <div><h4>Reports &amp; Analytics</h4><p>See sales, orders, and stock trends without spreadsheets.</p></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="about-section">
        <div class="container about-narrow">
            <h2>The team</h2>
            <p>We're a small team based in Siliguri, India — not a faceless company. If you call support, you're talking to someone who actually built the product.</p>

            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar"><i class="ph ph-user"></i></div>
                    <h3>Vikrant Singh</h3>
                    <span>Co-Founder</span>
                </div>
                <div class="team-card">
                    <div class="team-avatar"><i class="ph ph-user"></i></div>
                    <h3>Rishav Kumar</h3>
                    <span>Co-Founder</span>
                </div>
                <div class="team-card">
                    <div class="team-avatar"><i class="ph ph-code"></i></div>
                    <h3>Sayan &amp; Jeet</h3>
                    <span>Development</span>
                </div>
                <div class="team-card">
                    <div class="team-avatar"><i class="ph ph-check-circle"></i></div>
                    <h3>Rupesh</h3>
                    <span>Quality &amp; Testing</span>
                </div>
                <div class="team-card">
                    <div class="team-avatar"><i class="ph ph-handshake"></i></div>
                    <h3>Anish</h3>
                    <span>Sales</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="about-cta">
        <div class="container">
            <h2>Want to see if Bill&amp;Bite fits your kitchen?</h2>
            <p>Free basic plan, set up in under 10 minutes — no large-restaurant price tag.</p>
            <a href="{{ route('home') }}#pricing" class="btn btn-primary btn-lg">See Plans <i class="ph ph-arrow-right"></i></a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" style="background:white !important; border-top: 1px solid #eaeaea;">
        <div class="container">
            <div class="footer-bottom" style="text-align: center; padding: 30px 0; color: var(--text-light); font-size: 0.9rem;">
                <p style="margin-bottom: 10px;">
                    <a href="{{ route('about.us') }}" style="color: var(--text-light); margin: 0 10px;">About Us</a>
                    <a href="{{ route('blog.index') }}" style="color: var(--text-light); margin: 0 10px;">Blog</a>
                    <a href="{{ route('privacy.policy') }}" style="color: var(--text-light); margin: 0 10px;">Privacy Policy</a>
                    <a href="{{ route('terms.conditions') }}" style="color: var(--text-light); margin: 0 10px;">Terms &amp; Conditions</a>
                </p>
                <p>© 2026 Bill & Bite. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
