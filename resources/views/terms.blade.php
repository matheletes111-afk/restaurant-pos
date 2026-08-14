<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Bill&Bite</title>
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="{{ asset('frontend/style.css') }}">
    <style>
        /* Extra styles to ensure the Terms page is premium */
        html {
            scroll-behavior: smooth;
        }
        
        .terms-header {
            background: linear-gradient(135deg, #fffaf5 0%, #ffffff 100%);
            padding: 80px 0 50px;
            border-bottom: 1px solid #eaeaea;
            position: relative;
        }
        
        .terms-header .header-content {
            max-width: 800px;
        }

        .terms-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 15px 0 10px;
            letter-spacing: -0.5px;
        }

        .terms-header .last-updated {
            color: var(--text-light);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .terms-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 50px;
            margin: 50px auto 100px;
            align-items: start;
        }

        .terms-sidebar {
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

        .terms-main {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .acceptance-box {
            background: var(--bg-subtle);
            border-left: 4px solid var(--primary-color);
            padding: 24px;
            border-radius: 0 12px 12px 0;
            margin-bottom: 40px;
            font-weight: 500;
            color: var(--text-dark);
            line-height: 1.7;
        }

        .acceptance-box h4 {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-size: 1.05rem;
        }

        .terms-section {
            margin-bottom: 45px;
            scroll-margin-top: 120px;
        }

        .terms-section h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--bg-subtle);
        }

        .terms-section p {
            color: #444444;
            margin-bottom: 16px;
            line-height: 1.75;
        }

        .terms-section ul {
            margin-bottom: 20px;
        }

        .terms-section li {
            position: relative;
            padding-left: 24px;
            margin-bottom: 12px;
            color: #444444;
            line-height: 1.65;
        }

        .terms-section li strong {
            color: var(--text-dark);
        }

        .terms-section li::before {
            content: "•";
            color: var(--primary-color);
            position: absolute;
            left: 8px;
            font-weight: bold;
            font-size: 1.25rem;
            top: -2px;
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
            .terms-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .terms-sidebar {
                position: static;
            }

            .terms-main {
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

    <!-- Terms Header -->
    <header class="terms-header">
        <div class="container">
            <div class="header-content">
                <span class="badge"><i class="ph ph-shield-check"></i> Legal Agreement</span>
                <h1>Terms & Conditions</h1>
                <p class="last-updated">Last Updated: August 14, 2026</p>
            </div>
        </div>
    </header>

    <!-- Terms Content Section -->
    <section class="terms-content-section">
        <div class="container terms-container">
            <!-- Sidebar Navigation -->
            <aside class="terms-sidebar">
                <div class="sidebar-card">
                    <h3>Table of Contents</h3>
                    <ul>
                        <li><a href="#agreement" class="active">The Agreement</a></li>
                        <li><a href="#services">1. License & Services</a></li>
                        <li><a href="#security">2. Security & Accounts</a></li>
                        <li><a href="#billing">3. Fees & Taxes</a></li>
                        <li><a href="#payments">4. Third-Party Payments</a></li>
                        <li><a href="#data">5. Merchant Data</a></li>
                        <li><a href="#support">6. Support & Uptime</a></li>
                        <li><a href="#ip">7. Intellectual Property</a></li>
                        <li><a href="#warranties">8. Disclaimers</a></li>
                        <li><a href="#liability">9. Liability Limit</a></li>
                        <li><a href="#termination">10. Term & Renewal</a></li>
                        <li><a href="#governing-law">11. Dispute Resolution</a></li>
                        <li><a href="#contact">12. Contact Details</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="terms-main">
                <div id="agreement" class="terms-section">
                    <p>These Terms and Conditions ("Agreement" or "Terms") constitute a legally binding agreement between SRV Technology (POS name - billnbite.com) ("Provider", "we", "us", or "our") and the restaurant business entity or individual operator ("Merchant", "Subscriber", "you", or "your") accessing or using our Point-of-Sale software, web dashboards, handheld terminals, Kitchen Display Systems (KDS), and related services (collectively, the "Services").</p>
                    
                    <div class="acceptance-box">
                        <h4>ACCEPTANCE OF TERMS</h4>
                        <p>By signing an order form, registering an account, installing the POS application, or otherwise using the Services, you acknowledge that you have read, understood, and agreed to be bound by these Terms and our Privacy Policy.</p>
                    </div>
                </div>

                <div id="services" class="terms-section">
                    <h2>1. Services and License Grant</h2>
                    <ul>
                        <li><strong>Software License:</strong> Subject to payment of applicable subscription fees, Provider grants Merchant a non-exclusive, non-transferable, revocable, and limited right and license to install, access, and use the POS software solely for Merchant's internal restaurant business operations.</li>
                        <li><strong>Scope of Access:</strong> The license permits usage on the specific number of POS terminals, handheld devices, Kitchen Display Systems (KDS), or outlet locations specified in Merchant's active subscription tier or order form.</li>
                        <li><strong>Hardware & Peripherals:</strong> Merchant is responsible for procuring and maintaining compatible hardware (terminals, receipt printers, barcode scanners, cash drawers, tablets) and reliable internet connectivity required to operate the Services.</li>
                    </ul>
                </div>

                <div id="security" class="terms-section">
                    <h2>2. Account Management and Security</h2>
                    <ul>
                        <li><strong>Account Credentials:</strong> Merchant is responsible for maintaining the confidentiality of administrative credentials, master passwords, and staff PIN codes.</li>
                        <li><strong>Role-Based Access:</strong> Merchant must configure role permissions appropriately (e.g., manager vs. cashier roles for handling voids, discounts, drawer openings, and refunds). Provider is not liable for unauthorized transactions executed via valid staff credentials.</li>
                        <li><strong>Unauthorized Use:</strong> Merchant must immediately notify Provider of any unauthorized access, security breach, or compromised credentials associated with their account.</li>
                    </ul>
                </div>

                <div id="billing" class="terms-section">
                    <h2>3. Fees, Billing, and Taxes</h2>
                    <ul>
                        <li><strong>Subscription Fees:</strong> Merchant agrees to pay the recurring subscription fees, hardware leasing costs (if applicable), and software add-on fees as specified during signup or in the applicable invoice.</li>
                        <li><strong>Payment Terms:</strong> Subscription fees are billed in advance on a monthly or annual basis. Invoices are due upon issuance unless otherwise agreed in writing.</li>
                        <li><strong>Late Payments & Suspension:</strong> Accounts with overdue balances beyond 15 days may be subjected to service suspension or restricted access until full outstanding payment is settled.</li>
                        <li><strong>Taxes:</strong> All stated fees are exclusive of applicable value-added tax (VAT), goods and services tax (GST), sales tax, or other governmental assessments, which Merchant shall be responsible for paying.</li>
                    </ul>
                </div>

                <div id="payments" class="terms-section">
                    <h2>4. Payment Processing and Third-Party Services</h2>
                    <ul>
                        <li><strong>Integrated Gateways:</strong> Merchant acknowledges that card processing, digital wallets, and payout settlements are executed by independent PCI-compliant third-party payment processors (e.g., Stripe, Square, Razorpay). Provider does not hold or custody transaction funds.</li>
                        <li><strong>Payment Processor Terms:</strong> Merchant agrees to comply with the separate terms and conditions, underwriting requirements, and fee schedules established by their integrated payment gateway.</li>
                        <li><strong>Third-Party Integrations:</strong> Provider may offer integrations with third-party delivery platforms (e.g., DoorDash, UberEats, Zomato), accounting software, or marketing tools. Provider is not responsible for the availability, accuracy, or service interruptions caused by third-party APIs.</li>
                    </ul>
                </div>

                <div id="data" class="terms-section">
                    <h2>5. Merchant Data, Guest Information, and Privacy</h2>
                    <ul>
                        <li><strong>Ownership of Data:</strong> Merchant retains all ownership, rights, title, and interest in all business data, menu configurations, pricing, and guest transaction records input into the Services ("Merchant Data").</li>
                        <li><strong>Data Processing:</strong> To the extent Merchant Data includes personal data of dining guests (such as phone numbers, emails, or delivery addresses), Merchant acts as the Data Controller and Provider acts as the Data Processor under applicable data protection legislation.</li>
                        <li><strong>Compliance with Laws:</strong> Merchant represents and warrants that it has collected all necessary consents and provided appropriate privacy notices to guests before sending marketing communications or loyalty updates through the POS.</li>
                    </ul>
                </div>

                <div id="support" class="terms-section">
                    <h2>6. Service Availability, Offline Mode, and Support</h2>
                    <ul>
                        <li><strong>Uptime Target:</strong> Provider strives to maintain a commercial uptime of 99.5% for cloud-based dashboards and sync services, excluding scheduled maintenance windows.</li>
                        <li><strong>Offline Mode Functionality:</strong> The POS terminal application may allow local caching of orders and receipts during temporary internet disruptions. Merchant is responsible for reconnecting to the internet to complete cloud data synchronization and card settlement.</li>
                        <li><strong>Support:</strong> Provider delivers technical support via email, phone, or dashboard chat during designated operating hours as specified in the merchant's plan.</li>
                    </ul>
                </div>

                <div id="ip" class="terms-section">
                    <h2>7. Intellectual Property Rights</h2>
                    <p>All intellectual property rights, trademarks, trade secrets, software architecture, UI/UX designs, and documentation related to the Services are and remain the exclusive property of Provider and its licensors. Merchant shall not:</p>
                    <ul>
                        <li><strong>Reverse Engineering:</strong> Reverse engineer, decompile, disassemble, or attempt to derive source code from any software component.</li>
                        <li><strong>Sublicensing:</strong> Resell, sublicense, lease, rent, or distribute the Services to any unauthorized third party or restaurant location.</li>
                        <li><strong>Derivative Works:</strong> Modify, adapt, or build competing derivative software products based on Provider's proprietary system.</li>
                    </ul>
                </div>

                <div id="warranties" class="terms-section">
                    <h2>8. Warranties and Disclaimers</h2>
                    <ul>
                        <li><strong>As-Is Warranty:</strong> Except as expressly stated herein, the Services, hardware integrations, and documentation are provided on an "AS IS" and "AS AVAILABLE" basis without warranties of any kind, whether express or implied.</li>
                        <li><strong>Tax & Accounting Disclaimer:</strong> While the POS system includes tax reporting and ledger calculation features, Provider does not provide legal, financial, or tax advice. Merchant is solely responsible for verifying tax calculations, menu tax rates, and regulatory filings.</li>
                    </ul>
                </div>

                <div id="liability" class="terms-section">
                    <h2>9. Limitation of Liability</h2>
                    <p>To the maximum extent permitted by applicable law, in no event shall Provider be liable for any indirect, incidental, special, consequential, or punitive damages, including loss of profits, restaurant revenue, business interruption, data loss, food spoilage, or chargebacks arising out of or in connection with the use or inability to use the Services.</p>
                    <ul>
                        <li><strong>Aggregate Liability Cap:</strong> Provider's total cumulative liability under this Agreement shall not exceed the total subscription fees paid by Merchant to Provider in the preceding twelve (12) months prior to the event giving rise to liability.</li>
                    </ul>
                </div>

                <div id="termination" class="terms-section">
                    <h2>10. Term, Renewal, and Termination</h2>
                    <ul>
                        <li><strong>Term and Auto-Renewal:</strong> This Agreement commences upon Merchant acceptance and continues for the billing term selected, automatically renewing for successive terms unless canceled prior to the renewal date.</li>
                        <li><strong>Termination for Convenience:</strong> Merchant may terminate their subscription at any time via the billing portal. Subscription fees paid in advance are non-refundable unless specified otherwise in writing.</li>
                        <li><strong>Termination for Cause:</strong> Either party may terminate this Agreement immediately upon written notice if the other party materially breaches these Terms and fails to cure such breach within thirty (30) days of notice.</li>
                        <li><strong>Post-Termination Data Access:</strong> Upon termination, Merchant will have 30 days to export historical sales, inventory, and guest data, after which Provider may securely purge all operational data.</li>
                    </ul>
                </div>

                <div id="governing-law" class="terms-section">
                    <h2>11. Governing Law and Dispute Resolution</h2>
                    <p>This Agreement shall be governed by and construed in accordance with the laws of Siliguri, West Bengal, India, without regard to its conflict of law principles. Any dispute or claim arising out of or relating to this Agreement shall be resolved through good-faith negotiations or submitted to the exclusive jurisdiction of the competent courts in Siliguri, West Bengal, India.</p>
                </div>

                <div id="contact" class="terms-section">
                    <h2>12. Contact and Inquiries</h2>
                    <p>For legal notices, billing inquiries, or questions regarding these Terms, contact us at:</p>
                    
                    <ul class="contact-info-list">
                        <li>
                            <i class="ph ph-buildings"></i>
                            <strong>Company Name:</strong> SRV TECHNOLOGY
                        </li>
                        <li>
                            <i class="ph ph-envelope-simple"></i>
                            <strong>Legal & Billing Email:</strong> <a href="mailto:info@billnbite.com">info@billnbite.com</a>
                        </li>
                        <li>
                            <i class="ph ph-map-pin"></i>
                            <strong>Mailing Address:</strong> BT Ranadeep Colony, Matigara-734010, Siliguri, WB, India.
                        </li>
                        <li>
                            <i class="ph ph-user-circle"></i>
                            <strong>Customer Support:</strong> Vikrant Singh, <a href="tel:+917001769472">+91 70017 69472</a>
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
            const sections = document.querySelectorAll('.terms-section');
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
