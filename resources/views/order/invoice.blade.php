<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice #{{ $order->order_id ?? $order->id }} | {{ $order->customer_name ?: 'Guest' }} • Bill&Bite POS</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --inv-primary: #ff5e14;
            --inv-primary-dark: #e04a08;
            --inv-primary-light: #fff3ed;
            --inv-primary-gradient: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
            --inv-dark: #0f172a;
            --inv-slate: #1e293b;
            --inv-muted: #64748b;
            --inv-border: #e2e8f0;
            --inv-border-light: #f1f5f9;
            --inv-bg: #f8fafc;
            --inv-card: #ffffff;
            --inv-success: #10b981;
            --inv-success-dark: #059669;
            --inv-success-bg: #ecfdf5;
            --inv-danger: #ef4444;
            --inv-danger-bg: #fef2f2;
            --inv-warning: #f59e0b;
            --inv-warning-bg: #fffbeb;
            --inv-info: #0284c7;
            --inv-info-bg: #f0f9ff;
            --inv-purple: #8b5cf6;
            --inv-radius-lg: 20px;
            --inv-radius-md: 14px;
            --inv-radius-sm: 10px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: var(--inv-slate);
            -webkit-font-smoothing: antialiased;
        }

        .inv-page-wrap {
            max-width: 1040px;
            margin: 0 auto;
            padding: 24px 16px 48px;
        }

        /* Top Action Bar */
        .inv-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 22px;
        }

        .inv-top-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .inv-order-badge {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            padding: 7px 18px;
            border-radius: 30px;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.18);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .inv-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-inv-primary {
            background: var(--inv-primary-gradient);
            color: #ffffff !important;
            border: none;
            padding: 10px 22px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(255, 94, 20, 0.28);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            text-decoration: none;
        }

        .btn-inv-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 94, 20, 0.38);
            color: #ffffff !important;
        }

        .btn-inv-slate {
            background: #1e293b;
            color: #ffffff !important;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(30, 41, 59, 0.2);
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-inv-slate:hover {
            background: #0f172a;
            transform: translateY(-2px);
            color: #ffffff !important;
        }

        .btn-inv-whatsapp {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: #ffffff !important;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(37, 211, 102, 0.3);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            text-decoration: none;
        }

        .btn-inv-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 211, 102, 0.42);
            color: #ffffff !important;
        }

        .btn-inv-back {
            background: #ffffff;
            color: var(--inv-slate) !important;
            border: 1.5px solid var(--inv-border);
            padding: 9px 18px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-inv-back:hover {
            background: #f8fafc;
            border-color: var(--inv-primary);
            color: var(--inv-primary) !important;
        }

        /* Invoice Main Card */
        .invoice-card {
            background: #ffffff;
            border-radius: var(--inv-radius-lg);
            border: 1.5px solid var(--inv-border);
            box-shadow: 0 10px 35px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        /* Hero Header Canvas */
        .invoice-header-canvas {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);
            color: #ffffff;
            padding: 32px 36px;
            position: relative;
            overflow: hidden;
            border-bottom: 4px solid var(--inv-primary);
        }

        .invoice-header-canvas::after {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(255, 94, 20, 0.22), transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .inv-brand-block {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .inv-brand-logo {
            width: 68px;
            height: 68px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #ffffff;
            flex-shrink: 0;
            overflow: hidden;
        }

        .inv-brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .inv-restaurant-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 4px 0;
            line-height: 1.2;
        }

        .inv-restaurant-meta {
            font-size: 0.82rem;
            color: #94a3b8;
            margin: 0;
            line-height: 1.5;
        }

        .inv-doc-title-block {
            text-align: right;
        }

        .inv-doc-type-pill {
            display: inline-block;
            background: var(--inv-primary-gradient);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 6px;
            box-shadow: 0 2px 10px rgba(255, 94, 20, 0.35);
        }

        .inv-doc-number {
            font-family: 'Outfit', sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
        }

        .inv-doc-datetime {
            font-size: 0.82rem;
            color: #cbd5e1;
            margin-top: 4px;
        }

        /* Meta Information Strip */
        .inv-meta-grid {
            background: #f8fafc;
            border-bottom: 1.5px solid var(--inv-border);
            padding: 20px 36px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 18px;
        }

        .inv-meta-item {
            display: flex;
            flex-direction: column;
        }

        .inv-meta-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--inv-muted);
            margin-bottom: 3px;
        }

        .inv-meta-val {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--inv-dark);
        }

        /* Financial Summary Deck */
        .inv-financial-deck {
            margin: 26px 36px 18px;
            background: #ffffff;
            border: 1.5px solid var(--inv-border);
            border-radius: var(--inv-radius-md);
            padding: 20px 24px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
        }

        .inv-fin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            align-items: center;
        }

        .inv-fin-col {
            text-align: center;
            padding: 14px 16px;
            border-radius: var(--inv-radius-sm);
            background: #f8fafc;
            border: 1.5px solid var(--inv-border-light);
            transition: all 0.2s ease;
        }

        .inv-fin-col.col-total {
            border-color: #cbd5e1;
        }

        .inv-fin-col.col-paid {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .inv-fin-col.col-due {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .inv-fin-col.col-due.settled {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .inv-fin-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--inv-muted);
            margin-bottom: 4px;
        }

        .inv-fin-val {
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.15;
        }

        .inv-fin-val.text-total { color: var(--inv-dark); }
        .inv-fin-val.text-paid { color: var(--inv-success-dark); }
        .inv-fin-val.text-due { color: var(--inv-danger); }
        .inv-fin-val.text-due.settled { color: var(--inv-success-dark); }

        .inv-status-pill-wrap {
            text-align: center;
            margin-top: 16px;
        }

        .inv-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.82rem;
            font-weight: 800;
            padding: 6px 18px;
            border-radius: 30px;
            letter-spacing: 0.04em;
        }

        .inv-status-pill.status-paid {
            background: #ecfdf5;
            color: #047857;
            border: 1.5px solid #a7f3d0;
        }

        .inv-status-pill.status-partial {
            background: #fffbeb;
            color: #b45309;
            border: 1.5px solid #fde68a;
        }

        .inv-status-pill.status-pending {
            background: #fef2f2;
            color: #b91c1c;
            border: 1.5px solid #fecaca;
        }

        /* Tables & Section Layout */
        .inv-section-wrap {
            padding: 8px 36px 26px;
        }

        .inv-section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.12rem;
            font-weight: 800;
            color: var(--inv-dark);
            margin: 20px 0 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .inv-section-title .title-text {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .inv-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid var(--inv-border);
            border-radius: 12px;
            overflow: hidden;
        }

        .inv-table thead th {
            background: #f8fafc;
            color: var(--inv-muted);
            font-family: 'Outfit', sans-serif;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 12px 16px;
            border-bottom: 1.5px solid var(--inv-border);
            white-space: nowrap;
        }

        .inv-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--inv-border-light);
            font-size: 0.88rem;
            vertical-align: middle;
        }

        .inv-table tbody tr:hover td {
            background-color: #fafbfc;
        }

        .food-type-dot {
            width: 15px;
            height: 15px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            border: 1.5px solid;
            margin-right: 6px;
        }
        .food-type-dot.veg { border-color: #10b981; color: #10b981; }
        .food-type-dot.nonveg { border-color: #ef4444; color: #ef4444; }

        /* Itemized Addons Display in Invoice */
        .inv-item-addons-box {
            margin-top: 6px;
            background: #fffcf8;
            border: 1px solid #fed7aa;
            border-left: 3px solid #ff5e14;
            border-radius: 8px;
            padding: 5px 10px;
            display: inline-block;
            max-width: 100%;
        }

        .inv-addons-header {
            font-size: 0.7rem;
            font-weight: 800;
            color: #c2410c;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .inv-addons-list {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .inv-addon-line {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            color: #1e293b;
            flex-wrap: wrap;
        }

        .inv-addon-food-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }
        .inv-addon-food-dot.veg { background: #10b981; }
        .inv-addon-food-dot.nonveg { background: #ef4444; }

        .inv-addon-name {
            font-weight: 600;
        }

        .inv-addon-qty-badge {
            background: #ffedd5;
            color: #c2410c;
            font-weight: 800;
            font-size: 0.7rem;
            padding: 1px 5px;
            border-radius: 4px;
        }

        .inv-addon-rate {
            color: var(--inv-muted);
            font-size: 0.72rem;
            font-weight: 600;
        }

        .inv-addon-cost {
            font-weight: 700;
            color: #0f172a;
        }
        
        .inv-base-price-note {
            font-size: 0.72rem;
            color: var(--inv-muted);
            margin-top: 3px;
        }

        /* Calculation Summary Box */
        .inv-calc-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .inv-calc-box {
            width: 380px;
            max-width: 100%;
            background: #f8fafc;
            border: 1.5px solid var(--inv-border);
            border-radius: var(--inv-radius-md);
            padding: 18px 22px;
        }

        .inv-calc-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.88rem;
            margin-bottom: 8px;
            color: var(--inv-slate);
        }

        .inv-calc-row.grand-total {
            font-family: 'Outfit', sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--inv-dark);
            border-top: 2px dashed var(--inv-border);
            padding-top: 12px;
            margin-top: 10px;
            margin-bottom: 0;
        }

        /* Payment Badges */
        .payment-method-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .method-cash { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .method-upi { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
        .method-card { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
        .method-bank_transfer { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .method-other { background: #f1f5f9; color: var(--inv-muted); border: 1px solid var(--inv-border); }

        /* QR Scan & Pay Box */
        .inv-qr-card {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
            border: 1.5px solid var(--inv-border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            max-width: 480px;
            margin: 20px auto 0;
        }

        .inv-qr-img {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            background: #ffffff;
            padding: 4px;
            flex-shrink: 0;
        }

        .inv-qr-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Premium Modal Design */
        .modal-content-premium {
            border: none;
            border-radius: 24px;
            box-shadow: 0 25px 60px -10px rgba(15, 23, 42, 0.35);
            overflow: hidden;
        }

        .modal-header-premium {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 22px 28px;
            border-bottom: 3px solid var(--inv-primary);
        }

        .modal-header-premium .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-body-premium {
            padding: 28px;
            background: #ffffff;
        }

        .modal-footer-premium {
            padding: 16px 28px;
            background: #f8fafc;
            border-top: 1.5px solid var(--inv-border);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Form Controls in Modal */
        .pm-label {
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--inv-dark);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pm-input-wrap {
            position: relative;
        }

        .pm-input-prefix {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-family: 'Outfit', sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--inv-primary);
            pointer-events: none;
        }

        .pm-input-control {
            width: 100%;
            border: 1.5px solid var(--inv-border);
            border-radius: 12px;
            padding: 11px 16px;
            font-size: 0.95rem;
            font-weight: 600;
            background: #f8fafc;
            outline: none;
            transition: all 0.2s ease;
            color: var(--inv-dark);
        }

        .pm-input-control.has-prefix {
            padding-left: 36px;
            font-family: 'Outfit', monospace;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--inv-dark);
        }

        .pm-input-control:focus {
            background: #ffffff;
            border-color: var(--inv-primary);
            box-shadow: 0 0 0 4px rgba(255, 94, 20, 0.12);
        }

        /* Quick Amount Preset Chips */
        .quick-presets-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
            margin-bottom: 18px;
        }

        .preset-chip {
            background: #f1f5f9;
            border: 1.5px solid var(--inv-border);
            color: var(--inv-slate);
            font-size: 0.78rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .preset-chip:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
        }

        .preset-chip.chip-full {
            background: var(--inv-primary-light);
            border-color: rgba(255, 94, 20, 0.3);
            color: var(--inv-primary-dark);
        }

        .preset-chip.chip-full:hover {
            background: #ffe6d6;
        }

        /* Payment Mode Grid Selection */
        .payment-method-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }

        .payment-mode-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .payment-mode-card {
            border: 1.5px solid var(--inv-border);
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            background: #f8fafc;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .payment-mode-card i {
            font-size: 1.25rem;
            color: var(--inv-muted);
            transition: all 0.2s ease;
        }

        .payment-mode-card span {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--inv-slate);
        }

        .payment-mode-card:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .payment-mode-radio:checked + .payment-mode-card {
            background: #ffffff;
            border-color: var(--inv-primary);
            box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.15);
        }

        .payment-mode-radio:checked + .payment-mode-card i {
            color: var(--inv-primary);
            transform: scale(1.1);
        }

        .payment-mode-radio:checked + .payment-mode-card span {
            color: var(--inv-dark);
            font-weight: 800;
        }

        .hidden-iframe {
            position: absolute;
            width: 0;
            height: 0;
            border: 0;
            visibility: hidden;
        }

        .toast-float {
            position: fixed;
            top: 24px;
            right: 24px;
            padding: 14px 22px;
            border-radius: 14px;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.88rem;
            z-index: 99999;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInRight 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .toast-success { background: #10b981; }
        .toast-error { background: #ef4444; }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media print {
            .inv-topbar, .inv-actions, .pc-sidebar, .pc-header, .delete-payment, #showAddPaymentModal, #printInvoiceBtn {
                display: none !important;
            }
            body, .pc-container, .pc-content, .inv-page-wrap {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .invoice-card {
                border: none !important;
                box-shadow: none !important;
            }
            .invoice-header-canvas {
                background: #1e293b !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media (max-width: 768px) {
            .invoice-header-canvas {
                padding: 24px 20px;
            }
            .inv-meta-grid {
                padding: 18px 20px;
            }
            .inv-section-wrap {
                padding: 8px 20px 24px;
            }
            .inv-financial-deck {
                margin: 20px 20px 12px;
                padding: 16px;
            }
            .inv-brand-block {
                flex-direction: column;
                align-items: flex-start;
            }
            .inv-doc-title-block {
                text-align: left;
                margin-top: 16px;
            }
        }
    </style>
    @if(request()->has('embed'))
    <style>
        .pc-sidebar, .pc-header, .btn-inv-back {
            display: none !important;
        }
        .pc-container {
            margin-left: 0 !important;
            top: 0 !important;
            padding: 0 !important;
        }
        .pc-content {
            padding: 10px !important;
        }
    </style>
    @endif
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <div class="inv-page-wrap">
            
            {{-- Flash Messages --}}
            @include('includes.message')

            {{-- 1. Top Action Toolbar --}}
            <div class="inv-topbar">
                <div class="inv-top-title">
                    <span class="inv-order-badge">
                        <i class="fa-solid fa-receipt text-warning"></i> #{{ $order->order_id ?? $order->id }}
                    </span>
                    <span class="text-muted small fw-bold">
                        <i class="fa-regular fa-clock me-1"></i> {{ $order->created_at->format('d M Y, h:i A') }}
                    </span>
                </div>

                <div class="inv-actions">
                    <button type="button" class="btn-inv-whatsapp" id="showWhatsappModalBtn">
                        <i class="fa-brands fa-whatsapp"></i> Share Bill WhatsApp
                    </button>
                    <a href="{{ route('order.public.download', ['id' => $order->id, 'download' => 1]) }}" class="btn-inv-slate" target="_blank" title="Direct PDF Download">
                        <i class="fa-solid fa-file-arrow-down text-warning"></i> Download PDF
                    </a>
                    <button type="button" class="btn-inv-primary" id="showAddPaymentModal">
                        <i class="fa-solid fa-circle-plus"></i> Add Payment
                    </button>
                    <button type="button" class="btn-inv-slate" id="printInvoiceBtn">
                        <i class="fa-solid fa-print"></i> Print Invoice
                    </button>
                    @if(request('return') == 'rapid_bill' || request('return') == 'rapid-bill')
                    <a href="{{ route('rapid.bill') }}" class="btn-inv-primary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-bolt"></i> Back to Rapid Bill
                    </a>
                    @else
                    <a href="{{ route('order.management.dashboard') }}" class="btn-inv-back">
                        <i class="fa-solid fa-arrow-left"></i> Back to Orders
                    </a>
                    @endif
                </div>
            </div>

            {{-- 2. Invoice Document Canvas --}}
            <div class="invoice-card" id="invoiceContent">
                
                {{-- Header Canvas --}}
                <div class="invoice-header-canvas">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="inv-brand-block">
                                <div class="inv-brand-logo">
                                    @if(!empty($order->restaurant->logo))
                                        <img src="{{ asset('storage/' . $order->restaurant->logo) }}" alt="Logo">
                                    @else
                                        <i class="fa-solid fa-utensils"></i>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="inv-restaurant-name">{{ $order->restaurant->name ?? config('app.name', 'Restaurant') }}</h2>
                                    <p class="inv-restaurant-meta">
                                        @if(!empty($order->restaurant->address))
                                            <i class="fa-solid fa-location-dot me-1"></i> {{ $order->restaurant->address }}
                                            @if(!empty($order->restaurant->pincode)) - {{ $order->restaurant->pincode }} @endif
                                            <br>
                                        @endif
                                        @if(!empty($order->restaurant->gstin))
                                            <strong>GSTIN:</strong> {{ $order->restaurant->gstin }} |
                                        @endif
                                        @if(!empty($order->restaurant->fssai_number))
                                            <strong>FSSAI:</strong> {{ $order->restaurant->fssai_number }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="inv-doc-title-block">
                                <span class="inv-doc-type-pill">
                                    {{ $order->is_gst_bill == 'YES' ? 'TAX INVOICE' : 'RETAIL INVOICE' }}
                                </span>
                                <h3 class="inv-doc-number">Invoice #{{ $order->order_id ?? $order->id }}</h3>
                                <div class="inv-doc-datetime">
                                    <span>Date: {{ $order->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Meta Info Grid --}}
                <div class="inv-meta-grid">
                    <div class="inv-meta-item">
                        <span class="inv-meta-label">Customer Name</span>
                        <span class="inv-meta-val">{{ $order->customer_name ?: 'Walk-in Customer' }}</span>
                    </div>

                    <div class="inv-meta-item">
                        <span class="inv-meta-label">Phone Number</span>
                        <span class="inv-meta-val font-monospace">{{ $order->customer_phone ?: 'N/A' }}</span>
                    </div>

                    <div class="inv-meta-item">
                        <span class="inv-meta-label">Order Type</span>
                        <span class="inv-meta-val">
                            @if($order->order_type == 'DINE_IN')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <i class="fa-solid fa-chair me-1"></i> Dine In ({{ @$order->table->name ?? 'Table' }})
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                    <i class="fa-solid fa-bag-shopping me-1"></i> Takeaway
                                </span>
                            @endif
                        </span>
                    </div>

                    <div class="inv-meta-item">
                        <span class="inv-meta-label">Tax Specification</span>
                        <span class="inv-meta-val">
                            @if($order->is_gst_bill == 'YES')
                                <span class="badge bg-purple-subtle text-purple border px-2 py-1" style="background:#f5f3ff; color:#7c3aed; border-color:#ddd6fe;">
                                    GST Bill ({{ $order->restaurant_gst_percentage ?? 0 }}%)
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1">Non-GST Bill</span>
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Financial Status Summary Deck --}}
                <div class="inv-financial-deck">
                    <div class="inv-fin-grid">
                        <div class="inv-fin-col col-total">
                            <span class="inv-fin-label">Total Payable</span>
                            <div class="inv-fin-val text-total">{{ number_format($order->grand_total, 2) }}</div>
                        </div>

                        <div class="inv-fin-col col-paid">
                            <span class="inv-fin-label">Total Paid Amount</span>
                            <div class="inv-fin-val text-paid"><span id="totalPaidAmount">{{ number_format($totalPaid, 2) }}</span></div>
                        </div>

                        <div class="inv-fin-col col-due {{ $balanceDue <= 0 ? 'settled' : '' }}">
                            <span class="inv-fin-label">Remaining Balance</span>
                            <div class="inv-fin-val text-due {{ $balanceDue <= 0 ? 'settled' : '' }}">
                                <span id="balanceDueAmount">{{ number_format(max(0, $balanceDue), 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="inv-status-pill-wrap" id="paymentStatusBadge">
                        @if($balanceDue <= 0)
                            <span class="inv-status-pill status-paid">
                                <i class="fa-solid fa-circle-check"></i> FULLY SETTLED &amp; PAID
                            </span>
                        @elseif($totalPaid > 0)
                            <span class="inv-status-pill status-partial">
                                <i class="fa-solid fa-clock"></i> PARTIAL PAYMENT RECEIVED
                            </span>
                        @else
                            <span class="inv-status-pill status-pending">
                                <i class="fa-solid fa-circle-exclamation"></i> PAYMENT PENDING
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Itemized Ordered Dishes Table --}}
                @if($order->orderItems && count($order->orderItems) > 0)
                <div class="inv-section-wrap">
                    <div class="inv-section-title">
                        <div class="title-text">
                            <i class="fa-solid fa-bowl-food text-primary"></i>
                            <span>Ordered Items Summary</span>
                        </div>
                        <span class="badge bg-light text-dark border">{{ count($order->orderItems) }} Items</span>
                    </div>

                    <div class="table-responsive">
                        <table class="inv-table">
                            <thead>
                                <tr>
                                    <th width="40">#</th>
                                    <th>Dish Description</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Disc %</th>
                                    <th class="text-end">Taxable Val</th>
                                    <th class="text-end">GST Amt</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $idx => $item)
                                @php
                                    $addons = $item->addons_list;
                                    $basePrice = $item->subcategory->price ?? $item->price;
                                    $hasAddons = !empty($addons);
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            @if(@$item->subcategory->food_type == 'non-veg')
                                                <span class="food-type-dot nonveg mt-1" title="Non-Veg"><i class="fa-solid fa-circle"></i></span>
                                            @else
                                                <span class="food-type-dot veg mt-1" title="Veg"><i class="fa-solid fa-circle"></i></span>
                                            @endif
                                            <div>
                                                <strong class="text-dark">{{ $item->subcategory->name ?? 'Custom Item' }}</strong>

                                                @if($hasAddons)
                                                    <div class="inv-item-addons-box">
                                                        <div class="inv-addons-header">
                                                            <i class="fa-solid fa-puzzle-piece"></i> Mapped Add-ons ({{ count($addons) }})
                                                        </div>
                                                        <div class="inv-addons-list">
                                                            @foreach($addons as $a)
                                                                @php
                                                                    $aQty = $a['qty'] ?? $a['quantity'] ?? 1;
                                                                    $aPrice = floatval($a['price'] ?? 0);
                                                                    $aTotal = $aPrice * $aQty;
                                                                    $isNonVeg = isset($a['food_type']) && in_array(strtoupper($a['food_type']), ['NON-VEG', 'NONVEG']);
                                                                @endphp
                                                                <div class="inv-addon-line">
                                                                    <span class="inv-addon-food-dot {{ $isNonVeg ? 'nonveg' : 'veg' }}"></span>
                                                                    <span class="inv-addon-name">{{ $a['name'] ?? 'Add-on' }}</span>
                                                                    <span class="inv-addon-rate">({{ number_format($aPrice, 2) }})</span>
                                                                    <span class="inv-addon-qty-badge">x{{ $aQty }}</span>
                                                                    <span class="inv-addon-cost">+{{ number_format($aTotal, 2) }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="inv-base-price-note">
                                                        Base: {{ number_format($basePrice, 2) }} • Unit: {{ number_format($item->price, 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-center text-muted small">
                                        {{ $item->item_discount_percentage > 0 ? $item->item_discount_percentage . '%' : '-' }}
                                    </td>
                                    <td class="text-end">{{ number_format($item->taxable_amount ?? ($item->price * $item->quantity), 2) }}</td>
                                    <td class="text-end text-muted">{{ number_format($item->gst_amount ?? 0, 2) }}</td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($item->total_amount ?? ($item->price * $item->quantity), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Calculation Breakdown Box --}}
                    <div class="inv-calc-container">
                        <div class="inv-calc-box">
                            <div class="inv-calc-row">
                                <span class="text-muted">Item Subtotal:</span>
                                <span class="fw-semibold">{{ number_format($order->total_amount, 2) }}</span>
                            </div>

                            @if($order->discount > 0)
                            <div class="inv-calc-row text-danger">
                                <span>Order Discount ({{ $order->discount_percentage ?? 0 }}%):</span>
                                <span class="fw-semibold">- {{ number_format($order->discount, 2) }}</span>
                            </div>
                            @endif

                            @if($order->is_gst_bill == 'YES' && $order->gst_amount > 0)
                            <div class="inv-calc-row text-primary">
                                <span>GST ({{ $order->restaurant_gst_percentage ?? 0 }}%):</span>
                                <span class="fw-semibold">+ {{ number_format($order->gst_amount, 2) }}</span>
                            </div>
                            @endif

                            @if($order->round_off != 0)
                            <div class="inv-calc-row text-muted small">
                                <span>Round Off:</span>
                                <span>{{ $order->round_off > 0 ? '+' : '' }}{{ number_format($order->round_off, 2) }}</span>
                            </div>
                            @endif

                            <div class="inv-calc-row grand-total">
                                <span>Grand Total:</span>
                                <span class="text-primary">{{ number_format($order->grand_total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Payment History Ledger Section --}}
                <div class="inv-section-wrap" style="border-top: 1.5px solid var(--inv-border-light);">
                    <div class="inv-section-title">
                        <div class="title-text">
                            <i class="fa-solid fa-clock-rotate-left text-success"></i>
                            <span>Payment Transaction History</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="inv-table" id="paymentTable">
                            <thead>
                                <tr>
                                    <th width="40">#</th>
                                    <th>Date &amp; Time</th>
                                    <th class="text-end">Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Reference / TXN No</th>
                                    <th>Remarks</th>
                                    <th width="60" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="paymentTableBody">
                                @forelse($payments as $index => $payment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-muted font-monospace small">
                                        {{ $payment->payment_date ? $payment->payment_date->format('d M Y, h:i A') : $payment->created_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td class="text-end fw-extrabold text-success" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">
                                        {{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="payment-method-badge method-{{ strtolower(str_replace(' ', '_', $payment->payment_method)) }}">
                                            @if(str_contains(strtolower($payment->payment_method), 'cash'))
                                                <i class="fa-solid fa-money-bill-wave me-1"></i>
                                            @elseif(str_contains(strtolower($payment->payment_method), 'upi'))
                                                <i class="fa-solid fa-qrcode me-1"></i>
                                            @elseif(str_contains(strtolower($payment->payment_method), 'card'))
                                                <i class="fa-solid fa-credit-card me-1"></i>
                                            @else
                                                <i class="fa-solid fa-wallet me-1"></i>
                                            @endif
                                            {{ $payment->payment_method }}
                                        </span>
                                    </td>
                                    <td class="font-monospace small text-dark">{{ $payment->transaction_no ?: '-' }}</td>
                                    <td class="text-muted small">{{ $payment->remarks ?: '-' }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-payment rounded-circle" 
                                                style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                                data-id="{{ $payment->id }}"
                                                data-amount="{{ $payment->amount }}"
                                                title="Delete Payment Record">
                                            <i class="fa-solid fa-trash-can" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-credit-card fa-2x mb-2 d-block text-muted opacity-50"></i>
                                        No payments recorded yet for this order.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total Realized Payments:</th>
                                    <th class="text-end text-success fw-bold" id="totalPaidFooter">{{ number_format($totalPaid, 2) }}</th>
                                    <th colspan="4"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- QR Code Scan to Pay Section --}}
                @if(!empty($order->restaurant->qr_code_image) || !empty($order->restaurant->upi_id))
                <div class="p-4" style="background: #fafbfc; border-top: 1.5px solid var(--inv-border);">
                    <div class="inv-qr-card">
                        @if(!empty($order->restaurant->qr_code_image))
                            <div class="inv-qr-img">
                                <img src="{{ asset('storage/' . $order->restaurant->qr_code_image) }}" alt="QR Code">
                            </div>
                        @endif
                        <div>
                            <span class="badge bg-primary-subtle text-primary fw-bold mb-1 px-2 py-1">
                                <i class="fa-solid fa-bolt me-1"></i> Instant UPI Payment
                            </span>
                            <div class="fw-bold text-dark fs-6">Scan QR with any UPI App</div>
                            @if(!empty($order->restaurant->upi_id))
                                <div class="text-muted small mt-1">
                                    UPI ID: <strong class="text-dark font-monospace">{{ $order->restaurant->upi_id }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Footer Greeting --}}
                <div class="p-4 text-center text-muted small" style="background: #ffffff; border-top: 1.5px solid var(--inv-border-light);">
                    <p class="mb-0 fw-semibold text-secondary">
                        <i class="fa-solid fa-heart text-danger me-1"></i> Thank you for dining with {{ $order->restaurant->name ?? 'us' }}! Please visit us again.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add Payment Modal (Bootstrap 5) -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-premium">
            <div class="modal-header modal-header-premium">
                <h5 class="modal-title" id="addPaymentModalLabel">
                    <i class="fa-solid fa-cash-register text-primary"></i> Record Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPaymentForm">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body modal-body-premium">
                    
                    {{-- 1. Amount to Settle --}}
                    <div class="mb-3">
                        <label class="pm-label">
                            <span>Amount to Settle <span class="text-danger">*</span></span>
                            <span class="text-muted fw-normal">
                                Due: <strong class="text-danger"><span id="modalBalanceHint">{{ number_format(max(0, $balanceDue), 2) }}</span></strong>
                            </span>
                        </label>
                        <div class="pm-input-wrap">
                            <input type="number" name="amount" id="paymentAmount" class="pm-input-control" step="0.01" 
                                   max="{{ max(0, $balanceDue) }}" required placeholder="0.00" autofocus>
                        </div>
                        
                        {{-- Quick Presets --}}
                        <div class="quick-presets-row">
                            <button type="button" class="preset-chip chip-full" id="btnFillFullBalance">
                                <i class="fa-solid fa-bolt me-1"></i> Full Balance (<span id="chipFullVal">{{ number_format(max(0, $balanceDue), 2) }}</span>)
                            </button>
                            @if($balanceDue > 100)
                                <button type="button" class="preset-chip" id="btnFillHalfBalance">50% Balance</button>
                            @endif
                            <button type="button" class="preset-chip btn-quick-amt" data-amt="100">100</button>
                            <button type="button" class="preset-chip btn-quick-amt" data-amt="500">500</button>
                            <button type="button" class="preset-chip btn-quick-amt" data-amt="1000">1000</button>
                        </div>
                    </div>

                    {{-- 2. Visual Payment Mode Cards --}}
                    <div class="mb-3">
                        <label class="pm-label">Select Payment Mode <span class="text-danger">*</span></label>
                        <div class="payment-method-selector">
                            <label>
                                <input type="radio" name="payment_method" value="CASH" class="payment-mode-radio" checked>
                                <div class="payment-mode-card">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                    <span>Cash</span>
                                </div>
                            </label>
                            <label>
                                <input type="radio" name="payment_method" value="UPI" class="payment-mode-radio">
                                <div class="payment-mode-card">
                                    <i class="fa-solid fa-qrcode"></i>
                                    <span>UPI / QR</span>
                                </div>
                            </label>
                            <label>
                                <input type="radio" name="payment_method" value="CARD" class="payment-mode-radio">
                                <div class="payment-mode-card">
                                    <i class="fa-solid fa-credit-card"></i>
                                    <span>Card</span>
                                </div>
                            </label>
                            <label>
                                <input type="radio" name="payment_method" value="BANK_TRANSFER" class="payment-mode-radio">
                                <div class="payment-mode-card">
                                    <i class="fa-solid fa-building-columns"></i>
                                    <span>Bank</span>
                                </div>
                            </label>
                            <label>
                                <input type="radio" name="payment_method" value="OTHER" class="payment-mode-radio">
                                <div class="payment-mode-card">
                                    <i class="fa-solid fa-wallet"></i>
                                    <span>Other</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- 3. Transaction No --}}
                    <div class="mb-3">
                        <label class="pm-label">Transaction / Reference ID <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" name="transaction_no" id="transactionNo" class="pm-input-control" placeholder="e.g. UPI Ref / UTR / TXN-9988">
                    </div>

                    {{-- 4. Remarks --}}
                    <div>
                        <label class="pm-label">Remarks / Cashier Notes <span class="text-muted fw-normal">(Optional)</span></label>
                        <textarea name="remarks" id="remarks" class="pm-input-control" rows="2" placeholder="e.g. Split bill / Partial advance / Customer note"></textarea>
                    </div>
                </div>

                <div class="modal-footer modal-footer-premium">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-inv-primary" id="submitPaymentBtn">
                        <i class="fa-solid fa-check-circle"></i> Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Share Bill WhatsApp Modal (Bootstrap 5) -->
<div class="modal fade" id="shareWhatsappModal" tabindex="-1" aria-labelledby="shareWhatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-premium">
            <div class="modal-header modal-header-premium" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 60%, #047857 100%); border-bottom-color: #25D366;">
                <h5 class="modal-title" id="shareWhatsappModalLabel">
                    <i class="fa-brands fa-whatsapp text-success fs-4"></i> Share Bill on WhatsApp
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-premium">
                
                {{-- 1. Phone input --}}
                <div class="mb-3">
                    <label class="pm-label">
                        <span>Recipient WhatsApp Number <span class="text-danger">*</span></span>
                        <span class="text-muted fw-normal">Include country code if outside India</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-dark font-monospace" style="border: 1.5px solid var(--inv-border); border-right: none; border-radius: 12px 0 0 12px;">+91</span>
                        <input type="tel" id="whatsappPhone" class="pm-input-control font-monospace fw-bold" style="border-radius: 0 12px 12px 0;"
                               value="{{ preg_replace('/^\+?91/', '', preg_replace('/[^0-9]/', '', $order->customer_phone ?? '')) }}" 
                               placeholder="e.g. 9876543210" maxlength="15">
                    </div>
                </div>

                {{-- 2. Invoice Link & Copy --}}
                <div class="mb-3">
                    <label class="pm-label">
                        <span>Invoice Download Link (Outside Auth)</span>
                        <a href="{{ route('order.public.invoice', $order->id) }}" target="_blank" class="small text-primary text-decoration-none fw-bold">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Public View
                        </a>
                    </label>
                    <div class="input-group">
                        <input type="text" id="invoiceShareUrl" class="pm-input-control font-monospace small" 
                               value="{{ route('order.public.download', $order->id) }}" readonly style="border-radius: 12px 0 0 12px;">
                        <button class="btn btn-outline-secondary px-3" type="button" id="btnCopyInvoiceLink" title="Copy Link" style="border-radius: 0 12px 12px 0; border: 1.5px solid var(--inv-border); border-left: none;">
                            <i class="fa-regular fa-copy"></i> Copy
                        </button>
                    </div>
                </div>

                {{-- 3. Message Preview --}}
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="pm-label mb-0">WhatsApp Message Preview</label>
                        <button type="button" class="btn btn-link p-0 text-decoration-none small text-success fw-bold" id="btnCopyWhatsappMsg">
                            <i class="fa-solid fa-copy me-1"></i> Copy Text
                        </button>
                    </div>
                    <textarea id="whatsappMsgPreview" class="pm-input-control font-monospace small" rows="8" readonly style="background: #f8fafc; resize: none; font-size: 0.8rem; line-height: 1.45;"></textarea>
                </div>
            </div>

            <div class="modal-footer modal-footer-premium">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-inv-whatsapp" id="btnSendWhatsapp">
                    <i class="fa-brands fa-whatsapp fs-5"></i> Open in WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')

<script>
let currentBalanceDue = {{ max(0, $balanceDue) }};
let paymentModalInstance = null;
let whatsappModalInstance = null;

function showToast(message, isError = false) {
    let toastClass = isError ? 'toast-error' : 'toast-success';
    let icon = isError ? 'fa-circle-exclamation' : 'fa-circle-check';
    let toast = $(`<div class="toast-float ${toastClass}"><i class="fa-solid ${icon}"></i> <span>${message}</span></div>`);
    $('body').append(toast);
    setTimeout(() => {
        toast.fadeOut(300, function() { $(this).remove(); });
    }, 3500);
}

function openModal() {
    if (currentBalanceDue > 0) {
        $('#paymentAmount').val(currentBalanceDue.toFixed(2));
    } else {
        $('#paymentAmount').val('');
    }
    
    const modalEl = document.getElementById('addPaymentModal');
    if (!paymentModalInstance) {
        paymentModalInstance = new bootstrap.Modal(modalEl);
    }
    paymentModalInstance.show();
}

function closeModal() {
    if (paymentModalInstance) {
        paymentModalInstance.hide();
    }
    $('#addPaymentForm')[0].reset();
    $('input[name="payment_method"][value="CASH"]').prop('checked', true);
}

function generateWhatsappMessage() {
    let restaurantName = "{{ $order->restaurant->name ?? config('app.name', 'Restaurant') }}";
    let customerName = "{{ $order->customer_name ?: 'Valued Guest' }}";
    let invoiceNo = "#{{ $order->order_id ?? $order->id }}";
    let orderDate = "{{ $order->created_at->format('d M Y, h:i A') }}";
    let orderType = "{{ $order->order_type == 'DINE_IN' ? 'Dine In (' . (@$order->table->name ?? 'Table') . ')' : 'Takeaway' }}";
    let grandTotal = "{{ number_format($order->grand_total, 2) }}";
    let paidAmount = "" + parseFloat($('#totalPaidAmount').text() || '{{ $totalPaid }}').toFixed(2);
    let balanceVal = parseFloat($('#balanceDueAmount').text() || '{{ $balanceDue }}');
    let downloadUrl = "{{ route('order.public.download', $order->id) }}";
    let viewUrl = "{{ route('order.public.invoice', $order->id) }}";
    
    let msg = `🧾 *${restaurantName} - Bill & Receipt*\n`;
    msg += `--------------------------------\n`;
    msg += `👤 *Customer:* ${customerName}\n`;
    msg += `🆔 *Invoice No:* ${invoiceNo}\n`;
    msg += `📅 *Date:* ${orderDate}\n`;
    msg += `🍽️ *Type:* ${orderType}\n\n`;
    
    msg += `📋 *Ordered Items:*\n`;
    @if($order->orderItems && count($order->orderItems) > 0)
        @foreach($order->orderItems as $item)
            msg += `• {{ $item->quantity }}x {{ addslashes($item->subcategory->name ?? 'Item') }} - {{ number_format($item->total_amount ?? ($item->price * $item->quantity), 2) }}\n`;
        @endforeach
    @endif
    
    msg += `\n--------------------------------\n`;
    msg += `💰 *Grand Total:* ${grandTotal}\n`;
    msg += `✅ *Paid Amount:* ${paidAmount}\n`;
    if (balanceVal > 0) {
        msg += `⚠️ *Balance Due:* ${balanceVal.toFixed(2)}\n`;
    } else {
        msg += `✨ *Status:* Fully Paid & Settled\n`;
    }
    msg += `--------------------------------\n`;
    msg += `📄 *Download Bill PDF:*\n${downloadUrl}\n\n`;
    msg += `🌐 *View Online Bill:*\n${viewUrl}\n\n`;
    msg += `🙏 Thank you for dining with us! Have a wonderful day.`;
    
    return msg;
}

let autoPrintTimer = null;

function openWhatsappModal() {
    // Cancel any pending print triggers and clean background print iframes
    if (autoPrintTimer) {
        clearTimeout(autoPrintTimer);
        autoPrintTimer = null;
    }
    $('#pdfFrame').remove();

    let msg = generateWhatsappMessage();
    $('#whatsappMsgPreview').val(msg);

    const modalEl = document.getElementById('shareWhatsappModal');
    if (!whatsappModalInstance) {
        whatsappModalInstance = new bootstrap.Modal(modalEl);
    }
    whatsappModalInstance.show();
}

function getMethodClass(method) {
    const map = {
        'CASH': 'cash',
        'UPI': 'upi',
        'CARD': 'card',
        'BANK_TRANSFER': 'bank_transfer',
        'OTHER': 'other'
    };
    return map[method] || 'other';
}

function getMethodIcon(method) {
    let m = (method || '').toLowerCase();
    if (m.includes('cash')) return '<i class="fa-solid fa-money-bill-wave me-1"></i>';
    if (m.includes('upi')) return '<i class="fa-solid fa-qrcode me-1"></i>';
    if (m.includes('card')) return '<i class="fa-solid fa-credit-card me-1"></i>';
    if (m.includes('bank')) return '<i class="fa-solid fa-building-columns me-1"></i>';
    return '<i class="fa-solid fa-wallet me-1"></i>';
}

function refreshPaymentsTable() {
    $.ajax({
        url: "{{ route('order.get.payments', $order->id) }}",
        type: "GET",
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let tbody = $('#paymentTableBody');
                tbody.empty();

                if (response.payments.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-credit-card fa-2x mb-2 d-block text-muted opacity-50"></i>
                                No payments recorded yet for this order.
                            </td>
                        </tr>
                    `);
                } else {
                    $.each(response.payments, function(index, payment) {
                        let methodClass = getMethodClass(payment.payment_method);
                        let methodIcon = getMethodIcon(payment.payment_method);
                        let paymentDate = new Date(payment.payment_date || payment.created_at);
                        let dateStr = paymentDate.toLocaleString('en-IN', {
                            day: '2-digit', month: 'short', year: 'numeric',
                            hour: '2-digit', minute: '2-digit', hour12: true
                        });

                        tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td class="text-muted font-monospace small">${dateStr}</td>
                                <td class="text-end fw-extrabold text-success" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">
                                    ${parseFloat(payment.amount).toFixed(2)}
                                </td>
                                <td>
                                    <span class="payment-method-badge method-${methodClass}">
                                        ${methodIcon}
                                        ${payment.payment_method}
                                    </span>
                                </td>
                                <td class="font-monospace small text-dark">${payment.transaction_no || '-'}</td>
                                <td class="text-muted small">${payment.remarks || '-'}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-payment rounded-circle"
                                            style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                            data-id="${payment.id}"
                                            data-amount="${payment.amount}"
                                            title="Delete Payment Record">
                                        <i class="fa-solid fa-trash-can" style="font-size: 0.75rem;"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                }

                // Update summary amounts
                let totalPaid = parseFloat(response.total_paid || 0);
                let balanceDue = parseFloat(response.balance_due || 0);
                currentBalanceDue = Math.max(0, balanceDue);

                $('#totalPaidFooter').text(totalPaid.toFixed(2));
                $('#totalPaidAmount').text(totalPaid.toFixed(2));
                $('#balanceDueAmount').text(currentBalanceDue.toFixed(2));
                $('#modalBalanceHint').text(currentBalanceDue.toFixed(2));
                $('#chipFullVal').text(currentBalanceDue.toFixed(2));

                // Update column styling for balance due
                let dueCol = $('.inv-fin-col.col-due');
                let dueVal = $('.inv-fin-val.text-due');
                if (balanceDue <= 0) {
                    dueCol.addClass('settled');
                    dueVal.addClass('settled');
                } else {
                    dueCol.removeClass('settled');
                    dueVal.removeClass('settled');
                }

                // Update status badge
                let statusHtml = '';
                if (balanceDue <= 0) {
                    statusHtml = '<span class="inv-status-pill status-paid"><i class="fa-solid fa-circle-check"></i> FULLY SETTLED & PAID</span>';
                } else if (totalPaid > 0) {
                    statusHtml = '<span class="inv-status-pill status-partial"><i class="fa-solid fa-clock"></i> PARTIAL PAYMENT RECEIVED</span>';
                } else {
                    statusHtml = '<span class="inv-status-pill status-pending"><i class="fa-solid fa-circle-exclamation"></i> PAYMENT PENDING</span>';
                }
                $('#paymentStatusBadge').html(statusHtml);

                // Update modal max limit
                $('#paymentAmount').attr('max', currentBalanceDue);
            }
        },
        error: function(xhr) {
            console.error('Error refreshing payments:', xhr);
            showToast('Error refreshing payments', true);
        }
    });
}

$(document).ready(function() {
    // Show Payment Modal Button
    $('#showAddPaymentModal').click(function() { 
        openModal(); 
    });

    // Show WhatsApp Modal Button
    $('#showWhatsappModalBtn').click(function() {
        openWhatsappModal();
    });

    // Copy Invoice Link
    $('#btnCopyInvoiceLink').click(function() {
        let link = $('#invoiceShareUrl').val();
        navigator.clipboard.writeText(link).then(function() {
            showToast('Invoice link copied to clipboard!');
        }).catch(function() {
            let temp = $('<input>').val(link).appendTo('body').select();
            document.execCommand('copy');
            temp.remove();
            showToast('Invoice link copied to clipboard!');
        });
    });

    // Copy WhatsApp Message Text
    $('#btnCopyWhatsappMsg').click(function() {
        let msg = $('#whatsappMsgPreview').val();
        navigator.clipboard.writeText(msg).then(function() {
            showToast('WhatsApp message copied to clipboard!');
        }).catch(function() {
            let temp = $('<textarea>').val(msg).appendTo('body').select();
            document.execCommand('copy');
            temp.remove();
            showToast('WhatsApp message copied to clipboard!');
        });
    });

    // Send on WhatsApp Button
    $('#btnSendWhatsapp').click(function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Ensure no print dialog or timer fires
        if (autoPrintTimer) {
            clearTimeout(autoPrintTimer);
            autoPrintTimer = null;
        }
        $('#pdfFrame').remove();

        let phoneInput = $('#whatsappPhone').val().trim();
        let cleanPhone = phoneInput.replace(/[^0-9]/g, '');
        
        // If 10 digits without country code, add 91
        if (cleanPhone.length === 10) {
            cleanPhone = '91' + cleanPhone;
        }

        let msg = $('#whatsappMsgPreview').val();
        let encodedMsg = encodeURIComponent(msg);
        
        let waUrl = '';
        if (cleanPhone.length >= 10) {
            waUrl = `https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodedMsg}`;
        } else {
            waUrl = `https://api.whatsapp.com/send?text=${encodedMsg}`;
        }

        window.open(waUrl, '_blank');
        showToast('Opening WhatsApp...');
    });

    // Preset Chip Clicks
    $('#btnFillFullBalance').click(function() {
        $('#paymentAmount').val(currentBalanceDue.toFixed(2)).focus();
    });

    $('#btnFillHalfBalance').click(function() {
        let half = (currentBalanceDue / 2).toFixed(2);
        $('#paymentAmount').val(half).focus();
    });

    $('.btn-quick-amt').click(function() {
        let amt = parseFloat($(this).data('amt'));
        if (currentBalanceDue > 0 && amt > currentBalanceDue) {
            $('#paymentAmount').val(currentBalanceDue.toFixed(2)).focus();
        } else {
            $('#paymentAmount').val(amt.toFixed(2)).focus();
        }
    });

    // Add Payment AJAX Submit
    $('#addPaymentForm').on('submit', function(e) {
        e.preventDefault();

        let amount = parseFloat($('#paymentAmount').val());
        let paymentMethod = $('input[name="payment_method"]:checked').val();

        if (!amount || amount <= 0) { 
            showToast('Please enter a valid payment amount', true); 
            return; 
        }
        if (!paymentMethod) { 
            showToast('Please select a payment mode', true); 
            return; 
        }

        let submitBtn = $('#submitPaymentBtn');
        submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: "{{ route('order.add.payment', $order->id) }}",
            type: "POST",
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    closeModal();
                    refreshPaymentsTable();
                    showToast(response.message || 'Payment added successfully!');
                } else {
                    showToast(response.message || 'Failed to add payment', true);
                }
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Error adding payment', true);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fa-solid fa-check-circle"></i> Save Payment');
            }
        });
    });

    // Delete Payment AJAX
    $(document).on('click', '.delete-payment', function() {
        let paymentId = $(this).data('id');
        let amount = $(this).data('amount');

        if (confirm(`Delete payment of ${amount}? This action cannot be undone.`)) {
            let deleteBtn = $(this);
            deleteBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

            $.ajax({
                url: "{{ route('order.delete.payment', '') }}/" + paymentId,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        refreshPaymentsTable();
                        showToast(response.message || 'Payment deleted successfully');
                    } else {
                        showToast(response.message || 'Failed to delete payment', true);
                    }
                },
                error: function() {
                    showToast('Error deleting payment', true);
                    deleteBtn.prop('disabled', false).html('<i class="fa-solid fa-trash-can"></i>');
                }
            });
        }
    });

    // Print Invoice Button
    $('#printInvoiceBtn').click(function() {
        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Preparing...');

        let pdfUrl = "{{ route('order.receipt.pdf', $order->id) }}";
        
        $('#pdfFrame').remove();
        
        let iframe = $('<iframe>', {
            id: 'pdfFrame',
            src: pdfUrl,
            class: 'hidden-iframe'
        }).appendTo('body');

        iframe[0].onload = function() {
            btn.prop('disabled', false).html('<i class="fa-solid fa-print"></i> Print Invoice');
            try {
                iframe[0].contentWindow.focus();
                iframe[0].contentWindow.print();
            } catch (e) {
                window.open(pdfUrl, '_blank');
            }
        };

        setTimeout(function() {
            btn.prop('disabled', false).html('<i class="fa-solid fa-print"></i> Print Invoice');
        }, 5000);
    });

    @if(request('autoprint') == '1' || request('print') == '1' || request('auto_print') == '1')
    // Clean URL query parameters so print dialog doesn't re-trigger
    if (window.history.replaceState) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    autoPrintTimer = setTimeout(function() {
        // Only trigger if WhatsApp modal is not currently open
        if (!$('#shareWhatsappModal').hasClass('show')) {
            $('#printInvoiceBtn').trigger('click');
        }
    }, 600);
    @endif
});
</script>
</body>
</html>