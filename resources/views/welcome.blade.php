<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestoPOS - Modern Restaurant POS System</title>
    @include('includes.front_css')
    
    
</head>
<body>
    <!-- Header & Navigation -->
    <header id="header">
        <div class="container">
            <nav>
                <a href="#" class="logo">Resto<span>POS</span></a>
                <div class="nav-links" id="navLinks">
                    <a href="#features">Features</a>
                    <a href="#advantages">Why Choose Us</a>
                    <a href="#enquiry">Get Demo</a>
                    <a href="#contact">Contact</a>
                    <a href="#cta" class="btn btn-primary" style="padding: 8px 20px;">Register Free</a>
                </div>
                <div class="mobile-toggle" id="mobileToggle">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <h1 data-aos="fade-up" data-aos-duration="1000">
                Modern POS System for <span class="typed-text"></span>
            </h1>
            <p data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                Streamline your restaurant operations with our AI-powered POS system. Manage orders, inventory, staff, and customer experience from one intuitive platform.
            </p>
            <div class="hero-cta" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <a href="#cta" class="btn btn-success">Register Now for FREE</a>
                <p class="cta-tagline">Make your restaurant operation paperless</p>
                <a href="#features" class="btn btn-secondary" style="margin-top: 20px;">Explore Features</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 data-aos="fade-up">Powerful POS Features</h2>
                <p data-aos="fade-up" data-aos-delay="200">Everything you need to run your restaurant efficiently</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3>Menu Master</h3>
                    <p>Easily create, update, and manage your restaurant menu with categories, prices, and descriptions.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-chair"></i>
                    </div>
                    <h3>Table Master</h3>
                    <p>Manage table layouts, assign orders to tables, and track table availability in real-time.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3>Order Master</h3>
                    <p>Process dine-in, takeaway, and delivery orders quickly with our streamlined interface.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Order Report</h3>
                    <p>Generate detailed sales reports, track performance, and gain insights into your business.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3>Inventory Management</h3>
                    <p>Track stock levels, set reorder points, and manage suppliers to reduce waste and costs.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-oven"></i>
                    </div>
                    <h3>Kitchen Order Management</h3>
                    <p>Send orders directly to the kitchen display system and track preparation status in real-time.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Staff Management</h3>
                    <p>Assign roles, track shifts, and manage employee performance with our comprehensive tools.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3>Order via QR Code</h3>
                    <p>Allow customers to view menus and place orders directly from their smartphones using QR codes.</p>
                </div>
                
                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3>AI Powered System</h3>
                    <p>Get AI-powered insights, sales predictions, and personalized recommendations for your menu.</p>
                </div>
            </div>
            
            <div class="text-center mt-5" data-aos="fade-up">
                <a href="#cta" class="btn btn-success">Start Your FREE Trial</a>
                <p class="cta-tagline" style="color: var(--gray); margin-top: 10px;">No credit card required • 30-day free trial</p>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="section section-light" id="advantages">
        <div class="container">
            <div class="text-center mb-5">
                <h2 data-aos="fade-up">Why Choose RestoPOS?</h2>
                <p data-aos="fade-up" data-aos-delay="200">Discover the advantages of our modern restaurant POS system</p>
            </div>
            
            <div class="advantages-grid">
                <div class="advantage-card" data-aos="fade-up" data-aos-delay="100">
                    <h4><i class="fas fa-bolt"></i> Easy to Use</h4>
                    <p>Intuitive interface designed specifically for restaurant staff. Minimal training required with our user-friendly dashboard.</p>
                </div>
                
                <div class="advantage-card" data-aos="fade-up" data-aos-delay="200">
                    <h4><i class="fas fa-tachometer-alt"></i> Faster Order Processing</h4>
                    <p>Reduce order taking time by 60% with our streamlined workflow. Serve more customers during peak hours.</p>
                </div>
                
                <div class="advantage-card" data-aos="fade-up" data-aos-delay="300">
                    <h4><i class="fas fa-warehouse"></i> Smart Inventory Control</h4>
                    <p>Automatically track inventory levels, predict shortages, and generate purchase orders to reduce waste.</p>
                </div>
                
                <div class="advantage-card" data-aos="fade-up" data-aos-delay="100">
                    <h4><i class="fas fa-brain"></i> AI-Powered Insights</h4>
                    <p>Get actionable insights on customer preferences, peak hours, and profitable menu items to boost sales.</p>
                </div>
                
                <div class="advantage-card" data-aos="fade-up" data-aos-delay="200">
                    <h4><i class="fas fa-qrcode"></i> QR-Based Ordering</h4>
                    <p>Enable contactless ordering with QR codes. Reduce wait times and improve customer experience.</p>
                </div>
                
                <div class="advantage-card" data-aos="fade-up" data-aos-delay="300">
                    <h4><i class="fas fa-expand-arrows-alt"></i> Scalable Solution</h4>
                    <p>Suitable for small cafes to large restaurant chains. Easily add new locations and manage them centrally.</p>
                </div>
            </div>
            
            
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="cta">
        <div class="container">
            <h2 data-aos="fade-up">Ready to Transform Your Restaurant?</h2>
            <p class="cta-subtitle" data-aos="fade-up" data-aos-delay="200">
                Join thousands of restaurants that have streamlined their operations with RestoPOS
            </p>
            <div data-aos="fade-up" data-aos-delay="400">
                <a href="#enquiry" class="btn btn-success" style="background-color: white; color: var(--primary-dark); font-weight: 700; padding: 16px 40px;">
                    <i class="fas fa-rocket"></i> REGISTER NOW FOR FREE
                </a>
                <p class="cta-tagline">Make your restaurant operation paperless • 30-day free trial</p>
            </div>
            <div class="mt-3" data-aos="fade-up" data-aos-delay="600">
                <p style="color: rgba(255, 255, 255, 0.9); font-size: 1.1rem;">
                    <i class="fas fa-check-circle"></i> No setup fees &bull; 
                    <i class="fas fa-check-circle"></i> No credit card required &bull; 
                    <i class="fas fa-check-circle"></i> Full support included
                </p>
            </div>
        </div>
    </section>

    <!-- Enquiry Form Section -->
    <section class="section" id="enquiry">
        <div class="container">
            <div class="text-center mb-5">
                <h2 data-aos="fade-up">Request a Free Demo</h2>
                <p data-aos="fade-up" data-aos-delay="200">Get a personalized demo of RestoPOS tailored to your restaurant's needs</p>
            </div>
            
            <div class="form-container" data-aos="fade-up">
                <!-- Laravel Blade Form Start -->
                <form action="" method="POST">
                    
                    
                    <div class="form-group">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="Your full name">
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile">Mobile Number <span class="required">*</span></label>
                        <input type="tel" id="mobile" name="mobile" required placeholder="10-digit mobile number">
                        <div class="form-note">We'll contact you on this number to schedule the demo</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Your email address (optional)">
                    </div>
                    
                    <div class="form-group">
                        <label for="restaurant">Restaurant Name</label>
                        <input type="text" id="restaurant" name="restaurant" placeholder="Your restaurant name">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Additional Requirements</label>
                        <input type="text" id="message" name="message" placeholder="Any specific requirements?">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px;">Request Free Demo</button>
                    <p class="text-center mt-3" style="font-size: 0.9rem;">
                        Or <a href="#cta" style="color: var(--primary); font-weight: 600;">register for instant FREE access</a>
                    </p>
                </form>
                <!-- Laravel Blade Form End -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="section-dark" id="contact">
        <div class="container">
            <div class="footer-content">
                <div>
                    <div class="footer-logo">Resto<span>POS</span></div>
                    <p>Modern POS system designed specifically for restaurants of all sizes. Streamline operations, increase efficiency, and boost profits.</p>
                    <a href="#cta" class="btn btn-success" style="margin-top: 15px;">Start Free Trial</a>
                </div>
                
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#advantages">Why Choose Us</a></li>
                        <li><a href="#enquiry">Get Demo</a></li>
                        <li><a href="#cta">Free Trial</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Contact Us</h4>
                    <ul>
                        <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope"></i> info@restopos.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Restaurant Ave, New York, NY</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                &copy; 2023 RestoPOS. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    @include('includes.front_script')
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Initialize Type.js
        document.addEventListener('DOMContentLoaded', function() {
            var typed = new Typed('.typed-text', {
                strings: ['Fine Dining', 'Cafes', 'Bars', 'Food Trucks', 'Restaurants'],
                typeSpeed: 80,
                backSpeed: 50,
                loop: true,
                backDelay: 1500
            });
        });

        // Mobile Navigation Toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navLinks = document.getElementById('navLinks');
        
        mobileToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            mobileToggle.innerHTML = navLinks.classList.contains('active') 
                ? '<i class="fas fa-times"></i>' 
                : '<i class="fas fa-bars"></i>';
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function() {
                navLinks.classList.remove('active');
                mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 100) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
        });

        // Form validation
        const enquiryForm = document.querySelector('form');
        if (enquiryForm) {
            enquiryForm.addEventListener('submit', function(e) {
                const mobileInput = document.getElementById('mobile');
                const mobileValue = mobileInput.value.trim();
                
                // Basic mobile validation
                if (mobileValue.length < 10 || !/^\d+$/.test(mobileValue)) {
                    e.preventDefault();
                    alert('Please enter a valid 10-digit mobile number.');
                    mobileInput.focus();
                }
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>