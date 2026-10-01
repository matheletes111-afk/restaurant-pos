<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice #{{ $order->order_id ?? $order->id }} | {{ $order->customer_name ?: 'Guest' }}</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --inv-primary: #ff5e14;
            --inv-primary-hover: #ea580c;
            --inv-primary-gradient: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
            --inv-dark: #0f172a;
            --inv-slate: #1e293b;
            --inv-muted: #64748b;
            --inv-border: #e2e8f0;
            --inv-border-light: #f1f5f9;
            --inv-bg: #f8fafc;
            --inv-card: #ffffff;
            --inv-success: #10b981;
            --inv-success-bg: #ecfdf5;
            --inv-danger: #ef4444;
            --inv-danger-bg: #fef2f2;
            --inv-warning: #f59e0b;
            --inv-warning-bg: #fffbeb;
            --inv-info: #0284c7;
            --inv-info-bg: #f0f9ff;
            --inv-purple: #8b5cf6;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: var(--inv-slate);
            -webkit-font-smoothing: antialiased;
        }

        .inv-page-wrap {
            max-width: 1080px;
            margin: 0 auto;
            padding: 24px 16px;
        }

        /* Top Bar */
        .inv-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .inv-top-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .inv-order-badge {
            background: var(--inv-slate);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 30px;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }

        .inv-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-inv-primary {
            background: var(--inv-primary-gradient);
            color: #ffffff;
            border: none;
            padding: 10px 22px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(255, 94, 20, 0.28);
            transition: all 0.25s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-inv-primary:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(255, 94, 20, 0.38);
        }

        .btn-inv-slate {
            background: var(--inv-slate);
            color: #ffffff;
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
            color: #ffffff;
            transform: translateY(-2px);
        }

        .btn-inv-back {
            background: #ffffff;
            color: var(--inv-slate);
            border: 1px solid var(--inv-border);
            padding: 10px 18px;
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
            color: var(--inv-primary);
        }

        /* Invoice Container Card */
        .invoice-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--inv-border);
            box-shadow: 0 10px 35px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        /* Header Canvas */
        .invoice-header-canvas {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 32px 36px;
            position: relative;
            overflow: hidden;
        }

        .invoice-header-canvas::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(255, 94, 20, 0.2), transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .inv-brand-block {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .inv-brand-logo {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
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
            font-size: 1.55rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 4px 0;
            line-height: 1.2;
        }

        .inv-restaurant-meta {
            font-size: 0.82rem;
            color: #94a3b8;
            margin: 0;
            line-height: 1.45;
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
            letter-spacing: 0.1em;
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 8px;
            box-shadow: 0 2px 10px rgba(255, 94, 20, 0.35);
        }

        .inv-doc-number {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
        }

        .inv-doc-datetime {
            font-size: 0.82rem;
            color: #cbd5e1;
            margin-top: 4px;
        }

        /* Order Info Strip */
        .inv-meta-grid {
            background: #f8fafc;
            border-bottom: 1px solid var(--inv-border);
            padding: 24px 36px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
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
            margin-bottom: 4px;
        }

        .inv-meta-val {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--inv-dark);
        }

        /* Financial Health / Payment Summary Card */
        .inv-financial-deck {
            margin: 28px 36px 20px;
            background: #ffffff;
            border: 1px solid var(--inv-border);
            border-radius: 16px;
            padding: 20px 24px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        }

        .inv-fin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: center;
        }

        .inv-fin-col {
            text-align: center;
            padding: 10px 14px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid var(--inv-border-light);
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
            letter-spacing: 0.05em;
            color: var(--inv-muted);
            margin-bottom: 2px;
        }

        .inv-fin-val {
            font-family: 'Outfit', sans-serif;
            font-size: 1.7rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .inv-fin-val.text-total { color: var(--inv-dark); }
        .inv-fin-val.text-paid { color: var(--inv-success); }
        .inv-fin-val.text-due { color: var(--inv-danger); }
        .inv-fin-val.text-due.settled { color: var(--inv-success); }

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
            letter-spacing: 0.05em;
        }

        .inv-status-pill.status-paid {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .inv-status-pill.status-partial {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .inv-status-pill.status-pending {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Tables & Section Layout */
        .inv-section-wrap {
            padding: 10px 36px 28px;
        }

        .inv-section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--inv-dark);
            margin: 24px 0 14px;
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
            border: 1px solid var(--inv-border-light);
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
            border-bottom: 1px solid var(--inv-border);
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

        /* Calculation Summary Grid */
        .inv-calc-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .inv-calc-box {
            width: 360px;
            max-width: 100%;
            background: #f8fafc;
            border: 1px solid var(--inv-border);
            border-radius: 14px;
            padding: 16px 20px;
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
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--inv-dark);
            border-top: 2px dashed var(--inv-border);
            padding-top: 10px;
            margin-top: 10px;
            margin-bottom: 0;
        }

        /* Payment Badges */
        .payment-method-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.74rem;
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
            border: 1px solid var(--inv-border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            max-width: 480px;
            margin: 24px auto 0;
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

        /* Modal Enhancements */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1050;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-dialog {
            width: 100%;
            max-width: 480px;
            margin: 1.75rem auto;
        }

        .modal-content {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--inv-border);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
            overflow: hidden;
        }

        .modal-header {
            background: var(--inv-slate);
            color: #ffffff;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 16px 24px;
            background: #f8fafc;
            border-top: 1px solid var(--inv-border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--inv-slate);
            margin-bottom: 6px;
            display: block;
        }

        .form-control-custom {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--inv-border);
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            border-color: var(--inv-primary);
            box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.15);
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
            border-radius: 12px;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.88rem;
            z-index: 99999;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
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
                padding: 10px 20px 24px;
            }
            .inv-financial-deck {
                margin: 20px 20px 10px;
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
                        <i class="fas fa-receipt me-1"></i> #{{ $order->order_id ?? $order->id }}
                    </span>
                    <span class="text-muted small fw-bold">
                        <i class="far fa-clock me-1"></i> {{ $order->created_at->format('d M Y, h:i A') }}
                    </span>
                </div>

                <div class="inv-actions">
                    <button type="button" class="btn-inv-primary" id="showAddPaymentModal">
                        <i class="fas fa-plus-circle"></i> Add Payment
                    </button>
                    <button type="button" class="btn-inv-slate" id="printInvoiceBtn">
                        <i class="fas fa-print"></i> Print Invoice
                    </button>
                    <a href="{{ route('order.management.dashboard') }}" class="btn-inv-back">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
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
                                        <i class="fas fa-utensils"></i>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="inv-restaurant-name">{{ $order->restaurant->name ?? config('app.name', 'Restaurant') }}</h2>
                                    <p class="inv-restaurant-meta">
                                        @if(!empty($order->restaurant->address))
                                            <i class="fas fa-location-dot me-1"></i> {{ $order->restaurant->address }}
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
                                    <i class="fas fa-utensils me-1"></i> Dine In ({{ @$order->table->name ?? 'Table' }})
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                    <i class="fas fa-box me-1"></i> Takeaway
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
                            <div class="inv-fin-val text-total">₹{{ number_format($order->grand_total, 2) }}</div>
                        </div>

                        <div class="inv-fin-col col-paid">
                            <span class="inv-fin-label">Total Paid Amount</span>
                            <div class="inv-fin-val text-paid">₹<span id="totalPaidAmount">{{ number_format($totalPaid, 2) }}</span></div>
                        </div>

                        <div class="inv-fin-col col-due {{ $balanceDue <= 0 ? 'settled' : '' }}">
                            <span class="inv-fin-label">Remaining Balance</span>
                            <div class="inv-fin-val text-due {{ $balanceDue <= 0 ? 'settled' : '' }}">
                                ₹<span id="balanceDueAmount">{{ number_format(max(0, $balanceDue), 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="inv-status-pill-wrap" id="paymentStatusBadge">
                        @if($balanceDue <= 0)
                            <span class="inv-status-pill status-paid">
                                <i class="fas fa-check-circle"></i> FULLY SETTLED & PAID
                            </span>
                        @elseif($totalPaid > 0)
                            <span class="inv-status-pill status-partial">
                                <i class="fas fa-clock"></i> PARTIAL PAYMENT RECEIVED
                            </span>
                        @else
                            <span class="inv-status-pill status-pending">
                                <i class="fas fa-exclamation-circle"></i> PAYMENT PENDING
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Itemized Ordered Dishes Table --}}
                @if($order->orderItems && count($order->orderItems) > 0)
                <div class="inv-section-wrap">
                    <div class="inv-section-title">
                        <div class="title-text">
                            <i class="fas fa-bowl-food text-primary"></i>
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
                                    <th class="text-end">Total (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $idx => $item)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(@$item->subcategory->food_type == 'non-veg')
                                                <span class="food-type-dot nonveg" title="Non-Veg"><i class="fas fa-circle"></i></span>
                                            @else
                                                <span class="food-type-dot veg" title="Veg"><i class="fas fa-circle"></i></span>
                                            @endif
                                            <div>
                                                <strong class="text-dark">{{ $item->subcategory->name ?? 'Custom Item' }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-center text-muted small">
                                        {{ $item->item_discount_percentage > 0 ? $item->item_discount_percentage . '%' : '-' }}
                                    </td>
                                    <td class="text-end">₹{{ number_format($item->taxable_amount ?? ($item->price * $item->quantity), 2) }}</td>
                                    <td class="text-end text-muted">₹{{ number_format($item->gst_amount ?? 0, 2) }}</td>
                                    <td class="text-end fw-bold text-dark">₹{{ number_format($item->total_amount ?? ($item->price * $item->quantity), 2) }}</td>
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
                                <span class="fw-semibold">₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>

                            @if($order->discount > 0)
                            <div class="inv-calc-row text-danger">
                                <span>Order Discount ({{ $order->discount_percentage ?? 0 }}%):</span>
                                <span class="fw-semibold">- ₹{{ number_format($order->discount, 2) }}</span>
                            </div>
                            @endif

                            @if($order->is_gst_bill == 'YES' && $order->gst_amount > 0)
                            <div class="inv-calc-row text-primary">
                                <span>GST ({{ $order->restaurant_gst_percentage ?? 0 }}%):</span>
                                <span class="fw-semibold">+ ₹{{ number_format($order->gst_amount, 2) }}</span>
                            </div>
                            @endif

                            @if($order->round_off != 0)
                            <div class="inv-calc-row text-muted small">
                                <span>Round Off:</span>
                                <span>{{ $order->round_off > 0 ? '+' : '' }}₹{{ number_format($order->round_off, 2) }}</span>
                            </div>
                            @endif

                            <div class="inv-calc-row grand-total">
                                <span>Grand Total:</span>
                                <span class="text-primary">₹{{ number_format($order->grand_total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Payment History Ledger Section --}}
                <div class="inv-section-wrap" style="border-top: 1px solid var(--inv-border-light);">
                    <div class="inv-section-title">
                        <div class="title-text">
                            <i class="fas fa-history text-success"></i>
                            <span>Payment Transaction History</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="inv-table" id="paymentTable">
                            <thead>
                                <tr>
                                    <th width="40">#</th>
                                    <th>Date & Time</th>
                                    <th class="text-end">Amount (₹)</th>
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
                                        ₹{{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="payment-method-badge method-{{ strtolower(str_replace(' ', '_', $payment->payment_method)) }}">
                                            @if(str_contains(strtolower($payment->payment_method), 'cash'))
                                                <i class="fas fa-money-bill-wave me-1"></i>
                                            @elseif(str_contains(strtolower($payment->payment_method), 'upi'))
                                                <i class="fas fa-qrcode me-1"></i>
                                            @elseif(str_contains(strtolower($payment->payment_method), 'card'))
                                                <i class="fas fa-credit-card me-1"></i>
                                            @else
                                                <i class="fas fa-wallet me-1"></i>
                                            @endif
                                            {{ $payment->payment_method }}
                                        </span>
                                    </td>
                                    <td class="font-monospace small text-dark">{{ $payment->transaction_no ?: '-' }}</td>
                                    <td class="text-muted small">{{ $payment->remarks ?: '-' }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-payment rounded-circle" 
                                                style="width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                                data-id="{{ $payment->id }}"
                                                data-amount="{{ $payment->amount }}"
                                                title="Delete Payment Record">
                                            <i class="fas fa-trash-can" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-credit-card fa-2x mb-2 d-block text-muted opacity-50"></i>
                                        No payments recorded yet for this order.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total Realized Payments:</th>
                                    <th class="text-end text-success fw-bold" id="totalPaidFooter">₹{{ number_format($totalPaid, 2) }}</th>
                                    <th colspan="4"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- QR Code Scan to Pay Section --}}
                @if(!empty($order->restaurant->qr_code_image) || !empty($order->restaurant->upi_id))
                <div class="p-4" style="background: #fafbfc; border-top: 1px solid var(--inv-border);">
                    <div class="inv-qr-card">
                        @if(!empty($order->restaurant->qr_code_image))
                            <div class="inv-qr-img">
                                <img src="{{ asset('storage/' . $order->restaurant->qr_code_image) }}" alt="QR Code">
                            </div>
                        @endif
                        <div>
                            <span class="badge bg-primary-subtle text-primary fw-bold mb-1 px-2 py-1">
                                <i class="fas fa-bolt me-1"></i> Instant UPI Payment
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
                <div class="p-4 text-center text-muted small" style="background: #ffffff; border-top: 1px solid var(--inv-border-light);">
                    <p class="mb-0 fw-semibold text-secondary">
                        <i class="fas fa-heart text-danger me-1"></i> Thank you for dining with {{ $order->restaurant->name ?? 'us' }}! Please visit us again.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div id="addPaymentModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-circle-plus text-primary"></i> Record Order Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" id="closeModalBtn" aria-label="Close"></button>
            </div>
            <form id="addPaymentForm">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label-custom mb-0">Amount to Settle (₹) <span class="text-danger">*</span></label>
                            <span class="badge bg-light text-muted border">
                                Balance: ₹<span id="modalBalanceHint">{{ number_format(max(0, $balanceDue), 2) }}</span>
                            </span>
                        </div>
                        <input type="number" name="amount" id="paymentAmount" class="form-control-custom font-monospace fs-5 fw-bold" step="0.01" 
                               max="{{ max(0, $balanceDue) }}" required placeholder="0.00">
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Payment Mode <span class="text-danger">*</span></label>
                        <select name="payment_method" id="paymentMethod" class="form-control-custom" required>
                            <option value="">-- Choose Payment Mode --</option>
                            <option value="CASH">Cash</option>
                            <option value="UPI">UPI / QR Code</option>
                            <option value="CARD">Debit / Credit Card</option>
                            <option value="BANK_TRANSFER">Bank Transfer / NEFT</option>
                            <option value="OTHER">Other / Wallets</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Transaction / Reference ID (Optional)</label>
                        <input type="text" name="transaction_no" id="transactionNo" class="form-control-custom" placeholder="e.g. UPI Ref / UTR / TXN-9988">
                    </div>

                    <div class="mb-2">
                        <label class="form-label-custom">Remarks / Notes</label>
                        <textarea name="remarks" id="remarks" class="form-control-custom" rows="2" placeholder="Optional cashier notes"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-3 fw-bold" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn-inv-primary" id="submitPaymentBtn">
                        <i class="fas fa-check-circle"></i> Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
function showToast(message, isError = false) {
    let toastClass = isError ? 'toast-error' : 'toast-success';
    let icon = isError ? 'fa-circle-exclamation' : 'fa-circle-check';
    let toast = $(`<div class="toast-float ${toastClass}"><i class="fas ${icon}"></i> <span>${message}</span></div>`);
    $('body').append(toast);
    setTimeout(() => {
        toast.fadeOut(300, function() { $(this).remove(); });
    }, 3500);
}

function openModal() {
    let balanceDue = parseFloat($('#paymentAmount').attr('max')) || 0;
    if (balanceDue > 0) {
        $('#paymentAmount').val(balanceDue.toFixed(2));
    } else {
        $('#paymentAmount').val('');
    }
    $('#addPaymentModal').addClass('show');
    $('body').css('overflow', 'hidden');
}

function closeModal() {
    $('#addPaymentModal').removeClass('show');
    $('body').css('overflow', 'auto');
    $('#addPaymentForm')[0].reset();
    $('#paymentMethod').val('');
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
                                <i class="fas fa-credit-card fa-2x mb-2 d-block text-muted opacity-50"></i>
                                No payments recorded yet for this order.
                            </td>
                        </tr>
                    `);
                } else {
                    $.each(response.payments, function(index, payment) {
                        let methodClass = getMethodClass(payment.payment_method);
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
                                    ₹${parseFloat(payment.amount).toFixed(2)}
                                </td>
                                <td>
                                    <span class="payment-method-badge method-${methodClass}">
                                        ${payment.payment_method}
                                    </span>
                                </td>
                                <td class="font-monospace small text-dark">${payment.transaction_no || '-'}</td>
                                <td class="text-muted small">${payment.remarks || '-'}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-payment rounded-circle"
                                            style="width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                            data-id="${payment.id}"
                                            data-amount="${payment.amount}"
                                            title="Delete Payment Record">
                                        <i class="fas fa-trash-can" style="font-size: 0.75rem;"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                }

                // Update summary amounts
                let totalPaid = parseFloat(response.total_paid || 0);
                let balanceDue = parseFloat(response.balance_due || 0);

                $('#totalPaidFooter').text(`₹${totalPaid.toFixed(2)}`);
                $('#totalPaidAmount').text(totalPaid.toFixed(2));
                $('#balanceDueAmount').text(Math.max(0, balanceDue).toFixed(2));
                $('#modalBalanceHint').text(Math.max(0, balanceDue).toFixed(2));

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
                    statusHtml = '<span class="inv-status-pill status-paid"><i class="fas fa-check-circle"></i> FULLY SETTLED & PAID</span>';
                } else if (totalPaid > 0) {
                    statusHtml = '<span class="inv-status-pill status-partial"><i class="fas fa-clock"></i> PARTIAL PAYMENT RECEIVED</span>';
                } else {
                    statusHtml = '<span class="inv-status-pill status-pending"><i class="fas fa-exclamation-circle"></i> PAYMENT PENDING</span>';
                }
                $('#paymentStatusBadge').html(statusHtml);

                // Update modal max limit
                $('#paymentAmount').attr('max', Math.max(0, balanceDue));
            }
        },
        error: function(xhr) {
            console.error('Error refreshing payments:', xhr);
            showToast('Error refreshing payments', true);
        }
    });
}

$(document).ready(function() {
    $('#showAddPaymentModal').click(function() { openModal(); });
    $('#closeModalBtn, #cancelModalBtn').click(function() { closeModal(); });

    $('#addPaymentModal').click(function(e) {
        if (e.target === this) { closeModal(); }
    });

    // Add Payment AJAX
    $('#addPaymentForm').on('submit', function(e) {
        e.preventDefault();

        let amount = $('#paymentAmount').val();
        let paymentMethod = $('#paymentMethod').val();

        if (!amount || amount <= 0) { showToast('Please enter a valid amount', true); return; }
        if (!paymentMethod) { showToast('Please select payment method', true); return; }

        let submitBtn = $('#submitPaymentBtn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

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
                submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Save Payment');
            }
        });
    });

    // Delete Payment AJAX
    $(document).on('click', '.delete-payment', function() {
        let paymentId = $(this).data('id');
        let amount = $(this).data('amount');

        if (confirm(`Delete payment of ₹${amount}? This action cannot be undone.`)) {
            let deleteBtn = $(this);
            deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

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
                    deleteBtn.prop('disabled', false).html('<i class="fas fa-trash-can"></i>');
                }
            });
        }
    });

    // Print Invoice Button
    $('#printInvoiceBtn').click(function() {
        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Preparing...');

        let pdfUrl = "{{ route('order.receipt.pdf', $order->id) }}";
        
        $('#pdfFrame').remove();
        
        let iframe = $('<iframe>', {
            id: 'pdfFrame',
            src: pdfUrl,
            class: 'hidden-iframe'
        }).appendTo('body');

        iframe[0].onload = function() {
            btn.prop('disabled', false).html('<i class="fas fa-print"></i> Print Invoice');
            try {
                iframe[0].contentWindow.focus();
                iframe[0].contentWindow.print();
            } catch (e) {
                window.open(pdfUrl, '_blank');
            }
        };

        setTimeout(function() {
            btn.prop('disabled', false).html('<i class="fas fa-print"></i> Print Invoice');
        }, 5000);
    });
});
</script>
</body>
</html>