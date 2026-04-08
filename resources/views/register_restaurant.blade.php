<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Register Your Restaurant | RestoPOS</title>
    <!-- Google Fonts & Inter (matching homepage) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 (free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #FF6A00;
            --primary-light: #FF8C42;
            --bg: #0B0B0B;
            --secondary: #121212;
            --muted: #888888;
            --border: rgba(255,255,255,0.08);
            --glow: 0 0 24px rgba(255,106,0,0.25);
            --card-bg: rgba(255,255,255,0.03);
            --glass-border: 1px solid rgba(255,255,255,0.08);
        }

        body {
            background: var(--bg);
            color: #fff;
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            line-height: 1.5;
        }

        /* background glow blobs (matching hero style) */
        .register-blob {
            position: fixed;
            width: 800px;
            height: 800px;
            background: rgba(255,106,0,0.08);
            filter: blur(140px);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .blob-1 {
            top: -20%;
            right: -20%;
        }

        .blob-2 {
            bottom: -20%;
            left: -20%;
            background: rgba(255,106,0,0.05);
        }

        /* registration main container */
        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5rem 1.5rem;
            position: relative;
            z-index: 2;
        }

        .register-card {
            max-width: 880px;
            width: 100%;
            background: rgba(18, 18, 18, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: var(--glass-border);
            border-radius: 3rem;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5), var(--glow);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .register-header {
            background: linear-gradient(135deg, rgba(255,106,0,0.15), rgba(255,140,66,0.05));
            padding: 2rem 2rem 1.8rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .register-header h1 {
            font-size: clamp(1.8rem, 5vw, 2.6rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #FF6A00, #FF8C42);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .register-header p {
            color: var(--muted);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .register-body {
            padding: 2.5rem;
        }

        /* step indicator modern */
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2.5rem;
            position: relative;
            flex-wrap: wrap;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            z-index: 2;
            flex: 0 1 auto;
        }

        .step-circle {
            width: 48px;
            height: 48px;
            border-radius: 999px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--muted);
            transition: all 0.2s ease;
        }

        .step.active .step-circle {
            background: linear-gradient(135deg, #FF6A00, #FF8C42);
            border: none;
            color: #fff;
            box-shadow: var(--glow);
        }

        .step-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted);
        }

        .step.active .step-label {
            color: var(--primary-light);
        }

        .step-line {
            position: absolute;
            top: 24px;
            left: calc(50% - 80px);
            width: 160px;
            height: 2px;
            background: rgba(255,255,255,0.1);
            z-index: 1;
        }

        .step-line-fill {
            position: absolute;
            top: 24px;
            left: calc(50% - 80px);
            width: 0%;
            height: 2px;
            background: linear-gradient(90deg, #FF6A00, #FF8C42);
            z-index: 2;
            transition: width 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        /* form styling */
        .form-section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.8rem;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border-left: 3px solid var(--primary);
            padding-left: 1rem;
        }

        .form-section-title i {
            color: var(--primary);
            font-size: 1.4rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 0.5rem;
            display: block;
            color: rgba(255,255,255,0.7);
        }

        .required::after {
            content: '*';
            color: var(--primary);
            margin-left: 4px;
        }

        .form-control {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 0.9rem 1rem;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255,106,0,0.2);
            background: rgba(255,255,255,0.08);
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.3);
            font-weight: 400;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 2.7rem;
            cursor: pointer;
            color: var(--muted);
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .form-group {
            position: relative;
        }

        /* checkbox modern */
        .custom-check {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
        }

        .custom-check input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .custom-check label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
        }

        .custom-check a {
            color: var(--primary-light);
            text-decoration: none;
            font-weight: 600;
        }

        .custom-check a:hover {
            text-decoration: underline;
        }

        /* buttons */
        .btn-gradient {
            background: linear-gradient(135deg, #FF6A00, #FF8C42);
            border: none;
            border-radius: 40px;
            padding: 0.9rem 1.8rem;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.02em;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.2s ease;
            cursor: pointer;
            box-shadow: var(--glow);
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            opacity: 0.92;
        }

        .btn-outline-light {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 40px;
            padding: 0.9rem 1.8rem;
            font-weight: 600;
            color: #fff;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.3);
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            gap: 1rem;
        }

        .login-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            font-size: 0.85rem;
            color: var(--muted);
        }

        .login-link a {
            color: var(--primary-light);
            font-weight: 700;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* alerts */
        .alert-custom {
            background: rgba(255,80,80,0.1);
            border-left: 4px solid #ff4d4d;
            padding: 1rem;
            border-radius: 20px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.9);
        }

        .alert-success-custom {
            background: rgba(46, 204, 113, 0.12);
            border-left: 4px solid #2ecc71;
            padding: 1rem;
            border-radius: 20px;
            margin-bottom: 1.5rem;
        }

        /* modals dark */
        .modal-content {
            background: var(--secondary);
            border: 1px solid var(--border);
            border-radius: 2rem;
            color: #fff;
        }

        .modal-header {
            border-bottom-color: var(--border);
        }

        .modal-footer {
            border-top-color: var(--border);
        }

        .btn-close-white {
            filter: invert(1);
        }

        /* responsive */
        @media (max-width: 768px) {
            .register-body {
                padding: 1.8rem;
            }
            .step-line, .step-line-fill {
                display: none;
            }
            .step-indicator {
                gap: 1.2rem;
            }
            .step-circle {
                width: 42px;
                height: 42px;
                font-size: 1rem;
            }
            .action-buttons {
                flex-direction: column;
            }
            .btn-gradient, .btn-outline-light {
                justify-content: center;
            }
        }

        /* scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #111;
        }
        ::-webkit-scrollbar-thumb {
            background: #FF6A00;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="register-blob blob-1"></div>
<div class="register-blob blob-2"></div>

<div class="register-wrapper">
    <div class="register-card">
        <div class="register-header">
            <h1><i class="fas fa-utensils me-2"></i> Register Your Restaurant</h1>
            <p>Power up your business with RestoPOS — seamless, fast, India-ready</p>
        </div>

        <div class="register-body">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Restaurant Info</div>
                </div>
                <div class="step" id="step2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Owner Details</div>
                </div>
                <div class="step-line"></div>
                <div class="step-line-fill" id="stepLineFill"></div>
            </div>

            <!-- Error / Success messages (preserved dynamic style) -->
            @if ($errors->any())
                <div class="alert-custom">
                    <ul style="margin:0; padding-left:1.2rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert-success-custom">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Registration Form: Laravel blade preserves original action, method, csrf -->
            <form method="POST" action="{{ route('restaurant.register') }}">
                @csrf

                <!-- SECTION 1: RESTAURANT INFO -->
                <div id="section1">
                    <div class="form-section-title">
                        <i class="fas fa-store"></i> Restaurant Information
                    </div>
                    <div class="row" style="display: flex; gap: 1.2rem; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 200px;">
                            <div class="form-group">
                                <label class="form-label required">Restaurant Name</label>
                                <input type="text" name="restaurant_name" class="form-control" 
                                       value="{{ old('restaurant_name') }}" required 
                                       placeholder="e.g., Spice Garden">
                            </div>
                        </div>
                        <div style="flex: 1; min-width: 160px;">
                            <div class="form-group">
                                <label class="form-label required">Pincode</label>
                                <input type="text" name="pincode" class="form-control" 
                                       value="{{ old('pincode') }}" required 
                                       placeholder="e.g., 110001">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Full Address</label>
                        <input type="text" name="address" class="form-control" 
                               value="{{ old('address') }}" required 
                               placeholder="Street, city, landmark">
                    </div>

                    <div class="action-buttons">
                        <div></div>
                        <button type="button" class="btn-gradient" onclick="showSection(2)">
                            Next Step <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- SECTION 2: OWNER DETAILS -->
                <div id="section2" style="display: none;">
                    <div class="form-section-title">
                        <i class="fas fa-user-circle"></i> Owner & Account
                    </div>
                    <div class="row" style="display: flex; gap: 1.2rem; flex-wrap: wrap;">
                        <div style="flex: 1;">
                            <div class="form-group">
                                <label class="form-label required">Owner Name</label>
                                <input type="text" name="name" class="form-control" 
                                       value="{{ old('name') }}" required 
                                       placeholder="Full name">
                            </div>
                        </div>
                        <div style="flex: 1;">
                            <div class="form-group">
                                <label class="form-label required">Email Address</label>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ old('email') }}" required 
                                       placeholder="hello@restaurant.com">
                                @if($errors->has('email'))
                                    <small class="text-warning" style="font-size: 0.7rem;">{{ $errors->first('email') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display: flex; gap: 1.2rem; flex-wrap: wrap;">
                        <div style="flex: 1;">
                            <div class="form-group">
                                <label class="form-label required">Phone Number</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="{{ old('phone') }}" required 
                                       placeholder="+91 98765 43210">
                            </div>
                        </div>
                        <div style="flex: 1;">
                            <div class="form-group">
                                <label class="form-label required">Password</label>
                                <input type="password" name="password" id="password" class="form-control" 
                                       required placeholder="••••••••">
                                <span class="password-toggle" onclick="togglePassword()">
                                    <i class="fa-regular fa-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Terms checkbox (required) -->
                    <div class="custom-check">
                        <input type="checkbox" id="terms" required>
                        <label for="terms">
                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a> 
                            and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                        </label>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn-outline-light" onclick="showSection(1)">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="submit" class="btn-gradient">
                            <i class="fas fa-user-plus"></i> Register Restaurant
                        </button>
                    </div>
                </div>
            </form>

            <div class="login-link">
                Already have an account? <a href="{{ route('login') }}">Sign in to dashboard →</a>
            </div>
        </div>
    </div>
</div>



<!-- Bootstrap 5 JS (required for modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Step indicator and section toggling (preserving original logic but with modern design)
    function showSection(sectionNumber) {
        const section1 = document.getElementById('section1');
        const section2 = document.getElementById('section2');
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const stepLineFill = document.getElementById('stepLineFill');

        if (sectionNumber === 1) {
            section1.style.display = 'block';
            section2.style.display = 'none';
            step1.classList.add('active');
            step2.classList.remove('active');
            if (stepLineFill) stepLineFill.style.width = '0%';
        } else {
            section1.style.display = 'none';
            section2.style.display = 'block';
            step1.classList.add('active');
            step2.classList.add('active');
            if (stepLineFill) stepLineFill.style.width = '100%';
        }
    }

    // Password visibility toggle
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        if (!passwordInput) return;
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    // Form validation (no change in functionality: password length + terms)
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize step indicator (step1 active)
        showSection(1);

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password');
                const termsCheck = document.getElementById('terms');

                // only validate if second section is visible (i.e., both fields exist)
                if (password && termsCheck && window.getComputedStyle(document.getElementById('section2')).display !== 'none') {
                    if (password.value.length < 6) {
                        e.preventDefault();
                        alert('Password must be at least 6 characters long.');
                        password.focus();
                        return false;
                    }
                    if (!termsCheck.checked) {
                        e.preventDefault();
                        alert('Please agree to the Terms of Service and Privacy Policy.');
                        return false;
                    }
                }
                // also validate if somehow section1 is submitted (edge case)
                if (document.getElementById('section1').style.display !== 'none') {
                    // no password check yet, but we allow next - but validation will trigger only on final submit
                }
                return true;
            });
        }
    });
</script>

<!-- ensure that any inline old blade errors display nicely -->
</body>
</html>