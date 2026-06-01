<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill&Bite - Complete Restaurant Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="{{asset('frontend/style.css')}}">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="#" class="logo">
                <span class="logo-text">Bill<span class="text-orange">&</span>Bite</span>
            </a>
            <div class="nav-links">
                <a href="#">Features</a>
                <a href="#">Pricing</a>
                <a href="#">How It Works</a>
                <a href="#">Testimonials</a>
                <a href="#">FAQ</a>
            </div>
            <div class="nav-actions">
                <a href="#" class="login-link">Login</a>
                <a href="#" class="btn btn-primary">Book a Demo</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container hero-container">
            <div class="hero-content">
                <div class="badge">
                    <span class="badge-dot"></span> Complete Restaurant Management System
                </div>
                <h1>Manage Your Restaurant.<br>Simplify <span class="text-orange">Every Operation.</span></h1>
                <p>Bill & Bite is an all-in-one restaurant management system that helps you manage orders, staff, kitchen, inventory, payments and more — from one powerful dashboard.</p>
                
                <div class="hero-features">
                    <div class="hf-item">
                        <i class="ph ph-check-circle"></i> Easy To Use
                    </div>
                    <div class="hf-item">
                        <i class="ph ph-check-circle"></i> All-In-One Local Solution
                    </div>
                    <div class="hf-item">
                        <i class="ph ph-check-circle"></i> Cloud-Based
                    </div>
                    <div class="hf-item">
                        <i class="ph ph-check-circle"></i> Secure & Reliable
                    </div>
                </div>

                <div class="hero-cta">
                    <a href="#" class="btn btn-primary btn-lg">Book a Free Demo <i class="ph ph-arrow-right"></i></a>
                    <a href="#" class="btn btn-outline btn-lg">Explore Features</a>
                </div>

                <div class="trust-indicator">
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
                </div>
            </div>
            <div class="hero-graphics">
                <!-- Using a placeholder for the complex dashboard/mobile mockup -->
                <div class="graphic-wrapper">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=1000" alt="Dashboard Mockup" class="dashboard-img">
                    <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&q=80&w=400" alt="Mobile App Mockup" class="mobile-img">
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="features">
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
                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&q=80&w=800" alt="Restaurant Manager" class="main-b-img">
                    
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
                <i class="ph ph-storefront"></i>
                <h3>1000+</h3>
                <p>Happy Restaurants</p>
            </div>
            <div class="stat-item">
                <i class="ph ph-receipt"></i>
                <h3>50K+</h3>
                <p>Daily Orders Managed</p>
            </div>
            <div class="stat-item">
                <i class="ph ph-shield-check"></i>
                <h3>99.9%</h3>
                <p>Uptime & Reliability</p>
            </div>
            <div class="stat-item">
                <i class="ph ph-headset"></i>
                <h3>24/7</h3>
                <p>Customer Support</p>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-subtitle">TRUSTED BY RESTAURANT OWNERS</span>
                <h2>See What <span class="text-orange">Our Customers</span> Say</h2>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <i class="ph-fill ph-quotes text-orange quote-icon"></i>
                    <p class="review-text">Bill & Bite has completely transformed how we run our restaurant. It's easy to use and the support team is outstanding!</p>
                    <div class="stars mb-4">
                        <i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i>
                    </div>
                    <div class="reviewer">
                        <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=crop&q=80&w=100" alt="Rahul Verma">
                        <div>
                            <h4>Rahul Verma</h4>
                            <span>The Spice Hub</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <i class="ph-fill ph-quotes text-orange quote-icon"></i>
                    <p class="review-text">From inventory to billing to online orders, everything is streamlined. Highly recommended for every restaurant.</p>
                    <div class="stars mb-4">
                        <i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i>
                    </div>
                    <div class="reviewer">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=100" alt="Anjali Mehta">
                        <div>
                            <h4>Anjali Mehta</h4>
                            <span>Foodies Lounge</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <i class="ph-fill ph-quotes text-orange quote-icon"></i>
                    <p class="review-text">The QR ordering feature is a game changer. Our customers love it and our order management is so much better now.</p>
                    <div class="stars mb-4">
                        <i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i><i class="ph-fill ph-star"></i>
                    </div>
                    <div class="reviewer">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=100" alt="Vikram Singh">
                        <div>
                            <h4>Vikram Singh</h4>
                            <span>Burger Town</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
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
                    <h3>Book Your Free Demo</h3>
                    <form class="demo-form" id="demoLeadForm">
                        @csrf
                        <div id="form-alert" style="display: none; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 500; font-size: 0.9rem; text-align: center;"></div>
                        
                        <div class="form-group-row">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" placeholder="John Doe" required>
                            </div>
                            <div class="form-group">
                                <label>Restaurant Name</label>
                                <input type="text" name="restaurant_name" placeholder="The Food Place">
                            </div>
                        </div>
                        <div class="form-group-row">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" name="phone_number" placeholder="+91 98765 43210">
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email_address" placeholder="john@example.com" required>
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
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="logo mb-4">
                        <span class="logo-text">Bill<span class="text-orange">&</span>Bite</span>
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
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">How It Works</a></li>
                        <li><a href="#">Updates</a></li>
                        <li><a href="#">Integrations</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contact Us</h4>
                    <ul>
                        <li><i class="ph ph-phone"></i> +91 98765 43210</li>
                        <li><i class="ph ph-envelope-simple"></i> hello@billandbite.com</li>
                        <li><i class="ph ph-map-pin"></i> Bangalore, India</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2024 Bill & Bite. All rights reserved.</p>
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
</body>
</html>
