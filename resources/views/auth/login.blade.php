<!DOCTYPE html>
<html lang="en">
<head>
    <title>Restaurant POS Login • Bill&Bite</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #ff5e14;
            --primary-hover: #ea580c;
            --primary-light: rgba(255, 94, 20, 0.08);
            --primary-gradient: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
            --primary-glow: rgba(255, 94, 20, 0.35);

            --dark-bg: #080d19;
            --dark-card: #0f172a;
            --dark-card-border: rgba(255, 255, 255, 0.08);
            --dark-muted: #94a3b8;
            --dark-light: #f8fafc;

            --form-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --border-color: #e2e8f0;
            --input-bg: #f8fafc;

            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
            --shadow-sm: 0 4px 16px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 12px 32px rgba(15, 23, 42, 0.08);
            --shadow-primary: 0 8px 24px rgba(255, 94, 20, 0.32);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--form-bg);
            color: var(--text-main);
        }

        /* Full Layout Split (Zero Scroll on Desktop) */
        .login-layout-wrapper {
            display: flex;
            height: 100vh;
            max-height: 100vh;
            width: 100%;
            overflow: hidden;
        }

        /* ===================================================
           LEFT SIDE: LOGIN FORM COLUMN
           =================================================== */
        .login-form-column {
            flex: 0 0 46%;
            max-width: 46%;
            height: 100vh;
            max-height: 100vh;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 32px 52px;
            position: relative;
            z-index: 10;
            box-shadow: 16px 0 40px rgba(15, 23, 42, 0.03);
            overflow-y: auto;
        }

        .login-form-inner {
            width: 100%;
            max-width: 420px;
            margin: auto;
        }

        /* Top Brand Header */
        .brand-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .brand-logo-wrap {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
        }

        .brand-logo-img {
            max-height: 48px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.05));
            transition: transform 0.2s ease;
        }

        .brand-logo-img:hover {
            transform: scale(1.02);
        }

        .pos-terminal-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.22);
            color: #059669;
            font-size: 0.74rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .live-dot {
            width: 7px;
            height: 7px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        /* Form Hero Titles */
        .form-heading-group {
            margin-bottom: 20px;
        }

        .form-title-text {
            font-family: 'Outfit', sans-serif;
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .form-sub-text {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.45;
        }

        /* Alerts */
        .alert-modern {
            border-radius: var(--radius-md);
            padding: 11px 15px;
            font-size: 0.84rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            border: none;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            animation: slideInDown 0.3s ease;
        }

        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-modern-danger {
            background: #fef2f2;
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #b91c1c;
        }

        .alert-modern-success {
            background: #ecfdf5;
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #047857;
        }

        .alert-modern .btn-close {
            margin-left: auto;
            padding: 0;
            font-size: 0.75rem;
            background-size: 0.75em;
        }

        /* Form Controls */
        .form-group-custom {
            margin-bottom: 15px;
        }

        .form-label-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
            letter-spacing: 0.01em;
        }

        .form-input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-lead-icon {
            position: absolute;
            left: 15px;
            color: var(--text-light);
            font-size: 0.95rem;
            pointer-events: none;
            transition: color 0.2s ease;
            z-index: 2;
        }

        .form-input-field {
            width: 100%;
            height: 48px;
            background: var(--input-bg);
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0 16px 0 44px;
            font-size: 0.92rem;
            font-weight: 500;
            color: var(--text-main);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
        }

        .form-input-field::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .form-input-field:focus {
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3.5px rgba(255, 94, 20, 0.12), 0 4px 14px rgba(255, 94, 20, 0.06);
        }

        .form-input-field:focus + .input-lead-icon,
        .form-input-container:focus-within .input-lead-icon {
            color: var(--primary);
        }

        /* Toggle Password Button */
        .btn-toggle-password {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 0.95rem;
            cursor: pointer;
            padding: 6px;
            border-radius: var(--radius-sm);
            transition: color 0.2s ease;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-toggle-password:hover {
            color: var(--text-main);
        }

        /* Extra Row: Remember & Forgot */
        .form-options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 4px;
            margin-bottom: 18px;
        }

        .custom-checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            user-select: none;
        }

        .custom-checkbox-wrap input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
            border-radius: 4px;
        }

        .custom-checkbox-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
        }

        .link-forgot-pwd {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .link-forgot-pwd:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-pos-signin {
            width: 100%;
            height: 48px;
            background: var(--primary-gradient);
            border: none;
            border-radius: var(--radius-md);
            color: #ffffff;
            font-size: 0.96rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            cursor: pointer;
            box-shadow: var(--shadow-primary);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-pos-signin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(255, 94, 20, 0.4);
            color: #ffffff;
        }

        .btn-pos-signin:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(255, 94, 20, 0.3);
        }

        .btn-pos-signin i {
            transition: transform 0.2s ease;
        }

        .btn-pos-signin:hover i {
            transform: translateX(4px);
        }

        /* Support Button Row */
        .auth-support-row {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px dashed #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-contact-help {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: var(--text-muted);
            font-size: 0.82rem;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: var(--radius-full);
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-contact-help:hover {
            background: #ffffff;
            color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        /* Form Column Bottom Footer */
        .login-form-footer {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            padding-top: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.76rem;
            color: var(--text-light);
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
            color: #64748b;
        }

        .security-badge i {
            color: #10b981;
        }

        /* ===================================================
           RIGHT SIDE: RESTAURANT POS SHOWCASE COLUMN
           =================================================== */
        .login-showcase-column {
            flex: 1;
            height: 100vh;
            max-height: 100vh;
            background: #080d19;
            background-image:
                radial-gradient(circle at 85% 15%, rgba(255, 94, 20, 0.16) 0%, transparent 45%),
                radial-gradient(circle at 20% 85%, rgba(99, 102, 241, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(15, 23, 42, 0.8) 0%, #080d19 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 56px;
            color: #ffffff;
            overflow: hidden;
        }

        /* Subtle Geometric Background Grid */
        .login-showcase-column::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
            opacity: 0.6;
        }

        .showcase-inner {
            position: relative;
            z-index: 2;
            max-width: 620px;
            margin: auto 0;
        }

        /* Showcase Eyebrow Pill */
        .showcase-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            color: #f8fafc;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: var(--radius-full);
            margin-bottom: 16px;
            letter-spacing: 0.02em;
        }

        .showcase-eyebrow i {
            color: #ff8c42;
        }

        /* Showcase Headline */
        .showcase-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.35rem;
            font-weight: 900;
            line-height: 1.18;
            letter-spacing: -0.03em;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .gradient-span {
            background: linear-gradient(135deg, #ff5e14 0%, #ffa05c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .showcase-desc {
            font-size: 0.95rem;
            line-height: 1.55;
            color: #94a3b8;
            margin-bottom: 22px;
            font-weight: 400;
        }

        /* ===================================================
           STREAMLINED POS METRIC BAR & FEATURES (NO SCROLL)
           =================================================== */
        .pos-metrics-strip {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
        }

        .metric-stat-item {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .metric-icon-circle {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.92rem;
            flex-shrink: 0;
        }

        .metric-icon-circle.green {
            background: rgba(16, 185, 129, 0.18);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        .metric-icon-circle.orange {
            background: rgba(255, 94, 20, 0.18);
            border: 1px solid rgba(255, 94, 20, 0.3);
            color: #ff8c42;
        }

        .metric-icon-circle.blue {
            background: rgba(56, 189, 248, 0.18);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #38bdf8;
        }

        .metric-val-title {
            font-size: 0.88rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
        }

        .metric-val-sub {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 500;
            margin-top: 2px;
        }

        .metric-divider {
            width: 1px;
            height: 32px;
            background: rgba(255, 255, 255, 0.1);
        }

        /* 4 POS Feature Cards Grid */
        .showcase-features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .feature-grid-item {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: var(--radius-md);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }

        .feature-grid-item:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(255, 94, 20, 0.3);
            transform: translateY(-1px);
        }

        .feature-item-icon {
            font-size: 1.15rem;
            color: #ff8c42;
            flex-shrink: 0;
        }

        .feature-item-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 2px;
        }

        .feature-item-desc {
            font-size: 0.74rem;
            color: #94a3b8;
        }

        /* Showcase Bottom Trust Strip */
        .showcase-bottom-trust {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .stars-group {
            display: flex;
            align-items: center;
            gap: 3px;
            color: #f59e0b;
            font-size: 0.82rem;
        }

        /* ===================================================
           LOADING OVERLAY
           =================================================== */
        #loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 99999;
            color: #ffffff;
            animation: fadeIn 0.25s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .pos-spinner-ring {
            width: 52px;
            height: 52px;
            border: 4px solid rgba(255, 255, 255, 0.15);
            border-top-color: #ff5e14;
            border-right-color: #ff8c42;
            border-radius: 50%;
            animation: spin 0.85s linear infinite;
            margin-bottom: 18px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-title-msg {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .loading-sub-msg {
            font-size: 0.85rem;
            color: #94a3b8;
        }

        /* ===================================================
           MODAL LUXURY STYLING
           =================================================== */
        .modal-pos-dialog {
            max-width: 460px;
        }

        .modal-pos-content {
            background: #ffffff;
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.2);
            overflow: hidden;
        }

        .modal-pos-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 22px 26px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-pos-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
        }

        .modal-pos-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-btn-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #ffffff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-btn-close:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: rotate(90deg);
        }

        .modal-pos-body {
            padding: 26px 26px;
            text-align: center;
        }

        .contact-channel-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 12px;
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.2s ease;
        }

        .contact-channel-card:hover {
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 4px 16px rgba(255, 94, 20, 0.08);
            transform: translateY(-1px);
            color: var(--primary);
        }

        .channel-icon-pill {
            width: 42px;
            height: 42px;
            background: rgba(255, 94, 20, 0.1);
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .channel-info-text {
            text-align: left;
            flex: 1;
        }

        .channel-type-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.04em;
        }

        .channel-value-text {
            font-size: 0.92rem;
            font-weight: 800;
            color: var(--text-main);
        }

        /* ===================================================
           RESPONSIVENESS (MOBILE / TABLETS)
           =================================================== */
        @media (max-width: 1200px) {
            .login-form-column {
                padding: 28px 36px;
            }
            .login-showcase-column {
                padding: 32px 36px;
            }
            .showcase-title {
                font-size: 2.1rem;
            }
        }

        @media (max-width: 991px) {
            html, body {
                height: auto;
                max-height: none;
                overflow-y: auto;
            }
            .login-layout-wrapper {
                flex-direction: column;
                height: auto;
                max-height: none;
                overflow-y: auto;
            }
            .login-form-column {
                flex: 0 0 100%;
                max-width: 100%;
                min-height: 100vh;
                height: auto;
                max-height: none;
                padding: 36px 24px;
                box-shadow: none;
                overflow-y: visible;
            }
            .login-showcase-column {
                display: none; /* Hide heavy showcase graphic on mobile */
            }
            .form-title-text {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 480px) {
            .login-form-column {
                padding: 24px 18px;
            }
            .brand-logo-img {
                max-height: 42px;
            }
            .form-title-text {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>

<!-- High-Tech Loading Spinner Overlay -->
<div id="loading-overlay">
    <div class="pos-spinner-ring"></div>
    <div class="loading-title-msg">Connecting to POS Terminal</div>
    <div class="loading-sub-msg">Authenticating credentials &amp; preparing kitchen sync...</div>
</div>

<div class="login-layout-wrapper">

    <!-- ===================================================
         LEFT COLUMN: LOGIN FORM & BRANDING
         =================================================== -->
    <div class="login-form-column">

        <!-- Form Inner Centered Box -->
        <div class="login-form-inner">

            <!-- Logo & Terminal Status Row -->
            <div class="brand-header-row">
                <a href="{{ url('/') }}" class="brand-logo-wrap" title="Bill&Bite Home">
                    <img src="{{ asset('logo.png') }}" class="brand-logo-img" alt="Bill&Bite Logo" onerror="this.onerror=null; this.src='{{ asset('logo1.png') }}';">
                </a>

                <div class="pos-terminal-badge">
                    <span class="live-dot"></span>
                    <span>POS Terminal</span>
                </div>
            </div>

            <!-- Welcome Heading -->
            <div class="form-heading-group">
                <h1 class="form-title-text">Welcome Back</h1>
                <p class="form-sub-text">Enter your credentials to access live tables, orders &amp; kitchen billing terminal.</p>
            </div>

            <!-- Flash Session Alerts -->
            @if(session('error'))
                <div class="alert alert-modern alert-modern-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-circle-exclamation me-1" style="font-size: 1rem;"></i>
                    <div style="flex: 1;">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-modern alert-modern-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-circle-check me-1" style="font-size: 1rem;"></i>
                    <div style="flex: 1;">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Sign In Form -->
            <form action="{{ route('custom.login') }}" id="login-form" method="POST" novalidate autocomplete="on">
                @csrf

                <!-- Email Input -->
                <div class="form-group-custom">
                    <label class="form-label-custom" for="emailInput">
                        <span>Staff / Admin Email</span>
                    </label>
                    <div class="form-input-container">
                        <i class="fa-regular fa-envelope input-lead-icon"></i>
                        <input
                            type="email"
                            id="emailInput"
                            name="email"
                            class="form-input-field"
                            required
                            placeholder="manager@restaurant.com"
                            autocomplete="username"
                            value="{{ old('email') }}"
                            autofocus
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-group-custom">
                    <label class="form-label-custom" for="passwordInput">
                        <span>Terminal Password</span>
                    </label>
                    <div class="form-input-container">
                        <i class="fa-regular fa-lock input-lead-icon"></i>
                        <input
                            type="password"
                            id="passwordInput"
                            name="password"
                            class="form-input-field"
                            required
                            placeholder="••••••••"
                            autocomplete="current-password"
                        >
                        <button type="button" class="btn-toggle-password" id="btnTogglePassword" title="Show or hide password" aria-label="Toggle password view">
                            <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password Options -->
                <div class="form-options-row">
                    <label class="custom-checkbox-wrap" for="rememberMeCheckbox">
                        <input type="checkbox" name="remember" id="rememberMeCheckbox" checked>
                        <span class="custom-checkbox-label">Keep me signed in</span>
                    </label>

                    <a href="{{ route('forget.password.portal') }}" class="link-forgot-pwd">
                        Forgot Password?
                    </a>
                </div>

                <!-- Sign In Action Button -->
                <button type="submit" class="btn-pos-signin" id="btnSubmitForm">
                    <span>Sign In</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

            </form>

            <!-- Need Help / Contact Admin Row -->
            <div class="auth-support-row">
                <button type="button" class="btn-contact-help" data-bs-toggle="modal" data-bs-target="#contactModal">
                    <i class="fas fa-headset text-primary"></i>
                    <span>Need Help? Contact Admin</span>
                </button>
            </div>

        </div><!-- /.login-form-inner -->

        <!-- Bottom Security & Copyright Footer -->
        <footer class="login-form-footer">
            <div class="security-badge">
                <i class="fas fa-shield-halved"></i>
                <span>256-Bit Encrypted Session</span>
            </div>
            <div>&copy; {{ date('Y') }} Bill&Bite POS</div>
        </footer>

    </div><!-- /.login-form-column -->


    <!-- ===================================================
         RIGHT COLUMN: RESTAURANT POS SHOWCASE & CONTENT
         =================================================== -->
    <div class="login-showcase-column">

        <div class="showcase-inner">

            <!-- Eyebrow Pill -->
            <div class="showcase-eyebrow">
                <i class="fas fa-bolt-lightning"></i>
                <span>All-In-One Restaurant Management Cloud POS</span>
            </div>

            <!-- Headline -->
            <h2 class="showcase-title">
                Smart Dining, Live Kitchens &amp; POS <span class="gradient-span">Unified.</span>
            </h2>

            <!-- Description -->
            <p class="showcase-desc">
                From contactless QR table ordering to instant KOT routing, kitchen display status tracking, and rapid split billing — run your entire restaurant with zero friction.
            </p>

            <!-- Sleek Horizontal Terminal Metrics Bar -->
            <div class="pos-metrics-strip">
                <div class="metric-stat-item">
                    <div class="metric-icon-circle green">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <div class="metric-val-title">Instant Billing</div>
                        <div class="metric-val-sub">Fast Counter &amp; Split Invoices</div>
                    </div>
                </div>

                <div class="metric-divider"></div>

                <div class="metric-stat-item">
                    <div class="metric-icon-circle orange">
                        <i class="fas fa-fire-burner"></i>
                    </div>
                    <div>
                        <div class="metric-val-title">Live Kitchen Sync</div>
                        <div class="metric-val-sub">Zero Delay KOT Routing</div>
                    </div>
                </div>

                <div class="metric-divider"></div>

                <div class="metric-stat-item">
                    <div class="metric-icon-circle blue">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div>
                        <div class="metric-val-title">Dynamic QR</div>
                        <div class="metric-val-sub">Contactless Dining</div>
                    </div>
                </div>
            </div>

            <!-- 4 Quick Highlights Grid -->
            <div class="showcase-features-grid">
                <div class="feature-grid-item">
                    <i class="fas fa-chair feature-item-icon"></i>
                    <div>
                        <div class="feature-item-title">Table Management</div>
                        <div class="feature-item-desc">Live floor plan &amp; guest seating</div>
                    </div>
                </div>

                <div class="feature-grid-item">
                    <i class="fas fa-fire-burner feature-item-icon"></i>
                    <div>
                        <div class="feature-item-title">Kitchen KOT Dispatch</div>
                        <div class="feature-item-desc">Zero delay order routing</div>
                    </div>
                </div>

                <div class="feature-grid-item">
                    <i class="fas fa-qrcode feature-item-icon"></i>
                    <div>
                        <div class="feature-item-title">QR Dine-In Ordering</div>
                        <div class="feature-item-desc">Seamless contactless menus</div>
                    </div>
                </div>

                <div class="feature-grid-item">
                    <i class="fas fa-receipt feature-item-icon"></i>
                    <div>
                        <div class="feature-item-title">GST &amp; Split Billing</div>
                        <div class="feature-item-desc">Lightning-fast checkout invoices</div>
                    </div>
                </div>
            </div>

        </div><!-- /.showcase-inner -->

        <!-- Bottom Trust Strip -->
        <div class="showcase-bottom-trust">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div class="stars-group">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <strong style="color: #ffffff;">4.9/5 Rating</strong>
            </div>

            <div>Trusted by 500+ Restaurants, Cafes &amp; Cloud Kitchens</div>
        </div>

    </div><!-- /.login-showcase-column -->

</div><!-- /.login-layout-wrapper -->


<!-- ===================================================
     MODAL: CONTACT ADMIN / SUPPORT
     =================================================== -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-pos-dialog">
        <div class="modal-content modal-pos-content">

            <div class="modal-pos-header">
                <h5 class="modal-pos-title" id="contactModalLabel">
                    <i class="fas fa-headset text-primary"></i>
                    <span>Contact Administration</span>
                </h5>
                <button type="button" class="modal-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-pos-body">
                <div style="width: 52px; height: 52px; background: rgba(255, 94, 20, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; color: var(--primary); font-size: 1.4rem;">
                    <i class="fas fa-headset"></i>
                </div>

                <h4 style="font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">Need Assistance?</h4>
                <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px;">Please reach out to the system administrator for account credentials, role adjustments, or technical inquiries.</p>

                <!-- Email Channel -->
                <a href="mailto:info@billnbite.com" class="contact-channel-card">
                    <div class="channel-icon-pill">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="channel-info-text">
                        <div class="channel-type-label">Official Support Email</div>
                        <div class="channel-value-text">info@billnbite.com</div>
                    </div>
                    <i class="fas fa-arrow-up-right-from-square text-muted" style="font-size: 0.85rem;"></i>
                </a>

                <!-- Phone Channel -->
                <a href="tel:+917001769472" class="contact-channel-card">
                    <div class="channel-icon-pill">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="channel-info-text">
                        <div class="channel-type-label">Helpdesk Helpline</div>
                        <div class="channel-value-text">+91 7001769472</div>
                    </div>
                    <i class="fas fa-arrow-up-right-from-square text-muted" style="font-size: 0.85rem;"></i>
                </a>

                <div style="font-size: 0.76rem; color: var(--text-light); margin-top: 14px;">
                    <i class="fas fa-clock me-1"></i> Technical support available 7 days a week: 9:00 AM - 11:00 PM
                </div>
            </div>

            <div style="padding: 14px 26px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: var(--radius-full); font-weight: 700; font-size: 0.85rem;">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>


<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. Password Visibility Toggle
        const toggleBtn = document.getElementById('btnTogglePassword');
        const pwdInput = document.getElementById('passwordInput');
        const pwdIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && pwdInput && pwdIcon) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = pwdInput.getAttribute('type') === 'password';
                pwdInput.setAttribute('type', isPassword ? 'text' : 'password');

                pwdIcon.classList.toggle('fa-eye', !isPassword);
                pwdIcon.classList.toggle('fa-eye-slash', isPassword);
            });
        }

        // 2. Submit Loading State & Overlay
        const loginForm = document.getElementById('login-form');
        const loadingOverlay = document.getElementById('loading-overlay');
        const btnSubmit = document.getElementById('btnSubmitForm');

        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'flex';
                }
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<span>Signing In...</span> <i class="fas fa-spinner fa-spin"></i>';
                }
            });
        }

        // 3. Auto-Fade Alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-modern');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    alert.remove();
                }, 400);
            });
        }, 5000);

    });
</script>

</body>
</html>