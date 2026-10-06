<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $tableId = $table_details->id ?? ($mainOrder->table_id ?? null);
        $restaurantId = $restaurant_details->id ?? ($mainOrder->restaurant_id ?? null);
        $orderRecordId = $mainOrder->id ?? 0;
        $orderStatusNormalized = strtoupper($mainOrder->order_status ?? 'ACCEPTED');
        $isOrderDone = ($isCompleted ?? false) || ($mainOrder->order_complete === 'DONE' || $mainOrder->payment_status === 'PAID');
    @endphp

    <title>Order #{{ $orderId }} &bull; {{ $restaurant_details->name ?? 'Order Details' }}</title>
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
            --radius-sm: 12px;
            --radius-md: 18px;
            --radius-lg: 24px;
            --radius-full: 9999px;
            --shadow-sm: 0 4px 16px rgba(15, 23, 42, 0.05);
            --shadow-md: 0 12px 32px rgba(15, 23, 42, 0.08);

            --status-pending-bg: #fffbeb;
            --status-pending-text: #b45309;
            --status-pending-border: rgba(245, 158, 11, 0.3);

            --status-cooking-bg: #fff7ed;
            --status-cooking-text: #c2410c;
            --status-cooking-border: rgba(234, 88, 12, 0.3);

            --status-done-bg: #ecfdf5;
            --status-done-text: #047857;
            --status-done-border: rgba(16, 185, 129, 0.3);
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
                radial-gradient(ellipse 70% 45% at 50% -10%, rgba(255, 94, 20, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse 50% 35% at 85% 95%, rgba(16, 185, 129, 0.04) 0%, transparent 50%);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 120px;
        }

        .details-container {
            width: 100%;
            max-width: 780px;
            margin: 0 auto;
            padding: 20px 16px;
        }

        /* Top Bar */
        .details-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 18px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: var(--radius-lg);
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 12px;
            z-index: 100;
        }

        .brand-inline-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo-small {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .brand-name-header {
            font-family: 'Playfair Display', Georgia, serif;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--text-main);
            margin: 0;
            line-height: 1.2;
        }

        .table-pill-small {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--primary);
            background: rgba(255, 94, 20, 0.08);
            padding: 2px 8px;
            border-radius: var(--radius-full);
            margin-top: 2px;
        }

        .table-live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        .top-btn-add-more {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--primary-gradient);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.84rem;
            padding: 8px 16px;
            border-radius: var(--radius-full);
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(255, 94, 20, 0.25);
            transition: all 0.2s ease;
        }

        .top-btn-add-more:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(255, 94, 20, 0.35);
            color: #ffffff;
        }

        /* Hero Status Banner Card */
        .order-hero-banner {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 22px;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .hero-banner-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--primary-gradient);
        }

        .hero-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }

        .order-id-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--surface-2);
            border: 1px dashed #cbd5e1;
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .order-status-pill-big {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .order-status-pill-big.status-accepted {
            background: rgba(16, 185, 129, 0.12);
            color: #047857;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .order-status-pill-big.status-completed {
            background: rgba(99, 102, 241, 0.12);
            color: #4338ca;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .hero-title-main {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .hero-meta-subtitle {
            color: var(--text-muted);
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .hero-meta-subtitle span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Order Progress Steps */
        .progress-flow-box {
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .progress-step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .progress-step-item::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e2e8f0;
            z-index: -1;
        }

        .progress-step-item:last-child::after {
            display: none;
        }

        .progress-step-item.is-done::after {
            background: #10b981;
        }

        .step-circle-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f1f5f9;
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            border: 2px solid #e2e8f0;
            margin-bottom: 6px;
            transition: all 0.25s ease;
        }

        .progress-step-item.is-done .step-circle-icon {
            background: #10b981;
            color: #ffffff;
            border-color: #10b981;
        }

        .progress-step-item.is-active .step-circle-icon {
            background: #ff5e14;
            color: #ffffff;
            border-color: #ff5e14;
            box-shadow: 0 0 0 4px rgba(255, 94, 20, 0.2);
            animation: pulseActiveStep 1.8s infinite;
        }

        @keyframes pulseActiveStep {
            0% { box-shadow: 0 0 0 0 rgba(255, 94, 20, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 94, 20, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 94, 20, 0); }
        }

        .step-text-label {
            font-size: 0.76rem;
            font-weight: 700;
            color: var(--text-muted);
        }

        .progress-step-item.is-done .step-text-label,
        .progress-step-item.is-active .step-text-label {
            color: var(--text-main);
            font-weight: 800;
        }

        /* Order Details Section Heading */
        .section-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            margin-top: 10px;
        }

        .section-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .section-title i {
            color: var(--primary);
            font-size: 1.1rem;
        }

        /* KOT Batch Cards */
        .kot-lot-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            margin-bottom: 18px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .kot-lot-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            background: var(--surface-2);
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 10px;
        }

        .kot-lot-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .kot-number-badge {
            background: #0f172a;
            color: #ffffff;
            font-weight: 800;
            font-size: 0.82rem;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.03em;
        }

        .kot-batch-tag {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
        }

        .kot-items-count {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Dish List Items */
        .dish-row-item {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            transition: background 0.15s ease;
        }

        .dish-row-item:last-child {
            border-bottom: none;
        }

        .dish-row-item:hover {
            background: #fafafa;
        }

        .dish-info-col {
            flex: 1;
        }

        .dish-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
            flex-wrap: wrap;
        }

        .dish-qty-chip {
            background: rgba(255, 94, 20, 0.1);
            color: var(--primary);
            font-weight: 800;
            font-size: 0.8rem;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .dish-name-text {
            font-weight: 700;
            font-size: 0.98rem;
            color: var(--text-main);
        }

        .dish-unit-price-note {
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        .dish-right-col {
            display: flex;
            align-items: center;
            gap: 14px;
            text-align: right;
            flex-shrink: 0;
        }

        .dish-price-total {
            font-weight: 800;
            font-size: 0.98rem;
            color: var(--text-main);
            min-width: 60px;
        }

        /* Dish Status Pills */
        .dish-status-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: var(--radius-full);
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .dish-status-tag.status-pending {
            background: var(--status-pending-bg);
            color: var(--status-pending-text);
            border: 1px solid var(--status-pending-border);
        }

        .dish-status-tag.status-cooking {
            background: var(--status-cooking-bg);
            color: var(--status-cooking-text);
            border: 1px solid var(--status-cooking-border);
            animation: pulseFlame 2.5s infinite;
        }

        @keyframes pulseFlame {
            0% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.3); }
            70% { box-shadow: 0 0 0 8px rgba(234, 88, 12, 0); }
            100% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0); }
        }

        .dish-status-tag.status-done {
            background: var(--status-done-bg);
            color: var(--status-done-text);
            border: 1px solid var(--status-done-border);
        }

        /* Action Buttons */
        .btn-cancel-pending {
            background: #ffffff;
            border: 1px solid #fecaca;
            color: #ef4444;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: var(--radius-full);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .btn-cancel-pending:hover {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fca5a5;
        }

        .badge-locked-state {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.74rem;
            color: var(--text-light);
            font-weight: 700;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: var(--radius-full);
        }

        /* Running Bill Summary Card */
        .bill-summary-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 22px 24px;
            margin-top: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }

        .bill-calc-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.94rem;
            color: var(--text-muted);
        }

        .bill-calc-row.discount-row {
            color: #10b981;
            font-weight: 700;
        }

        .bill-calc-row.grand-total-row {
            border-top: 2px dashed var(--border);
            margin-top: 10px;
            padding-top: 14px;
            font-size: 1.18rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .grand-highlight-price {
            font-size: 1.45rem;
            color: var(--primary);
            font-weight: 800;
        }

        .bill-counter-notice {
            background: var(--surface-2);
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 12px 16px;
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.86rem;
            color: var(--text-muted);
        }

        .bill-counter-notice i {
            color: var(--primary);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        /* Floating Bottom Action Bar */
        .floating-action-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid var(--border);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.08);
            z-index: 1000;
        }

        .floating-inner-wrap {
            width: 100%;
            max-width: 780px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .floating-total-left {
            display: flex;
            flex-direction: column;
        }

        .floating-total-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .floating-total-val {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .btn-order-more-master {
            background: var(--primary-gradient);
            color: #ffffff;
            font-weight: 800;
            font-size: 0.98rem;
            padding: 14px 28px;
            border-radius: var(--radius-full);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 20px rgba(255, 94, 20, 0.35);
            transition: all 0.25s ease;
        }

        .btn-order-more-master:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(255, 94, 20, 0.45);
            color: #ffffff;
        }

        .btn-refresh-status {
            background: #ffffff;
            color: var(--primary);
            border: 1px solid rgba(255, 94, 20, 0.35);
            border-radius: var(--radius-full);
            padding: 6px 14px;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            white-space: nowrap;
        }

        .btn-refresh-status:hover {
            background: var(--surface-2);
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(255, 94, 20, 0.18);
        }

        .btn-refresh-status:active {
            transform: translateY(0);
        }

        .btn-refresh-status:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        /* Toast Feedback */
        .toast-popup {
            position: fixed;
            top: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: var(--radius-full);
            font-size: 0.92rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.25);
            z-index: 9999;
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            pointer-events: none;
        }

        .toast-popup.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        @media (max-width: 600px) {
            .details-container { padding: 14px 12px; }
            .hero-title-main { font-size: 1.35rem; }
            .btn-order-more-master { padding: 12px 20px; font-size: 0.9rem; }
            .dish-row-item { padding: 14px 14px; }
            .kot-lot-header { padding: 12px 14px; }
        }
    </style>
</head>
<body>

<div class="details-container">

    <!-- Top Sticky Brand Navigation -->
    <header class="details-top-bar">
        <div class="brand-inline-group">
            @if(!empty($restaurant_details) && $restaurant_details->hasLogo())
                <img src="{{ $restaurant_details->logo_url }}" alt="{{ $restaurant_details->name }}" class="brand-logo-small" onerror="this.onerror=null; this.src='{{ asset('logo.png') }}';">
            @else
                <img src="{{ asset('logo.png') }}" alt="Logo" class="brand-logo-small">
            @endif
            <div>
                <h2 class="brand-name-header">{{ $restaurant_details->name ?? 'Premium Restaurant' }}</h2>
                @if(isset($table_details) && !empty($table_details->name))
                    <div class="table-pill-small">
                        <span class="table-live-dot"></span> Table {{ $table_details->name }}
                    </div>
                @endif
            </div>
        </div>

        @if($tableId && $restaurantId && !$isOrderDone)
            <a href="{{ route('temp.order.create', [$tableId, $restaurantId]) }}" class="top-btn-add-more">
                <i class="fas fa-plus"></i>
                <span>Add Dishes</span>
            </a>
        @endif
    </header>

    @if(session('info'))
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px 18px; border-radius: 14px; margin-bottom: 16px; font-size: 0.92rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif

    <!-- Hero Status Banner Card -->
    <div class="order-hero-banner" id="heroBannerSection">
        <div class="hero-banner-accent"></div>

        <div class="hero-top-row">
            <div class="order-id-badge">
                <i class="fas fa-receipt text-primary"></i>
                <span>Order #{{ $orderId }}</span>
            </div>

            @if($isOrderDone)
                <span class="order-status-pill-big status-completed">
                    <i class="fas fa-check-circle"></i> Dining Completed
                </span>
            @else
                <span class="order-status-pill-big status-accepted">
                    <i class="fas fa-fire-burner"></i> In Kitchen
                </span>
            @endif
        </div>

        <h1 class="hero-title-main">
            @if($isOrderDone)
                Order Completed &bull; Thank You!
            @else
                Order Accepted &bull; Kitchen Preparing
            @endif
        </h1>

        <div class="hero-meta-subtitle">
            <span><i class="far fa-user text-muted"></i> Guest: <strong>{{ $customerName ?? 'Table Guest' }}</strong></span>
            @if(isset($table_details) && !empty($table_details->name))
                <span><i class="fas fa-chair text-muted"></i> <strong>Table {{ $table_details->name }}</strong></span>
            @endif
            <span><i class="far fa-clock text-muted"></i> Placed at {{ $mainOrder->created_at ? $mainOrder->created_at->format('h:i A') : now()->format('h:i A') }}</span>
        </div>

        @if(!$isOrderDone)
            <!-- Progress Tracker -->
            <div class="progress-flow-box">
                <div class="progress-step-item is-done">
                    <div class="step-circle-icon"><i class="fas fa-check"></i></div>
                    <span class="step-label step-text-label">Ordered</span>
                </div>
                <div class="progress-step-item is-done">
                    <div class="step-circle-icon"><i class="fas fa-check"></i></div>
                    <span class="step-label step-text-label">Accepted</span>
                </div>
                <div class="progress-step-item is-active">
                    <div class="step-circle-icon"><i class="fas fa-fire-burner"></i></div>
                    <span class="step-label step-text-label">Cooking</span>
                </div>
                <div class="progress-step-item">
                    <div class="step-circle-icon"><i class="fas fa-bell-concierge"></i></div>
                    <span class="step-label step-text-label">Served</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Section: KOT Lots Breakdown -->
    <div class="section-header-row">
        <h3 class="section-title">
            <i class="fas fa-list-check"></i>
            <span>Ordered Items &amp; Kitchen Status</span>
        </h3>
        <button type="button" class="btn-refresh-status" onclick="manualRefreshOrderDetails(this)" title="Refresh kitchen status">
            <i class="fas fa-sync-alt"></i> <span>Refresh Status</span>
        </button>
    </div>

    @php $lotIndex = 1; @endphp
    @foreach($itemsByKot as $kotNo => $kotItems)
        <div class="kot-lot-card" id="kotLot_{{ md5($kotNo) }}">
            <div class="kot-lot-header">
                <div class="kot-lot-title-group">
                    <span class="kot-number-badge">
                        <i class="fas fa-receipt"></i> {{ $kotNo }}
                    </span>
                    <span class="kot-batch-tag">
                        @if($lotIndex === 1)
                            Initial Order Batch
                        @else
                            Added Batch #{{ $lotIndex - 1 }}
                        @endif
                    </span>
                </div>
                <span class="kot-items-count">{{ $kotItems->count() }} {{ Str::plural('dish', $kotItems->count()) }}</span>
            </div>

            <div class="kot-dishes-list">
                @foreach($kotItems as $itm)
                    @php
                        $itmName = $itm->subcategory->name ?? 'Dish';
                        $itmStatus = strtoupper($itm->order_status ?? 'PENDING');
                        $itmTotal = floatval($itm->total_amount);
                        $itmPrice = floatval($itm->discounted_price ?? $itm->price);
                    @endphp
                    <div class="dish-row-item" id="dishRow_{{ $itm->id }}" data-item-id="{{ $itm->id }}">
                        <div class="dish-info-col">
                            <div class="dish-title-row">
                                <span class="dish-qty-chip">{{ $itm->quantity }}x</span>
                                <span class="dish-name-text">{{ $itmName }}</span>
                            </div>
                            @if(!empty($itm->addons_list) && count($itm->addons_list) > 0)
                                <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px;">
                                    @foreach($itm->addons_list as $addon)
                                        <span style="background: #fff3ed; color: #ff5e14; border: 1px solid #ffd8c7; border-radius: 6px; padding: 2px 6px; font-size: 0.72rem; font-weight: 600;">
                                            + {{ $addon['name'] }} (₹{{ number_format($addon['price'], 2) }}{{ ($addon['qty'] ?? 1) > 1 ? ' x' . $addon['qty'] : '' }})
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="dish-unit-price-note">
                                ₹{{ number_format($itmPrice, 2) }} each &bull; Line Total: ₹{{ number_format($itmTotal, 2) }}
                            </div>
                        </div>

                        <div class="dish-right-col">
                            <!-- Kitchen Status Badge -->
                            <div id="dishStatusWrap_{{ $itm->id }}">
                                @if($itmStatus === 'COOKING')
                                    <span class="dish-status-tag status-cooking" id="statusBadge_{{ $itm->id }}">
                                        <i class="fas fa-fire-burner"></i> Cooking
                                    </span>
                                @elseif($itmStatus === 'DONE')
                                    <span class="dish-status-tag status-done" id="statusBadge_{{ $itm->id }}">
                                        <i class="fas fa-check-circle"></i> Cooked
                                    </span>
                                @else
                                    <span class="dish-status-tag status-pending" id="statusBadge_{{ $itm->id }}">
                                        <i class="fas fa-hourglass-half"></i> Pending
                                    </span>
                                @endif
                            </div>

                            <!-- Action: Cancel Pending or Locked Indicator -->
                            <div id="dishActionWrap_{{ $itm->id }}">
                                @if($itmStatus === 'PENDING')
                                    <button type="button" class="btn-cancel-pending" onclick="cancelPendingDish({{ $itm->id }})" title="Cancel this dish before cooking starts">
                                        <i class="fas fa-trash-alt"></i> Cancel
                                    </button>
                                @else
                                    <span class="badge-locked-state" title="Kitchen is actively preparing this dish and it cannot be cancelled.">
                                        <i class="fas fa-lock"></i> Locked
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @php $lotIndex++; @endphp
    @endforeach

    <!-- Running Bill Summary Card -->
    <div class="bill-summary-card">
        <h4 style="font-family: 'Playfair Display', serif; font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-invoice text-primary"></i> Running Bill Summary
        </h4>

        <div class="bill-calc-row">
            <span>Subtotal ({{ $mainOrder->orderItems->count() }} items)</span>
            <span>₹<span id="billSubtotalVal">{{ number_format($subtotal, 2) }}</span></span>
        </div>

        @if(isset($discount) && $discount > 0)
            <div class="bill-calc-row discount-row">
                <span>Special Discount Savings</span>
                <span>− ₹{{ number_format($discount, 2) }}</span>
            </div>
        @endif

        <div class="bill-calc-row">
            <span>Net Taxable</span>
            <span>₹{{ number_format($taxableAmount, 2) }}</span>
        </div>

        @if($isGstBill && $gstAmount > 0)
            <div class="bill-calc-row">
                <span>GST (CGST + SGST)</span>
                <span>₹{{ number_format($gstAmount, 2) }}</span>
            </div>
        @endif

        <div class="bill-calc-row grand-total-row">
            <span>Total Running Amount</span>
            <span class="grand-highlight-price">₹<span id="billGrandTotalVal">{{ number_format($grandTotal, 2) }}</span></span>
        </div>

        <div class="bill-counter-notice">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Pay at Counter / Table:</strong> No checkout needed here. Restaurant staff will present your final bill when you are ready to conclude dining.
            </div>
        </div>
    </div>

</div><!-- /.details-container -->

<!-- Floating Bottom Sticky Bar -->
<div class="floating-action-bar">
    <div class="floating-inner-wrap">
        <div class="floating-total-left">
            <span class="floating-total-label">Running Total</span>
            <span class="floating-total-val">₹{{ number_format($grandTotal, 2) }}</span>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            @if($tableId && $restaurantId && !$isOrderDone)
                <a href="{{ route('temp.order.create', [$tableId, $restaurantId]) }}" class="btn-order-more-master">
                    <i class="fas fa-plus-circle"></i>
                    <span>Order More Items</span>
                </a>
            @else
                <a href="{{ route('temp.order.fresh', [$tableId, $restaurantId]) }}" class="btn-order-more-master" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-plus-circle"></i>
                    <span>Start Fresh Order</span>
                </a>
            @endif
        </div>
    </div>
</div>

<div id="toastNotification" class="toast-popup">
    <i class="fas fa-check-circle text-success"></i>
    <span id="toastMessage">Success</span>
</div>

<script>
    const orderId = {{ $orderRecordId }};

    function showToast(msg, isSuccess = true) {
        const toast = document.getElementById('toastNotification');
        const text = document.getElementById('toastMessage');
        if (!toast || !text) return;
        text.textContent = msg;
        toast.querySelector('i').className = isSuccess ? 'fas fa-check-circle text-success' : 'fas fa-exclamation-triangle text-danger';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3200);
    }

    // Cancel a pending dish item
    function cancelPendingDish(itemId) {
        if (!confirm('Are you sure you want to cancel this pending dish?')) {
            return;
        }

        const row = document.getElementById('dishRow_' + itemId);
        const btn = row ? row.querySelector('.btn-cancel-pending') : null;
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
            btn.disabled = true;
        }

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
                if (row) {
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(20px)';
                    row.style.transition = 'all 0.3s ease';
                    setTimeout(() => {
                        row.remove();
                        setTimeout(() => window.location.reload(), 500);
                    }, 300);
                } else {
                    window.location.reload();
                }
            } else {
                alert((data && data.message) ? data.message : 'Could not cancel item.');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-trash-alt"></i> Cancel';
                    btn.disabled = false;
                }
            }
        })
        .catch(err => {
            alert(err.message || 'Error while cancelling item.');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-trash-alt"></i> Cancel';
                btn.disabled = false;
            }
        });
    }

    // Manual on-demand status refresh (no continuous background polling)
    function manualRefreshOrderDetails(btn) {
        if (!orderId) return;
        const icon = btn ? btn.querySelector('i') : document.querySelector('.btn-refresh-status i');
        if (icon) icon.classList.add('fa-spin');
        if (btn) btn.disabled = true;

        fetch("{{ route('order.status.check', ':id') }}".replace(':id', orderId) + '?type=main')
            .then(res => res.json())
            .then(data => {
                if (icon) icon.classList.remove('fa-spin');
                if (btn) btn.disabled = false;
                if (!data.status) return;

                // If order completed or closed by restaurant:
                if (data.is_completed) {
                    showToast('Dining completed by restaurant. Reloading...');
                    setTimeout(() => window.location.reload(), 1200);
                    return;
                }

                // Update each dish's kitchen status badge & lock state
                if (data.items && Array.isArray(data.items)) {
                    data.items.forEach(itm => {
                        const badgeWrap = document.getElementById('dishStatusWrap_' + itm.id);
                        const actionWrap = document.getElementById('dishActionWrap_' + itm.id);

                        if (badgeWrap) {
                            if (itm.order_status === 'COOKING') {
                                badgeWrap.innerHTML = '<span class="dish-status-tag status-cooking" id="statusBadge_' + itm.id + '"><i class="fas fa-fire-burner"></i> Cooking</span>';
                            } else if (itm.order_status === 'DONE') {
                                badgeWrap.innerHTML = '<span class="dish-status-tag status-done" id="statusBadge_' + itm.id + '"><i class="fas fa-check-circle"></i> Cooked</span>';
                            } else {
                                badgeWrap.innerHTML = '<span class="dish-status-tag status-pending" id="statusBadge_' + itm.id + '"><i class="fas fa-hourglass-half"></i> Pending</span>';
                            }
                        }

                        if (actionWrap) {
                            if (itm.order_status === 'PENDING') {
                                actionWrap.innerHTML = '<button type="button" class="btn-cancel-pending" onclick="cancelPendingDish(' + itm.id + ')"><i class="fas fa-trash-alt"></i> Cancel</button>';
                            } else {
                                actionWrap.innerHTML = '<span class="badge-locked-state" title="Kitchen is actively preparing this dish and it cannot be cancelled."><i class="fas fa-lock"></i> Locked</span>';
                            }
                        }
                    });
                    showToast('Kitchen status refreshed successfully!');
                }
            })
            .catch(err => {
                if (icon) icon.classList.remove('fa-spin');
                if (btn) btn.disabled = false;
                showToast('Could not refresh status. Please try again.');
            });
    }
</script>

</body>
</html>
