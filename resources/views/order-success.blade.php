<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $statusNormalized = strtoupper($orderStatus ?? 'PENDING');
        $isPending = in_array($statusNormalized, ['PENDING', 'AWAITING']);
        $isAccepted = in_array($statusNormalized, ['ACCEPTED', 'APPROVED', 'COMPLETED', 'SERVED', 'IN_KITCHEN']);
        $isRejected = in_array($statusNormalized, ['REJECTED', 'CANCELLED', 'DECLINED']);
        $isDone = ($isCompleted ?? false) || (isset($mainOrder) && ($mainOrder->order_complete === 'DONE' || $mainOrder->payment_status === 'PAID'));
        
        $tableId = $table_details->id ?? ($tempOrder->table_id ?? ($mainOrder->table_id ?? null));
        $restaurantId = $restaurant_details->id ?? ($tempOrder->restaurant_id ?? ($mainOrder->restaurant_id ?? null));
        $orderRecordId = $tempOrder->id ?? ($mainOrder->id ?? $id ?? 0);
    @endphp

    <title>
        @if($isAccepted)
            Order Accepted | {{ $restaurant_details->name ?? 'Premium Dining' }}
        @elseif($isRejected)
            Order Declined | {{ $restaurant_details->name ?? 'Premium Dining' }}
        @else
            Order Awaiting Approval | {{ $restaurant_details->name ?? 'Premium Dining' }}
        @endif
    </title>
    
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #ff5e14;
            --primary-gradient: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
            --surface: #ffffff;
            --surface-2: #f8fafc;
            --surface-3: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --border: #e2e8f0;
            --radius-md: 18px;
            --radius-lg: 26px;
            --radius-full: 9999px;
            --shadow-sm: 0 4px 16px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 12px 36px rgba(15, 23, 42, 0.08);

            /* Dynamic Status Variables */
            @if($isAccepted)
                --theme-color: #10b981;
                --theme-dark: #047857;
                --theme-light: rgba(16, 185, 129, 0.1);
                --theme-border: rgba(16, 185, 129, 0.25);
                --theme-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
                --theme-glow: rgba(16, 185, 129, 0.35);
            @elseif($isRejected)
                --theme-color: #ef4444;
                --theme-dark: #b91c1c;
                --theme-light: rgba(239, 68, 68, 0.1);
                --theme-border: rgba(239, 68, 68, 0.25);
                --theme-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                --theme-glow: rgba(239, 68, 68, 0.35);
            @else
                --theme-color: #f59e0b;
                --theme-dark: #b45309;
                --theme-light: rgba(245, 158, 11, 0.1);
                --theme-border: rgba(245, 158, 11, 0.25);
                --theme-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                --theme-glow: rgba(245, 158, 11, 0.35);
            @endif
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #f8fafc;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -10%, var(--theme-light) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 85% 95%, rgba(255, 94, 20, 0.03) 0%, transparent 50%);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
        }

        .success-wrap {
            width: 100%;
            max-width: 580px;
            margin: 0 auto;
            animation: cardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(24px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Top Branding */
        .top-brand-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 0 4px;
        }

        .brand-logo-pill {
            background: #ffffff;
            padding: 8px 18px;
            border-radius: var(--radius-full);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            display: inline-flex;
            align-items: center;
            height: 48px;
        }

        .brand-logo-pill img {
            max-height: 32px;
            max-width: 140px;
            object-fit: contain;
            display: block;
        }

        .table-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            color: var(--text-main);
            border: 1px solid var(--border);
            padding: 8px 16px;
            border-radius: var(--radius-full);
            font-size: 0.86rem;
            font-weight: 700;
            box-shadow: var(--shadow-sm);
        }

        /* Main Status Card */
        .status-main-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            position: relative;
        }

        .card-accent-bar {
            height: 6px;
            background: var(--theme-gradient);
            width: 100%;
        }

        /* Hero Status Section */
        .hero-status-section {
            text-align: center;
            padding: 40px 32px 28px;
            position: relative;
            background: radial-gradient(circle at 50% 20%, var(--theme-light) 0%, transparent 70%);
        }

        .status-icon-ring {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #ffffff;
            border: 2.5px solid var(--theme-border);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            color: var(--theme-color);
            margin-bottom: 20px;
            box-shadow: 0 8px 24px var(--theme-glow);
            position: relative;
        }

        .status-icon-ring.pulse-anim {
            animation: pulseAwaiting 2.2s infinite;
        }

        @keyframes pulseAwaiting {
            0% { box-shadow: 0 0 0 0 var(--theme-glow); }
            70% { box-shadow: 0 0 0 18px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        .status-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.1rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 8px;
            line-height: 1.25;
        }

        .status-state-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--theme-light);
            color: var(--theme-dark);
            border: 1.5px solid var(--theme-border);
            padding: 7px 20px;
            border-radius: var(--radius-full);
            font-size: 0.88rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 14px;
        }

        .status-desc-text {
            color: var(--text-muted);
            font-size: 0.96rem;
            line-height: 1.55;
            max-width: 440px;
            margin: 0 auto;
        }

        .status-desc-text strong {
            color: var(--text-main);
        }

        /* Order Reference Pill */
        .order-ref-box {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--surface-2);
            border: 1px dashed var(--border);
            border-radius: var(--radius-full);
            padding: 8px 18px;
            margin-top: 18px;
            font-size: 0.9rem;
        }

        .order-ref-box span {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .order-ref-box strong {
            font-family: monospace;
            font-size: 1.05rem;
            color: var(--text-main);
            font-weight: 800;
        }

        /* Live Polling Notice for Awaiting State */
        .live-polling-banner {
            background: #fffbeb;
            border-top: 1px solid #fef3c7;
            border-bottom: 1px solid #fef3c7;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.86rem;
            font-weight: 600;
            color: #92400e;
        }

        .dot-flashing {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #f59e0b;
            animation: dotFlash 1.4s infinite linear;
        }

        @keyframes dotFlash {
            0%, 100% { opacity: 0.3; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        /* Order Progress Tracker Steps (For Accepted State) */
        .order-steps-container {
            padding: 24px 32px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
        }

        .steps-wrapper {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .steps-wrapper::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 30px;
            right: 30px;
            height: 3px;
            background: var(--surface-3);
            z-index: 1;
        }

        .steps-wrapper.progress-half::after {
            content: '';
            position: absolute;
            top: 18px;
            left: 30px;
            width: 60%;
            height: 3px;
            background: #10b981;
            z-index: 2;
        }

        .step-node {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 3;
            text-align: center;
            flex: 1;
        }

        .step-icon-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 8px;
            transition: all 0.3s;
        }

        .step-node.completed .step-icon-circle {
            background: #10b981;
            border-color: #10b981;
            color: #ffffff;
        }

        .step-node.active .step-icon-circle {
            background: #ffffff;
            border-color: #10b981;
            color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
        }

        .step-label {
            font-size: 0.76rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .step-node.active .step-label,
        .step-node.completed .step-label {
            color: var(--text-main);
        }

        /* Order Details & Summary */
        .order-details-pane {
            padding: 28px 32px;
        }

        .section-header-mini {
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-row-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--surface-3);
            font-size: 0.94rem;
        }

        .detail-row-flex:last-child {
            border-bottom: none;
        }

        .detail-lbl {
            color: var(--text-muted);
            font-weight: 500;
        }

        .detail-val {
            font-weight: 700;
            color: var(--text-main);
        }

        /* Itemized List in Card */
        @if(isset($items) && count($items) > 0)
        .items-preview-list {
            background: var(--surface-2);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            margin: 20px 0;
            border: 1px solid var(--border);
        }

        .item-preview-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eef2f6;
            font-size: 0.92rem;
        }

        .item-preview-row:last-child {
            border-bottom: none;
        }

        .item-qty-badge {
            background: #ffffff;
            color: var(--primary);
            border: 1px solid rgba(255, 94, 20, 0.25);
            font-weight: 800;
            font-size: 0.78rem;
            padding: 2px 7px;
            border-radius: 6px;
            margin-right: 8px;
        }

        .kot-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            letter-spacing: 0.03em;
        }

        .item-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            transition: all 0.3s ease;
        }

        .item-status-pill.status-pending {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .item-status-pill.status-cooking {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid rgba(59, 130, 246, 0.3);
            animation: pulseSubtle 2s infinite;
        }

        .item-status-pill.status-done {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        @keyframes pulseSubtle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.75; }
        }

        .btn-item-cancel {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 6px;
            padding: 3px 8px;
            font-size: 0.72rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-item-cancel:hover {
            background: #dc2626;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
        }

        .badge-item-locked {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f8fafc;
            color: #94a3b8;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 2px 7px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        @endif

        /* Toast feedback */
        .toast-feedback {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(40px);
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 9999px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toast-feedback.show {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        /* Totals Card */
        .order-totals-strip {
            background: #f8fafc;
            border-radius: var(--radius-md);
            padding: 18px 22px;
            border: 1px solid var(--border);
            margin-top: 16px;
        }

        .total-item-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.92rem;
            color: var(--text-muted);
            padding: 5px 0;
        }

        .total-item-row.discount-text {
            color: #16a34a;
            font-weight: 600;
        }

        .total-item-row.grand-row {
            border-top: 2px dashed #cbd5e1;
            padding-top: 14px;
            margin-top: 8px;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .grand-highlight {
            color: var(--primary);
            font-size: 1.45rem;
        }

        /* Action Buttons Area */
        .actions-card-footer {
            padding: 0 32px 32px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-action-primary {
            background: var(--primary-gradient);
            color: #ffffff !important;
            border: none;
            border-radius: var(--radius-full);
            padding: 15px 32px;
            font-size: 0.98rem;
            font-weight: 700;
            text-align: center;
            text-decoration: none !important;
            box-shadow: 0 6px 20px rgba(255, 94, 20, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 94, 20, 0.4);
        }

        .btn-action-secondary {
            background: var(--surface-2);
            color: var(--text-muted) !important;
            border: 1px solid var(--border);
            border-radius: var(--radius-full);
            padding: 13px 32px;
            font-size: 0.94rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-action-secondary:hover {
            background: #ffffff;
            color: var(--text-main) !important;
            border-color: #cbd5e1;
        }

        .btn-action-cancel-order {
            background: #ffffff;
            color: #ef4444 !important;
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: var(--radius-full);
            padding: 13px 32px;
            font-size: 0.94rem;
            font-weight: 700;
            text-align: center;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-action-cancel-order:hover {
            background: #fef2f2;
            border-color: #ef4444;
            color: #b91c1c !important;
            transform: translateY(-1px);
        }

        /* Confetti sparkle effect */
        .sparkle-particle {
            position: absolute;
            pointer-events: none;
            animation: sparkleFly 1.8s ease-out forwards;
        }

        @keyframes sparkleFly {
            0% { opacity: 1; transform: translateY(0) scale(0.5); }
            100% { opacity: 0; transform: translateY(-70px) scale(1.3); }
        }

        @media (max-width: 480px) {
            .hero-status-section { padding: 32px 20px 24px; }
            .status-title { font-size: 1.7rem; }
            .order-details-pane, .actions-card-footer { padding-left: 20px; padding-right: 20px; }
            .order-steps-container { padding: 20px 16px; }
            .step-label { font-size: 0.68rem; }
        }
    </style>
</head>
<body>

<div class="success-wrap">

    <!-- Top Branding Strip -->
    <div class="top-brand-card">
        <div class="brand-logo-pill">
            @if(!empty($restaurant_details) && $restaurant_details->hasLogo())
                <img src="{{ $restaurant_details->logo_url }}" alt="{{ $restaurant_details->name }}" onerror="this.onerror=null; this.src='{{ asset('logo.png') }}';">
            @else
                <img src="{{ asset('logo.png') }}" alt="Bill & Bite Logo">
            @endif
        </div>

        @if(isset($table_details) && !empty($table_details->name))
            <div class="table-pill-badge">
                <i class="fas fa-chair" style="color: var(--primary);"></i> Table {{ $table_details->name }}
            </div>
        @endif
    </div>

    <!-- Main Card -->
    <div class="status-main-card">
        <div class="card-accent-bar"></div>

        <!-- HERO STATUS DISPLAY -->
        <div class="hero-status-section" id="heroStatusSection">
            
            @if($isAccepted)
                <!-- ACCEPTED STATE -->
                <div class="status-icon-ring" style="color: #10b981; border-color: rgba(16,185,129,0.3); box-shadow: 0 8px 24px rgba(16,185,129,0.25);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="status-title">Order Accepted! 🎉</h1>
                <div>
                    <span class="status-state-pill" style="background: rgba(16,185,129,0.1); color: #047857; border-color: rgba(16,185,129,0.25);">
                        <i class="fas fa-utensils"></i> Order Accepted & In Kitchen
                    </span>
                </div>
                <p class="status-desc-text">
                    Great news, <strong>{{ $customerName ?? 'Guest' }}</strong>! The restaurant has approved your order. Our kitchen team is preparing your delicious meal right now.
                </p>

            @elseif($isRejected)
                <!-- REJECTED STATE -->
                <div class="status-icon-ring" style="color: #ef4444; border-color: rgba(239,68,68,0.3); box-shadow: 0 8px 24px rgba(239,68,68,0.25);">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h1 class="status-title">Order Declined</h1>
                <div>
                    <span class="status-state-pill" style="background: rgba(239,68,68,0.1); color: #b91c1c; border-color: rgba(239,68,68,0.25);">
                        <i class="fas fa-ban"></i> Order Not Accepted
                    </span>
                </div>
                <p class="status-desc-text">
                    We apologize, <strong>{{ $customerName ?? 'Guest' }}</strong>. The restaurant was unable to accept this order at the moment. Please contact your waiter or restaurant staff for assistance.
                </p>

            @else
                <!-- AWAITING / PENDING STATE -->
                <div class="status-icon-ring pulse-anim" style="color: #f59e0b; border-color: rgba(245,158,11,0.3); box-shadow: 0 8px 24px rgba(245,158,11,0.25);">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <h1 class="status-title">Order Placed</h1>
                <div>
                    <span class="status-state-pill" style="background: rgba(245,158,11,0.1); color: #b45309; border-color: rgba(245,158,11,0.25);">
                        <i class="fas fa-clock"></i> Awaiting Confirmation
                    </span>
                </div>
                <p class="status-desc-text">
                    Thank you, <strong>{{ $customerName ?? 'Guest' }}</strong>! Your order has been received and is currently <strong>waiting for approval</strong> from the restaurant staff.
                </p>
            @endif

            <!-- Order Number Reference -->
            <div class="order-ref-box">
                <span>Order No:</span>
                <strong>{{ $orderId ?? 'Pending' }}</strong>
            </div>

        </div>

        @if($isPending)
            <!-- Live Status Checking Banner -->
            <div class="live-polling-banner">
                <span class="dot-flashing"></span>
                <span>Connecting live with kitchen POS &bull; Please stay on this page</span>
            </div>
        @endif

        @if($isAccepted)
            <!-- Progress Tracker for Accepted Orders -->
            <div class="order-steps-container">
                <div class="steps-wrapper progress-half">
                    <div class="step-node completed">
                        <div class="step-icon-circle"><i class="fas fa-check"></i></div>
                        <span class="step-label">Placed</span>
                    </div>
                    <div class="step-node completed">
                        <div class="step-icon-circle"><i class="fas fa-check"></i></div>
                        <span class="step-label">Accepted</span>
                    </div>
                    <div class="step-node active">
                        <div class="step-icon-circle"><i class="fas fa-fire-burner"></i></div>
                        <span class="step-label">Cooking</span>
                    </div>
                    <div class="step-node">
                        <div class="step-icon-circle"><i class="fas fa-bell-concierge"></i></div>
                        <span class="step-label">Served</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Order Information & Breakdown -->
        <div class="order-details-pane">
            <div class="section-header-mini">
                <i class="fas fa-receipt" style="color: var(--primary);"></i> Order Information
            </div>

            <div class="detail-row-flex">
                <span class="detail-lbl"><i class="far fa-calendar-alt me-1"></i> Date & Time</span>
                <span class="detail-val">{{ now()->format('d M Y, h:i A') }}</span>
            </div>

            <div class="detail-row-flex">
                <span class="detail-lbl"><i class="far fa-user me-1"></i> Customer</span>
                <span class="detail-val">{{ $customerName ?? 'Guest' }}</span>
            </div>

            @if(isset($table_details) && !empty($table_details->name))
                <div class="detail-row-flex">
                    <span class="detail-lbl"><i class="fas fa-chair me-1"></i> Table</span>
                    <span class="detail-val" style="color: var(--primary);">Table {{ $table_details->name }}</span>
                </div>
            @endif

            <div class="detail-row-flex">
                <span class="detail-lbl"><i class="fas fa-info-circle me-1"></i> Status</span>
                <span class="detail-val" style="font-weight: 800; color: var(--theme-color);">
                    @if($isAccepted)
                        <i class="fas fa-check-circle me-1"></i> Accepted & In Kitchen
                    @elseif($isRejected)
                        <i class="fas fa-times-circle me-1"></i> Declined
                    @else
                        <i class="fas fa-spinner fa-spin me-1"></i> Awaiting Approval
                    @endif
                </span>
            </div>

            <!-- Ordered Dishes List (If Available) -->
            @if(isset($items) && count($items) > 0)
                <div class="items-preview-list">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em;">
                            Ordered Dishes ({{ count($items) }})
                        </div>
                        <span style="font-size: 0.72rem; color: #64748b; font-weight: 600;">
                            <i class="fas fa-circle-info me-1"></i> Live Kitchen Status
                        </span>
                    </div>
                    <div id="itemsContainerList">
                    @foreach($items as $itm)
                        @php
                            $isStandaloneAddon = empty($itm->subcategory_id);
                            $itemName = $itm->menuItem->name ?? ($itm->subcategory->name ?? ($itm->name ?? ($isStandaloneAddon ? 'Add-on' : 'Dish')));
                            $qty = max(1, intval($itm->quantity ?? ($itm->qty ?? 1)));
                            $basePrice = floatval($itm->price ?? 0);
                            $discPercent = floatval($itm->item_discount_percentage ?? 0);
                            $discPrice = floatval($itm->discounted_price ?? ($basePrice - ($basePrice * $discPercent / 100)));
                            $addonsList = $itm->addons_list ?? [];
                            $addonsCost = 0;
                            if (!empty($addonsList) && is_array($addonsList)) {
                                foreach ($addonsList as $a) {
                                    $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                                }
                            }
                            $itemTaxable = $isStandaloneAddon ? ($discPrice * $qty) : (($discPrice * $qty) + $addonsCost);
                            $itemGstRate = floatval($itm->gst_rate ?? 0);
                            $itemGst = ($itemTaxable * $itemGstRate) / 100;
                            $total = floatval($itm->total_amount ?: ($isGstBill ? ($itemTaxable + $itemGst) : ($isStandaloneAddon ? ($basePrice * $qty) : (($basePrice * $qty) + $addonsCost))));
                            $itemStatus = strtoupper($itm->order_status ?? 'PENDING');
                            $kotNo = $itm->kot_no ?? null;
                        @endphp
                        <div class="item-preview-row" id="itemRow_{{ $itm->id }}" data-item-id="{{ $itm->id }}" style="padding: 10px 0;">
                            <div style="flex: 1; min-width: 0; padding-right: 10px;">
                                <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 6px; margin-bottom: 4px;">
                                    <span class="item-qty-badge">{{ $qty }}x</span>
                                    <strong style="color: var(--text-main); font-size: 0.94rem;">{{ $itemName }}</strong>
                                    @if($isStandaloneAddon)
                                        <span style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px; font-size: 0.68rem; font-weight: 700; text-transform: uppercase;">
                                             Add-on
                                        </span>
                                    @endif
                                    @if(!empty($kotNo))
                                        <span class="kot-badge" id="kotBadge_{{ $itm->id }}"><i class="fas fa-receipt"></i> {{ $kotNo }}</span>
                                    @endif
                                </div>
                                @if(!empty($addonsList) && count($addonsList) > 0 && !$isStandaloneAddon)
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px; margin-bottom: 6px;">
                                        @foreach($addonsList as $addon)
                                            @php
                                                $aQty = $addon['qty'] ?? $addon['quantity'] ?? 1;
                                                $aPrice = floatval($addon['price'] ?? 0);
                                            @endphp
                                            <span style="background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; border-radius: 6px; padding: 2px 7px; font-size: 0.72rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fas fa-plus-circle" style="font-size: 0.65rem;"></i>
                                                <span>{{ $addon['name'] ?? 'Add-on' }}</span>
                                                <span style="color: #c2410c;">(₹{{ number_format($aPrice, 2) }})</span>
                                                <span style="background: #ffedd5; color: #c2410c; font-weight: 800; font-size: 0.68rem; padding: 0 4px; border-radius: 3px; border: 1px solid #fed7aa;">x{{ $aQty }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="item-status-pill status-{{ strtolower($itemStatus) }}" id="itemStatusBadge_{{ $itm->id }}">
                                        @if($itemStatus === 'COOKING')
                                            <i class="fas fa-fire-burner"></i> Cooking
                                        @elseif($itemStatus === 'DONE')
                                            <i class="fas fa-check-circle"></i> Cooked
                                        @else
                                            <i class="fas fa-hourglass-half"></i> Pending
                                        @endif
                                    </span>
                                    <div id="itemActionArea_{{ $itm->id }}">
                                        @if($itemStatus === 'PENDING')
                                            <button type="button" class="btn-item-cancel" onclick="cancelDishItem({{ $itm->id }})" title="Cancel this pending dish">
                                                <i class="fas fa-trash-alt"></i> Cancel
                                            </button>
                                        @else
                                            <span class="badge-item-locked" title="Kitchen is already preparing or cooked this dish. Cannot cancel or modify.">
                                                <i class="fas fa-lock"></i> Locked
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0;">
                                <span style="font-weight: 800; font-size: 0.96rem; color: var(--text-main);">₹{{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            @endif

            <!-- Totals Strip -->
            @if(isset($grandTotal) && $grandTotal > 0)
                <div class="order-totals-strip">
                    @if(isset($subtotal) && $subtotal > 0)
                        <div class="total-item-row">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                    @endif
                    @if(isset($discount) && $discount > 0)
                        <div class="total-item-row discount-text">
                            <span>Total Discount</span>
                            <span>− ₹{{ number_format($discount, 2) }}</span>
                        </div>
                    @endif
                    @if(isset($gstAmount) && $gstAmount > 0)
                        <div class="total-item-row">
                            <span>GST</span>
                            <span>₹{{ number_format($gstAmount, 2) }}</span>
                        </div>
                    @endif
                    <div class="total-item-row grand-row">
                        <span>Total Payable</span>
                        <span class="grand-highlight">₹{{ number_format($grandTotal, 2) }}</span>
                    </div>
                </div>
            @endif

        </div>

        <!-- Action Footer -->
        <div class="actions-card-footer">
            @if($tableId && $restaurantId)
                @if($isDone)
                    <a href="{{ route('temp.order.fresh', [$tableId, $restaurantId]) }}" class="btn-action-primary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-plus-circle"></i>
                        <span>Start Fresh Order</span>
                    </a>
                @else
                    <a href="{{ route('temp.order.create', [$tableId, $restaurantId]) }}" class="btn-action-primary">
                        <i class="fas fa-plus-circle"></i>
                        <span>{{ $isRejected ? 'Choose Other Dishes' : 'Order More Items' }}</span>
                    </a>
                @endif
            @endif

            <a href="javascript:window.location.reload();" class="btn-action-secondary">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh Status</span>
            </a>

            @if($isPending && !$isAccepted && !$isRejected && $tableId && $restaurantId)
                <button type="button" class="btn-action-cancel-order" onclick="cancelEntireOrder({{ $orderRecordId }})" title="Cancel this pending order">
                    <i class="fas fa-ban"></i>
                    <span>Cancel Entire Order</span>
                </button>
            @endif
        </div>

    </div>
</div>

<div id="toastFeedback" class="toast-feedback"><i class="fas fa-check-circle text-success"></i> <span id="toastMsg">Updated</span></div>

<script>
    function showToast(msg, isSuccess = true) {
        const toast = document.getElementById('toastFeedback');
        const msgEl = document.getElementById('toastMsg');
        if (!toast || !msgEl) return;
        msgEl.textContent = msg;
        toast.querySelector('i').className = isSuccess ? 'fas fa-check-circle text-success' : 'fas fa-exclamation-triangle text-danger';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3200);
    }

    function cancelDishItem(itemId) {
        if (!confirm('Are you sure you want to cancel this pending dish?')) {
            return;
        }

        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        fetch("{{ url('/order-customer/item/delete') }}/" + itemId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                _token: '{{ csrf_token() }}'
            })
        })
        .then(async res => {
            const data = await res.json().catch(() => null);
            if (!res.ok) {
                const errMsg = (data && data.message) ? data.message : ('Error ' + res.status);
                throw new Error(errMsg);
            }
            return data;
        })
        .then(data => {
            if (data && data.status) {
                showToast(data.message || 'Item cancelled successfully.');
                if (data.order_cancelled && data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 800);
                    return;
                }
                const row = document.getElementById('itemRow_' + itemId);
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        row.remove();
                        setTimeout(() => window.location.reload(), 600);
                    }, 300);
                } else {
                    window.location.reload();
                }
            } else {
                alert((data && data.message) ? data.message : 'Could not cancel item.');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(err => {
            alert(err.message || 'Error while cancelling item.');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    function cancelEntireOrder(orderId) {
        if (!confirm('Are you sure you want to cancel this entire pending order?')) {
            return;
        }

        fetch("{{ url('/order-customer/cancel') }}/" + orderId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                _token: '{{ csrf_token() }}'
            })
        })
        .then(async res => {
            const data = await res.json().catch(() => null);
            if (!res.ok) {
                const errMsg = (data && data.message) ? data.message : ('Error ' + res.status);
                throw new Error(errMsg);
            }
            return data;
        })
        .then(data => {
            if (data && data.status) {
                showToast(data.message || 'Order cancelled successfully.');
                setTimeout(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                }, 800);
            } else {
                alert((data && data.message) ? data.message : 'Could not cancel order.');
            }
        })
        .catch(err => {
            alert(err.message || 'Error while cancelling order.');
        });
    }

    // Live Auto-Polling for Real-time status update
    const orderRecordId = {{ $orderRecordId }};
    if (orderRecordId) {
        let pollInterval = setInterval(function() {
            const checkType = '{{ isset($mainOrder) && $mainOrder ? "main" : "temp" }}';
            fetch("{{ route('order.status.check', ':id') }}".replace(':id', orderRecordId) + '?type=' + checkType)
                .then(res => res.json())
                .then(data => {
                    if (!data.status) return;

                    // If order completed or closed by restaurant:
                    if (data.is_completed) {
                        clearInterval(pollInterval);
                        window.location.reload();
                        return;
                    }

                    // If it was pending approval and was approved or rejected:
                    @if($isPending)
                    if (data.order_status && data.order_status !== 'PENDING') {
                        clearInterval(pollInterval);
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else if (data.main_order_id) {
                            window.location.href = "{{ url('/order-details') }}/" + data.main_order_id;
                        } else {
                            window.location.reload();
                        }
                        return;
                    }
                    @endif

                    // Live update each item's kitchen status badge & lock state
                    if (data.items && Array.isArray(data.items)) {
                        data.items.forEach(itm => {
                            const badge = document.getElementById('itemStatusBadge_' + itm.id);
                            const actionArea = document.getElementById('itemActionArea_' + itm.id);
                            const kotBadge = document.getElementById('kotBadge_' + itm.id);

                            if (kotBadge && itm.kot_no) {
                                kotBadge.innerHTML = '<i class="fas fa-receipt"></i> ' + itm.kot_no;
                            }

                            if (badge) {
                                const statusLower = itm.order_status.toLowerCase();
                                badge.className = 'item-status-pill status-' + statusLower;
                                if (itm.order_status === 'COOKING') {
                                    badge.innerHTML = '<i class="fas fa-fire-burner"></i> Cooking';
                                } else if (itm.order_status === 'DONE') {
                                    badge.innerHTML = '<i class="fas fa-check-circle"></i> Cooked';
                                } else {
                                    badge.innerHTML = '<i class="fas fa-hourglass-half"></i> Pending';
                                }
                            }

                            if (actionArea) {
                                if (itm.order_status === 'PENDING') {
                                    actionArea.innerHTML = `<button type="button" class="btn-item-cancel" onclick="cancelDishItem(${itm.id})" title="Cancel this pending dish"><i class="fas fa-trash-alt"></i> Cancel</button>`;
                                } else {
                                    actionArea.innerHTML = `<span class="badge-item-locked" title="Kitchen is already preparing or cooked this dish. Cannot cancel or modify."><i class="fas fa-lock"></i> Locked</span>`;
                                }
                            }
                        });
                    }
                })
                .catch(err => {
                    console.log('Status polling...', err);
                });
        }, 3500);
    }

    // Sparkle effect for Accepted state
    @if($isAccepted)
    document.addEventListener('DOMContentLoaded', function() {
        const ring = document.querySelector('.status-icon-ring');
        if (!ring) return;
        const colors = ['#10b981', '#34d399', '#f59e0b', '#fbbf24'];

        for (let i = 0; i < 14; i++) {
            setTimeout(() => {
                const sp = document.createElement('div');
                sp.className = 'sparkle-particle';
                sp.innerHTML = '<i class="fas fa-star"></i>';
                sp.style.color = colors[Math.floor(Math.random() * colors.length)];
                sp.style.left = ring.offsetLeft + (ring.offsetWidth / 2) + (Math.random() - 0.5) * 100 + 'px';
                sp.style.top = ring.offsetTop + (ring.offsetHeight / 2) + (Math.random() - 0.5) * 80 + 'px';
                sp.style.fontSize = (Math.random() * 14 + 10) + 'px';
                document.getElementById('heroStatusSection').appendChild(sp);
                setTimeout(() => sp.remove(), 1800);
            }, i * 120);
        }
    });
    @endif
</script>

</body>
</html>