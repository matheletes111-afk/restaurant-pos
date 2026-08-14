<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Bill&Bite</title>
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="{{ asset('frontend/style.css') }}">
    <style>
        /* Premium layout styles for the privacy page */
        html {
            scroll-behavior: smooth;
        }
        
        .privacy-header {
            background: linear-gradient(135deg, #fffaf5 0%, #ffffff 100%);
            padding: 80px 0 50px;
            border-bottom: 1px solid #eaeaea;
            position: relative;
        }
        
        .privacy-header .header-content {
            max-width: 800px;
        }

        .privacy-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 15px 0 10px;
            letter-spacing: -0.5px;
        }

        .privacy-header .last-updated {
            color: var(--text-light);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .privacy-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 50px;
            margin: 50px auto 100px;
            align-items: start;
        }

        .privacy-sidebar {
            position: sticky;
            top: 100px;
        }

        .sidebar-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .sidebar-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-card ul {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar-card a {
            display: block;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-light);
            transition: all 0.2s ease;
        }

        .sidebar-card a:hover {
            color: var(--primary-color);
            background-color: var(--bg-subtle);
        }

        .sidebar-card a.active {
            color: var(--primary-color);
            background-color: var(--bg-subtle);
            font-weight: 600;
        }

        .privacy-main {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .intro-box {
            background: var(--bg-subtle);
            border-left: 4px solid var(--primary-color);
            padding: 24px;
            border-radius: 0 12px 12px 0;
            margin-bottom: 40px;
            font-weight: 500;
            color: var(--text-dark);
            line-height: 1.7;
        }

        .privacy-section {
            margin-bottom: 45px;
            scroll-margin-top: 120px;
        }

        .privacy-section h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--bg-subtle);
        }

        .privacy-section h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 20px 0 10px;
        }

        .privacy-section p {
            color: #444444;
            margin-bottom: 16px;
            line-height: 1.75;
        }

        .privacy-section ul {
            margin-bottom: 20px;
        }

        .privacy-section li {
            position: relative;
            padding-left: 24px;
            margin-bottom: 12px;
            color: #444444;
            line-height: 1.65;
        }

        .privacy-section li strong {
            color: var(--text-dark);
        }

        .privacy-section li::before {
            content: "•";
            color: var(--primary-color);
            position: absolute;
            left: 8px;
            font-weight: bold;
            font-size: 1.25rem;
            top: -2px;
        }

        .responsibility-callout {
            background: #fff8f5;
            border: 1px dashed var(--primary-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .responsibility-callout h4 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .contact-info-list {
            background: #fbfbfb;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            margin-top: 20px;
        }

        .contact-info-list li {
            list-style: none;
            position: relative;
            padding-left: 32px !important;
            margin-bottom: 14px;
        }

        .contact-info-list li:last-child {
            margin-bottom: 0;
        }

        .contact-info-list li i {
            position: absolute;
            left: 8px;
            top: 4px;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .contact-info-list li::before {
            display: none !important;
        }

        @media (max-width: 992px) {
            .privacy-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .privacy-sidebar {
                position: static;
            }

            .privacy-main {
                padding: 30px;
            }
        }
    </style>
</head>
<body>

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
                <a href="{{ route('home') }}#platform-benefits">Benefits</a>
                <a href="{{ route('home') }}#faq">FAQ</a>
            </div>
            <div class="nav-actions">
                <a href="{{ route('home') }}" class="btn-login"><i class="ph ph-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </nav>

    <!-- Privacy Header -->
    <header class="privacy-header">
        <div class="container">
            <div class="header-content">
                <span class="badge"><i class="ph ph-shield-check"></i> Privacy Policy</span>
                <h1>Privacy & Policy</h1>
                <p class="last-updated">Last Updated: August 14, 2026</p>
            </div>
        </div>
    </header>

    <!-- Privacy Content Section -->
    <section class="privacy-content-section">
        <div class="container privacy-container">
            <!-- Sidebar Navigation -->
            <aside class="privacy-sidebar">
                <div class="sidebar-card">
                    <h3>Table of Contents</h3>
                    <ul>
                        <li><a href="#intro" class="active">Overview</a></li>
                        <li><a href="#collect">1. Information We Collect</a></li>
                        <li><a href="#use">2. How We Use Info</a></li>
                        <li><a href="#share">3. Data Sharing</a></li>
                        <li><a href="#roles">4. Data Roles</a></li>
                        <li><a href="#retention">5. Data Retention</a></li>
                        <li><a href="#security">6. Security Safeguards</a></li>
                        <li><a href="#rights">7. Data Subject Rights</a></li>
                        <li><a href="#minors">8. Children's Privacy</a></li>
                        <li><a href="#updates">9. Policy Updates</a></li>
                        <li><a href="#contact">10. Contact Details</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="privacy-main">
                <div id="intro" class="privacy-section">
                    <p>SRV Technology operates the billnbite.com Point of Sale (POS) software, mobile applications, web dashboard, and hardware integration services (collectively, the "Services").</p>
                    
                    <div class="intro-box">
                        <p>This Privacy Policy explains how we collect, use, process, store, and protect information when:</p>
                        <ul style="margin-bottom: 0;">
                            <li style="margin-bottom: 6px;"><strong>Merchants / Restaurant Operators ("Subscribers"):</strong> Register an account, manage subscriptions, and operate POS terminals, Kitchen Display Systems (KDS), or web dashboards.</li>
                            <li style="margin-bottom: 0;"><strong>End Customers ("Diners / Guests"):</strong> Place orders, make payments, interact with digital/QR menus, or join restaurant loyalty programs processed through our POS ecosystem.</li>
                        </ul>
                    </div>
                </div>

                <div id="collect" class="privacy-section">
                    <h2>1. Information We Collect</h2>
                    
                    <h3>A. Information from Merchants & Restaurant Staff</h3>
                    <ul>
                        <li><strong>Account & Business Data:</strong> Owner/manager name, restaurant legal name, physical business address, business email, phone number, tax registration identifiers (e.g., GSTIN, VAT, EIN, or business licenses).</li>
                        <li><strong>Employee / Staff Profiles:</strong> Staff member names, employee IDs/passcodes, role permissions, clock-in/out timestamps, shift records, and server-specific sales tracking.</li>
                        <li><strong>Subscription & Billing Data:</strong> Invoicing records, bank transfer details, and payment method details (processed via PCI-compliant payment gateways).</li>
                    </ul>

                    <h3>B. Information Processed on Behalf of Merchants (Customer & Order Data)</h3>
                    <p>When guests place orders at the restaurant or via integrated ordering channels:</p>
                    <ul>
                        <li><strong>Transaction Details:</strong> Items ordered, item customizations, order totals, tips, table numbers, date and timestamps, payment status, and promotional discount codes.</li>
                        <li><strong>Diner Contact Data (if provided):</strong> Customer name, phone number, delivery address (for takeout/delivery orders), and email address (for digital receipts, feedback, or loyalty programs).</li>
                        <li><strong>Payment Card Data:</strong> Credit/debit card details and UPI/digital wallet IDs. <br><em>Note: Full cardholder numbers are tokenized and processed directly by our PCI-DSS certified payment partners (Razorpay); full payment card numbers are never stored on local POS terminals.</em></li>
                    </ul>

                    <h3>C. Technical and Device Data (Collected Automatically)</h3>
                    <ul>
                        <li><strong>Device & Terminal Info:</strong> Hardware model, operating system version, unique terminal/device IDs, IP address, and peripheral status (receipt printers, barcode scanners, cash drawers, KDS monitors).</li>
                        <li><strong>Usage & Diagnostic Logs:</strong> POS session durations, crash reports, network error logs, and offline transaction synchronization logs.</li>
                    </ul>
                </div>

                <div id="use" class="privacy-section">
                    <h2>2. How We Use the Information</h2>
                    <p>We utilize the collected information for the following operational and commercial purposes:</p>
                    <ul>
                        <li><strong>Core POS Functionality:</strong> Operate, configure, and maintain POS terminals, handheld ordering devices, Kitchen Display Systems (KDS), and cloud-based management dashboards.</li>
                        <li><strong>Order & Payment Processing:</strong> Process dining and online sales, issue physical/digital receipts, and settle card/digital transactions.</li>
                        <li><strong>Offline Synchronization:</strong> Securely cache offline transactions locally and synchronize them to central cloud databases once connectivity is restored.</li>
                        <li><strong>Business Analytics & Reporting:</strong> Provide restaurant owners with real-time sales reports, inventory deduction tracking, tax calculations, and end-of-day (Z-Report) summaries.</li>
                        <li><strong>Customer Engagement:</strong> Facilitate merchant loyalty programs, send SMS/WhatsApp order updates, and deliver responsive customer support.</li>
                        <li><strong>Security & Compliance:</strong> Monitor security events, detect fraudulent activities, verify transactions, and comply with applicable tax, financial, and accounting laws.</li>
                    </ul>
                </div>

                <div id="share" class="privacy-section">
                    <h2>3. Data Sharing and Third-Party Disclosures</h2>
                    <p>We do not sell customer or restaurant data to third-party data brokers. Data is shared solely under strict operational parameters:</p>
                    <ul>
                        <li><strong>Payment Processors:</strong> Integrated gateways (e.g., Stripe, Razorpay, Square, Adyen) to securely process card payments and online settlements.</li>
                        <li><strong>Cloud & Hosting Infrastructure:</strong> Enterprise cloud providers (e.g., AWS, Google Cloud, Supabase) for database hosting, automatic data backups, and user authentication.</li>
                        <li><strong>Integrated Third-Party Services:</strong> Food delivery aggregators (e.g., DoorDash, UberEats, Zomato, Swiggy), accounting software (e.g., QuickBooks, Tally), and SMS/messaging gateways as configured by the merchant.</li>
                        <li><strong>Legal & Regulatory Authorities:</strong> When required by court order, statutory tax audit, law enforcement request, or applicable law.</li>
                    </ul>
                </div>

                <div id="roles" class="privacy-section">
                    <h2>4. Roles Under Data Protection Laws</h2>
                    <ul>
                        <li><strong>POS Provider as Data Processor / Service Provider:</strong> For all guest/diner personal data (such as diner phone numbers, delivery addresses, and order histories), the Restaurant Merchant is the Data Controller, and we process this data strictly under their instructions.</li>
                        <li><strong>POS Provider as Data Controller:</strong> For merchant account credentials, direct billing records, and merchant support communications, we act as the primary Data Controller.</li>
                    </ul>

                    <div class="responsibility-callout">
                        <h4>MERCHANT RESPONSIBILITY</h4>
                        <p>Merchants are responsible for providing appropriate privacy notices and obtaining any required consent from their diners when collecting personal information for marketing or loyalty programs.</p>
                    </div>
                </div>

                <div id="retention" class="privacy-section">
                    <h2>5. Data Retention</h2>
                    <ul>
                        <li><strong>Operational & Transaction Data:</strong> Retained as long as the merchant maintains an active account to provide ongoing analytics, order lookups, and reporting.</li>
                        <li><strong>Financial & Tax Records:</strong> Invoices, tax summaries, and transaction logs are retained for statutory retention periods (typically 5 to 7 years) to comply with tax and accounting laws.</li>
                        <li><strong>Post-Termination Deletion:</strong> Upon account closure or contract termination, merchant operational data is archived and purged within 30 to 90 days, unless retention is mandated by law.</li>
                    </ul>
                </div>

                <div id="security" class="privacy-section">
                    <h2>6. Data Security & Storage Safeguards</h2>
                    <p>We maintain industry-standard physical, electronic, and procedural safeguards:</p>
                    <ul>
                        <li><strong>Encryption:</strong> Data in transit is protected using TLS 1.2/1.3 encryption. Sensitive data at rest is encrypted using AES-256 standards.</li>
                        <li><strong>PCI-DSS Compliance:</strong> Payment integrations strictly adhere to Payment Card Industry Data Security Standards.</li>
                        <li><strong>Role-Based Access Control (RBAC):</strong> Restricts terminal operations, cashier drawers, void/refund actions, and back-office reports based on assigned employee roles.</li>
                        <li><strong>Offline Security:</strong> Locally cached transactions on POS terminals are encrypted and purged or synced immediately upon internet reconnection.</li>
                    </ul>
                </div>

                <div id="rights" class="privacy-section">
                    <h2>7. Data Subject Rights & Choices</h2>
                    <p>Depending on applicable jurisdiction (e.g., GDPR, CCPA/CPRA, India DPDP Act), merchants and individuals have specific rights:</p>
                    <ul>
                        <li><strong>Access & Portability:</strong> Request an export of business records, sales history, or customer lists in a structured, machine-readable format.</li>
                        <li><strong>Correction & Update:</strong> Modify inaccurate account details via the POS settings dashboard.</li>
                        <li><strong>Erasure / Deletion:</strong> Request deletion of personal records, subject to statutory tax and financial retention requirements.</li>
                    </ul>
                    <p><strong>Contact for Privacy Requests:</strong> To exercise these rights, or if an end customer wishes to request removal of their contact data, contact us at <a href="mailto:privacy@billnbite.com">privacy@billnbite.com</a>.</p>
                </div>

                <div id="minors" class="privacy-section">
                    <h2>8. Children’s Privacy</h2>
                    <p>Our POS software and related services are commercial business tools intended for business operators and adults over the age of 18. We do not knowingly collect personal data from minors.</p>
                </div>

                <div id="updates" class="privacy-section">
                    <h2>9. Updates to This Policy</h2>
                    <p>We may update this Privacy Policy periodically to reflect changes in software features, security standards, or regulations. We will notify merchants of significant modifications via the POS dashboard or registered email.</p>
                </div>

                <div id="contact" class="privacy-section">
                    <h2>10. Contact Information</h2>
                    <p>If you have questions, feedback, or data privacy requests regarding this policy:</p>
                    
                    <ul class="contact-info-list">
                        <li>
                            <i class="ph ph-buildings"></i>
                            <strong>Company Name:</strong> SRV TECHNOLOGY
                        </li>
                        <li>
                            <i class="ph ph-envelope-simple"></i>
                            <strong>Privacy Email:</strong> <a href="mailto:info@billnbite.com">info@billnbite.com</a>
                        </li>
                        <li>
                            <i class="ph ph-map-pin"></i>
                            <strong>Office Address:</strong> BT Ranadeep Colony, Matigara-734010, Siliguri, WB, India.
                        </li>
                        <li>
                            <i class="ph ph-user-circle"></i>
                            <strong>Contact Name & Number:</strong> Vikrant Singh, <a href="tel:+917001769472">+91 70017 69472</a>
                        </li>
                    </ul>
                </div>
            </main>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" style="background:white !important; border-top: 1px solid #eaeaea;">
        <div class="container">
            <div class="footer-bottom" style="text-align: center; padding: 30px 0; color: var(--text-light); font-size: 0.9rem;">
                <p>© 2026 Bill & Bite. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.privacy-section');
            const sidebarLinks = document.querySelectorAll('.sidebar-card a');

            // Scrollspy logic to highlight sidebar based on active content section
            window.addEventListener('scroll', function() {
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    if (pageYOffset >= (sectionTop - 150)) {
                        current = section.getAttribute('id');
                    }
                });

                sidebarLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${current}`) {
                        link.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>
</html>
