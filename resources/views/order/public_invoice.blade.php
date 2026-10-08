<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bill & Receipt #{{ $order->order_id ?? $order->id }} • {{ $order->restaurant->name ?? config('app.name', 'Restaurant') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --pub-primary: #ff5e14;
            --pub-primary-dark: #e04a08;
            --pub-primary-gradient: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
            --pub-dark: #0f172a;
            --pub-slate: #1e293b;
            --pub-muted: #64748b;
            --pub-border: #e2e8f0;
            --pub-card: #ffffff;
            --pub-success: #10b981;
            --pub-success-bg: #ecfdf5;
            --pub-warning: #f59e0b;
            --pub-warning-bg: #fffbeb;
            --pub-radius: 20px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: var(--pub-slate);
            -webkit-font-smoothing: antialiased;
            padding: 20px 12px 60px;
        }

        .pub-invoice-container {
            max-width: 680px;
            margin: 0 auto;
        }

        /* Floating Top Bar */
        .pub-top-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn-pub-action {
            background: #ffffff;
            border: 1.5px solid var(--pub-border);
            color: var(--pub-dark);
            padding: 9px 18px;
            border-radius: 30px;
            font-size: 0.88rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-pub-action:hover {
            border-color: var(--pub-primary);
            color: var(--pub-primary);
            transform: translateY(-1px);
        }

        .btn-pub-download {
            background: var(--pub-primary-gradient);
            color: #ffffff !important;
            border: none;
            box-shadow: 0 4px 14px rgba(255, 94, 20, 0.28);
        }

        .btn-pub-download:hover {
            box-shadow: 0 6px 20px rgba(255, 94, 20, 0.38);
            transform: translateY(-2px);
        }

        /* Invoice Card */
        .pub-card {
            background: #ffffff;
            border-radius: var(--pub-radius);
            border: 1.5px solid var(--pub-border);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        /* Header */
        .pub-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 70%, #0f172a 100%);
            color: #ffffff;
            padding: 28px 26px;
            position: relative;
            border-bottom: 4px solid var(--pub-primary);
        }

        .pub-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .pub-brand-logo {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #ffffff;
            overflow: hidden;
            flex-shrink: 0;
        }

        .pub-brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pub-restaurant-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 4px;
            color: #ffffff;
        }

        .pub-restaurant-address {
            font-size: 0.8rem;
            color: #94a3b8;
            line-height: 1.4;
            margin-bottom: 0;
        }

        /* Meta Banner */
        .pub-meta-bar {
            background: #f8fafc;
            padding: 16px 24px;
            border-bottom: 1.5px solid var(--pub-border);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
        }

        .pub-meta-item .label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--pub-muted);
            font-weight: 700;
            display: block;
        }

        .pub-meta-item .val {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--pub-dark);
        }

        /* Items Section */
        .pub-body {
            padding: 24px;
        }

        .pub-section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--pub-dark);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pub-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pub-table th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--pub-muted);
            font-weight: 800;
            border-bottom: 2px solid var(--pub-border);
            padding: 8px 6px;
        }

        .pub-table td {
            padding: 12px 6px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
            vertical-align: middle;
        }

        .food-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .food-dot.veg { background: #10b981; }
        .food-dot.nonveg { background: #ef4444; }

        .pub-item-addons-box {
            margin-top: 4px;
            background: #fffcf8;
            border: 1px solid #fed7aa;
            border-left: 2.5px solid #ff5e14;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 0.75rem;
            display: inline-block;
        }

        .pub-addon-line {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #334155;
            margin-top: 2px;
        }

        .pub-addon-badge {
            background: #ffedd5;
            color: #c2410c;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 1px 4px;
            border-radius: 4px;
        }

        /* Calculation Box */
        .pub-calc-box {
            background: #f8fafc;
            border: 1.5px solid var(--pub-border);
            border-radius: 14px;
            padding: 18px;
            margin-top: 20px;
        }

        .pub-calc-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.88rem;
            margin-bottom: 8px;
            color: var(--pub-slate);
        }

        .pub-calc-row.grand {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--pub-dark);
            border-top: 2px dashed var(--pub-border);
            padding-top: 12px;
            margin-top: 10px;
            margin-bottom: 0;
        }

        /* Status Deck */
        .pub-status-deck {
            background: #ffffff;
            border: 1.5px solid var(--pub-border);
            border-radius: 14px;
            padding: 16px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .pub-status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
        }

        .badge-settled {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .badge-due {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        /* QR Payment */
        .pub-qr-card {
            background: #f8fafc;
            border: 1.5px dashed #cbd5e1;
            border-radius: 14px;
            padding: 16px;
            margin-top: 20px;
            text-align: center;
        }

        .pub-footer {
            text-align: center;
            padding: 20px;
            font-size: 0.82rem;
            color: var(--pub-muted);
            border-top: 1.5px solid #f1f5f9;
        }

        @media print {
            .pub-top-actions, .pub-footer-btn-wrap {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .pub-card {
                box-shadow: none !important;
                border: none !important;
            }
            .pub-header {
                background: #1e293b !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="pub-invoice-container">

    <!-- Top Action Bar -->
    <div class="pub-top-actions">
        <div>
            <span class="badge bg-dark text-white px-3 py-2 rounded-pill font-monospace fw-bold">
                <i class="fa-solid fa-receipt text-warning me-1"></i> #{{ $order->order_id ?? $order->id }}
            </span>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('order.public.download', ['id' => $order->id, 'download' => 1]) }}" class="btn-pub-action btn-pub-download">
                <i class="fa-solid fa-file-arrow-down"></i> Download PDF
            </a>
            <button type="button" class="btn-pub-action" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Main Card -->
    <div class="pub-card">
        
        <!-- Header -->
        <div class="pub-header">
            <div class="pub-brand">
                <div class="pub-brand-logo">
                    @if(!empty($order->restaurant->logo))
                        <img src="{{ asset('storage/' . $order->restaurant->logo) }}" alt="Logo">
                    @else
                        <i class="fa-solid fa-utensils"></i>
                    @endif
                </div>
                <div>
                    <h1 class="pub-restaurant-name">{{ $order->restaurant->name ?? config('app.name', 'Restaurant') }}</h1>
                    <p class="pub-restaurant-address">
                        @if(!empty($order->restaurant->address))
                            <i class="fa-solid fa-location-dot me-1"></i> {{ $order->restaurant->address }}
                            @if(!empty($order->restaurant->pincode)) - {{ $order->restaurant->pincode }} @endif
                            <br>
                        @endif
                        @if(!empty($order->restaurant->phone_number))
                            <i class="fa-solid fa-phone me-1"></i> {{ $order->restaurant->phone_number }}
                        @endif
                        @if(!empty($order->restaurant->gstin))
                            | <strong>GSTIN:</strong> {{ $order->restaurant->gstin }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Meta Bar -->
        <div class="pub-meta-bar">
            <div class="pub-meta-item">
                <span class="label">Date &amp; Time</span>
                <span class="val">{{ $order->created_at->format('d M Y, h:i A') }}</span>
            </div>
            <div class="pub-meta-item">
                <span class="label">Customer</span>
                <span class="val">{{ $order->customer_name ?: 'Walk-in Guest' }}</span>
            </div>
            <div class="pub-meta-item">
                <span class="label">Order Type</span>
                <span class="val">
                    @if($order->order_type == 'DINE_IN')
                        <i class="fa-solid fa-chair text-primary me-1"></i> Dine In ({{ @$order->table->name ?? 'Table' }})
                    @else
                        <i class="fa-solid fa-bag-shopping text-info me-1"></i> Takeaway
                    @endif
                </span>
            </div>
            <div class="pub-meta-item">
                <span class="label">Invoice Type</span>
                <span class="val">{{ $order->is_gst_bill == 'YES' ? 'Tax Invoice' : 'Retail Bill' }}</span>
            </div>
        </div>

        <!-- Body -->
        <div class="pub-body">
            
            <div class="pub-section-title">
                <i class="fa-solid fa-bowl-food text-primary"></i>
                <span>Ordered Items</span>
            </div>

            @if($order->orderItems && count($order->orderItems) > 0)
            <div class="table-responsive">
                <table class="pub-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-center" width="60">Qty</th>
                            <th class="text-end" width="90">Price</th>
                            <th class="text-end" width="100">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $pubOriginalSubtotal = 0;
                            $pubTaxableTotal = 0;
                            $pubGstTotal = 0;
                            $pubItemDiscountTotal = 0;
                        @endphp
                        @foreach($order->orderItems as $item)
                        @php
                            $addons = $item->addons_list ?? [];
                            $isAddonItem = empty($item->subcategory_id);
                            $hasAddons = !$isAddonItem && !empty($addons) && count($addons) > 0;
                            $addonsCost = 0;
                            if ($hasAddons) {
                                foreach ($addons as $a) {
                                    $aQty = $a['qty'] ?? $a['quantity'] ?? 1;
                                    $aPrice = floatval($a['price'] ?? 0);
                                    $addonsCost += ($aPrice * $aQty);
                                }
                            }
                            $itemPrice = (float)$item->price;
                            $itemQty = (int)$item->quantity;
                            $itemDiscount = (float)($item->item_discount_percentage ?? 0);
                            $discountedPrice = (float)($item->discounted_price ?? ($itemPrice - ($itemPrice * $itemDiscount / 100)));
                            
                            $lineOriginal = $isAddonItem ? ($itemPrice * $itemQty) : (($itemPrice * $itemQty) + $addonsCost);
                            $lineTaxable = $item->taxable_amount ?? ($isAddonItem ? ($discountedPrice * $itemQty) : (($discountedPrice * $itemQty) + $addonsCost));
                            $lineGst = $item->gst_amount ?? (($lineTaxable * ($item->gst_rate ?? 0)) / 100);
                            $lineTotal = $item->total_amount ?? ($lineTaxable + $lineGst);

                            $pubOriginalSubtotal += $lineOriginal;
                            $pubTaxableTotal += $lineTaxable;
                            $pubGstTotal += $lineGst;
                            $pubItemDiscountTotal += ($itemPrice * $itemDiscount / 100) * $itemQty;
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-start">
                                    @if(@$item->subcategory->food_type == 'non-veg')
                                        <span class="food-dot nonveg mt-1" title="Non-Veg"></span>
                                    @else
                                        <span class="food-dot veg mt-1" title="Veg"></span>
                                    @endif
                                    <div>
                                        <strong class="text-dark">{{ $item->subcategory->name ?? 'Dish Item' }}</strong>
                                        @if($isAddonItem)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.68rem; font-weight: 700;">Add-on</span>
                                        @endif
                                        @if($item->item_discount_percentage > 0)
                                            <span class="badge bg-success-subtle text-success ms-1 small">{{ $item->item_discount_percentage }}% off</span>
                                        @endif
                                        @if($hasAddons)
                                            <div class="pub-item-addons-box">
                                                @foreach($addons as $a)
                                                    @php
                                                        $aQty = $a['qty'] ?? $a['quantity'] ?? 1;
                                                        $aPrice = floatval($a['price'] ?? 0);
                                                        $aTotal = $aPrice * $aQty;
                                                    @endphp
                                                    <div class="pub-addon-line" style="display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                                        <span style="font-weight: 600;">+ {{ $a['name'] ?? 'Add-on' }}</span>
                                                        <span class="text-muted small">(₹{{ number_format($aPrice, 2) }})</span>
                                                        <span class="pub-addon-badge" style="background: #ffedd5; color: #c2410c; font-size: 0.72rem; font-weight: 800; padding: 1px 5px; border-radius: 4px; border: 1px solid #fed7aa;">x{{ $aQty }}</span>
                                                        <span style="font-weight: 700; color: #ea580c; font-size: 0.76rem;">= +₹{{ number_format($aTotal, 2) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center font-monospace fw-bold">{{ $item->quantity }}</td>
                            <td class="text-end text-muted font-monospace">₹{{ number_format($item->price, 2) }}</td>
                            <td class="text-end fw-bold text-dark font-monospace">₹{{ number_format($lineTotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Calculation Box -->
            @php
                $pubDisplaySubtotal = ($order->total_amount > 0 && abs($order->total_amount - $pubOriginalSubtotal) < 0.01) ? $order->total_amount : $pubOriginalSubtotal;
                $pubDisplayTaxable = ($order->taxable_amount > 0 && abs($order->taxable_amount - $pubTaxableTotal) < 0.01) ? $order->taxable_amount : $pubTaxableTotal;
                $pubDisplayGst = ($order->gst_amount > 0 && abs($order->gst_amount - $pubGstTotal) < 0.01) ? $order->gst_amount : $pubGstTotal;
                $pubDisplayDiscount = $order->discount ?? (($pubDisplayTaxable + $pubDisplayGst) * ($order->discount_percentage ?? 0) / 100);
                $pubDisplayGrandTotal = $order->grand_total ?? ($pubDisplayTaxable + $pubDisplayGst - $pubDisplayDiscount);
            @endphp
            <div class="pub-calc-box">
                <div class="pub-calc-row">
                    <span class="text-muted">Item Subtotal:</span>
                    <span class="fw-semibold">₹{{ number_format($pubDisplaySubtotal, 2) }}</span>
                </div>

                @if($pubItemDiscountTotal > 0)
                    <div class="pub-calc-row text-success">
                        <span>Item Discounts:</span>
                        <span>- ₹{{ number_format($pubItemDiscountTotal, 2) }}</span>
                    </div>
                @endif

                @if($order->is_gst_bill == 'YES' && $pubDisplayGst > 0)
                    <div class="pub-calc-row">
                        <span class="text-muted">GST ({{ $order->restaurant_gst_percentage ?? 0 }}%):</span>
                        <span>+ ₹{{ number_format($pubDisplayGst, 2) }}</span>
                    </div>
                @endif

                @if($pubDisplayDiscount > 0 || ($order->discount_percentage ?? 0) > 0)
                    <div class="pub-calc-row text-danger">
                        <span>Order Discount ({{ $order->discount_percentage ?? 0 }}%):</span>
                        <span>- ₹{{ number_format($pubDisplayDiscount, 2) }}</span>
                    </div>
                @endif

                <div class="pub-calc-row grand">
                    <span>Grand Total:</span>
                    <span class="text-primary font-monospace">₹{{ number_format($pubDisplayGrandTotal, 2) }}</span>
                </div>
            </div>

            <!-- Status Deck -->
            <div class="pub-status-deck">
                <div>
                    <div class="small text-muted mb-1">Payment Status:</div>
                    @if($balanceDue <= 0)
                        <span class="pub-status-badge badge-settled">
                            <i class="fa-solid fa-circle-check"></i> Fully Paid ({{ number_format($totalPaid, 2) }})
                        </span>
                    @else
                        <span class="pub-status-badge badge-due">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ number_format($balanceDue, 2) }} Due
                        </span>
                    @endif
                </div>

                @if($payments && count($payments) > 0)
                <div class="text-end">
                    <div class="small text-muted mb-1">Paid Modes:</div>
                    <div>
                        @foreach($payments as $p)
                            <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                                {{ $p->payment_method }}: {{ number_format($p->amount, 2) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Instant UPI Payment QR if unpaid -->
            @if($balanceDue > 0 && (!empty($order->restaurant->qr_code_image) || !empty($order->restaurant->upi_id)))
            <div class="pub-qr-card">
                <div class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-qrcode text-primary me-1"></i> Scan &amp; Pay Remaining {{ number_format($balanceDue, 2) }}
                </div>
                @if(!empty($order->restaurant->qr_code_image))
                    <img src="{{ asset('storage/' . $order->restaurant->qr_code_image) }}" alt="QR" style="width: 130px; height: 130px; object-fit: contain; margin: 8px 0; border: 1px solid #cbd5e1; border-radius: 8px;">
                @endif
                @if(!empty($order->restaurant->upi_id))
                    <div class="small text-muted font-monospace">UPI ID: <strong>{{ $order->restaurant->upi_id }}</strong></div>
                @endif
            </div>
            @endif

        </div>

        <!-- Footer -->
        <div class="pub-footer">
            <p class="mb-1 fw-bold text-dark">
                Thank you for dining with {{ $order->restaurant->name ?? 'us' }}!
            </p>
            <p class="mb-0 text-muted small">
                Powered by Bill&amp;Bite POS
            </p>
        </div>

    </div>

</div>

</body>
</html>
