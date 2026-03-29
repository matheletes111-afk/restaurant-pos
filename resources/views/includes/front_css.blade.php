<!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <!-- Type.js -->
<style>
        :root {
            --primary: #D4AF37;
            --primary-dark: #B8941F;
            --secondary: #1a1a1a;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --success: #28a745;
            --border-radius: 8px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #fff;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 3.5rem;
        }

        h2 {
            font-size: 2.5rem;
            position: relative;
            display: inline-block;
            margin-bottom: 2.5rem;
        }

        h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 4px;
            background-color: var(--primary);
        }

        h3 {
            font-size: 1.8rem;
        }

        p {
            margin-bottom: 1.5rem;
            color: var(--gray);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section {
            padding: 80px 0;
        }

        .section-light {
            background-color: var(--light);
        }

        .section-dark {
            background-color: var(--secondary);
            color: white;
        }

        .btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            border: none;
            outline: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: var(--box-shadow);
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-secondary:hover {
            background-color: var(--primary);
            color: white;
        }

        .btn-success {
            background-color: #2ecc71;
            color: white;
            font-size: 1.1rem;
            padding: 14px 32px;
        }

        .btn-success:hover {
            background-color: #27ae60;
            transform: translateY(-3px);
            box-shadow: var(--box-shadow);
        }

        .text-center {
            text-align: center;
        }

        .mb-4 {
            margin-bottom: 2rem;
        }

        .mb-5 {
            margin-bottom: 3rem;
        }

        .mt-3 {
            margin-top: 1.5rem;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta-section h2 {
            color: white;
            font-size: 2.8rem;
        }

        .cta-section h2:after {
            background-color: white;
            left: 50%;
            transform: translateX(-50%);
        }

        .cta-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .cta-tagline {
            font-size: 1.1rem;
            margin-top: 10px;
            font-style: italic;
            color: rgba(255, 255, 255, 0.85);
        }

        /* Header & Navigation */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            padding: 20px 0;
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .header-scrolled {
            padding: 15px 0;
            background-color: rgba(255, 255, 255, 0.98);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--secondary);
        }

        .logo span {
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            font-weight: 500;
            position: relative;
        }

        .nav-links a:after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: var(--transition);
        }

        .nav-links a:hover:after {
            width: 100%;
        }

        .mobile-toggle {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            padding-top: 180px;
            padding-bottom: 100px;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .hero p {
            font-size: 1.3rem;
            max-width: 800px;
            margin: 0 auto 2.5rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .typed-text {
            color: var(--primary);
        }

        .hero-cta {
            margin-top: 2rem;
        }

        /* Features Section */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 3rem;
        }

        .feature-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        /* Why Choose Us */
        .advantages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 3rem;
        }

        .advantage-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--box-shadow);
        }

        .advantage-card h4 {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--secondary);
        }

        .advantage-card i {
            color: var(--primary);
        }

        /* Form Section */
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: var(--border-radius);
            padding: 40px;
            box-shadow: var(--box-shadow);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus {
            border-color: var(--primary);
            outline: none;
        }

        .form-note {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 0.5rem;
        }

        .required {
            color: #e74c3c;
        }

        /* Footer */
        footer {
            background-color: var(--secondary);
            color: white;
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-logo {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .footer-links h4 {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .footer-links h4:after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--primary);
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--primary);
            padding-left: 5px;
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            h1 {
                font-size: 3rem;
            }
            
            h2 {
                font-size: 2.2rem;
            }
            
            .hero {
                padding-top: 160px;
                padding-bottom: 80px;
            }
            
            .hero h1 {
                font-size: 3.2rem;
            }
            
            .cta-section h2 {
                font-size: 2.4rem;
            }
        }

        @media (max-width: 768px) {
            .mobile-toggle {
                display: block;
            }
            
            .nav-links {
                position: fixed;
                top: 80px;
                left: 0;
                width: 100%;
                background-color: white;
                flex-direction: column;
                align-items: center;
                padding: 20px 0;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
                transform: translateY(-150%);
                transition: transform 0.5s ease;
                z-index: 999;
            }
            
            .nav-links.active {
                transform: translateY(0);
            }
            
            .hero h1 {
                font-size: 2.8rem;
            }
            
            .section {
                padding: 60px 0;
            }
            
            .cta-section {
                padding: 60px 0;
            }
            
            .cta-section h2 {
                font-size: 2rem;
            }
            
            .form-container {
                padding: 30px 20px;
            }
        }

        @media (max-width: 576px) {
            h1 {
                font-size: 2.5rem;
            }
            
            h2 {
                font-size: 2rem;
            }
            
            .hero {
                padding-top: 140px;
                padding-bottom: 60px;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .cta-section h2 {
                font-size: 1.8rem;
            }
            
            .cta-subtitle {
                font-size: 1.1rem;
            }
            
            .btn {
                padding: 10px 20px;
            }
            
            .btn-success {
                padding: 12px 24px;
                font-size: 1rem;
            }
            
            .features-grid, .advantages-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>